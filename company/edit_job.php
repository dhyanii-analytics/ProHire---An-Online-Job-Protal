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
$query = "SELECT * FROM jobs WHERE id = $job_id AND company_id = $company_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    // Job not found or doesn't belong to this company
    header('Location: dashboard.php');
    exit();
}

$job = mysqli_fetch_assoc($result);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $requirements = mysqli_real_escape_string($conn, $_POST['requirements']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $job_type = mysqli_real_escape_string($conn, $_POST['job_type']);
    $experience_required = mysqli_real_escape_string($conn, $_POST['experience_required']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    
    $update_query = "UPDATE jobs SET 
                    job_title = '$job_title', 
                    description = '$description', 
                    requirements = '$requirements', 
                    salary = '$salary', 
                    location = '$location', 
                    job_type = '$job_type', 
                    experience_required = '$experience_required', 
                    deadline = '$deadline' 
                    WHERE id = $job_id AND company_id = $company_id";
    
    if (mysqli_query($conn, $update_query)) {
        $success = "Job updated successfully!";
        // Refresh job data
        $query = "SELECT * FROM jobs WHERE id = $job_id AND company_id = $company_id";
        $result = mysqli_query($conn, $query);
        $job = mysqli_fetch_assoc($result);
    } else {
        $error = "Failed to update job. Please try again.";
    }
}

$pageTitle = 'Edit Job - ' . $job['job_title'];
include_once '../includes/header.php';
?>

<div class="container" style="padding: 50px 0;">
    <div class="form-container">
        <h2>Edit Job</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="job_title">Job Title</label>
                <input type="text" name="job_title" id="job_title" value="<?php echo $job['job_title']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Job Description</label>
                <textarea name="description" id="description" required><?php echo $job['description']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="requirements">Requirements</label>
                <textarea name="requirements" id="requirements" required><?php echo $job['requirements']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="salary">Salary</label>
                <input type="text" name="salary" id="salary" value="<?php echo $job['salary']; ?>" placeholder="e.g. $50,000 - $70,000">
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" name="location" id="location" value="<?php echo $job['location']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="job_type">Job Type</label>
                <select name="job_type" id="job_type" required>
                    <option value="">Select Job Type</option>
                    <option value="Full-time" <?php echo ($job['job_type'] == 'Full-time') ? 'selected' : ''; ?>>Full-time</option>
                    <option value="Part-time" <?php echo ($job['job_type'] == 'Part-time') ? 'selected' : ''; ?>>Part-time</option>
                    <option value="Contract" <?php echo ($job['job_type'] == 'Contract') ? 'selected' : ''; ?>>Contract</option>
                    <option value="Internship" <?php echo ($job['job_type'] == 'Internship') ? 'selected' : ''; ?>>Internship</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="experience_required">Experience Required</label>
                <select name="experience_required" id="experience_required" required>
                    <option value="">Select Experience Level</option>
                    <option value="Fresher" <?php echo ($job['experience_required'] == 'Fresher') ? 'selected' : ''; ?>>Fresher</option>
                    <option value="1-2 years" <?php echo ($job['experience_required'] == '1-2 years') ? 'selected' : ''; ?>>1-2 years</option>
                    <option value="3-5 years" <?php echo ($job['experience_required'] == '3-5 years') ? 'selected' : ''; ?>>3-5 years</option>
                    <option value="5+ years" <?php echo ($job['experience_required'] == '5+ years') ? 'selected' : ''; ?>>5+ years</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="deadline">Application Deadline</label>
                <input type="date" name="deadline" id="deadline" value="<?php echo $job['deadline']; ?>" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Job</button>
                <a href="view_job.php?id=<?php echo $job_id; ?>" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>