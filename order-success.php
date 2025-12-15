<?php
$pageTitle = "Order Success";
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php');
}

$orderNumber = sanitizeInput($_GET['order'] ?? '');

if (empty($orderNumber)) {
    redirect(SITE_URL);
}

$db = Database::getInstance();
$stmt = $db->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ?");
$stmt->bind_param("si", $orderNumber, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    redirect(SITE_URL);
}

// Get order items
$stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $order['id']);
$stmt->execute();
$orderItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    
                    <h2 class="fw-bold mb-3">Order Placed Successfully!</h2>
                    <p class="lead text-muted mb-4">Thank you for your purchase</p>
                    
                    <div class="alert alert-success">
                        <h5 class="mb-2">Order Number: <strong><?php echo htmlspecialchars($orderNumber); ?></strong></h5>
                        <p class="mb-0">A confirmation email has been sent to your email address</p>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <?php foreach ($orderItems as $item): ?>
                                            <tr>
                                                <td class="text-start"><?php echo htmlspecialchars($item['product_title']); ?></td>
                                                <td class="text-end fw-bold"><?php echo formatPrice($item['price']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if ($order['discount_amount'] > 0): ?>
                                            <tr class="text-success">
                                                <td class="text-start">Discount</td>
                                                <td class="text-end fw-bold">-<?php echo formatPrice($order['discount_amount']); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td class="text-start">Tax</td>
                                            <td class="text-end fw-bold"><?php echo formatPrice($order['tax_amount']); ?></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="text-start"><strong>Total</strong></td>
                                            <td class="text-end"><strong class="text-primary h5"><?php echo formatPrice($order['final_amount']); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="<?php echo SITE_URL; ?>/orders.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-download me-2"></i>Download Your Products
                        </a>
                        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline-secondary">
                            <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
