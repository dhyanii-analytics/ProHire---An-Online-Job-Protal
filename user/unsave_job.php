<?php
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Set default message variables
$message = '';
$message_type = 'danger'; // Default to failure

if ($job_id <= 0) {
    $message = "Error: Invalid job ID provided for removal.";
} else {
    // Perform the deletion query
    // We delete the record that matches both the user ID and the job ID
    $delete_query = "DELETE FROM saved_jobs WHERE user_id = '$user_id' AND job_id = '$job_id'";

    if (mysqli_query($conn, $delete_query)) {
        // Check if any rows were affected (meaning a job was actually deleted)
        if (mysqli_affected_rows($conn) > 0) {
            $message = "Job successfully removed from your saved list.";
            $message_type = 'success';
        } else {
            $message = "Error: The job was not found in your saved list.";
            $message_type = 'info';
        }
    } else {
        $message = "Database error: Could not remove job. " . mysqli_error($conn);
    }
}

// Store message and redirect user back to the saved jobs list
$_SESSION['flash_message'] = $message;
$_SESSION['flash_type'] = $message_type;

// Redirect back to the list of saved jobs
header("Location: saved_jobs.php");
exit();

// The following is just a fallback display, redirection is the primary action.
?>

<div style="max-width: 600px; margin: 50px auto; padding: 20px; text-align: center; border-radius: 8px; font-family: Arial, sans-serif;
     <?php 
        if ($message_type == 'success') echo 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;';
        elseif ($message_type == 'danger') echo 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;';
        else echo 'background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;';
     ?>
     ">
    <p style="margin: 0; font-weight: bold;"><?php echo $message; ?></p>
    <p style="margin-top: 10px;"><a href="saved_jobs.php" style="color: #007bff;">Click here to return to saved jobs.</a></p>
</div>