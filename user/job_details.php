<?php
require_once '../includes/config.php';

// Check if user is logged in (DO NOT REDIRECT YET - store status)
$is_registered_user = isset($_SESSION['user_id']);
$user_id = $is_registered_user ? $_SESSION['user_id'] : 0; // Set user_id if logged in

// Get job ID
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($job_id <= 0) {
    header('Location: search_jobs.php');
    exit();
}

// Get job details (accessible to everyone)
$query = "SELECT j.*, c.company_name, c.description as company_description, c.logo_path, c.address 
          FROM jobs j 
          JOIN companies c ON j.company_id = c.id 
          WHERE j.id = $job_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header('Location: search_jobs.php');
    exit();
}

$job = mysqli_fetch_assoc($result);

// 1. CHECK APPLICATION STATUS (ONLY run if user is logged in)
$already_applied = false;
$application_status = null; // New variable to store the status
if ($is_registered_user) {
    // MODIFIED QUERY to fetch the status
    $application_query = "SELECT status FROM applications WHERE job_id = $job_id AND user_id = $user_id";
    $application_result = mysqli_query($conn, $application_query);
    
    if (mysqli_num_rows($application_result) > 0) {
        $already_applied = true;
        $application_data = mysqli_fetch_assoc($application_result);
        $application_status = $application_data['status']; // Store the status
    }
}


// 2. CHECK SAVED STATUS (NEW LOGIC - ONLY run if user is logged in)
$is_saved = false;
if ($is_registered_user) {
    $save_check_query = "SELECT * FROM saved_jobs WHERE job_id = $job_id AND user_id = $user_id";
    $save_check_result = mysqli_query($conn, $save_check_query);
    $is_saved = mysqli_num_rows($save_check_result) > 0;
}


// 3. FLASH MESSAGE LOGIC & HELPER FUNCTION
function get_message_styles($style) {
    return match ($style) {
        'success' => 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;',
        'danger' => 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;',
        'info' => 'background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;',
        default => 'background-color: #e9ecef; color: #495057; border: 1px solid #ced4da;',
    };
}

// NEW: Helper function to color the application status badge
function get_status_styles($status) {
    return match (strtolower($status)) {
        'pending' => 'background-color: #ffc107; color: #343a40; border-color: #ffc107;',
        'reviewed' => 'background-color: #17a2b8; color: white; border-color: #17a2b8;',
        'interview' => 'background-color: #007bff; color: white; border-color: #007bff;',
        'accepted' => 'background-color: #28a745; color: white; border-color: #28a745;',
        'rejected' => 'background-color: #dc3545; color: white; border-color: #dc3545;',
        default => 'background-color: #6c757d; color: white; border-color: #6c757d;',
    };
}

// Retrieve and clear flash messages from session (used by save_job.php/unsave_job.php)
$flash_message = $_SESSION['flash_message'] ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';

if (isset($_SESSION['flash_message'])) {
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}
// --- END FLASH MESSAGE LOGIC ---


$pageTitle = $job['job_title'] . ' - ' . $job['company_name'];
include_once '../includes/header.php';
?>

