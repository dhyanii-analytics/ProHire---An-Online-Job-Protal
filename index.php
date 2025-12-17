<?php
require_once 'includes/config.php';
$pageTitle = 'Home';
include_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Find Your Dream Job</h1>
        <p>ProHire connects talented professionals with top companies. Discover opportunities that match your skills and aspirations.</p>
    
        <form action="search.php" method="GET" class="search-bar">
    <input type="text" name="keyword" placeholder="Job title, keywords, or company..." value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
    <input type="text" name="location" placeholder="City or state" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
</form>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose ProHire?</h2>
            <p>We provide the best platform for job seekers and employers to connect and grow together.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-search"></i>
                <h3>Smart Job Search</h3>
                <p>Our advanced search algorithms help you find the perfect job match based on your skills and preferences.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-building"></i>
                <h3>Top Companies</h3>
                <p>Connect with leading companies and startups looking for talented professionals like you.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-file-alt"></i>
                <h3>Easy Applications</h3>
                <p>Apply to multiple jobs with just a few clicks. Track your applications and get updates in real-time.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-user-tie"></i>
                <h3>Professional Profiles</h3>
                <p>Create a standout profile that showcases your skills, experience, and achievements to potential employers.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-bell"></i>
                <h3>Job Alerts</h3>
                <p>Get notified about new job opportunities that match your preferences as soon as they're posted.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure & Private</h3>
                <p>Your data is secure with us. Control who sees your profile and manage your privacy settings.</p>
            </div>
        </div>
    </div>
</section>

<!-- Job Listings Section -->
<section class="job-listings">
    <div class="container">
        <div class="section-title">
            <h2>Featured Jobs</h2>
            <p>Check out the latest job opportunities from top companies.</p>
        </div>
        
        <div class="jobs-container">
            <?php
            // Fetch featured jobs from database
            $query = "SELECT j.*, c.company_name, c.logo_path 
                      FROM jobs j 
                      JOIN companies c ON j.company_id = c.id 
                      ORDER BY j.posted_on DESC 
                      LIMIT 6";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0) {
                while ($job = mysqli_fetch_assoc($result)) {
                    $job_id = $job['id'];
                    $title = $job['job_title'];
                    $company = $job['company_name'];
                    $logo = !empty($job['logo_path']) ? BASE_URL . $job['logo_path'] : BASE_URL . 'assets/images/company-placeholder.png';
                    $location = $job['location'];
                    $type = $job['job_type'];
                    $salary = $job['salary'];
                    $description = substr($job['description'], 0, 150) . '...';
                    
                    echo '<div class="job-card">';
                    echo '<div class="job-card-header">';
                    echo '<img src="' . $logo . '" alt="' . $company . '" class="company-logo">';
                    echo '<h3 class="job-title">' . $title . '</h3>';
                    echo '<p class="company-name">' . $company . '</p>';
                    echo '<div class="job-meta">';
                    echo '<span><i class="fas fa-map-marker-alt"></i> ' . $location . '</span>';
                    echo '<span><i class="fas fa-briefcase"></i> ' . $type . '</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="job-card-body">';
                    echo '<p class="job-description">' . $description . '</p>';
                    echo '</div>';
                    echo '<div class="job-card-footer">';
                    echo '<span class="job-salary">' . $salary . '</span>';
                    echo '<a href="user/job_details.php?id=' . $job_id . '" class="btn apply-btn">View Details</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No jobs available at the moment.</p>';
            }
            ?>
        </div>
        
        <div class="text-center" style="margin-top: 30px;">
            <a href="user/search_jobs.php" class="btn btn-primary">View All Jobs</a>
        </div>
    </div>
</section>

<section class="testimonials">
    <div class="container">
        <div class="section-title">
            <h2>Success Stories</h2>
            <p>Hear from professionals who found their dream jobs through ProHire.</p>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <p class="testimonial-content">"ProHire helped me land my dream job at a leading tech company. The platform is easy to use and the job recommendations were spot on!"</p>
                <div class="testimonial-author">
                    <img src="assets/images/testimonials/shiha_shah.jpg" alt="Shiha Shah" class="author-avatar">
                    <div class="author-info">
                        <h4>Shiha Shah</h4>
                        <p>Senior UX Designer</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <p class="testimonial-content">"As a recruiter, ProHire has made my job so much easier. The quality of candidates and the filtering options are exceptional."</p>
                <div class="testimonial-author">
                    <img src="assets/images/testimonials/mihir_dev.jpg" alt="Mihir Dev" class="author-avatar">
                    <div class="author-info">
                        <h4>Mihir Dev</h4>
                        <p>HR Manager at TechCorp</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <p class="testimonial-content">"I was looking for a career change and ProHire connected me with opportunities I wouldn't have found elsewhere. Highly recommended!"</p>
                <div class="testimonial-author">
                    <img src="assets/images/testimonials/niya_patel.jpg" alt="Niya Patel" class="author-avatar">
                    <div class="author-info">
                        <h4>Niya Patel</h4>
                        <p>Marketing Director</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>