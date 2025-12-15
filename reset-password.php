<?php
require_once 'config/config.php';

if (isLoggedIn()) {
    redirect(SITE_URL);
}

$token = sanitizeInput($_GET['token'] ?? '');
$error = '';
$success = '';
$validToken = false;

if (empty($token)) {
    redirect(SITE_URL . '/forgot-password.php');
}

$db = Database::getInstance();

// Check if reset_token column exists
$columns = $db->query("SHOW COLUMNS FROM users LIKE 'reset_token'")->num_rows;
if ($columns == 0) {
    $error = 'Password reset system is not configured. Please contact administrator or run setup-password-reset.php';
    $validToken = false;
} else {
    // Verify token
    $stmt = $db->prepare("SELECT id, name FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $validToken = true;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
        
        if (empty($password) || empty($confirmPassword)) {
            $error = 'Please fill in all fields';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match';
        } else {
            // Verify Turnstile token
            $turnstileResult = verifyTurnstile($turnstileToken, $_SERVER['REMOTE_ADDR']);
            if (!$turnstileResult['success']) {
                $error = 'Security verification failed. Please try again.';
            } else {
            $hashedPassword = password_hash($password, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);
            $stmt = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $user['id']);
            
            if ($stmt->execute()) {
                redirect(SITE_URL . '/login.php?reset=1');
            } else {
                $error = 'Failed to reset password. Please try again.';
            }
            }
        }
    }
    } else {
        $error = 'Invalid or expired reset token. Please request a new password reset link.';
    }
}

$pageTitle = "Reset Password";
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 40vh; display: flex; align-items: center;">
    <div class="container text-center text-white">
        <div class="animate__animated animate__fadeInDown">
            <i class="fas fa-lock fa-4x mb-3" style="opacity: 0.9;"></i>
            <h1 class="display-4 fw-bold mb-3">Reset Your Password</h1>
            <p class="lead mb-0">Create a new secure password for your account</p>
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
                    
                    <?php if ($validToken): ?>
                        <form method="POST" action="">
                            <div class="form-outline mb-4">
                                <input type="password" id="password" name="password" class="form-control form-control-lg" required>
                                <label class="form-label" for="password">New Password</label>
                            </div>
                            
                            <div class="form-outline mb-4">
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       class="form-control form-control-lg" required>
                                <label class="form-label" for="confirm_password">Confirm New Password</label>
                            </div>
                            
                            <!-- Cloudflare Turnstile -->
                            <div class="mb-4 d-flex justify-content-center">
                                <?php echo getTurnstileWidget(); ?>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-4" style="border-radius: 10px; padding: 15px;">
                                <i class="fas fa-check me-2"></i>Reset Password
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="text-center">
                            <a href="<?php echo SITE_URL; ?>/forgot-password.php" class="btn btn-primary btn-lg" style="border-radius: 10px; padding: 15px;">
                                <i class="fas fa-redo me-2"></i>Request New Reset Link
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Security Info -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="row text-center g-3">
                            <div class="col-4">
                                <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                <p class="small mb-0 text-muted">Secure</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-key fa-2x text-primary mb-2"></i>
                                <p class="small mb-0 text-muted">Encrypted</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-check-circle fa-2x text-info mb-2"></i>
                                <p class="small mb-0 text-muted">Verified</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Password Tips -->
            <div class="card border-0 shadow-sm mt-4 animate__animated animate__fadeInUp" style="border-radius: 15px; animation-delay: 0.2s;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>Password Tips</h6>
                    <ul class="small text-muted mb-0">
                        <li>Use at least 8 characters</li>
                        <li>Include uppercase and lowercase letters</li>
                        <li>Add numbers and special characters</li>
                        <li>Avoid common words or patterns</li>
                    </ul>
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

/* Password strength indicator */
#password {
    transition: border-color 0.3s ease;
}
</style>

<script>
// Password strength indicator
document.getElementById('password')?.addEventListener('input', function() {
    const password = this.value;
    const strength = password.length >= 8 ? 'strong' : password.length >= 6 ? 'medium' : 'weak';
    
    if (password.length > 0) {
        if (strength === 'strong') {
            this.style.borderColor = '#28a745';
        } else if (strength === 'medium') {
            this.style.borderColor = '#ffc107';
        } else {
            this.style.borderColor = '#dc3545';
        }
    } else {
        this.style.borderColor = '';
    }
});

// Password match indicator
document.getElementById('confirm_password')?.addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword.length > 0) {
        if (password === confirmPassword) {
            this.style.borderColor = '#28a745';
        } else {
            this.style.borderColor = '#dc3545';
        }
    } else {
        this.style.borderColor = '';
    }
});

// Form animation on submit
document.querySelector('form')?.addEventListener('submit', function(e) {
    const button = this.querySelector('button[type="submit"]');
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Resetting Password...';
    button.disabled = true;
});
</script>

<?php require_once 'includes/footer.php'; ?>