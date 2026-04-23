<?php
require_once dirname(__FILE__) . '/Database.php';

class ComplaintModel {
    private $db;
    private $conn;

    public function __construct() {
        $this->db   = new Database();
        $this->conn = $this->db->connect();
    }

    private function clean($data) {
        return trim(strip_tags($data));
    }

    // File a complaint - user or driver (UC-21)
    public function fileComplaint($data) {
        $ride_id     = isset($data['ride_id']) ? intval($data['ride_id']) : 0;
        $user_id     = intval($data['filed_by_user_id']);
        $filed_type  = $this->clean($data['filed_by_type']);
        $subject     = $this->clean($data['subject']);
        $description = $this->clean($data['description']);

        if ($subject === '' || $description === '') {
            return array('success' => false, 'message' => 'Subject and description are required');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO complaints (ride_id, filed_by_user_id, filed_by_type, subject, description, status)
             VALUES (?, ?, ?, ?, ?, 'open')"
        );
        $stmt->bind_param("iisss", $ride_id, $user_id, $filed_type, $subject, $description);

        if ($stmt->execute()) {
            $complaint_id = $this->db->lastInsertId();
            $stmt->close();
            return array('success' => true, 'message' => 'Complaint filed! Reference ID: #' . $complaint_id);
        }
        $stmt->close();
        return array('success' => false, 'message' => 'Failed: ' . $this->db->getError());
    }

    // Get complaints by user with admin response
    public function getComplaintsByUser($user_id) {
        $stmt = $this->db->prepare(
            "SELECT complaint_id, ride_id, subject, description, status, admin_response, resolved_at, created_at
             FROM complaints WHERE filed_by_user_id = ? AND filed_by_type = 'user'
             ORDER BY created_at DESC"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($cid, $rid, $subject, $desc, $status, $admin_response, $resolved_at, $created_at);
        $rows = array();
        while ($stmt->fetch()) {
            $rows[] = array(
                'complaint_id'   => $cid,
                'ride_id'        => $rid,
                'subject'        => $subject,
                'description'    => $desc,
                'status'         => $status,
                'admin_response' => $admin_response,
                'resolved_at'    => $resolved_at,
                'created_at'     => $created_at
            );
        }
        $stmt->close();
        return $rows;
    }

    // Get complaints by driver
    public function getComplaintsByDriver($driver_id) {
        $stmt = $this->db->prepare(
            "SELECT complaint_id, ride_id, subject, description, status, admin_response, resolved_at, created_at
             FROM complaints WHERE filed_by_user_id = ? AND filed_by_type = 'driver'
             ORDER BY created_at DESC"
        );
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $stmt->bind_result($cid, $rid, $subject, $desc, $status, $admin_response, $resolved_at, $created_at);
        $rows = array();
        while ($stmt->fetch()) {
            $rows[] = array(
                'complaint_id'   => $cid,
                'ride_id'        => $rid,
                'subject'        => $subject,
                'description'    => $desc,
                'status'         => $status,
                'admin_response' => $admin_response,
                'resolved_at'    => $resolved_at,
                'created_at'     => $created_at
            );
        }
        $stmt->close();
        return $rows;
    }

    // Get ALL complaints for admin with ride info
    public function getAllComplaints() {
        $result = $this->db->query(
            "SELECT c.complaint_id, c.ride_id, c.filed_by_user_id, c.filed_by_type,
                    c.subject, c.description, c.status, c.admin_response, c.resolved_at, c.created_at
             FROM complaints c
             ORDER BY c.created_at DESC"
        );
        return $this->db->fetchAll($result);
    }

    // Admin: resolve complaint with response
    public function resolveComplaint($complaint_id, $admin_id, $response) {
        $response = trim(strip_tags($response));
        $stmt = $this->db->prepare(
            "UPDATE complaints
             SET status='resolved', admin_response=?, resolved_by=?, resolved_at=NOW()
             WHERE complaint_id=?"
        );
        $stmt->bind_param("sii", $response, $admin_id, $complaint_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Update complaint status
    public function updateStatus($complaint_id, $status) {
        $stmt = $this->db->prepare(
            "UPDATE complaints SET status=? WHERE complaint_id=?"
        );
        $stmt->bind_param("si", $status, $complaint_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>
