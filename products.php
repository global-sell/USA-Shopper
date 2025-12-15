<?php
$pageTitle = "Products";
require_once 'includes/header.php';

$db = Database::getInstance();

// Function to get seeded random number based on visitor cookie
function getVisitorSeededRandom($min, $max, $purpose = '') {
    $cookieName = 'visitor_stats_seed';
    if (!isset($_COOKIE[$cookieName])) {
        $visitorSeed = bin2hex(random_bytes(8));
        setcookie($cookieName, $visitorSeed, time() + 30 * 24 * 60 * 60, "/", "", false, true);
        $_COOKIE[$cookieName] = $visitorSeed;
    }
    
    // Create a seed from visitor cookie, current day, and purpose
    $seed = crc32($_COOKIE[$cookieName] . date('Y-m-d') . $purpose);
    mt_srand($seed);
    return mt_rand($min, $max);
}

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

// Get filters
$search = sanitizeInput($_GET['search'] ?? '');
$category = sanitizeInput($_GET['category'] ?? '');
$sortBy = sanitizeInput($_GET['sort'] ?? 'latest');

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = PRODUCTS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Build query
$where = ["p.status = 'active'"];
$params = [];
$types = '';

if (!empty($search)) {
    $where[] = "(p.title LIKE ? OR p.description LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'ss';
}

if (!empty($category)) {
    $where[] = "c.slug = ?";
    $params[] = $category;
    $types .= 's';
}

$whereClause = implode(' AND ', $where);

// Sorting
$orderBy = match($sortBy) {
    'price_low' => 'p.price ASC',
    'price_high' => 'p.price DESC',
    'popular' => 'p.sold DESC',
    default => 'p.created_at DESC'
};

// Generate consistent random numbers for this visitor
$totalProducts = getVisitorSeededRandom(1, 100000, 'products');

// For database queries, we'll still need the real count internally
$countQuery = "SELECT COUNT(*) as total FROM products p 
               LEFT JOIN categories c ON p.category_id = c.id 
               WHERE $whereClause";
$stmt = $db->prepare($countQuery);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$realTotalProducts = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($realTotalProducts / $perPage);

// Get products
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE $whereClause 
          ORDER BY $orderBy 
          LIMIT ? OFFSET ?";

$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

$stmt = $db->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get categories for filter (real categories for functionality)
$categories = $db->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Generate random but consistent category count for display
$displayCategoryCount = getVisitorSeededRandom(50, 200, 'categories');
?>

<!-- Hero Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center text-white">
        <div class="animate__animated animate__fadeInDown">
            <span class="badge bg-warning text-dark px-3 py-2 mb-3">
                <i class="fas fa-globe me-2"></i>WORLDWIDE SHIPPING AVAILABLE
            </span>
            <h1 class="display-4 fw-bold mb-3">Explore Our Products</h1>
            <p class="lead mb-3">Discover premium products with international shipping to 100+ countries</p>
            <div class="mt-4">
                <a href="#products-grid" class="badge bg-white text-primary px-3 py-2 me-2 text-decoration-none hero-badge">
                    <i class="fas fa-box me-2"></i><?php echo $totalProducts; ?> Products
                </a>
                <a href="<?php echo SITE_URL; ?>/products.php" class="badge bg-white text-primary px-3 py-2 me-2 text-decoration-none hero-badge">
                    <i class="fas fa-tags me-2"></i><?php echo $displayCategoryCount; ?> Categories
                </a>
                <a href="#shipping-info" class="badge bg-success text-white px-3 py-2 me-2 text-decoration-none hero-badge">
                    <i class="fas fa-shipping-fast me-2"></i>Free Shipping $50+
                </a>
                <a href="<?php echo SITE_URL; ?>/about.php" class="badge bg-info text-white px-3 py-2 text-decoration-none hero-badge">
                    <i class="fas fa-shield-alt me-2"></i>Buyer Protection
                </a>
            </div>
            <div class="mt-3">
                <small class="opacity-75">
                    <i class="fas fa-dollar-sign me-1"></i>USD | 
                    <i class="fas fa-pound-sign me-1"></i>GBP | 
                    <i class="fas fa-euro-sign me-1"></i>EUR | 
                    CAD | AUD
                </small>
            </div>
        </div>
    </div>
</section>

