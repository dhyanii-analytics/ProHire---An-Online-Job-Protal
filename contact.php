<?php
require_once 'includes/config.php';
$pageTitle = 'Contact Us';
include_once 'includes/header.php';

// Process contact form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // In a real application, you would send an email here
    // For demo purposes, we'll just show a success message
    $success = "Your message has been sent successfully! We'll get back to you soon.";
}
?>

<div class="container" style="padding: 50px 0;">
    <h1>Contact Us</h1>
    
    <div class="contact-container">
        <div class="contact-info">
            <h2>Get in Touch</h2>
            <p>Have questions or feedback? We'd love to hear from you.</p>
            
            <div class="contact-details">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Address</h3>
                        <p>123 Rich Street, Ahmedabad </p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Phone</h3>
                        <p>+91 95555 07070</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p>info@prohire.com</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="contact-form">
            <h2>Send us a Message</h2>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" name="name" id="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea name="message" id="message" required></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.contact-container {
    display: flex;
    gap: 30px;
    margin-top: 30px;
}

.contact-info, .contact-form {
    flex: 1;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.contact-details {
    margin-top: 30px;
}

.contact-item {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.contact-item i {
    font-size: 1.5rem;
    color: #4f46e5;
    width: 30px;
    text-align: center;
}

.contact-item h3 {
    margin-bottom: 5px;
    color: #4f46e5;
}

@media (max-width: 768px) {
    .contact-container {
        flex-direction: column;
    }
}
</style>

<?php include_once 'includes/footer.php'; ?>