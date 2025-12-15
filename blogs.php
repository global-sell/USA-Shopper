<?php
$pageTitle = "Our Blogs";
require_once 'includes/header.php';

$db = Database::getInstance();

// Check if blogs table exists
$tableCheck = $db->query("SHOW TABLES LIKE 'blogs'");
$tableExists = $tableCheck->num_rows > 0;

if ($tableExists) {
    // Get blogs from database
    $blogs = $db->query("SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
    
    // Format blogs for display
    foreach ($blogs as &$blog) {
        // Set image URL
        if (!empty($blog['featured_image'])) {
            $blog['image'] = UPLOADS_URL . '/blogs/' . $blog['featured_image'];
        } else {
            $blog['image'] = 'https://via.placeholder.com/400x250/667eea/ffffff?text=' . urlencode(substr($blog['title'], 0, 20));
        }
        // Format date
        $blog['date'] = $blog['created_at'];
    }
    unset($blog);
} else {
    // Fallback to sample data if table doesn't exist
    $blogs = [
        [
            'id' => 1,
            'slug' => 'shopping-tips-2025',
            'title' => 'Top 10 Shopping Tips for Smart Buyers in 2025',
            'excerpt' => 'Discover the best strategies to save money and find quality products online. Learn how to spot deals, compare prices, and shop smarter.',
            'image' => 'https://via.placeholder.com/800x500/667eea/ffffff?text=Shopping+Tips',
            'category' => 'Shopping Guide',
            'date' => '2025-01-15',
            'author' => 'Sarah Johnson'
        ],
        [
            'id' => 2,
            'slug' => 'perfect-electronics-guide',
            'title' => 'How to Choose the Perfect Electronics for Your Home',
            'excerpt' => 'A comprehensive guide to selecting the right gadgets and electronics that fit your lifestyle and budget.',
            'image' => 'https://via.placeholder.com/800x500/4CAF50/ffffff?text=Electronics+Guide',
            'category' => 'Electronics',
            'date' => '2025-01-12',
            'author' => 'Mike Chen'
        ],
        [
            'id' => 3,
            'slug' => 'latest-tech-trends',
            'title' => 'Latest Technology Trends Shaping 2025',
            'excerpt' => 'Explore the cutting-edge innovations and tech trends that are transforming how we live and work.',
            'image' => 'https://via.placeholder.com/800x500/FF5722/ffffff?text=Tech+Trends',
            'category' => 'Technology',
            'date' => '2025-01-10',
            'author' => 'Alex Rivera'
        ],
        [
            'id' => 4,
            'slug' => 'lifestyle-wellness-tips',
            'title' => 'Wellness and Lifestyle Tips for Modern Living',
            'excerpt' => 'Discover practical tips to maintain a healthy work-life balance and boost your overall wellbeing.',
            'image' => 'https://via.placeholder.com/800x500/9C27B0/ffffff?text=Lifestyle+Tips',
            'category' => 'Lifestyle',
            'date' => '2025-01-08',
            'author' => 'Emma Watson'
        ],
        [
            'id' => 5,
            'slug' => 'product-reviews-january',
            'title' => 'Top Product Reviews: Must-Have Items This Month',
            'excerpt' => 'Our expert reviews of the best products available right now, from gadgets to home essentials.',
            'image' => 'https://via.placeholder.com/800x500/FF9800/ffffff?text=Product+Reviews',
            'category' => 'Reviews',
            'date' => '2025-01-05',
            'author' => 'David Park'
        ],
        [
            'id' => 6,
            'slug' => 'smart-home-automation',
            'title' => 'Smart Home Automation: A Beginner\'s Guide',
            'excerpt' => 'Learn how to transform your home with smart devices and automation systems.',
            'image' => 'https://via.placeholder.com/800x500/2196F3/ffffff?text=Smart+Home',
            'category' => 'Technology',
            'date' => '2025-01-03',
            'author' => 'Sarah Johnson'
        ]
    ];
}
?>

<!-- Breaking News Ticker -->
<div class="breaking-news-ticker">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <div class="breaking-label">
                <i class="fas fa-bolt me-2"></i>BREAKING NEWS
            </div>
            <div class="ticker-content">
                <marquee behavior="scroll" direction="left" scrollamount="5">
                    <span class="ticker-item">🔥 New Product Launch: Revolutionary Smart Home Devices Now Available</span>
                    <span class="ticker-item mx-4">•</span>
                    <span class="ticker-item">⚡ Flash Sale Alert: Up to 70% OFF on Electronics - Limited Time Only!</span>
                    <span class="ticker-item mx-4">•</span>
                    <span class="ticker-item">🎉 Join 50,000+ Happy Customers Shopping with Us Today</span>
                </marquee>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-4">
    <!-- News Header with Categories -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="display-5 fw-bold mb-0">Latest News & Insights</h1>
                    <p class="text-muted mb-0">Stay updated with trending stories and expert advice</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-primary active category-filter" data-category="all">All</button>
                    <button class="btn btn-sm btn-outline-primary category-filter" data-category="tech">Technology</button>
                    <button class="btn btn-sm btn-outline-primary category-filter" data-category="shopping">Shopping</button>
                    <button class="btn btn-sm btn-outline-primary category-filter" data-category="lifestyle">Lifestyle</button>
                    <button class="btn btn-sm btn-outline-primary category-filter" data-category="reviews">Reviews</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <?php if (!empty($blogs)): ?>
            <!-- Hero Featured Post -->
            <div class="hero-post mb-4 position-relative overflow-hidden rounded-3">
                <a href="<?php echo SITE_URL; ?>/blog/<?php echo $blogs[0]['slug'] ?? '#'; ?>" class="text-decoration-none">
                    <img src="<?php echo $blogs[0]['image']; ?>" class="w-100" style="height: 500px; object-fit: cover;" alt="Hero">
                    <div class="hero-overlay position-absolute bottom-0 start-0 w-100 p-4">
                        <span class="badge bg-danger mb-2"><i class="fas fa-fire me-1"></i>TRENDING NOW</span>
                        <h2 class="text-white fw-bold mb-2"><?php echo htmlspecialchars($blogs[0]['title']); ?></h2>
                        <p class="text-white-50 mb-3"><?php echo htmlspecialchars(substr($blogs[0]['excerpt'] ?? '', 0, 150)); ?>...</p>
                        <div class="d-flex align-items-center gap-3 text-white-50">
                            <span><i class="fas fa-user me-2"></i><?php echo htmlspecialchars($blogs[0]['author'] ?? 'Admin'); ?></span>
                            <span><i class="fas fa-clock me-2"></i><?php echo date('M j, Y', strtotime($blogs[0]['date'] ?? 'now')); ?></span>
                            <span><i class="fas fa-eye me-2"></i><?php echo rand(1000, 9999); ?> views</span>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Trending Grid -->
            <div class="row g-3 mb-4">
                <?php for ($i = 1; $i <= 3 && isset($blogs[$i]); $i++): ?>
                <div class="col-md-4">
                    <div class="news-card-small position-relative overflow-hidden rounded-3">
                        <a href="<?php echo SITE_URL; ?>/blog/<?php echo $blogs[$i]['slug'] ?? '#'; ?>" class="text-decoration-none">
                            <img src="<?php echo $blogs[$i]['image']; ?>" class="w-100" style="height: 200px; object-fit: cover;" alt="News">
                            <div class="news-overlay-small position-absolute bottom-0 start-0 w-100 p-3">
                                <span class="badge bg-warning text-dark mb-2"><i class="fas fa-star me-1"></i>Popular</span>
                                <h6 class="text-white fw-bold mb-0"><?php echo htmlspecialchars(substr($blogs[$i]['title'], 0, 60)); ?>...</h6>
                            </div>
                        </a>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            
            <!-- Latest News Section -->
            <h3 class="fw-bold mb-3"><i class="fas fa-newspaper me-2 text-primary"></i>Latest Stories</h3>
            <div class="row g-4 mb-5">
                <?php foreach (array_slice($blogs, 4) as $blog): ?>
                <div class="col-md-6">
                    <div class="news-card border-0 shadow-sm rounded-3 overflow-hidden h-100 hover-lift">
                        <div class="row g-0">
                            <div class="col-5">
                                <a href="<?php echo SITE_URL; ?>/blog/<?php echo $blog['slug'] ?? '#'; ?>">
                                    <img src="<?php echo $blog['image']; ?>" class="w-100 h-100" style="object-fit: cover; min-height: 180px;" alt="News">
                                </a>
                            </div>
                            <div class="col-7">
                                <div class="card-body p-3">
                                    <span class="badge bg-primary-subtle text-primary mb-2"><?php echo $blog['category'] ?? 'General'; ?></span>
                                    <h6 class="fw-bold mb-2">
                                        <a href="<?php echo SITE_URL; ?>/blog/<?php echo $blog['slug'] ?? '#'; ?>" class="text-dark text-decoration-none hover-primary">
                                            <?php echo htmlspecialchars(substr($blog['title'], 0, 70)); ?>...
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-2"><?php echo htmlspecialchars(substr($blog['excerpt'] ?? '', 0, 80)); ?>...</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i><?php echo date('M j', strtotime($blog['date'] ?? 'now')); ?>
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-eye me-1"></i><?php echo rand(100, 999); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Trending Topics -->
            <div class="card border-0 shadow-sm mb-4 rounded-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fas fa-fire text-danger me-2"></i>Trending Topics</h5>
                    <div class="list-group list-group-flush">
                        <?php for ($i = 0; $i < 5 && isset($blogs[$i]); $i++): ?>
                        <a href="<?php echo SITE_URL; ?>/blog/<?php echo $blogs[$i]['slug'] ?? '#'; ?>" class="list-group-item list-group-item-action border-0 px-0">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-danger me-3 mt-1"><?php echo $i + 1; ?></span>
                                <div>
                                    <h6 class="mb-1 fw-bold small"><?php echo htmlspecialchars(substr($blogs[$i]['title'], 0, 60)); ?>...</h6>
                                    <small class="text-muted"><i class="fas fa-eye me-1"></i><?php echo rand(1000, 9999); ?> views</small>
                                </div>
                            </div>
                        </a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            
            <!-- Popular Categories -->
            <div class="card border-0 shadow-sm mb-4 rounded-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fas fa-th-large text-primary me-2"></i>Categories</h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm text-start category-sidebar-btn" data-category="tech">
                            <i class="fas fa-laptop me-2"></i>TECHNOLOGY <span class="badge bg-primary float-end">45</span>
                        </button>
                        <button class="btn btn-outline-success btn-sm text-start category-sidebar-btn" data-category="shopping">
                            <i class="fas fa-shopping-cart me-2"></i>SHOPPING <span class="badge bg-success float-end">32</span>
                        </button>
                        <button class="btn btn-outline-info btn-sm text-start category-sidebar-btn" data-category="lifestyle">
                            <i class="fas fa-heart me-2"></i>LIFESTYLE <span class="badge bg-info float-end">28</span>
                        </button>
                        <button class="btn btn-outline-warning btn-sm text-start category-sidebar-btn" data-category="reviews">
                            <i class="fas fa-star me-2"></i>REVIEWS <span class="badge bg-warning float-end">21</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Customer Reviews Widget -->
            <div class="card border-0 shadow-sm mb-4 rounded-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fas fa-star text-warning me-2"></i>Customer Reviews</h5>
                    <div id="customerReviews">
                        <!-- Reviews will be dynamically loaded here -->
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted"><i class="fas fa-sync-alt me-1"></i>Reviews rotate automatically</small>
                    </div>
                </div>
            </div>
            
            <!-- Newsletter Box -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="text-center text-white">
                        <i class="fas fa-envelope fa-3x mb-3"></i>
                        <h5 class="fw-bold mb-2">Newsletter</h5>
                        <p class="small mb-3">Get daily news updates!</p>
                        <form id="newsletterForm" onsubmit="subscribeNewsletter(event)">
                            <input type="email" id="newsletterEmail" class="form-control mb-2" placeholder="Your email" required>
                            <button type="submit" class="btn btn-light w-100">
                                <i class="fas fa-paper-plane me-2"></i>SUBSCRIBE
                            </button>
                        </form>
                    </div>
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
                        <button class="btn btn-light btn-lg" onclick="loadNewReviews()">
                            <i class="fas fa-random me-2"></i>Show Different Reviews
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Breaking News Ticker */
.breaking-news-ticker {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    padding: 10px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.breaking-label {
    background: white;
    color: #dc2626;
    padding: 5px 15px;
    font-weight: 700;
    font-size: 0.9rem;
    border-radius: 5px;
    margin-right: 15px;
    animation: pulse 2s infinite;
}

.ticker-content {
    flex: 1;
    overflow: hidden;
}

.ticker-item {
    font-size: 0.95rem;
    font-weight: 500;
}

/* Hero Post */
.hero-post {
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}

.hero-post:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.25);
}

