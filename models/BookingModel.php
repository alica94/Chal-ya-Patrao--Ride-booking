<?php
require_once dirname(__FILE__) . '/Database.php';

class BookingModel {
    private $db;
    private $conn;

    public function __construct() {
        $this->db   = new Database();
        $this->conn = $this->db->connect();
    }

    private function clean($data) {
        return trim(strip_tags($data));
    }

    // ──────────────────────────────────────────────────────────────────
    // DRIVER AVAILABILITY QUERIES
    // ──────────────────────────────────────────────────────────────────

    public function getAvailableDrivers($pet_friendly = false) {
        $where = $pet_friendly ? "AND v.is_pet_friendly = 1" : "";
        $result = $this->db->query(
            "SELECT d.driver_id, d.full_name, d.avg_rating, d.gender, d.is_online,
                    v.vehicle_type, v.model, v.color, v.registration_number,
                    v.is_pet_friendly, v.seats, v.car_category
             FROM drivers d
             JOIN vehicles v ON d.driver_id = v.driver_id
             WHERE d.approval_status = 'approved' AND v.is_active = 1 {$where}
             ORDER BY d.is_online DESC, d.avg_rating DESC"
        );
        return $this->db->fetchAll($result);
    }

    public function autoAssignDriver($pet_friendly = false) {
        $where = $pet_friendly ? "AND v.is_pet_friendly = 1" : "";
        $result = $this->db->query(
            "SELECT d.driver_id
             FROM drivers d
             JOIN vehicles v ON d.driver_id = v.driver_id
             WHERE d.approval_status = 'approved'
               AND d.is_online = 1
               AND v.is_active = 1
               {$where}
             ORDER BY d.avg_rating DESC
             LIMIT 1"
        );
        $rows = $this->db->fetchAll($result);
        return !empty($rows) ? intval($rows[0]['driver_id']) : 0;
    }

