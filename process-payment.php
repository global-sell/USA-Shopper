<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/cart.php');
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
$couponCode = sanitizeInput($_POST['coupon_code'] ?? '');

// Apply coupon if provided
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
        }
    }
}

$taxRate = (float)getSetting('tax_percentage', 0) / 100;
$taxAmount = ($subtotal - $discount) * $taxRate;
$total = $subtotal - $discount + $taxAmount;

// Create order
$orderNumber = generateOrderNumber();
$paymentMethod = getSetting('payment_gateway', 'razorpay');

$stmt = $db->prepare("INSERT INTO orders (user_id, order_number, total_amount, discount_amount, tax_amount, final_amount, coupon_code, payment_method, payment_status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'completed')");
$stmt->bind_param("isddddss", $_SESSION['user_id'], $orderNumber, $subtotal, $discount, $taxAmount, $total, $couponCode, $paymentMethod);

if (!$stmt->execute()) {
    $_SESSION['error'] = 'Failed to create order. Please try again.';
    redirect(SITE_URL . '/checkout.php');
}

$orderId = $db->getConnection()->insert_id;

// Add order items
$stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, product_title, price) VALUES (?, ?, ?, ?)");

foreach ($cartItems as $item) {
    $stmt->bind_param("iisd", $orderId, $item['id'], $item['title'], $item['price']);
    $stmt->execute();
    
    // Update product sold count
    $updateStmt = $db->prepare("UPDATE products SET sold = sold + 1 WHERE id = ?");
    $updateStmt->bind_param("i", $item['id']);
    $updateStmt->execute();
    
    // Create download token
    $downloadToken = generateToken();
    $expiresAt = date('Y-m-d H:i:s', strtotime('+' . DOWNLOAD_LINK_EXPIRY_DAYS . ' days'));
    
    $downloadStmt = $db->prepare("INSERT INTO downloads (user_id, product_id, order_id, download_token, max_downloads, expires_at) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
    $maxDownloads = MAX_DOWNLOADS_PER_PRODUCT;
    $downloadStmt->bind_param("iiisis", $_SESSION['user_id'], $item['id'], $orderId, $downloadToken, $maxDownloads, $expiresAt);
    $downloadStmt->execute();
}

// Update coupon usage
if (!empty($couponCode)) {
    $couponUpdateStmt = $db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE code = ?");
    $couponUpdateStmt->bind_param("s", $couponCode);
    $couponUpdateStmt->execute();
}

// Clear cart
$clearCartStmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
$clearCartStmt->bind_param("i", $_SESSION['user_id']);
$clearCartStmt->execute();

// Clear session coupon
unset($_SESSION['applied_coupon']);

// Send order confirmation email
$currentUser = getCurrentUser();
$subject = "Order Confirmation - " . $orderNumber;
$message = "
    <h2>Thank you for your order!</h2>
    <p>Hi " . htmlspecialchars($currentUser['name']) . ",</p>
    <p>Your order has been successfully placed.</p>
    <p><strong>Order Number:</strong> $orderNumber</p>
    <p><strong>Total Amount:</strong> " . formatPrice($total) . "</p>
    <p>You can download your products from your account dashboard.</p>
    <p><a href='" . SITE_URL . "/orders.php'>View Orders</a></p>
    <p>Best regards,<br>" . getSetting('site_name', 'YBT Digital') . " Team</p>
";
sendEmail($currentUser['email'], $subject, $message);

// Redirect to success page
$_SESSION['success'] = 'Order placed successfully!';
redirect(SITE_URL . '/order-success.php?order=' . $orderNumber);
?>
