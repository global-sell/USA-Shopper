<?php
require_once __DIR__ . '/config/config.php';

// Check authentication BEFORE any output
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$db = Database::getInstance();
$currentUser = getCurrentUser();

// Safeguard: If user is logged in but getCurrentUser() returns null, logout
if (!$currentUser) {
    session_destroy();
    redirect(SITE_URL . '/login.php?error=session_expired');
}

$error = '';
$success = '';

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    
    if (empty($name) || empty($email)) {
        $error = 'Please fill in all fields';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email format';
    } else {
        // Check if email is already taken by another user
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email already taken';
        } else {
            $stmt = $db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $email, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $success = 'Profile updated successfully';
                $currentUser = getCurrentUser();
            } else {
                $error = 'Failed to update profile';
            }
        }
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'Please fill in all password fields';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters long';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match';
    } else {
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!password_verify($currentPassword, $user['password'])) {
            $error = 'Current password is incorrect';
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                $success = 'Password changed successfully';
            } else {
                $error = 'Failed to change password';
            }
        }
    }
}

// Handle address update
if (isset($_POST['update_addresses'])) {
    $billingAddress = sanitizeInput($_POST['billing_address'] ?? '');
    $billingCity = sanitizeInput($_POST['billing_city'] ?? '');
    $billingState = sanitizeInput($_POST['billing_state'] ?? '');
    $billingZip = sanitizeInput($_POST['billing_zip'] ?? '');
    $billingCountry = sanitizeInput($_POST['billing_country'] ?? '');
    $billingPhone = sanitizeInput($_POST['billing_phone'] ?? '');
    
    $sameAsBilling = isset($_POST['same_as_billing']) ? 1 : 0;
    
    if ($sameAsBilling) {
        $shippingAddress = $billingAddress;
        $shippingCity = $billingCity;
        $shippingState = $billingState;
        $shippingZip = $billingZip;
        $shippingCountry = $billingCountry;
        $shippingPhone = $billingPhone;
    } else {
        $shippingAddress = sanitizeInput($_POST['shipping_address'] ?? '');
        $shippingCity = sanitizeInput($_POST['shipping_city'] ?? '');
        $shippingState = sanitizeInput($_POST['shipping_state'] ?? '');
        $shippingZip = sanitizeInput($_POST['shipping_zip'] ?? '');
        $shippingCountry = sanitizeInput($_POST['shipping_country'] ?? '');
        $shippingPhone = sanitizeInput($_POST['shipping_phone'] ?? '');
    }
    
    $stmt = $db->prepare("UPDATE users SET 
                         billing_address = ?, billing_city = ?, billing_state = ?, billing_zip = ?, billing_country = ?, billing_phone = ?,
                         shipping_address = ?, shipping_city = ?, shipping_state = ?, shipping_zip = ?, shipping_country = ?, shipping_phone = ?,
                         same_as_billing = ?
                         WHERE id = ?");
    $stmt->bind_param("ssssssssssssii", 
        $billingAddress, $billingCity, $billingState, $billingZip, $billingCountry, $billingPhone,
        $shippingAddress, $shippingCity, $shippingState, $shippingZip, $shippingCountry, $shippingPhone,
        $sameAsBilling, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $success = 'Addresses updated successfully';
        $currentUser = getCurrentUser();
    } else {
        $error = 'Failed to update addresses';
    }
}

// Get user statistics
$stmt = $db->prepare("SELECT COUNT(*) as total_orders, COALESCE(SUM(final_amount), 0) as total_spent 
                     FROM orders WHERE user_id = ? AND payment_status = 'completed'");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$stmt = $db->prepare("SELECT COUNT(DISTINCT product_id) as total_products 
                     FROM order_items oi 
                     JOIN orders o ON oi.order_id = o.id 
                     WHERE o.user_id = ? AND o.payment_status = 'completed'");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$productStats = $stmt->get_result()->fetch_assoc();

// Get recent orders
$stmt = $db->prepare("SELECT id, order_number, total_amount, payment_status, created_at 
                     FROM orders WHERE user_id = ? 
                     ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$recentOrders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate profile completion
$profileCompletion = 0;
$totalFields = 5;
if (!empty($currentUser['name'])) $profileCompletion++;
if (!empty($currentUser['email'])) $profileCompletion++;
if (!empty($currentUser['billing_address'])) $profileCompletion++;
if (!empty($currentUser['billing_country'])) $profileCompletion++;
if (!empty($currentUser['shipping_address'])) $profileCompletion++;
$profileCompletionPercentage = round(($profileCompletion / $totalFields) * 100);

$pageTitle = "My Profile";
require_once 'includes/header.php';
?>

<div class="container py-4">
    <!-- Modern Profile Header -->
    <div class="card bg-gradient mb-4" style="background: linear-gradient(135deg, #f0f4ff 0%, #e8ecff 100%); border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-2" style="color: #1a1a1a; text-shadow: none; letter-spacing: 0.3px;">
                        <i class="fas fa-user-circle me-2" style="color: #667eea;"></i>Welcome, <?php echo htmlspecialchars($currentUser['name'] ?? 'User'); ?>!
                    </h2>
                    <p class="mb-3" style="font-size: 1.05rem; color: #4a4a4a; letter-spacing: 0.2px; font-weight: 500;">
                        <i class="fas fa-globe me-2" style="color: #667eea;"></i>Manage your profile and international shipping preferences
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="d-flex align-items-center bg-white rounded-3 p-2 px-3 shadow-sm" style="border: 1px solid #e0e7ff;">
                            <div class="rounded-circle p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #667eea;">
                                <i class="fas fa-envelope" style="color: #ffffff;"></i>
                            </div>
                            <div>
                                <small class="d-block fw-bold" style="font-size: 0.75rem; color: #6b7280;">Email</small>
                                <strong style="font-size: 0.95rem; color: #1a1a1a;"><?php echo htmlspecialchars($currentUser['email'] ?? 'N/A'); ?></strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center bg-white rounded-3 p-2 px-3 shadow-sm" style="border: 1px solid #e0e7ff;">
                            <div class="rounded-circle p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #667eea;">
                                <i class="fas fa-calendar" style="color: #ffffff;"></i>
                            </div>
                            <div>
                                <small class="d-block fw-bold" style="font-size: 0.75rem; color: #6b7280;">Member Since</small>
                                <strong style="font-size: 0.95rem; color: #1a1a1a;">
                                    <?php 
                                    if (isset($currentUser['created_at']) && !empty($currentUser['created_at'])) {
                                        echo date('M Y', strtotime($currentUser['created_at']));
                                    } else {
                                        echo 'Recently';
                                    }
                                    ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-end mt-3 mt-lg-0">
                    <div class="bg-white rounded-3 p-3 shadow-sm" style="border: 1px solid #e0e7ff;">
                        <div class="mb-2">
                            <i class="fas fa-shipping-fast fa-2x mb-2" style="color: #667eea;"></i>
                        </div>
                        <h6 class="fw-bold mb-1" style="color: #1a1a1a; letter-spacing: 0.3px;">Global Shipping</h6>
                        <small style="color: #4a4a4a; font-weight: 600; letter-spacing: 0.2px;">We ship to 100+ countries worldwide</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 10px; border-left: 4px solid #dc3545; animation: slideInDown 0.5s ease-out;">
            <i class="fas fa-exclamation-circle me-2"></i><strong>Error!</strong> <?php echo $error; ?>
            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" style="border-radius: 10px; border-left: 4px solid #28a745; animation: slideInDown 0.5s ease-out;">
            <i class="fas fa-check-circle me-2"></i><strong>Success!</strong> <?php echo $success; ?>
            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- International Shipping Info Banner -->
    <div class="alert alert-info border-0 shadow-sm mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="fw-bold mb-2">
                    <i class="fas fa-globe-americas me-2"></i>International Shipping Available
                </h6>
                <p class="mb-0 small">
                    We ship to 100+ countries worldwide. Free shipping on orders over $50 to USA, UK, Canada, and Australia. 
                    Update your shipping address below to see delivery options for your location.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                <span class="badge bg-primary px-3 py-2 me-1">🇺🇸 USA</span>
                <span class="badge bg-primary px-3 py-2 me-1">🇬🇧 UK</span>
                <span class="badge bg-primary px-3 py-2 me-1">🇨🇦 CA</span>
                <span class="badge bg-primary px-3 py-2">🇦🇺 AU</span>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card text-white border-0 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle p-3 mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2);">
                        <i class="fas fa-box fa-2x" style="color: #ffffff;"></i>
                    </div>
                    <h2 class="fw-bold mb-1" style="font-size: 2.5rem;"><?php echo $stats['total_orders']; ?></h2>
                    <p class="mb-0 opacity-90 fw-bold">Total Orders</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card text-white border-0 h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 15px; box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3); transition: all 0.3s ease;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle p-3 mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2);">
                        <i class="fas fa-shopping-bag fa-2x" style="color: #ffffff;"></i>
                    </div>
                    <h2 class="fw-bold mb-1" style="font-size: 2.5rem;"><?php echo $productStats['total_products']; ?></h2>
                    <p class="mb-0 opacity-90 fw-bold">Products Owned</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card text-white border-0 h-100" style="background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%); border-radius: 15px; box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3); transition: all 0.3s ease;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle p-3 mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2);">
                        <i class="fas fa-dollar-sign fa-2x" style="color: #ffffff;"></i>
                    </div>
                    <h2 class="fw-bold mb-1" style="font-size: 2.5rem;"><?php echo formatPrice($stats['total_spent']); ?></h2>
                    <p class="mb-0 opacity-90 fw-bold">Total Spent</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card text-white border-0 h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 15px; box-shadow: 0 8px 20px rgba(240, 147, 251, 0.3); transition: all 0.3s ease;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle p-3 mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2);">
                        <i class="fas fa-globe fa-2x" style="color: #ffffff;"></i>
                    </div>
                    <h2 class="fw-bold mb-1" style="font-size: 1.8rem;"><?php echo !empty($currentUser['billing_country']) ? htmlspecialchars($currentUser['billing_country']) : 'Not Set'; ?></h2>
                    <p class="mb-0 opacity-90 fw-bold">Your Country</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Completion & Recent Orders -->
    <div class="row mb-4">
        <!-- Profile Completion -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100" style="border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-chart-line me-2" style="color: #667eea;"></i>Profile Completion</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size: 0.9rem; color: #6b7280;">Complete your profile</span>
                            <span class="fw-bold" style="color: #667eea;"><?php echo $profileCompletionPercentage; ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: <?php echo $profileCompletionPercentage; ?>%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);" 
                                 aria-valuenow="<?php echo $profileCompletionPercentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0" style="font-size: 0.9rem;">
                        <li class="mb-2 <?php echo !empty($currentUser['name']) ? 'text-success' : 'text-muted'; ?>">
                            <i class="fas fa-<?php echo !empty($currentUser['name']) ? 'check-circle' : 'circle'; ?> me-2"></i>Full Name
                        </li>
                        <li class="mb-2 <?php echo !empty($currentUser['email']) ? 'text-success' : 'text-muted'; ?>">
                            <i class="fas fa-<?php echo !empty($currentUser['email']) ? 'check-circle' : 'circle'; ?> me-2"></i>Email Address
                        </li>
                        <li class="mb-2 <?php echo !empty($currentUser['billing_address']) ? 'text-success' : 'text-muted'; ?>">
                            <i class="fas fa-<?php echo !empty($currentUser['billing_address']) ? 'check-circle' : 'circle'; ?> me-2"></i>Billing Address
                        </li>
                        <li class="mb-2 <?php echo !empty($currentUser['billing_country']) ? 'text-success' : 'text-muted'; ?>">
                            <i class="fas fa-<?php echo !empty($currentUser['billing_country']) ? 'check-circle' : 'circle'; ?> me-2"></i>Country
                        </li>
                        <li class="mb-2 <?php echo !empty($currentUser['shipping_address']) ? 'text-success' : 'text-muted'; ?>">
                            <i class="fas fa-<?php echo !empty($currentUser['shipping_address']) ? 'check-circle' : 'circle'; ?> me-2"></i>Shipping Address
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100" style="border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="fas fa-shopping-bag me-2" style="color: #667eea;"></i>Recent Orders</h6>
                        <a href="<?php echo SITE_URL; ?>/orders" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <?php if (empty($recentOrders)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x mb-3" style="color: #e0e7ff;"></i>
                            <p class="text-muted mb-3">No orders yet</p>
                            <a href="<?php echo SITE_URL; ?>/products" class="btn btn-primary btn-sm">
                                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th style="font-size: 0.85rem; font-weight: 600; color: #6b7280;">Order #</th>
                                        <th style="font-size: 0.85rem; font-weight: 600; color: #6b7280;">Date</th>
                                        <th style="font-size: 0.85rem; font-weight: 600; color: #6b7280;">Total</th>
                                        <th style="font-size: 0.85rem; font-weight: 600; color: #6b7280;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td>
                                            <a href="https://globalsell.site/track-order" target="_blank"
                                               class="text-decoration-none fw-bold" style="color: #667eea; font-size: 0.9rem;">
                                                #<?php echo htmlspecialchars($order['order_number']); ?>
                                            </a>
                                        </td>
                                        <td style="font-size: 0.9rem; color: #6b7280;">
                                            <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                        </td>
                                        <td class="fw-bold" style="font-size: 0.95rem; color: #1a1a1a;">
                                            <?php echo formatPrice($order['total_amount']); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'completed' => 'success',
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusColor = $statusColors[$order['payment_status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo $statusColor; ?>" style="font-size: 0.75rem;">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-outline mb-4">
                            <input type="text" id="name" name="name" class="form-control" 
                                   value="<?php echo htmlspecialchars($currentUser['name'] ?? ''); ?>" required>
                            <label class="form-label" for="name">Full Name</label>
                        </div>
                        
                        <div class="form-outline mb-4">
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                            <label class="form-label" for="email">Email Address</label>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Change Password -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-outline mb-4">
                            <input type="password" id="current_password" name="current_password" 
                                   class="form-control" required>
                            <label class="form-label" for="current_password">Current Password</label>
                        </div>
                        
                        <div class="form-outline mb-4">
                            <input type="password" id="new_password" name="new_password" 
                                   class="form-control" required>
                            <label class="form-label" for="new_password">New Password</label>
                        </div>
                        
                        <div class="form-outline mb-4">
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   class="form-control" required>
                            <label class="form-label" for="confirm_password">Confirm New Password</label>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Address Management -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address Management</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <!-- Billing Address -->
                            <div class="col-lg-6 mb-4">
                                <h6 class="fw-bold mb-3"><i class="fas fa-file-invoice me-2"></i>Billing Address</h6>
                                
                                <div class="form-outline mb-3">
                                    <textarea id="billing_address" name="billing_address" class="form-control" rows="2"><?php echo htmlspecialchars($currentUser['billing_address'] ?? ''); ?></textarea>
                                    <label class="form-label" for="billing_address">Street Address</label>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-outline">
                                            <input type="text" id="billing_city" name="billing_city" class="form-control" value="<?php echo htmlspecialchars($currentUser['billing_city'] ?? ''); ?>">
                                            <label class="form-label" for="billing_city">City</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-outline">
                                            <input type="text" id="billing_state" name="billing_state" class="form-control" value="<?php echo htmlspecialchars($currentUser['billing_state'] ?? ''); ?>">
                                            <label class="form-label" for="billing_state">State</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-outline">
                                            <input type="text" id="billing_zip" name="billing_zip" class="form-control" value="<?php echo htmlspecialchars($currentUser['billing_zip'] ?? ''); ?>">
                                            <label class="form-label" for="billing_zip">ZIP Code</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="billing_country">Country</label>
                                        <select id="billing_country" name="billing_country" class="form-select">
                                            <option value="">Select Country</option>
                                            <optgroup label="Popular Countries">
                                                <option value="United States" <?php echo ($currentUser['billing_country'] ?? '') === 'United States' ? 'selected' : ''; ?>>🇺🇸 United States</option>
                                                <option value="United Kingdom" <?php echo ($currentUser['billing_country'] ?? '') === 'United Kingdom' ? 'selected' : ''; ?>>🇬🇧 United Kingdom</option>
                                                <option value="Canada" <?php echo ($currentUser['billing_country'] ?? '') === 'Canada' ? 'selected' : ''; ?>>🇨🇦 Canada</option>
                                                <option value="Australia" <?php echo ($currentUser['billing_country'] ?? '') === 'Australia' ? 'selected' : ''; ?>>🇦🇺 Australia</option>
                                                <option value="Germany" <?php echo ($currentUser['billing_country'] ?? '') === 'Germany' ? 'selected' : ''; ?>>🇩🇪 Germany</option>
                                                <option value="France" <?php echo ($currentUser['billing_country'] ?? '') === 'France' ? 'selected' : ''; ?>>🇫🇷 France</option>
                                            </optgroup>
                                            <optgroup label="Europe">
                                                <option value="Austria" <?php echo ($currentUser['billing_country'] ?? '') === 'Austria' ? 'selected' : ''; ?>>🇦🇹 Austria</option>
                                                <option value="Belgium" <?php echo ($currentUser['billing_country'] ?? '') === 'Belgium' ? 'selected' : ''; ?>>🇧🇪 Belgium</option>
                                                <option value="Bulgaria" <?php echo ($currentUser['billing_country'] ?? '') === 'Bulgaria' ? 'selected' : ''; ?>>🇧🇬 Bulgaria</option>
                                                <option value="Croatia" <?php echo ($currentUser['billing_country'] ?? '') === 'Croatia' ? 'selected' : ''; ?>>🇭🇷 Croatia</option>
                                                <option value="Cyprus" <?php echo ($currentUser['billing_country'] ?? '') === 'Cyprus' ? 'selected' : ''; ?>>🇨🇾 Cyprus</option>
                                                <option value="Czech Republic" <?php echo ($currentUser['billing_country'] ?? '') === 'Czech Republic' ? 'selected' : ''; ?>>🇨🇿 Czech Republic</option>
                                                <option value="Denmark" <?php echo ($currentUser['billing_country'] ?? '') === 'Denmark' ? 'selected' : ''; ?>>🇩🇰 Denmark</option>
                                                <option value="Estonia" <?php echo ($currentUser['billing_country'] ?? '') === 'Estonia' ? 'selected' : ''; ?>>🇪🇪 Estonia</option>
                                                <option value="Finland" <?php echo ($currentUser['billing_country'] ?? '') === 'Finland' ? 'selected' : ''; ?>>🇫🇮 Finland</option>
                                                <option value="Greece" <?php echo ($currentUser['billing_country'] ?? '') === 'Greece' ? 'selected' : ''; ?>>🇬🇷 Greece</option>
                                                <option value="Hungary" <?php echo ($currentUser['billing_country'] ?? '') === 'Hungary' ? 'selected' : ''; ?>>🇭🇺 Hungary</option>
                                                <option value="Iceland" <?php echo ($currentUser['billing_country'] ?? '') === 'Iceland' ? 'selected' : ''; ?>>🇮🇸 Iceland</option>
                                                <option value="Ireland" <?php echo ($currentUser['billing_country'] ?? '') === 'Ireland' ? 'selected' : ''; ?>>🇮🇪 Ireland</option>
                                                <option value="Italy" <?php echo ($currentUser['billing_country'] ?? '') === 'Italy' ? 'selected' : ''; ?>>🇮🇹 Italy</option>
                                                <option value="Latvia" <?php echo ($currentUser['billing_country'] ?? '') === 'Latvia' ? 'selected' : ''; ?>>🇱🇻 Latvia</option>
                                                <option value="Lithuania" <?php echo ($currentUser['billing_country'] ?? '') === 'Lithuania' ? 'selected' : ''; ?>>🇱🇹 Lithuania</option>
                                                <option value="Luxembourg" <?php echo ($currentUser['billing_country'] ?? '') === 'Luxembourg' ? 'selected' : ''; ?>>🇱🇺 Luxembourg</option>
                                                <option value="Malta" <?php echo ($currentUser['billing_country'] ?? '') === 'Malta' ? 'selected' : ''; ?>>🇲🇹 Malta</option>
                                                <option value="Netherlands" <?php echo ($currentUser['billing_country'] ?? '') === 'Netherlands' ? 'selected' : ''; ?>>🇳🇱 Netherlands</option>
                                                <option value="Norway" <?php echo ($currentUser['billing_country'] ?? '') === 'Norway' ? 'selected' : ''; ?>>🇳🇴 Norway</option>
                                                <option value="Poland" <?php echo ($currentUser['billing_country'] ?? '') === 'Poland' ? 'selected' : ''; ?>>🇵🇱 Poland</option>
                                                <option value="Portugal" <?php echo ($currentUser['billing_country'] ?? '') === 'Portugal' ? 'selected' : ''; ?>>🇵🇹 Portugal</option>
                                                <option value="Romania" <?php echo ($currentUser['billing_country'] ?? '') === 'Romania' ? 'selected' : ''; ?>>🇷🇴 Romania</option>
                                                <option value="Slovakia" <?php echo ($currentUser['billing_country'] ?? '') === 'Slovakia' ? 'selected' : ''; ?>>🇸🇰 Slovakia</option>
                                                <option value="Slovenia" <?php echo ($currentUser['billing_country'] ?? '') === 'Slovenia' ? 'selected' : ''; ?>>🇸🇮 Slovenia</option>
                                                <option value="Spain" <?php echo ($currentUser['billing_country'] ?? '') === 'Spain' ? 'selected' : ''; ?>>🇪🇸 Spain</option>
                                                <option value="Sweden" <?php echo ($currentUser['billing_country'] ?? '') === 'Sweden' ? 'selected' : ''; ?>>🇸🇪 Sweden</option>
                                                <option value="Switzerland" <?php echo ($currentUser['billing_country'] ?? '') === 'Switzerland' ? 'selected' : ''; ?>>🇨🇭 Switzerland</option>
                                            </optgroup>
                                            <optgroup label="Asia">
                                                <option value="Bangladesh" <?php echo ($currentUser['billing_country'] ?? '') === 'Bangladesh' ? 'selected' : ''; ?>>🇧🇩 Bangladesh</option>
                                                <option value="China" <?php echo ($currentUser['billing_country'] ?? '') === 'China' ? 'selected' : ''; ?>>🇨🇳 China</option>
                                                <option value="Hong Kong" <?php echo ($currentUser['billing_country'] ?? '') === 'Hong Kong' ? 'selected' : ''; ?>>🇭🇰 Hong Kong</option>
                                                <option value="India" <?php echo ($currentUser['billing_country'] ?? '') === 'India' ? 'selected' : ''; ?>>🇮🇳 India</option>
                                                <option value="Indonesia" <?php echo ($currentUser['billing_country'] ?? '') === 'Indonesia' ? 'selected' : ''; ?>>🇮🇩 Indonesia</option>
                                                <option value="Japan" <?php echo ($currentUser['billing_country'] ?? '') === 'Japan' ? 'selected' : ''; ?>>🇯🇵 Japan</option>
                                                <option value="Malaysia" <?php echo ($currentUser['billing_country'] ?? '') === 'Malaysia' ? 'selected' : ''; ?>>🇲🇾 Malaysia</option>
                                                <option value="Pakistan" <?php echo ($currentUser['billing_country'] ?? '') === 'Pakistan' ? 'selected' : ''; ?>>🇵🇰 Pakistan</option>
                                                <option value="Philippines" <?php echo ($currentUser['billing_country'] ?? '') === 'Philippines' ? 'selected' : ''; ?>>🇵🇭 Philippines</option>
                                                <option value="Singapore" <?php echo ($currentUser['billing_country'] ?? '') === 'Singapore' ? 'selected' : ''; ?>>🇸🇬 Singapore</option>
                                                <option value="South Korea" <?php echo ($currentUser['billing_country'] ?? '') === 'South Korea' ? 'selected' : ''; ?>>🇰🇷 South Korea</option>
                                                <option value="Sri Lanka" <?php echo ($currentUser['billing_country'] ?? '') === 'Sri Lanka' ? 'selected' : ''; ?>>🇱🇰 Sri Lanka</option>
                                                <option value="Taiwan" <?php echo ($currentUser['billing_country'] ?? '') === 'Taiwan' ? 'selected' : ''; ?>>🇹🇼 Taiwan</option>
                                                <option value="Thailand" <?php echo ($currentUser['billing_country'] ?? '') === 'Thailand' ? 'selected' : ''; ?>>🇹🇭 Thailand</option>
                                                <option value="Vietnam" <?php echo ($currentUser['billing_country'] ?? '') === 'Vietnam' ? 'selected' : ''; ?>>🇻🇳 Vietnam</option>
                                            </optgroup>
                                            <optgroup label="Middle East">
                                                <option value="Bahrain" <?php echo ($currentUser['billing_country'] ?? '') === 'Bahrain' ? 'selected' : ''; ?>>🇧🇭 Bahrain</option>
                                                <option value="Israel" <?php echo ($currentUser['billing_country'] ?? '') === 'Israel' ? 'selected' : ''; ?>>🇮🇱 Israel</option>
                                                <option value="Jordan" <?php echo ($currentUser['billing_country'] ?? '') === 'Jordan' ? 'selected' : ''; ?>>🇯🇴 Jordan</option>
                                                <option value="Kuwait" <?php echo ($currentUser['billing_country'] ?? '') === 'Kuwait' ? 'selected' : ''; ?>>🇰🇼 Kuwait</option>
                                                <option value="Lebanon" <?php echo ($currentUser['billing_country'] ?? '') === 'Lebanon' ? 'selected' : ''; ?>>🇱🇧 Lebanon</option>
                                                <option value="Oman" <?php echo ($currentUser['billing_country'] ?? '') === 'Oman' ? 'selected' : ''; ?>>🇴🇲 Oman</option>
                                                <option value="Qatar" <?php echo ($currentUser['billing_country'] ?? '') === 'Qatar' ? 'selected' : ''; ?>>🇶🇦 Qatar</option>
                                                <option value="Saudi Arabia" <?php echo ($currentUser['billing_country'] ?? '') === 'Saudi Arabia' ? 'selected' : ''; ?>>🇸🇦 Saudi Arabia</option>
                                                <option value="Turkey" <?php echo ($currentUser['billing_country'] ?? '') === 'Turkey' ? 'selected' : ''; ?>>🇹🇷 Turkey</option>
                                                <option value="UAE" <?php echo ($currentUser['billing_country'] ?? '') === 'UAE' ? 'selected' : ''; ?>>🇦🇪 UAE</option>
                                            </optgroup>
                                            <optgroup label="Africa">
                                                <option value="Egypt" <?php echo ($currentUser['billing_country'] ?? '') === 'Egypt' ? 'selected' : ''; ?>>🇪🇬 Egypt</option>
                                                <option value="Kenya" <?php echo ($currentUser['billing_country'] ?? '') === 'Kenya' ? 'selected' : ''; ?>>🇰🇪 Kenya</option>
                                                <option value="Morocco" <?php echo ($currentUser['billing_country'] ?? '') === 'Morocco' ? 'selected' : ''; ?>>🇲🇦 Morocco</option>
                                                <option value="Nigeria" <?php echo ($currentUser['billing_country'] ?? '') === 'Nigeria' ? 'selected' : ''; ?>>🇳🇬 Nigeria</option>
                                                <option value="South Africa" <?php echo ($currentUser['billing_country'] ?? '') === 'South Africa' ? 'selected' : ''; ?>>🇿🇦 South Africa</option>
                                            </optgroup>
                                            <optgroup label="Americas">
                                                <option value="Argentina" <?php echo ($currentUser['billing_country'] ?? '') === 'Argentina' ? 'selected' : ''; ?>>🇦🇷 Argentina</option>
                                                <option value="Brazil" <?php echo ($currentUser['billing_country'] ?? '') === 'Brazil' ? 'selected' : ''; ?>>🇧🇷 Brazil</option>
                                                <option value="Chile" <?php echo ($currentUser['billing_country'] ?? '') === 'Chile' ? 'selected' : ''; ?>>🇨🇱 Chile</option>
                                                <option value="Colombia" <?php echo ($currentUser['billing_country'] ?? '') === 'Colombia' ? 'selected' : ''; ?>>🇨🇴 Colombia</option>
                                                <option value="Mexico" <?php echo ($currentUser['billing_country'] ?? '') === 'Mexico' ? 'selected' : ''; ?>>🇲🇽 Mexico</option>
                                                <option value="Peru" <?php echo ($currentUser['billing_country'] ?? '') === 'Peru' ? 'selected' : ''; ?>>🇵🇪 Peru</option>
                                            </optgroup>
                                            <optgroup label="Oceania">
                                                <option value="New Zealand" <?php echo ($currentUser['billing_country'] ?? '') === 'New Zealand' ? 'selected' : ''; ?>>🇳🇿 New Zealand</option>
                                            </optgroup>
                                            <optgroup label="Other">
                                                <option value="Other" <?php echo ($currentUser['billing_country'] ?? '') === 'Other' ? 'selected' : ''; ?>>🌍 Other</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-outline mb-3">
                                    <input type="tel" id="billing_phone" name="billing_phone" class="form-control" 
                                           value="<?php echo htmlspecialchars($currentUser['billing_phone'] ?? ''); ?>"
                                           placeholder="+1 (555) 123-4567">
                                    <label class="form-label" for="billing_phone">Phone (with country code)</label>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Include country code (e.g., +1 for USA, +44 for UK)
                                </small>
                            </div>
                            
                            <!-- Shipping Address -->
                            <div class="col-lg-6 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0"><i class="fas fa-shipping-fast me-2"></i>Shipping Address</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="same_as_billing" name="same_as_billing" 
                                               <?php echo ($currentUser['same_as_billing'] ?? true) ? 'checked' : ''; ?>
                                               onchange="toggleShippingAddress()">
                                        <label class="form-check-label" for="same_as_billing">
                                            Same as billing
                                        </label>
                                    </div>
                                </div>
                                
                                <div id="shipping_fields" style="display: <?php echo ($currentUser['same_as_billing'] ?? true) ? 'none' : 'block'; ?>;">
                                    <div class="form-outline mb-3">
                                        <textarea id="shipping_address" name="shipping_address" class="form-control" rows="2"><?php echo htmlspecialchars($currentUser['shipping_address'] ?? ''); ?></textarea>
                                        <label class="form-label" for="shipping_address">Street Address</label>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-outline">
                                                <input type="text" id="shipping_city" name="shipping_city" class="form-control" value="<?php echo htmlspecialchars($currentUser['shipping_city'] ?? ''); ?>">
                                                <label class="form-label" for="shipping_city">City</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-outline">
                                                <input type="text" id="shipping_state" name="shipping_state" class="form-control" value="<?php echo htmlspecialchars($currentUser['shipping_state'] ?? ''); ?>">
                                                <label class="form-label" for="shipping_state">State</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-outline">
                                                <input type="text" id="shipping_zip" name="shipping_zip" class="form-control" value="<?php echo htmlspecialchars($currentUser['shipping_zip'] ?? ''); ?>">
                                                <label class="form-label" for="shipping_zip">ZIP Code</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="shipping_country">Country</label>
                                            <select id="shipping_country" name="shipping_country" class="form-select">
                                                <option value="">Select Country</option>
                                                <optgroup label="Popular Countries">
                                                    <option value="United States" <?php echo ($currentUser['shipping_country'] ?? '') === 'United States' ? 'selected' : ''; ?>>🇺🇸 United States</option>
                                                    <option value="United Kingdom" <?php echo ($currentUser['shipping_country'] ?? '') === 'United Kingdom' ? 'selected' : ''; ?>>🇬🇧 United Kingdom</option>
                                                    <option value="Canada" <?php echo ($currentUser['shipping_country'] ?? '') === 'Canada' ? 'selected' : ''; ?>>🇨🇦 Canada</option>
                                                    <option value="Australia" <?php echo ($currentUser['shipping_country'] ?? '') === 'Australia' ? 'selected' : ''; ?>>🇦🇺 Australia</option>
                                                    <option value="Germany" <?php echo ($currentUser['shipping_country'] ?? '') === 'Germany' ? 'selected' : ''; ?>>🇩🇪 Germany</option>
                                                    <option value="France" <?php echo ($currentUser['shipping_country'] ?? '') === 'France' ? 'selected' : ''; ?>>🇫🇷 France</option>
                                                </optgroup>
                                                <optgroup label="Europe">
                                                    <option value="Austria" <?php echo ($currentUser['shipping_country'] ?? '') === 'Austria' ? 'selected' : ''; ?>>🇦🇹 Austria</option>
                                                    <option value="Belgium" <?php echo ($currentUser['shipping_country'] ?? '') === 'Belgium' ? 'selected' : ''; ?>>🇧🇪 Belgium</option>
                                                    <option value="Bulgaria" <?php echo ($currentUser['shipping_country'] ?? '') === 'Bulgaria' ? 'selected' : ''; ?>>🇧🇬 Bulgaria</option>
                                                    <option value="Croatia" <?php echo ($currentUser['shipping_country'] ?? '') === 'Croatia' ? 'selected' : ''; ?>>🇭🇷 Croatia</option>
                                                    <option value="Cyprus" <?php echo ($currentUser['shipping_country'] ?? '') === 'Cyprus' ? 'selected' : ''; ?>>🇨🇾 Cyprus</option>
                                                    <option value="Czech Republic" <?php echo ($currentUser['shipping_country'] ?? '') === 'Czech Republic' ? 'selected' : ''; ?>>🇨🇿 Czech Republic</option>
                                                    <option value="Denmark" <?php echo ($currentUser['shipping_country'] ?? '') === 'Denmark' ? 'selected' : ''; ?>>🇩🇰 Denmark</option>
                                                    <option value="Estonia" <?php echo ($currentUser['shipping_country'] ?? '') === 'Estonia' ? 'selected' : ''; ?>>🇪🇪 Estonia</option>
                                                    <option value="Finland" <?php echo ($currentUser['shipping_country'] ?? '') === 'Finland' ? 'selected' : ''; ?>>🇫🇮 Finland</option>
                                                    <option value="Greece" <?php echo ($currentUser['shipping_country'] ?? '') === 'Greece' ? 'selected' : ''; ?>>🇬🇷 Greece</option>
                                                    <option value="Hungary" <?php echo ($currentUser['shipping_country'] ?? '') === 'Hungary' ? 'selected' : ''; ?>>🇭🇺 Hungary</option>
                                                    <option value="Iceland" <?php echo ($currentUser['shipping_country'] ?? '') === 'Iceland' ? 'selected' : ''; ?>>🇮🇸 Iceland</option>
                                                    <option value="Ireland" <?php echo ($currentUser['shipping_country'] ?? '') === 'Ireland' ? 'selected' : ''; ?>>🇮🇪 Ireland</option>
                                                    <option value="Italy" <?php echo ($currentUser['shipping_country'] ?? '') === 'Italy' ? 'selected' : ''; ?>>🇮🇹 Italy</option>
                                                    <option value="Latvia" <?php echo ($currentUser['shipping_country'] ?? '') === 'Latvia' ? 'selected' : ''; ?>>🇱🇻 Latvia</option>
                                                    <option value="Lithuania" <?php echo ($currentUser['shipping_country'] ?? '') === 'Lithuania' ? 'selected' : ''; ?>>🇱🇹 Lithuania</option>
                                                    <option value="Luxembourg" <?php echo ($currentUser['shipping_country'] ?? '') === 'Luxembourg' ? 'selected' : ''; ?>>🇱🇺 Luxembourg</option>
                                                    <option value="Malta" <?php echo ($currentUser['shipping_country'] ?? '') === 'Malta' ? 'selected' : ''; ?>>🇲🇹 Malta</option>
                                                    <option value="Netherlands" <?php echo ($currentUser['shipping_country'] ?? '') === 'Netherlands' ? 'selected' : ''; ?>>🇳🇱 Netherlands</option>
                                                    <option value="Norway" <?php echo ($currentUser['shipping_country'] ?? '') === 'Norway' ? 'selected' : ''; ?>>🇳🇴 Norway</option>
                                                    <option value="Poland" <?php echo ($currentUser['shipping_country'] ?? '') === 'Poland' ? 'selected' : ''; ?>>🇵🇱 Poland</option>
                                                    <option value="Portugal" <?php echo ($currentUser['shipping_country'] ?? '') === 'Portugal' ? 'selected' : ''; ?>>🇵🇹 Portugal</option>
                                                    <option value="Romania" <?php echo ($currentUser['shipping_country'] ?? '') === 'Romania' ? 'selected' : ''; ?>>🇷🇴 Romania</option>
                                                    <option value="Slovakia" <?php echo ($currentUser['shipping_country'] ?? '') === 'Slovakia' ? 'selected' : ''; ?>>🇸🇰 Slovakia</option>
                                                    <option value="Slovenia" <?php echo ($currentUser['shipping_country'] ?? '') === 'Slovenia' ? 'selected' : ''; ?>>🇸🇮 Slovenia</option>
                                                    <option value="Spain" <?php echo ($currentUser['shipping_country'] ?? '') === 'Spain' ? 'selected' : ''; ?>>🇪🇸 Spain</option>
                                                    <option value="Sweden" <?php echo ($currentUser['shipping_country'] ?? '') === 'Sweden' ? 'selected' : ''; ?>>🇸🇪 Sweden</option>
                                                    <option value="Switzerland" <?php echo ($currentUser['shipping_country'] ?? '') === 'Switzerland' ? 'selected' : ''; ?>>🇨🇭 Switzerland</option>
                                                </optgroup>
                                                <optgroup label="Asia">
                                                    <option value="Bangladesh" <?php echo ($currentUser['shipping_country'] ?? '') === 'Bangladesh' ? 'selected' : ''; ?>>🇧🇩 Bangladesh</option>
                                                    <option value="China" <?php echo ($currentUser['shipping_country'] ?? '') === 'China' ? 'selected' : ''; ?>>🇨🇳 China</option>
                                                    <option value="Hong Kong" <?php echo ($currentUser['shipping_country'] ?? '') === 'Hong Kong' ? 'selected' : ''; ?>>🇭🇰 Hong Kong</option>
                                                    <option value="India" <?php echo ($currentUser['shipping_country'] ?? '') === 'India' ? 'selected' : ''; ?>>🇮🇳 India</option>
                                                    <option value="Indonesia" <?php echo ($currentUser['shipping_country'] ?? '') === 'Indonesia' ? 'selected' : ''; ?>>🇮🇩 Indonesia</option>
                                                    <option value="Japan" <?php echo ($currentUser['shipping_country'] ?? '') === 'Japan' ? 'selected' : ''; ?>>🇯🇵 Japan</option>
                                                    <option value="Malaysia" <?php echo ($currentUser['shipping_country'] ?? '') === 'Malaysia' ? 'selected' : ''; ?>>🇲🇾 Malaysia</option>
                                                    <option value="Pakistan" <?php echo ($currentUser['shipping_country'] ?? '') === 'Pakistan' ? 'selected' : ''; ?>>🇵🇰 Pakistan</option>
                                                    <option value="Philippines" <?php echo ($currentUser['shipping_country'] ?? '') === 'Philippines' ? 'selected' : ''; ?>>🇵🇭 Philippines</option>
                                                    <option value="Singapore" <?php echo ($currentUser['shipping_country'] ?? '') === 'Singapore' ? 'selected' : ''; ?>>🇸🇬 Singapore</option>
                                                    <option value="South Korea" <?php echo ($currentUser['shipping_country'] ?? '') === 'South Korea' ? 'selected' : ''; ?>>🇰🇷 South Korea</option>
                                                    <option value="Sri Lanka" <?php echo ($currentUser['shipping_country'] ?? '') === 'Sri Lanka' ? 'selected' : ''; ?>>🇱🇰 Sri Lanka</option>
                                                    <option value="Taiwan" <?php echo ($currentUser['shipping_country'] ?? '') === 'Taiwan' ? 'selected' : ''; ?>>🇹🇼 Taiwan</option>
                                                    <option value="Thailand" <?php echo ($currentUser['shipping_country'] ?? '') === 'Thailand' ? 'selected' : ''; ?>>🇹🇭 Thailand</option>
                                                    <option value="Vietnam" <?php echo ($currentUser['shipping_country'] ?? '') === 'Vietnam' ? 'selected' : ''; ?>>🇻🇳 Vietnam</option>
                                                </optgroup>
                                                <optgroup label="Middle East">
                                                    <option value="Bahrain" <?php echo ($currentUser['shipping_country'] ?? '') === 'Bahrain' ? 'selected' : ''; ?>>🇧🇭 Bahrain</option>
                                                    <option value="Israel" <?php echo ($currentUser['shipping_country'] ?? '') === 'Israel' ? 'selected' : ''; ?>>🇮🇱 Israel</option>
                                                    <option value="Jordan" <?php echo ($currentUser['shipping_country'] ?? '') === 'Jordan' ? 'selected' : ''; ?>>🇯🇴 Jordan</option>
                                                    <option value="Kuwait" <?php echo ($currentUser['shipping_country'] ?? '') === 'Kuwait' ? 'selected' : ''; ?>>🇰🇼 Kuwait</option>
                                                    <option value="Lebanon" <?php echo ($currentUser['shipping_country'] ?? '') === 'Lebanon' ? 'selected' : ''; ?>>🇱🇧 Lebanon</option>
                                                    <option value="Oman" <?php echo ($currentUser['shipping_country'] ?? '') === 'Oman' ? 'selected' : ''; ?>>🇴🇲 Oman</option>
                                                    <option value="Qatar" <?php echo ($currentUser['shipping_country'] ?? '') === 'Qatar' ? 'selected' : ''; ?>>🇶🇦 Qatar</option>
                                                    <option value="Saudi Arabia" <?php echo ($currentUser['shipping_country'] ?? '') === 'Saudi Arabia' ? 'selected' : ''; ?>>🇸🇦 Saudi Arabia</option>
                                                    <option value="Turkey" <?php echo ($currentUser['shipping_country'] ?? '') === 'Turkey' ? 'selected' : ''; ?>>🇹🇷 Turkey</option>
                                                    <option value="UAE" <?php echo ($currentUser['shipping_country'] ?? '') === 'UAE' ? 'selected' : ''; ?>>🇦🇪 UAE</option>
                                                </optgroup>
                                                <optgroup label="Africa">
                                                    <option value="Egypt" <?php echo ($currentUser['shipping_country'] ?? '') === 'Egypt' ? 'selected' : ''; ?>>🇪🇬 Egypt</option>
                                                    <option value="Kenya" <?php echo ($currentUser['shipping_country'] ?? '') === 'Kenya' ? 'selected' : ''; ?>>🇰🇪 Kenya</option>
                                                    <option value="Morocco" <?php echo ($currentUser['shipping_country'] ?? '') === 'Morocco' ? 'selected' : ''; ?>>🇲🇦 Morocco</option>
                                                    <option value="Nigeria" <?php echo ($currentUser['shipping_country'] ?? '') === 'Nigeria' ? 'selected' : ''; ?>>🇳🇬 Nigeria</option>
                                                    <option value="South Africa" <?php echo ($currentUser['shipping_country'] ?? '') === 'South Africa' ? 'selected' : ''; ?>>🇿🇦 South Africa</option>
                                                </optgroup>
                                                <optgroup label="Americas">
                                                    <option value="Argentina" <?php echo ($currentUser['shipping_country'] ?? '') === 'Argentina' ? 'selected' : ''; ?>>🇦🇷 Argentina</option>
                                                    <option value="Brazil" <?php echo ($currentUser['shipping_country'] ?? '') === 'Brazil' ? 'selected' : ''; ?>>🇧🇷 Brazil</option>
                                                    <option value="Chile" <?php echo ($currentUser['shipping_country'] ?? '') === 'Chile' ? 'selected' : ''; ?>>🇨🇱 Chile</option>
                                                    <option value="Colombia" <?php echo ($currentUser['shipping_country'] ?? '') === 'Colombia' ? 'selected' : ''; ?>>🇨🇴 Colombia</option>
                                                    <option value="Mexico" <?php echo ($currentUser['shipping_country'] ?? '') === 'Mexico' ? 'selected' : ''; ?>>🇲🇽 Mexico</option>
                                                    <option value="Peru" <?php echo ($currentUser['shipping_country'] ?? '') === 'Peru' ? 'selected' : ''; ?>>🇵🇪 Peru</option>
                                                </optgroup>
                                                <optgroup label="Oceania">
                                                    <option value="New Zealand" <?php echo ($currentUser['shipping_country'] ?? '') === 'New Zealand' ? 'selected' : ''; ?>>🇳🇿 New Zealand</option>
                                                </optgroup>
                                                <optgroup label="Other">
                                                    <option value="Other" <?php echo ($currentUser['shipping_country'] ?? '') === 'Other' ? 'selected' : ''; ?>>🌍 Other</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-outline mb-3">
                                        <input type="tel" id="shipping_phone" name="shipping_phone" class="form-control" 
                                               value="<?php echo htmlspecialchars($currentUser['shipping_phone'] ?? ''); ?>"
                                               placeholder="+1 (555) 123-4567">
                                        <label class="form-label" for="shipping_phone">Phone (with country code)</label>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Include country code for international delivery
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" name="update_addresses" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Save Addresses
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Account Security & Preferences -->
    <div class="row mb-4">
        <!-- Account Security -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100" style="border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4"><i class="fas fa-shield-alt me-2" style="color: #667eea;"></i>Account Security</h6>
                    
                    <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                        <div class="rounded-circle p-2 me-3" style="width: 45px; height: 45px; background: #e8ecff; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-key" style="color: #667eea;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1" style="font-size: 0.95rem;">Password</h6>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">Last changed: <?php echo date('M d, Y'); ?></p>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('current_password').scrollIntoView({behavior: 'smooth'});">
                            Change
                        </button>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                        <div class="rounded-circle p-2 me-3" style="width: 45px; height: 45px; background: #e8ecff; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-envelope" style="color: #667eea;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1" style="font-size: 0.95rem;">Email Verification</h6>
                            <p class="mb-0" style="font-size: 0.85rem;">
                                <span class="badge bg-success">Verified</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start">
                        <div class="rounded-circle p-2 me-3" style="width: 45px; height: 45px; background: #e8ecff; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clock" style="color: #667eea;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1" style="font-size: 0.95rem;">Last Login</h6>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;"><?php echo date('M d, Y g:i A'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Preferences & Newsletter -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100" style="border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4"><i class="fas fa-cog me-2" style="color: #667eea;"></i>Preferences & Notifications</h6>
                    
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-1" style="font-size: 0.95rem;">Newsletter Subscription</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">Get updates on new products & offers</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="newsletter" checked style="cursor: pointer;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-1" style="font-size: 0.95rem;">Order Updates</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">Email notifications for order status</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="order_updates" checked style="cursor: pointer;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-1" style="font-size: 0.95rem;">Promotional Emails</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">Special deals and promotions</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="promotions" checked style="cursor: pointer;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-0" style="background: #e8ecff; border: none; font-size: 0.85rem;">
                        <i class="fas fa-info-circle me-2"></i>
                        You can manage all email preferences at any time
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-link me-2"></i>Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="<?php echo SITE_URL; ?>/orders" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-box fa-lg mb-2 d-block"></i>
                                <small>My Orders</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="<?php echo SITE_URL; ?>/products" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-shopping-bag fa-lg mb-2 d-block"></i>
                                <small>Shop</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="<?php echo SITE_URL; ?>/cart" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-shopping-cart fa-lg mb-2 d-block"></i>
                                <small>Cart</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="https://globalsell.site/track-order" target="_blank" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-truck fa-lg mb-2 d-block"></i>
                                <small>Track Order</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="<?php echo SITE_URL; ?>/support" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-headset fa-lg mb-2 d-block"></i>
                                <small>Support</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-outline-danger w-100 py-3">
                                <i class="fas fa-sign-out-alt fa-lg mb-2 d-block"></i>
                                <small>Logout</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Card Animations */
.stat-card {
    animation: fadeInUp 0.6s ease-out;
}

.stat-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2) !important;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

/* Alert Animation */
@keyframes slideInDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Button Hover Effects */
.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Form Input Focus */
.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    border-color: #667eea;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.form-control,
.form-select {
    transition: all 0.3s ease;
}

/* Card Hover Effects */
.card {
    transition: all 0.3s ease;
}

.card:not(.stat-card):hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

/* Smooth Scrollbar */
::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Loading State for Forms */
.btn-primary:disabled,
.btn-success:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* International Banner Styling */
.alert-info {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
}
</style>

<script>
function toggleShippingAddress() {
    const checkbox = document.getElementById('same_as_billing');
    const shippingFields = document.getElementById('shipping_fields');
    
    if (checkbox.checked) {
        shippingFields.style.display = 'none';
    } else {
        shippingFields.style.display = 'block';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>