<?php
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$pageTitle = 'Dashboard';
include_once '../includes/header.php';

// Get user details
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Get application statistics
$applications_query = "SELECT COUNT(*) as total FROM applications WHERE user_id = $user_id";
$applications_result = mysqli_query($conn, $applications_query);
$applications = mysqli_fetch_assoc($applications_result);

// Get saved jobs count (if you implement this feature)
$saved_jobs_query = "SELECT COUNT(*) as total FROM saved_jobs WHERE user_id = $user_id";
$saved_jobs_result = mysqli_query($conn, $saved_jobs_query);
$saved_jobs = mysqli_fetch_assoc($saved_jobs_result);

// Get recommended jobs
$recommended_query = "SELECT j.*, c.company_name, c.logo_path 
                      FROM jobs j 
                      JOIN companies c ON j.company_id = c.id 
                      WHERE j.location LIKE '%{$user['location']}%' 
                      OR j.requirements LIKE '%{$user['skills']}%'
                      ORDER BY j.posted_on DESC 
                      LIMIT 5";
$recommended_result = mysqli_query($conn, $recommended_query);
?>

<div class="container dashboard">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo $user['full_name']; ?>!</h1>
        <a href="profile.php" class="btn">Edit Profile</a>
    </div>

    <div class="dashboard-search-bar" style="margin: 40px auto; max-width: 1200px;">
        <form action="search_jobs.php" method="GET" class="search-form" 
              style="display: flex; border: 3px solid #4f46e5; border-radius: 12px; overflow: hidden; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);">
            
            <input type="text" name="keyword" placeholder="Job title, company, or keyword..." class="form-control keyword-input" required 
                   style="border: none; padding: 20px 25px; font-size: 1.3rem; flex-grow: 1; border-radius: 0; outline: none; box-sizing: border-box;">
            
            <input type="text" name="location" placeholder="Location (e.g., London, Remote)" class="form-control location-input" 
                   style="border: none; padding: 20px 25px; font-size: 1.3rem; flex-grow: 0.5; border-radius: 0; outline: none; border-left: 1px solid #e2e8f0; box-sizing: border-box;">
            
            <button type="submit" class="btn btn-primary search-button" 
                    style="background-color: #6366f1; color: white; padding: 20px 40px; font-size: 1.3rem; border: none; cursor: pointer; flex-shrink: 0; border-radius: 0; transition: background-color 0.3s; box-sizing: border-box;">
                <i class="fas fa-search"></i> <span class="button-text">Search Jobs</span>
            </button>
        </form>
    </div>
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $applications['total']; ?></h3>
                <p>Applications</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-bookmark"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $saved_jobs['total']; ?></h3>
                <p>Saved Jobs</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-info">
                <h3>0</h3>
                <p>Interviews</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-info">
                <h3>0%</h3>
                <p>Profile Completion</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-content">
        <h2>Recommended Jobs For You</h2>
        
        <?php if (mysqli_num_rows($recommended_result) > 0): ?>
            <div class="jobs-container">
                <?php while ($job = mysqli_fetch_assoc($recommended_result)): ?>
                    <div class="job-card">
                        <div class="job-card-header">
                            <img src="<?php echo !empty($job['logo_path']) ? BASE_URL . $job['logo_path'] : BASE_URL . 'assets/images/company-placeholder.png'; ?>" alt="<?php echo $job['company_name']; ?>" class="company-logo">
                            <h3 class="job-title"><?php echo $job['job_title']; ?></h3>
                            <p class="company-name"><?php echo $job['company_name']; ?></p>
                            <div class="job-meta">
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo $job['location']; ?></span>
                                <span><i class="fas fa-briefcase"></i> <?php echo $job['job_type']; ?></span>
                            </div>
                        </div>
                        <div class="job-card-body">
                            <p class="job-description"><?php echo substr($job['description'], 0, 150) . '...'; ?></p>
                        </div>
                        <div class="job-card-footer">
                            <span class="job-salary"><?php echo $job['salary']; ?></span>
                            <a href="job_details.php?id=<?php echo $job['id']; ?>" class="btn apply-btn">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No recommended jobs available at the moment. <a href="search_jobs.php">Browse all jobs</a></p>
        <?php endif; ?>
    </div>
    
    <div class="dashboard-content" style="margin-top: 30px;">
        <h2>Recent Applications</h2>
        
        <?php
        $recent_applications_query = "SELECT a.*, j.job_title, j.salary, c.company_name 
                                     FROM applications a 
                                     JOIN jobs j ON a.job_id = j.id 
                                     JOIN companies c ON j.company_id = c.id 
                                     WHERE a.user_id = $user_id 
                                     ORDER BY a.applied_on DESC 
                                     LIMIT 5";
        $recent_applications_result = mysqli_query($conn, $recent_applications_query);
        
        if (mysqli_num_rows($recent_applications_result) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Applied On</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($app = mysqli_fetch_assoc($recent_applications_result)): ?>
                        <tr>
                            <td><?php echo $app['job_title']; ?></td>
                            <td><?php echo $app['company_name']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($app['applied_on'])); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($app['status']); ?>"><?php echo $app['status']; ?></span></td>
                            <td>
                                <a href="job_details.php?id=<?php echo $app['job_id']; ?>" class="btn btn-sm">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="text-center" style="margin-top: 20px;">
                <a href="applications.php" class="btn">View All Applications</a>
            </div>
        <?php else: ?>
            <p>You haven't applied to any jobs yet. <a href="search_jobs.php">Browse jobs</a></p>
        <?php endif; ?>
    </div>
</div>

<style>
/* Responsive adjustments: impossible with pure inline CSS */
@media (max-width: 768px) {
    .dashboard-search-bar .search-form {
        flex-direction: column; /* Stack elements vertically */
        border-radius: 8px !important;
    }
    
    .dashboard-search-bar .search-form input,
    .dashboard-search-bar .search-form button {
        width: 100%; /* Take full width when stacked */
        border-radius: 0 !important;
        text-align: center;
        padding: 15px 20px !important; 
        font-size: 1.1rem !important;
    }
    
    .dashboard-search-bar .location-input {
        border-top: 1px solid #e2e8f0 !important; /* Separator between inputs */
        border-left: none !important;
    }

    .dashboard-search-bar .search-button .button-text {
        display: none; /* Hide the "Search Jobs" text on small screens, show only the icon */
    }
}
/* Hover effect: also impossible with pure inline CSS */
.dashboard-search-bar .search-button:hover {
    background-color: #4f46e5 !important; /* Primary-dark color */
}
</style>

<?php include_once '../includes/footer.php'; ?>