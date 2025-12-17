<?php
require_once '../includes/config.php';

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header('Location: ../login.php');
    exit();
}

$company_id = $_SESSION['company_id'];
$pageTitle = 'Company Dashboard';
include_once '../includes/header.php';

// Get company details
$query = "SELECT * FROM companies WHERE id = $company_id";
$result = mysqli_query($conn, $query);
$company = mysqli_fetch_assoc($result);

// Get job statistics
$jobs_query = "SELECT COUNT(*) as total FROM jobs WHERE company_id = $company_id";
$jobs_result = mysqli_query($conn, $jobs_query);
$jobs = mysqli_fetch_assoc($jobs_result);

// Get applications statistics (Total)
$applications_query = "SELECT COUNT(*) as total FROM applications a 
                       JOIN jobs j ON a.job_id = j.id 
                       WHERE j.company_id = $company_id";
$applications_result = mysqli_query($conn, $applications_query);
$applications = mysqli_fetch_assoc($applications_result);

// --------------------------------------------------------------------------
// START: FIX FOR SHORTLISTED COUNT
// Query to count applications with status 'Shortlisted' for this company's jobs
$shortlisted_query = "SELECT COUNT(*) as total FROM applications a 
                      JOIN jobs j ON a.job_id = j.id 
                      WHERE j.company_id = $company_id AND a.status = 'Shortlisted'";
$shortlisted_result = mysqli_query($conn, $shortlisted_query);
$shortlisted = mysqli_fetch_assoc($shortlisted_result);
// END: FIX FOR SHORTLISTED COUNT
// --------------------------------------------------------------------------

// Get recent applications
$recent_applications_query = "SELECT a.*, j.job_title, u.full_name 
                             FROM applications a 
                             JOIN jobs j ON a.job_id = j.id 
                             JOIN users u ON a.user_id = u.id 
                             WHERE j.company_id = $company_id 
                             ORDER BY a.applied_on DESC 
                             LIMIT 5";
$recent_applications_result = mysqli_query($conn, $recent_applications_query);
?>

<div class="container dashboard">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo $company['company_name']; ?>!</h1>
        <div class="header-actions" style="display: flex; gap: 10px;">
            <a href="post_job.php" class="btn btn-primary">Post a New Job</a>
            <a href="profile.php" class="btn btn-secondary" style="background-color: #6c757d; color: white;">Edit Profile</a>
        </div>
    </div>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $jobs['total']; ?></h3>
                <p>Active Jobs</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $applications['total']; ?></h3>
                <p>Total Applications</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $shortlisted['total']; ?></h3>
                <p>Shortlisted</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-handshake"></i>
            </div>
            <div class="stat-info">
                <h3>0</h3>
                <p>Hired</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-content">
        <h2>Recent Applications</h2>
        
        <?php if (mysqli_num_rows($recent_applications_result) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Job Title</th>
                        <th>Applied On</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($app = mysqli_fetch_assoc($recent_applications_result)): ?>
                        <tr>
                            <td><?php echo $app['full_name']; ?></td>
                            <td><?php echo $app['job_title']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($app['applied_on'])); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($app['status']); ?>"><?php echo $app['status']; ?></span></td>
                            <td>
                                <a href="view_application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="text-center" style="margin-top: 20px;">
                <a href="applications.php" class="btn">View All Applications</a>
            </div>
        <?php else: ?>
            <p>No applications received yet. <a href="post_job.php">Post a job</a> to start receiving applications.</p>
        <?php endif; ?>
    </div>
    
    <div class="dashboard-content" style="margin-top: 30px;">
        <h2>Your Job Listings</h2>
        
        <?php
        $jobs_list_query = "SELECT * FROM jobs WHERE company_id = $company_id ORDER BY posted_on DESC LIMIT 5";
        $jobs_list_result = mysqli_query($conn, $jobs_list_query);
        
        if (mysqli_num_rows($jobs_list_result) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Location</th>
                        <th>Posted On</th>
                        <th>Applications</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($job = mysqli_fetch_assoc($jobs_list_result)): 
                        // Get application count for this job
                        $app_count_query = "SELECT COUNT(*) as count FROM applications WHERE job_id = {$job['id']}";
                        $app_count_result = mysqli_query($conn, $app_count_query);
                        $app_count = mysqli_fetch_assoc($app_count_result)['count'];
                    ?>
                        <tr>
                            <td><?php echo $job['job_title']; ?></td>
                            <td><?php echo $job['location']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($job['posted_on'])); ?></td>
                            <td><?php echo $app_count; ?></td>
                            <td>
                                <a href="view_application.php?id=<?php echo $job['id']; ?>" class="btn btn-sm">View Applications</a>
                                <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm">Edit</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="text-center" style="margin-top: 20px;">
                <a href="manage_jobs.php" class="btn">View All Jobs</a>
            </div>
        <?php else: ?>
            <p>You haven't posted any jobs yet. <a href="post_job.php">Post your first job</a></p>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>