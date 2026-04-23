<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>चल या!!! PATRAO – Goa's #1 Ride</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Theme Toggle -->
<button class="theme-toggle" id="themeToggle" title="Toggle Dark/Light Mode">
    <i class="fas fa-moon" id="themeIcon"></i>
</button>

<nav class="navbar">
    <div class="nav-container">
        <a href="index.php?page=home" class="logo-container">
            <div class="logo-ring-small">
                <div class="logo-inner-small">
                    <i class="fas fa-car-side logo-car-nav"></i>
                </div>
            </div>
            <div class="logo-text">
                <h1>चल या!!! PATRAO</h1>
                <p class="tagline"><i class="fas fa-bolt"></i> On Time. Every Time.</p>
            </div>
        </a>
        <ul class="nav-menu" id="navMenu">
            <li><a href="index.php?page=home" class="nav-link"><i class="fas fa-home"></i> Home</a></li>
            <?php if (isset($_SESSION['admin_id'])): ?>
                <li><a href="index.php?page=admin_dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="index.php?page=admin_users" class="nav-link"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="index.php?page=admin_drivers" class="nav-link"><i class="fas fa-id-card"></i> Drivers</a></li>
                <li><a href="index.php?page=admin_complaints" class="nav-link"><i class="fas fa-flag"></i> Complaints</a></li>
                <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin'): ?>
                <li><a href="index.php?page=admin_create" class="nav-link"><i class="fas fa-user-shield"></i> Add Admin</a></li>
                <?php endif; ?>
                <li><a href="index.php?page=admin_logout" class="nav-link nav-logout"><i class="fas fa-sign-out-alt"></i> <?php echo htmlspecialchars($_SESSION['full_name']); ?></a></li>
            <?php elseif (isset($_SESSION['driver_id'])): ?>
                <li><a href="index.php?page=driver_dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="index.php?page=driver_map" class="nav-link"><i class="fas fa-map-marked-alt"></i> Map</a></li>
                <li><a href="index.php?page=driver_ratings" class="nav-link"><i class="fas fa-star"></i> Ratings</a></li>
                <li><a href="index.php?page=driver_logout" class="nav-link nav-logout"><i class="fas fa-sign-out-alt"></i> <?php echo htmlspecialchars($_SESSION['full_name']); ?></a></li>
            <?php elseif (isset($_SESSION['user_id'])): ?>
                <li><a href="index.php?page=booking" class="nav-link"><i class="fas fa-car"></i> Book Ride</a></li>
                <li><a href="index.php?page=my_rides" class="nav-link"><i class="fas fa-list"></i> My Rides</a></li>
                <li><a href="index.php?page=complaint" class="nav-link"><i class="fas fa-flag"></i> Complaints</a></li>
                <li><a href="index.php?page=profile" class="nav-link"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['full_name']); ?></a></li>
                <li><a href="index.php?page=logout" class="nav-link nav-logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php else: ?>
                <li><a href="index.php?page=login" class="nav-link"><i class="fas fa-sign-in-alt"></i> User Login</a></li>
                <li><a href="index.php?page=register" class="nav-link"><i class="fas fa-user-plus"></i> Register</a></li>
                <li><a href="index.php?page=driver_login" class="nav-link"><i class="fas fa-car"></i> Driver Login</a></li>
                <li><a href="index.php?page=driver_register" class="nav-link"><i class="fas fa-id-card"></i> Driver Register</a></li>
                <li><a href="index.php?page=admin_login" class="nav-link nav-admin"><i class="fas fa-user-shield"></i> Admin</a></li>
            <?php endif; ?>
        </ul>
        <div class="hamburger" id="hamburger">
            <span class="bar"></span><span class="bar"></span><span class="bar"></span>
        </div>
    </div>
</nav>
<main class="main-content">
