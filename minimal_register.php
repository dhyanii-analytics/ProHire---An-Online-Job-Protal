<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h2>Form Submitted</h2>";
    echo "<h3>POST Data:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    $role = $_POST['role'] ?? '';
    
    if ($role == 'user') {
        $full_name = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        echo "<h3>User Registration Data:</h3>";
        echo "<p>Full Name: $full_name</p>";
        echo "<p>Email: $email</p>";
        echo "<p>Password: $password</p>";
        echo "<p>Confirm Password: $confirm_password</p>";
        
        if (empty($full_name) || empty($email) || empty($password)) {
            echo "<p style='color: red;'>Error: All fields are required</p>";
        } elseif ($password !== $confirm_password) {
            echo "<p style='color: red;'>Error: Passwords do not match</p>";
        } else {
            // Check if email already exists
            $check_query = "SELECT id FROM users WHERE email = '$email'";
            $check_result = mysqli_query($conn, $check_query);
            
            if ($check_result && mysqli_num_rows($check_result) > 0) {
                echo "<p style='color: red;'>Error: Email already exists</p>";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $insert_query = "INSERT INTO users (full_name, email, password) VALUES ('$full_name', '$email', '$hashed_password')";
                $insert_result = mysqli_query($conn, $insert_query);
                
                if ($insert_result) {
                    echo "<p style='color: green;'>Success: User registered successfully</p>";
                } else {
                    echo "<p style='color: red;'>Error: " . mysqli_error($conn) . "</p>";
                }
            }
        }
    } elseif ($role == 'company') {
        $company_name = $_POST['company_name'] ?? '';
        $email = $_POST['company_email'] ?? '';
        $password = $_POST['company_password'] ?? '';
        $confirm_password = $_POST['company_confirm_password'] ?? '';
        
        echo "<h3>Company Registration Data:</h3>";
        echo "<p>Company Name: $company_name</p>";
        echo "<p>Email: $email</p>";
        echo "<p>Password: $password</p>";
        echo "<p>Confirm Password: $confirm_password</p>";
        
        if (empty($company_name) || empty($email) || empty($password)) {
            echo "<p style='color: red;'>Error: All fields are required</p>";
        } elseif ($password !== $confirm_password) {
            echo "<p style='color: red;'>Error: Passwords do not match</p>";
        } else {
            // Check if email already exists
            $check_query = "SELECT id FROM companies WHERE email = '$email'";
            $check_result = mysqli_query($conn, $check_query);
            
            if ($check_result && mysqli_num_rows($check_result) > 0) {
                echo "<p style='color: red;'>Error: Email already exists</p>";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $insert_query = "INSERT INTO companies (company_name, email, password) VALUES ('$company_name', '$email', '$hashed_password')";
                $insert_result = mysqli_query($conn, $insert_query);
                
                if ($insert_result) {
                    echo "<p style='color: green;'>Success: Company registered successfully</p>";
                } else {
                    echo "<p style='color: red;'>Error: " . mysqli_error($conn) . "</p>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Minimal Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 15px;
            background: #6366f1;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #4f46e5;
        }
        .form-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>Minimal Registration Form</h1>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="role">Register As:</label>
            <select name="role" id="role" required>
                <option value="">Select Role</option>
                <option value="user">Job Seeker</option>
                <option value="company">Employer</option>
            </select>
        </div>
        
        <div id="user-form" class="form-section" style="display: none;">
            <h2>User Registration</h2>
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" name="full_name" id="full_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
        </div>
        
        <div id="company-form" class="form-section" style="display: none;">
            <h2>Company Registration</h2>
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" name="company_name" id="company_name" required>
            </div>
            <div class="form-group">
                <label for="company_email">Email:</label>
                <input type="email" name="company_email" id="company_email" required>
            </div>
            <div class="form-group">
                <label for="company_password">Password:</label>
                <input type="password" name="company_password" id="company_password" required>
            </div>
            <div class="form-group">
                <label for="company_confirm_password">Confirm Password:</label>
                <input type="password" name="company_confirm_password" id="company_confirm_password" required>
            </div>
        </div>
        
        <div class="form-group">
            <button type="submit">Register</button>
        </div>
    </form>
    
    <script>
        document.getElementById('role').addEventListener('change', function() {
            const userForm = document.getElementById('user-form');
            const companyForm = document.getElementById('company-form');
            
            if (this.value === 'user') {
                userForm.style.display = 'block';
                companyForm.style.display = 'none';
            } else if (this.value === 'company') {
                userForm.style.display = 'none';
                companyForm.style.display = 'block';
            } else {
                userForm.style.display = 'none';
                companyForm.style.display = 'none';
            }
        });
    </script>
</body>
</html>