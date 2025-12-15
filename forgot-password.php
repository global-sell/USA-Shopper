<?php
require_once 'config/config.php';

if (isLoggedIn()) {
    redirect(SITE_URL);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
    
    if (empty($email)) {
        $error = 'Please enter your email address';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email format';
    } else {
        // Verify Turnstile token
        $turnstileResult = verifyTurnstile($turnstileToken, $_SERVER['REMOTE_ADDR']);
        if (!$turnstileResult['success']) {
            $error = 'Security verification failed. Please try again.';
        } else {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            // Check if reset columns exist, add if not
            $columns = $db->query("SHOW COLUMNS FROM users LIKE 'reset_token'")->num_rows;
            if ($columns == 0) {
                $db->query("ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL AFTER password");
                $db->query("ALTER TABLE users ADD COLUMN reset_token_expiry DATETIME NULL AFTER reset_token");
            }
            
            // Generate reset token
            $resetToken = generateToken();
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
            $stmt->bind_param("ssi", $resetToken, $expiry, $user['id']);
            $stmt->execute();
            
            // Send reset email
            $resetLink = SITE_URL . '/reset-password.php?token=' . $resetToken;
            $subject = "Password Reset Request";
            $message = "
                <h2>Password Reset Request</h2>
                <p>Hi " . htmlspecialchars($user['name']) . ",</p>
                <p>We received a request to reset your password. Click the link below to reset it:</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you didn't request this, please ignore this email.</p>
                <p>Best regards,<br>" . getSetting('site_name', 'YBT Digital') . " Team</p>
            ";
            
            sendEmail($email, $subject, $message);
            
            // For development: Show the reset link directly
            if (defined('DEVELOPMENT_MODE') || !@mail('test@test.com', 'test', 'test')) {
                $success = 'Password reset link: <a href="' . $resetLink . '" class="fw-bold">' . $resetLink . '</a><br><small class="text-muted">Click the link above to reset your password. (Email logging is active - check logs/emails.log for the full email)</small>';
            } else {
                $success = 'Password reset link has been sent to your email address.';
            }
        } else {
            $error = 'No account found with that email address';
        }
        }
    }
}

$pageTitle = "Forgot Password";
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 40vh; display: flex; align-items: center;">
    <div class="container text-center text-white">
        <div class="animate__animated animate__fadeInDown">
            <i class="fas fa-key fa-4x mb-3" style="opacity: 0.9;"></i>
            <h1 class="display-4 fw-bold mb-3">Forgot Password?</h1>
            <p class="lead mb-0">Don't worry, we'll help you reset it</p>
        </div>
    </div>
</section>

<div class="container py-5" style="margin-top: -80px;">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card border-0 shadow-lg animate__animated animate__fadeInUp" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-4 p-md-5">
                    
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
                    
                    <form method="POST" action="">
                        <div class="form-outline mb-4">
                            <input type="email" id="email" name="email" class="form-control form-control-lg" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            <label class="form-label" for="email">Email Address</label>
                        </div>
                        
                        <!-- Cloudflare Turnstile -->
                        <div class="mb-4 d-flex justify-content-center">
                            <?php echo getTurnstileWidget(); ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-4" style="border-radius: 10px; padding: 15px;">
                            <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                        </button>
                        
                        <div class="text-center">
                            <a href="<?php echo SITE_URL; ?>/login.php" class="text-primary text-decoration-none fw-bold">
                                <i class="fas fa-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </form>
                    
                    <!-- Help Info -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="row text-center g-3">
                            <div class="col-4">
                                <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                                <p class="small mb-0 text-muted">1 Hour</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                <p class="small mb-0 text-muted">Secure</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-envelope fa-2x text-info mb-2"></i>
                                <p class="small mb-0 text-muted">Email</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Info Cards -->
            <div class="row g-3 mt-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInLeft" style="border-radius: 15px; animation-delay: 0.2s;">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-question-circle fa-3x text-primary mb-3"></i>
                            <h6 class="fw-bold">Need Help?</h6>
                            <p class="small text-muted mb-0">Contact our support team</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInRight" style="border-radius: 15px; animation-delay: 0.2s;">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-user-plus fa-3x text-success mb-3"></i>
                            <h6 class="fw-bold">New User?</h6>
                            <p class="small text-muted mb-0"><a href="<?php echo SITE_URL; ?>/signup.php" class="text-decoration-none">Create an account</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
.form-outline input:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    border-color: #667eea;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.card {
    transition: transform 0.3s ease;
}

.animate__animated {
    animation-duration: 0.8s;
}

a.text-primary:hover {
    color: #764ba2 !important;
}

.alert-success a {
    color: #0f5132;
    text-decoration: underline;
}

.alert-success a:hover {
    color: #0a3622;
}
</style>

<script>
// Form animation on submit
document.querySelector('form')?.addEventListener('submit', function(e) {
    const button = this.querySelector('button[type="submit"]');
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
    button.disabled = true;
});
</script>

<?php require_once 'includes/footer.php'; ?>
