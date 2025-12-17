<?php
require_once '../includes/config.php';

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header('Location: ../login.php?role=company');
    exit();
}

$company_id = $_SESSION['company_id'];

// Get job ID from URL
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($job_id <= 0) {
    header('Location: dashboard.php');
    exit();
}

// Get job details
$query = "SELECT j.*, c.company_name, c.description as company_description, c.logo_path 
          FROM jobs j 
          JOIN companies c ON j.company_id = c.id 
          WHERE j.id = $job_id AND j.company_id = $company_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    // Job not found or doesn't belong to this company
    header('Location: dashboard.php');
    exit();
}

$job = mysqli_fetch_assoc($result);

// Get applications for this job
$applications_query = "SELECT a.*, u.full_name, u.email, u.phone 
                      FROM applications a 
                      JOIN users u ON a.user_id = u.id 
                      WHERE a.job_id = $job_id 
                      ORDER BY a.applied_on DESC";
$applications_result = mysqli_query($conn, $applications_query);
$applications_count = mysqli_num_rows($applications_result);

$pageTitle = 'View Job - ' . $job['job_title'];
include_once '../includes/header.php';
?>

<div class="container" style="padding: 50px 0;">
    <div class="job-details">
        <div class="job-header">
            <img src="<?php echo !empty($job['logo_path']) ? BASE_URL . $job['logo_path'] : BASE_URL . 'assets/images/company-placeholder.png'; ?>" alt="<?php echo $job['company_name']; ?>" class="company-logo">
            <div class="job-info">
                <h1><?php echo $job['job_title']; ?></h1>
                <h2><?php echo $job['company_name']; ?></h2>
                <div class="job-meta">
                    <span><i class="fas fa-map-marker-alt"></i> <?php echo $job['location']; ?></span>
                    <span><i class="fas fa-briefcase"></i> <?php echo $job['job_type']; ?></span>
                    <span><i class="fas fa-clock"></i> <?php echo $job['experience_required']; ?></span>
                    <span><i class="fas fa-calendar"></i> Posted on <?php echo date('M d, Y', strtotime($job['posted_on'])); ?></span>
                    <span><i class="fas fa-hourglass-end"></i> Deadline: <?php echo date('M d, Y', strtotime($job['deadline'])); ?></span>
                </div>
            </div>
            <div class="job-salary">
                <h3><?php echo $job['salary']; ?></h3>
                <p><?php echo $applications_count; ?> Applications</p>
            </div>
        </div>
        
        <div class="job-content">
            <div class="job-description">
                <h3>Job Description</h3>
                <p><?php echo nl2br($job['description']); ?></p>
            </div>
            
            <div class="job-requirements">
                <h3>Requirements</h3>
                <p><?php echo nl2br($job['requirements']); ?></p>
            </div>
            
            <div class="company-info">
                <h3>About Company</h3>
                <p><?php echo nl2br($job['company_description']); ?></p>
            </div>
        </div>
        
        <div class="job-actions">
            <a href="edit_job.php?id=<?php echo $job_id; ?>" class="btn btn-primary">Edit Job</a>
            <a href="delete_job.php?id=<?php echo $job_id; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this job?')">Delete Job</a>
            <a href="manage_jobs.php" class="btn">Back to Jobs</a>
        </div>
        
        <?php if ($applications_count > 0): ?>
            <div class="applications-section">
                <h2>Applications (<?php echo $applications_count; ?>)</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Applied On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($app = mysqli_fetch_assoc($applications_result)): ?>
                            <tr>
                                <td><?php echo $app['full_name']; ?></td>
                                <td><?php echo $app['email']; ?></td>
                                <td><?php echo $app['phone']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($app['applied_on'])); ?></td>
                                <td><span class="status-badge status-<?php echo strtolower($app['status']); ?>"><?php echo $app['status']; ?></span></td>
                                <td>
                                    <a href="view_application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm">View Details</a>
                                    <a href="update_application_status.php?id=<?php echo $app['id']; ?>&status=Shortlisted" class="btn btn-sm">Shortlist</a>
                                    <a href="update_application_status.php?id=<?php echo $app['id']; ?>&status=Rejected" class="btn btn-sm">Reject</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-applications">
                <p>No applications received yet for this job.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.job-details {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.job-header {
    display: flex;
    padding: 30px;
    border-bottom: 1px solid #e2e8f0;
    align-items: center;
}

.company-logo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 20px;
}

.job-info {
    flex: 1;
}

.job-info h1 {
    font-size: 1.8rem;
    margin-bottom: 5px;
    color: #334155;
}

.job-info h2 {
    font-size: 1.2rem;
    color: #64748b;
    margin-bottom: 10px;
}

.job-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.job-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #64748b;
}

.job-salary {
    text-align: right;
}

.job-salary h3 {
    font-size: 1.5rem;
    color: #10b981;
    margin-bottom: 5px;
}

.job-salary p {
    color: #64748b;
}

.job-content {
    padding: 30px;
}

.job-description, .job-requirements, .company-info {
    margin-bottom: 30px;
}

.job-description h3, .job-requirements h3, .company-info h3 {
    font-size: 1.3rem;
    margin-bottom: 15px;
    color: #4f46e5;
}

.job-actions {
    padding: 20px 30px;
    background: #f8fafc;
    display: flex;
    gap: 15px;
}

.applications-section {
    padding: 30px;
}

.applications-section h2 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: #4f46e5;
}

.no-applications {
    padding: 30px;
    text-align: center;
    color: #64748b;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-applied {
    background: #dbeafe;
    color: #1e40af;
}

.status-shortlisted {
    background: #d1fae5;
    color: #065f46;
}

.status-rejected {
    background: #fee2e2;
    color: #b91c1c;
}

@media (max-width: 768px) {
    .job-header {
        flex-direction: column;
        text-align: center;
    }
    
    .company-logo {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .job-salary {
        text-align: center;
        margin-top: 15px;
    }
    
    .job-meta {
        justify-content: center;
    }
    
    .job-actions {
        flex-direction: column;
    }
}
</style>

<?php include_once '../includes/footer.php'; ?>