<?php
require_once 'config/config.php';
require_once 'config/google-oauth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
    
    if (empty($usernameOrEmail) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        // Verify Turnstile token
        $turnstileResult = verifyTurnstile($turnstileToken, $_SERVER['REMOTE_ADDR']);
        if (!$turnstileResult['success']) {
            $error = 'Security verification failed. Please try again.';
        } else {
        $db = Database::getInstance();
        
        // Check if input is email or username
        if (validateEmail($usernameOrEmail)) {
            // Login with email
            $stmt = $db->prepare("SELECT id, name, email, username, password, role, status FROM users WHERE email = ?");
        } else {
            // Login with username
            $stmt = $db->prepare("SELECT id, name, email, username, password, role, status FROM users WHERE username = ?");
        }
        
        $stmt->bind_param("s", $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if ($user['status'] === 'blocked') {
                $error = 'Your account has been blocked. Please contact support.';
            } elseif (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_username'] = $user['username'] ?? '';
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();
                
                // Redirect to intended page or home
                $redirect_url = $_GET['redirect'] ?? SITE_URL;
                redirect($redirect_url);
            } else {
                $error = 'Invalid username/email or password';
            }
        } else {
            $error = 'Invalid username/email or password';
        }
        }
    }
}

$pageTitle = "Login";
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 40vh; display: flex; align-items: center;">
    <div class="container text-center text-white">
        <div class="animate__animated animate__fadeInDown">
            <i class="fas fa-user-circle fa-4x mb-3" style="opacity: 0.9;"></i>
            <h1 class="display-4 fw-bold mb-3">Welcome Back</h1>
            <p class="lead mb-0">Login to continue your shopping journey</p>
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
                    
                    <?php if (isset($_GET['registered'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>Registration successful! Please login.
                            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['reset'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>Password reset successful! Please login with your new password.
                            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-outline mb-3">
                            <input type="text" id="email" name="email" class="form-control form-control-lg" 
                                   value="<?php echo htmlspecialchars($usernameOrEmail ?? ''); ?>" required>
                            <label class="form-label" for="email">Username or Email</label>
                        </div>
                        
                        <div class="form-outline mb-3 position-relative">
                            <input type="password" id="password" name="password" class="form-control form-control-lg" required style="padding-right: 45px;">
                            <label class="form-label" for="password">Password</label>
                            <button type="button" class="btn btn-link password-toggle position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); z-index: 10; text-decoration: none;" onclick="togglePassword('password')">
                                <i class="fas fa-eye-slash" id="password-icon"></i>
                            </button>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                            <a href="<?php echo SITE_URL; ?>/forgot-password.php" class="text-primary">Forgot password?</a>
                        </div>
                        
                        <!-- Cloudflare Turnstile -->
                        <div class="mb-4 d-flex justify-content-center">
                            <?php echo getTurnstileWidget(); ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" style="border-radius: 10px; padding: 15px;">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Account
                        </button>
                    </form>
                    
                    <!-- Divider -->
                    <div class="d-flex align-items-center my-4">
                        <hr class="flex-grow-1">
                        <span class="px-3 text-muted">OR</span>
                        <hr class="flex-grow-1">
                    </div>
                    
                    <!-- Google Login Button -->
                    <a href="<?php echo getGoogleLoginUrl(); ?>" class="btn btn-outline-dark btn-lg w-100 mb-4 d-flex align-items-center justify-content-center" style="border-radius: 10px; padding: 15px;">
                        <svg width="20" height="20" class="me-3" viewBox="0 0 48 48">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                            <path fill="none" d="M0 0h48v48H0z"></path>
                        </svg>
                        Continue with Google
                    </a>
                    
                    <div class="text-center">
                        <p class="mb-0 text-muted">Don't have an account? 
                            <a href="<?php echo SITE_URL; ?>/signup.php" class="text-primary fw-bold text-decoration-none">Create one now</a>
                        </p>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="row text-center g-3">
                            <div class="col-4">
                                <i class="fas fa-lock fa-2x text-primary mb-2"></i>
                                <p class="small mb-0 text-muted">Secure</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                <p class="small mb-0 text-muted">Protected</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-user-check fa-2x text-info mb-2"></i>
                                <p class="small mb-0 text-muted">Verified</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Info Cards -->
            <div class="row g-3 mt-4">
                <div class="col-md-6">
                    <a href="<?php echo SITE_URL; ?>/products.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInLeft" style="border-radius: 15px; animation-delay: 0.3s;">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-shopping-bag fa-3x text-primary mb-3"></i>
                                <h6 class="fw-bold">Shop Now</h6>
                                <p class="small text-muted mb-0">Browse thousands of products</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="<?php echo SITE_URL; ?>/products.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInRight" style="border-radius: 15px; animation-delay: 0.3s;">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-tags fa-3x text-success mb-3"></i>
                                <h6 class="fw-bold">Best Deals</h6>
                                <p class="small text-muted mb-0">Save more with exclusive offers</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
/* Fix autofill styling */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 30px white inset !important;
    -webkit-text-fill-color: #000 !important;
    transition: background-color 5000s ease-in-out 0s;
}

/* Fix floating labels with autofill */
.form-outline input:-webkit-autofill ~ label,
.form-outline input:not(:placeholder-shown) ~ label {
    transform: translateY(-1rem) translateY(0.1rem) scale(0.8);
    background: white;
    padding: 0 0.5rem;
}

/* Ensure labels are properly positioned */
.form-outline {
    position: relative;
}

.form-outline label {
    position: absolute;
    top: 0.5rem;
    left: 0.75rem;
    transition: all 0.2s ease;
    pointer-events: none;
    color: #6c757d;
    z-index: 1;
}

/* Active label state */
.form-outline label.active,
.form-outline input:focus ~ label,
.form-outline input:not(:placeholder-shown) ~ label,
.form-outline input.active ~ label {
    transform: translateY(-1.5rem) scale(0.85);
    background: white;
    padding: 0 0.5rem;
    color: #667eea;
}

.form-outline input {
    padding: 0.75rem;
}

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

.btn-outline-primary {
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    transform: scale(1.1);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
    color: white;
}

.card {
    transition: transform 0.3s ease;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.animate__animated {
    animation-duration: 0.8s;
}

a.text-primary:hover {
    color: #764ba2 !important;
}

/* Password Toggle Button */
.password-toggle {
    color: #6c757d;
    padding: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.password-toggle:hover {
    color: #667eea;
    transform: translateY(-50%) scale(1.1);
}

.password-toggle i {
    transition: all 0.4s ease;
}

.password-toggle.active i {
    animation: eyeBlink 0.3s ease;
}

@keyframes eyeBlink {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(0.8); }
}

/* Visual Effects */
/* Animated gradient background */
body {
    position: relative;
    overflow-x: hidden;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    opacity: 0.03;
    z-index: -1;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Floating shapes */
.floating-shapes {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    overflow: hidden;
    z-index: -1;
    pointer-events: none;
}

.shape {
    position: absolute;
    opacity: 0.1;
    animation: float 20s infinite ease-in-out;
}

.shape-1 {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.shape-2 {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #764ba2, #667eea);
    border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
    top: 70%;
    left: 80%;
    animation-delay: 2s;
}

.shape-3 {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    top: 40%;
    left: 85%;
    animation-delay: 4s;
}

.shape-4 {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #764ba2, #667eea);
    border-radius: 20% 80% 80% 20% / 20% 20% 80% 80%;
    top: 80%;
    left: 15%;
    animation-delay: 6s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-30px) rotate(180deg);
    }
}

/* Card entrance animation */
.card {
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Input focus glow effect */
.form-outline input:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25),
                0 0 20px rgba(102, 126, 234, 0.2);
    border-color: #667eea;
    animation: inputGlow 0.3s ease;
}

@keyframes inputGlow {
    0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
    100% { box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25), 0 0 20px rgba(102, 126, 234, 0.2); }
}

/* Button ripple effect */
.btn-primary {
    position: relative;
    overflow: hidden;
}

.btn-primary::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-primary:hover::after {
    width: 300px;
    height: 300px;
}

/* Label animation */
.form-outline label {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-outline input:focus ~ label,
.form-outline label.active {
    animation: labelFloat 0.3s ease;
}

@keyframes labelFloat {
    0% {
        transform: translateY(0) scale(1);
    }
    100% {
        transform: translateY(-1.5rem) scale(0.85);
    }
}

/* Card hover lift effect */
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
}

/* Hero section pulse */
.animate__animated {
    animation-duration: 1s;
}

/* Icon pulse on hover */
.fas:hover {
    animation: iconPulse 0.5s ease;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Alert slide in */
.alert {
    animation: slideInDown 0.4s ease;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Feature Cards Active Effects */
.row.g-3.mt-4 .card {
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    background: #fff;
    position: relative;
    overflow: hidden;
}

.row.g-3.mt-4 .card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 50px rgba(102, 126, 234, 0.25) !important;
    border-color: #667eea;
}

.row.g-3.mt-4 .card:active {
    transform: translateY(-8px) scale(1.01);
}

.row.g-3.mt-4 .card i {
    transition: all 0.4s ease;
}

.row.g-3.mt-4 .card:hover i {
    transform: scale(1.2) rotateY(180deg);
}

.row.g-3.mt-4 .card h6 {
    transition: color 0.3s ease;
}

.row.g-3.mt-4 .card:hover h6 {
    color: #667eea;
}

.row.g-3.mt-4 .card p {
    transition: color 0.3s ease;
}

.row.g-3.mt-4 .card:hover p {
    color: #764ba2;
}

/* Add shimmer effect on hover */
.row.g-3.mt-4 .card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.2), transparent);
    transition: left 0.5s;
}

