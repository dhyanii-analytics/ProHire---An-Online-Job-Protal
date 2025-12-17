<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the database config file is accessible first
require_once __DIR__ . '/config.php'; 

// --- Admin Authentication Logic ---
// 1. Check if the admin is NOT logged in (using 'admin_id' as the key)
if (!isset($_SESSION['admin_id'])) {
    // 2. Redirect unauthenticated users to the login page (one level up)
    header('Location: ../login.php');
    exit();
}

// Optional: Fetch admin details if needed later (e.g., for display)
$admin_id = $_SESSION['admin_id'];
// You can add a query here to fetch the admin's name from the database if necessary.
?>