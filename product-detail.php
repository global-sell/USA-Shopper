<?php
require_once 'config/config.php';

$db = Database::getInstance();

// Function to format numbers in K/M format
function formatSoldCount($number) {
    if ($number >= 1000000) {
        $formatted = $number / 1000000;
        // Check if it's a whole number
        if ($formatted == floor($formatted)) {
            return intval($formatted) . 'M';
        }
        return number_format($formatted, 1) . 'M';
    } elseif ($number >= 1000) {
        $formatted = $number / 1000;
        // Check if it's a whole number
        if ($formatted == floor($formatted)) {
            return intval($formatted) . 'K';
        }
        return number_format($formatted, 1) . 'K';
    }
    return $number;
}

// Get product by slug or id (backward compatibility)
$slug = sanitizeInput($_GET['slug'] ?? '');
$productId = (int)($_GET['id'] ?? 0);

if (!empty($slug)) {
    // Get product by slug
    $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         WHERE p.slug = ? AND p.status = 'active'");
    $stmt->bind_param("s", $slug);
} elseif ($productId > 0) {
    // Get product by id (backward compatibility)
    $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         WHERE p.id = ? AND p.status = 'active'");
    $stmt->bind_param("i", $productId);
} else {
    redirect(SITE_URL . '/products.php');
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect(SITE_URL . '/products.php');
}

$product = $result->fetch_assoc();
$productId = $product['id'];

// Redirect to slug-based URL if accessed via id
if (empty($slug) && !empty($product['slug'])) {
    redirect(SITE_URL . '/' . $product['slug']);
}

// Update view count
$db->query("UPDATE products SET views = views + 1 WHERE id = $productId");

