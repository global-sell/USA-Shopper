<?php
require_once 'config/config.php';

$db = Database::getInstance();

// Check if blogs table exists
$tableCheck = $db->query("SHOW TABLES LIKE 'blogs'");
$tableExists = $tableCheck->num_rows > 0;

$blog = null;
$blogs = [];

if ($tableExists) {
    // Get all blogs for sidebar
    $blogsResult = $db->query("SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT 10");
    if ($blogsResult) {
        $blogs = $blogsResult->fetch_all(MYSQLI_ASSOC);
        
        // Format blogs for display
        foreach ($blogs as &$b) {
            if (!empty($b['featured_image'])) {
                $b['image'] = UPLOADS_URL . '/blogs/' . $b['featured_image'];
            } else {
                $b['image'] = 'https://via.placeholder.com/400x250/667eea/ffffff?text=' . urlencode(substr($b['title'], 0, 20));
            }
            $b['date'] = $b['created_at'];
        }
        unset($b);
    }
    // Get blog by slug or id (backward compatibility)
    $slug = sanitizeInput($_GET['slug'] ?? '');
    $blogId = (int)($_GET['id'] ?? 0);
    
    if (!empty($slug)) {
        // Get blog by slug
        $stmt = $db->prepare("SELECT * FROM blogs WHERE slug = ? AND status = 'published'");
        $stmt->bind_param("s", $slug);
    } elseif ($blogId > 0) {
        // Get blog by id (backward compatibility)
        $stmt = $db->prepare("SELECT * FROM blogs WHERE id = ? AND status = 'published'");
        $stmt->bind_param("i", $blogId);
    }
    
    if (isset($stmt)) {
        $stmt->execute();
        $blog = $stmt->get_result()->fetch_assoc();
    }
    
    if ($blog) {
        $blogId = $blog['id'];
        
        // Redirect to slug-based URL if accessed via id
        if (empty($slug) && !empty($blog['slug'])) {
            redirect(SITE_URL . '/blog/' . $blog['slug']);
        }
        
        // Update view count
        $db->query("UPDATE blogs SET views = views + 1 WHERE id = $blogId");
        
        // Format for display
        if (!empty($blog['featured_image'])) {
            $blog['image'] = UPLOADS_URL . '/blogs/' . $blog['featured_image'];
        } else {
            $blog['image'] = 'https://via.placeholder.com/1200x600/667eea/ffffff?text=' . urlencode(substr($blog['title'], 0, 20));
        }
        
        // Parse tags
        if (!empty($blog['tags'])) {
            $blog['tags'] = array_map('trim', explode(',', $blog['tags']));
        } else {
            $blog['tags'] = [];
        }
        
        $blog['date'] = $blog['created_at'];
    }
}

