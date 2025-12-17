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
    $company_id = (int)$_GET['id'];
    
    if ($action == 'delete') {
        // First delete all jobs posted by this company
        $delete_jobs_query = "DELETE FROM jobs WHERE company_id = $company_id";
        mysqli_query($conn, $delete_jobs_query);
        
        // Then delete the company
        $delete_query = "DELETE FROM companies WHERE id = $company_id";
        
        if (mysqli_query($conn, $delete_query)) {
            $success = "Company deleted successfully!";
        } else {
            $error = "Failed to delete company. Please try again.";
        }
    }
}

$pageTitle = 'Manage Companies';
include_once '../includes/header.php';

// Get all companies
$query = "SELECT * FROM companies ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container" style="padding: 50px 0;">
    <div class="dashboard-header">
        <h1>Manage Companies</h1>
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
                    <th>Company Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Industry</th>
                    <th>Registered On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($company = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $company['id']; ?></td>
                        <td><?php echo $company['company_name']; ?></td>
                        <td><?php echo $company['email']; ?></td>
                        <td><?php echo $company['phone']; ?></td>
                        <td><?php echo $company['industry']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($company['created_at'])); ?></td>
                        <td>
                            <a href="view_company.php?id=<?php echo $company['id']; ?>" class="btn btn-sm">View</a>
                            <a href="manage_companies.php?action=delete&id=<?php echo $company['id']; ?>" class="btn btn-sm" onclick="return confirm('Are you sure you want to delete this company?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No companies found.</p>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>