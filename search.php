<?php
require_once 'includes/config.php';
$pageTitle = 'Search Results';
include_once 'includes/header.php';

// Get search parameters
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';

// Build the query
$query = "SELECT j.*, c.company_name, c.logo_path 
          FROM jobs j 
          JOIN companies c ON j.company_id = c.id 
          WHERE 1=1";

if (!empty($keyword)) {
    $query .= " AND (j.job_title LIKE '%$keyword%' OR j.description LIKE '%$keyword%' OR c.company_name LIKE '%$keyword%')";
}

$query .= " ORDER BY j.posted_on DESC";

// Execute the query
$result = mysqli_query($conn, $query);

// Check for errors
if (!$result) {
    echo "<div class='alert alert-danger'>Database error: " . mysqli_error($conn) . "</div>";
} else {
    // Display search results
    if (mysqli_num_rows($result) > 0) {
        echo "<div class='section-title'>";
        echo "<h2>Search Results</h2>";
        echo "<p>Showing " . mysqli_num_rows($result) . " jobs matching \"" . htmlspecialchars($keyword) . "\"</p>";
        echo "</div>";
        
        echo "<div class='jobs-container'>";
        while ($job = mysqli_fetch_assoc($result)) {
            $job_id = $job['id'];
            $title = $job['job_title'];
            $company = $job['company_name'];
            $logo = !empty($job['logo_path']) ? BASE_URL . $job['logo_path'] : BASE_URL . 'assets/images/company-placeholder.png';
            $location = !empty($job['location']) ? $job['location'] : 'Location not specified';
            $type = !empty($job['job_type']) ? $job['job_type'] : 'Job type not specified';
            $salary = !empty($job['salary']) ? $job['salary'] : 'Salary not specified';
            $description = !empty($job['description']) ? substr($job['description'], 0, 150) . '...' : 'No description available';
            
            echo '<div class="job-card">';
            echo '<div class="job-card-header">';
            echo '<img src="' . $logo . '" alt="' . $company . '" class="company-logo">';
            echo '<h3 class="job-title">' . htmlspecialchars($title) . '</h3>';
            echo '<p class="company-name">' . htmlspecialchars($company) . '</p>';
            echo '<div class="job-meta">';
            echo '<span><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($location) . '</span>';
            echo '<span><i class="fas fa-briefcase"></i> ' . htmlspecialchars($type) . '</span>';
            echo '</div>';
            echo '</div>';
            echo '<div class="job-card-body">';
            echo '<p class="job-description">' . htmlspecialchars($description) . '</p>';
            echo '</div>';
            echo '<div class="job-card-footer">';
            echo '<span class="job-salary">' . htmlspecialchars($salary) . '</span>';
            // CORRECTED LINK: Changed to Job_details.php to match the case/underscore of your actual file name
            echo '<a href="' . BASE_URL . 'Job_details.php?id=' . $job_id . '" class="btn apply-btn">View Details</a>';
            echo '</div>';
            echo '</div>';
        }
        echo "</div>";
    } else {
        echo '<div class="no-results">';
        echo '<h3>No jobs found</h3>';
        echo '<p>We couldn\'t find any jobs matching your search criteria: <strong>"' . htmlspecialchars($keyword) . '"</strong></p>';
        echo '<p>Please try different keywords or check back later for new opportunities.</p>';
        // FIXED LINK: Using BASE_URL for absolute path to homepage
        echo '<a href="' . BASE_URL . 'index.php" class="btn btn-primary">Back to Home</a>';
        echo '</div>';
    }
}
?>

<div class="text-center" style="margin-top: 30px;">
    <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-primary">Back to Home</a>
</div>

<?php include_once 'includes/footer.php'; ?>