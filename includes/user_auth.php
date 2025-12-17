<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the config file is accessible first
require_once __DIR__ . '/config.php';

// --- User Authentication Logic ---
// Check if the user is NOT logged in (using 'user_id' from the session)
if (!isset($_SESSION['user_id'])) {
    // Redirect unauthenticated users to the login page
    header('Location: ../login.php');
    exit();
}

// Optional: Fetch user details for display or further checks (recommended)
$user_id = $_SESSION['user_id'];

// FIX APPLIED: Changed 'user_id' to 'id' to match the database column name
$user_query = "SELECT full_name FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $authenticated_user = mysqli_fetch_assoc($result);
    // The user variable is now accessible on all user pages
} else {
    // Optional: Add error logging for database connection issues
    // error_log("Database error in user_auth.php: " . mysqli_error($conn));
}
?>