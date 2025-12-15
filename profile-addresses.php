<?php
$pageTitle = "My Addresses";
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$db = Database::getInstance();
$error = '';
$success = '';

// Get current user with addresses
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

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
        // Copy billing to shipping
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
        // Refresh user data
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    } else {
        $error = 'Failed to update addresses';
    }
}
?>

<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">My Account</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?php echo SITE_URL; ?>/profile.php" class="text-decoration-none">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo SITE_URL; ?>/profile-addresses.php" class="text-decoration-none text-primary fw-bold">
                                <i class="fas fa-map-marker-alt me-2"></i>Addresses
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo SITE_URL; ?>/orders.php" class="text-decoration-none">
                                <i class="fas fa-box me-2"></i>Orders
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo SITE_URL; ?>/support.php" class="text-decoration-none">
                                <i class="fas fa-headset me-2"></i>Support
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/logout.php" class="text-decoration-none text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <h2 class="fw-bold mb-4">My Addresses</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <!-- Billing Address -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Billing Address</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-outline">
                                    <textarea id="billing_address" name="billing_address" class="form-control" rows="3"><?php echo htmlspecialchars($user['billing_address'] ?? ''); ?></textarea>
                                    <label class="form-label" for="billing_address">Street Address</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="text" id="billing_city" name="billing_city" class="form-control" value="<?php echo htmlspecialchars($user['billing_city'] ?? ''); ?>">
                                    <label class="form-label" for="billing_city">City</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="text" id="billing_state" name="billing_state" class="form-control" value="<?php echo htmlspecialchars($user['billing_state'] ?? ''); ?>">
                                    <label class="form-label" for="billing_state">State/Province</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="text" id="billing_zip" name="billing_zip" class="form-control" value="<?php echo htmlspecialchars($user['billing_zip'] ?? ''); ?>">
                                    <label class="form-label" for="billing_zip">ZIP/Postal Code</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="text" id="billing_country" name="billing_country" class="form-control" value="<?php echo htmlspecialchars($user['billing_country'] ?? 'USA'); ?>">
                                    <label class="form-label" for="billing_country">Country</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-outline">
                                    <input type="tel" id="billing_phone" name="billing_phone" class="form-control" value="<?php echo htmlspecialchars($user['billing_phone'] ?? ''); ?>">
                                    <label class="form-label" for="billing_phone">Phone Number</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Same as Billing Checkbox -->
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="same_as_billing" name="same_as_billing" 
                           <?php echo ($user['same_as_billing'] ?? true) ? 'checked' : ''; ?>
                           onchange="toggleShippingAddress()">
                    <label class="form-check-label" for="same_as_billing">
                        Shipping address is the same as billing address
                    </label>
                </div>
                
                <!-- Shipping Address -->
                <div class="card mb-4" id="shipping_address_section" style="display: <?php echo ($user['same_as_billing'] ?? true) ? 'none' : 'block'; ?>;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-outline">
                                    <textarea id="shipping_address" name="shipping_address" class="form-control" rows="3"><?php echo htmlspecialchars($user['shipping_address'] ?? ''); ?></textarea>
                                    <label class="form-label" for="shipping_address">Street Address</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="text" id="shipping_city" name="shipping_city" class="form-control" value="<?php echo htmlspecialchars($user['shipping_city'] ?? ''); ?>">
                                    <label class="form-label" for="shipping_city">City</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="text" id="shipping_state" name="shipping_state" class="form-control" value="<?php echo htmlspecialchars($user['shipping_state'] ?? ''); ?>">
                                    <label class="form-label" for="shipping_state">State/Province</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="text" id="shipping_zip" name="shipping_zip" class="form-control" value="<?php echo htmlspecialchars($user['shipping_zip'] ?? ''); ?>">
                                    <label class="form-label" for="shipping_zip">ZIP/Postal Code</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="text" id="shipping_country" name="shipping_country" class="form-control" value="<?php echo htmlspecialchars($user['shipping_country'] ?? 'USA'); ?>">
                                    <label class="form-label" for="shipping_country">Country</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-outline">
                                    <input type="tel" id="shipping_phone" name="shipping_phone" class="form-control" value="<?php echo htmlspecialchars($user['shipping_phone'] ?? ''); ?>">
                                    <label class="form-label" for="shipping_phone">Phone Number</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="update_addresses" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Save Addresses
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleShippingAddress() {
    const checkbox = document.getElementById('same_as_billing');
    const shippingSection = document.getElementById('shipping_address_section');
    
    if (checkbox.checked) {
        shippingSection.style.display = 'none';
    } else {
        shippingSection.style.display = 'block';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
