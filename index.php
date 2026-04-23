<?php
session_start();

$page = isset($_GET['page']) ? $_GET['page'] : 'splash';

switch ($page) {

    case 'splash':
        include 'views/splash.php'; break;

    case 'home':
        include 'views/home.php'; break;

    // USER
    case 'register':
        include 'controllers/UserController.php'; handleRegister(); break;
    case 'login':
        include 'controllers/UserController.php'; handleLogin(); break;
    case 'logout':
        include 'controllers/UserController.php'; handleLogout(); break;
    case 'profile':
        include 'controllers/UserController.php'; handleProfile(); break;

    // BOOKING FLOW
    case 'booking':
        include 'controllers/BookingController.php'; handleBooking(); break;
    case 'select_driver':
        include 'controllers/FeatureController.php'; handleSelectDriver(); break;
    case 'otp_verify':
        include 'controllers/BookingController.php'; handleOtpVerify(); break;
    case 'payment':
        include 'controllers/BookingController.php'; handlePayment(); break;
    case 'my_rides':
        include 'controllers/BookingController.php'; handleMyRides(); break;
    case 'cancel':
        include 'controllers/BookingController.php'; handleCancel(); break;
    case 'thankyou':
        include 'views/thankyou.php'; break;

    // FEATURES
    case 'track_ride':
        include 'controllers/FeatureController.php'; handleTrackRide(); break;
    case 'rate_ride':
        include 'controllers/FeatureController.php'; handleRateRide(); break;
    case 'complaint':
        include 'controllers/FeatureController.php'; handleComplaint(); break;
    case 'apply_coupon':
        include 'controllers/BookingController.php'; handleApplyCoupon(); break;

    // DRIVER
    case 'driver_register':
        include 'controllers/DriverController.php'; handleDriverRegister(); break;
    case 'driver_login':
        include 'controllers/DriverController.php'; handleDriverLogin(); break;
    case 'driver_dashboard':
        include 'controllers/DriverController.php'; handleDriverDashboard(); break;
    case 'driver_logout':
        include 'controllers/DriverController.php'; handleDriverLogout(); break;
    case 'driver_ratings':
        include 'controllers/FeatureController.php'; handleDriverRatings(); break;
    case 'driver_complaint':
        include 'controllers/FeatureController.php'; handleComplaint(); break;
    case 'driver_ride_action':
        include 'controllers/FeatureController.php'; handleDriverRideAction(); break;
    case 'driver_map':
        include 'controllers/DriverController.php'; handleDriverMap(); break;
    case 'driver_toggle_status':                                                          
        include 'controllers/DriverController.php'; handleDriverToggleStatus(); break; 
    case 'reselect_driver':
        include 'controllers/BookingController.php'; handleReselectDriver(); break;  

    // ADMIN
    case 'admin_login':
        include 'controllers/AdminController.php'; handleAdminLogin(); break;
    case 'admin_dashboard':
        include 'controllers/AdminController.php'; handleAdminDashboard(); break;
    case 'admin_users':
        include 'controllers/AdminController.php'; handleAdminManageUsers(); break;
    case 'admin_drivers':
        include 'controllers/AdminController.php'; handleAdminManageDrivers(); break;
    case 'admin_create':
        include 'controllers/AdminController.php'; handleAdminCreateAdmin(); break;
    case 'admin_logout':
        include 'controllers/AdminController.php'; handleAdminLogout(); break;
    case 'admin_complaints':
        include 'controllers/AdminController.php'; handleAdminComplaints(); break;

    default:
        include 'views/splash.php'; break;
}
?>