<!-- International Trust Bar -->
<section class="py-3 bg-light border-bottom">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-globe-americas text-primary"></i>
                    <small class="fw-bold">Ship to 100+ Countries</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-box text-success"></i>
                    <small class="fw-bold">Real-Time Tracking</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-lock text-warning"></i>
                    <small class="fw-bold">Secure Payment</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-undo text-info"></i>
                    <small class="fw-bold">30-Day Returns</small>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    
    <!-- Advanced Filters Panel -->
    <div class="row mb-4" style="margin-top: -50px;">
        <div class="col-12">
            <div class="card border-0 shadow-lg animate__animated animate__fadeInUp filter-card" style="border-radius: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            <div class="filter-icon-bg">
                                <i class="fas fa-sliders-h fa-2x text-white"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-white ms-3">Advanced Filters</h5>
                        </div>
                        <button class="btn btn-light btn-sm" type="button" onclick="toggleFilters()">
                            <i class="fas fa-chevron-down" id="filterToggleIcon"></i>
                        </button>
                    </div>
                    
                    <div id="filterContent">
                        <form method="GET" action="" class="row g-3">
                            <!-- Search with Icon -->
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label text-white small fw-bold">
                                    <i class="fas fa-search me-1"></i>Search Products
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-0">
                                        <i class="fas fa-search text-primary"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-0" 
                                           value="<?php echo htmlspecialchars($search); ?>" 
                                           placeholder="Type to search..." id="searchInput">
                                </div>
                            </div>
                            
                            <!-- Category -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-white small fw-bold">
                                    <i class="fas fa-tags me-1"></i>Category
                                </label>
                                <select name="category" class="form-select form-select-lg" style="border-radius: 10px;">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['slug']; ?>" 
                                                <?php echo $category === $cat['slug'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Sort -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-white small fw-bold">
                                    <i class="fas fa-sort-amount-down me-1"></i>Sort By
                                </label>
                                <select name="sort" class="form-select form-select-lg" style="border-radius: 10px;">
                                    <option value="latest" <?php echo $sortBy === 'latest' ? 'selected' : ''; ?>>⭐ Newest First</option>
                                    <option value="popular" <?php echo $sortBy === 'popular' ? 'selected' : ''; ?>>🔥 Most Popular</option>
                                    <option value="price_low" <?php echo $sortBy === 'price_low' ? 'selected' : ''; ?>>💰 Price: Low → High</option>
                                    <option value="price_high" <?php echo $sortBy === 'price_high' ? 'selected' : ''; ?>>💎 Price: High → Low</option>
                                </select>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label text-white small fw-bold opacity-0">Action</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-light btn-lg fw-bold">
                                        <i class="fas fa-filter me-2"></i>Apply
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- View Toggle & Stats Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary px-3 py-2">
                        <i class="fas fa-box me-2"></i><?php echo $totalProducts; ?> Products
                    </span>
                    <?php if (!empty($search)): ?>
                        <span class="badge bg-success px-3 py-2">
                            <i class="fas fa-search me-2"></i>Search: "<?php echo htmlspecialchars($search); ?>"
                            <a href="<?php echo SITE_URL; ?>/products.php" class="text-white ms-2">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- View Toggle -->
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" onclick="changeView('grid')" id="gridView">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="changeView('list')" id="listView">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Results Info -->
    <div class="row mb-4" id="products-grid">
        <div class="col-md-6">
            <h5 class="fw-bold">
                <?php if (!empty($search)): ?>
                    Search Results for "<?php echo htmlspecialchars($search); ?>"
                <?php elseif (!empty($category)): ?>
                    <?php 
                    $catName = array_filter($categories, fn($c) => $c['slug'] === $category);
                    echo !empty($catName) ? htmlspecialchars(reset($catName)['name']) : 'Products';
                    ?>
                <?php else: ?>
                    All Products
                <?php endif; ?>
            </h5>
        </div>
        <div class="col-md-6 text-md-end">
            <?php if (!empty($search) || !empty($category)): ?>
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear Filters
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="row g-4 mb-4 products-grid-section">
        <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h5>No products found</h5>
                    <p class="mb-0">Try adjusting your filters or search terms</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card product-card h-100 border-0 shadow-sm position-relative" style="border-radius: 15px; overflow: hidden;">
                        <?php 
                        $screenshots = json_decode($product['screenshots'], true);
                        $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/400x300/4CAF50/ffffff?text=' . urlencode($product['title']);
                        
                        // Product status
                        $isNew = (time() - strtotime($product['created_at'])) < (7 * 24 * 60 * 60);
                        $onSale = !empty($product['sale_price']) && $product['sale_price'] < $product['price'];
                        $discount = $onSale ? round((($product['price'] - $product['sale_price']) / $product['price']) * 100) : 0;
                        $soldCount = $product['sold'] ?? 0;
                        ?>
                        
                        <!-- Product Image with Overlay -->
                        <div class="product-image-wrapper position-relative">
                            <a href="<?php echo SITE_URL; ?>/<?php echo $product['slug']; ?>" class="text-decoration-none d-block">
                                <img src="<?php echo $firstImage; ?>" class="card-img-top product-img" alt="<?php echo htmlspecialchars($product['title']); ?>" loading="lazy">
                                <div class="image-overlay">
                                    <i class="fas fa-eye fa-2x"></i>
                                    <span class="d-block mt-2 fw-bold">View Details</span>
                                </div>
                            </a>
                            <div class="product-badges position-absolute top-0 start-0 p-2">
                                <?php if ($isNew): ?>
                                    <span class="badge bg-success mb-1 d-block animate__animated animate__pulse animate__infinite">NEW</span>
                                <?php endif; ?>
                                <?php if ($onSale): ?>
                                    <span class="badge bg-danger mb-1 d-block">-<?php echo $discount; ?>%</span>
                                    <!-- Countdown Timer for Sale -->
                                    <div class="countdown-timer badge bg-dark mb-1 d-block" data-end-time="<?php echo strtotime('+2 days'); ?>">
                                        <i class="fas fa-clock me-1"></i>
                                        <span class="timer-text">Loading...</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($soldCount > 1000): ?>
                                    <span class="badge bg-warning mb-1 d-block">
                                        <i class="fas fa-trophy me-1"></i>Best Seller
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="quick-actions position-absolute top-0 end-0 p-2">
                                <button class="btn btn-sm btn-light rounded-circle mb-2 quick-btn" onclick="addToWishlist(<?php echo $product['id']; ?>); showConfetti();" title="Add to Wishlist" data-bs-toggle="tooltip">
                                    <i class="fas fa-heart text-danger"></i>
                                </button>
                                <button class="btn btn-sm btn-light rounded-circle mb-2 quick-btn" onclick="addToCompare(<?php echo $product['id']; ?>)" title="Add to Compare" data-bs-toggle="tooltip">
                                    <i class="fas fa-balance-scale text-info"></i>
                                </button>
                                <button class="btn btn-sm btn-light rounded-circle quick-btn" onclick="quickView(<?php echo $product['id']; ?>)" title="Quick View" data-bs-toggle="tooltip">
                                    <i class="fas fa-eye text-primary"></i>
                                </button>
                            </div>
                            
                            <!-- Product Hover Tooltip -->
                            <div class="product-tooltip">
                                <div class="tooltip-content">
                                    <i class="fas fa-bolt text-warning"></i> Quick Purchase Available
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <?php if ($product['category_name']): ?>
                                <span class="badge bg-primary mb-2 align-self-start">
                                    <?php echo htmlspecialchars($product['category_name']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <h5 class="card-title fw-bold">
                                <a href="<?php echo SITE_URL; ?>/<?php echo $product['slug']; ?>" class="text-dark text-decoration-none product-title-link">
                                    <?php echo htmlspecialchars($product['title']); ?>
                                </a>
                            </h5>
                            <a href="<?php echo SITE_URL; ?>/<?php echo $product['slug']; ?>" class="text-decoration-none">
                                <p class="card-text text-muted small flex-grow-1 product-description-link">
                                    <?php echo substr(htmlspecialchars($product['description']), 0, 80); ?>...
                                </p>
                            </a>
                            
                            <div class="mt-auto">
                                <!-- Rating Stars -->
                                <a href="<?php echo SITE_URL; ?>/<?php echo $product['slug']; ?>" class="text-decoration-none">
                                    <div class="product-rating mb-2">
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
                                    <small class="text-muted ms-1">(<?php echo rand(50, 500); ?>)</small>
                                    </div>
                                </a>
                                
                                <!-- Price -->
                                <a href="<?php echo SITE_URL; ?>/<?php echo $product['slug']; ?>" class="text-decoration-none d-block">
                                    <div class="product-price mb-3 product-price-link">
                                        <?php if ($onSale): ?>
                                            <div>
                                                <span class="h5 text-danger fw-bold mb-0"><?php echo formatPrice($product['sale_price']); ?></span>
                                                <span class="text-muted text-decoration-line-through ms-2"><?php echo formatPrice($product['price']); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="h5 text-primary fw-bold mb-0"><?php echo formatPrice($product['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                
                                <!-- Shipping Badge & Sold Count -->
                                <a href="<?php echo SITE_URL; ?>/<?php echo $product['slug']; ?>" class="text-decoration-none d-block">
                                    <div class="mb-3 d-flex gap-2 flex-wrap align-items-center">
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="fas fa-shipping-fast me-1"></i>Free Shipping
                                        </span>
                                        <span class="badge bg-primary-subtle text-primary">
                                            <i class="fas fa-fire me-1"></i><?php echo formatSoldCount($soldCount); ?> Sold
                                        </span>
                                        
                                        <!-- Stock Indicator -->
                                        <?php 
                                        $stockStatus = rand(1, 100); // Simulated stock, replace with actual
                                        if ($stockStatus > 50): ?>
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="fas fa-check-circle me-1"></i>In Stock
                                            </span>
                                        <?php elseif ($stockStatus > 10): ?>
                                            <span class="badge bg-warning-subtle text-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger">
                                                <i class="fas fa-times-circle me-1"></i>Out of Stock
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                
                                <!-- Quick Add to Cart -->
                                <div class="quick-add-cart">
                                    <button onclick="quickAddToCart(<?php echo $product['id']; ?>, event)" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-shopping-cart me-1"></i>Quick Add
                                    </button>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <?php if (isLoggedIn()): ?>
                                        <?php if (in_array($product['product_type'] ?? 'simple', ['external', 'affiliate'])): ?>
                                            <?php 
                                            $externalUrl = !empty($product['affiliate_link']) ? $product['affiliate_link'] : $product['external_url'];
                                            $buttonText = !empty($product['button_text']) ? $product['button_text'] : 'Buy Now';
                                            ?>
                                            <a href="<?php echo htmlspecialchars($externalUrl); ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-external-link-alt me-1"></i><?php echo htmlspecialchars($buttonText); ?>
                                            </a>
                                        <?php else: ?>
                                            <button onclick="buyNow(<?php echo $product['id']; ?>)" 
                                                    class="btn btn-success btn-sm">
                                                <i class="fas fa-bolt me-1"></i>Buy Now
                                            </button>
                                            <button onclick="addToCart(<?php echo $product['id']; ?>)" 
                                                    class="btn btn-primary btn-sm">
                                                <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if (in_array($product['product_type'] ?? 'simple', ['external', 'affiliate'])): ?>
                                            <?php 
                                            $externalUrl = !empty($product['affiliate_link']) ? $product['affiliate_link'] : $product['external_url'];
                                            $buttonText = !empty($product['button_text']) ? $product['button_text'] : 'Buy Now';
                                            ?>
                                            <a href="<?php echo htmlspecialchars($externalUrl); ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-external-link-alt me-1"></i><?php echo htmlspecialchars($buttonText); ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo SITE_URL; ?>/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-bolt me-1"></i>Buy Now
                                            </a>
                                            <a href="<?php echo SITE_URL; ?>/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- International Shipping Info Banner -->
    <div class="row mb-4" id="shipping-info">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 15px;">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="fw-bold mb-2">
                                <i class="fas fa-shipping-fast me-2"></i>Free International Shipping
                            </h5>
                            <p class="mb-0">
                                Get free shipping on orders over $50 to USA, UK, Canada, and Australia. 
                                Express delivery available to 100+ countries worldwide with real-time tracking.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                                <span class="badge bg-white text-dark px-3 py-2">🇺🇸 USA</span>
                                <span class="badge bg-white text-dark px-3 py-2">🇬🇧 UK</span>
                                <span class="badge bg-white text-dark px-3 py-2">🇨🇦 CA</span>
                                <span class="badge bg-white text-dark px-3 py-2">🇦🇺 AU</span>
                                <span class="badge bg-white text-dark px-3 py-2">+96</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="row">
            <div class="col-12">
                <nav aria-label="Product pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo urlencode($sortBy); ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo urlencode($sortBy); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php elseif (abs($i - $page) == 3): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo urlencode($sortBy); ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Scroll Progress Bar -->
<div id="scrollProgress"></div>

<!-- Back to Top Button with Counter -->
<button onclick="scrollToTop()" id="backToTop" class="btn btn-primary btn-lg rounded-circle shadow-lg">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Floating Wishlist Counter -->
<div id="wishlistFloat" class="position-fixed" style="bottom: 90px; right: 20px; z-index: 1020;" onclick="window.location.href='<?php echo SITE_URL; ?>/wishlist.php'">
    <button class="btn btn-danger btn-lg rounded-circle shadow-lg position-relative">
        <i class="fas fa-heart"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-dark" id="wishlistCounter" style="display: none;">
            0
        </span>
    </button>
</div>

<script>
// Wishlist Management
let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');

// Update wishlist counter on page load
document.addEventListener('DOMContentLoaded', function() {
    updateWishlistCounter();
    initScrollEffects();
});

function addToWishlist(productId) {
    if (!wishlist.includes(productId)) {
        wishlist.push(productId);
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        showNotification('❤️ Added to wishlist!', 'success');
        updateWishlistCounter();
        animateWishlistButton();
    } else {
        showNotification('Already in wishlist', 'info');
    }
}

function updateWishlistCounter() {
    const counter = document.getElementById('wishlistCounter');
    if (wishlist.length > 0) {
        counter.textContent = wishlist.length;
        counter.style.display = 'block';
    } else {
        counter.style.display = 'none';
    }
}

function animateWishlistButton() {
    const btn = document.querySelector('#wishlistFloat button');
    btn.style.transform = 'scale(1.3)';
    setTimeout(() => {
        btn.style.transform = 'scale(1)';
    }, 300);
}

// Quick View Function
function quickView(productId) {
    showNotification('Opening product details...', 'info');
    // Redirect to product page
    window.location.href = '<?php echo SITE_URL; ?>/product-detail.php?id=' + productId;
}

// Show Notification
function showNotification(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-lg`;
    alert.style.zIndex = '9999';
    alert.style.minWidth = '300px';
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

// Buy Now function - adds to cart and redirects to checkout
function buyNow(productId) {
    // Use relative API path so AJAX works on local and production
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
            window.location.href = '<?php echo SITE_URL; ?>/checkout.php';
        } else {
            // Redirect to login if not authenticated
            if (typeof data.message === 'string' && data.message.toLowerCase().includes('please login')) {
                const returnUrl = encodeURIComponent(window.location.pathname + window.location.search);
                window.location.href = '<?php echo SITE_URL; ?>/login.php?redirect=' + returnUrl;
                return;
            }
            showNotification(data.message || 'Failed to add product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

// Scroll Effects
function initScrollEffects() {
    window.addEventListener('scroll', function() {
        // Scroll progress bar
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        document.getElementById('scrollProgress').style.width = scrolled + '%';
        
        // Back to top button
        const backToTop = document.getElementById('backToTop');
        if (winScroll > 300) {
            backToTop.style.display = 'flex';
            backToTop.style.opacity = '1';
        } else {
            backToTop.style.opacity = '0';
            setTimeout(() => {
                if (winScroll <= 300) backToTop.style.display = 'none';
            }, 300);
        }
        
        // Animate products on scroll
        animateOnScroll();
    });
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Animate products when they come into view with BLINK effect
function animateOnScroll() {
    const products = document.querySelectorAll('.product-card');
    products.forEach(product => {
        const rect = product.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight - 100 && rect.bottom > 0;
        if (isVisible && !product.classList.contains('animated')) {
            product.classList.add('animated', 'animate__fadeInUp', 'product-blink');
            
            // Add sequential blink effect
            setTimeout(() => {
                product.classList.add('blink-flash');
            }, 300);
            
            // Remove blink class after animation
            setTimeout(() => {
                product.classList.remove('product-blink', 'blink-flash');
            }, 1500);
        }
    });
}

// Multiple Blink Types for Products
const observerOptions = {
    threshold: 0.2,
    rootMargin: '0px'
};

const productObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting && !entry.target.classList.contains('observed')) {
            entry.target.classList.add('observed');
            
            // Determine blink type based on product position
            const blinkTypes = ['blink-glow', 'blink-pulse', 'blink-fade', 'blink-shimmer'];
            const blinkType = blinkTypes[index % 4]; // Cycle through types
            
            // Check if product has special badges
            const hasNewBadge = entry.target.querySelector('.badge.bg-success');
            const hasSaleBadge = entry.target.querySelector('.badge.bg-danger');
            
            // Apply different blink based on product features
            setTimeout(() => {
                if (hasSaleBadge) {
                    entry.target.classList.add('blink-sale'); // Red glow for sale items
                } else if (hasNewBadge) {
                    entry.target.classList.add('blink-new'); // Green glow for new items
                } else {
                    entry.target.classList.add(blinkType); // Cycle through other types
                }
                
                // Cleanup after animation
                setTimeout(() => {
                    entry.target.classList.remove('blink-glow', 'blink-pulse', 'blink-fade', 'blink-shimmer', 'blink-sale', 'blink-new');
                }, 800);
            }, index * 50);
        }
    });
}, observerOptions);

// Observe all product cards
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.product-card').forEach(card => {
        productObserver.observe(card);
    });
    
    // Show blink types legend (optional)
    setTimeout(() => {
        showBlinkLegend();
    }, 2000);
});

// Show Blink Types Legend
function showBlinkLegend() {
    const legend = document.createElement('div');
    legend.className = 'blink-legend';
    legend.innerHTML = `
        <div class="legend-header">
            <i class="fas fa-sparkles me-2"></i>Scroll Effects Active
            <button onclick="this.closest('.blink-legend').remove()" class="btn-close btn-close-white btn-sm ms-auto"></button>
        </div>
        <div class="legend-items">
            <div class="legend-item">
                <span class="legend-dot" style="background: #dc2626;"></span>
                <small>Sale Items - Red Glow</small>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #16a34a;"></span>
                <small>New Items - Green Glow</small>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #667eea;"></span>
                <small>Featured - Blue Glow</small>
            </div>
        </div>
    `;
    document.body.appendChild(legend);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        legend.style.opacity = '0';
        setTimeout(() => legend.remove(), 300);
    }, 5000);
}

// Product Image Lazy Loading
document.querySelectorAll('.product-img').forEach(img => {
    if ('loading' in HTMLImageElement.prototype) {
        img.loading = 'lazy';
    }
});

// Add product comparison
let compareList = JSON.parse(localStorage.getItem('compareList') || '[]');

function addToCompare(productId) {
    if (compareList.length >= 4) {
        showNotification('⚠️ Maximum 4 products can be compared', 'warning');
        return;
    }
    if (!compareList.includes(productId)) {
        compareList.push(productId);
        localStorage.setItem('compareList', JSON.stringify(compareList));
        showNotification('✓ Added to comparison', 'success');
    }
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Product hover sound effect (optional)
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
    });
});

// Toggle Filters
function toggleFilters() {
    const content = document.getElementById('filterContent');
    const icon = document.getElementById('filterToggleIcon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        content.style.display = 'none';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

// Change View (Grid/List)
let currentView = 'grid';
function changeView(view) {
    currentView = view;
    const productsGrid = document.getElementById('products-grid').nextElementSibling;
    const cards = productsGrid.querySelectorAll('.col-lg-3');
    
    if (view === 'list') {
        cards.forEach(card => {
            card.className = 'col-12 mb-3';
            const productCard = card.querySelector('.product-card');
            productCard.classList.add('list-view');
        });
        document.getElementById('listView').classList.add('active');
        document.getElementById('gridView').classList.remove('active');
    } else {
        cards.forEach(card => {
            card.className = 'col-lg-3 col-md-4 col-sm-6 mb-4';
            const productCard = card.querySelector('.product-card');
            productCard.classList.remove('list-view');
        });
        document.getElementById('gridView').classList.add('active');
        document.getElementById('listView').classList.remove('active');
    }
    
    // Animate the change
    cards.forEach((card, index) => {
        card.style.animation = 'none';
        setTimeout(() => {
            card.style.animation = `fadeInUp 0.5s ease ${index * 0.05}s both`;
        }, 10);
    });
}

// Live Search with debounce
let searchTimeout;
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const query = e.target.value;
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                // Show loading indicator
                this.style.background = "url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMCIgY3k9IjEwIiByPSI4IiBzdHJva2U9IiM2NjdlZWEiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0ibm9uZSIvPjwvc3ZnPg==') no-repeat right 10px center";
                this.style.backgroundSize = "20px";
            }, 500);
        }
    });
}

// Product Quick Stats Animation
document.addEventListener('DOMContentLoaded', function() {
    const statsNumbers = document.querySelectorAll('.badge');
    statsNumbers.forEach((stat, index) => {
        stat.style.opacity = '0';
        setTimeout(() => {
            stat.style.transition = 'all 0.5s ease';
            stat.style.opacity = '1';
            stat.style.transform = 'scale(1)';
        }, index * 100);
    });
});

// Confetti effect on wishlist add (fun feature!)
function showConfetti() {
    // Simple confetti animation
    const colors = ['#667eea', '#764ba2', '#f093fb', '#48bb78'];
    for (let i = 0; i < 30; i++) {
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

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for search focus
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('searchInput')?.focus();
        showNotification('⌨️ Search activated', 'info');
    }
    
    // G for grid view
    if (e.key === 'g' && !e.ctrlKey && !e.metaKey) {
        changeView('grid');
    }
    
    // L for list view
    if (e.key === 'l' && !e.ctrlKey && !e.metaKey) {
        changeView('list');
    }
    
    // F for filter toggle
    if (e.key === 'f' && !e.ctrlKey && !e.metaKey) {
        toggleFilters();
    }
});

// Double Click to Zoom Product Image
document.querySelectorAll('.product-img').forEach(img => {
    img.addEventListener('dblclick', function(e) {
        e.preventDefault();
        zoomImage(this);
    });
});

function zoomImage(img) {
    const modal = document.createElement('div');
    modal.className = 'zoom-modal';
    modal.innerHTML = `
        <div class="zoom-backdrop" onclick="this.parentElement.remove()">
            <img src="${img.src}" class="zoom-img" alt="Zoomed">
            <button class="zoom-close" onclick="this.closest('.zoom-modal').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Animate in
    setTimeout(() => {
        modal.querySelector('.zoom-img').style.transform = 'scale(1)';
    }, 10);
}

// Subtle Product Card Tilt Effect (Disabled to fix link issues)
// Removed to prevent interference with clicking products

// Add to Cart with Animation
function addToCartAnimated(productId) {
    const button = event.target.closest('button');
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
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
            button.innerHTML = '<i class="fas fa-check"></i> Added!';
            button.classList.add('btn-success');
            showNotification('✓ Added to cart!', 'success');
            showConfetti();
            
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-cart-plus me-1"></i>Add to Cart';
                button.classList.remove('btn-success');
                button.disabled = false;
            }, 2000);
        } else {
            button.innerHTML = '<i class="fas fa-times"></i> Failed';
            button.disabled = false;
            showNotification(data.message || 'Failed to add to cart', 'error');
        }
    });
}

