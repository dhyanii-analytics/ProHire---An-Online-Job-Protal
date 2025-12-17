<?php
require_once '../includes/config.php';

// Set default message variables
$message = '';
$message_type = 'danger';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// 2. Input validation and sanitation
$user_id = $_SESSION['user_id'];
// Use intval for safe integer conversion
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check for valid job ID
if ($job_id <= 0) {
    // If job ID is invalid, set an error message and redirect to the job search page
    $_SESSION['flash_message'] = "Error: Invalid job ID provided for saving.";
    $_SESSION['flash_type'] = 'danger';
    header('Location: search_jobs.php'); 
    exit();
}

// 3. Check if the job is already saved by the user
// NOTE: Using backticks (`) for all identifiers to prevent SQL errors.
$check_query = "SELECT * FROM `saved_jobs` WHERE `user_id` = '$user_id' AND `job_id` = '$job_id'";
$check_result = mysqli_query($conn, $check_query);

if (!$check_result) {
    // Handle error during the SELECT operation itself
    $message = "Database error during check: " . mysqli_error($conn);
    $message_type = 'danger';
}
elseif (mysqli_num_rows($check_result) > 0) {
    // Job already saved
    $message = "This job is already saved to your list.";
    $message_type = 'info';
} else {
    // 4. Save the job.
    
    // CORRECTION APPLIED HERE:
    // We only insert user_id and job_id. We rely on the database's DEFAULT CURRENT_TIMESTAMP 
    // for the 'saved_on' column to avoid the 'Unknown column' error.
    $insert_query = "INSERT INTO `saved_jobs` (`user_id`, `job_id`) 
                     VALUES ('$user_id', '$job_id')";

    if (mysqli_query($conn, $insert_query)) {
        $message = "Job successfully saved to your list!";
        $message_type = 'success';
    } else {
        // CATCH ANY DATABASE ERROR
        $message = "Error saving job: Database failure. Details: " . mysqli_error($conn);
        $message_type = 'danger';
    }
}

// 5. Store message and redirect user back to the job details page
$_SESSION['flash_message'] = $message;
$_SESSION['flash_type'] = $message_type;

// Redirect back to the job details page
header("Location: job_details.php?id=$job_id");
exit();

?>