// Check if user owns this product
$userOwnsProduct = false;
if (isLoggedIn()) {
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM order_items oi 
                         JOIN orders o ON oi.order_id = o.id 
                         WHERE o.user_id = ? AND oi.product_id = ? AND o.payment_status = 'completed'");
    $stmt->bind_param("ii", $_SESSION['user_id'], $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $userOwnsProduct = $row['count'] > 0;
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && isLoggedIn()) {
    $rating = (int)($_POST['rating'] ?? 0);
    $reviewTitle = sanitizeInput($_POST['review_title'] ?? '');
    $reviewText = sanitizeInput($_POST['review_text'] ?? '');
    
    if ($rating >= 1 && $rating <= 5 && !empty($reviewText)) {
        // Check if reviews table exists
        $tableExists = $db->query("SHOW TABLES LIKE 'product_reviews'")->num_rows > 0;
        
        if (!$tableExists) {
            // Create table if it doesn't exist
            $db->query("CREATE TABLE IF NOT EXISTS product_reviews (
                id INT PRIMARY KEY AUTO_INCREMENT,
                product_id INT NOT NULL,
                user_id INT NOT NULL,
                rating INT NOT NULL,
                review_title VARCHAR(255),
                review_text TEXT,
                status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_product (user_id, product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }
        
        $stmt = $db->prepare("INSERT INTO product_reviews (product_id, user_id, rating, review_title, review_text) 
                             VALUES (?, ?, ?, ?, ?) 
                             ON DUPLICATE KEY UPDATE rating = ?, review_title = ?, review_text = ?, updated_at = CURRENT_TIMESTAMP");
        $stmt->bind_param("iiississ", $productId, $_SESSION['user_id'], $rating, $reviewTitle, $reviewText, $rating, $reviewTitle, $reviewText);
        
        if ($stmt->execute()) {
            $_SESSION['review_success'] = 'Thank you for your review!';
            redirect($_SERVER['REQUEST_URI']);
        }
    }
}

// Get product reviews
$reviews = [];
$avgRating = 0;
$totalReviews = 0;

$tableExists = $db->query("SHOW TABLES LIKE 'product_reviews'")->num_rows > 0;
if ($tableExists) {
    $stmt = $db->prepare("SELECT r.*, u.name as user_name 
                         FROM product_reviews r 
                         JOIN users u ON r.user_id = u.id 
                         WHERE r.product_id = ? AND r.status = 'approved' 
                         ORDER BY r.created_at DESC");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Calculate average rating
    if (!empty($reviews)) {
        $totalRating = array_sum(array_column($reviews, 'rating'));
        $totalReviews = count($reviews);
        $avgRating = $totalRating / $totalReviews;
    }
    
    // Check if user has already reviewed
    $userHasReviewed = false;
    if (isLoggedIn()) {
        $stmt = $db->prepare("SELECT id FROM product_reviews WHERE product_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $productId, $_SESSION['user_id']);
        $stmt->execute();
        $userHasReviewed = $stmt->get_result()->num_rows > 0;
    }
}

// Get related products
$relatedProducts = [];
if ($product['category_id']) {
    $stmt = $db->prepare("SELECT * FROM products 
                         WHERE category_id = ? AND id != ? AND status = 'active' 
                         ORDER BY sold DESC LIMIT 4");
    $stmt->bind_param("ii", $product['category_id'], $productId);
    $stmt->execute();
    $relatedProducts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$pageTitle = $product['title'];
require_once 'includes/header.php';

$screenshots = json_decode($product['screenshots'], true) ?: [];
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/products.php">Products</a></li>
            <?php if ($product['category_name']): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo SITE_URL; ?>/products.php?category=<?php echo $product['category_slug']; ?>">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['title']); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($screenshots)): ?>
                        <div id="productCarousel" class="carousel slide" data-mdb-ride="carousel">
                            <div class="carousel-inner rounded">
                                <?php foreach ($screenshots as $index => $screenshot): ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <img src="<?php echo UPLOADS_URL . '/screenshots/' . $screenshot; ?>" 
                                             class="d-block w-100" alt="Screenshot <?php echo $index + 1; ?>"
                                             style="max-height: 400px; object-fit: contain;">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($screenshots) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-mdb-target="#productCarousel" data-mdb-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-mdb-target="#productCarousel" data-mdb-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <img src="https://via.placeholder.com/600x400/4CAF50/ffffff?text=<?php echo urlencode($product['title']); ?>" 
                             class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['title']); ?>">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body" style="display: flex; flex-direction: column;">
                    <?php if ($product['category_name']): ?>
                        <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <?php endif; ?>
                    
                    <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($product['title']); ?></h2>
                    
                    <!-- Stats & Rating -->
                    <div class="mb-3">
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="badge bg-primary-subtle text-primary fs-6 py-2 px-3">
                                <i class="fas fa-fire me-2"></i><?php echo formatSoldCount($product['sold']); ?> Sold
                            </span>
                            
                            <!-- Star Rating -->
                            <div class="product-rating-detail">
                                <?php 
                                $rating = 4.5; // You can get this from database
                                for ($i = 1; $i <= 5; $i++): 
                                    if ($i <= floor($rating)): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php elseif ($i - 0.5 <= $rating): ?>
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    <?php else: ?>
                                        <i class="far fa-star text-warning"></i>
                                    <?php endif;
                                endfor; ?>
                                <span class="text-muted ms-2"><?php echo $rating; ?> (<?php echo rand(50, 500); ?> reviews)</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Price with Sale Badge -->
                    <div class="mb-4">
                        <?php 
                        $isNew = (time() - strtotime($product['created_at'])) < (7 * 24 * 60 * 60);
                        $onSale = !empty($product['sale_price']) && $product['sale_price'] < $product['price'];
                        ?>
                        
                        <?php if ($onSale): ?>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="h2 text-danger fw-bold mb-0"><?php echo formatPrice($product['sale_price']); ?></span>
                                <span class="h4 text-muted text-decoration-line-through mb-0"><?php echo formatPrice($product['price']); ?></span>
                                <span class="badge bg-danger">
                                    <?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>% OFF
                                </span>
                            </div>
                        <?php else: ?>
                            <span class="h2 text-primary fw-bold"><?php echo formatPrice($product['price']); ?></span>
                        <?php endif; ?>
                        
                        <?php if ($isNew): ?>
                            <span class="badge bg-success ms-2">NEW ARRIVAL</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Quick Actions (Wishlist & Share) -->
                    <div class="mb-4">
                        <div class="btn-group" role="group">
                            <button onclick="addToWishlist(<?php echo $product['id']; ?>)" class="btn btn-outline-danger" title="Add to Wishlist">
                                <i class="fas fa-heart me-2"></i>Add to Wishlist
                            </button>
                            <button onclick="shareProduct()" class="btn btn-outline-primary" title="Share Product">
                                <i class="fas fa-share-alt me-2"></i>Share
                            </button>
                        </div>
                    </div>
                    
                    <!-- Quick Stats Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <div class="card text-center stats-card">
                                <div class="card-body py-3">
                                    <i class="fas fa-download text-primary fa-2x mb-2"></i>
                                    <h6 class="fw-bold mb-0"><?php echo formatSoldCount($product['sold']); ?></h6>
                                    <small class="text-muted">Downloads</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card text-center stats-card">
                                <div class="card-body py-3">
                                    <i class="fas fa-star text-warning fa-2x mb-2"></i>
                                    <h6 class="fw-bold mb-0"><?php echo $rating; ?></h6>
                                    <small class="text-muted">Rating</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card text-center stats-card">
                                <div class="card-body py-3">
                                    <i class="fas fa-comments text-success fa-2x mb-2"></i>
                                    <h6 class="fw-bold mb-0"><?php echo rand(20, 200); ?></h6>
                                    <small class="text-muted">Reviews</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trust Badges -->
                    <div class="mb-4">
                        <div class="d-flex flex-wrap gap-3 justify-content-center align-items-center p-3 bg-light rounded">
                            <div class="trust-badge">
                                <i class="fas fa-shield-alt text-success me-2"></i>
                                <small class="fw-bold">Secure Payment</small>
                            </div>
                            <div class="trust-badge">
                                <i class="fas fa-undo text-primary me-2"></i>
                                <small class="fw-bold">30-Day Guarantee</small>
                            </div>
                            <div class="trust-badge">
                                <i class="fas fa-headset text-info me-2"></i>
                                <small class="fw-bold">24/7 Support</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Buy Now Button Section - Middle Position -->
                    <div class="mb-4">
                        <?php if ($userOwnsProduct): ?>
                            <a href="<?php echo SITE_URL; ?>/orders.php" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-check-circle me-2"></i>You Own This Product - View Downloads
                            </a>
                        <?php elseif (in_array($product['product_type'] ?? 'simple', ['external', 'affiliate'])): ?>
                            <?php 
                            $externalUrl = !empty($product['affiliate_link']) ? $product['affiliate_link'] : $product['external_url'];
                            $buttonText = !empty($product['button_text']) ? $product['button_text'] : 'Buy Now';
                            ?>
                            <a href="<?php echo htmlspecialchars($externalUrl); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="btn btn-success btn-lg w-100">
                                <i class="fas fa-external-link-alt me-2"></i><?php echo htmlspecialchars($buttonText); ?>
                            </a>
                        <?php elseif (isLoggedIn()): ?>
                            <button onclick="buyNow(<?php echo $product['id']; ?>)" class="btn btn-success btn-lg w-100 mb-2">
                                <i class="fas fa-bolt me-2"></i>Buy Now
                            </button>
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                            </button>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                               class="btn btn-success btn-lg w-100 mb-2">
                                <i class="fas fa-bolt me-2"></i>Buy Now
                            </a>
                            <a href="<?php echo SITE_URL; ?>/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                               class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Description Section -->
                    <div class="mb-4" style="order: 1;">
                        <h5 class="fw-bold mb-2">Description</h5>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                    
                    <?php if ($product['demo_url']): ?>
                        <div class="mb-4">
                            <a href="<?php echo htmlspecialchars($product['demo_url']); ?>" 
                               target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt me-2"></i>View Demo
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Additional Info -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Product Information</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>Instant Download
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>Lifetime Updates
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>24/7 Support
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>Money Back Guarantee
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <!-- Reviews Section -->
    <div class="mt-5">
        <h3 class="fw-bold mb-4">
            <i class="fas fa-star text-warning me-2"></i>Customer Reviews
            <?php if ($totalReviews > 0): ?>
                <span class="badge bg-primary"><?php echo $totalReviews; ?></span>
            <?php endif; ?>
        </h3>
        
        <?php if (isset($_SESSION['review_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['review_success']; unset($_SESSION['review_success']); ?>
                <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($totalReviews > 0): ?>
            <!-- Rating Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <h1 class="display-4 fw-bold mb-0"><?php echo number_format($avgRating, 1); ?></h1>
                            <div class="text-warning mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?php echo $i <= round($avgRating) ? '' : '-o'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-muted mb-0"><?php echo $totalReviews; ?> review<?php echo $totalReviews != 1 ? 's' : ''; ?></p>
                        </div>
                        <div class="col-md-9">
                            <?php
                            // Calculate rating distribution
                            $ratingCounts = array_fill(1, 5, 0);
                            foreach ($reviews as $review) {
                                $ratingCounts[$review['rating']]++;
                            }
                            for ($i = 5; $i >= 1; $i--):
                                $percentage = $totalReviews > 0 ? ($ratingCounts[$i] / $totalReviews) * 100 : 0;
                            ?>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-2" style="width: 60px;"><?php echo $i; ?> star</span>
                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                    <span class="text-muted" style="width: 40px;"><?php echo $ratingCounts[$i]; ?></span>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Write Review Form -->
        <?php if (isLoggedIn()): ?>
            <?php if (!isset($userHasReviewed) || !$userHasReviewed): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Write a Review</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Rating <span class="text-danger">*</span></label>
                                <div class="rating-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" required>
                                        <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Review Title</label>
                                <input type="text" name="review_title" class="form-control" placeholder="Sum up your experience">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Your Review <span class="text-danger">*</span></label>
                                <textarea name="review_text" class="form-control" rows="4" required placeholder="Share your thoughts about this product..."></textarea>
                            </div>
                            <button type="submit" name="submit_review" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Review
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>You have already reviewed this product.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-sign-in-alt me-2"></i>Please <a href="<?php echo SITE_URL; ?>/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">login</a> to write a review.
            </div>
        <?php endif; ?>
        
        <!-- Reviews List -->
        <?php if (!empty($reviews)): ?>
            <h5 class="fw-bold mb-3">All Reviews</h5>
            <?php foreach ($reviews as $review): ?>
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($review['user_name']); ?></h6>
                                <div class="text-warning mb-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?php echo $i <= $review['rating'] ? '' : '-o'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                        </div>
                        <?php if (!empty($review['review_title'])): ?>
                            <h6 class="fw-bold"><?php echo htmlspecialchars($review['review_title']); ?></h6>
                        <?php endif; ?>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif ($totalReviews == 0): ?>
            <div class="text-center py-5">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <p class="text-muted">No reviews yet. Be the first to review this product!</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5">
            <h3 class="fw-bold mb-4">Related Products</h3>
            <div class="row g-4">
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="card product-card h-100">
                            <?php 
                            $relatedScreenshots = json_decode($relatedProduct['screenshots'], true);
                            $relatedImage = !empty($relatedScreenshots) ? UPLOADS_URL . '/screenshots/' . $relatedScreenshots[0] : 'https://via.placeholder.com/400x300/4CAF50/ffffff?text=' . urlencode($relatedProduct['title']);
                            ?>
                            <a href="<?php echo SITE_URL; ?>/<?php echo $relatedProduct['slug']; ?>" class="text-decoration-none">
                                <img src="<?php echo $relatedImage; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($relatedProduct['title']); ?>" style="cursor: pointer;">
                            </a>
                            <div class="card-body">
                                <h6 class="card-title fw-bold"><?php echo htmlspecialchars($relatedProduct['title']); ?></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h6 mb-0 text-primary fw-bold"><?php echo formatPrice($relatedProduct['price']); ?></span>
                                    <a href="<?php echo SITE_URL; ?>/<?php echo $relatedProduct['slug']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Customer Reviews Testimonials -->
    <div class="mt-5">
        <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body p-5 text-white">
                <div class="text-center mb-4">
                    <h2 class="fw-bold mb-2"><i class="fas fa-quote-left me-3"></i>What Our Customers Say</h2>
                    <p class="mb-0">Real feedback from verified buyers</p>
                </div>
                
                <div class="row g-4" id="customerReviews">
                    <!-- Reviews will be loaded here -->
                </div>
                
                <div class="text-center mt-4">
                    <button class="btn btn-light btn-lg" onclick="loadNewCustomerReviews()">
                        <i class="fas fa-random me-2"></i>Show Different Reviews
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Buy Now function - adds to cart and redirects to checkout
function buyNow(productId) {
    // Add to cart via AJAX
    fetch('<?php echo SITE_URL; ?>/api/add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to checkout immediately
            window.location.href = '<?php echo SITE_URL; ?>/checkout.php';
        } else {
            alert(data.message || 'Failed to add product to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>

<style>
/* Star Rating Input */
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 5px;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input label {
    cursor: pointer;
    font-size: 2rem;
    color: #ddd;
    transition: color 0.2s;
}

.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input[type="radio"]:checked ~ label {
    color: #ffc107;
}

.rating-input label i {
    transition: transform 0.2s;
}

.rating-input label:hover i {
    transform: scale(1.2);
}
</style>

<script>
// Wishlist functionality
function addToWishlist(productId) {
    let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    
    if (!wishlist.includes(productId)) {
        wishlist.push(productId);
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        showNotification('❤️ Added to wishlist!', 'success');
        
        // Confetti effect
        showConfetti();
    } else {
        showNotification('Already in wishlist', 'info');
    }
}

// Share Product
function shareProduct() {
    const title = '<?php echo addslashes($product['title']); ?>';
    const url = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).then(() => {
            showNotification('✓ Shared successfully!', 'success');
        }).catch((error) => {
            console.log('Share failed:', error);
            copyToClipboard(url);
        });
    } else {
        copyToClipboard(url);
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('🔗 Link copied to clipboard!', 'success');
    });
}

// Show Notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} notification-toast`;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 250px; animation: slideInRight 0.3s ease;';
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Confetti effect
function showConfetti() {
    const colors = ['#667eea', '#764ba2', '#f093fb', '#48bb78', '#fbbf24'];
    for (let i = 0; i < 50; i++) {
        createConfetti(colors[Math.floor(Math.random() * colors.length)]);
    }
}

function createConfetti(color) {
    const confetti = document.createElement('div');
    confetti.style.position = 'fixed';
    confetti.style.width = '10px';
    confetti.style.height = '10px';
    confetti.style.backgroundColor = color;
    confetti.style.left = Math.random() * window.innerWidth + 'px';
    confetti.style.top = '-10px';
    confetti.style.opacity = '1';
    confetti.style.zIndex = '9999';
    confetti.style.borderRadius = '50%';
    document.body.appendChild(confetti);
    
    let pos = -10;
    const fall = setInterval(() => {
        if (pos >= window.innerHeight) {
            clearInterval(fall);
            confetti.remove();
        } else {
            pos += 5;
            confetti.style.top = pos + 'px';
            confetti.style.opacity = 1 - (pos / window.innerHeight);
        }
    }, 20);
}

// Add animations on scroll
document.addEventListener('DOMContentLoaded', function() {
    // Animate elements
    const animatedElements = document.querySelectorAll('.card, .badge, .btn');
    animatedElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        setTimeout(() => {
            el.style.transition = 'all 0.5s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 50);
    });
});

// Image zoom on hover
document.querySelectorAll('.carousel-item img').forEach(img => {
    img.addEventListener('mousemove', function(e) {
        const rect = this.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;
        this.style.transformOrigin = `${x}% ${y}%`;
        this.style.transform = 'scale(1.3)';
    });
    
    img.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
});
</script>

<style>
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.product-rating-detail i {
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.product-rating-detail i:hover {
    transform: scale(1.2) rotate(10deg);
}

.btn-group .btn {
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.carousel-item img {
    transition: transform 0.3s ease;
    cursor: zoom-in;
}

.badge {
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stats Cards */
.stats-card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    cursor: default;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.stats-card i {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Trust Badges */
.trust-badge {
    display: flex;
    align-items: center;
    padding: 5px 10px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.trust-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.trust-badge i {
    font-size: 1.2rem;
}

/* Sale badge pulse */
.badge.bg-danger {
    animation: pulseBadge 1.5s infinite;
}

@keyframes pulseBadge {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
    }
}

/* Share button glow */
.btn-outline-primary:hover {
    box-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
}

.btn-outline-danger:hover {
    box-shadow: 0 0 20px rgba(220, 38, 38, 0.5);
}

/* Price glow effect */
.h2.text-danger {
    text-shadow: 0 0 10px rgba(220, 38, 38, 0.3);
    animation: priceGlow 2s infinite;
}

@keyframes priceGlow {
    0%, 100% {
        text-shadow: 0 0 10px rgba(220, 38, 38, 0.3);
    }
    50% {
        text-shadow: 0 0 20px rgba(220, 38, 38, 0.6);
    }
}

/* Customer Reviews Styling */
.review-card {
    transition: all 0.3s ease;
    border: 2px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
}

.review-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    border-color: rgba(255,255,255,0.3);
}

.review-card .fa-star {
    transition: all 0.3s ease;
}

.review-card:hover .fa-star {
    transform: scale(1.2);
}

#customerReviews .col-md-4 {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

#customerReviews .col-md-4:nth-child(1) {
    animation-delay: 0.1s;
}

#customerReviews .col-md-4:nth-child(2) {
    animation-delay: 0.2s;
}

#customerReviews .col-md-4:nth-child(3) {
    animation-delay: 0.3s;
}
</style>

<script>
// Customer Reviews Database
const customerReviewsData = [
    {name: "Sarah Johnson", location: "New York, USA", rating: 5, review: "Amazing product! Exactly as described and delivered quickly. Very satisfied with my purchase!", avatar: "S", color: "#667eea"},
    {name: "Michael Chen", location: "San Francisco, USA", rating: 5, review: "Best purchase I've made this year! Quality is outstanding and customer service was excellent.", avatar: "M", color: "#f093fb"},
    {name: "Emily Rodriguez", location: "London, UK", rating: 5, review: "Absolutely love it! Great quality and works perfectly. Highly recommend to everyone!", avatar: "E", color: "#48bb78"},
    {name: "David Park", location: "Seoul, Korea", rating: 4, review: "Very good product! Worth the price. Fast shipping and well packaged. Will buy again.", avatar: "D", color: "#fbbf24"},
    {name: "Jessica Williams", location: "Toronto, Canada", rating: 5, review: "Exceeded my expectations! The quality is superb and it arrived earlier than expected.", avatar: "J", color: "#764ba2"},
    {name: "Ahmed Hassan", location: "Dubai, UAE", rating: 5, review: "Fantastic product! Exactly what I needed. Great value for money and excellent service.", avatar: "A", color: "#f5576c"},
    {name: "Sophie Martin", location: "Paris, France", rating: 5, review: "Perfect! The quality is amazing and it works flawlessly. Very happy with this purchase!", avatar: "S", color: "#17a2b8"},
    {name: "Carlos Silva", location: "São Paulo, Brazil", rating: 4, review: "Great product! Good quality and fast delivery. Would definitely recommend to friends.", avatar: "C", color: "#dc2626"},
    {name: "Lisa Anderson", location: "Sydney, Australia", rating: 5, review: "Love it! Better than I expected. The quality is excellent and shipping was super fast.", avatar: "L", color: "#9C27B0"},
    {name: "Kevin O'Brien", location: "Dublin, Ireland", rating: 5, review: "Excellent product! Works perfectly and arrived in perfect condition. Very satisfied!", avatar: "K", color: "#2196F3"},
    {name: "Priya Sharma", location: "Mumbai, India", rating: 5, review: "Outstanding quality! Exactly as shown in pictures. Very pleased with my purchase.", avatar: "P", color: "#FF9800"},
    {name: "Thomas Mueller", location: "Berlin, Germany", rating: 4, review: "Very good! Quality is great and delivery was prompt. Would buy from this seller again.", avatar: "T", color: "#4CAF50"},
    {name: "Maria Garcia", location: "Madrid, Spain", rating: 5, review: "Amazing! Best quality I've seen. Fast shipping and excellent customer support.", avatar: "M", color: "#E91E63"},
    {name: "James Taylor", location: "Los Angeles, USA", rating: 5, review: "Perfect purchase! High quality and works great. Shipping was fast and packaging secure.", avatar: "J", color: "#00BCD4"},
    {name: "Yuki Tanaka", location: "Tokyo, Japan", rating: 5, review: "Excellent product! Superior quality and arrived quickly. Very happy with this buy!", avatar: "Y", color: "#673AB7"}
];

// Load Customer Reviews
function loadCustomerReviews() {
    const container = document.getElementById('customerReviews');
    if (!container) return;
    
    const randomReviews = getRandomCustomerReviews(3);
    container.innerHTML = randomReviews.map(review => `
        <div class="col-md-4">
            <div class="review-card p-4 rounded-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px; background: rgba(255,255,255,0.2);">
                        <span class="fw-bold">${review.avatar}</span>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">${review.name}</h6>
                        <small><i class="fas fa-map-marker-alt me-1"></i>${review.location}</small>
                    </div>
                </div>
                <div class="mb-3">${generateReviewStars(review.rating)}</div>
                <p class="mb-0">"${review.review}"</p>
            </div>
        </div>
    `).join('');
    
    container.style.opacity = '0';
    setTimeout(() => {
        container.style.transition = 'opacity 0.5s ease';
        container.style.opacity = '1';
    }, 50);
}

// Load New Random Reviews
function loadNewCustomerReviews() {
    loadCustomerReviews();
    showToast('✓ Loaded new reviews!', 'success');
}

// Get Random Reviews
function getRandomCustomerReviews(count) {
    const shuffled = [...customerReviewsData].sort(() => Math.random() - 0.5);
    return shuffled.slice(0, count);
}

// Generate Star Rating
function generateReviewStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += i <= rating ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
    }
    return stars;
}

// Toast Notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.cssText = 'position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease; box-shadow: 0 5px 20px rgba(0,0,0,0.2);';
    toast.innerHTML = `
        <div class="d-flex align-items-center justify-content-between">
            <span><i class="fas fa-check-circle me-2"></i>${message}</span>
            <button type="button" class="btn-close ms-3" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCustomerReviews();
});

// Add animation styles
const animStyle = document.createElement('style');
animStyle.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(animStyle);
</script>

<?php require_once 'includes/footer.php'; ?>