// Product Quick Stats Counter Animation
function animateNumber(element, start, end, duration) {
    let current = start;
    const range = end - start;
    const increment = range / (duration / 16);
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= end) {
            element.textContent = end;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Countdown Timer for Sale Products
function updateCountdownTimers() {
    document.querySelectorAll('.countdown-timer').forEach(timer => {
        const endTime = parseInt(timer.getAttribute('data-end-time')) * 1000;
        const now = Date.now();
        const remaining = endTime - now;
        
        if (remaining > 0) {
            const days = Math.floor(remaining / (1000 * 60 * 60 * 24));
            const hours = Math.floor((remaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
            
            const timerText = timer.querySelector('.timer-text');
            if (days > 0) {
                timerText.textContent = `${days}d ${hours}h`;
            } else if (hours > 0) {
                timerText.textContent = `${hours}h ${minutes}m`;
            } else {
                timerText.textContent = `${minutes}m ${seconds}s`;
            }
        } else {
            timer.querySelector('.timer-text').textContent = 'Expired';
            timer.classList.add('expired');
        }
    });
}

// Update countdown every second
setInterval(updateCountdownTimers, 1000);
updateCountdownTimers();

// Quick Add to Cart Function
function quickAddToCart(productId, event) {
    event.stopPropagation();
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> Added!';
        button.classList.remove('btn-success');
        button.classList.add('btn-success');
        
        showNotification('🛒 Product added to cart!', 'success');
        showConfetti();
        
        // Track recently viewed
        trackRecentlyViewed(productId);
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        }, 2000);
    }, 800);
}