// Fallback to sample data if table doesn't exist or blog not found
if (!$blog) {
    $blogs = [
    [
        'id' => 1,
        'slug' => 'shopping-tips-2025',
        'title' => 'Top 10 Shopping Tips for Smart Buyers in 2025',
        'excerpt' => 'Discover the best strategies to save money and find quality products online. Learn how to spot deals, compare prices, and shop smarter.',
        'content' => '<p>Shopping online has become an essential part of modern life, but with so many options available, it can be overwhelming to make the right choices. Here are our top 10 tips to help you become a smarter shopper in 2025.</p>

<h3>1. Compare Prices Across Multiple Platforms</h3>
<p>Never settle for the first price you see. Use price comparison tools and check multiple websites to ensure you\'re getting the best deal possible.</p>

<h3>2. Read Customer Reviews</h3>
<p>Real customer reviews provide invaluable insights into product quality, shipping times, and overall satisfaction. Look for detailed reviews with photos.</p>

<h3>3. Sign Up for Price Alerts</h3>
<p>Many websites offer price drop notifications. Set up alerts for items you\'re interested in and wait for the perfect moment to buy.</p>

<h3>4. Use Cashback and Rewards Programs</h3>
<p>Take advantage of cashback websites and credit card rewards to get money back on your purchases.</p>

<h3>5. Check Return Policies</h3>
<p>Always review the return policy before making a purchase. A flexible return policy can save you from buyer\'s remorse.</p>

<h3>6. Look for Coupon Codes</h3>
<p>Before checking out, search for coupon codes. Many websites offer first-time buyer discounts or seasonal promotions.</p>

<h3>7. Shop During Sales Events</h3>
<p>Plan major purchases around sales events like Black Friday, Cyber Monday, or seasonal clearances.</p>

<h3>8. Verify Seller Authenticity</h3>
<p>Make sure you\'re buying from reputable sellers. Check ratings, reviews, and business credentials.</p>

<h3>9. Use Secure Payment Methods</h3>
<p>Always use secure payment methods and avoid sharing sensitive information on unsecured websites.</p>

<h3>10. Create a Shopping List</h3>
<p>Avoid impulse purchases by creating a shopping list and sticking to it. This helps you stay within budget and buy only what you need.</p>

<p>By following these tips, you\'ll become a more informed and confident shopper, saving both time and money while getting the products you truly want.</p>',
        'image' => 'https://via.placeholder.com/1200x600/667eea/ffffff?text=Shopping+Tips',
        'category' => 'Shopping Guide',
        'date' => '2025-01-15',
        'author' => 'Sarah Johnson',
        'tags' => ['Shopping', 'Tips', 'Smart Buying', 'Online Shopping']
    ],
    [
        'id' => 2,
        'slug' => 'perfect-electronics-guide',
        'title' => 'How to Choose the Perfect Electronics for Your Home',
        'excerpt' => 'A comprehensive guide to selecting the right gadgets and electronics that fit your lifestyle and budget.',
        'content' => '<p>Choosing the right electronics for your home can be challenging with so many options available. This guide will help you make informed decisions.</p>

<h3>Understanding Your Needs</h3>
<p>Before purchasing any electronic device, assess your actual needs. Consider how you\'ll use the device, who will use it, and what features are essential versus nice-to-have.</p>

<h3>Budget Considerations</h3>
<p>Set a realistic budget and stick to it. Remember that the most expensive option isn\'t always the best for your specific needs.</p>

<h3>Research and Compare</h3>
<p>Read professional reviews, watch video demonstrations, and compare specifications across different brands and models.</p>

<h3>Energy Efficiency</h3>
<p>Look for energy-efficient models that will save you money on electricity bills in the long run.</p>

<h3>Warranty and Support</h3>
<p>Check the warranty period and available customer support. Good after-sales service is crucial for electronics.</p>

<p>Making smart choices when buying electronics ensures you get the best value for your money and products that truly enhance your daily life.</p>',
        'image' => 'https://via.placeholder.com/1200x600/4CAF50/ffffff?text=Electronics+Guide',
        'category' => 'Electronics',
        'date' => '2025-01-12',
        'author' => 'Mike Chen',
        'tags' => ['Electronics', 'Home', 'Gadgets', 'Technology']
    ],
    [
        'id' => 3,
        'slug' => 'sustainable-shopping-guide',
        'title' => 'Sustainable Shopping: Eco-Friendly Products You Need',
        'excerpt' => 'Make a positive impact on the environment with these eco-friendly product recommendations and sustainable shopping practices.',
        'content' => '<p>Sustainable shopping is more than a trend—it\'s a responsibility. Here\'s how you can make environmentally conscious choices.</p>

<h3>Why Sustainable Shopping Matters</h3>
<p>Every purchase we make has an environmental impact. By choosing sustainable products, we reduce waste, conserve resources, and support ethical businesses.</p>

<h3>Look for Eco-Certifications</h3>
<p>Products with certifications like Fair Trade, Organic, or Energy Star meet specific environmental standards.</p>

<h3>Choose Quality Over Quantity</h3>
<p>Invest in durable, high-quality products that last longer rather than cheap items that need frequent replacement.</p>

<h3>Support Local and Small Businesses</h3>
<p>Local products often have a smaller carbon footprint and support your community\'s economy.</p>

<h3>Reduce Packaging Waste</h3>
<p>Choose products with minimal or recyclable packaging whenever possible.</p>

<p>Together, we can make a difference through conscious shopping decisions that benefit both our planet and future generations.</p>',
        'image' => 'https://via.placeholder.com/1200x600/2196F3/ffffff?text=Eco+Friendly',
        'category' => 'Sustainability',
        'date' => '2025-01-10',
        'author' => 'Emma Green',
        'tags' => ['Sustainability', 'Eco-Friendly', 'Green Living', 'Environment']
    ],
    [
        'id' => 4,
        'slug' => 'fashion-trends-2025',
        'title' => 'Fashion Trends 2025: What\'s Hot This Season',
        'excerpt' => 'Stay ahead of the curve with the latest fashion trends, style tips, and must-have items for the new season.',
        'content' => '<p>Fashion is constantly evolving, and 2025 brings exciting new trends that blend style with comfort and sustainability.</p>

<h3>Bold Colors and Patterns</h3>
<p>This season is all about making a statement with vibrant colors and eye-catching patterns.</p>

<h3>Sustainable Fashion</h3>
<p>Eco-friendly materials and ethical production methods are becoming mainstream in the fashion industry.</p>

<h3>Comfort Meets Style</h3>
<p>The athleisure trend continues, with comfortable yet stylish pieces perfect for both work and leisure.</p>

<h3>Vintage Revival</h3>
<p>Retro styles from the 90s and early 2000s are making a comeback with modern twists.</p>

<h3>Minimalist Accessories</h3>
<p>Simple, elegant accessories that complement rather than overpower your outfit are trending.</p>

<p>Stay fashionable while expressing your unique personality with these trending styles.</p>',
        'image' => 'https://via.placeholder.com/1200x600/FF9800/ffffff?text=Fashion+Trends',
        'category' => 'Fashion',
        'date' => '2025-01-08',
        'author' => 'Lisa Martinez',
        'tags' => ['Fashion', 'Trends', 'Style', 'Clothing']
    ],
    [
        'id' => 5,
        'title' => 'Home Organization Hacks That Actually Work',
        'excerpt' => 'Transform your living space with these practical organization tips and product recommendations.',
        'content' => '<p>A well-organized home reduces stress and increases productivity. Here are proven organization hacks that really work.</p>

<h3>Start with Decluttering</h3>
<p>Before organizing, remove items you no longer need. Donate, sell, or recycle to create more space.</p>

<h3>Use Vertical Space</h3>
<p>Install shelves, hooks, and wall-mounted organizers to maximize vertical storage.</p>

<h3>Label Everything</h3>
<p>Clear labels help everyone in the household know where things belong, making it easier to maintain organization.</p>

<h3>Invest in Storage Solutions</h3>
<p>Quality storage containers, drawer dividers, and closet organizers make a huge difference.</p>

<h3>Create Daily Routines</h3>
<p>Spend 10-15 minutes each day tidying up to prevent clutter from accumulating.</p>

<p>With these simple hacks, you can create a more organized, peaceful living environment.</p>',
        'image' => 'https://via.placeholder.com/1200x600/E91E63/ffffff?text=Home+Organization',
        'category' => 'Home & Living',
        'date' => '2025-01-05',
        'author' => 'David Wilson',
        'tags' => ['Home', 'Organization', 'Declutter', 'Storage']
    ],
    [
        'id' => 6,
        'title' => 'The Ultimate Gift Guide for Every Occasion',
        'excerpt' => 'Find the perfect gift for your loved ones with our curated selection of thoughtful and unique presents.',
        'content' => '<p>Gift-giving doesn\'t have to be stressful. This comprehensive guide will help you find the perfect present for any occasion.</p>

<h3>For the Tech Enthusiast</h3>
<p>Latest gadgets, smart home devices, and innovative tech accessories always make great gifts for technology lovers.</p>

<h3>For the Homebody</h3>
<p>Cozy blankets, scented candles, premium coffee or tea sets, and comfortable loungewear are perfect for those who love staying home.</p>

<h3>For the Fitness Fanatic</h3>
<p>Workout equipment, fitness trackers, athletic wear, and healthy meal prep containers support their active lifestyle.</p>

<h3>For the Foodie</h3>
<p>Gourmet ingredients, cooking gadgets, cookbook collections, and restaurant gift cards delight food enthusiasts.</p>

<h3>For the Creative Soul</h3>
<p>Art supplies, craft kits, DIY project materials, and creative workshops inspire artistic expression.</p>

<h3>Personalized Gifts</h3>
<p>Custom items with names, photos, or special messages add a personal touch that shows you care.</p>

<p>Remember, the best gifts come from the heart and show that you truly know and appreciate the recipient.</p>',
        'image' => 'https://via.placeholder.com/1200x600/9C27B0/ffffff?text=Gift+Guide',
        'category' => 'Gift Ideas',
        'date' => '2025-01-03',
        'author' => 'Rachel Adams',
        'tags' => ['Gifts', 'Shopping', 'Occasions', 'Ideas']
    ]
];
    
    // Find the blog post from sample data
    foreach ($blogs as $b) {
        if ($b['id'] == $blogId) {
            $blog = $b;
            break;
        }
    }
    
    // Redirect if blog not found
    if (!$blog) {
        redirect(SITE_URL . '/blogs.php');
    }
}

