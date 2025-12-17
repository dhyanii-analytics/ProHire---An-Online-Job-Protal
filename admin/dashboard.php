<?php
// 1. Define base path for reliable includes (using the absolute path technique)

$includes_path = __DIR__ . '/../includes/';

// 2. Load config and the new admin authentication file
require_once $includes_path . 'config.php';
require_once $includes_path . 'admin_auth.php'; // Includes the logic that checks $_SESSION['admin_id'] and redirects



$pageTitle = 'Admin Dashboard';
include_once $includes_path . 'header.php'; // Updated path

// Get statistics (Keep this clean, simple queries are fine for counts)
$users_query = "SELECT COUNT(*) as total FROM users";
$users_result = mysqli_query($conn, $users_query);
$users = mysqli_fetch_assoc($users_result);

$companies_query = "SELECT COUNT(*) as total FROM companies";
$companies_result = mysqli_query($conn, $companies_query);
$companies = mysqli_fetch_assoc($companies_result);

$jobs_query = "SELECT COUNT(*) as total FROM jobs";
$jobs_result = mysqli_query($conn, $jobs_query);
$jobs = mysqli_fetch_assoc($jobs_result);

$applications_query = "SELECT COUNT(*) as total FROM applications";
$applications_result = mysqli_query($conn, $applications_query);
$applications = mysqli_fetch_assoc($applications_result);

// Get recent users
$recent_users_query = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users_result = mysqli_query($conn, $recent_users_query);

// Get recent companies
$recent_companies_query = "SELECT * FROM companies ORDER BY created_at DESC LIMIT 5";
$recent_companies_result = mysqli_query($conn, $recent_companies_query);

// ADDED: Get recent jobs
$recent_jobs_query = "SELECT j.id, j.job_title, c.company_name, j.location, j.posted_on
                      FROM jobs j
                      JOIN companies c ON j.company_id = c.id
                      ORDER BY j.posted_on DESC LIMIT 5";
$recent_jobs_result = mysqli_query($conn, $recent_jobs_query);
?>

<div class="container dashboard">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <a href="manage_jobs.php" class="btn btn-primary">Manage Jobs</a> 
    </div>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo htmlspecialchars($users['total']); ?></h3>
                <p>Users</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo htmlspecialchars($companies['total']); ?></h3>
                <p>Companies</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo htmlspecialchars($jobs['total']); ?></h3>
                <p>Jobs</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo htmlspecialchars($applications['total']); ?></h3>
                <p>Applications</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-content">
        <h2>Recent Users</h2>
        
        <?php if (mysqli_num_rows($recent_users_result) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($recent_users_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['location']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="view_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm">View</a>
                                <a href="manage_users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="text-center" style="margin-top: 20px;">
                <a href="manage_users.php" class="btn">View All Users</a>
            </div>
        <?php else: ?>
            <p>No users registered yet.</p>
        <?php endif; ?>
    </div>
    
    <div class="dashboard-content" style="margin-top: 30px;">
        <h2>Recent Companies</h2>
        
        <?php if (mysqli_num_rows($recent_companies_result) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Industry</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($company = mysqli_fetch_assoc($recent_companies_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($company['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($company['email']); ?></td>
                            <td><?php echo htmlspecialchars($company['industry']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($company['created_at'])); ?></td>
                            <td>
                                <a href="view_company.php?id=<?php echo $company['id']; ?>" class="btn btn-sm">View</a>
                                <a href="manage_companies.php?action=delete&id=<?php echo $company['id']; ?>" class="btn btn-sm" onclick="return confirm('Are you sure you want to delete this company?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="text-center" style="margin-top: 20px;">
                <a href="manage_companies.php" class="btn">View All Companies</a>
            </div>
        <?php else: ?>
            <p>No companies registered yet.</p>
        <?php endif; ?>
    </div>
    
    <div class="dashboard-content" style="margin-top: 30px;">
        <h2>Recent Jobs</h2>
        
        <?php if ($recent_jobs_result && mysqli_num_rows($recent_jobs_result) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Location</th>
                        <th>Posted On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($job = mysqli_fetch_assoc($recent_jobs_result)): ?>
                        <tr>
                            <td><?php echo $job['id']; ?></td>
                            <td><?php echo htmlspecialchars($job['job_title']); ?></td>
                            <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($job['location']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($job['posted_on'])); ?></td>
                            <td>
                                <a href="view_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm">View</a>
                                <a href="manage_jobs.php?action=delete&id=<?php echo $job['id']; ?>" class="btn btn-sm" onclick="return confirm('Are you sure you want to delete this job?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="text-center" style="margin-top: 20px;">
                <a href="manage_jobs.php" class="btn">View All Jobs</a>
            </div>
        <?php else: ?>
            <p>No jobs posted yet.</p>
        <?php endif; ?>
    </div>
    </div>

<?php include_once $includes_path . 'footer.php'; ?>