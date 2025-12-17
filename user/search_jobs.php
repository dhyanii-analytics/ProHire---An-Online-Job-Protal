<?php
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Search Jobs';
include_once '../includes/header.php'; // Assuming header includes <body> and opens main content wrapper

// Get search parameters
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$location = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';
$job_type = isset($_GET['job_type']) ? mysqli_real_escape_string($conn, $_GET['job_type']) : '';
$experience = isset($_GET['experience']) ? mysqli_real_escape_string($conn, $_GET['experience']) : '';

// Build query
$query = "SELECT j.*, c.company_name, c.logo_path 
          FROM jobs j 
          JOIN companies c ON j.company_id = c.id 
          WHERE 1=1";

if (!empty($keyword)) {
    $query .= " AND (j.job_title LIKE '%$keyword%' OR j.description LIKE '%$keyword%' OR c.company_name LIKE '%$keyword%')";
}

if (!empty($location)) {
    $query .= " AND j.location LIKE '%$location%'";
}

if (!empty($job_type)) {
    $query .= " AND j.job_type = '$job_type'";
}

if (!empty($experience)) {
    $query .= " AND j.experience_required = '$experience'";
}

$query .= " ORDER BY j.posted_on DESC";

$result = mysqli_query($conn, $query);
?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif; display: flex; flex-wrap: wrap; gap: 25px;">
    
    <div style="width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: #333; margin: 0;">Search Jobs</h1>
        
        <a href="dashboard.php" class="btn" 
           style="background-color: #6c757d; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; font-size: 1em; display: flex; align-items: center; gap: 5px;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="job-filters" style="flex: 0 0 250px; background-color: #f8f9fa; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); height: fit-content;">
        <h3 style="color: #007bff; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Filter Jobs</h3>
        
        <form method="GET" action="" style="display: flex; flex-direction: column; gap: 15px;">
            
            <div class="filter-group" style="display: flex; flex-direction: column;">
                <label for="keyword" style="margin-bottom: 5px; font-weight: bold; color: #555;">Keyword</label>
                <input type="text" name="keyword" id="keyword" value="<?php echo htmlspecialchars($keyword); ?>" 
                        style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box;">
            </div>
            
            <div class="filter-group" style="display: flex; flex-direction: column;">
                <label for="location" style="margin-bottom: 5px; font-weight: bold; color: #555;">Location</label>
                <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($location); ?>"
                        style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box;">
            </div>
            
            <div class="filter-group" style="display: flex; flex-direction: column;">
                <label for="job_type" style="margin-bottom: 5px; font-weight: bold; color: #555;">Job Type</label>
                <select name="job_type" id="job_type" 
                        style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box; background-color: white;">
                    <option value="">All Types</option>
                    <option value="Full-time" <?php echo ($job_type == 'Full-time') ? 'selected' : ''; ?>>Full-time</option>
                    <option value="Part-time" <?php echo ($job_type == 'Part-time') ? 'selected' : ''; ?>>Part-time</option>
                    <option value="Contract" <?php echo ($job_type == 'Contract') ? 'selected' : ''; ?>>Contract</option>
                    <option value="Internship" <?php echo ($job_type == 'Internship') ? 'selected' : ''; ?>>Internship</option>
                </select>
            </div>
            
            <div class="filter-group" style="display: flex; flex-direction: column;">
                <label for="experience" style="margin-bottom: 5px; font-weight: bold; color: #555;">Experience</label>
                <select name="experience" id="experience"
                        style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box; background-color: white;">
                    <option value="">All Levels</option>
                    <option value="Fresher" <?php echo ($experience == 'Fresher') ? 'selected' : ''; ?>>Fresher</option>
                    <option value="1-2 years" <?php echo ($experience == '1-2 years') ? 'selected' : ''; ?>>1-2 years</option>
                    <option value="3-5 years" <?php echo ($experience == '3-5 years') ? 'selected' : ''; ?>>3-5 years</option>
                    <option value="5+ years" <?php echo ($experience == '5+ years') ? 'selected' : ''; ?>>5+ years</option>
                </select>
            </div>
            
            <div class="filter-group" style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="submit" class="btn" 
                        style="background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; flex-grow: 1;">
                    Apply Filters
                </button>
                <a href="search_jobs.php" class="btn" 
                   style="background-color: #6c757d; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; text-decoration: none; text-align: center; display: block;">
                    Reset
                </a>
            </div>
        </form>
    </div>
    
    <div class="jobs-container" style="flex: 1; display: flex; flex-direction: column; gap: 20px; min-width: 0;">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($job = mysqli_fetch_assoc($result)): ?>
                
                <div class="job-card" 
                     style="background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); border-left: 5px solid #007bff; transition: transform 0.2s;">
                    
                    <div class="job-card-header" style="display: flex; align-items: center; gap: 15px; border-bottom: 1px dashed #eee; padding-bottom: 15px; margin-bottom: 15px;">
                        <img src="<?php echo !empty($job['logo_path']) ? BASE_URL . $job['logo_path'] : BASE_URL . 'assets/images/company-placeholder.png'; ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>" class="company-logo"
                             style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
                        
                        <div style="flex-grow: 1;">
                            <h3 class="job-title" style="margin: 0; font-size: 1.3em; color: #333;"><?php echo htmlspecialchars($job['job_title']); ?></h3>
                            <p class="company-name" style="margin: 3px 0 0 0; color: #6c757d; font-size: 0.95em;"><?php echo htmlspecialchars($job['company_name']); ?></p>
                        </div>

                        <div class="job-meta" style="display: flex; gap: 20px; font-size: 0.9em; color: #6c757d;">
                             <span style="display: flex; align-items: center; gap: 5px;">
                                 <i class="fas fa-map-marker-alt" style="color: #007bff;"></i> <?php echo htmlspecialchars($job['location']); ?>
                             </span>
                             <span style="display: flex; align-items: center; gap: 5px;">
                                 <i class="fas fa-briefcase" style="color: #007bff;"></i> <?php echo htmlspecialchars($job['job_type']); ?>
                             </span>
                             <span style="display: flex; align-items: center; gap: 5px;">
                                 <i class="fas fa-clock" style="color: #007bff;"></i> <?php echo htmlspecialchars($job['experience_required']); ?>
                             </span>
                         </div>
                    </div>
                    
                    <div class="job-card-body">
                        <p class="job-description" style="color: #495057; font-size: 1em; line-height: 1.6; margin-bottom: 15px;">
                            <?php echo htmlspecialchars(substr($job['description'], 0, 150)) . '...'; ?>
                        </p>
                    </div>
                    
                    <div class="job-card-footer" style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px;">
                        <span class="job-salary" style="font-size: 1.1em; font-weight: bold; color: #28a745;"><?php echo htmlspecialchars($job['salary']); ?></span>
                        <a href="job_details.php?id=<?php echo $job['id']; ?>" class="btn apply-btn" 
                           style="background-color: #007bff; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: background-color 0.3s;">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #dc3545; font-size: 1.1em; padding: 20px; background-color: #fff3f3; border-radius: 8px;">
                No jobs found matching your criteria. Try adjusting your filters.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>