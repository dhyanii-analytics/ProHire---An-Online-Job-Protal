<?php
require_once 'config.php';
// Start session here if it wasn't done in config, though it's already in config.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>">
                    <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="ProHire Logo" class="logo-image"> 
                    <span>ProHire</span> 
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/search_jobs.php">Find Jobs</a></li>
                    <li><a href="<?php echo BASE_URL; ?>company/index.php">For Companies</a></li>
                    <li><a href="<?php echo BASE_URL; ?>about.php">About Us</a></li>
                    <li><a href="<?php echo BASE_URL; ?>contact.php">Contact</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>user/dashboard.php" class="btn">My Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>logout.php" class="btn">Logout</a>
                <?php elseif(isset($_SESSION['company_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>company/dashboard.php" class="btn">Company Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>logout.php" class="btn">Logout</a>
                <?php elseif(isset($_SESSION['admin_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="btn">Admin Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>logout.php" class="btn">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>login.php" class="btn">Login</a>
                    <a href="<?php echo BASE_URL; ?>register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>