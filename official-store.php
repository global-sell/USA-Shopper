<?php
// Redirect to external official store
header("Location: https://globalsell.site/storefront/home?parent=HOME&rt=HOME");
exit();

$db = Database::getInstance();

// Get all products
$products = $db->query("SELECT p.*, c.name as category_name 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.status = 'active' 
                       ORDER BY p.created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container py-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">
            <i class="fas fa-store me-3"></i>Official US Shopper Store
        </h1>
        <p class="lead text-muted">Authentic products, verified sellers, guaranteed quality</p>
        <div class="d-flex justify-content-center gap-3 mt-4">
            <span class="badge bg-success p-2"><i class="fas fa-check-circle me-1"></i>Verified Products</span>
            <span class="badge bg-primary p-2"><i class="fas fa-shield-alt me-1"></i>Secure Shopping</span>
            <span class="badge bg-warning p-2"><i class="fas fa-shipping-fast me-1"></i>Fast Delivery</span>
        </div>
    </div>
    
    <!-- Features -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-certificate fa-3x text-primary"></i>
                    </div>
                    <h6 class="fw-bold">100% Authentic</h6>
                    <p class="text-muted small mb-0">All products are genuine and verified</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-award fa-3x text-success"></i>
                    </div>
                    <h6 class="fw-bold">Quality Guaranteed</h6>
                    <p class="text-muted small mb-0">Top-rated products only</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-truck fa-3x text-warning"></i>
                    </div>
                    <h6 class="fw-bold">Free Shipping</h6>
                    <p class="text-muted small mb-0">On orders over $50</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-undo-alt fa-3x text-info"></i>
                    </div>
                    <h6 class="fw-bold">Easy Returns</h6>
                    <p class="text-muted small mb-0">30-day return policy</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Products Section -->
    <div class="mb-4">
        <h3 class="fw-bold mb-4">Featured Products</h3>
    </div>
    
    <?php if (empty($products)): ?>
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h4 class="mb-3">No Products Available</h4>
            <p class="text-muted">Check back soon for amazing products!</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <?php 
                $screenshots = json_decode($product['screenshots'], true);
                $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/300x300/4CAF50/ffffff?text=' . urlencode(substr($product['title'], 0, 2));
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        <a href="<?php echo SITE_URL; ?>/<?php echo $product['slug']; ?>" class="text-decoration-none">
                            <div class="position-relative">
                                <img src="<?php echo $firstImage; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($product['title']); ?>"
                                     style="height: 200px; object-fit: cover; cursor: pointer;"
                                     onerror="this.src='https://via.placeholder.com/300x300/4CAF50/ffffff?text=Product'">
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                    <i class="fas fa-certificate"></i> Official
                                </span>
                            </div>
                        </a>
                        <div class="card-body">
                            <?php if ($product['category_name']): ?>
                                <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <?php endif; ?>
                            <h6 class="card-title fw-bold" style="height: 40px; overflow: hidden;">
                                <?php echo htmlspecialchars($product['title']); ?>
                            </h6>
                            <div class="mb-2">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <small class="text-muted ms-1">(<?php echo $product['sold']; ?>)</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-primary fw-bold mb-0"><?php echo formatPrice($product['price']); ?></h5>
                                <?php if ($product['sold'] > 50): ?>
                                    <span class="badge bg-success">Best Seller</span>
                                <?php endif; ?>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="<?php echo SITE_URL; ?>/<?php echo $product['slug']; ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                <?php if (isLoggedIn()): ?>
                                    <button onclick="addToCart(<?php echo $product['id']; ?>)" 
                                            class="btn btn-primary btn-sm">
                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                    </button>
                                <?php else: ?>
                                    <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <small class="text-muted">
                                <i class="fas fa-shopping-bag me-1"></i><?php echo $product['sold']; ?> sold
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Trust Badges -->
    <div class="card mt-5 border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body p-5 text-center text-white">
            <h3 class="fw-bold mb-4">Why Shop at Our Official Store?</h3>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <i class="fas fa-lock fa-3x mb-3"></i>
                    <h5 class="fw-bold">Secure Payments</h5>
                    <p class="mb-0">Your transactions are protected with industry-standard encryption</p>
                </div>
                <div class="col-md-4 mb-3">
                    <i class="fas fa-headset fa-3x mb-3"></i>
                    <h5 class="fw-bold">24/7 Support</h5>
                    <p class="mb-0">Our customer service team is always ready to help you</p>
                </div>
                <div class="col-md-4 mb-3">
                    <i class="fas fa-medal fa-3x mb-3"></i>
                    <h5 class="fw-bold">Best Prices</h5>
                    <p class="mb-0">Competitive pricing with exclusive deals and discounts</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
