<?php
require_once 'includes/config.php';
$pageTitle = 'Error';
include_once 'includes/header.php';

// Get error parameters
$errorCode = isset($_GET['code']) ? $_GET['code'] : 404;
$errorMessage = isset($_GET['message']) ? $_GET['message'] : 'Page not found';

// Set error messages based on error code
$errorTitles = [
    404 => 'Page Not Found',
    403 => 'Access Denied',
    500 => 'Server Error',
    400 => 'Bad Request',
    401 => 'Unauthorized'
];

$errorMessages = [
    404 => 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.',
    403 => 'You don\'t have permission to access this resource.',
    500 => 'Our server encountered an internal error and was unable to complete your request.',
    400 => 'The server cannot process the request due to a client error.',
    401 => 'You need to log in to access this page.'
];

// Set default values if error code is not in our list
$errorTitle = isset($errorTitles[$errorCode]) ? $errorTitles[$errorCode] : 'Error';
$errorMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : $errorMessage;
?>

<div class="container" style="padding: 50px 0;">
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>Error <?php echo $errorCode; ?></h1>
        <h2><?php echo $errorTitle; ?></h2>
        <p><?php echo $errorMessage; ?></p>
        
        <div class="error-actions">
            <a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Go to Homepage</a>
            <a href="javascript:history.back()" class="btn">Go Back</a>
        </div>
        
        <?php if (isset($_SESSION['user_id']) || isset($_SESSION['company_id']) || isset($_SESSION['admin_id'])): ?>
            <div class="error-help">
                <h3>Need Help?</h3>
                <p>If you believe this is an error, please contact our support team.</p>
                <a href="<?php echo BASE_URL; ?>contact.php" class="btn">Contact Support</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.error-container {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
    background: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.error-icon {
    font-size: 4rem;
    color: #ef4444;
    margin-bottom: 20px;
}

.error-container h1 {
    font-size: 3rem;
    color: #ef4444;
    margin-bottom: 10px;
}

.error-container h2 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: #334155;
}

.error-container p {
    font-size: 1.1rem;
    margin-bottom: 30px;
    color: #64748b;
}

.error-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
}

.error-help {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid #e2e8f0;
}

.error-help h3 {
    font-size: 1.2rem;
    margin-bottom: 10px;
    color: #334155;
}

@media (max-width: 768px) {
    .error-container {
        padding: 30px 20px;
    }
    
    .error-container h1 {
        font-size: 2.5rem;
    }
    
    .error-actions {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php include_once 'includes/footer.php'; ?>