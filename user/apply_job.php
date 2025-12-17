<?php
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

// Helper function for flash messages
function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

if ($job_id <= 0) {
    set_flash_message("Error: Invalid job ID for application.", 'danger');
    header('Location: search_jobs.php');
    exit();
}

// 1. CHECK if job exists AND FETCH company_id (CRITICAL STEP)
$job_query = "SELECT company_id, job_title FROM jobs WHERE id = $job_id";
$job_result = mysqli_query($conn, $job_query);

if (mysqli_num_rows($job_result) == 0) {
    set_flash_message("Error: Job not found.", 'danger');
    header('Location: search_jobs.php');
    exit();
}

$job_data = mysqli_fetch_assoc($job_result);
$company_id = $job_data['company_id']; 
$job_title = $job_data['job_title'];

// 2. CHECK if user has already applied
$check_query = "SELECT * FROM applications WHERE job_id = $job_id AND user_id = $user_id";
$check_result = mysqli_query($conn, $check_query);
$already_applied = mysqli_num_rows($check_result) > 0;

if ($already_applied) {
    set_flash_message("You have already applied for this job.", 'info');
    header("Location: job_details.php?id=$job_id");
    exit();
}


// 3. PROCESS APPLICATION SUBMISSION (Form POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resume_path = null;

    // Handle file upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['resume']['tmp_name'];
        $file_name = basename($_FILES['resume']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Sanitize and create unique file name
        $new_file_name = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
        $upload_dir = '../assets/resumes/';
        // Ensure the directory exists and is writable: C:\xampp\htdocs\proadvanced\assets\resumes\
        if (!is_dir($upload_dir)) {
             // Attempt to create the directory if it doesn't exist
             mkdir($upload_dir, 0777, true);
        }

        $target_file = $upload_dir . $new_file_name;
        $resume_path = 'assets/resumes/' . $new_file_name; // Path stored in DB

        // Check file extension
        if (!in_array($file_ext, ['pdf', 'doc', 'docx'])) {
            $error = "Only PDF, DOC, and DOCX files are allowed.";
        }
        
        // Move file
        if (empty($error) && !move_uploaded_file($file_tmp, $target_file)) {
            $error = "Failed to upload file. Check permissions for " . $upload_dir;
        }
    } else {
        $error = "Please upload your resume.";
    }


    if (empty($error)) {
        // SQL query to insert application (LINE 92 in your setup)
        $application_date = date('Y-m-d H:i:s');
        
        // NOTE: Column names wrapped in backticks (`) for safety
        $insert_query = "INSERT INTO `applications` 
                         (`job_id`, `company_id`, `user_id`, `application_date`, `resume_path`, `status`) 
                         VALUES 
                         ('$job_id', '$company_id', '$user_id', '$application_date', '$resume_path', 'Pending')"; 

        if (mysqli_query($conn, $insert_query)) {
            set_flash_message("Your application for '$job_title' has been submitted successfully!", 'success');
            header("Location: job_details.php?id=$job_id");
            exit();
        } else {
            // Error when inserting into the database
            $error = "Failed to submit application. Database Error: " . mysqli_error($conn);
            // Fallthrough to display error on the form page
        }
    }
    
    // If there was an error during POST, update the flash message
    if (!empty($error)) {
        set_flash_message($error, 'danger');
        // Continue to display the form page
    }
}

$pageTitle = "Apply for $job_title";
include_once '../includes/header.php';

// Display flash message if set by POST failure (or previous error)
$flash_message = $_SESSION['flash_message'] ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
if (isset($_SESSION['flash_message'])) {
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

?>

<div class="container" style="max-width: 600px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    
    <h1 style="font-size: 24px; color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
        Apply for: <?php echo htmlspecialchars($job_title); ?>
    </h1>

    <?php if ($flash_message): ?>
        <div style="padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; <?php 
            if ($flash_type == 'success') echo 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;';
            elseif ($flash_type == 'danger') echo 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;';
            else echo 'background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;';
        ?>">
            <?php echo $flash_message; ?>
        </div>
    <?php endif; ?>

    <p style="color: #666; margin-bottom: 25px;">
        To apply, please confirm your details and upload your latest resume (PDF, DOC, DOCX only).
    </p>

    <form method="POST" action="apply_job.php?id=<?php echo $job_id; ?>" enctype="multipart/form-data">
        
        <div style="margin-bottom: 15px;">
            <label for="resume" style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Upload Resume</label>
            <input type="file" name="resume" id="resume" required 
                   style="display: block; width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            <small style="color: #888;">Max size: 5MB. Formats: PDF, DOC, DOCX.</small>
        </div>

        <button type="submit" 
                style="width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">
            Submit Application
        </button>
        <a href="job_details.php?id=<?php echo $job_id; ?>" 
           style="display: block; margin-top: 15px; text-align: center; color: #6c757d; text-decoration: none;">
            Cancel and Go Back
        </a>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>