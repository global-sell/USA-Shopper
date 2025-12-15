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
$total = $subtotal + $taxAmount;
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
    </h2>
    
    <?php if (empty($cartItems)): ?>
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h4 class="mb-3">Your cart is empty</h4>
                <p class="text-muted mb-4">Start adding products to your cart</p>
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>Browse Products
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($cartItems as $item): ?>
                            <?php 
                            $screenshots = json_decode($item['screenshots'], true);
                            $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/100x100/4CAF50/ffffff?text=Product';
                            ?>
                            <div class="row align-items-center mb-3 pb-3 border-bottom">
                                <div class="col-md-2 col-3">
                                    <img src="<?php echo $firstImage; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                         class="img-fluid rounded">
                                </div>
                                <div class="col-md-6 col-9">
                                    <h6 class="fw-bold mb-1">
                                        <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $item['id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($item['title']); ?>
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        <?php echo substr(htmlspecialchars($item['description']), 0, 100); ?>...
                                    </p>
                                </div>
                                <div class="col-md-2 col-6 text-md-center mt-2 mt-md-0">
                                    <span class="h6 fw-bold text-primary"><?php echo formatPrice($item['price']); ?></span>
                                </div>
                                <div class="col-md-2 col-6 text-md-end mt-2 mt-md-0">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal (<?php echo count($cartItems); ?> items)</span>
                            <span class="fw-bold"><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tax (<?php echo getSetting('tax_percentage', 0); ?>%)</span>
                            <span class="fw-bold"><?php echo formatPrice($taxAmount); ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="h5 mb-0">Total</span>
                            <span class="h5 mb-0 text-primary fw-bold"><?php echo formatPrice($total); ?></span>
                        </div>
                        
                        <a href="<?php echo SITE_URL; ?>/checkout.php" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-lock me-2"></i>Proceed to Checkout
                        </a>
                        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