$pageTitle = $blog['title'];
require_once 'includes/header.php';
?>

<!-- Reading Progress Bar -->
<div class="reading-progress-bar" id="readingProgress"></div>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/blogs.php">Blog</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($blog['title']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Featured Image -->
            <div class="card border-0 shadow-sm mb-4">
                <img src="<?php echo $blog['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($blog['title']); ?>" style="height: 400px; object-fit: cover;">
            </div>

            <!-- Article Content -->
            <article class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <!-- Category Badge -->
                    <span class="badge bg-primary mb-3"><?php echo htmlspecialchars($blog['category']); ?></span>
                    
                    <!-- Title -->
                    <h1 class="fw-bold mb-4"><?php echo htmlspecialchars($blog['title']); ?></h1>
                    
                    <!-- Enhanced Meta Info -->
                    <div class="row align-items-center mb-4 pb-4 border-bottom">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center me-3" 
                                     style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <span class="fw-bold fs-4"><?php echo strtoupper(substr($blog['author'], 0, 1)); ?></span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold fs-5"><?php echo htmlspecialchars($blog['author']); ?></p>
                                    <small class="text-muted d-flex align-items-center gap-2 flex-wrap">
                                        <span><i class="fas fa-calendar me-1"></i><?php echo date('M j, Y', strtotime($blog['date'])); ?></span>
                                        <span>•</span>
                                        <span><i class="fas fa-clock me-1"></i><?php echo ceil(str_word_count(strip_tags($blog['content'])) / 200); ?> min read</span>
                                        <span>•</span>
                                        <span><i class="fas fa-eye me-1"></i><?php echo rand(1000, 9999); ?> views</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <div class="d-flex gap-2 justify-content-md-end">
                                <button class="btn btn-outline-danger btn-sm reaction-btn" onclick="reactToPost('like')">
                                    <i class="fas fa-heart me-1"></i><span id="likeCount"><?php echo rand(10, 99); ?></span>
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="bookmarkPost()">
                                    <i class="fas fa-bookmark me-1"></i>Save
                                </button>
                                <button class="btn btn-outline-success btn-sm" onclick="copyLink()">
                                    <i class="fas fa-link me-1"></i>Copy Link
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="blog-content">
                        <?php echo $blog['content']; ?>
                    </div>
                    
                    <!-- Tags -->
                    <div class="mt-5 pt-4 border-top">
                        <h6 class="fw-bold mb-3">Tags:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($blog['tags'] as $tag): ?>
                                <span class="badge bg-light text-dark"><?php echo htmlspecialchars($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Enhanced Share Buttons -->
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="fw-bold mb-3"><i class="fas fa-share-alt me-2"></i>Share this article:</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/blog/' . $blog['slug']); ?>" 
                               target="_blank" class="btn btn-facebook btn-sm">
                                <i class="fab fa-facebook-f me-1"></i>Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/blog/' . $blog['slug']); ?>&text=<?php echo urlencode($blog['title']); ?>" 
                               target="_blank" class="btn btn-twitter btn-sm">
                                <i class="fab fa-twitter me-1"></i>Twitter
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(SITE_URL . '/blog/' . $blog['slug']); ?>&title=<?php echo urlencode($blog['title']); ?>" 
                               target="_blank" class="btn btn-linkedin btn-sm">
                                <i class="fab fa-linkedin-in me-1"></i>LinkedIn
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($blog['title'] . ' ' . SITE_URL . '/blog/' . $blog['slug']); ?>" 
                               target="_blank" class="btn btn-whatsapp btn-sm">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>
                            <button onclick="printArticle()" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-print me-1"></i>Print
                            </button>
                        </div>
                    </div>
                </div>
            </article>
            
            <!-- Author Card -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center me-3" 
                             style="width: 70px; height: 70px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <span class="fw-bold fs-3"><?php echo strtoupper(substr($blog['author'], 0, 1)); ?></span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($blog['author']); ?></h5>
                            <small class="text-muted">Content Writer & Blogger</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Passionate about creating valuable content that helps readers make informed decisions. Specializing in technology, lifestyle, and consumer trends.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-globe"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Comments Section -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-comments me-2"></i>Comments (<?php echo rand(5, 20); ?>)</h5>
                    
                    <!-- Comment Form -->
                    <form id="commentForm" onsubmit="submitComment(event)" class="mb-4">
                        <div class="mb-3">
                            <textarea class="form-control" rows="3" placeholder="Share your thoughts..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Be respectful and constructive</small>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-paper-plane me-1"></i>Post Comment
                            </button>
                        </div>
                    </form>
                    
                    <!-- Sample Comments -->
                    <div class="comment-list">
                        <div class="comment-item mb-3 pb-3 border-bottom">
                            <div class="d-flex">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <span class="fw-bold">J</span>
                                </div>
                                <div>
                                    <p class="mb-1 fw-bold">John Doe <small class="text-muted fw-normal">• 2 hours ago</small></p>
                                    <p class="mb-2 small">Great article! Very informative and well-written. Thanks for sharing!</p>
                                    <div class="d-flex gap-3">
                                        <button class="btn btn-link btn-sm p-0 text-decoration-none"><i class="fas fa-thumbs-up me-1"></i>Like (5)</button>
                                        <button class="btn btn-link btn-sm p-0 text-decoration-none"><i class="fas fa-reply me-1"></i>Reply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="comment-item mb-3">
                            <div class="d-flex">
                                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <span class="fw-bold">A</span>
                                </div>
                                <div>
                                    <p class="mb-1 fw-bold">Alice Smith <small class="text-muted fw-normal">• 5 hours ago</small></p>
                                    <p class="mb-2 small">This helped me so much! I'll definitely be implementing these tips.</p>
                                    <div class="d-flex gap-3">
                                        <button class="btn btn-link btn-sm p-0 text-decoration-none"><i class="fas fa-thumbs-up me-1"></i>Like (12)</button>
                                        <button class="btn btn-link btn-sm p-0 text-decoration-none"><i class="fas fa-reply me-1"></i>Reply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-outline-primary btn-sm w-100" onclick="loadMoreComments()">
                        <i class="fas fa-chevron-down me-1"></i>Load More Comments
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Recent Posts -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Recent Posts</h5>
                    <?php 
                    $recentPosts = array_filter($blogs, function($b) use ($blogId) {
                        return $b['id'] != $blogId;
                    });
                    $recentPosts = array_slice($recentPosts, 0, 4);
                    foreach ($recentPosts as $recent): 
                    ?>
                        <div class="mb-3 pb-3 <?php echo $recent !== end($recentPosts) ? 'border-bottom' : ''; ?>">
                            <a href="<?php echo SITE_URL; ?>/blog/<?php echo $recent['slug']; ?>" 
                               class="text-decoration-none text-dark">
                                <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($recent['title']); ?></h6>
                            </a>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i><?php echo date('M j, Y', strtotime($recent['date'])); ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Categories -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Categories</h5>
                    <?php 
                    $categories = array_unique(array_column($blogs, 'category'));
                    foreach ($categories as $cat): 
                    ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><?php echo htmlspecialchars($cat); ?></span>
                            <span class="badge bg-light text-dark">
                                <?php echo count(array_filter($blogs, function($b) use ($cat) { return $b['category'] == $cat; })); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Customer Reviews Widget -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-star text-warning me-2"></i>Customer Reviews</h5>
                    <div id="customerReviews">
                        <!-- Reviews will be dynamically loaded here -->
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted"><i class="fas fa-sync-alt me-1"></i>Reviews rotate automatically</small>
                    </div>
                </div>
            </div>
            
            <!-- Newsletter -->
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <h5 class="fw-bold mb-3">Subscribe to Newsletter</h5>
                    <p class="small mb-3">Get the latest posts delivered right to your inbox.</p>
                    <form>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Your email" required>
                        </div>
                        <button type="submit" class="btn btn-light w-100">
                            <i class="fas fa-paper-plane me-2"></i>Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Full Width Customer Testimonials Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body p-5 text-white">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-2"><i class="fas fa-quote-left me-3"></i>What Our Readers Say</h2>
                        <p class="mb-0">Real feedback from our amazing community</p>
                    </div>
                    
                    <div class="row g-4" id="mainReviews">
                        <!-- Main reviews will be loaded here -->
                    </div>
                    
                    <div class="text-center mt-4">
                        <button class="btn btn-light" onclick="loadNewReviews()">
                            <i class="fas fa-random me-2"></i>Show Different Reviews
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Posts -->
    <div class="mt-5">
        <h3 class="fw-bold mb-4">You May Also Like</h3>
        <div class="row g-4">
            <?php 
            $relatedPosts = array_filter($blogs, function($b) use ($blogId, $blog) {
                return $b['id'] != $blogId && $b['category'] == $blog['category'];
            });
            $relatedPosts = array_slice($relatedPosts, 0, 3);
            
            if (empty($relatedPosts)) {
                $relatedPosts = array_filter($blogs, function($b) use ($blogId) {
                    return $b['id'] != $blogId;
                });
                $relatedPosts = array_slice($relatedPosts, 0, 3);
            }
            
            foreach ($relatedPosts as $related): 
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <img src="<?php echo $related['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($related['title']); ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($related['category']); ?></span>
                            <h6 class="fw-bold"><?php echo htmlspecialchars($related['title']); ?></h6>
                            <p class="text-muted small"><?php echo htmlspecialchars($related['excerpt']); ?></p>
                            <a href="<?php echo SITE_URL; ?>/blog/<?php echo $related['slug']; ?>" 
                               class="btn btn-outline-primary btn-sm">
                                Read More
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.blog-content h3 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.blog-content p {
    margin-bottom: 1.5rem;
    line-height: 1.8;
    color: #555;
}

.blog-content ul, .blog-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.blog-content li {
    margin-bottom: 0.5rem;
    line-height: 1.8;
}

/* Reading Progress Bar */
.reading-progress-bar {
    position: fixed;
    top: 0;
    left: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    z-index: 9999;
    width: 0%;
    transition: width 0.1s ease;
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.5);
}

