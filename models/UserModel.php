<?php
require_once dirname(__FILE__) . '/Database.php';

class UserModel {
    private $db;
    private $conn;
    private $salt = "chalya_patrao_salt";

    public function __construct() {
        $this->db   = new Database();
        $this->conn = $this->db->connect();
    }

    private function clean($data) {
        return trim(strip_tags($data));
    }

    private function hashPassword($password) {
        return sha1($this->salt . $password);
    }

    public function checkEmail($email) {
        $email = $this->clean($email);
        $stmt  = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = ($stmt->num_rows > 0);
        $stmt->close();
        return $exists;
    }

    // ── Validate date_of_birth ──────────────────────────────────────────────
    private function validateDob($dob) {
        if (empty($dob)) return false;
        $d = DateTime::createFromFormat('Y-m-d', $dob);
        if (!$d) return false;
        if ($d > new DateTime()) return false;
        $age = (new DateTime())->diff($d)->y;
        if ($age < 5 || $age > 120) return false;
        return true;
    }

    // ── Register ────────────────────────────────────────────────────────────
    public function register($data) {
        $full_name = $this->clean($data['full_name']);
        $email     = $this->clean($data['email']);
        $phone     = $this->clean($data['phone']);
        $password  = trim($data['password']);
        $user_type = $this->clean($data['user_type']);
        $lang      = $this->clean($data['preferred_language']);
        $dob       = isset($data['date_of_birth']) ? $this->clean($data['date_of_birth']) : '';

        if ($full_name === '' || $email === '' || $phone === '' || $password === '') {
            return array('success' => false, 'message' => 'All fields are required');
        }
        if (empty($dob)) {
            return array('success' => false, 'message' => 'Date of birth is required');
        }
        if (!$this->validateDob($dob)) {
            return array('success' => false, 'message' => 'Please enter a valid date of birth');
        }

        // ✅ Student must use recognised Goa college email
        if ($user_type === 'student') {
            $domain = strtolower(substr(strrchr($email, '@'), 1));
            $allowed_colleges = [
                'chowgules.ac.in', 'dempocollege.ac.in', 'rosarymargao.ac.in',
                'goa.bits-pilani.ac.in', 'unigoa.ac.in', 'gim.ac.in',
                'iitgoa.ac.in', 'nit.goa.ac.in', 'govcollegepanji.ac.in',
                'agnel.ac.in', 'drait.ac.in', 'srosc.ac.in',
            ];
            if (!in_array($domain, $allowed_colleges)) {
                return array('success' => false, 'message' => 'Only students from recognised Goa colleges are eligible. Please use your official college email (e.g. alc000@chowgules.ac.in).');
            }
        }

        if ($this->checkEmail($email)) {
            return array('success' => false, 'message' => 'Email already registered');
        }

        $hashed = $this->hashPassword($password);
        $stmt   = $this->db->prepare(
            "INSERT INTO users
             (full_name, email, phone_number, password_hash, date_of_birth, user_type, preferred_language)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssss", $full_name, $email, $phone, $hashed, $dob, $user_type, $lang);

        if ($stmt->execute()) {
            $stmt->close();
            return array('success' => true, 'message' => 'Registration successful! Please login.');
        }
        $stmt->close();
        return array('success' => false, 'message' => 'Registration failed: ' . $this->db->getError());
    }

    // ── Login ───────────────────────────────────────────────────────────────
    public function login($email, $password) {
        $email    = $this->clean($email);
        $password = trim($password);

        if ($email === '' || $password === '') {
            return array('success' => false, 'message' => 'Email and password required');
        }

        $stmt = $this->db->prepare(
            "SELECT user_id, full_name, password_hash, user_type FROM users WHERE email = ? AND is_active = 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $stmt->close();
            return array('success' => false, 'message' => 'Invalid email or password');
        }

        $stmt->bind_result($user_id, $full_name, $stored_hash, $user_type);
        $stmt->fetch();
        $stmt->close();

        if ($stored_hash !== $this->hashPassword($password)) {
            return array('success' => false, 'message' => 'Invalid email or password');
        }

        if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['user_id']   = $user_id;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email']     = $email;
        $_SESSION['user_type'] = $user_type;

        return array('success' => true, 'message' => 'Login successful');
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
    }

    // ── Get user by ID (includes DOB) ────────────────────────────────────────
    public function getUserById($user_id) {
        $stmt = $this->db->prepare(
            "SELECT user_id, full_name, email, phone_number, date_of_birth,
                    user_type, preferred_language
             FROM users WHERE user_id = ?"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($uid, $full_name, $email, $phone, $dob, $user_type, $lang);
        $stmt->fetch();
        $stmt->close();
        return array(
            'user_id'            => $uid,
            'full_name'          => $full_name,
            'email'              => $email,
            'phone_number'       => $phone,
            'date_of_birth'      => $dob,
            'user_type'          => $user_type,
            'preferred_language' => $lang
        );
    }

    public function updateUser($user_id, $data) {
        $full_name = $this->clean($data['full_name']);
        $phone     = $this->clean($data['phone']);
        $user_type = $this->clean($data['user_type']);
        $lang      = $this->clean($data['preferred_language']);

        $stmt = $this->db->prepare(
            "UPDATE users SET full_name=?, phone_number=?, user_type=?, preferred_language=?
             WHERE user_id=?"
        );
        $stmt->bind_param("ssssi", $full_name, $phone, $user_type, $lang, $user_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function deleteUser($user_id) {
        $stmt = $this->db->prepare("UPDATE users SET is_active=0 WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // ── Admin: get all users (includes DOB) ─────────────────────────────────
    public function getAllUsers() {
        $result = $this->db->query(
            "SELECT user_id, full_name, email, phone_number, date_of_birth,
                    user_type, is_active, created_at
             FROM users WHERE is_active = 1 ORDER BY created_at DESC"
        );
        return $this->db->fetchAll($result);
    }
}
?>