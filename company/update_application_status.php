<?php
require_once '../includes/config.php';

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header('Location: ../login.php?role=company');
    exit();
}

$company_id = $_SESSION['company_id'];

// Get application ID and new status from URL
$application_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$new_status = isset($_GET['status']) ? $_GET['status'] : '';

if ($application_id <= 0 || empty($new_status)) {
    header('Location: applications.php');
    exit();
}

// Check if application exists and belongs to this company
$query = "SELECT a.id 
          FROM applications a 
          JOIN jobs j ON a.job_id = j.id 
          JOIN companies c ON j.company_id = c.id 
          WHERE a.id = $application_id AND c.id = $company_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    // Application not found or doesn't belong to this company
    header('Location: applications.php');
    exit();
}

// Update application status
$update_query = "UPDATE applications SET status = '$new_status' WHERE id = $application_id";

if (mysqli_query($conn, $update_query)) {
    // Set success message
    $_SESSION['success_message'] = "Application status updated successfully!";
} else {
    // Set error message
    $_SESSION['error_message'] = "Failed to update application status. Please try again.";
}

// Redirect back to the previous page
if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: applications.php');
}
exit();
?>