/* Blog Content Styling */
.blog-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
}

.blog-content h3 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #667eea;
    font-weight: 700;
}

.blog-content p {
    margin-bottom: 1.5rem;
}

/* Social Share Buttons */
.btn-facebook { background: #1877f2; color: white; border: none; }
.btn-facebook:hover { background: #0d6efd; color: white; }
.btn-twitter { background: #1da1f2; color: white; border: none; }
.btn-twitter:hover { background: #0c8bd9; color: white; }
.btn-linkedin { background: #0077b5; color: white; border: none; }
.btn-linkedin:hover { background: #006399; color: white; }
.btn-whatsapp { background: #25d366; color: white; border: none; }
.btn-whatsapp:hover { background: #20bd5a; color: white; }

/* Reaction Button */
.reaction-btn.liked {
    background: #dc2626;
    color: white;
    border-color: #dc2626;
}

.reaction-btn.liked:hover {
    background: #b91c1c;
    border-color: #b91c1c;
}

/* Comments Section */
.comment-item {
    transition: all 0.3s ease;
}

.comment-item:hover {
    background: #f8f9fa;
    padding: 10px;
    margin: -10px;
    border-radius: 8px;
}

/* Author Card Animation */
.card:hover {
    transform: translateY(-5px);
    transition: all 0.3s ease;
}

/* Tag Badges */
.badge.bg-light {
    transition: all 0.3s ease;
    cursor: pointer;
}

.badge.bg-light:hover {
    background: #667eea !important;
    color: white !important;
    transform: scale(1.05);
}

/* Animations */
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

.card {
    animation: fadeInUp 0.5s ease;
}

/* Print Styles */
@media print {
    .reading-progress-bar, 
    nav, 
    .btn, 
    .sidebar,
    .comment-form,
    footer { display: none !important; }
    
    .blog-content { font-size: 12pt; }
}

/* Customer Reviews Styling */
.review-item {
    transition: all 0.3s ease;
}

.review-item:hover {
    background: #f8f9fa;
    padding: 10px;
    margin: -10px;
    border-radius: 8px;
}

.review-card {
    transition: all 0.3s ease;
    border: 2px solid rgba(255,255,255,0.1);
}

.review-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    border-color: rgba(255,255,255,0.3);
}

/* Review Stars Animation */
.review-item .fa-star,
.review-card .fa-star {
    transition: all 0.3s ease;
}

.review-item:hover .fa-star,
.review-card:hover .fa-star {
    transform: scale(1.2);
}

/* Testimonials Section */
#mainReviews .col-md-4 {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

#mainReviews .col-md-4:nth-child(1) {
    animation-delay: 0.1s;
}

#mainReviews .col-md-4:nth-child(2) {
    animation-delay: 0.2s;
}

#mainReviews .col-md-4:nth-child(3) {
    animation-delay: 0.3s;
}
</style>

<script>
// Reading Progress Bar
function updateReadingProgress() {
    const article = document.querySelector('.blog-content');
    if (!article) return;
    
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const articleTop = article.offsetTop;
    const articleHeight = article.offsetHeight;
    const windowHeight = window.innerHeight;
    
    const scrollDistance = scrollTop - articleTop + windowHeight;
    const totalDistance = articleHeight + windowHeight;
    const percentage = Math.min(100, Math.max(0, (scrollDistance / totalDistance) * 100));
    
    document.getElementById('readingProgress').style.width = percentage + '%';
}

window.addEventListener('scroll', updateReadingProgress);
window.addEventListener('resize', updateReadingProgress);

// Like/React to Post
let hasLiked = localStorage.getItem('liked_<?php echo $blog['id'] ?? 1; ?>') === 'true';
const likeBtn = document.querySelector('.reaction-btn');
if (hasLiked && likeBtn) {
    likeBtn.classList.add('liked');
}

function reactToPost(type) {
    const likeCount = document.getElementById('likeCount');
    const currentCount = parseInt(likeCount.textContent);
    const btn = document.querySelector('.reaction-btn');
    
    if (!hasLiked) {
        likeCount.textContent = currentCount + 1;
        btn.classList.add('liked');
        hasLiked = true;
        localStorage.setItem('liked_<?php echo $blog['id'] ?? 1; ?>', 'true');
        showNotification('❤️ Thanks for your reaction!', 'success');
        
        // Confetti effect
        createConfetti();
    } else {
        likeCount.textContent = currentCount - 1;
        btn.classList.remove('liked');
        hasLiked = false;
        localStorage.removeItem('liked_<?php echo $blog['id'] ?? 1; ?>');
        showNotification('Reaction removed', 'info');
    }
}

// Bookmark Post
function bookmarkPost() {
    const bookmarked = localStorage.getItem('bookmarked_<?php echo $blog['id'] ?? 1; ?>');
    
    if (!bookmarked) {
        localStorage.setItem('bookmarked_<?php echo $blog['id'] ?? 1; ?>', 'true');
        showNotification('📚 Article saved to bookmarks!', 'success');
    } else {
        localStorage.removeItem('bookmarked_<?php echo $blog['id'] ?? 1; ?>');
        showNotification('Removed from bookmarks', 'info');
    }
}

// Copy Link
function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        showNotification('🔗 Link copied to clipboard!', 'success');
    });
}

// Print Article
function printArticle() {
    window.print();
}

// Submit Comment
function submitComment(event) {
    event.preventDefault();
    const form = event.target;
    const textarea = form.querySelector('textarea');
    const button = form.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Posting...';
    button.disabled = true;
    
    setTimeout(() => {
        showNotification('✓ Comment posted successfully!', 'success');
        textarea.value = '';
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Add comment to list (in real app, this would reload from server)
        addCommentToList('You', 'Just now', textarea.value);
    }, 1500);
}

// Add Comment to List
function addCommentToList(author, time, text) {
    const commentList = document.querySelector('.comment-list');
    const newComment = document.createElement('div');
    newComment.className = 'comment-item mb-3 pb-3 border-bottom';
    newComment.innerHTML = `
        <div class="d-flex">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                 style="width: 40px; height: 40px; flex-shrink: 0;">
                <span class="fw-bold">${author.charAt(0)}</span>
            </div>
            <div>
                <p class="mb-1 fw-bold">${author} <small class="text-muted fw-normal">• ${time}</small></p>
                <p class="mb-2 small">${text}</p>
                <div class="d-flex gap-3">
                    <button class="btn btn-link btn-sm p-0 text-decoration-none"><i class="fas fa-thumbs-up me-1"></i>Like (0)</button>
                    <button class="btn btn-link btn-sm p-0 text-decoration-none"><i class="fas fa-reply me-1"></i>Reply</button>
                </div>
            </div>
        </div>
    `;
    commentList.insertBefore(newComment, commentList.firstChild);
}

// Load More Comments
function loadMoreComments() {
    showNotification('Loading more comments...', 'info');
    // In real app, this would load from server
}

// Notification System
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification-toast`;
    notification.style.cssText = 'position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease; box-shadow: 0 5px 20px rgba(0,0,0,0.2);';
    notification.innerHTML = `
        <div class="d-flex align-items-center justify-content-between">
            <span><i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>${message}</span>
            <button type="button" class="btn-close ms-3" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Confetti Effect
function createConfetti() {
    const colors = ['#667eea', '#764ba2', '#f093fb', '#48bb78', '#fbbf24'];
    for (let i = 0; i < 30; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.width = '8px';
        confetti.style.height = '8px';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.left = Math.random() * window.innerWidth + 'px';
        confetti.style.top = '50%';
        confetti.style.opacity = '1';
        confetti.style.zIndex = '9999';
        confetti.style.borderRadius = '50%';
        confetti.style.pointerEvents = 'none';
        document.body.appendChild(confetti);
        
        const angle = Math.random() * Math.PI * 2;
        const velocity = 15 + Math.random() * 10;
        let vx = Math.cos(angle) * velocity;
        let vy = Math.sin(angle) * velocity;
        let x = parseFloat(confetti.style.left);
        let y = window.innerHeight / 2;
        
        const animate = () => {
            vy += 0.5; // gravity
            x += vx;
            y += vy;
            
            confetti.style.left = x + 'px';
            confetti.style.top = y + 'px';
            confetti.style.opacity = Math.max(0, parseFloat(confetti.style.opacity) - 0.02);
            
            if (parseFloat(confetti.style.opacity) > 0 && y < window.innerHeight) {
                requestAnimationFrame(animate);
            } else {
                confetti.remove();
            }
        };
        requestAnimationFrame(animate);
    }
}

// Smooth Scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateReadingProgress();
    loadCustomerReviews();
    loadMainReviews();
    
    // Auto-rotate sidebar reviews every 10 seconds
    setInterval(loadCustomerReviews, 10000);
});

