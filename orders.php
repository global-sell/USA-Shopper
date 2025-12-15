<?php
$pageTitle = "My Orders";
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$db = Database::getInstance();

// Get user orders
$stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-box me-2"></i>My Orders & Downloads
    </h2>
    
    <?php if (empty($orders)): ?>
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4 class="mb-3">No orders yet</h4>
                <p class="text-muted mb-4">Start shopping to see your orders here</p>
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>Browse Products
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php
            // Get order items
            $stmt = $db->prepare("SELECT oi.*, p.id as product_id FROM order_items oi 
                                 LEFT JOIN products p ON oi.product_id = p.id 
                                 WHERE oi.order_id = ?");
            $stmt->bind_param("i", $order['id']);
            $stmt->execute();
            $orderItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            $statusClass = match($order['payment_status']) {
                'completed' => 'success',
                'pending' => 'warning',
                'failed' => 'danger',
                'refunded' => 'info',
                default => 'secondary'
            };
            ?>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">
                                Order #<?php echo htmlspecialchars($order['order_number']); ?>
                            </h5>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?>
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                            <span class="badge bg-<?php echo $statusClass; ?> me-2">
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                            <span class="h5 mb-0 text-primary fw-bold">
                                <?php echo formatPrice($order['final_amount']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php foreach ($orderItems as $item): ?>
                        <?php
                        // Get download info
                        $downloadStmt = $db->prepare("SELECT * FROM downloads 
                                                      WHERE user_id = ? AND product_id = ? AND order_id = ?");
                        $downloadStmt->bind_param("iii", $_SESSION['user_id'], $item['product_id'], $order['id']);
                        $downloadStmt->execute();
                        $download = $downloadStmt->get_result()->fetch_assoc();
                        ?>
                        
                        <div class="row align-items-center mb-3 pb-3 border-bottom">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['product_title']); ?></h6>
                                <p class="text-muted small mb-0">
                                    Price: <?php echo formatPrice($item['price']); ?>
                                </p>
                            </div>
                            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                <?php if ($order['payment_status'] === 'completed' && $download): ?>
                                    <?php if ($download['download_count'] < $download['max_downloads']): ?>
                                        <a href="<?php echo SITE_URL; ?>/download.php?token=<?php echo $download['download_token']; ?>" 
                                           class="btn btn-sm btn-primary me-2">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                        <small class="text-muted">
                                            <?php echo $download['download_count']; ?>/<?php echo $download['max_downloads']; ?> downloads
                                        </small>
                                    <?php else: ?>
                                        <span class="text-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Download limit reached
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Not available</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Order Summary -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <?php if ($order['coupon_code']): ?>
                                <small class="text-muted">
                                    <i class="fas fa-tag me-1"></i>Coupon: <strong><?php echo htmlspecialchars($order['coupon_code']); ?></strong>
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small class="text-muted">
                                Payment: <?php echo ucfirst($order['payment_method']); ?>
                                <?php if ($order['transaction_id']): ?>
                                    | TXN: <?php echo htmlspecialchars($order['transaction_id']); ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
