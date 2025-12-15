<?php
$pageTitle = "Order Tracking";
require_once 'includes/header.php';

$db = Database::getInstance();
$order = null;
$error = '';

// Handle order tracking
if (isset($_POST['track_order']) || isset($_GET['order'])) {
    $orderNumber = sanitizeInput($_POST['order_number'] ?? $_GET['order'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    
    if (!empty($orderNumber)) {
        if (isLoggedIn()) {
            // For logged-in users, check their orders
            $stmt = $db->prepare("SELECT o.*, u.name as user_name, u.email as user_email 
                                 FROM orders o 
                                 JOIN users u ON o.user_id = u.id 
                                 WHERE o.order_number = ? AND o.user_id = ?");
            $stmt->bind_param("si", $orderNumber, $_SESSION['user_id']);
        } else {
            // For guest users, require email
            if (empty($email)) {
                $error = 'Please enter your email address';
            } else {
                $stmt = $db->prepare("SELECT o.*, u.name as user_name, u.email as user_email 
                                     FROM orders o 
                                     JOIN users u ON o.user_id = u.id 
                                     WHERE o.order_number = ? AND u.email = ?");
                $stmt->bind_param("ss", $orderNumber, $email);
            }
        }
        
        if (!$error && isset($stmt)) {
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $order = $result->fetch_assoc();
                
                // Get order items
                $stmt = $db->prepare("SELECT oi.*, p.title, p.screenshots 
                                     FROM order_items oi 
                                     JOIN products p ON oi.product_id = p.id 
                                     WHERE oi.order_id = ?");
                $stmt->bind_param("i", $order['id']);
                $stmt->execute();
                $order['items'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } else {
                $error = 'Order not found. Please check your order number and email.';
            }
        }
    } else {
        $error = 'Please enter an order number';
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-map-marker-alt me-2"></i>Track Your Order
                </h1>
                <p class="lead text-muted">Enter your order details to track your shipment</p>
            </div>
            
            <!-- Tracking Form -->
            <?php if (!$order): ?>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="form-outline mb-4">
                                <input type="text" id="order_number" name="order_number" class="form-control form-control-lg" 
                                       value="<?php echo htmlspecialchars($_POST['order_number'] ?? ''); ?>" required>
                                <label class="form-label" for="order_number">Order Number</label>
                            </div>
                            
                            <?php if (!isLoggedIn()): ?>
                                <div class="form-outline mb-4">
                                    <input type="email" id="email" name="email" class="form-control form-control-lg" 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                    <label class="form-label" for="email">Email Address</label>
                                </div>
                            <?php endif; ?>
                            
                            <button type="submit" name="track_order" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-search me-2"></i>Track Order
                            </button>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                You can find your order number in the confirmation email
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Help Section -->
                <div class="card border-0 bg-light">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Need Help?</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Order number can be found in your confirmation email
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Use the email address you used during checkout
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <a href="<?php echo SITE_URL; ?>/contact.php">Contact us</a> if you need assistance
                            </li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <!-- Order Details -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-box me-2"></i>Order Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <strong>Order Number:</strong><br>
                                <span class="text-primary"><?php echo htmlspecialchars($order['order_number']); ?></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Order Date:</strong><br>
                                <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Customer:</strong><br>
                                <?php echo htmlspecialchars($order['user_name']); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Email:</strong><br>
                                <?php echo htmlspecialchars($order['user_email']); ?>
                            </div>
                        </div>
                        
                        <!-- Order Status -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Order Status</h6>
                            <div class="progress" style="height: 30px;">
                                <?php
                                $statusWidth = match($order['payment_status']) {
                                    'pending' => 25,
                                    'processing' => 50,
                                    'completed' => 100,
                                    'failed' => 0,
                                    'refunded' => 0,
                                    default => 0
                                };
                                $statusColor = match($order['payment_status']) {
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'completed' => 'success',
                                    'failed' => 'danger',
                                    'refunded' => 'secondary',
                                    default => 'secondary'
                                };
                                ?>
                                <div class="progress-bar bg-<?php echo $statusColor; ?>" 
                                     role="progressbar" 
                                     style="width: <?php echo $statusWidth; ?>%">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Items -->
                        <h6 class="fw-bold mb-3">Order Items</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                            </td>
                                            <td><?php echo formatPrice($item['price']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><strong>Subtotal:</strong></td>
                                        <td><?php echo formatPrice($order['subtotal']); ?></td>
                                    </tr>
                                    <?php if ($order['discount'] > 0): ?>
                                        <tr>
                                            <td><strong>Discount:</strong></td>
                                            <td class="text-success">-<?php echo formatPrice($order['discount']); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td><strong>Tax:</strong></td>
                                        <td><?php echo formatPrice($order['tax']); ?></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td><strong>Total:</strong></td>
                                        <td><strong><?php echo formatPrice($order['final_amount']); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <a href="?track_order" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Track Another Order
                            </a>
                            <?php if (isLoggedIn()): ?>
                                <a href="<?php echo SITE_URL; ?>/orders.php" class="btn btn-primary">
                                    <i class="fas fa-box me-2"></i>View All Orders
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
