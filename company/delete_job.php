<?php
require_once '../includes/config.php';

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header('Location: ../login.php?role=company');
    exit();
}

$company_id = $_SESSION['company_id'];

// Get job ID from URL
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($job_id <= 0) {
    header('Location: dashboard.php');
    exit();
}

// Check if job exists and belongs to this company
$query = "SELECT id FROM jobs WHERE id = $job_id AND company_id = $company_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    // Job not found or doesn't belong to this company
    header('Location: dashboard.php');
    exit();
}

// Delete applications for this job first
$delete_applications_query = "DELETE FROM applications WHERE job_id = $job_id";
mysqli_query($conn, $delete_applications_query);

// Delete the job
$delete_job_query = "DELETE FROM jobs WHERE id = $job_id AND company_id = $company_id";

if (mysqli_query($conn, $delete_job_query)) {
    // Set success message
    $_SESSION['success_message'] = "Job deleted successfully!";
} else {
    // Set error message
    $_SESSION['error_message'] = "Failed to delete job. Please try again.";
}

// Redirect back to manage jobs page
header('Location: manage_jobs.php');
exit();
?>