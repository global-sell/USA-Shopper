<?php
$pageTitle = "Shopping Cart";
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$db = Database::getInstance();

// Handle remove from cart
if (isset($_POST['remove_item'])) {
    $cartId = (int)$_POST['cart_id'];
    $stmt = $db->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cartId, $_SESSION['user_id']);
    $stmt->execute();
    redirect(SITE_URL . '/cart.php');
}

// Get cart items
$stmt = $db->prepare("SELECT c.id as cart_id, p.* FROM cart c 
                     JOIN products p ON c.product_id = p.id 
                     WHERE c.user_id = ? AND p.status = 'active'
                     ORDER BY c.added_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'];
}

$taxRate = (float)getSetting('tax_percentage', 0) / 100;
$taxAmount = $subtotal * $taxRate;
$shipping = 0; // Free shipping
$total = $subtotal + $taxAmount + $shipping;
?>

<style>
.cart-item-card {
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
}

.cart-item-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.cart-item-image {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
}

.price-tag {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1976d2;
}

.summary-card {
    position: sticky;
    top: 20px;
    border: 2px solid #e0e0e0;
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e0e0e0;
    z-index: 0;
}

.progress-step {
    position: relative;
    z-index: 1;
    text-align: center;
    flex: 1;
}

.progress-step-circle {
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

.progress-step.active .progress-step-circle {
    background: #1976d2;
    border-color: #1976d2;
    color: white;
}

.progress-step.completed .progress-step-circle {
    background: #4caf50;
    border-color: #4caf50;
    color: white;
}

@media (max-width: 768px) {
    .cart-item-image {
        width: 80px;
        height: 80px;
    }
    
    .progress-steps {
        font-size: 0.85rem;
    }
}
</style>

<div class="container py-4">
    <!-- Progress Steps -->
    <div class="progress-steps mb-5">
        <div class="progress-step active">
            <div class="progress-step-circle">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <small class="fw-bold">Shopping Cart</small>
        </div>
        <div class="progress-step">
            <div class="progress-step-circle">
                <i class="fas fa-credit-card"></i>
            </div>
            <small>Checkout</small>
        </div>
        <div class="progress-step">
            <div class="progress-step-circle">
                <i class="fas fa-check"></i>
            </div>
            <small>Order Complete</small>
        </div>
    </div>
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
            <?php if (!empty($cartItems)): ?>
                <span class="badge bg-primary"><?php echo count($cartItems); ?></span>
            <?php endif; ?>
        </h2>
        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
        </a>
    </div>
    
    <?php if (empty($cartItems)): ?>
        <!-- Empty Cart -->
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <div class="mb-4">
                    <i class="fas fa-shopping-cart fa-5x text-muted"></i>
                </div>
                <h3 class="fw-bold mb-3">Your Cart is Empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                </a>
            </div>
        </div>
        
        <!-- Suggested Products -->
        <div class="mt-5">
            <h4 class="fw-bold mb-4">You May Also Like</h4>
            <div class="row g-3">
                <?php
                $suggested = $db->query("SELECT * FROM products WHERE status = 'active' ORDER BY RAND() LIMIT 4")->fetch_all(MYSQLI_ASSOC);
                foreach ($suggested as $product):
                    $screenshots = json_decode($product['screenshots'], true);
                    $image = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/200x200/4CAF50/ffffff?text=Product';
                ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                <img src="<?php echo $image; ?>" class="card-img-top" style="height: 150px; object-fit: cover; cursor: pointer;" alt="<?php echo htmlspecialchars($product['title']); ?>">
                            </a>
                            <div class="card-body">
                                <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($product['title']); ?></h6>
                                <p class="text-primary fw-bold mb-2"><?php echo formatPrice($product['price']); ?></p>
                                <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8 mb-4">
                <!-- Free Shipping Banner -->
                <div class="alert alert-success border-0 shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-truck fa-2x me-3"></i>
                        <div>
                            <strong>Free Shipping!</strong>
                            <p class="mb-0 small">Your order qualifies for free shipping</p>
                        </div>
                    </div>
                </div>
                
                <!-- Cart Items List -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">Cart Items (<?php echo count($cartItems); ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($cartItems as $index => $item): ?>
                            <?php 
                            $screenshots = json_decode($item['screenshots'], true);
                            $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/120x120/4CAF50/ffffff?text=Product';
                            ?>
                            <div class="cart-item-card p-4 <?php echo $index < count($cartItems) - 1 ? 'border-bottom' : ''; ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-2 col-3 mb-3 mb-md-0">
                                        <img src="<?php echo $firstImage; ?>" 
                                             alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                             class="cart-item-image w-100"
                                             onerror="this.src='https://via.placeholder.com/120x120/4CAF50/ffffff?text=Product'">
                                    </div>
                                    <div class="col-md-5 col-9 mb-3 mb-md-0">
                                        <h6 class="fw-bold mb-2">
                                            <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $item['id']; ?>" 
                                               class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars($item['title']); ?>
                                            </a>
                                        </h6>
                                        <p class="text-muted small mb-2">
                                            <?php echo substr(htmlspecialchars($item['description']), 0, 80); ?>...
                                        </p>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-shopping-bag me-1"></i><?php echo $item['sold']; ?> sold
                                            </span>
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-star text-warning me-1"></i>4.5
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 text-center mb-3 mb-md-0">
                                        <div class="price-tag"><?php echo formatPrice($item['price']); ?></div>
                                        <small class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>In Stock
                                        </small>
                                    </div>
                                    <div class="col-md-2 col-6 text-center">
                                        <form method="POST" onsubmit="return confirm('Remove this item from cart?');">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <button type="submit" name="remove_item" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </form>
                                        <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $item['id']; ?>" 
                                           class="btn btn-link btn-sm text-decoration-none mt-2">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Coupon Code -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-tag me-2"></i>Have a Coupon Code?
                        </h6>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Enter coupon code">
                            <button class="btn btn-outline-primary" type="button">
                                <i class="fas fa-check me-2"></i>Apply
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card summary-card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-receipt me-2"></i>Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal (<?php echo count($cartItems); ?> items)</span>
                            <span class="fw-bold"><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Shipping</span>
                            <span class="fw-bold text-success">FREE</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tax (<?php echo getSetting('tax_percentage', 0); ?>%)</span>
                            <span class="fw-bold"><?php echo formatPrice($taxAmount); ?></span>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 mb-0 fw-bold">Total</span>
                            <span class="h4 mb-0 text-primary fw-bold"><?php echo formatPrice($total); ?></span>
                        </div>
                        
                        <!-- Trust Badges -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-shield-alt text-success me-2"></i>
                                <small>Secure Checkout</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-lock text-success me-2"></i>
                                <small>SSL Encrypted</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-undo text-success me-2"></i>
                                <small>30-Day Money Back</small>
                            </div>
                        </div>
                        
                        <a href="<?php echo SITE_URL; ?>/checkout.php" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-lock me-2"></i>Proceed to Checkout
                        </a>
                        
                        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                        
                        <!-- Payment Methods -->
                        <div class="mt-4 text-center">
                            <small class="text-muted d-block mb-2">We Accept</small>
                            <div class="d-flex justify-content-center gap-2">
                                <i class="fab fa-cc-visa fa-2x text-primary"></i>
                                <i class="fab fa-cc-mastercard fa-2x text-warning"></i>
                                <i class="fab fa-cc-amex fa-2x text-info"></i>
                                <i class="fab fa-cc-paypal fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Help Card -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body text-center">
                        <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                        <h6 class="fw-bold mb-2">Need Help?</h6>
                        <p class="small text-muted mb-3">Our customer service team is here to help</p>
                        <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