// Track Recently Viewed Products
function trackRecentlyViewed(productId) {
    let recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
    
    // Remove if already exists
    recentlyViewed = recentlyViewed.filter(id => id !== productId);
    
    // Add to beginning
    recentlyViewed.unshift(productId);
    
    // Keep only last 10
    if (recentlyViewed.length > 10) {
        recentlyViewed = recentlyViewed.slice(0, 10);
    }
    
    localStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));
}

// Load recently viewed on product click
document.querySelectorAll('.product-card').forEach((card, index) => {
    card.addEventListener('click', function(e) {
        // Don't track if clicking action buttons
        if (!e.target.closest('.quick-actions') && !e.target.closest('.quick-add-cart')) {
            const productId = this.querySelector('[data-product-id]')?.getAttribute('data-product-id');
            if (productId) {
                trackRecentlyViewed(productId);
            }
        }
    });
});

// Price Range Filter Animation
const priceRangeInputs = document.querySelectorAll('input[type="range"]');
priceRangeInputs.forEach(input => {
    input.addEventListener('input', function() {
        this.style.background = `linear-gradient(to right, #667eea 0%, #667eea ${(this.value / this.max) * 100}%, #ddd ${(this.value / this.max) * 100}%, #ddd 100%)`;
    });
});
</script>

