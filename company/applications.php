<?php
require_once '../includes/config.php';

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header('Location: ../login.php');
    exit();
}

$company_id = $_SESSION['company_id'];
$pageTitle = 'Job Applications';
include_once '../includes/header.php';

// Get job ID if specified
$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;

// Build query
$query = "SELECT a.*, j.job_title, u.full_name, u.email, u.phone, u.skills, u.experience, u.education, u.resume_path 
          FROM applications a 
          JOIN jobs j ON a.job_id = j.id 
          JOIN users u ON a.user_id = u.id 
          WHERE j.company_id = $company_id";

if ($job_id > 0) {
    $query .= " AND a.job_id = $job_id";
}

$query .= " ORDER BY a.applied_on DESC";

$result = mysqli_query($conn, $query);
?>

<div class="container" style="padding: 50px 0;">
    <div class="dashboard-header">
        <h1>Job Applications</h1>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
    
    <?php if (mysqli_num_rows($result) > 0): ?>
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
                <?php while ($app = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>
                            <strong><?php echo $app['full_name']; ?></strong><br>
                            <small><?php echo $app['email']; ?></small><br>
                            <small><?php echo $app['phone']; ?></small>
                        </td>
                        <td><?php echo $app['job_title']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($app['applied_on'])); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($app['status']); ?>"><?php echo $app['status']; ?></span>
                        </td>
                        <td>
                            <a href="view_application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm">View Details</a>
                            <a href="update_application_status.php?id=<?php echo $app['id']; ?>&status=Shortlisted" class="btn btn-sm">Shortlist</a>
                            <a href="update_application_status.php?id=<?php echo $app['id']; ?>&status=Rejected" class="btn btn-sm">Reject</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No applications found.</p>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>