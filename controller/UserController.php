<?php
require_once 'models/UserModel.php';

function handleRegister() {
    $model   = new UserModel();
    $error   = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        $result = $model->register($_POST);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
    include 'views/register.php';
}

function handleLogin() {
    $model = new UserModel();
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $email    = isset($_POST['email'])    ? $_POST['email']    : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $result   = $model->login($email, $password);

        if ($result['success']) {
            // Fetch user_type and store in session
            $user = $model->getUserById($_SESSION['user_id']);
            if ($user) {
                $_SESSION['user_type'] = $user['user_type'];
            }
            header('Location: index.php?page=home');
            exit;
        } else {
            $error = $result['message'];
        }
    }
    include 'views/login.php';
}

function handleLogout() {
    $model = new UserModel();
    $model->logout();
    header('Location: index.php?page=login');
    exit;
}

function handleProfile() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }

    $model   = new UserModel();
    $error   = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $updated = $model->updateUser($_SESSION['user_id'], $_POST);
        if ($updated) {
            $_SESSION['full_name'] = trim(strip_tags($_POST['full_name']));
            if (isset($_POST['user_type'])) {
                $_SESSION['user_type'] = trim(strip_tags($_POST['user_type']));
            }
            $success = 'Profile updated successfully!';
        } else {
            $error = 'Update failed. Please try again.';
        }
    }

    $user = $model->getUserById($_SESSION['user_id']);
    include 'views/profile.php';
}
?>