// Customer Reviews Database
const customerReviews = [
    {
        name: "Sarah Johnson",
        location: "New York, USA",
        rating: 5,
        review: "Absolutely love this blog! The content is always informative and well-researched. I've learned so much from reading these articles.",
        avatar: "S",
        color: "#667eea"
    },
    {
        name: "Michael Chen",
        location: "San Francisco, USA",
        rating: 5,
        review: "The best blog I've found for shopping tips and product reviews. Very helpful and trustworthy recommendations!",
        avatar: "M",
        color: "#f093fb"
    },
    {
        name: "Emily Rodriguez",
        location: "London, UK",
        rating: 5,
        review: "I read every single post! The writing style is engaging and the information is always up-to-date. Highly recommended!",
        avatar: "E",
        color: "#48bb78"
    },
    {
        name: "David Park",
        location: "Seoul, Korea",
        rating: 4,
        review: "Great content that actually helps me make better purchasing decisions. The tips are practical and easy to follow.",
        avatar: "D",
        color: "#fbbf24"
    },
    {
        name: "Jessica Williams",
        location: "Toronto, Canada",
        rating: 5,
        review: "This blog has become my go-to resource for product research. The detailed guides are incredibly helpful!",
        avatar: "J",
        color: "#764ba2"
    },
    {
        name: "Ahmed Hassan",
        location: "Dubai, UAE",
        rating: 5,
        review: "Fantastic blog with honest reviews and helpful advice. I've saved so much money by following these tips!",
        avatar: "A",
        color: "#f5576c"
    },
    {
        name: "Sophie Martin",
        location: "Paris, France",
        rating: 5,
        review: "The quality of content here is outstanding. Every article is well-written and provides real value to readers.",
        avatar: "S",
        color: "#17a2b8"
    },
    {
        name: "Carlos Silva",
        location: "São Paulo, Brazil",
        rating: 4,
        review: "I appreciate the honest and unbiased product reviews. It's refreshing to find genuine content online.",
        avatar: "C",
        color: "#dc2626"
    },
    {
        name: "Lisa Anderson",
        location: "Sydney, Australia",
        rating: 5,
        review: "Amazing blog! The shopping guides have helped me find the best deals and avoid scams. Thank you!",
        avatar: "L",
        color: "#9C27B0"
    },
    {
        name: "Kevin O'Brien",
        location: "Dublin, Ireland",
        rating: 5,
        review: "Best blog for consumer advice! The tips are practical, the reviews are thorough, and the content is always fresh.",
        avatar: "K",
        color: "#2196F3"
    },
    {
        name: "Priya Sharma",
        location: "Mumbai, India",
        rating: 5,
        review: "I've recommended this blog to all my friends. The product comparisons are especially helpful for making informed choices.",
        avatar: "P",
        color: "#FF9800"
    },
    {
        name: "Thomas Mueller",
        location: "Berlin, Germany",
        rating: 4,
        review: "Very informative and well-organized content. I always check this blog before making any major purchase.",
        avatar: "T",
        color: "#4CAF50"
    },
    {
        name: "Maria Garcia",
        location: "Madrid, Spain",
        rating: 5,
        review: "Love the detailed analysis and honest opinions. This blog has never steered me wrong!",
        avatar: "M",
        color: "#E91E63"
    },
    {
        name: "James Taylor",
        location: "Los Angeles, USA",
        rating: 5,
        review: "Excellent resource for product information. The guides are comprehensive and easy to understand.",
        avatar: "J",
        color: "#00BCD4"
    },
    {
        name: "Yuki Tanaka",
        location: "Tokyo, Japan",
        rating: 5,
        review: "The blog's content quality is consistently high. I've learned valuable shopping strategies from reading here.",
        avatar: "Y",
        color: "#673AB7"
    }
];

