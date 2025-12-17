<?php
// Define the path to the includes folder reliably.
$includes_path = __DIR__ . '/../includes/';

require_once $includes_path . 'config.php';
require_once $includes_path . 'user_auth.php';

$pageTitle = 'My Applications';
include_once $includes_path . 'header.php';

$user_id = $_SESSION['user_id'];

// 1. Prepared Statement: Use ? placeholders to prevent SQL injection
$query = "SELECT
             a.*,
             j.job_title,
             j.location,
             j.job_type,
             c.company_name,
             c.logo_path
           FROM applications a
           JOIN jobs j ON a.job_id = j.id
           JOIN companies c ON j.company_id = c.id
           WHERE a.user_id = ?
           ORDER BY a.applied_on DESC";

// 2. Prepare the statement
$stmt = mysqli_prepare($conn, $query);

// Check if statement preparation failed
if (!$stmt) {
    die("Database error: Unable to prepare statement." . mysqli_error($conn));
}

// 3. Bind the user_id parameter (i = integer)
mysqli_stmt_bind_param($stmt, "i", $user_id);

// 4. Execute the statement
mysqli_stmt_execute($stmt);

// 5. Get the result set
$result = mysqli_stmt_get_result($stmt);

// Check if the result retrieval failed
if (!$result) {
    die("Database error: Unable to get results." . mysqli_error($conn));
}
?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>My Applications</h1>
            <a href="dashboard.php" class="btn btn-secondary" 
               style="background-color: #6c757d; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; font-size: 1em;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <div class="dashboard-content">
            <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="applications-list">
                <?php while ($application = mysqli_fetch_assoc($result)): ?>
                <div class="application-card">
                    
                    <div class="application-header">
                        <div class="application-info">
                            <h3><?php echo htmlspecialchars($application['job_title']); ?></h3>
                            <div class="application-meta">
                                
                                <div class="company-name-and-logo">
                                    <img class="small-logo-img" 
                                            src="<?php echo !empty($application['logo_path']) ? BASE_URL . $application['logo_path'] : BASE_URL . 'assets/images/company-placeholder.png'; ?>" 
                                            alt="<?php echo htmlspecialchars($application['company_name']); ?>">
                                    <span class="company-name-text"><?php echo htmlspecialchars($application['company_name']); ?></span>
                                </div>
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($application['location']); ?></span>
                                <span><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($application['job_type']); ?></span>
                                <span><i class="fas fa-calendar"></i> Applied: <?php echo date('M d, Y', strtotime($application['applied_on'])); ?></span>
                            </div>
                        </div>
                        
                        </div>
                    
                    <div class="application-body">
                        <div class="application-status">
                            <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $application['status'])); ?>"><?php echo htmlspecialchars($application['status']); ?></span>
                        </div>
                        <a href="job_details.php?id=<?php echo htmlspecialchars($application['job_id']); ?>" class="btn btn-outline-primary apply-btn">View Details</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="no-results">
                <h3>No Applications Found!</h3>
                <p>It looks like you haven't applied for any jobs yet. Start exploring our job listings today!</p>
                <a href="<?php echo BASE_URL; ?>job_listings.php" class="btn btn-primary">Browse Jobs</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// 6. Close the statement and free the result
mysqli_stmt_close($stmt);
mysqli_free_result($result);

include_once $includes_path . 'footer.php';
?>