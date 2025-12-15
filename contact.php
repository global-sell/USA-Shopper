<?php
$pageTitle = "Contact Us";
require_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email format';
    } else {
        // Verify Turnstile token
        $turnstileResult = verifyTurnstile($turnstileToken, $_SERVER['REMOTE_ADDR']);
        if (!$turnstileResult['success']) {
            $error = 'Security verification failed. Please try again.';
        } else {
        // Send email to admin
        $adminEmail = getSetting('from_email', 'admin@ybtdigital.com');
        $emailSubject = "Contact Form: " . $subject;
        $emailMessage = "
            <h3>New Contact Form Submission</h3>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Subject:</strong> $subject</p>
            <p><strong>Message:</strong></p>
            <p>" . nl2br($message) . "</p>
        ";
        
        if (sendEmail($adminEmail, $emailSubject, $emailMessage)) {
            $success = 'Thank you for contacting us! We will get back to you soon.';
            $name = $email = $subject = $message = '';
        } else {
            $error = 'Failed to send message. Please try again later.';
        }
        }
    }
}
?>

<!-- Hero Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center text-white">
        <h1 class="display-4 fw-bold mb-3">Get In Touch</h1>
        <p class="lead mb-0">Have a question? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
    </div>
</section>

<div class="container py-5">
    
    <div class="row g-4">
        <!-- Contact Form -->
        <div class="col-lg-8">
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-envelope me-2"></i>Send Us a Message</h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="form-outline">
                                    <input type="text" id="name" name="name" class="form-control" 
                                           value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                    <label class="form-label" for="name">Your Name</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-outline">
                                    <input type="email" id="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                    <label class="form-label" for="email">Your Email</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-outline mb-4">
                            <input type="text" id="subject" name="subject" class="form-control" 
                                   value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                            <label class="form-label" for="subject">Subject</label>
                        </div>
                        
                        <div class="form-outline mb-4">
                            <textarea id="message" name="message" class="form-control" rows="6" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                            <label class="form-label" for="message">Message</label>
                        </div>
                        
                        <!-- Cloudflare Turnstile -->
                        <div class="mb-4 d-flex justify-content-center">
                            <?php echo getTurnstileWidget(); ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
        
        <!-- Contact Info Sidebar -->
        <div class="col-lg-4">
            <!-- Contact Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                <i class="fas fa-envelope fa-lg text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Email</h6>
                                <p class="text-muted mb-0 small"><?php echo getSetting('from_email', 'support@ybtdigital.com'); ?></p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                <i class="fas fa-headset fa-lg text-success"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Support</h6>
                                <p class="text-muted mb-0 small">24/7 Customer Support</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start">
                            <div class="bg-info bg-opacity-10 rounded p-3 me-3">
                                <i class="fas fa-clock fa-lg text-info"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Response Time</h6>
                                <p class="text-muted mb-0 small">Within 24 hours</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Social Media -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <h5 class="fw-bold mb-3">Follow Us</h5>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" class="btn btn-primary btn-lg rounded-circle" style="width: 50px; height: 50px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-info btn-lg rounded-circle" style="width: 50px; height: 50px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-danger btn-lg rounded-circle" style="width: 50px; height: 50px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-success btn-lg rounded-circle" style="width: 50px; height: 50px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Quick Links</h5>
                    <div class="d-grid gap-2">
                        <a href="<?php echo SITE_URL; ?>/blogs.php" class="btn btn-outline-primary text-start">
                            <i class="fas fa-blog me-2"></i>Visit Our Blog
                        </a>
                        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline-primary text-start">
                            <i class="fas fa-shopping-bag me-2"></i>Browse Products
                        </a>
                        <?php if (!isLoggedIn()): ?>
                        <a href="<?php echo SITE_URL; ?>/signup.php" class="btn btn-outline-primary text-start">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- FAQ Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="text-center mb-4">
                <h3 class="fw-bold">Frequently Asked Questions</h3>
                <p class="text-muted">Find quick answers to common questions</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><i class="fas fa-question-circle text-primary me-2"></i>How do I place an order?</h5>
                            <p class="text-muted mb-0">Browse our products, add items to your cart, and proceed to checkout. You'll receive instant access after payment.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><i class="fas fa-question-circle text-primary me-2"></i>What payment methods do you accept?</h5>
                            <p class="text-muted mb-0">We accept all major credit cards, debit cards, and secure online payment methods through our payment gateway.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><i class="fas fa-question-circle text-primary me-2"></i>Can I get a refund?</h5>
                            <p class="text-muted mb-0">Yes! We offer a 30-day money-back guarantee if you're not satisfied with your purchase.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><i class="fas fa-question-circle text-primary me-2"></i>How long does delivery take?</h5>
                            <p class="text-muted mb-0">Digital products are available for instant download. Physical products typically ship within 2-5 business days.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>