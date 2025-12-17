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
    $user_id = (int)$_GET['id'];
    
    if ($action == 'delete') {
        $delete_query = "DELETE FROM users WHERE id = $user_id";
        
        if (mysqli_query($conn, $delete_query)) {
            $success = "User deleted successfully!";
        } else {
            $error = "Failed to delete user. Please try again.";
        }
    }
}

$pageTitle = 'Manage Users';
include_once '../includes/header.php';

// Get all users
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container" style="padding: 50px 0;">
    <div class="dashboard-header">
        <h1>Manage Users</h1>
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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Registered On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['full_name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['phone']; ?></td>
                        <td><?php echo $user['location']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <a href="view_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm">View</a>
                            <a href="manage_users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>