<!-- Add Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
/* Filter Card Styling */
.filter-card {
    position: relative;
    overflow: hidden;
}

.filter-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    bottom: -50%;
    left: -50%;
    background: linear-gradient(to bottom, rgba(255,255,255,0.1), transparent);
    transform: rotate(30deg);
    pointer-events: none;
}

.filter-icon-bg {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

#filterContent {
    transition: all 0.3s ease;
}

/* Product Card Styling */
.product-card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

.product-card .product-img {
    cursor: pointer;
}

.product-card .product-title-link {
    cursor: pointer;
}

.product-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(102, 126, 234, 0.08) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.product-card:hover::before {
    opacity: 1;
}

.product-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.product-card:hover::after {
    transform: scaleX(1);
}

.product-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.25) !important;
    background: linear-gradient(135deg, #ffffff 0%, #faf5ff 100%);
}

/* Alternate card colors */
.product-card:nth-child(4n+1) {
    background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
}

.product-card:nth-child(4n+2) {
    background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
}

.product-card:nth-child(4n+3) {
    background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
}

.product-card:nth-child(4n+4) {
    background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
}

/* List View */
.product-card.list-view {
    display: flex;
    flex-direction: row;
}

.product-card.list-view .product-image-wrapper {
    width: 300px;
    flex-shrink: 0;
}

