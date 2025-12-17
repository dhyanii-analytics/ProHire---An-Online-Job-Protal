<?php
require_once '../includes/config.php';

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header('Location: ../login.php');
    exit();
}

// Display success or error messages
$success_message = '';
$error_message = '';

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$company_id = $_SESSION['company_id'];
$pageTitle = 'Manage Jobs';
include_once '../includes/header.php';

// Get company jobs
$query = "SELECT j.*, 
          (SELECT COUNT(*) FROM applications WHERE job_id = j.id) as application_count 
          FROM jobs j 
          WHERE j.company_id = $company_id 
          ORDER BY j.posted_on DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container" style="padding: 50px 0;">
    <div class="dashboard-header">
        <h1>Manage Jobs</h1>
        <a href="post_job.php" class="btn btn-primary">Post a New Job</a>
    </div>
    
    <!-- Add this section to display messages -->
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Location</th>
                    <th>Job Type</th>
                    <th>Posted On</th>
                    <th>Deadline</th>
                    <th>Applications</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($job = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $job['job_title']; ?></td>
                        <td><?php echo $job['location']; ?></td>
                        <td><?php echo $job['job_type']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($job['posted_on'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($job['deadline'])); ?></td>
                        <td><?php echo $job['application_count']; ?></td>
                        <td>
                            <?php 
                            $today = date('Y-m-d');
                            if ($job['deadline'] < $today) {
                                echo '<span class="status-badge status-expired">Expired</span>';
                            } else {
                                echo '<span class="status-badge status-active">Active</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="view_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm">View</a>
                            <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm">Edit</a>
                            <a href="applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-sm">Applications</a>
                            <a href="delete_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm" onclick="return confirm('Are you sure you want to delete this job?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You haven't posted any jobs yet. <a href="post_job.php">Post your first job</a></p>
    <?php endif; ?>
</div>

<style>
.table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.table th, .table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.table th {
    background: #f8fafc;
    font-weight: 600;
    color: #4f46e5;
}

.table tr:hover {
    background: #f8fafc;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-expired {
    background: #fee2e2;
    color: #b91c1c;
}

@media (max-width: 768px) {
    .table {
        display: block;
        overflow-x: auto;
    }
}
</style>

<?php include_once '../includes/footer.php'; ?>