.row.g-3.mt-4 .card:hover::before {
    left: 100%;
}

/* Icon container glow */
.row.g-3.mt-4 .card:hover .fa-lock,
.row.g-3.mt-4 .card:hover .fa-shield-alt,
.row.g-3.mt-4 .card:hover .fa-bolt,
.row.g-3.mt-4 .card:hover .fa-shopping-bag,
.row.g-3.mt-4 .card:hover .fa-tags {
    text-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
}

/* Link styling for cards */
.row.g-3.mt-4 a {
    display: block;
    text-decoration: none !important;
}

.row.g-3.mt-4 a .card {
    transition: all 0.4s ease;
}
</style>

<!-- Floating Shapes -->
<div class="floating-shapes">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <div class="shape shape-4"></div>
</div>

<script>
// Password Toggle Function
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    const button = icon.closest('.password-toggle');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        button.classList.add('active');
        
        // Animation
        setTimeout(() => button.classList.remove('active'), 300);
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        button.classList.add('active');
        
        // Animation
        setTimeout(() => button.classList.remove('active'), 300);
    }
}

// Initialize Material Design form inputs
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all form inputs with MDB
    const inputs = document.querySelectorAll('.form-outline input');
    inputs.forEach(input => {
        // Check if input has value (including autofill)
        function checkInput() {
            if (input.value !== '' || input.matches(':autofill')) {
                input.classList.add('active');
                const label = input.nextElementSibling;
                if (label && label.classList.contains('form-label')) {
                    label.classList.add('active');
                }
            }
        }
        
        // Check on load
        checkInput();
        
        // Check on input change
        input.addEventListener('input', checkInput);
        input.addEventListener('change', checkInput);
        
        // Handle focus
        input.addEventListener('focus', function() {
            const label = this.nextElementSibling;
            if (label && label.classList.contains('form-label')) {
                label.classList.add('active');
            }
        });
        
        // Handle blur
        input.addEventListener('blur', function() {
            if (this.value === '' && !this.matches(':autofill')) {
                const label = this.nextElementSibling;
                if (label && label.classList.contains('form-label')) {
                    label.classList.remove('active');
                }
            }
        });
    });
    
    // Check for autofill after a delay
    setTimeout(() => {
        inputs.forEach(input => {
            if (input.matches(':autofill') || input.value !== '') {
                input.classList.add('active');
                const label = input.nextElementSibling;
                if (label && label.classList.contains('form-label')) {
                    label.classList.add('active');
                }
            }
        });
    }, 100);
});

// Form animation on submit
document.querySelector('form')?.addEventListener('submit', function(e) {
    const button = this.querySelector('button[type="submit"]');
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging in...';
    button.disabled = true;
});

// Auto-hide success messages
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);

</script>

<?php require_once 'includes/footer.php'; ?>