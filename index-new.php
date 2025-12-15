<?php
$pageTitle = "Home";
require_once 'includes/header.php';

// Fetch data
$db = Database::getInstance();
$featuredProducts = $db->query("SELECT * FROM products WHERE status = 'active' ORDER BY downloads DESC LIMIT 12")->fetch_all(MYSQLI_ASSOC);
$newProducts = $db->query("SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 12")->fetch_all(MYSQLI_ASSOC);
$categories = $db->query("SELECT * FROM categories WHERE status = 'active' LIMIT 8")->fetch_all(MYSQLI_ASSOC);
?>

<style>
.hero-carousel {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 400px;
}

.category-box {
    background: white;
    border-radius: 4px;
    padding: 20px;
    height: 100%;
    transition: transform 0.2s;
}

.category-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 15px;
}

.category-item {
    position: relative;
    overflow: hidden;
    border-radius: 4px;
    aspect-ratio: 1;
}

.category-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-item-label {
    position: absolute;
    bottom: 8px;
    left: 8px;
    background: rgba(255,255,255,0.95);
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.product-scroll {
    display: flex;
    overflow-x: auto;
    gap: 15px;
    padding: 10px 0;
    scroll-behavior: smooth;
}

.product-scroll::-webkit-scrollbar {
    height: 8px;
}

.product-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.product-scroll::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.product-scroll::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.product-card-small {
    min-width: 200px;
    max-width: 200px;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s;
    cursor: pointer;
}

.product-card-small:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.product-card-small img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.product-card-small .p-body {
    padding: 12px;
}

.product-card-small .p-title {
    font-size: 14px;
    font-weight: 600;
    height: 40px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    margin-bottom: 8px;
}

.product-card-small .p-price {
    font-size: 18px;
    font-weight: 700;
    color: #B12704;
}

.product-card-small .p-rating {
    font-size: 12px;
    color: #FFA41C;
    margin-bottom: 5px;
}

.section-title {
    font-size: 21px;
    font-weight: 700;
    margin-bottom: 15px;
}

.see-more-link {
    color: #007185;
    font-size: 14px;
    text-decoration: none;
}

.see-more-link:hover {
    color: #C7511F;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .category-box {
        padding: 15px;
    }
    
    .product-card-small {
        min-width: 150px;
        max-width: 150px;
    }
    
    .product-card-small img {
        height: 150px;
    }
}
</style>

