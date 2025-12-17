<?php
require_once '../includes/config.php';

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header('Location: ../login.php');
    exit();
}

$company_id = $_SESSION['company_id'];
$pageTitle = 'Post a Job';
include_once '../includes/header.php';

// Process job posting
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $requirements = mysqli_real_escape_string($conn, $_POST['requirements']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $job_type = mysqli_real_escape_string($conn, $_POST['job_type']);
    $experience_required = mysqli_real_escape_string($conn, $_POST['experience_required']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    
    $insert_query = "INSERT INTO jobs (company_id, job_title, description, requirements, salary, location, job_type, experience_required, deadline) 
                    VALUES ($company_id, '$job_title', '$description', '$requirements', '$salary', '$location', '$job_type', '$experience_required', '$deadline')";
    
    if (mysqli_query($conn, $insert_query)) {
        $success = "Job posted successfully!";
    } else {
        $error = "Failed to post job. Please try again.";
    }
}
?>

<div class="container" style="padding: 50px 0;">
    <h1>Post a New Job</h1>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="form-container">
        <form method="POST" action="">
            <div class="form-group">
                <label for="job_title">Job Title</label>
                <input type="text" name="job_title" id="job_title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Job Description</label>
                <textarea name="description" id="description" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="requirements">Requirements</label>
                <textarea name="requirements" id="requirements" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="salary">Salary</label>
                <input type="text" name="salary" id="salary" placeholder="e.g. $50,000 - $70,000">
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" name="location" id="location" required>
            </div>
            
            <div class="form-group">
                <label for="job_type">Job Type</label>
                <select name="job_type" id="job_type" required>
                    <option value="">Select Job Type</option>
                    <option value="Full-time">Full-time</option>
                    <option value="Part-time">Part-time</option>
                    <option value="Contract">Contract</option>
                    <option value="Internship">Internship</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="experience_required">Experience Required</label>
                <select name="experience_required" id="experience_required" required>
                    <option value="">Select Experience Level</option>
                    <option value="Fresher">Fresher</option>
                    <option value="1-2 years">1-2 years</option>
                    <option value="3-5 years">3-5 years</option>
                    <option value="5+ years">5+ years</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="deadline">Application Deadline</label>
                <input type="date" name="deadline" id="deadline" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Post Job</button>
                <a href="dashboard.php" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>