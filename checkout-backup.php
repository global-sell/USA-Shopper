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
$total = $subtotal - $discount + $taxAmount;

// Get payment gateway settings
$paymentGateway = getSetting('payment_gateway', 'razorpay');
$currentUser = getCurrentUser();
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-credit-card me-2"></i>Checkout
    </h2>
    
    <div class="row">
        <!-- Order Details -->
        <div class="col-lg-8 mb-4">
            <!-- Billing Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Billing Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <p class="mb-0"><?php echo htmlspecialchars($currentUser['name']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <p class="mb-0"><?php echo htmlspecialchars($currentUser['email']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Order Items</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <?php 
                            $screenshots = json_decode($item['screenshots'], true);
                            $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/80x80/4CAF50/ffffff?text=Product';
                            ?>
                            <img src="<?php echo $firstImage; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                 class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['title']); ?></h6>
                                <p class="text-muted small mb-0">
                                    <?php echo substr(htmlspecialchars($item['description']), 0, 80); ?>...
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-primary"><?php echo formatPrice($item['price']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Method</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Payment Gateway: <strong><?php echo ucfirst($paymentGateway); ?></strong>
                    </div>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-lock me-2"></i>Your payment information is secure and encrypted
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                </div>
                <div class="card-body">
                    <!-- Coupon Code -->
                    <form method="POST" class="mb-3">
                        <label class="form-label fw-bold">Coupon Code</label>
                        <div class="input-group">
                            <input type="text" name="coupon_code" class="form-control" 
                                   placeholder="Enter code" value="<?php echo htmlspecialchars($couponCode); ?>"
                                   <?php echo !empty($couponCode) ? 'readonly' : ''; ?>>
                            <?php if (empty($couponCode)): ?>
                                <button type="submit" name="apply_coupon" class="btn btn-outline-primary">Apply</button>
                            <?php else: ?>
                                <button type="submit" name="remove_coupon" class="btn btn-outline-danger">Remove</button>
                            <?php endif; ?>
                        </div>
                        <?php if ($couponError): ?>
                            <small class="text-danger"><?php echo $couponError; ?></small>
                        <?php elseif (!empty($couponCode) && $discount > 0): ?>
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i>Coupon applied successfully!
                            </small>
                        <?php endif; ?>
                    </form>
                    
                    <hr>
                    
                    <!-- Price Breakdown -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span class="fw-bold"><?php echo formatPrice($subtotal); ?></span>
                    </div>
                    
                    <?php if ($discount > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount</span>
                            <span class="fw-bold">-<?php echo formatPrice($discount); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tax (<?php echo getSetting('tax_percentage', 0); ?>%)</span>
                        <span class="fw-bold"><?php echo formatPrice($taxAmount); ?></span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 mb-0">Total</span>
                        <span class="h5 mb-0 text-primary fw-bold"><?php echo formatPrice($total); ?></span>
                    </div>
                    
                    <form method="POST" action="<?php echo SITE_URL; ?>/process-payment.php">
                        <input type="hidden" name="coupon_code" value="<?php echo htmlspecialchars($couponCode); ?>">
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                            <i class="fas fa-lock me-2"></i>Place Order
                        </button>
                    </form>
                    
                    <a href="<?php echo SITE_URL; ?>/cart.php" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
