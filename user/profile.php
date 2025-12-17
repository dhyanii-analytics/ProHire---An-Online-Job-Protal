<?php
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$pageTitle = 'My Profile';
include_once '../includes/header.php';

// Get user details
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Process profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $experience = mysqli_real_escape_string($conn, $_POST['experience']);
    $education = mysqli_real_escape_string($conn, $_POST['education']);
    
    // Handle profile picture upload
    $profile_pic = $user['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_pic']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $file_name = time() . '_' . $_FILES['profile_pic']['name'];
            $file_path = '../assets/images/profiles/' . $file_name;
            
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $file_path)) {
                $profile_pic = 'assets/images/profiles/' . $file_name;
            }
        }
    }
    
    // Handle resume upload
    $resume_path = $user['resume_path'];
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $file_type = $_FILES['resume']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $file_name = time() . '_' . $_FILES['resume']['name'];
            $file_path = '../assets/resumes/' . $file_name;
            
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $file_path)) {
                $resume_path = 'assets/resumes/' . $file_name;
            }
        }
    }
    
    $update_query = "UPDATE users SET 
                    full_name = '$full_name', 
                    email = '$email', 
                    phone = '$phone', 
                    location = '$location', 
                    skills = '$skills', 
                    experience = '$experience', 
                    education = '$education', 
                    profile_pic = '$profile_pic', 
                    resume_path = '$resume_path' 
                    WHERE id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        $success = "Profile updated successfully!";
        
        // Refresh user data
        $query = "SELECT * FROM users WHERE id = $user_id";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = "Failed to update profile. Please try again.";
    }
}
?>

<div class="container" style="padding: 50px 0;">
    <h1>My Profile</h1>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="profile-picture">
                <img src="<?php echo !empty($user['profile_pic']) ? BASE_URL . $user['profile_pic'] : BASE_URL . 'assets/images/profile-placeholder.jpg'; ?>" alt="Profile Picture">
            </div>
            <h2><?php echo $user['full_name']; ?></h2>
            <p><?php echo $user['email']; ?></p>
            <p><?php echo $user['location']; ?></p>
            
            <div class="profile-stats">
                <div class="stat">
                    <h3>0</h3>
                    <p>Applications</p>
                </div>
                <div class="stat">
                    <h3>0</h3>
                    <p>Interviews</p>
                </div>
                <div class="stat">
                    <h3>0%</h3>
                    <p>Profile Completion</p>
                </div>
            </div>
        </div>
        
        <div class="profile-content">
            <form method="POST" action="" enctype="multipart/form-data">
                <h3>Personal Information</h3>
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" value="<?php echo $user['full_name']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo $user['email']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="<?php echo $user['phone']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" name="location" id="location" value="<?php echo $user['location']; ?>">
                </div>
                
                <h3>Professional Information</h3>
                <div class="form-group">
                    <label for="skills">Skills</label>
                    <textarea name="skills" id="skills"><?php echo $user['skills']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="experience">Work Experience</label>
                    <textarea name="experience" id="experience"><?php echo $user['experience']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="education">Education</label>
                    <textarea name="education" id="education"><?php echo $user['education']; ?></textarea>
                </div>
                
                <h3>Documents</h3>
                <div class="form-group">
                    <label for="profile_pic">Profile Picture</label>
                    <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="resume">Resume</label>
                    <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx">
                    <?php if (!empty($user['resume_path'])): ?>
                        <p>Current Resume: <a href="<?php echo BASE_URL . $user['resume_path']; ?>" target="_blank">View</a></p>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="dashboard.php" class="btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.profile-container {
    display: flex;
    gap: 30px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.profile-sidebar {
    width: 300px;
    padding: 30px;
    background: #f8fafc;
    text-align: center;
}

.profile-picture {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto 20px;
}

.profile-picture img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-sidebar h2 {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.profile-stats {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
}

.profile-stats .stat {
    text-align: center;
}

.profile-stats .stat h3 {
    font-size: 1.5rem;
    margin-bottom: 5px;
    color: #4f46e5;
}

.profile-content {
    flex: 1;
    padding: 30px;
}

.profile-content h3 {
    font-size: 1.3rem;
    margin-bottom: 20px;
    color: #4f46e5;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 10px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 16px;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

@media (max-width: 768px) {
    .profile-container {
        flex-direction: column;
    }
    
    .profile-sidebar {
        width: 100%;
    }
    
    .profile-stats {
        justify-content: center;
        gap: 30px;
    }
}
</style>

<?php include_once '../includes/footer.php'; ?>