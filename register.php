<?php
// register.php

// Ensure your paths are correct relative to the file location
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect logged in users
if (isset($_SESSION['user_id']) || isset($_SESSION['company_id'])) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Register';
// Assuming header.php and footer.php are in the 'includes' folder
include_once 'includes/header.php';

// Variables to hold messages
$error = '';
$success = '';

// Process registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Sanitize and retrieve common data
    $role = isset($_POST['role']) ? $_POST['role'] : '';
    // Use prepared statements in a production environment for security, 
    // but sticking to mysqli_real_escape_string for consistency with existing code structure
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 2. Basic Validation
    if (empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // --- USER REGISTRATION ---
        if ($role == 'user') {
            // Retrieve and sanitize user-specific fields
            $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);
            $location = mysqli_real_escape_string($conn, $_POST['location']);
            
            // Check if email already exists
            $check_query = "SELECT id FROM users WHERE email = '$email'";
            $check_result = mysqli_query($conn, $check_query);
            if (mysqli_num_rows($check_result) > 0) {
                $error = "User with this email already exists.";
            } else {
                // FIX: Insert query now includes the 'created_at' column using MySQL's NOW() function
                $insert_query = "INSERT INTO users (full_name, email, password, phone, location, created_at) 
                                 VALUES ('$full_name', '$email', '$hashed_password', '$phone', '$location', NOW())";
                
                if (mysqli_query($conn, $insert_query)) {
                    $success = "User registered successfully! You can now log in.";
                } else {
                    // FIX: Display database error for debugging schema problems
                    $error = "Registration failed (User): " . mysqli_error($conn) . ". Please check your 'users' table columns.";
                }
            }
        
        // --- COMPANY REGISTRATION ---
        } elseif ($role == 'company') {
            // Retrieve and sanitize company-specific fields
            $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);
            $address = mysqli_real_escape_string($conn, $_POST['address']);
            $industry = mysqli_real_escape_string($conn, $_POST['industry']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            
            // Check if email already exists
            $check_query = "SELECT id FROM companies WHERE email = '$email'";
            $check_result = mysqli_query($conn, $check_query);
            if (mysqli_num_rows($check_result) > 0) {
                $error = "Company with this email already exists.";
            } else {
                // FIX: Insert query now includes the 'created_at' column using MySQL's NOW() function
                $insert_query = "INSERT INTO companies (company_name, email, password, phone, address, industry, description, created_at) 
                                 VALUES ('$company_name', '$email', '$hashed_password', '$phone', '$address', '$industry', '$description', NOW())";
                
                if (mysqli_query($conn, $insert_query)) {
                    $success = "Company registered successfully! You can now log in.";
                } else {
                    // FIX: Display database error for debugging schema problems
                    $error = "Registration failed (Company): " . mysqli_error($conn) . ". Please check your 'companies' table columns.";
                }
            }
        } else {
             $error = "Invalid role selected.";
        }
    }
}
?>

<div class="container registration-page">
    <h2>Register to ProHire</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?> <a href="login.php">Click here to Login</a></div>
    <?php endif; ?>

    <form method="POST" action="register.php" class="registration-form">
        
        <div class="form-group">
            <label for="role">I am registering as:</label>
            <select name="role" id="role" class="form-control" required>
                <option value="">-- Select Role --</option>
                <option value="user" <?php echo (isset($_POST['role']) && $_POST['role'] == 'user') ? 'selected' : ''; ?>>Job Seeker (User)</option>
                <option value="company" <?php echo (isset($_POST['role']) && $_POST['role'] == 'company') ? 'selected' : ''; ?>>Employer (Company)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
       
<div class="form-group">
    <label for="password">Password</label>
    <div class="password-input-wrapper">
        <input type="password" name="password" id="password" class="form-control" required>
        <button type="button" class="toggle-password" data-target="password">
            <i class="fas fa-eye"></i>
        </button>
    </div>
    <div class="password-strength">
        <div class="strength-meter-fill"></div>
        <p class="strength-text">Password strength</p>
    </div>
</div>

<div class="form-group">
    <label for="confirm_password">Confirm Password</label>
    <div class="password-input-wrapper">
        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        <button type="button" class="toggle-password" data-target="confirm_password">
            <i class="fas fa-eye"></i>
        </button>
    </div>
</div>



        <div id="user-fields" style="display: none;">
            <hr>
            <h4>User Details</h4>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="user_phone">Phone Number (Optional)</label>
                <input type="text" name="phone" id="user_phone" class="form-control" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="location">Location (City, State)</label>
                <input type="text" name="location" id="location" class="form-control" value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>">
            </div>
        </div>

        <div id="company-fields" style="display: none;">
            <hr>
            <h4>Company Details</h4>
            <div class="form-group">
                <label for="company_name">Company Name</label>
                <input type="text" name="company_name" id="company_name" class="form-control" value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="company_phone">Company Phone (Optional)</label>
                <input type="text" name="phone" id="company_phone" class="form-control" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="address">Head Office Address</label>
                <input type="text" name="address" id="address" class="form-control" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="industry">Industry</label>
                <input type="text" name="industry" id="industry" class="form-control" value="<?php echo isset($_POST['industry']) ? htmlspecialchars($_POST['industry']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="description">Company Description</label>
                <textarea name="description" id="description" class="form-control" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-4">Register</button>
        <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a></p>
    </form>
</div>

<script>
    // This script ensures the correct fields are visible and marked as required
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const userFields = document.getElementById('user-fields');
        const companyFields = document.getElementById('company-fields');

        function toggleRoleFields() {
            // Get all input/textarea elements within the role-specific divs
            const userInputs = userFields.querySelectorAll('input, textarea');
            const companyInputs = companyFields.querySelectorAll('input, textarea');

            if (roleSelect.value === 'user') {
                userFields.style.display = 'block';
                companyFields.style.display = 'none';
                
                // Set required attributes for critical user registration fields
                userInputs.forEach(el => {
                    // Set Full Name as required when registering as a User
                    if (el.name === 'full_name') {
                        el.setAttribute('required', 'required');
                    }
                });
                companyInputs.forEach(el => el.removeAttribute('required'));

            } else if (roleSelect.value === 'company') {
                userFields.style.display = 'none';
                companyFields.style.display = 'block';

                // Set required attributes for critical company registration fields
                userInputs.forEach(el => el.removeAttribute('required'));
                companyInputs.forEach(el => {
                    // Set Company Name as required when registering as a Company
                    if (el.name === 'company_name') {
                        el.setAttribute('required', 'required');
                    }
                });
            } else {
                // If nothing is selected, hide fields and remove required attributes
                userFields.style.display = 'none';
                companyFields.style.display = 'none';
                userInputs.forEach(el => el.removeAttribute('required'));
                companyInputs.forEach(el => el.removeAttribute('required'));
            }
        }
        
        roleSelect.addEventListener('change', toggleRoleFields);
        // Initial call to set state based on URL or previous post
        toggleRoleFields();
    });
</script>

<?php 
// Assuming footer.php is in the 'includes' folder
include_once 'includes/footer.php'; 
?>