.product-card.list-view .card-body {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.product-card.list-view .product-img {
    height: 200px;
    width: 100%;
}

.product-image-wrapper {
    overflow: hidden;
    position: relative;
}

.product-img {
    height: 250px;
    object-fit: cover;
    transition: transform 0.6s ease;
    width: 100%;
}

.product-card:hover .product-img {
    transform: scale(1.15) rotate(2deg);
}

/* Product Badges */
.product-badges .badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.4rem 0.8rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* Quick Actions */
.quick-actions {
    opacity: 0;
    transform: translateX(20px);
    transition: all 0.3s ease;
}

.product-card:hover .quick-actions {
    opacity: 1;
    transform: translateX(0);
}

.quick-btn {
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}

.quick-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
}

/* Image Overlay on Hover */
.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(102, 126, 234, 0.95);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    color: white;
    cursor: pointer;
}

.image-overlay i {
    animation: bounceIcon 1s infinite;
}

@keyframes bounceIcon {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

.product-image-wrapper:hover .image-overlay {
    opacity: 1;
}

/* Product Title Link */
.product-title-link {
    transition: color 0.3s ease;
}

.product-title-link:hover {
    color: #667eea !important;
}

/* Product Description Link */
.product-description-link {
    cursor: pointer;
    transition: color 0.3s ease;
}

.product-description-link:hover {
    color: #667eea !important;
}

/* Product Price Link */
.product-price-link {
    cursor: pointer;
    transition: all 0.3s ease;
}

.product-price-link:hover .h5 {
    transform: scale(1.05);
    color: #667eea !important;
}

/* Product Rating Link */
.product-rating {
    cursor: pointer;
    transition: all 0.3s ease;
}

.product-rating:hover {
    transform: translateY(-2px);
}

.product-rating:hover i {
    color: #fbbf24 !important;
}

/* Shipping Badge Hover */
.badge.bg-success-subtle:hover {
    background: #48bb78 !important;
    color: white !important;
    transform: scale(1.05);
    transition: all 0.3s ease;
}

/* Sold Count Badge */
.badge.bg-primary-subtle {
    animation: soldPulse 2s infinite;
}

@keyframes soldPulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.badge.bg-primary-subtle:hover {
    background: #667eea !important;
    color: white !important;
    transform: scale(1.08);
    transition: all 0.3s ease;
}

.badge.bg-primary-subtle .fa-fire {
    animation: fireFlicker 1.5s infinite;
}

@keyframes fireFlicker {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
        transform: scale(1.1);
    }
}

/* Quick Add to Cart Button */
.quick-add-cart {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    width: calc(100% - 20px);
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 10;
}

.product-card:hover .quick-add-cart {
    opacity: 1;
    bottom: 15px;
}

.quick-add-cart .btn {
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    font-weight: 600;
    backdrop-filter: blur(10px);
}

/* Countdown Timer */
.countdown-timer {
    font-size: 0.75rem;
    font-weight: 600;
    animation: timerPulse 1s infinite;
}

@keyframes timerPulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(0,0,0,0.7);
    }
    50% {
        box-shadow: 0 0 0 5px rgba(0,0,0,0);
    }
}

.countdown-timer.expired {
    background: #dc2626 !important;
    animation: shake 0.5s infinite;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-2px); }
    75% { transform: translateX(2px); }
}

/* Stock Indicators */
.badge.bg-warning-subtle {
    animation: warningBlink 2s infinite;
}

@keyframes warningBlink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.badge.bg-success-subtle {
    box-shadow: 0 0 10px rgba(72, 187, 120, 0.3);
}

.badge.bg-danger-subtle {
    animation: shake 0.8s infinite;
}

/* Best Seller Badge */
.badge.bg-warning {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
    box-shadow: 0 0 15px rgba(251, 191, 36, 0.5);
    animation: glowBadge 2s infinite;
}

@keyframes glowBadge {
    0%, 100% {
        box-shadow: 0 0 15px rgba(251, 191, 36, 0.5);
    }
    50% {
        box-shadow: 0 0 25px rgba(251, 191, 36, 0.8);
    }
}

/* Product Tooltip */
.product-tooltip {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.85);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
    z-index: 5;
    white-space: nowrap;
    font-size: 0.8rem;
}

.product-card:hover .product-tooltip {
    opacity: 0.9;
    bottom: 15px;
}

.tooltip-content {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    font-weight: 600;
}

/* Skeleton Loading Animation */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
}

@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Product Rating */
.product-rating i {
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.product-rating i:hover {
    transform: scale(1.3) rotate(10deg);
}

/* Product Price */
.product-price {
    font-size: 1.1rem;
}

/* Card Body */
.product-card .card-body {
    padding: 1.25rem;
    position: relative;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
}

.product-card .card-body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(102, 126, 234, 0.02) 10px, rgba(102, 126, 234, 0.02) 20px);
    pointer-events: none;
}

.card-title {
    font-size: 1rem;
    line-height: 1.4;
    min-height: 2.6rem;
    margin-bottom: 0.5rem;
    color: #2d3748;
}

.card-text {
    font-size: 0.875rem;
    color: #718096;
    line-height: 1.5;
}

/* Buttons */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    transition: all 0.3s ease;
    font-weight: 600;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    border: none;
    transition: all 0.3s ease;
    font-weight: 600;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(72, 187, 120, 0.4);
}

/* Form Elements */
.form-control, .form-select {
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    padding: 0.625rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Pagination */
.pagination .page-link {
    color: #667eea;
    border-radius: 8px;
    margin: 0 4px;
    border: none;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Badges */
.badge {
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 6px;
}

/* Loading Animation */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Smooth Animations */
.animate__animated {
    animation-duration: 0.6s;
}

/* Scroll Progress Bar */
#scrollProgress {
    position: fixed;
    top: 0;
    left: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    z-index: 9999;
    transition: width 0.3s ease;
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.5);
}

