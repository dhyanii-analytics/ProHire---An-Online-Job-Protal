<?php
require_once 'includes/config.php';
 $pageTitle = 'Company Profile';

// Get company ID from URL
 $company_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($company_id > 0) {
    // Fetch company details from database
    $query = "SELECT * FROM companies WHERE id = $company_id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $company = mysqli_fetch_assoc($result);
        
        // Fetch company jobs
        $jobs_query = "SELECT * FROM jobs WHERE company_id = $company_id ORDER BY posted_date DESC LIMIT 5";
        $jobs_result = mysqli_query($conn, $jobs_query);
    } else {
        $error = "Company not found!";
    }
} else {
    $error = "Invalid company ID!";
}

include_once 'includes/header.php';
?>

<div class="container" style="padding: 50px 0;">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="company-details">
                    <div class="company-header">
                        <div class="company-logo">
                            <img src="assets/images/company/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['company_name']); ?>" class="img-fluid">
                        </div>
                        <div class="company-info">
                            <h2><?php echo htmlspecialchars($company['company_name']); ?></h2>
                            <div class="company-meta">
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($company['address']); ?></span>
                                <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($company['phone']); ?></span>
                                <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($company['email']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="company-description">
                        <h3>About Company</h3>
                        <p><?php echo nl2br(htmlspecialchars($company['description'])); ?></p>
                    </div>
                    
                    <div class="company-stats">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <h4><?php echo mysqli_num_rows($jobs_result); ?></h4>
                                    <p>Active Jobs</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <h4><?php echo htmlspecialchars($company['industry']); ?></h4>
                                    <p>Industry</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <h4><?php echo htmlspecialchars($company['size']); ?></h4>
                                    <p>Company Size</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="company-jobs">
                        <h3>Recent Jobs</h3>
                        <?php if (mysqli_num_rows($jobs_result) > 0): ?>
                            <div class="job-listings">
                                <?php while ($job = mysqli_fetch_assoc($jobs_result)): ?>
                                    <div class="job-card">
                                        <div class="job-header">
                                            <h3><a href="job-details.php?id=<?php echo $job['id']; ?>"><?php echo htmlspecialchars($job['title']); ?></a></h3>
                                            <span class="job-type"><?php echo htmlspecialchars($job['job_type']); ?></span>
                                        </div>
                                        <div class="job-meta">
                                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                            <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($job['posted_date'])); ?></span>
                                        </div>
                                        <p class="job-description"><?php echo substr(htmlspecialchars($job['description']), 0, 200) . '...'; ?></p>
                                        <div class="job-footer">
                                            <a href="job-details.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">View Details</a>
                                            <span class="salary"><?php echo htmlspecialchars($job['salary_range']); ?></span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="text-center">
                                <a href="search.php?company=<?php echo $company_id; ?>" class="btn btn-outline-primary">View All Jobs</a>
                            </div>
                        <?php else: ?>
                            <p>No active jobs at the moment.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="company-map">
                    <h3>Location</h3>
                    <!-- You can embed a map here using Google Maps or another service -->
                    <div class="map-placeholder">
                        <img src="assets/images/map-placeholder.jpg" alt="Map" class="img-fluid">
                    </div>
                    <p><?php echo htmlspecialchars($company['address']); ?></p>
                </div>
                
                <div class="company-social">
                    <h3>Connect With Us</h3>
                    <div class="social-links">
                        <?php if (!empty($company['website'])): ?>
                            <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank"><i class="fas fa-globe"></i> Website</a>
                        <?php endif; ?>
                        <?php if (!empty($company['linkedin'])): ?>
                            <a href="<?php echo htmlspecialchars($company['linkedin']); ?>" target="_blank"><i class="fab fa-linkedin"></i> LinkedIn</a>
                        <?php endif; ?>
                        <?php if (!empty($company['twitter'])): ?>
                            <a href="<?php echo htmlspecialchars($company['twitter']); ?>" target="_blank"><i class="fab fa-twitter"></i> Twitter</a>
                        <?php endif; ?>
                        <?php if (!empty($company['facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($company['facebook']); ?>" target="_blank"><i class="fab fa-facebook"></i> Facebook</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>