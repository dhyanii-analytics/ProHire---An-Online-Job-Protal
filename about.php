<?php
require_once 'includes/config.php';
$pageTitle = 'About Us';
include_once 'includes/header.php';
?>

<div class="container" style="padding: 50px 0;">
    <h1>About ProHire</h1>
    
    <div class="about-content">
        <p>ProHire is a leading job portal that connects talented professionals with top companies. Our mission is to make the job search process efficient, transparent, and rewarding for both job seekers and employers.</p>
        
        <h2>Our Vision</h2>
        <p>To create a world where everyone has access to meaningful employment opportunities that match their skills and aspirations.</p>
        
        <h2>Our Mission</h2>
        <p>To provide a platform that simplifies the hiring process by leveraging technology to connect the right talent with the right opportunities.</p>
        
        <h2>Our Values</h2>
        <ul>
            <li><strong>Integrity:</strong> We operate with transparency and honesty in all our interactions.</li>
            <li><strong>Innovation:</strong> We continuously improve our platform to provide the best experience.</li>
            <li><strong>Inclusivity:</strong> We believe in equal opportunities for all, regardless of background.</li>
            <li><strong>Excellence:</strong> We strive for excellence in everything we do.</li>
        </ul>
        
        <h2>Our Team</h2>
        <p>Our team consists of experienced professionals from the tech and HR industries who are passionate about transforming the way people find jobs and companies hire talent.</p>
        
        <h2>Contact Us</h2>
        <p>If you have any questions or feedback, please don't hesitate to <a href="contact.php">contact us</a>.</p>
    </div>
</div>

<style>
.about-content {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.about-content h2 {
    color: #4f46e5;
    margin-top: 30px;
    margin-bottom: 15px;
}

.about-content ul {
    margin-left: 20px;
}

.about-content li {
    margin-bottom: 10px;
}
</style>

<?php include_once 'includes/footer.php'; ?>