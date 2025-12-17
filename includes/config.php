<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pro_db');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    // Log the error
    $log_file = __DIR__ . '/../logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] Database connection failed: " . mysqli_connect_error() . "\n";
    
    // Create logs directory if it doesn't exist
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    // Append to log file
    file_put_contents($log_file, $log_message, FILE_APPEND);
    
    // Redirect to error page
    header('Location: error.php?code=500&message=Database connection failed');
    exit();
}

// Start session
session_start();

// Base URL - Changed to match the exact folder case for reliability
define('BASE_URL', '/proadvanced/');

// Site name - FIXED to match the project name
define('SITE_NAME', 'ProAdvanced');

// Development mode (set to false in production)
define('DEV_MODE', true);

// Error reporting
if (DEV_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Include functions using absolute path
require_once __DIR__ . '/functions.php';
?>