/* Back to Top Button */
#backToTop {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1020;
    width: 50px;
    height: 50px;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    cursor: pointer;
    animation: bounce 2s infinite;
}

#backToTop:hover {
    transform: translateY(-5px) scale(1.1);
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Wishlist Float Button */
#wishlistFloat button {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

#wishlistFloat button:hover {
    transform: scale(1.15) rotate(5deg);
    box-shadow: 0 10px 25px rgba(220, 38, 38, 0.4) !important;
}

#wishlistCounter {
    font-size: 0.7rem;
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

/* Product Card Fade In Animation */
.product-card.animated {
    animation-duration: 0.6s;
    animation-fill-mode: both;
}

/* Multiple Blink Types */

/* 1. Glow Blink - Blue/Purple Glow */
.blink-glow {
    animation: blinkGlow 0.8s ease-in-out;
}

@keyframes blinkGlow {
    0%, 100% {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    50% {
        box-shadow: 0 0 25px rgba(102, 126, 234, 0.5);
    }
}

/* 2. Pulse Blink - Scale Pulse */
.blink-pulse {
    animation: blinkPulse 0.8s ease-in-out;
}

@keyframes blinkPulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    50% {
        transform: scale(1.03);
        box-shadow: 0 0 20px rgba(118, 75, 162, 0.4);
    }
}

/* 3. Fade Blink - Opacity + Shadow */
.blink-fade {
    animation: blinkFade 0.8s ease-in-out;
}

@keyframes blinkFade {
    0%, 100% {
        opacity: 1;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    50% {
        opacity: 0.92;
        box-shadow: 0 0 20px rgba(240, 147, 251, 0.4);
    }
}

/* 4. Shimmer Blink - Border Shimmer */
.blink-shimmer {
    animation: blinkShimmer 0.8s ease-in-out;
    position: relative;
}

@keyframes blinkShimmer {
    0%, 100% {
        border-color: transparent;
    }
    50% {
        border-color: rgba(72, 187, 120, 0.4);
        box-shadow: 0 0 20px rgba(72, 187, 120, 0.3);
    }
}

/* 5. Sale Blink - Red Glow for Sale Items */
.blink-sale {
    animation: blinkSale 0.8s ease-in-out;
}

@keyframes blinkSale {
    0%, 100% {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    25% {
        box-shadow: 0 0 30px rgba(220, 38, 38, 0.6);
    }
    75% {
        box-shadow: 0 0 25px rgba(239, 68, 68, 0.5);
    }
}

/* 6. New Blink - Green Glow for New Items */
.blink-new {
    animation: blinkNew 0.8s ease-in-out;
}

@keyframes blinkNew {
    0%, 100% {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    25% {
        box-shadow: 0 0 30px rgba(34, 197, 94, 0.6);
    }
    75% {
        box-shadow: 0 0 25px rgba(72, 187, 120, 0.5);
    }
}

/* Flash Effect - Subtle Version */
.blink-flash {
    animation: flashBorder 0.5s ease-in-out;
}

@keyframes flashBorder {
    0%, 100% {
        border-color: transparent;
    }
    50% {
        border-color: rgba(102, 126, 234, 0.3);
        box-shadow: 0 0 15px rgba(102, 126, 234, 0.2);
    }
}

/* Pulse Effect - Subtle Version */
.pulse-effect {
    animation: pulseGlow 0.8s ease-in-out;
}

@keyframes pulseGlow {
    0%, 100% {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    50% {
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.15);
    }
}

/* Staggered Animation on Initial Load */
.product-card:nth-child(1) {
    animation-delay: 0.1s;
}

.product-card:nth-child(2) {
    animation-delay: 0.2s;
}

.product-card:nth-child(3) {
    animation-delay: 0.3s;
}

.product-card:nth-child(4) {
    animation-delay: 0.4s;
}

.product-card:nth-child(5) {
    animation-delay: 0.5s;
}

.product-card:nth-child(6) {
    animation-delay: 0.6s;
}

.product-card:nth-child(7) {
    animation-delay: 0.7s;
}

.product-card:nth-child(8) {
    animation-delay: 0.8s;
}

/* Enhanced Badge Animations */
.product-badges .badge {
    animation: slideInLeft 0.5s ease;
}

@keyframes slideInLeft {
    from {
        transform: translateX(-20px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Shimmer effect for loading */
@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

.product-card.loading {
    animation: shimmer 2s infinite;
    background: linear-gradient(to right, #f0f0f0 8%, #e0e0e0 18%, #f0f0f0 33%);
    background-size: 1000px 100%;
}

/* Price Highlight Effect */
.product-price .h5 {
    position: relative;
    display: inline-block;
}

.product-price .text-danger {
    animation: priceGlow 2s infinite;
}

@keyframes priceGlow {
    0%, 100% {
        text-shadow: 0 0 5px rgba(220, 38, 38, 0.3);
    }
    50% {
        text-shadow: 0 0 15px rgba(220, 38, 38, 0.6);
    }
}

/* Neon Glow Effects */
.filter-card:hover {
    box-shadow: 0 0 30px rgba(102, 126, 234, 0.6),
                0 0 60px rgba(118, 75, 162, 0.4) !important;
}

.btn-primary.active, .btn-outline-primary.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    box-shadow: 0 0 20px rgba(102, 126, 234, 0.6);
}

/* Enhanced Badge Animations */
.badge {
    position: relative;
    overflow: hidden;
}

.badge::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.badge:hover::after {
    width: 200px;
    height: 200px;
}

/* Sparkle Effect */
@keyframes sparkle {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(0.8);
    }
}

.product-badges .badge.bg-danger {
    animation: sparkle 1.5s infinite, badgePulse 2s infinite;
}

/* Search Input Glow */
#searchInput:focus {
    box-shadow: 0 0 20px rgba(102, 126, 234, 0.4) !important;
    border-color: #667eea !important;
}

/* Stats Bar Animation */
.bg-light.rounded-3 {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    position: relative;
    overflow: hidden;
}

.bg-light.rounded-3::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
    animation: shine 3s infinite;
}

@keyframes shine {
    to {
        left: 100%;
    }
}

/* Price Tag Special Effect */
.product-price .h5 {
    text-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}

/* Floating Animation for Wishlist */
@keyframes float-smooth {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

#wishlistFloat {
    animation: float-smooth 3s ease-in-out infinite;
}

/* Rainbow Border Effect - Disabled for better performance */

/* Products Grid Section Background */
.products-grid-section {
    padding: 2rem;
    border-radius: 20px;
    background-color: #fafafa;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(118, 75, 162, 0.03) 0%, transparent 50%),
        radial-gradient(rgba(102, 126, 234, 0.1) 1px, transparent 1px);
    background-size: 100% 100%, 100% 100%, 20px 20px;
    position: relative;
}

.products-grid-section::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #667eea, #764ba2, #f093fb);
    border-radius: 20px;
    z-index: -1;
    opacity: 0.1;
}

/* Responsive */
@media (max-width: 768px) {
    .product-img {
        height: 200px;
    }
    
    .quick-actions {
        opacity: 1;
        transform: translateX(0);
    }
    
    #backToTop, #wishlistFloat button {
        width: 45px;
        height: 45px;
    }
    
    #scrollProgress {
        height: 3px;
    }
    
    .filter-icon-bg {
        width: 50px;
        height: 50px;
    }
    
    .product-card.list-view {
        flex-direction: column;
    }
    
    .product-card.list-view .product-image-wrapper {
        width: 100%;
    }
    
    .products-grid-section {
        padding: 1rem;
        background-size: 100% 100%, 100% 100%, 15px 15px;
    }
    
    .blink-legend {
        bottom: 10px;
        left: 10px;
        right: 10px;
        min-width: auto;
        padding: 12px 15px;
        font-size: 0.8rem;
    }
    
    .legend-items {
        flex-direction: row;
        flex-wrap: wrap;
    }
    
    .legend-item {
        flex: 1 1 auto;
        min-width: 100px;
    }
}

