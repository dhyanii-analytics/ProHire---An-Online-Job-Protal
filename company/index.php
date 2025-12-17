<?php
require_once '../includes/config.php';

// Check if company is already logged in
if (isset($_SESSION['company_id'])) {
    header('Location: dashboard.php');
    exit();
}

$pageTitle = 'For Companies';
include_once '../includes/header.php';
?>

<div class="container" style="padding: 50px 0;">
    <div class="company-landing">
        <h1>Partner with ProHire to Find Top Talent</h1>
        <p>Join thousands of companies that have found their perfect candidates through our platform.</p>
        
        <div class="company-features">
            <div class="feature-card">
                <i class="fas fa-users"></i>
                <h3>Access to Top Talent</h3>
                <p>Connect with qualified professionals across various industries and experience levels.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-bullseye"></i>
                <h3>Targeted Hiring</h3>
                <p>Use our advanced filtering to find candidates that match your specific requirements.</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-tachometer-alt"></i>
                <h3>Efficient Process</h3>
                <p>Streamline your hiring process with our easy-to-use application management system.</p>
            </div>
        </div>
        
        <div class="company-cta">
            <h2>Ready to Find Your Next Hire?</h2>
            <p>Create an account to start posting jobs and connecting with candidates today.</p>
            <div class="cta-buttons">
                <a href="../login.php?role=company" class="btn btn-primary">Login</a>
                <a href="../register.php?role=company" class="btn">Register</a>
            </div>
        </div>
    </div>
</div>

<style>
.company-landing {
    text-align: center;
    max-width: 1000px;
    margin: 0 auto;
}

.company-landing h1 {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 20px;
}

.company-landing p {
    font-size: 1.2rem;
    color: var(--dark-gray);
    margin-bottom: 40px;
}

.company-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
}

.company-features .feature-card {
    background: var(--white);
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    text-align: center;
}

.company-features .feature-card i {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 20px;
}

.company-features .feature-card h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: var(--text-color);
}

.company-cta {
    background: var(--white);
    border-radius: 10px;
    padding: 40px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.company-cta h2 {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.cta-buttons {
    margin-top: 30px;
    display: flex;
    justify-content: center;
    gap: 15px;
}

@media (max-width: 768px) {
    .company-landing h1 {
        font-size: 2rem;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php include_once '../includes/footer.php'; ?>