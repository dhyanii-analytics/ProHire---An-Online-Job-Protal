<?php
require_once 'includes/config.php';

// require_once 'includes/functions.php';

// 1. Get Job ID and validate it safely
$job_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$job_id) {
    // If no ID is provided, redirect to the search page
    header('Location: ' . BASE_URL . 'search.php');
    exit();
}

// 2. FETCH JOB DETAILS (Secure: Prepared Statement)
$job = false;
$query = "
    SELECT j.*, c.company_name, c.logo_path, c.description AS company_description, c.id AS company_id
    FROM jobs j
    JOIN companies c ON j.company_id = c.id
    WHERE j.id = ?
";

$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $job_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $job = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

// Handle job not found
if (!$job) {
    $pageTitle = 'Job Not Found';
    include_once 'includes/header.php';
    // 1) CREATE A BACK BUTTON in case of error
    echo '<div class="container" style="padding: 50px 0;"><div class="no-results"><h3>Job Not Found</h3><p>The job you are looking for does not exist or the link is invalid.</p><a href="javascript:history.back()" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Go Back</a></div></div>';
    include_once 'includes/footer.php';
    exit();
}

$pageTitle = htmlspecialchars($job['job_title']) . ' at ' . htmlspecialchars($job['company_name']);
include_once 'includes/header.php';

// Prepare data for display
$company_name = htmlspecialchars($job['company_name']);
$logo_path = !empty($job['logo_path']) ? BASE_URL . $job['logo_path'] : BASE_URL . 'assets/images/company-placeholder.png';
$is_user_logged_in = isset($_SESSION['user_id']);
$user_id = $is_user_logged_in ? $_SESSION['user_id'] : 0;
$company_id = $job['company_id'];

// Check if job is saved (if user is logged in)
$is_saved = false;
if ($is_user_logged_in) {
    $saved_query = "SELECT id FROM saved_jobs WHERE user_id = ? AND job_id = ?";
    $saved_stmt = mysqli_prepare($conn, $saved_query);
    if ($saved_stmt) {
        mysqli_stmt_bind_param($saved_stmt, "ii", $user_id, $job_id);
        mysqli_stmt_execute($saved_stmt);
        mysqli_stmt_store_result($saved_stmt);
        if (mysqli_stmt_num_rows($saved_stmt) > 0) {
            $is_saved = true;
        }
        mysqli_stmt_close($saved_stmt);
    }
}

// Check if user has already applied (if user is logged in)
$has_applied = false;
if ($is_user_logged_in) {
    $applied_query = "SELECT id FROM applications WHERE user_id = ? AND job_id = ?";
    $applied_stmt = mysqli_prepare($conn, $applied_query);
    if ($applied_stmt) {
        mysqli_stmt_bind_param($applied_stmt, "ii", $user_id, $job_id);
        mysqli_stmt_execute($applied_stmt);
        mysqli_stmt_store_result($applied_stmt);
        if (mysqli_stmt_num_rows($applied_stmt) > 0) {
            $has_applied = true;
        }
        mysqli_stmt_close($applied_stmt);
    }
}

?>