/* Zoom Modal */
.zoom-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    animation: fadeIn 0.3s ease;
}

.zoom-backdrop {
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: zoom-out;
}

.zoom-img {
    max-width: 90%;
    max-height: 90vh;
    object-fit: contain;
    transform: scale(0.8);
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    border-radius: 10px;
}

.zoom-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 1.5rem;
    cursor: pointer;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.zoom-close:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg) scale(1.1);
}

/* Removed 3D Tilt Effect to fix click issues */

/* Blink Legend */
.blink-legend {
    position: fixed;
    bottom: 80px;
    left: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    z-index: 1000;
    min-width: 250px;
    animation: slideInLeft 0.5s ease forwards;
    transition: opacity 0.3s ease;
}

.legend-header {
    display: flex;
    align-items: center;
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 0.9rem;
}

.legend-items {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 5px;
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
}

.legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 10px currentColor;
    animation: pulse 2s infinite;
}

/* Keyboard Shortcuts Indicator */
.keyboard-hint {
    position: fixed;
    bottom: 20px;
    left: 20px;
    background: rgba(0,0,0,0.9);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    font-size: 0.85rem;
    z-index: 1000;
    opacity: 0;
    animation: slideInLeft 0.5s ease forwards;
}

@keyframes slideInLeft {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.keyboard-hint kbd {
    background: #667eea;
    padding: 3px 8px;
    border-radius: 5px;
    font-family: monospace;
    margin: 0 5px;
}

/* Loading animation for images */
.card-img-top {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Add to Cart Button Animation */
.btn.btn-success {
    animation: successPulse 0.6s ease;
}

@keyframes successPulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Enhanced Card Shadows */
.product-card {
    box-shadow: 
        0 1px 3px rgba(0,0,0,0.05),
        0 5px 15px rgba(0,0,0,0.03);
}

.product-card:hover {
    box-shadow: 
        0 10px 30px rgba(102, 126, 234, 0.15),
        0 20px 60px rgba(118, 75, 162, 0.1),
        0 1px 3px rgba(0,0,0,0.05) !important;
}

.card-img-top[src] {
    animation: none;
    background: none;
}

/* Hero Badge Hover Effects */
.hero-badge {
    transition: all 0.3s ease;
    display: inline-block;
}

.hero-badge:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.hero-badge.bg-white:hover {
    background: #f8f9fa !important;
}

.hero-badge.bg-success:hover {
    background: #28a745 !important;
    opacity: 0.9;
}

.hero-badge.bg-info:hover {
    background: #17a2b8 !important;
    opacity: 0.9;
}

/* Smooth Scroll */
html {
    scroll-behavior: smooth;
}

/* Floating Compare Bar */
.floating-compare-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 -5px 20px rgba(0,0,0,0.2);
    padding: 15px 0;
    z-index: 1030;
    animation: slideInUp 0.5s ease;
}

@keyframes slideInUp {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}

.floating-compare-bar .btn-light {
    background: white;
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.floating-compare-bar .btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.floating-compare-bar .btn-outline-light {
    border: 2px solid white;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.floating-compare-bar .btn-outline-light:hover {
    background: white;
    color: #667eea;
    transform: translateY(-2px);
}

/* Quick View Modal */
#quickViewModal .modal-content {
    border-radius: 20px;
    overflow: hidden;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

#quickViewModal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

#quickViewModal .btn-close {
    filter: brightness(0) invert(1);
}
</style>

<!-- Floating Compare Bar -->
<div id="compareBar" class="floating-compare-bar" style="display: none;">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fas fa-balance-scale fa-2x text-white me-3"></i>
                <div>
                    <h6 class="text-white mb-0">Compare Products</h6>
                    <small class="text-white-50"><span id="compareCount">0</span> items selected</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button onclick="viewComparison()" class="btn btn-light btn-sm">
                    <i class="fas fa-eye me-1"></i>Compare Now
                </button>
                <button onclick="clearComparison()" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-trash me-1"></i>Clear All
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Product Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Quick View</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="quickViewContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update Compare Bar
function updateCompareBar() {
    const compareList = JSON.parse(localStorage.getItem('compareList') || '[]');
    const compareBar = document.getElementById('compareBar');
    const compareCount = document.getElementById('compareCount');
    
    if (compareList.length > 0) {
        compareBar.style.display = 'block';
        compareCount.textContent = compareList.length;
    } else {
        compareBar.style.display = 'none';
    }
}

function viewComparison() {
    const compareList = JSON.parse(localStorage.getItem('compareList') || '[]');
    if (compareList.length > 1) {
        window.location.href = '<?php echo SITE_URL; ?>/compare.php';
    } else {
        showNotification('Please add at least 2 products to compare', 'info');
    }
}

function clearComparison() {
    localStorage.removeItem('compareList');
    updateCompareBar();
    showNotification('✓ Comparison list cleared', 'success');
}

// Initialize
updateCompareBar();
</script>

<?php require_once 'includes/footer.php'; ?>