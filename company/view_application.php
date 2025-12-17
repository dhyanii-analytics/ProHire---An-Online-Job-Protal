<?php
require_once '../includes/config.php';

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header('Location: ../login.php?role=company');
    exit();
}

$company_id = $_SESSION['company_id'];

// Get application ID from URL
$application_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($application_id <= 0) {
    header('Location: applications.php');
    exit();
}

// Get application details
$query = "SELECT a.*, j.job_title, j.salary, j.location, j.job_type, 
                 u.full_name, u.email, u.phone, u.location as user_location, 
                 u.skills, u.experience, u.education, u.resume_path
          FROM applications a 
          JOIN jobs j ON a.job_id = j.id 
          JOIN users u ON a.user_id = u.id 
          JOIN companies c ON j.company_id = c.id 
          WHERE a.id = $application_id AND c.id = $company_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    // Application not found or doesn't belong to this company
    header('Location: applications.php');
    exit();
}

$application = mysqli_fetch_assoc($result);

$pageTitle = 'Application Details - ' . $application['full_name'];
include_once '../includes/header.php';
?>

<div class="container" style="padding: 50px 0;">
    <div class="application-details">
        <div class="application-header">
            <h1>Application Details</h1>
            <div class="application-meta">
                <p><strong>Job:</strong> <?php echo $application['job_title']; ?></p>
                <p><strong>Applied On:</strong> <?php echo date('M d, Y', strtotime($application['applied_on'])); ?></p>
                <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($application['status']); ?>"><?php echo $application['status']; ?></span></p>
            </div>
        </div>
        
        <div class="application-content">
            <div class="candidate-info">
                <h2>Candidate Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <h3>Full Name</h3>
                        <p><?php echo $application['full_name']; ?></p>
                    </div>
                    <div class="info-item">
                        <h3>Email</h3>
                        <p><?php echo $application['email']; ?></p>
                    </div>
                    <div class="info-item">
                        <h3>Phone</h3>
                        <p><?php echo $application['phone']; ?></p>
                    </div>
                    <div class="info-item">
                        <h3>Location</h3>
                        <p><?php echo $application['user_location']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="job-info">
                <h2>Job Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <h3>Job Title</h3>
                        <p><?php echo $application['job_title']; ?></p>
                    </div>
                    <div class="info-item">
                        <h3>Salary</h3>
                        <p><?php echo $application['salary']; ?></p>
                    </div>
                    <div class="info-item">
                        <h3>Location</h3>
                        <p><?php echo $application['location']; ?></p>
                    </div>
                    <div class="info-item">
                        <h3>Job Type</h3>
                        <p><?php echo $application['job_type']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="candidate-profile">
                <h2>Candidate Profile</h2>
                
                <?php if (!empty($application['skills'])): ?>
                    <div class="profile-section">
                        <h3>Skills</h3>
                        <p><?php echo nl2br($application['skills']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($application['experience'])): ?>
                    <div class="profile-section">
                        <h3>Work Experience</h3>
                        <p><?php echo nl2br($application['experience']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($application['education'])): ?>
                    <div class="profile-section">
                        <h3>Education</h3>
                        <p><?php echo nl2br($application['education']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($application['resume_path'])): ?>
                    <div class="profile-section">
                        <h3>Resume</h3>
                        <a href="<?php echo BASE_URL . $application['resume_path']; ?>" target="_blank" class="btn">View Resume</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="application-actions">
            <a href="update_application_status.php?id=<?php echo $application_id; ?>&status=Shortlisted" class="btn btn-primary">Shortlist</a>
            <a href="update_application_status.php?id=<?php echo $application_id; ?>&status=Rejected" class="btn">Reject</a>
            <a href="applications.php" class="btn">Back to Applications</a>
        </div>
    </div>
</div>

<style>
.application-details {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.application-header {
    padding: 30px;
    border-bottom: 1px solid #e2e8f0;
}

.application-header h1 {
    font-size: 1.8rem;
    margin-bottom: 20px;
    color: #4f46e5;
}

.application-meta p {
    margin-bottom: 5px;
}

.application-content {
    padding: 30px;
}

.candidate-info, .job-info {
    margin-bottom: 30px;
}

.candidate-info h2, .job-info h2 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: #4f46e5;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.info-item h3 {
    font-size: 1rem;
    margin-bottom: 5px;
    color: #64748b;
}

.candidate-profile h2 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: #4f46e5;
}

.profile-section {
    margin-bottom: 25px;
}

.profile-section h3 {
    font-size: 1.2rem;
    margin-bottom: 10px;
    color: #334155;
}

.application-actions {
    padding: 20px 30px;
    background: #f8fafc;
    display: flex;
    gap: 15px;
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
    .application-actions {
        flex-direction: column;
    }
}
</style>

<?php include_once '../includes/footer.php'; ?>