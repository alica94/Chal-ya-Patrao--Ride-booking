<?php
require_once 'models/DriverModel.php';
require_once 'models/BookingModel.php';

function handleDriverRegister() {
    $model   = new DriverModel();
    $error   = '';
    $success = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['driver_register'])) {
        $result = $model->register($_POST);
        if ($result['success']) { $success = $result['message']; }
        else                    { $error   = $result['message']; }
    }
    include 'views/driver_register.php';
}

function handleDriverLogin() {
    $model = new DriverModel();
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['driver_login'])) {
        $result = $model->login($_POST['phone'], $_POST['password']);
        if ($result['success']) {
            header('Location: index.php?page=driver_dashboard'); exit;
        } else {
            $error = $result['message'];
        }
    }
    include 'views/driver_login.php';
}

function handleDriverDashboard() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['driver_id'])) {
        header('Location: index.php?page=driver_login'); exit;
    }

    $driverModel  = new DriverModel();
    $bookingModel = new BookingModel();

    $driver  = $driverModel->getDriverById($_SESSION['driver_id']);
    $vehicle = $driverModel->getVehicleByDriver($_SESSION['driver_id']);

    // Rides I'm assigned to
    $my_rides = $bookingModel->getDriverRides($_SESSION['driver_id']);

    // Pending REQUESTS (user selected me specifically)
    $pending_requests = $bookingModel->getPendingRequestsForDriver($_SESSION['driver_id']);

    // Open auto-assign pool rides I can pick up
    $pending_rides = $bookingModel->getPendingRidesForDriver();

    $success = isset($_SESSION['driver_msg_success']) ? $_SESSION['driver_msg_success'] : '';
    $error   = isset($_SESSION['driver_msg_error'])   ? $_SESSION['driver_msg_error']   : '';
    unset($_SESSION['driver_msg_success'], $_SESSION['driver_msg_error']);

    include 'views/driver_dashboard.php';
}

function handleDriverMap() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['driver_id'])) {
        header('Location: index.php?page=driver_login'); exit;
    }

    $bookingModel = new BookingModel();
    $my_rides     = $bookingModel->getDriverRides($_SESSION['driver_id']);

    $current_ride = null;
    foreach ($my_rides as $r) {
        if (in_array($r['status'], ['accepted', 'confirmed'])) {
            $current_ride = $r;
            break;
        }
    }
    include 'views/driver_map.php';
}

function handleDriverLogout() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['driver_id'])) {
        $driverModel = new DriverModel();
        $driverModel->setOffline($_SESSION['driver_id']); // ✅
    }
    session_unset();
    session_destroy();
    header('Location: index.php?page=driver_login'); exit;
}

function handleDriverToggleStatus() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['driver_id'])) {
        header('Location: index.php?page=driver_login'); exit;
    }
    $driverModel = new DriverModel();
    $driver      = $driverModel->getDriverById($_SESSION['driver_id']);
    $newStatus   = $driver['is_online'] ? 0 : 1;
    $driverModel->setOnlineStatus($_SESSION['driver_id'], $newStatus);
    $_SESSION['driver_msg_success'] = $newStatus ? 'You are now Online! 🟢' : 'You are now Offline. 🔴';
    header('Location: index.php?page=driver_dashboard'); exit;
}
?>