    // ──────────────────────────────────────────────────────────────────
    // CREATE BOOKING
    // ──────────────────────────────────────────────────────────────────
    public function createBooking($data) {
        $user_id              = intval($data['user_id']);
        $pickup               = $this->clean($data['pickup_location']);
        $dropoff              = $this->clean($data['dropoff_location']);
        $ride_type            = $this->clean($data['ride_type']);
        $car_type             = isset($data['car_type'])          ? $this->clean($data['car_type'])          : 'economy_4seater';
        $payment_mode         = $this->clean($data['payment_mode']);
        $fare                 = floatval($data['fare']);
        $requested_driver_id  = isset($data['requested_driver_id']) && intval($data['requested_driver_id']) > 0
                                ? intval($data['requested_driver_id']) : 0;
        $assign_mode          = isset($data['driver_assign_mode']) ? $this->clean($data['driver_assign_mode']) : 'manual';
        $pet_friendly         = isset($data['pet_friendly_required']) ? intval($data['pet_friendly_required']) : 0;
        $coupon_code          = isset($data['coupon_code'])       ? strtoupper($this->clean($data['coupon_code'])) : '';
        $discount_amount      = isset($data['discount_amount'])   ? floatval($data['discount_amount'])       : 0.0;

        if ($pickup === '' || $dropoff === '' || $ride_type === '') {
            return array('success' => false, 'message' => 'All fields required');
        }

        // ✅ Server-side student coupon guard
        // Even if someone bypasses the JS, this blocks the discount on the backend
        if ($coupon_code === 'STUDENT10') {
            $user_email  = isset($data['user_email']) ? strtolower(trim($data['user_email'])) : '';
            $user_type   = isset($data['user_type'])  ? strtolower(trim($data['user_type']))  : '';
            $domain      = substr(strrchr($user_email, '@'), 1);
            $is_student  = ($user_type === 'student' && str_ends_with($domain, '.ac.in'));

            if (!$is_student) {
                // Strip the coupon silently — don't let it go through
                $coupon_code     = '';
                $discount_amount = 0.0;
            } else {
                // Recalculate 10% discount server-side so it can't be tampered
                $discount_amount = round($fare * 0.10, 2);
                $fare            = round($fare - $discount_amount, 2);
            }
        }

        $driver_id = 0;
        if ($assign_mode === 'auto') {
            $driver_id = $this->autoAssignDriver($pet_friendly);
            $status = $driver_id > 0 ? 'confirmed' : 'pending';
        } else {
            $status = 'pending';
        }

        $req_did = $requested_driver_id > 0 ? $requested_driver_id : null;
        $did     = $driver_id > 0 ? $driver_id : null;

        $stmt = $this->db->prepare(
            "INSERT INTO rides
             (user_id, driver_id, requested_driver_id, pickup_location, dropoff_location,
              ride_type, car_type, pet_friendly_required, driver_assign_mode,
              payment_mode, fare, coupon_code, discount_amount, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "iiissssisssdss",
            $user_id, $did, $req_did,
            $pickup, $dropoff, $ride_type, $car_type,
            $pet_friendly, $assign_mode, $payment_mode,
            $fare, $coupon_code, $discount_amount, $status
        );

        if ($stmt->execute()) {
            $ride_id = $this->db->lastInsertId();
            $stmt->close();

            if ($assign_mode === 'manual' && $requested_driver_id > 0) {
                $this->createDriverRequest($ride_id, $requested_driver_id, $user_id);
            }

            return array(
                'success'   => true,
                'message'   => 'Ride booked!',
                'ride_id'   => $ride_id,
                'driver_id' => $driver_id,
                'status'    => $status
            );
        }
        $error = $this->db->getError();
        $stmt->close();
        return array('success' => false, 'message' => 'Booking failed: ' . $error);
    }

    // ──────────────────────────────────────────────────────────────────
    // DRIVER REQUEST (notification)
    // ──────────────────────────────────────────────────────────────────

    public function createDriverRequest($ride_id, $driver_id, $user_id) {
        $stmt = $this->db->prepare(
            "INSERT INTO driver_requests (ride_id, driver_id, user_id, status)
             VALUES (?, ?, ?, 'pending')"
        );
        $stmt->bind_param("iii", $ride_id, $driver_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    public function getPendingRequestsForDriver($driver_id) {
        $stmt = $this->db->prepare(
            "SELECT dr.request_id, dr.ride_id, dr.notified_at, dr.status,
                    r.pickup_location, r.dropoff_location, r.ride_type, r.car_type,
                    r.fare, r.payment_mode, r.pet_friendly_required,
                    u.full_name AS user_name, u.phone_number AS user_phone
             FROM driver_requests dr
             JOIN rides r ON dr.ride_id = r.ride_id
             JOIN users u ON dr.user_id = u.user_id
             WHERE dr.driver_id = ? AND dr.status = 'pending'
             ORDER BY dr.notified_at DESC"
        );
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $stmt->bind_result(
            $req_id, $ride_id, $notified_at, $req_status,
            $pickup, $dropoff, $rtype, $ctype, $fare, $pmode, $pet,
            $uname, $uphone
        );
        $rows = array();
        while ($stmt->fetch()) {
            $rows[] = array(
                'request_id'           => $req_id,
                'ride_id'              => $ride_id,
                'notified_at'          => $notified_at,
                'status'               => $req_status,
                'pickup_location'      => $pickup,
                'dropoff_location'     => $dropoff,
                'ride_type'            => $rtype,
                'car_type'             => $ctype,
                'fare'                 => $fare,
                'payment_mode'         => $pmode,
                'pet_friendly_required'=> $pet,
                'user_name'            => $uname,
                'user_phone'           => $uphone,
            );
        }
        $stmt->close();
        return $rows;
    }

    public function acceptDriverRequest($request_id, $driver_id) {
        $stmt = $this->db->prepare(
            "UPDATE driver_requests
             SET status='accepted', responded_at=NOW()
             WHERE request_id=? AND driver_id=? AND status='pending'"
        );
        $stmt->bind_param("ii", $request_id, $driver_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected === 0) {
            return array('success' => false, 'message' => 'Request not found or already responded.');
        }

        $stmt2 = $this->db->prepare("SELECT ride_id FROM driver_requests WHERE request_id=?");
        $stmt2->bind_param("i", $request_id);
        $stmt2->execute();
        $stmt2->bind_result($ride_id);
        $stmt2->fetch();
        $stmt2->close();

        $stmt3 = $this->db->prepare(
            "UPDATE rides SET driver_id=?, status='accepted'
             WHERE ride_id=? AND status='pending'"
        );
        $stmt3->bind_param("ii", $driver_id, $ride_id);
        $stmt3->execute();
        $stmt3->close();

        $stmt4 = $this->db->prepare(
            "UPDATE driver_requests
             SET status='rejected', responded_at=NOW()
             WHERE ride_id=? AND request_id != ? AND status='pending'"
        );
        $stmt4->bind_param("ii", $ride_id, $request_id);
        $stmt4->execute();
        $stmt4->close();

        return array('success' => true, 'message' => 'Ride accepted! Passenger has been notified.');
    }

    public function rejectDriverRequest($request_id, $driver_id) {
        $stmt = $this->db->prepare(
            "UPDATE driver_requests
             SET status='rejected', responded_at=NOW()
             WHERE request_id=? AND driver_id=? AND status='pending'"
        );
        $stmt->bind_param("ii", $request_id, $driver_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected > 0) {
            return array('success' => true, 'message' => 'Ride request rejected.');
        }
        return array('success' => false, 'message' => 'Could not reject request.');
    }

    // ──────────────────────────────────────────────────────────────────
    // USER BOOKINGS
    // ──────────────────────────────────────────────────────────────────

    public function getUserBookings($user_id) {
        $stmt = $this->db->prepare(
            "SELECT r.ride_id, r.pickup_location, r.dropoff_location, r.ride_type, r.car_type,
                    r.pet_friendly_required, r.driver_assign_mode,
                    r.fare, r.payment_mode, r.status, r.booked_at,
                    r.driver_id, r.requested_driver_id,
                    d.full_name AS driver_name, d.avg_rating AS driver_rating,
                    dr.status AS request_status
             FROM rides r
             LEFT JOIN drivers d ON r.driver_id = d.driver_id
             LEFT JOIN driver_requests dr ON r.ride_id = dr.ride_id AND r.requested_driver_id = dr.driver_id
             WHERE r.user_id = ? ORDER BY r.booked_at DESC"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result(
            $rid, $pickup, $dropoff, $rtype, $ctype, $pet, $amode,
            $fare, $pmode, $status, $booked_at,
            $did, $req_did, $dname, $drating, $req_status
        );
        $rows = array();
        while ($stmt->fetch()) {
            $rows[] = array(
                'ride_id'              => $rid,
                'pickup_location'      => $pickup,
                'dropoff_location'     => $dropoff,
                'ride_type'            => $rtype,
                'car_type'             => $ctype,
                'pet_friendly_required'=> $pet,
                'driver_assign_mode'   => $amode,
                'fare'                 => $fare,
                'payment_mode'         => $pmode,
                'status'               => $status,
                'booked_at'            => $booked_at,
                'driver_id'            => $did,
                'requested_driver_id'  => $req_did,
                'driver_name'          => $dname,
                'driver_rating'        => $drating,
                'request_status'       => $req_status,
            );
        }
        $stmt->close();
        return $rows;
    }

    public function getBookingById($ride_id) {
        $stmt = $this->db->prepare(
            "SELECT r.ride_id, r.user_id, r.driver_id, r.requested_driver_id,
                    r.pickup_location, r.dropoff_location, r.ride_type, r.car_type,
                    r.pet_friendly_required, r.fare, r.payment_mode, r.status, r.booked_at,
                    d.full_name AS driver_name, d.phone_number AS driver_phone,
                    d.avg_rating AS driver_rating,
                    v.model AS vehicle_model, v.color AS vehicle_color,
                    v.registration_number AS vehicle_reg
             FROM rides r
             LEFT JOIN drivers d  ON r.driver_id = d.driver_id
             LEFT JOIN vehicles v ON d.driver_id = v.driver_id AND v.is_active = 1
             WHERE r.ride_id = ?"
        );
        $stmt->bind_param("i", $ride_id);
        $stmt->execute();
        $stmt->bind_result(
            $rid, $uid, $did, $req_did,
            $pickup, $dropoff, $rtype, $ctype, $pet, $fare, $pmode,
            $status, $booked_at, $dname, $dphone, $drating,
            $vmodel, $vcolor, $vreg
        );
        $row = null;
        if ($stmt->fetch()) {
            $row = array(
                'ride_id'              => $rid,
                'user_id'              => $uid,
                'driver_id'            => $did,
                'requested_driver_id'  => $req_did,
                'pickup_location'      => $pickup,
                'dropoff_location'     => $dropoff,
                'ride_type'            => $rtype,
                'car_type'             => $ctype,
                'pet_friendly_required'=> $pet,
                'fare'                 => $fare,
                'payment_mode'         => $pmode,
                'status'               => $status,
                'booked_at'            => $booked_at,
                'driver_name'          => $dname,
                'driver_phone'         => $dphone,
                'driver_rating'        => $drating,
                'vehicle_model'        => $vmodel,
                'vehicle_color'        => $vcolor,
                'vehicle_reg'          => $vreg
            );
        }
        $stmt->close();
        return $row;
    }

    public function getDriverRides($driver_id) {
        $stmt = $this->db->prepare(
            "SELECT r.ride_id, r.pickup_location, r.dropoff_location, r.ride_type, r.car_type,
                    r.pet_friendly_required, r.fare, r.payment_mode, r.status, r.booked_at,
                    u.full_name AS user_name, u.phone_number AS user_phone
             FROM rides r
             JOIN users u ON r.user_id = u.user_id
             WHERE r.driver_id = ?
             ORDER BY r.booked_at DESC"
        );
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $stmt->bind_result(
            $rid, $pickup, $dropoff, $rtype, $ctype, $pet, $fare, $pmode,
            $status, $booked_at, $uname, $uphone
        );
        $rows = array();
        while ($stmt->fetch()) {
            $rows[] = array(
                'ride_id'              => $rid,
                'pickup_location'      => $pickup,
                'dropoff_location'     => $dropoff,
                'ride_type'            => $rtype,
                'car_type'             => $ctype,
                'pet_friendly_required'=> $pet,
                'fare'                 => $fare,
                'payment_mode'         => $pmode,
                'status'               => $status,
                'booked_at'            => $booked_at,
                'user_name'            => $uname,
                'user_phone'           => $uphone
            );
        }
        $stmt->close();
        return $rows;
    }

    public function getPendingRidesForDriver() {
        $stmt = $this->db->prepare(
            "SELECT r.ride_id, r.pickup_location, r.dropoff_location, r.ride_type, r.car_type,
                    r.pet_friendly_required, r.fare, r.payment_mode, r.booked_at,
                    u.full_name AS user_name, u.phone_number AS user_phone
             FROM rides r
             JOIN users u ON r.user_id = u.user_id
             WHERE (r.driver_id IS NULL OR r.driver_id = 0)
               AND r.status = 'pending'
               AND r.driver_assign_mode = 'auto'
             ORDER BY r.booked_at DESC"
        );
        $stmt->execute();
        $stmt->bind_result(
            $rid, $pickup, $dropoff, $rtype, $ctype, $pet, $fare, $pmode,
            $booked_at, $uname, $uphone
        );
        $rows = array();
        while ($stmt->fetch()) {
            $rows[] = array(
                'ride_id'              => $rid,
                'pickup_location'      => $pickup,
                'dropoff_location'     => $dropoff,
                'ride_type'            => $rtype,
                'car_type'             => $ctype,
                'pet_friendly_required'=> $pet,
                'fare'                 => $fare,
                'payment_mode'         => $pmode,
                'booked_at'            => $booked_at,
                'user_name'            => $uname,
                'user_phone'           => $uphone
            );
        }
        $stmt->close();
        return $rows;
    }

    public function acceptRide($ride_id, $driver_id) {
        $stmt = $this->db->prepare(
            "UPDATE rides SET driver_id=?, status='accepted'
             WHERE ride_id=? AND (driver_id IS NULL OR driver_id=0) AND status='pending'"
        );
        $stmt->bind_param("ii", $driver_id, $ride_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        if ($affected > 0) {
            return array('success' => true,  'message' => 'Ride accepted!');
        }
        return array('success' => false, 'message' => 'Ride already taken or not available.');
    }

    public function updateRideStatus($ride_id, $driver_id, $status) {
        $stmt = $this->db->prepare(
            "UPDATE rides SET status=? WHERE ride_id=? AND driver_id=?"
        );
        $stmt->bind_param("sii", $status, $ride_id, $driver_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function cancelBooking($ride_id, $user_id) {
        $stmt = $this->db->prepare(
            "UPDATE rides SET status='cancelled'
             WHERE ride_id=? AND user_id=? AND status IN ('pending','confirmed','accepted')"
        );
        $stmt->bind_param("ii", $ride_id, $user_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        if ($affected > 0) {
            return array('success' => true,  'message' => 'Ride cancelled successfully');
        }
        return array('success' => false, 'message' => 'Cannot cancel this ride');
    }

    public function getPricing($ride_type) {
        $stmt = $this->db->prepare(
            "SELECT base_fare, per_km_rate, cancellation_fee FROM ride_pricing WHERE ride_type = ?"
        );
        $stmt->bind_param("s", $ride_type);
        $stmt->execute();
        $stmt->bind_result($base_fare, $per_km, $cancel_fee);
        $row = null;
        if ($stmt->fetch()) {
            $row = array(
                'base_fare'        => $base_fare,
                'per_km_rate'      => $per_km,
                'cancellation_fee' => $cancel_fee
            );
        }
        $stmt->close();
        return $row;
    }
}
?>