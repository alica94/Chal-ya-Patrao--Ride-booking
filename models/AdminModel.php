<?php
require_once dirname(__FILE__) . '/Database.php';

class AdminModel {
    private $db;
    private $conn;
    private $salt = "chalya_admin_salt";

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

    public function login($email, $password) {
        $email    = $this->clean($email);
        $password = trim($password);

        if ($email==='' || $password==='') {
            return array('success'=>false,'message'=>'Email and password required');
        }

        $stmt = $this->db->prepare(
            "SELECT admin_id, full_name, password_hash, role FROM admin WHERE email = ?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $stmt->close();
            return array('success'=>false,'message'=>'Invalid email or password');
        }

        $stmt->bind_result($admin_id, $full_name, $stored_hash, $role);
        $stmt->fetch();
        $stmt->close();

        if ($stored_hash !== $this->hashPassword($password)) {
            return array('success'=>false,'message'=>'Invalid email or password');
        }

        if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['admin_id']   = $admin_id;
        $_SESSION['full_name']  = $full_name;
        $_SESSION['role']       = 'admin';
        $_SESSION['admin_role'] = $role;

        return array('success'=>true,'message'=>'Admin login successful');
    }

    public function createAdmin($data) {
        $full_name = $this->clean($data['full_name']);
        $email     = $this->clean($data['email']);
        $password  = trim($data['password']);
        $role      = $this->clean($data['admin_role']);

        if ($full_name==='' || $email==='' || $password==='') {
            return array('success'=>false,'message'=>'All fields are required');
        }

        $stmt = $this->db->prepare("SELECT admin_id FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return array('success'=>false,'message'=>'Email already registered as admin');
        }
        $stmt->close();

        $hashed = $this->hashPassword($password);
        $stmt2  = $this->db->prepare(
            "INSERT INTO admin (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)"
        );
        $stmt2->bind_param("ssss", $full_name, $email, $hashed, $role);

        if ($stmt2->execute()) {
            $stmt2->close();
            return array('success'=>true,'message'=>'Admin account created successfully');
        }
        $stmt2->close();
        return array('success'=>false,'message'=>'Failed: '.$this->db->getError());
    }

    // ✅ FIXED - added date_of_birth
    public function getAllUsers() {
         $result = $this->db->query(
            "SELECT user_id, full_name, email, phone_number, date_of_birth, user_type, is_active, created_at FROM users ORDER BY created_at DESC"
        );
        return $this->db->fetchAll($result);
    }

    public function getUserById($user_id) {
        $stmt = $this->db->prepare("SELECT user_id, full_name, email, phone_number, user_type, is_active FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($uid, $full_name, $email, $phone, $user_type, $is_active);
        $stmt->fetch();
        $stmt->close();
        return array(
            'user_id'   => $uid,
            'full_name' => $full_name,
            'email'     => $email,
            'phone_number' => $phone,
            'user_type' => $user_type,
            'is_active' => $is_active
        );
    }

    public function blockUser($user_id) {
        $stmt = $this->db->prepare("UPDATE users SET is_active=0 WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function unblockUser($user_id) {
        $stmt = $this->db->prepare("UPDATE users SET is_active=1 WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // ✅ FIXED - order by driver_id so it's always 1,2,3,4
    public function getAllDrivers() {
         $result = $this->db->query(
        "SELECT driver_id, full_name, phone_number, gender, approval_status, avg_rating, created_at FROM drivers ORDER BY driver_id ASC"
        );
    return $this->db->fetchAll($result);
    }

    public function approveDriver($driver_id) {
        $stmt = $this->db->prepare("UPDATE drivers SET approval_status='approved' WHERE driver_id=?");
        $stmt->bind_param("i", $driver_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function rejectDriver($driver_id) {
        $stmt = $this->db->prepare("UPDATE drivers SET approval_status='rejected' WHERE driver_id=?");
        $stmt->bind_param("i", $driver_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getAllRides() {
        $result = $this->db->query(
            "SELECT r.ride_id, u.full_name AS user_name, r.pickup_location, r.dropoff_location, r.ride_type, r.fare, r.status, r.booked_at FROM rides r JOIN users u ON r.user_id = u.user_id ORDER BY r.booked_at DESC"
        );
        return $this->db->fetchAll($result);
    }
    // ✅ Get ALL pending rides for admin notification (both auto & manual)
    public function getPendingRideRequests() {
        $result = $this->db->query(
            "SELECT r.ride_id, r.ride_id AS request_id, r.booked_at AS notified_at,
                r.pickup_location, r.dropoff_location, r.ride_type,
                r.car_type, r.fare, r.payment_mode, r.driver_assign_mode,
                u.full_name AS user_name, u.phone_number AS user_phone,
                COALESCE(d.full_name, 'Not assigned yet') AS driver_name
            FROM rides r
            JOIN users u ON r.user_id = u.user_id
            LEFT JOIN drivers d ON r.driver_id = d.driver_id
            WHERE r.status = 'pending'
            ORDER BY r.booked_at DESC"
        );
    return $this->db->fetchAll($result);
    }
}
?>
