<?php
require_once dirname(__FILE__) . '/Database.php';

class RatingModel {
    private $db;
    private $conn;

    public function __construct() {
        $this->db   = new Database();
        $this->conn = $this->db->connect();
    }

    private function clean($data) {
        return trim(strip_tags($data));
    }

    // Check if user already rated this ride
    public function hasRated($ride_id, $user_id) {
        $stmt = $this->db->prepare(
            "SELECT rating_id FROM ratings WHERE ride_id = ? AND rated_by_user_id = ?"
        );
        $stmt->bind_param("ii", $ride_id, $user_id);
        $stmt->execute();
        $stmt->store_result();
        $exists = ($stmt->num_rows > 0);
        $stmt->close();
        return $exists;
    }

    // Submit rating (UC-16)
    public function submitRating($data) {
        $ride_id   = intval($data['ride_id']);
        $user_id   = intval($data['user_id']);
        $driver_id = intval($data['driver_id']);
        $stars     = intval($data['stars']);
        $feedback  = $this->clean($data['feedback_text']);

        if ($stars < 1 || $stars > 5) {
            return array('success'=>false,'message'=>'Rating must be between 1 and 5 stars');
        }
        if ($this->hasRated($ride_id, $user_id)) {
            return array('success'=>false,'message'=>'You have already rated this ride');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO ratings (ride_id, rated_by_user_id, rated_driver_id, stars, feedback_text) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iiiis", $ride_id, $user_id, $driver_id, $stars, $feedback);

        if ($stmt->execute()) {
            $stmt->close();
            // Update driver average rating
            $this->updateDriverAvgRating($driver_id);
            return array('success'=>true,'message'=>'Thank you for your rating!');
        }
        $stmt->close();
        return array('success'=>false,'message'=>'Rating failed: '.$this->db->getError());
    }

    // Update driver average rating (UC-16)
    public function updateDriverAvgRating($driver_id) {
        $stmt = $this->db->prepare(
            "UPDATE drivers SET avg_rating = (SELECT AVG(stars) FROM ratings WHERE rated_driver_id = ?) WHERE driver_id = ?"
        );
        $stmt->bind_param("ii", $driver_id, $driver_id);
        $stmt->execute();
        $stmt->close();
    }

    // Get all ratings for a driver (UC-29)
    public function getRatingsByDriver($driver_id) {
        $stmt = $this->db->prepare(
            "SELECT r.rating_id, r.stars, r.feedback_text, r.created_at, u.full_name AS user_name
             FROM ratings r
             JOIN users u ON r.rated_by_user_id = u.user_id
             WHERE r.rated_driver_id = ?
             ORDER BY r.created_at DESC"
        );
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $stmt->bind_result($rating_id, $stars, $feedback, $created_at, $user_name);
        $rows = array();
        while ($stmt->fetch()) {
            $rows[] = array(
                'rating_id'     => $rating_id,
                'stars'         => $stars,
                'feedback_text' => $feedback,
                'created_at'    => $created_at,
                'user_name'     => $user_name
            );
        }
        $stmt->close();
        return $rows;
    }

    // Get rating for a specific ride
    public function getRatingByRide($ride_id) {
        $stmt = $this->db->prepare(
            "SELECT rating_id, stars, feedback_text, created_at FROM ratings WHERE ride_id = ?"
        );
        $stmt->bind_param("i", $ride_id);
        $stmt->execute();
        $stmt->bind_result($rating_id, $stars, $feedback, $created_at);
        $row = null;
        if ($stmt->fetch()) {
            $row = array(
                'rating_id'     => $rating_id,
                'stars'         => $stars,
                'feedback_text' => $feedback,
                'created_at'    => $created_at
            );
        }
        $stmt->close();
        return $row;
    }
}
?>
