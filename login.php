<?php
require_once 'includes/config.php';
$pageTitle = 'Login';
include_once 'includes/header.php';

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    if (empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required";
    } else {
        if ($role == 'user') {
            $query = "SELECT * FROM users WHERE email = '$email'";
        } elseif ($role == 'company') {
            $query = "SELECT * FROM companies WHERE email = '$email'";
        } elseif ($role == 'admin') {
            $query = "SELECT * FROM admin WHERE username = '$email'";
        } else {
            $error = "Invalid role selected";
        }
        
        if (!isset($error)) {
            $result = mysqli_query($conn, $query);
            
            if (!$result) {
                // Log database error
                log_error("Database query failed: " . mysqli_error($conn), __FILE__, __LINE__);
                $error = "A database error occurred. Please try again later.";
            } else if (mysqli_num_rows($result) == 0) {
                $error = "Invalid email/username";
            } else {
                $user = mysqli_fetch_assoc($result);
                
                // Check password - try both hashed and plain text
                $is_valid = false;
                
                // First, try using password_verify (for hashed passwords)
                if (password_verify($password, $user['password'])) {
                    $is_valid = true;
                }
                // If that fails, try plain text comparison (for sample data)
                else if ($password === $user['password']) {
                    $is_valid = true;
                }
                
                if ($is_valid) {
                    // Set session variables
                    if ($role == 'user') {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['full_name'];
                        header('Location: user/dashboard.php');
                    } elseif ($role == 'company') {
                        $_SESSION['company_id'] = $user['id'];
                        $_SESSION['company_name'] = $user['company_name'];
                        header('Location: company/dashboard.php');
                    } elseif ($role == 'admin') {
                        $_SESSION['admin_id'] = $user['id'];
                        $_SESSION['admin_name'] = $user['username'];
                        header('Location: admin/dashboard.php');
                    }
                    exit();
                } else {
                    $error = "Invalid password";
                }
            }
        }
    }
}

// Get role from URL parameter
$selectedRole = isset($_GET['role']) ? $_GET['role'] : '';
?>

<div class="container" style="padding: 50px 0;">
    <div class="form-container">
        <h2>Login to Your Account</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="role">Login As</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="">Select Role</option>
                    <option value="user" <?php echo ($selectedRole == 'user') ? 'selected' : ''; ?>>Job Seeker</option>
                    <option value="company" <?php echo ($selectedRole == 'company') ? 'selected' : ''; ?>>Employer</option>
                    <option value="admin" <?php echo ($selectedRole == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="email">Email/Username</label>
                <input type="text" name="email" id="email" class="form-control" required>
            </div>
            
           <div class="form-group">
    <label for="password">Password</label>
    <div class="password-input-wrapper">
        <input type="password" name="password" id="password" class="form-control" required>
        <button type="button" class="toggle-password" data-target="password">
            <i class="fas fa-eye"></i>
        </button>
    </div>
</div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </div>
            
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const toggleIcon = passwordField.nextElementSibling.querySelector('i');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>

<style>
.password-container {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #64748b;
}
</style>

<?php include_once 'includes/footer.php'; ?>