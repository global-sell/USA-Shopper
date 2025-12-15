<?php
$pageTitle = "Home";
require_once 'includes/header.php';

// Fetch featured products
$db = Database::getInstance();
$featuredProducts = $db->query("SELECT * FROM products WHERE status = 'active' ORDER BY downloads DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);

// Fetch categories
$categories = $db->query("SELECT * FROM categories WHERE status = 'active' LIMIT 6")->fetch_all(MYSQLI_ASSOC);
?>

<!-- Hero Section -->
<section class="hero-section py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold mb-3 fade-in">Premium Digital Products</h1>
                <p class="lead mb-4 fade-in">Discover high-quality themes, templates, apps, and digital resources for your projects.</p>
                <div class="fade-in">
                    <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-light btn-lg me-2">
                        <i class="fas fa-shopping-bag me-2"></i>Explore Products
                    </a>
                    <?php if (!isLoggedIn()): ?>
                        <a href="<?php echo SITE_URL; ?>/signup.php" class="btn btn-outline-light btn-lg">
                            Get Started
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 fade-in">
                <img src="https://via.placeholder.com/600x400/667eea/ffffff?text=Digital+Products" 
                     alt="Digital Products" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4 fw-bold">Browse Categories</h2>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <div class="col-md-4 col-sm-6">
                    <a href="<?php echo SITE_URL; ?>/products.php?category=<?php echo $category['slug']; ?>" 
                       class="text-decoration-none">
                        <div class="card category-card h-100 text-center p-4">
                            <div class="mb-3">
                                <?php if (isset($category['icon']) && !empty($category['icon'])): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/categories/<?php echo $category['icon']; ?>" 
                                         alt="<?php echo htmlspecialchars($category['name']); ?>" 
                                         style="width: 80px; height: 80px; object-fit: contain; margin: 0 auto;">
                                <?php else: ?>
                                    <i class="fas fa-folder-open fa-3x text-primary"></i>
                                <?php endif; ?>
                            </div>
                            <h5 class="fw-bold"><?php echo htmlspecialchars($category['name']); ?></h5>
                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($category['description']); ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Featured Products</h2>
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline-primary">View All</a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card product-card">
                        <?php 
                        $screenshots = json_decode($product['screenshots'], true);
                        $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/400x300/4CAF50/ffffff?text=' . urlencode($product['title']);
                        ?>
                        <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                            <img src="<?php echo $firstImage; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['title']); ?>" style="cursor: pointer;">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($product['title']); ?></h5>
                            <p class="card-text text-muted small"><?php echo substr(htmlspecialchars($product['description']), 0, 100); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 text-primary fw-bold"><?php echo formatPrice($product['price']); ?></span>
                                <div>
                                    <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (isLoggedIn()): ?>
                                        <button onclick="addToCart(<?php echo $product['id']; ?>)" 
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    <?php else: ?>
                                        <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-sm btn-primary">
                                            <i class="fas fa-cart-plus"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <small class="text-muted">
                                <i class="fas fa-shopping-bag me-1"></i><?php echo $product['sold']; ?> sold
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">Why Choose Us?</h2>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-shield-alt fa-3x text-primary"></i>
                </div>
                <h5 class="fw-bold">Secure Downloads</h5>
                <p class="text-muted">All products are scanned and verified for security</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-sync-alt fa-3x text-primary"></i>
                </div>
                <h5 class="fw-bold">Lifetime Updates</h5>
                <p class="text-muted">Get free updates for all purchased products</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-headset fa-3x text-primary"></i>
                </div>
                <h5 class="fw-bold">24/7 Support</h5>
                <p class="text-muted">Our support team is always ready to help</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">What Our Customers Say</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Amazing quality products! The WordPress theme I purchased exceeded my expectations."</p>
                        <div class="d-flex align-items-center mt-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <span class="fw-bold">JD</span>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold">John Doe</h6>
                                <small class="text-muted">Web Developer</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Great customer support and instant downloads. Highly recommended!"</p>
                        <div class="d-flex align-items-center mt-3">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <span class="fw-bold">SM</span>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold">Sarah Miller</h6>
                                <small class="text-muted">Designer</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Best marketplace for digital products. Clean interface and easy checkout process."</p>
                        <div class="d-flex align-items-center mt-3">
                            <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <span class="fw-bold">MJ</span>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold">Mike Johnson</h6>
                                <small class="text-muted">Entrepreneur</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center text-white">
        <h2 class="fw-bold mb-3">Ready to Get Started?</h2>
        <p class="lead mb-4">Join thousands of satisfied customers and start downloading premium digital products today!</p>
        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-light btn-lg">
            <i class="fas fa-rocket me-2"></i>Browse Products
        </a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