<div class="container job-details-page" style="padding: 50px 0;">
    <a href="javascript:history.back()" class="btn btn-outline-secondary" style="margin-bottom: 30px; font-size: 1.05rem;">
        <i class="fas fa-arrow-left"></i> Back to Jobs
    </a>

    <div class="row">
        <div class="col-lg-8">
            <div class="job-header-section">
                <div class="job-company-info" style="display: flex; align-items: center; margin-bottom: 20px;">
                    
                    <img src="<?php echo $logo_path; ?>" alt="<?php echo $company_name; ?> Logo" style="height: 120px; width: auto; max-width: 120px; object-fit: contain; margin-right: 15px; border-radius: 8px; border: 1px solid #e0e0e0;">
                    
                    <div class="job-title-meta">
                        <h1 style="font-size: 3rem; margin-bottom: 5px;"><?php echo htmlspecialchars($job['job_title']); ?></h1>
                        
                        <p class="company-name-link" style="font-size: 1.25rem; font-weight: 600; color: #4f46e5;">
                            <?php echo $company_name; ?>
                        </p>
                        
                        <div class="job-meta-details" style="font-size: 1.15rem; margin-top: 10px;">
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                            <span><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($job['job_type']); ?></span>
                            <span><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['salary']); ?></span>
                            <span><i class="fas fa-clock"></i> Deadline: <?php echo date('M d, Y', strtotime($job['deadline'])); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="job-actions-buttons">
                    <?php if ($is_user_logged_in): ?>
                        <?php if ($has_applied): ?>
                            <button class="btn btn-success" disabled style="font-size: 1.05rem;"><i class="fas fa-check"></i> Applied</button>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>user/apply_job.php?id=<?php echo $job_id; ?>" class="btn btn-primary" style="font-size: 1.05rem;">Apply Now</a>
                        <?php endif; ?>

                        <?php if ($is_saved): ?>
                            <a href="<?php echo BASE_URL; ?>user/unsave_job.php?id=<?php echo $job_id; ?>" class="btn btn-outline-danger" style="font-size: 1.05rem;"><i class="fas fa-bookmark"></i> Saved</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>user/save_job.php?id=<?php echo $job_id; ?>" class="btn btn-outline-secondary" style="font-size: 1.05rem;"><i class="far fa-bookmark"></i> Save Job</a>
                        <?php endif; ?>

                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>login.php" class="btn btn-primary" style="font-size: 1.05rem;">Login to Apply</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <hr style="border-top: 1px solid #ccc; margin: 30px 0;">

            <div class="job-detail-content">
                <h2 style="font-size: 2rem;">Job Description</h2>
                <div class="job-section-body" style="font-size: 1.15rem; line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                </div>
            </div>

            <div class="job-detail-content">
                <h2 style="font-size: 2rem;">Key Requirements</h2>
                <div class="job-section-body" style="font-size: 1.15rem; line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($job['requirements'])); ?>
                </div>
            </div>

             <div class="job-detail-content">
                <h2 style="font-size: 2rem;">Benefits Offered</h2>
                <div class="job-section-body" style="font-size: 1.15rem; line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($job['benefits'])); ?>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="sidebar-card company-card">
                <h3 style="font-size: 1.5rem;">About <?php echo $company_name; ?></h3>
                <p style="font-size: 1.05rem;"><?php echo substr(htmlspecialchars($job['company_description']), 0, 200) . '...'; ?></p>
                <a href="<?php echo BASE_URL; ?>register.php" class="btn btn-primary btn-block" style="font-size: 1.05rem;">Register to See Details</a>
            </div>

            <div class="sidebar-card similar-jobs-card">
                <h3 style="font-size: 1.5rem;">Similar Jobs</h3>
                <div class="similar-jobs-list">
                    <?php
                    // 3. FETCH SIMILAR JOBS (Based on location or job type, exclude current job)
                    $similar_query = "
                        SELECT id, job_title, location, job_type, salary
                        FROM jobs
                        WHERE (location = ? OR job_type = ?) AND id != ?
                        ORDER BY posted_on DESC
                        LIMIT 3
                    ";

                    $similar_stmt = mysqli_prepare($conn, $similar_query);
                    $similar_result = false;

                    if ($similar_stmt) {
                        mysqli_stmt_bind_param($similar_stmt, "ssi", $job['location'], $job['job_type'], $job_id);
                        mysqli_stmt_execute($similar_stmt);
                        $similar_result = mysqli_stmt_get_result($similar_stmt);
                    }

                    if ($similar_result && mysqli_num_rows($similar_result) > 0) {
                        while ($similar_job = mysqli_fetch_assoc($similar_result)) {
                    ?>
                        <div class="similar-job-item">
                            <h4 style="font-size: 1.25rem;"><?php echo htmlspecialchars($similar_job['job_title']); ?></h4>
                            <div class="job-meta-small" style="font-size: 0.95rem;">
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($similar_job['location']); ?></span>
                                <span><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($similar_job['job_type']); ?></span>
                            </div>
                            <a href="<?php echo BASE_URL; ?>Job_details.php?id=<?php echo $similar_job['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    <?php
                        } // end while loop
                        if ($similar_stmt) {
                            mysqli_stmt_close($similar_stmt);
                        }
                    } else { // end if similar jobs exist
                    ?>
                        <p>No similar jobs found at this time.</p>
                    <?php } // end else ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>