<?php
$pageTitle = "Checkout";
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$db = Database::getInstance();

// Get cart items
$stmt = $db->prepare("SELECT c.id as cart_id, p.* FROM cart c 
                     JOIN products p ON c.product_id = p.id 
                     WHERE c.user_id = ? AND p.status = 'active'");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($cartItems)) {
    redirect(SITE_URL . '/cart.php');
}

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'];
}

$discount = 0;
$couponCode = '';
$couponError = '';

// Handle coupon application
if (isset($_POST['apply_coupon'])) {
    $couponCode = sanitizeInput($_POST['coupon_code'] ?? '');
    
    if (!empty($couponCode)) {
        $stmt = $db->prepare("SELECT * FROM coupons 
                             WHERE code = ? AND status = 'active' 
                             AND (expires_at IS NULL OR expires_at > NOW())
                             AND (max_usage IS NULL OR used_count < max_usage)");
        $stmt->bind_param("s", $couponCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($coupon = $result->fetch_assoc()) {
            if ($subtotal >= $coupon['min_purchase']) {
                if ($coupon['discount_type'] === 'flat') {
                    $discount = $coupon['discount_value'];
                } else {
                    $discount = ($subtotal * $coupon['discount_value']) / 100;
                }
                $_SESSION['applied_coupon'] = $couponCode;
            } else {
                $couponError = 'Minimum purchase amount is ' . formatPrice($coupon['min_purchase']);
            }
        } else {
            $couponError = 'Invalid or expired coupon code';
        }
    }
}

// Remove coupon
if (isset($_POST['remove_coupon'])) {
    unset($_SESSION['applied_coupon']);
    $couponCode = '';
    $discount = 0;
}

// Apply saved coupon
if (isset($_SESSION['applied_coupon']) && empty($couponCode)) {
    $couponCode = $_SESSION['applied_coupon'];
    $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active'");
    $stmt->bind_param("s", $couponCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($coupon = $result->fetch_assoc()) {
        if ($coupon['discount_type'] === 'flat') {
            $discount = $coupon['discount_value'];
        } else {
            $discount = ($subtotal * $coupon['discount_value']) / 100;
        }
    }
}

$taxRate = (float)getSetting('tax_percentage', 0) / 100;
$taxAmount = ($subtotal - $discount) * $taxRate;
$shipping = 0; // Free shipping
$total = $subtotal - $discount + $taxAmount + $shipping;

// Get payment gateway settings
$paymentGateway = getSetting('payment_gateway', 'razorpay');
$currentUser = getCurrentUser();
?>

<style>
.checkout-progress {
    display: flex;
    justify-content: space-between;
    margin-bottom: 3rem;
    position: relative;
}

.checkout-progress::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e0e0e0;
    z-index: 0;
}

.checkout-step {
    position: relative;
    z-index: 1;
    text-align: center;
    flex: 1;
}

.checkout-step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-weight: bold;
}

.checkout-step.active .checkout-step-circle {
    background: #1976d2;
    border-color: #1976d2;
    color: white;
}

.checkout-step.completed .checkout-step-circle {
    background: #4caf50;
    border-color: #4caf50;
    color: white;
}

.order-item-card {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.order-item-card:hover {
    border-left-color: #1976d2;
    background: #f8f9fa;
}

.summary-card {
    position: sticky;
    top: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .checkout-progress {
        font-size: 0.85rem;
    }
}
</style>

<div class="container py-4">
    <!-- Progress Steps -->
    <div class="checkout-progress">
        <div class="checkout-step completed">
            <div class="checkout-step-circle">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <small class="fw-bold">Cart</small>
        </div>
        <div class="checkout-step active">
            <div class="checkout-step-circle">
                <i class="fas fa-credit-card"></i>
            </div>
            <small class="fw-bold">Checkout</small>
        </div>
        <div class="checkout-step">
            <div class="checkout-step-circle">
                <i class="fas fa-check"></i>
            </div>
            <small>Complete</small>
        </div>
    </div>
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="fas fa-credit-card me-2"></i>Secure Checkout
        </h2>
        <a href="<?php echo SITE_URL; ?>/cart.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Cart
        </a>
    </div>
    
    <div class="row">
        <!-- Left Column - Order Details -->
        <div class="col-lg-8 mb-4">
            <!-- Billing Information -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-user me-2 text-primary"></i>Billing Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Full Name</label>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($currentUser['name']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Email Address</label>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($currentUser['email']); ?></p>
                        </div>
                    </div>
                    <div class="alert alert-info border-0 mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Order confirmation will be sent to this email address</small>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-shopping-bag me-2 text-primary"></i>Order Items 
                        <span class="badge bg-primary"><?php echo count($cartItems); ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php foreach ($cartItems as $index => $item): ?>
                        <?php 
                        $screenshots = json_decode($item['screenshots'], true);
                        $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/100x100/4CAF50/ffffff?text=Product';
                        ?>
                        <div class="order-item-card p-3 <?php echo $index < count($cartItems) - 1 ? 'border-bottom' : ''; ?>">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $firstImage; ?>" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                     class="rounded me-3" 
                                     style="width: 80px; height: 80px; object-fit: cover;"
                                     onerror="this.src='https://via.placeholder.com/80x80/4CAF50/ffffff?text=Product'">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['title']); ?></h6>
                                    <p class="text-muted small mb-2">
                                        <?php echo substr(htmlspecialchars($item['description']), 0, 80); ?>...
                                    </p>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-download me-1"></i>Digital Product
                                    </span>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0 text-primary fw-bold"><?php echo formatPrice($item['price']); ?></div>
                                    <small class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>In Stock
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-credit-card me-2 text-primary"></i>Payment Method
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success border-0 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt fa-2x me-3"></i>
                            <div>
                                <strong>Secure Payment</strong>
                                <p class="mb-0 small">Payment Gateway: <strong><?php echo ucfirst($paymentGateway); ?></strong></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-3">
                            <i class="fab fa-cc-visa fa-3x text-primary"></i>
                        </div>
                        <div class="col-3">
                            <i class="fab fa-cc-mastercard fa-3x text-warning"></i>
                        </div>
                        <div class="col-3">
                            <i class="fab fa-cc-amex fa-3x text-info"></i>
                        </div>
                        <div class="col-3">
                            <i class="fab fa-cc-paypal fa-3x text-primary"></i>
                        </div>
                    </div>
                    
                    <div class="mt-3 p-3 bg-light rounded">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-lock text-success me-2"></i>
                            <small>256-bit SSL Encrypted</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            <small>PCI DSS Compliant</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Order Summary -->
        <div class="col-lg-4">
            <div class="card summary-card border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-receipt me-2"></i>Order Summary
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Coupon Code -->
                    <form method="POST" class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-tag me-2"></i>Have a Coupon?
                        </label>
                        <div class="input-group">
                            <input type="text" name="coupon_code" class="form-control" 
                                   placeholder="Enter code" value="<?php echo htmlspecialchars($couponCode); ?>"
                                   <?php echo !empty($couponCode) ? 'readonly' : ''; ?>>
                            <?php if (empty($couponCode)): ?>
                                <button type="submit" name="apply_coupon" class="btn btn-outline-primary">
                                    <i class="fas fa-check me-1"></i>Apply
                                </button>
                            <?php else: ?>
                                <button type="submit" name="remove_coupon" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-1"></i>Remove
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php if ($couponError): ?>
                            <small class="text-danger">
                                <i class="fas fa-exclamation-circle me-1"></i><?php echo $couponError; ?>
                            </small>
                        <?php elseif (!empty($couponCode) && $discount > 0): ?>
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i>Coupon applied successfully!
                            </small>
                        <?php endif; ?>
                    </form>
                    
                    <hr>
                    
                    <!-- Price Breakdown -->
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal (<?php echo count($cartItems); ?> items)</span>
                        <span class="fw-bold"><?php echo formatPrice($subtotal); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Shipping</span>
                        <span class="fw-bold text-success">FREE</span>
                    </div>
                    
                    <?php if ($discount > 0): ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-success">
                                <i class="fas fa-tag me-1"></i>Discount
                            </span>
                            <span class="fw-bold text-success">-<?php echo formatPrice($discount); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Tax (<?php echo getSetting('tax_percentage', 0); ?>%)</span>
                        <span class="fw-bold"><?php echo formatPrice($taxAmount); ?></span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 mb-0 fw-bold">Total</span>
                        <span class="h4 mb-0 text-primary fw-bold"><?php echo formatPrice($total); ?></span>
                    </div>
                    
                    <!-- Place Order Button -->
                    <form method="POST" action="<?php echo SITE_URL; ?>/process-payment.php">
                        <input type="hidden" name="coupon_code" value="<?php echo htmlspecialchars($couponCode); ?>">
                        <button type="submit" class="btn btn-success btn-lg w-100 mb-3">
                            <i class="fas fa-lock me-2"></i>Place Order Securely
                        </button>
                    </form>
                    
                    <a href="<?php echo SITE_URL; ?>/cart.php" class="btn btn-outline-secondary w-100 mb-3">
                        <i class="fas fa-arrow-left me-2"></i>Back to Cart
                    </a>
                    
                    <!-- Trust Badges -->
                    <div class="mt-4 p-3 bg-light rounded text-center">
                        <small class="text-muted d-block mb-2">
                            <i class="fas fa-shield-alt me-1"></i>100% Secure Checkout
                        </small>
                        <small class="text-muted d-block">
                            <i class="fas fa-undo me-1"></i>30-Day Money Back Guarantee
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
