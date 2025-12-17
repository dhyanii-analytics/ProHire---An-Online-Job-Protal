<?php


// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if user is logged in
function is_user_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to check if company is logged in
function is_company_logged_in() {
    return isset($_SESSION['company_id']);
}

// Function to check if admin is logged in
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}

// Function to redirect if not logged in
function redirect_if_not_logged_in($role = 'user') {
    if ($role == 'user' && !is_user_logged_in()) {
        header('Location: ../login.php');
        exit();
    } elseif ($role == 'company' && !is_company_logged_in()) {
        header('Location: ../login.php');
        exit();
    } elseif ($role == 'admin' && !is_admin_logged_in()) {
        header('Location: ../login.php');
        exit();
    }
}

// Function to format date
function format_date($date) {
    return date('M d, Y', strtotime($date));
}

// Function to get job application count
function get_application_count($job_id) {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM applications WHERE job_id = $job_id";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    return $data['count'];
}

// Function to get user application count
function get_user_application_count($user_id) {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM applications WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    return $data['count'];
}

// Function to redirect to error page
function redirect_to_error($code = 404, $message = '') {
    $url = BASE_URL . 'error.php?code=' . $code;
    if (!empty($message)) {
        $url .= '&message=' . urlencode($message);
    }
    header('Location: ' . $url);
    exit();
}

// Function to log errors
function log_error($message, $file = '', $line = 0) {
    $log_file = __DIR__ . '/../logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] Error: $message";
    
    if (!empty($file)) {
        $log_message .= " in $file";
    }
    
    if ($line > 0) {
        $log_message .= " on line $line";
    }
    
    $log_message .= "\n";
    
    // Create logs directory if it doesn't exist
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    // Append to log file
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Function to display user-friendly error messages
function display_error($message, $type = 'danger') {
    return '<div class="alert alert-' . $type . '">' . $message . '</div>';
}

// Function to handle database errors
function handle_db_error($query = '') {
    global $conn;
    $error = mysqli_error($conn);
    $message = "Database error: " . $error;
    
    if (!empty($query)) {
        $message .= " Query: " . $query;
    }
    
    log_error($message);
    
    // Show user-friendly error in development
    if (defined('DEV_MODE') && DEV_MODE) {
        return display_error($message);
    } else {
        return display_error("A database error occurred. Please try again later.");
    }
}

// Function to validate email
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate password strength
function is_strong_password($password) {
    // At least 8 characters, at least one letter, one number and one special character
    if (strlen($password) < 8) {
        return false;
    }
    
    if (!preg_match('/[A-Za-z]/', $password)) {
        return false;
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return false;
    }
    
    return true;
}

// Custom error handler
function custom_error_handler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return false;
    }
    
    $error_type = match($errno) {
        E_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Strict',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED => 'Deprecated',
        E_USER_DEPRECATED => 'User Deprecated',
        default => 'Unknown'
    };
    
    $message = "[$error_type] $errstr in $errfile on line $errline";
    log_error($message, $errfile, $errline);
    
    // Don't execute PHP internal error handler
    return true;
}

// Set custom error handler
set_error_handler('custom_error_handler');

// No closing PHP tag