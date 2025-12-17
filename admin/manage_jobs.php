<?php
require_once '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $job_id = (int)$_GET['id'];
    
    if ($action == 'delete') {
        // First delete all applications for this job
        $delete_applications_query = "DELETE FROM applications WHERE job_id = $job_id";
        mysqli_query($conn, $delete_applications_query);
        
        // Then delete the job
        $delete_query = "DELETE FROM jobs WHERE id = $job_id";
        
        if (mysqli_query($conn, $delete_query)) {
            $success = "Job deleted successfully!";
        } else {
            $error = "Failed to delete job. Please try again.";
        }
    }
}

$pageTitle = 'Manage Jobs';
include_once '../includes/header.php';

// Get all jobs
$query = "SELECT j.*, c.company_name 
          FROM jobs j 
          JOIN companies c ON j.company_id = c.id 
          ORDER BY j.posted_on DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container" style="padding: 50px 0;">
    <div class="dashboard-header">
        <h1>Manage Jobs</h1>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Posted On</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($job = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $job['id']; ?></td>
                        <td><?php echo $job['job_title']; ?></td>
                        <td><?php echo $job['company_name']; ?></td>
                        <td><?php echo $job['location']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($job['posted_on'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($job['deadline'])); ?></td>
                        <td>
                            <a href="view_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm">View</a>
                            <a href="manage_jobs.php?action=delete&id=<?php echo $job['id']; ?>" class="btn btn-sm" onclick="return confirm('Are you sure you want to delete this job?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No jobs found.</p>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>