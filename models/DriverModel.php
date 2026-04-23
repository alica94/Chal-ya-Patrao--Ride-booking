<?php
require_once dirname(__FILE__) . '/Database.php';

class DriverModel {
    private $db;
    private $conn;
    private $salt = "chalya_driver_salt";

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

    public function checkPhone($phone) {
        $phone = $this->clean($phone);
        $stmt  = $this->db->prepare("SELECT driver_id FROM drivers WHERE phone_number = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();
        $exists = ($stmt->num_rows > 0);
        $stmt->close();
        return $exists;
    }

    public function register($data) {
        $full_name = $this->clean($data['full_name']);
        $phone     = $this->clean($data['phone']);
        $gender    = $this->clean($data['gender']);
        $license   = $this->clean($data['license_number']);
        $password  = trim($data['password']);
        $veh_type  = $this->clean($data['vehicle_type']);
        $reg_no    = $this->clean($data['registration_number']);
        $model     = $this->clean($data['vehicle_model']);
        $color     = $this->clean($data['vehicle_color']);
        $pet       = isset($data['is_pet_friendly']) ? 1 : 0;
        $ins_exp   = $this->clean($data['insurance_expiry']);

        if ($full_name==='' || $phone==='' || $gender==='' || $license==='' || $password==='') {
            return array('success'=>false,'message'=>'All driver fields are required');
        }
        if ($veh_type==='' || $reg_no==='' || $model==='' || $color==='') {
            return array('success'=>false,'message'=>'All vehicle fields are required');
        }
        if ($this->checkPhone($phone)) {
            return array('success'=>false,'message'=>'Phone number already registered');
        }

        $hashed = $this->hashPassword($password);
        $stmt   = $this->db->prepare(
            "INSERT INTO drivers (full_name, phone_number, gender, license_number, password_hash, approval_status) VALUES (?, ?, ?, ?, ?, 'pending')"
        );
        $stmt->bind_param("sssss", $full_name, $phone, $gender, $license, $hashed);

        if (!$stmt->execute()) {
            $stmt->close();
            return array('success'=>false,'message'=>'Driver registration failed: '.$this->db->getError());
        }
        $driver_id = $this->db->lastInsertId();
        $stmt->close();

        $stmt2 = $this->db->prepare(
            "INSERT INTO vehicles (driver_id, vehicle_type, registration_number, model, color, is_pet_friendly, insurance_expiry) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt2->bind_param("isssiis", $driver_id, $veh_type, $reg_no, $model, $color, $pet, $ins_exp);

        if (!$stmt2->execute()) {
            $stmt2->close();
            return array('success'=>false,'message'=>'Vehicle registration failed: '.$this->db->getError());
        }
        $stmt2->close();
        return array('success'=>true,'message'=>'Registration submitted! Await admin approval.');
    }

    public function login($phone, $password) {
        $phone    = $this->clean($phone);
        $password = trim($password);

        if ($phone==='' || $password==='') {
            return array('success'=>false,'message'=>'Phone and password required');
        }

        $stmt = $this->db->prepare(
            "SELECT driver_id, full_name, password_hash, approval_status FROM drivers WHERE phone_number = ?"
        );
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $stmt->close();
            return array('success'=>false,'message'=>'Invalid phone or password');
        }

        $stmt->bind_result($driver_id, $full_name, $stored_hash, $approval_status);
        $stmt->fetch();
        $stmt->close();

        if ($stored_hash !== $this->hashPassword($password)) {
            return array('success'=>false,'message'=>'Invalid phone or password');
        }
        if ($approval_status === 'pending') {
            return array('success'=>false,'message'=>'Account pending admin approval.');
        }
        if ($approval_status === 'rejected') {
            return array('success'=>false,'message'=>'Account rejected. Contact support.');
        }

        if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['driver_id']       = $driver_id;
        $_SESSION['full_name']       = $full_name;
        $_SESSION['role']            = 'driver';
        $_SESSION['approval_status'] = $approval_status;

        // ✅ Set driver online on login
        $stmtOnline = $this->db->prepare("UPDATE drivers SET is_online=1 WHERE driver_id=?");
        $stmtOnline->bind_param("i", $driver_id);
        $stmtOnline->execute();
        $stmtOnline->close();

        return array('success'=>true,'message'=>'Login successful');
    }

    public function getDriverById($driver_id) {
        $stmt = $this->db->prepare(
            "SELECT driver_id, full_name, phone_number, gender, license_number, approval_status, avg_rating, is_online FROM drivers WHERE driver_id = ?"
        );
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $stmt->bind_result($did, $full_name, $phone, $gender, $license, $approval_status, $avg_rating, $is_online);
        $stmt->fetch();
        $stmt->close();
        return array(
            'driver_id'       => $did,
            'full_name'       => $full_name,
            'phone_number'    => $phone,
            'gender'          => $gender,
            'license_number'  => $license,
            'approval_status' => $approval_status,
            'avg_rating'      => $avg_rating,
            'is_online'       => $is_online
        );
    }

    public function getVehicleByDriver($driver_id) {
        $stmt = $this->db->prepare(
            "SELECT vehicle_id, vehicle_type, registration_number, model, color, seats, is_pet_friendly, insurance_expiry FROM vehicles WHERE driver_id = ? AND is_active = 1 LIMIT 1"
        );
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $stmt->bind_result($vid, $vtype, $reg, $model, $color, $seats, $pet, $ins);
        if ($stmt->fetch()) {
            $stmt->close();
            return array(
                'vehicle_id'          => $vid,
                'vehicle_type'        => $vtype,
                'registration_number' => $reg,
                'model'               => $model,
                'color'               => $color,
                'seats'               => $seats,
                'is_pet_friendly'     => $pet,
                'insurance_expiry'    => $ins
            );
        }
        $stmt->close();
        return null;
    }

    public function getAllDrivers() {
        $result = $this->db->query(
            "SELECT driver_id, full_name, phone_number, gender, approval_status, avg_rating, created_at FROM drivers ORDER BY created_at DESC"
        );
        return $this->db->fetchAll($result);
    }

    public function updateApprovalStatus($driver_id, $status) {
        $stmt = $this->db->prepare("UPDATE drivers SET approval_status=? WHERE driver_id=?");
        $stmt->bind_param("si", $status, $driver_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // ✅ Set driver offline
    public function setOffline($driver_id) {
        $stmt = $this->db->prepare("UPDATE drivers SET is_online=0 WHERE driver_id=?");
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $stmt->close();
    }

    // ✅ Toggle online/offline status
    public function setOnlineStatus($driver_id, $status) {
        $stmt = $this->db->prepare("UPDATE drivers SET is_online=? WHERE driver_id=?");
        $stmt->bind_param("ii", $status, $driver_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>