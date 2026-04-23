<?php
require_once 'models/RatingModel.php';
require_once 'models/ComplaintModel.php';
require_once 'models/BookingModel.php';

// Select Driver — UC-19
function handleSelectDriver() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login'); exit;
    }
    if (!isset($_SESSION['pending_pickup'])) {
        header('Location: index.php?page=booking'); exit;
    }

    $booking = new BookingModel();
    $error   = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_to_payment'])) {
        $_SESSION['pending_driver_id'] = intval($_POST['selected_driver_id']);
        header('Location: index.php?page=payment');
        exit;
    }

    // Filter by pet-friendly if required
    $pet_friendly = !empty($_SESSION['pending_pet_friendly']) ? true : false;
    $drivers = $booking->getAvailableDrivers($pet_friendly);
    include 'views/select_driver.php';
}

// Track Ride — UC-09, UC-10
function handleTrackRide() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login'); exit;
    }

    $booking       = new BookingModel();
    $ratingModel   = new RatingModel();
    $ride_id       = isset($_GET['ride_id']) ? intval($_GET['ride_id']) : 0;
    $ride          = $ride_id > 0 ? $booking->getBookingById($ride_id) : null;
    $already_rated = false;

    if ($ride && isset($_SESSION['user_id'])) {
        $already_rated = $ratingModel->hasRated($ride_id, $_SESSION['user_id']);
    }
    include 'views/track_ride.php';
}

// Rate Ride — UC-16
function handleRateRide() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login'); exit;
    }

    $ratingModel  = new RatingModel();
    $bookingModel = new BookingModel();
    $error        = '';
    $success      = '';
    $ride_id      = isset($_GET['ride_id']) ? intval($_GET['ride_id']) : 0;
    $ride         = $ride_id > 0 ? $bookingModel->getBookingById($ride_id) : null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
        if (intval($_POST['stars']) === 0) {
            $error = 'Please select a star rating.';
        } else {
            $result = $ratingModel->submitRating(array(
                'ride_id'       => $_POST['ride_id'],
                'user_id'       => $_SESSION['user_id'],
                'driver_id'     => $_POST['driver_id'],
                'stars'         => $_POST['stars'],
                'feedback_text' => isset($_POST['feedback_text']) ? $_POST['feedback_text'] : ''
            ));
            if ($result['success']) { $success = $result['message']; }
            else                    { $error   = $result['message']; }
        }
    }
    include 'views/rate_ride.php';
}

// Complaint — UC-21
function handleComplaint() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    $is_user   = isset($_SESSION['user_id']);
    $is_driver = isset($_SESSION['driver_id']);
    if (!$is_user && !$is_driver) {
        header('Location: index.php?page=login'); exit;
    }

    $model      = new ComplaintModel();
    $error      = '';
    $success    = '';
    $complaints = array();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_complaint'])) {
        $filed_id   = $is_user ? $_SESSION['user_id'] : $_SESSION['driver_id'];
        $filed_type = $is_user ? 'user' : 'driver';
        $result = $model->fileComplaint(array(
            'ride_id'          => isset($_POST['ride_id']) ? intval($_POST['ride_id']) : 0,
            'filed_by_user_id' => $filed_id,
            'filed_by_type'    => $filed_type,
            'subject'          => $_POST['subject'],
            'description'      => $_POST['description']
        ));
        if ($result['success']) { $success = $result['message']; }
        else                    { $error   = $result['message']; }
    }

    if ($is_user) {
        $complaints = $model->getComplaintsByUser($_SESSION['user_id']);
    } else {
        $complaints = $model->getComplaintsByDriver($_SESSION['driver_id']);
    }
    include 'views/complaint.php';
}

// Driver Ratings — UC-29
function handleDriverRatings() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['driver_id'])) {
        header('Location: index.php?page=driver_login'); exit;
    }
    $model   = new RatingModel();
    $ratings = $model->getRatingsByDriver($_SESSION['driver_id']);
    include 'views/driver_ratings.php';
}

// Driver ride actions — UC-23, UC-27
// Handles: accept/reject ride REQUEST (notification) AND update ride status
function handleDriverRideAction() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['driver_id'])) {
        header('Location: index.php?page=driver_login'); exit;
    }

    $booking    = new BookingModel();
    $success    = '';
    $error      = '';
    $driver_id  = intval($_SESSION['driver_id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // ── Accept a specific ride REQUEST (notification) ──────────────────
        if (isset($_POST['accept_request'])) {
            $request_id = intval($_POST['request_id']);
            $result     = $booking->acceptDriverRequest($request_id, $driver_id);
            $success    = $result['success'] ? $result['message'] : '';
            $error      = $result['success'] ? '' : $result['message'];

        // ── Reject a specific ride REQUEST ─────────────────────────────────
        } elseif (isset($_POST['reject_request'])) {
            $request_id = intval($_POST['request_id']);
            $result     = $booking->rejectDriverRequest($request_id, $driver_id);
            $success    = $result['success'] ? $result['message'] : '';
            $error      = $result['success'] ? '' : $result['message'];

        // ── Accept an open (auto-assign pool) ride ─────────────────────────
        } elseif (isset($_POST['accept_ride'])) {
            $ride_id = intval($_POST['ride_id']);
            $result  = $booking->acceptRide($ride_id, $driver_id);
            $success = $result['success'] ? $result['message'] : '';
            $error   = $result['success'] ? '' : $result['message'];

        // ── Update status of an assigned ride ──────────────────────────────
        } elseif (isset($_POST['update_status'])) {
            $ride_id    = intval($_POST['ride_id']);
            $new_status = trim($_POST['new_status']);
            $booking->updateRideStatus($ride_id, $driver_id, $new_status);
            $success = 'Ride #' . $ride_id . ' status updated to: ' . $new_status;
        }
    }

    $_SESSION['driver_msg_success'] = $success;
    $_SESSION['driver_msg_error']   = $error;
    header('Location: index.php?page=driver_dashboard');
    exit;
}
?>
