<?php
require_once 'models/AdminModel.php';
require_once 'models/ComplaintModel.php';

function requireAdmin() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin_id'])) {
        header('Location: index.php?page=admin_login');
        exit;
    }
}

function handleAdminLogin() {
    $model = new AdminModel();
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
        $email    = isset($_POST['email'])    ? $_POST['email']    : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $result   = $model->login($email, $password);
        if ($result['success']) {
            header('Location: index.php?page=admin_dashboard');
            exit;
        } else {
            $error = $result['message'];
        }
    }
    include 'views/admin_login.php';
}

function handleAdminDashboard() {
    requireAdmin();
    $model           = new AdminModel();
    $users           = $model->getAllUsers();
    $drivers         = $model->getAllDrivers();
    $rides           = $model->getAllRides();
    $pending_requests = $model->getPendingRideRequests(); // ✅ added
    include 'views/admin_dashboard.php';
}

function handleAdminManageUsers() {
    requireAdmin();
    $model   = new AdminModel();
    $success = '';
    $error   = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = intval($_POST['user_id']);
        if (isset($_POST['block'])) {
            $model->blockUser($user_id);
            $success = 'User blocked successfully.';
        } elseif (isset($_POST['unblock'])) {
            $model->unblockUser($user_id);
            $success = 'User unblocked successfully.';
        }
    }
    $users = $model->getAllUsers();
    include 'views/admin_users.php';
}

function handleAdminManageDrivers() {
    requireAdmin();
    $model   = new AdminModel();
    $success = '';
    $error   = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $driver_id = intval($_POST['driver_id']);
        if (isset($_POST['approve'])) {
            $model->approveDriver($driver_id);
            $success = 'Driver approved successfully.';
        } elseif (isset($_POST['reject'])) {
            $model->rejectDriver($driver_id);
            $success = 'Driver rejected.';
        }
    }
    $drivers = $model->getAllDrivers();
    include 'views/admin_drivers.php';
}

function handleAdminComplaints() {
    requireAdmin();
    $model      = new ComplaintModel();
    $success    = '';
    $error      = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $complaint_id    = intval($_POST['complaint_id']);
        $admin_response  = trim($_POST['admin_response'] ?? '');

        if (isset($_POST['resolve_complaint'])) {
            $model->resolveComplaint($complaint_id, $_SESSION['admin_id'], $admin_response);
            $success = 'Complaint #'.$complaint_id.' resolved.';
        } elseif (isset($_POST['close_complaint'])) {
            $model->updateStatus($complaint_id, 'closed');
            $success = 'Complaint #'.$complaint_id.' closed.';
        }
    }

    $complaints = $model->getAllComplaints();
    include 'views/admin_complaints.php';
}

function handleAdminCreateAdmin() {
    requireAdmin();
    if ($_SESSION['admin_role'] !== 'super_admin') {
        die('Access denied. Only Super Admin can create admin accounts.');
    }
    $model   = new AdminModel();
    $error   = '';
    $success = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])) {
        $result = $model->createAdmin($_POST);
        if ($result['success']) { $success = $result['message']; }
        else                    { $error   = $result['message']; }
    }
    include 'views/admin_create.php';
}

function handleAdminLogout() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    session_unset();
    session_destroy();
    header('Location: index.php?page=admin_login');
    exit;
}
?>