// Load Customer Reviews in Sidebar
function loadCustomerReviews() {
    const container = document.getElementById('customerReviews');
    if (!container) return;
    
    // Get 2 random reviews
    const randomReviews = getRandomReviews(2);
    
    container.innerHTML = randomReviews.map(review => `
        <div class="review-item mb-3 pb-3 border-bottom">
            <div class="d-flex mb-2">
                <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-2" 
                     style="width: 35px; height: 35px; background: ${review.color}; flex-shrink: 0;">
                    <span class="fw-bold small">${review.avatar}</span>
                </div>
                <div>
                    <p class="mb-0 fw-bold small">${review.name}</p>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        <i class="fas fa-map-marker-alt me-1"></i>${review.location}
                    </small>
                </div>
            </div>
            <div class="mb-2">
                ${generateStars(review.rating)}
            </div>
            <p class="small text-muted mb-0">"${review.review.substring(0, 100)}..."</p>
        </div>
    `).join('');
    
    // Add fade animation
    container.style.opacity = '0';
    setTimeout(() => {
        container.style.transition = 'opacity 0.5s ease';
        container.style.opacity = '1';
    }, 50);
}

// Load Main Reviews Section
function loadMainReviews() {
    const container = document.getElementById('mainReviews');
    if (!container) return;
    
    // Get 3 random reviews
    const randomReviews = getRandomReviews(3);
    
    container.innerHTML = randomReviews.map(review => `
        <div class="col-md-4">
            <div class="review-card p-4 rounded-3" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
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
                <div class="mb-3">
                    ${generateStars(review.rating, 'text-warning')}
                </div>
                <p class="mb-0">"${review.review}"</p>
            </div>
        </div>
    `).join('');
    
    // Add fade animation
    container.style.opacity = '0';
    setTimeout(() => {
        container.style.transition = 'opacity 0.5s ease';
        container.style.opacity = '1';
    }, 50);
}

// Load New Random Reviews (Button Click)
function loadNewReviews() {
    loadMainReviews();
    showNotification('✓ Loaded new reviews!', 'success');
}

// Get Random Reviews from Database
function getRandomReviews(count) {
    const shuffled = [...customerReviews].sort(() => Math.random() - 0.5);
    return shuffled.slice(0, count);
}

// Generate Star Rating HTML
function generateStars(rating, colorClass = 'text-warning') {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += `<i class="fas fa-star ${colorClass}"></i>`;
        } else {
            stars += `<i class="far fa-star ${colorClass}"></i>`;
        }
    }
    return stars;
}
</script>

<?php require_once 'includes/footer.php'; ?>