<div class="container" style="padding: 50px 0;">
    
    <?php if ($flash_message): ?>
        <div style="max-width: 900px; margin: 0 auto 20px auto; padding: 15px; border-radius: 4px; font-weight: bold; <?php echo get_message_styles($flash_type); ?>">
            <?php echo $flash_message; ?>
        </div>
    <?php endif; ?>
    <div class="job-details">
        <div class="job-header">
            <img src="<?php echo !empty($job['logo_path']) ? BASE_URL . $job['logo_path'] : BASE_URL . 'assets/images/company-placeholder.png'; ?>" alt="<?php echo $job['company_name']; ?>" class="company-logo">
            <div class="job-info">
                <h1><?php echo $job['job_title']; ?></h1>
                <h2><?php echo $job['company_name']; ?></h2>
                <div class="job-meta">
                    <span><i class="fas fa-map-marker-alt"></i> <?php echo $job['location']; ?></span>
                    <span><i class="fas fa-briefcase"></i> <?php echo $job['job_type']; ?></span>
                    <span><i class="fas fa-clock"></i> <?php echo $job['experience_required']; ?></span>
                    <span><i class="fas fa-calendar"></i> Posted on <?php echo date('M d, Y', strtotime($job['posted_on'])); ?></span>
                </div>
            </div>
            <?php if ($already_applied && $application_status): ?>
            <div class="job-salary" style="display: flex; flex-direction: column; align-items: flex-end; justify-content: center; gap: 5px;">
                <h4 style="font-size: 0.9rem; color: #64748b; margin-bottom: 5px;">Application Status:</h4>
                <span style="font-size: 1.2rem; padding: 8px 15px; border-radius: 5px; font-weight: bold; border: 1px solid; <?php echo get_status_styles($application_status); ?>">
                    <?php echo htmlspecialchars($application_status); ?>
                </span>
            </div>
            <?php else: ?>
            <div class="job-salary">
                <h3><?php echo $job['salary']; ?></h3>
            </div>
            <?php endif; ?>
            </div>
        
        <div class="job-content">
            <div class="job-description">
                <h3>Job Description</h3>
                <p><?php echo nl2br($job['description']); ?></p>
            </div>
            
            <div class="job-requirements">
                <h3>Requirements</h3>
                <p><?php echo nl2br($job['requirements']); ?></p>
            </div>
            
            <div class="company-info">
                <h3>About Company</h3>
                <p><?php echo nl2br($job['company_description']); ?></p>
                <p><strong>Address:</strong> <?php echo $job['address']; ?></p>
            </div>
        </div>
        
        <div class="job-actions" style="padding: 20px 30px; background: #f8fafc; display: flex; gap: 15px; align-items: center;">
            
            <?php if ($is_registered_user): ?>
                
                <?php if ($already_applied): ?>
                    <span style="background-color: #6c757d; color: white; padding: 12px 25px; border-radius: 6px; font-size: 1.1em; font-weight: bold; display: inline-block;">
                        Applied <i class="fas fa-check"></i>
                    </span>
                <?php else: ?>
                    <a href="apply_job.php?id=<?php echo $job_id; ?>" 
                        style="background-color: #28a745; color: white; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-size: 1.1em; font-weight: bold; transition: background-color 0.3s;">
                        Apply Now
                    </a>
                <?php endif; ?>

                <?php if ($is_saved): ?>
                    <span style="background-color: #ffc107; color: #343a40; padding: 12px 25px; border-radius: 6px; font-size: 1.1em; font-weight: bold; display: inline-block;">
                        <i class="fas fa-bookmark"></i> Saved
                    </span>
                    <a href="unsave_job.php?id=<?php echo $job_id; ?>" 
                        style="background-color: #dc3545; color: white; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-size: 1.1em; transition: background-color 0.3s;">
                        Remove
                    </a>
                <?php else: ?>
                    <a href="save_job.php?id=<?php echo $job_id; ?>" 
                        style="background-color: #007bff; color: white; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-size: 1.1em; font-weight: bold; transition: background-color 0.3s;">
                        <i class="fas fa-bookmark"></i> Save Job
                    </a>
                <?php endif; ?>
                
            <?php else: ?>
                <a href="../register.php" 
                    style="background-color: #ffc107; color: #343a40; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-size: 1.1em; font-weight: bold; transition: background-color 0.3s;">
                    Register to Apply / Save
                </a>
            <?php endif; ?>
            <a href="search_jobs.php" class="btn" style="padding: 12px 25px; border-radius: 6px; text-decoration: none; font-size: 1.1em; background-color: #64748b; color: white;">Back to Jobs</a>
        </div>
    </div>
</div>

<style>
/* Existing CSS styles */
.job-details {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.job-header {
    display: flex;
    padding: 30px;
    border-bottom: 1px solid #e2e8f0;
    align-items: center;
}

.company-logo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 20px;
}

.job-info {
    flex: 1;
}

.job-info h1 {
    font-size: 1.8rem;
    margin-bottom: 5px;
    color: #334155;
}

.job-info h2 {
    font-size: 1.2rem;
    color: #64748b;
    margin-bottom: 10px;
}

.job-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.job-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #64748b;
}

.job-salary {
    text-align: right;
}

.job-salary h3 {
    font-size: 1.5rem;
    color: #10b981;
}

.job-content {
    padding: 30px;
}

.job-description, .job-requirements, .company-info {
    margin-bottom: 30px;
}

.job-description h3, .job-requirements h3, .company-info h3 {
    font-size: 1.3rem;
    margin-bottom: 15px;
    color: #4f46e5;
}

/* The job-actions CSS is now mostly defined inline to handle button styles */
.job-actions {
    /* Keep the original layout styles */
    padding: 20px 30px;
    background: #f8fafc;
    display: flex;
    gap: 15px;
}

/* Removed alert styles as they are now handled by the get_message_styles PHP function with inline CSS */

@media (max-width: 768px) {
    .job-header {
        flex-direction: column;
        text-align: center;
    }
    
    .company-logo {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .job-salary {
        text-align: center;
        margin-top: 15px;
    }
    
    .job-meta {
        justify-content: center;
    }
    
    .job-actions {
        flex-direction: column;
    }
}
</style>

<?php include_once '../includes/footer.php'; ?>