<?php
// Define the path using the robust __DIR__ method
$includes_path = __DIR__ . '/../includes/';

// Load configuration and admin authentication
require_once $includes_path . 'config.php';
require_once $includes_path . 'admin_auth.php'; // Ensures only admin can view

// 1. Get Company ID and validate it
// Use filter_input for safer data retrieval and casting to integer
$company_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Check if the ID is missing or invalid
if (!$company_id) {
    $_SESSION['error'] = "Invalid or missing company ID!";
    header('Location: manage_companies.php');
    exit();
}

// 2. FETCH COMPANY DETAILS (SECURE: Prepared Statement)
$company = false;
$query = "SELECT * FROM companies WHERE id = ?";

// Prepare the statement
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    // Bind the company ID (i = integer)
    mysqli_stmt_bind_param($stmt, "i", $company_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $company = mysqli_fetch_assoc($result);
    } 
    mysqli_stmt_close($stmt); // Close statement resources
}

// Handle company not found after database check
if (!$company) {
    $_SESSION['error'] = "Company not found!";
    header('Location: manage_companies.php');
    exit();
}

$pageTitle = 'View Company';
include_once $includes_path . 'header.php';

?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>Company Details</h1>
            <a href="manage_companies.php" class="btn btn-primary">Back to Companies</a>
        </div>
        
        <div class="dashboard-content">
            <div class="company-details">
                <div class="company-header" style="display: flex; align-items: center; gap: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                    
                    <div class="company-logo">
                        <?php 
                        // Use BASE_URL from config.php
                        $logo_src = !empty($company['logo_path']) ? BASE_URL . $company['logo_path'] : BASE_URL . 'assets/images/company-placeholder.png';
                        
                        // ADDED: Style for small, square logo display (80x80px)
                        $logo_style = 'style="width: 80px; height: 80px; object-fit: contain; border: 1px solid #ccc; border-radius: 4px;"';
                        ?>
                        <img src="<?php echo htmlspecialchars($logo_src); ?>" alt="<?php echo htmlspecialchars($company['company_name']); ?> Logo" <?php echo $logo_style; ?>>
                    </div>
                    
                    <div class="company-info" style="flex-grow: 1;">
                        <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($company['company_name']); ?></h2>
                        <div class="company-meta" style="display: flex; flex-wrap: wrap; gap: 15px; font-size: 0.95em;">
                            <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($company['email']); ?></span>
                            <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($company['phone']); ?></span>
                            <span><i class="fas fa-industry"></i> <?php echo htmlspecialchars($company['industry']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="company-body" style="padding-top: 20px;">
                    <div class="company-section">
                        <h3>Address</h3>
                        <p><?php echo nl2br(htmlspecialchars($company['address'])); ?></p>
                    </div>
                    
                    <div class="company-section">
                        <h3>Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($company['description'])); ?></p>
                    </div>
                    
                    <div class="company-section">
                        <h3>Jobs Posted</h3>
                        <?php
                        // 3. FETCH JOBS POSTED (SECURE: Prepared Statement)
                        $jobs_query = "SELECT id, job_title, location, job_type, posted_on FROM jobs WHERE company_id = ? ORDER BY posted_on DESC";
                        
                        $jobs_stmt = mysqli_prepare($conn, $jobs_query);
                        $jobs_result = false; // Initialize to false
                        
                        if ($jobs_stmt) {
                            mysqli_stmt_bind_param($jobs_stmt, "i", $company_id);
                            mysqli_stmt_execute($jobs_stmt);
                            $jobs_result = mysqli_stmt_get_result($jobs_stmt);
                        }
                        
                        if ($jobs_result && mysqli_num_rows($jobs_result) > 0):
                        ?>
                        <div class="jobs-list">
                            <?php while ($job = mysqli_fetch_assoc($jobs_result)): ?>
                            <div class="job-item">
                                <h4><?php echo htmlspecialchars($job['job_title']); ?></h4>
                                <div class="job-meta">
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                    <span><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($job['job_type']); ?></span>
                                    <span><i class="fas fa-calendar"></i> Posted: <?php echo date('M d, Y', strtotime($job['posted_on'])); ?></span>
                                </div>
                                <div class="job-actions">
                                    <a href="view_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php 
                            mysqli_stmt_close($jobs_stmt); // Close jobs statement
                        else: 
                        ?>
                        <p>No jobs posted by this company.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once $includes_path . 'footer.php'; ?>