.hero-overlay {
    background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 50%, transparent 100%);
}

.news-overlay-small {
    background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.5) 70%, transparent 100%);
}

.news-card-small {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.news-card-small:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

/* Latest News Cards */
.news-card {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.hover-primary:hover {
    color: #667eea !important;
}

/* Category Filters */
.category-filter {
    transition: all 0.3s ease;
}

.category-filter:hover {
    transform: translateY(-2px);
}

/* Sidebar */
.list-group-item:hover {
    background: #f8f9fa;
    padding-left: 10px !important;
}

/* Animations */
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Responsive */
@media (max-width: 768px) {
    .breaking-label {
        font-size: 0.75rem;
        padding: 3px 10px;
    }
    
    .hero-post img {
        height: 300px !important;
    }
}
</style>

<script>
// Show Notification Function
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification-toast`;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease; box-shadow: 0 5px 20px rgba(0,0,0,0.2);';
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
    }, 4000);
}

// Newsletter Subscription
function subscribeNewsletter(event) {
    event.preventDefault();
    const email = document.getElementById('newsletterEmail').value;
    const button = event.target.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subscribing...';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        showNotification(`✓ Successfully subscribed with ${email}!`, 'success');
        document.getElementById('newsletterForm').reset();
        button.innerHTML = '<i class="fas fa-check me-2"></i>Subscribed!';
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 2000);
    }, 1500);
}

// Top Category Filter Buttons
document.querySelectorAll('.category-filter').forEach(btn => {
    btn.addEventListener('click', function() {
        // Update active state
        document.querySelectorAll('.category-filter').forEach(b => {
            b.classList.remove('active');
            b.classList.replace('btn-primary', 'btn-outline-primary');
        });
        this.classList.add('active');
        this.classList.replace('btn-outline-primary', 'btn-primary');
        
        const category = this.dataset.category;
        filterByCategory(category);
        
        showNotification(`Filtering by: ${this.textContent}`, 'info');
    });
});

// Sidebar Category Buttons
document.querySelectorAll('.category-sidebar-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const category = this.dataset.category;
        
        // Update top filter button
        document.querySelectorAll('.category-filter').forEach(b => {
            b.classList.remove('active');
            b.classList.replace('btn-primary', 'btn-outline-primary');
            
            if (b.dataset.category === category) {
                b.classList.add('active');
                b.classList.replace('btn-outline-primary', 'btn-primary');
            }
        });
        
        filterByCategory(category);
        
        // Scroll to top smoothly
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        showNotification(`Showing ${this.textContent.split(' ')[0]} articles`, 'info');
    });
});

// Filter Function
function filterByCategory(category) {
    // In a real application, this would filter the blog posts
    // For now, we'll just show a console message
    console.log('Filtering by category:', category);
    
    // You can add AJAX call here to load filtered posts
    // Example:
    // fetch(`blogs.php?category=${category}`)
    //     .then(response => response.json())
    //     .then(data => updateBlogPosts(data));
}

// Smooth scroll animations
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.news-card, .news-card-small');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Load customer reviews
    loadCustomerReviews();
    loadMainReviews();
    
    // Auto-rotate sidebar reviews every 10 seconds
    setInterval(loadCustomerReviews, 10000);
});

// Slide animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
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
`;
document.head.appendChild(style);

// Customer Reviews Database
const customerReviews = [
    {name: "Sarah Johnson", location: "New York, USA", rating: 5, review: "Absolutely love this blog! The content is always informative and well-researched. I've learned so much from reading these articles.", avatar: "S", color: "#667eea"},
    {name: "Michael Chen", location: "San Francisco, USA", rating: 5, review: "The best blog I've found for shopping tips and product reviews. Very helpful and trustworthy recommendations!", avatar: "M", color: "#f093fb"},
    {name: "Emily Rodriguez", location: "London, UK", rating: 5, review: "I read every single post! The writing style is engaging and the information is always up-to-date. Highly recommended!", avatar: "E", color: "#48bb78"},
    {name: "David Park", location: "Seoul, Korea", rating: 4, review: "Great content that actually helps me make better purchasing decisions. The tips are practical and easy to follow.", avatar: "D", color: "#fbbf24"},
    {name: "Jessica Williams", location: "Toronto, Canada", rating: 5, review: "This blog has become my go-to resource for product research. The detailed guides are incredibly helpful!", avatar: "J", color: "#764ba2"},
    {name: "Ahmed Hassan", location: "Dubai, UAE", rating: 5, review: "Fantastic blog with honest reviews and helpful advice. I've saved so much money by following these tips!", avatar: "A", color: "#f5576c"},
    {name: "Sophie Martin", location: "Paris, France", rating: 5, review: "The quality of content here is outstanding. Every article is well-written and provides real value to readers.", avatar: "S", color: "#17a2b8"},
    {name: "Carlos Silva", location: "São Paulo, Brazil", rating: 4, review: "I appreciate the honest and unbiased product reviews. It's refreshing to find genuine content online.", avatar: "C", color: "#dc2626"},
    {name: "Lisa Anderson", location: "Sydney, Australia", rating: 5, review: "Amazing blog! The shopping guides have helped me find the best deals and avoid scams. Thank you!", avatar: "L", color: "#9C27B0"},
    {name: "Kevin O'Brien", location: "Dublin, Ireland", rating: 5, review: "Best blog for consumer advice! The tips are practical, the reviews are thorough, and the content is always fresh.", avatar: "K", color: "#2196F3"},
    {name: "Priya Sharma", location: "Mumbai, India", rating: 5, review: "I've recommended this blog to all my friends. The product comparisons are especially helpful for making informed choices.", avatar: "P", color: "#FF9800"},
    {name: "Thomas Mueller", location: "Berlin, Germany", rating: 4, review: "Very informative and well-organized content. I always check this blog before making any major purchase.", avatar: "T", color: "#4CAF50"},
    {name: "Maria Garcia", location: "Madrid, Spain", rating: 5, review: "Love the detailed analysis and honest opinions. This blog has never steered me wrong!", avatar: "M", color: "#E91E63"},
    {name: "James Taylor", location: "Los Angeles, USA", rating: 5, review: "Excellent resource for product information. The guides are comprehensive and easy to understand.", avatar: "J", color: "#00BCD4"},
    {name: "Yuki Tanaka", location: "Tokyo, Japan", rating: 5, review: "The blog's content quality is consistently high. I've learned valuable shopping strategies from reading here.", avatar: "Y", color: "#673AB7"}
];

// Load Customer Reviews in Sidebar
function loadCustomerReviews() {
    const container = document.getElementById('customerReviews');
    if (!container) return;
    
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
            <div class="mb-2">${generateStars(review.rating)}</div>
            <p class="small text-muted mb-0">"${review.review.substring(0, 100)}..."</p>
        </div>
    `).join('');
    
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
                <div class="mb-3">${generateStars(review.rating, 'text-warning')}</div>
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
        stars += i <= rating ? `<i class="fas fa-star ${colorClass}"></i>` : `<i class="far fa-star ${colorClass}"></i>`;
    }
    return stars;
}
</script>

<?php require_once 'includes/footer.php'; ?>