<!-- Hero Carousel -->
<section class="hero-carousel py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold text-white mb-3">Digital Products Under $50</h1>
                <p class="lead text-white mb-4">Premium themes, templates, apps & more at unbeatable prices</p>
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-warning btn-lg px-5">
                    Shop now
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Category Cards Section -->
<section class="py-4" style="background: #F7F8F8;">
    <div class="container">
        <div class="row g-3">
            <?php 
            $categoryGroups = [
                [
                    'title' => 'Shop for your digital needs',
                    'items' => array_slice($categories, 0, 4),
                    'link' => 'products.php'
                ],
                [
                    'title' => 'Get your project started',
                    'items' => array_slice($categories, 0, 4),
                    'link' => 'products.php'
                ],
                [
                    'title' => 'Trending digital products',
                    'items' => array_slice($categories, 0, 4),
                    'link' => 'products.php'
                ],
                [
                    'title' => 'Top categories',
                    'items' => array_slice($categories, 0, 4),
                    'link' => 'products.php'
                ]
            ];
            
            foreach ($categoryGroups as $group): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="category-box">
                        <h5 class="fw-bold mb-3"><?php echo $group['title']; ?></h5>
                        <div class="category-grid">
                            <?php foreach ($group['items'] as $cat): ?>
                                <div class="category-item">
                                    <img src="https://via.placeholder.com/150x150/<?php echo sprintf('%06X', mt_rand(0, 0xFFFFFF)); ?>/ffffff?text=<?php echo urlencode(substr($cat['name'], 0, 1)); ?>" 
                                         alt="<?php echo htmlspecialchars($cat['name']); ?>">
                                    <div class="category-item-label"><?php echo htmlspecialchars($cat['name']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/<?php echo $group['link']; ?>" class="see-more-link d-block mt-3">
                            See more
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Horizontal Product Scroll - Best Sellers -->
<section class="py-4 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="section-title mb-0">Best Sellers in Digital Products</h2>
            <a href="<?php echo SITE_URL; ?>/products.php" class="see-more-link">See more</a>
        </div>
        
        <div class="product-scroll">
            <?php foreach ($featuredProducts as $product): ?>
                <?php 
                $screenshots = json_decode($product['screenshots'], true);
                $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/200x200/4CAF50/ffffff?text=' . urlencode(substr($product['title'], 0, 1));
                ?>
                <div class="product-card-small" onclick="window.location.href='<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>'">
                    <img src="<?php echo $firstImage; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                    <div class="p-body">
                        <div class="p-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span class="text-muted">(<?php echo $product['sold']; ?>)</span>
                        </div>
                        <div class="p-title"><?php echo htmlspecialchars($product['title']); ?></div>
                        <div class="p-price"><?php echo formatPrice($product['price']); ?></div>
                        <?php if ($product['sold'] > 50): ?>
                            <small class="text-muted">Best Seller</small>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Category Boxes - Second Row -->
<section class="py-4" style="background: #F7F8F8;">
    <div class="container">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="category-box">
                    <h5 class="fw-bold mb-3">Gear up to get fit</h5>
                    <div class="category-grid">
                        <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
                            <div class="category-item">
                                <img src="https://via.placeholder.com/150x150/FF6B6B/ffffff?text=<?php echo urlencode(substr($cat['name'], 0, 1)); ?>" 
                                     alt="<?php echo htmlspecialchars($cat['name']); ?>">
                                <div class="category-item-label"><?php echo htmlspecialchars($cat['name']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/products.php" class="see-more-link d-block mt-3">Discover more</a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="category-box">
                    <h5 class="fw-bold mb-3">Most-loved digital essentials</h5>
                    <div class="category-grid">
                        <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
                            <div class="category-item">
                                <img src="https://via.placeholder.com/150x150/4ECDC4/ffffff?text=<?php echo urlencode(substr($cat['name'], 0, 1)); ?>" 
                                     alt="<?php echo htmlspecialchars($cat['name']); ?>">
                                <div class="category-item-label"><?php echo htmlspecialchars($cat['name']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/products.php" class="see-more-link d-block mt-3">Discover more</a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="category-box">
                    <h5 class="fw-bold mb-3">Elevate your Projects</h5>
                    <div class="category-grid">
                        <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
                            <div class="category-item">
                                <img src="https://via.placeholder.com/150x150/95E1D3/ffffff?text=<?php echo urlencode(substr($cat['name'], 0, 1)); ?>" 
                                     alt="<?php echo htmlspecialchars($cat['name']); ?>">
                                <div class="category-item-label"><?php echo htmlspecialchars($cat['name']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/products.php" class="see-more-link d-block mt-3">Discover more</a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="category-box">
                    <h5 class="fw-bold mb-3">Premium & Business</h5>
                    <div class="category-grid">
                        <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
                            <div class="category-item">
                                <img src="https://via.placeholder.com/150x150/F38181/ffffff?text=<?php echo urlencode(substr($cat['name'], 0, 1)); ?>" 
                                     alt="<?php echo htmlspecialchars($cat['name']); ?>">
                                <div class="category-item-label"><?php echo htmlspecialchars($cat['name']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/products.php" class="see-more-link d-block mt-3">Shop now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals Horizontal Scroll -->
<section class="py-4 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="section-title mb-0">New Arrivals</h2>
            <a href="<?php echo SITE_URL; ?>/products.php?sort=latest" class="see-more-link">See more</a>
        </div>
        
        <div class="product-scroll">
            <?php foreach ($newProducts as $product): ?>
                <?php 
                $screenshots = json_decode($product['screenshots'], true);
                $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/200x200/2196F3/ffffff?text=' . urlencode(substr($product['title'], 0, 1));
                ?>
                <div class="product-card-small" onclick="window.location.href='<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>'">
                    <div class="position-relative">
                        <img src="<?php echo $firstImage; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2" style="font-size: 10px;">NEW</span>
                    </div>
                    <div class="p-body">
                        <div class="p-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span class="text-muted">(<?php echo rand(10, 100); ?>)</span>
                        </div>
                        <div class="p-title"><?php echo htmlspecialchars($product['title']); ?></div>
                        <div class="p-price"><?php echo formatPrice($product['price']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Trust Badges -->
<section class="py-4" style="background: #F7F8F8;">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <i class="fas fa-shipping-fast fa-3x text-primary mb-2"></i>
                    <h6 class="fw-bold mb-1">Instant Download</h6>
                    <small class="text-muted">Get your products immediately</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <i class="fas fa-shield-alt fa-3x text-success mb-2"></i>
                    <h6 class="fw-bold mb-1">Secure Payment</h6>
                    <small class="text-muted">100% secure transactions</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <i class="fas fa-undo fa-3x text-warning mb-2"></i>
                    <h6 class="fw-bold mb-1">Money Back</h6>
                    <small class="text-muted">30-day refund guarantee</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <i class="fas fa-headset fa-3x text-info mb-2"></i>
                    <h6 class="fw-bold mb-1">24/7 Support</h6>
                    <small class="text-muted">Always here to help</small>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
