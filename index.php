<?php
$pageTitle = "Home";
require_once 'includes/header.php';

// Fetch featured products
$db = Database::getInstance();
$featuredProducts = $db->query("SELECT * FROM products WHERE status = 'active' ORDER BY sold DESC LIMIT 12")->fetch_all(MYSQLI_ASSOC);

// Fetch new arrivals
$newArrivals = $db->query("SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 12")->fetch_all(MYSQLI_ASSOC);

// Fetch categories
$categories = $db->query("SELECT * FROM categories WHERE status = 'active' LIMIT 6")->fetch_all(MYSQLI_ASSOC);

// Fetch recent blogs
$recentBlogs = $db->query("SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);

// Get total stats
$totalProducts = $db->query("SELECT COUNT(*) as count FROM products WHERE status = 'active'")->fetch_assoc()['count'];
$totalOrders = $db->query("SELECT COUNT(*) as count FROM orders WHERE payment_status = 'completed'")->fetch_assoc()['count'];
$totalCustomers = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
// Load dynamic per-user stats (consisten per IP per day)
require_once 'includes/dynamic-stats.php';
$stats = generateRandomStats();
?>

<!-- Top Announcement Bar -->
<section class="announcement-bar text-white py-2 position-relative overflow-hidden" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 50%, #ff4757 100%);">
    <div class="announcement-bg position-absolute w-100 h-100" style="top: 0; left: 0; opacity: 0.1;">
        <div class="moving-gradient"></div>
    </div>
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                    <i class="fas fa-bullhorn announcement-icon"></i>
                    <div class="announcement-slider">
                        <div class="announcement-slide active">
                            <span class="fw-bold">🎉 MEGA SALE: </span>
                            <span>Up to 70% OFF on Selected Items! Free Shipping on Orders Over $50</span>
                        </div>
                        <div class="announcement-slide">
                            <span class="fw-bold">⚡ FLASH DEAL: </span>
                            <span>Limited Time Only - Save Big Today! Shop Now Before It's Gone!</span>
                        </div>
                        <div class="announcement-slide">
                            <span class="fw-bold">🚚 FREE SHIPPING: </span>
                            <span>On All Orders Over $50 - Worldwide Delivery Available!</span>
                        </div>
                        <div class="announcement-slide">
                            <span class="fw-bold">🎁 NEW ARRIVALS: </span>
                            <span>Fresh Products Just Landed - Check Out What's New!</span>
                        </div>
                        <div class="announcement-slide">
                            <span class="fw-bold">💳 SAFE CHECKOUT: </span>
                            <span>100% Secure Payment - Shop with Confidence!</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center text-md-end mt-2 mt-md-0">
                <a href="<?php echo SITE_URL; ?>/products.php?sale=mega" class="btn btn-warning btn-sm fw-bold pulse-button">
                    Shop Sale <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Utility Bar -->
<section class="utility-bar bg-dark text-white py-2 border-bottom border-secondary">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-4 small">
                    <a href="tel:+18559997840" class="d-flex align-items-center gap-2 text-white text-decoration-none">
                        <i class="fas fa-phone-alt"></i>
                        <span>(+1) 855-999-7840</span>
                    </a>
                    <a href="mailto:support@us.usashopper.site" class="d-flex align-items-center gap-2 text-white text-decoration-none">
                        <i class="fas fa-envelope"></i>
                        <span>support@us.usashopper.site</span>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-center justify-content-md-end gap-3">
                    <!-- Language Selector -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-dark dropdown-toggle border-0 text-white" type="button" id="languageDropdown" data-mdb-toggle="dropdown">
                            <i class="fas fa-globe me-1"></i> English
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="#"><img src="https://flagcdn.com/w20/us.png" class="me-2" width="20"> English</a></li>
                            <li><a class="dropdown-item" href="#"><img src="https://flagcdn.com/w20/es.png" class="me-2" width="20"> Español</a></li>
                            <li><a class="dropdown-item" href="#"><img src="https://flagcdn.com/w20/fr.png" class="me-2" width="20"> Français</a></li>
                            <li><a class="dropdown-item" href="#"><img src="https://flagcdn.com/w20/de.png" class="me-2" width="20"> Deutsch</a></li>
                        </ul>
                    </div>
                    
                    <!-- Currency Selector -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-dark dropdown-toggle border-0 text-white" type="button" id="currencyDropdown" data-mdb-toggle="dropdown">
                            <i class="fas fa-dollar-sign me-1"></i> USD
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="#">$ USD</a></li>
                            <li><a class="dropdown-item" href="#">€ EUR</a></li>
                            <li><a class="dropdown-item" href="#">£ GBP</a></li>
                            <li><a class="dropdown-item" href="#">¥ JPY</a></li>
                            <li><a class="dropdown-item" href="#">₹ INR</a></li>
                        </ul>
                    </div>
                    
                    <!-- Social Links -->
                    <div class="d-flex gap-2 ms-2">
                        <a href="https://www.facebook.com/usshopper1" class="text-white" title="Facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.facebook.com/usshopper1" class="text-white" title="Twitter" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.facebook.com/usshopper1" class="text-white" title="Instagram" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.facebook.com/usshopper1" class="text-white" title="LinkedIn" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Top Announcement Bar */
    .announcement-bar {
        font-size: 14px;
        position: relative;
        overflow: hidden;
    }

    .moving-gradient {
        position: absolute;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: moveGradient 3s linear infinite;
    }

    @keyframes moveGradient {
        0% {
            transform: translateX(-50%) translateY(-50%);
        }
        100% {
            transform: translateX(0%) translateY(0%);
        }
    }

    .announcement-icon {
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }

    .announcement-slider {
        position: relative;
        min-height: 30px;
        display: inline-block;
        vertical-align: middle;
    }

    .announcement-slide {
        display: none;
        animation: fadeIn 0.5s ease;
    }

    .announcement-slide.active {
        display: inline;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .pulse-button {
        animation: pulse-btn 2s infinite;
    }

    @keyframes pulse-btn {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
        }
    }

    .announcement-bar a:hover {
        transform: scale(1.05);
        transition: all 0.3s ease;
    }

    @media (max-width: 768px) {
        .announcement-slide {
            font-size: 12px;
        }
        
        .announcement-slider {
            min-height: 35px;
        }
        
        .announcement-bar {
            font-size: 12px;
        }
    }

    /* Utility Bar */
    .utility-bar {
        font-size: 13px;
    }

    .utility-bar a {
        transition: all 0.3s ease;
    }

    .utility-bar a:hover {
        color: #ffc107 !important;
    }

    .utility-bar a:hover i {
        transform: scale(1.2);
    }

    .utility-bar a:hover span {
        text-decoration: underline;
    }

    .utility-bar .dropdown-toggle {
        font-size: 13px;
        padding: 5px 10px;
    }

    .utility-bar .dropdown-toggle:hover {
        background: #495057 !important;
    }

    .utility-bar .dropdown-item {
        font-size: 13px;
        padding: 8px 15px;
    }

    .utility-bar .dropdown-item:hover {
        background: #495057;
    }
</style>

<!-- Hero Multi-Banner Carousel -->
<section class="hero-banner-section position-relative overflow-hidden">
    <div class="swiper heroSwiper">
        <div class="swiper-wrapper">
            
            <!-- Hero Banner 1 - Flash Sale -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 90vh; display: flex; align-items: center; padding: 120px 0 80px 0;">
                    <div class="position-absolute w-100 h-100" style="top: 0; left: 0; opacity: 0.15;">
                        <div class="floating-shapes">
                            <div class="shape shape-1"></div>
                            <div class="shape shape-2"></div>
                        </div>
                    </div>
                    <div class="container position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-white mb-5 mb-lg-0">
                                <span class="badge bg-danger px-4 py-2 pulse-animation mb-3" style="font-size: 1rem;">
                                    <i class="fas fa-bolt me-2"></i>FLASH SALE - Limited Time!
                                </span>
                                
                                <!-- Countdown Timer -->
                                <div class="countdown-timer bg-white bg-opacity-20 backdrop-blur rounded-4 p-4 mb-4 d-inline-block">
                                    <div class="text-center mb-2">
                                        <small class="text-warning fw-bold d-block mb-1" style="font-size: 0.85rem;">
                                            <i class="fas fa-clock me-1"></i>SALE ENDS IN
                                        </small>
                                        <small class="text-white opacity-75" style="font-size: 0.75rem;">
                                            🇺🇸 USA Eastern Time (ET)
                                        </small>
                                    </div>
                                    <div class="d-flex gap-3 justify-content-center align-items-center flex-wrap">
                                        <div class="text-center">
                                            <div class="countdown-box bg-white text-dark rounded-3 px-3 py-2 mb-1" style="min-width: 70px;">
                                                <h3 class="fw-bold mb-0" id="days">00</h3>
                                            </div>
                                            <small class="text-white fw-bold">Days</small>
                                        </div>
                                        <span class="text-white fw-bold fs-3">:</span>
                                        <div class="text-center">
                                            <div class="countdown-box bg-white text-dark rounded-3 px-3 py-2 mb-1" style="min-width: 70px;">
                                                <h3 class="fw-bold mb-0" id="hours">00</h3>
                                            </div>
                                            <small class="text-white fw-bold">Hours</small>
                                        </div>
                                        <span class="text-white fw-bold fs-3">:</span>
                                        <div class="text-center">
                                            <div class="countdown-box bg-white text-dark rounded-3 px-3 py-2 mb-1" style="min-width: 70px;">
                                                <h3 class="fw-bold mb-0" id="minutes">00</h3>
                                            </div>
                                            <small class="text-white fw-bold">Minutes</small>
                                        </div>
                                        <span class="text-white fw-bold fs-3">:</span>
                                        <div class="text-center">
                                            <div class="countdown-box bg-white text-dark rounded-3 px-3 py-2 mb-1" style="min-width: 70px;">
                                                <h3 class="fw-bold mb-0" id="seconds">00</h3>
                                            </div>
                                            <small class="text-white fw-bold">Seconds</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <h1 class="display-2 fw-bold mb-4" style="line-height: 1.1;">
                                    Shop Smart,<br>
                                    <span class="text-warning">Save Big!</span>
                                </h1>
                                <p class="lead mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                                    Discover exclusive deals on premium products. <strong>Shipping worldwide</strong> with customs support.
                                </p>
                                <div class="bg-white bg-opacity-10 rounded-3 p-3 mb-4 d-inline-block backdrop-blur">
                                    <small class="d-block mb-1 opacity-75">💰 We accept: USD, GBP, EUR, CAD, AUD</small>
                                </div>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-warning btn-lg px-5 py-3 shadow-lg fw-bold hover-lift">
                                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/signup.php" class="btn btn-outline-light btn-lg px-5 py-3 hover-lift">
                                        <i class="fas fa-gift me-2"></i>Get 20% Off
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 text-center position-relative">
                                <div class="hero-image-wrapper position-relative">
                                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=700&h=500&fit=crop" 
                                         alt="Happy Shopping Customer" class="img-fluid rounded-4 shadow-lg hover-zoom" style="max-height: 500px;">
                                    
                                    <!-- Discount Badge -->
                                    <div class="floating-badge position-absolute bg-warning text-dark rounded-circle p-3 shadow-lg pulse-badge" style="top: 20px; right: 20px; width: 90px; height: 90px; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10;">
                                        <strong style="font-size: 1.5rem; line-height: 1;">50%</strong>
                                        <small style="font-size: 0.8rem; font-weight: bold;">OFF</small>
                                    </div>
                                    
                                    <!-- Free Shipping Badge -->
                                    <div class="floating-badge position-absolute bg-success text-white rounded-pill px-4 py-2 shadow-lg" style="bottom: 20px; left: 20px; z-index: 10; animation: slideInLeft 1s ease;">
                                        <i class="fas fa-shipping-fast me-2"></i>
                                        <strong>FREE Shipping</strong>
                                    </div>
                                    
                                    <!-- Verified Badge -->
                                    <div class="floating-badge position-absolute bg-primary text-white rounded-pill px-3 py-2 shadow-lg" style="top: 100px; left: -10px; z-index: 10; font-size: 0.85rem; animation: bounceIn 1.5s ease;">
                                        <i class="fas fa-shield-alt me-2"></i>Verified Products
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Banner 2 - New Arrivals -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%); min-height: 90vh; display: flex; align-items: center; padding: 120px 0 80px 0;">
                    <div class="container position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-white mb-5 mb-lg-0">
                                <span class="badge bg-white text-danger px-4 py-2 mb-3" style="font-size: 1rem;">
                                    <i class="fas fa-star me-2"></i>NEW ARRIVALS
                                </span>
                                <h1 class="display-2 fw-bold mb-4" style="line-height: 1.1;">
                                    Latest Products<br>
                                    <span class="text-white">Just Arrived!</span>
                                </h1>
                                <p class="lead mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                                    Check out our newest collection. Fresh styles, trending products, and exclusive items.
                                </p>
                                <div class="d-flex gap-4 mb-4">
                                    <div>
                                        <h3 class="fw-bold mb-0 counter" data-target="<?php echo $totalProducts; ?>">0</h3>
                                        <small><i class="fas fa-box me-1"></i>Products</small>
                                    </div>
                                    <div>
                                        <h3 class="fw-bold mb-0">4.9★</h3>
                                        <small><i class="fas fa-star me-1"></i>Rating</small>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-light btn-lg px-5 py-3 shadow-lg fw-bold hover-lift">
                                        <i class="fas fa-eye me-2"></i>Explore Now
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 text-center position-relative">
                                <div class="hero-image-wrapper position-relative">
                                    <!-- Online Shopping Image -->
                                    <img src="https://images.unsplash.com/photo-1516321497487-e288fb19713f?w=700&h=500&fit=crop" 
                                         alt="Online Shopping" class="img-fluid rounded-4 shadow-lg hover-zoom" style="max-height: 500px;">
                                    
                                    <!-- New Badge -->
                                    <div class="floating-badge position-absolute bg-danger text-white rounded-pill px-4 py-3 shadow-lg" style="top: 30px; right: 30px; z-index: 10; animation: pulse 2s infinite;">
                                        <i class="fas fa-star me-2"></i>
                                        <strong style="font-size: 1.1rem;">NEW</strong>
                                    </div>
                                    
                                    <!-- Trending Badge -->
                                    <div class="floating-badge position-absolute bg-warning text-dark rounded-pill px-4 py-2 shadow-lg" style="bottom: 80px; right: -10px; z-index: 10; animation: fadeInRight 1.2s ease;">
                                        <i class="fas fa-fire me-2"></i>Trending Now
                                    </div>
                                    
                                    <!-- Stock Badge -->
                                    <div class="floating-badge position-absolute bg-white text-dark rounded-3 px-3 py-2 shadow-lg" style="bottom: 20px; left: 20px; z-index: 10;">
                                        <small class="fw-bold"><i class="fas fa-box-open me-2 text-success"></i>In Stock</small>
                                    </div>
                                    
                                    <!-- Limited Stock Badge -->
                                    <div class="floating-badge position-absolute bg-danger text-white rounded-3 px-3 py-2 shadow-lg pulse-badge" style="top: 110px; left: 10px; z-index: 10; font-size: 0.85rem;">
                                        <i class="fas fa-clock me-2"></i>Limited Stock
                                    </div>
                                    
                                    <!-- Top Rated Badge -->
                                    <div class="floating-badge position-absolute bg-success text-white rounded-circle p-2 shadow-lg" style="top: 180px; right: 15px; width: 70px; height: 70px; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10; animation: rotateIn 1.5s ease;">
                                        <i class="fas fa-thumbs-up fa-lg mb-1"></i>
                                        <small style="font-size: 0.7rem; font-weight: bold;">Top</small>
                                    </div>
                                    
                                    <!-- Free Returns Badge -->
                                    <div class="floating-badge position-absolute bg-primary text-white rounded-pill px-3 py-2 shadow-lg" style="bottom: 130px; left: 5px; z-index: 10; font-size: 0.8rem; animation: slideInLeft 1.3s ease;">
                                        <i class="fas fa-undo-alt me-2"></i>Free Returns
                                    </div>
                                    
                                    <!-- Express Delivery Badge -->
                                    <div class="floating-badge position-absolute bg-info text-white rounded-pill px-3 py-2 shadow-lg" style="top: 250px; right: -5px; z-index: 10; font-size: 0.8rem; animation: bounceIn 1.8s ease;">
                                        <i class="fas fa-shipping-fast me-2"></i>Express
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Banner 3 - Free Shipping -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); min-height: 90vh; display: flex; align-items: center; padding: 120px 0 80px 0;">
                    <div class="container position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-white mb-5 mb-lg-0">
                                <span class="badge bg-white text-success px-4 py-2 mb-3" style="font-size: 1rem;">
                                    <i class="fas fa-truck me-2"></i>FREE WORLDWIDE SHIPPING
                                </span>
                                <h1 class="display-2 fw-bold mb-4" style="line-height: 1.1;">
                                    Ship to<br>
                                    <span class="text-white"><?php echo $stats['countries']; ?>+ Countries!</span>
                                </h1>
                                <p class="lead mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                                    Free shipping on orders over $50 to USA. Fast delivery with real-time tracking worldwide.
                                </p>
                                <div class="bg-white bg-opacity-20 rounded-3 p-3 mb-4 backdrop-blur">
                                    <div class="d-flex gap-3 flex-wrap">
                                        <span>🇺🇸 USA</span>
                                        <span>🇬🇧 UK</span>
                                        <span>🇨🇦 Canada</span>
                                        <span>🇦🇺 Australia</span>
                                        <span>+ 71 More</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-light btn-lg px-5 py-3 shadow-lg fw-bold hover-lift">
                                        <i class="fas fa-gift me-2"></i>Shop Now
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 text-center position-relative">
                                <div class="hero-image-wrapper position-relative">
                                    <!-- Person Receiving Package -->
                                    <img src="https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=700&h=500&fit=crop" 
                                         alt="Receiving Package Delivery" class="img-fluid rounded-4 shadow-lg hover-zoom" style="max-height: 500px;">
                                    
                                    <!-- Free Shipping Circle Badge -->
                                    <div class="floating-badge position-absolute bg-white text-success rounded-circle p-4 shadow-lg" style="top: 20px; right: 20px; width: 110px; height: 110px; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10; animation: rotateIn 1s ease;">
                                        <i class="fas fa-truck fa-2x mb-2"></i>
                                        <strong style="font-size: 0.85rem; text-align: center; line-height: 1.2;">FREE<br>SHIP</strong>
                                    </div>
                                    
                                    <!-- Countries Badge -->
                                    <div class="floating-badge position-absolute bg-primary text-white rounded-pill px-4 py-3 shadow-lg" style="bottom: 30px; left: 20px; z-index: 10; animation: slideInUp 1.2s ease;">
                                        <strong style="font-size: 1.2rem;"><?php echo $stats['countries']; ?>+ Countries</strong>
                                    </div>
                                    
                                    <!-- Fast Delivery Badge -->
                                    <div class="floating-badge position-absolute bg-warning text-dark rounded-3 px-3 py-2 shadow-lg" style="top: 120px; left: -10px; z-index: 10;">
                                        <i class="fas fa-bolt me-2"></i><strong>Fast Delivery</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Banner 4 - Premium Quality -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 90vh; display: flex; align-items: center; padding: 120px 0 80px 0;">
                    <div class="container position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-white mb-5 mb-lg-0">
                                <span class="badge bg-white text-danger px-4 py-2 mb-3" style="font-size: 1rem;">
                                    <i class="fas fa-crown me-2"></i>PREMIUM QUALITY
                                </span>
                                <h1 class="display-2 fw-bold mb-4" style="line-height: 1.1;">
                                    100% Authentic<br>
                                    <span class="text-white">Products!</span>
                                </h1>
                                <p class="lead mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                                    Verified sellers, genuine products, and buyer protection. Shop with confidence!
                                </p>
                                <div class="d-flex gap-4 mb-4">
                                    <div>
                                        <h3 class="fw-bold mb-0">100%</h3>
                                        <small><i class="fas fa-check-circle me-1"></i>Authentic</small>
                                    </div>
                                    <div>
                                        <h3 class="fw-bold mb-0">99%</h3>
                                        <small><i class="fas fa-smile me-1"></i>Satisfied</small>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-light btn-lg px-5 py-3 shadow-lg fw-bold hover-lift">
                                        <i class="fas fa-gem me-2"></i>View Premium
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 text-center position-relative">
                                <div class="hero-image-wrapper position-relative">
                                    <!-- Shopping Cart with Products -->
                                    <img src="https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=700&h=500&fit=crop" 
                                         alt="Shopping Cart" class="img-fluid rounded-4 shadow-lg hover-zoom" style="max-height: 500px;">
                                    
                                    <!-- Premium Quality Badge -->
                                    <div class="floating-badge position-absolute bg-dark text-warning rounded-circle p-3 shadow-lg" style="top: 20px; right: 20px; width: 95px; height: 95px; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10; animation: pulse 2s infinite;">
                                        <i class="fas fa-crown fa-2x mb-1"></i>
                                        <small style="font-size: 0.75rem; font-weight: bold;">PREMIUM</small>
                                    </div>
                                    
                                    <!-- Authentic Badge -->
                                    <div class="floating-badge position-absolute bg-success text-white rounded-pill px-4 py-2 shadow-lg" style="bottom: 30px; right: 20px; z-index: 10; animation: bounceIn 1.5s ease;">
                                        <i class="fas fa-check-circle me-2"></i><strong>100% Authentic</strong>
                                    </div>
                                    
                                    <!-- Quality Verified Badge -->
                                    <div class="floating-badge position-absolute bg-info text-white rounded-3 px-3 py-2 shadow-lg" style="top: 100px; left: -5px; z-index: 10;">
                                        <i class="fas fa-certificate me-2"></i><strong>Quality Verified</strong>
                                    </div>
                                    
                                    <!-- Money Back Badge -->
                                    <div class="floating-badge position-absolute bg-white text-dark rounded-pill px-3 py-2 shadow-lg" style="bottom: 100px; left: 20px; z-index: 10; font-size: 0.85rem;">
                                        <i class="fas fa-undo me-2 text-success"></i>30-Day Returns
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Banner 5 - Electronics -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%); min-height: 90vh; display: flex; align-items: center; padding: 120px 0 80px 0;">
                    <div class="container position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-white mb-5 mb-lg-0">
                                <span class="badge bg-white text-primary px-4 py-2 mb-3" style="font-size: 1rem;">
                                    <i class="fas fa-laptop me-2"></i>ELECTRONICS SALE
                                </span>
                                <h1 class="display-2 fw-bold mb-4" style="line-height: 1.1;">
                                    Tech Deals<br>
                                    <span class="text-warning">Up to 60% OFF!</span>
                                </h1>
                                <p class="lead mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                                    Latest gadgets, laptops, phones & accessories. Free tech support included!
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="<?php echo SITE_URL; ?>/products.php?category=electronics" class="btn btn-warning btn-lg px-5 py-3 shadow-lg fw-bold hover-lift">
                                        <i class="fas fa-mobile-alt me-2"></i>Shop Tech
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 text-center position-relative">
                                <div class="hero-image-wrapper position-relative">
                                    <img src="https://images.unsplash.com/photo-1498049794561-7780e7231661?w=700&h=500&fit=crop" 
                                         alt="Electronics Shopping" class="img-fluid rounded-4 shadow-lg hover-zoom" style="max-height: 500px;">
                                    
                                    <div class="floating-badge position-absolute bg-danger text-white rounded-circle p-3 shadow-lg pulse-badge" style="top: 20px; right: 20px; width: 90px; height: 90px; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10;">
                                        <strong style="font-size: 1.5rem; line-height: 1;">60%</strong>
                                        <small style="font-size: 0.8rem; font-weight: bold;">OFF</small>
                                    </div>
                                    
                                    <div class="floating-badge position-absolute bg-warning text-dark rounded-pill px-4 py-2 shadow-lg" style="bottom: 30px; left: 20px; z-index: 10;">
                                        <i class="fas fa-tools me-2"></i><strong>Free Tech Support</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Banner 6 - Fashion -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #ff6a88 0%, #ff99ac 100%); min-height: 90vh; display: flex; align-items: center; padding: 120px 0 80px 0;">
                    <div class="container position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-white mb-5 mb-lg-0">
                                <span class="badge bg-white text-danger px-4 py-2 mb-3" style="font-size: 1rem;">
                                    <i class="fas fa-tshirt me-2"></i>FASHION WEEK
                                </span>
                                <h1 class="display-2 fw-bold mb-4" style="line-height: 1.1;">
                                    Style Your Way<br>
                                    <span class="text-white">Trendy Fashion!</span>
                                </h1>
                                <p class="lead mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                                    Discover the latest fashion trends. Clothing, shoes & accessories for everyone.
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="<?php echo SITE_URL; ?>/products.php?category=fashion" class="btn btn-light btn-lg px-5 py-3 shadow-lg fw-bold hover-lift">
                                        <i class="fas fa-shopping-bag me-2"></i>Shop Fashion
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 text-center position-relative">
                                <div class="hero-image-wrapper position-relative">
                                    <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=700&h=500&fit=crop" 
                                         alt="Fashion Shopping" class="img-fluid rounded-4 shadow-lg hover-zoom" style="max-height: 500px;">
                                    
                                    <div class="floating-badge position-absolute bg-dark text-white rounded-pill px-4 py-3 shadow-lg" style="top: 30px; right: 20px; z-index: 10; animation: pulse 2s infinite;">
                                        <i class="fas fa-heart me-2"></i><strong style="font-size: 1.1rem;">TRENDING</strong>
                                    </div>
                                    
                                    <div class="floating-badge position-absolute bg-success text-white rounded-pill px-4 py-2 shadow-lg" style="bottom: 30px; left: 20px; z-index: 10;">
                                        <i class="fas fa-tag me-2"></i>New Arrivals
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Banner 7 - Home & Garden -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); min-height: 90vh; display: flex; align-items: center; padding: 120px 0 80px 0;">
                    <div class="container position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-white mb-5 mb-lg-0">
                                <span class="badge bg-white text-success px-4 py-2 mb-3" style="font-size: 1rem;">
                                    <i class="fas fa-home me-2"></i>HOME & GARDEN
                                </span>
                                <h1 class="display-2 fw-bold mb-4" style="line-height: 1.1;">
                                    Your Dream Home<br>
                                    <span class="text-white">Starts Here!</span>
                                </h1>
                                <p class="lead mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                                    Furniture, decor, kitchen essentials & more. Create your perfect space.
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="<?php echo SITE_URL; ?>/products.php?category=home" class="btn btn-light btn-lg px-5 py-3 shadow-lg fw-bold hover-lift">
                                        <i class="fas fa-couch me-2"></i>Shop Home
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 text-center position-relative">
                                <div class="hero-image-wrapper position-relative">
                                    <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=700&h=500&fit=crop" 
                                         alt="Home Decor" class="img-fluid rounded-4 shadow-lg hover-zoom" style="max-height: 500px;">
                                    
                                    <div class="floating-badge position-absolute bg-warning text-dark rounded-circle p-3 shadow-lg" style="top: 20px; right: 20px; width: 90px; height: 90px; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10;">
                                        <i class="fas fa-home fa-2x mb-1"></i>
                                        <small style="font-size: 0.75rem; font-weight: bold;">BEST</small>
                                    </div>
                                    
                                    <div class="floating-badge position-absolute bg-primary text-white rounded-pill px-4 py-2 shadow-lg" style="bottom: 30px; right: 20px; z-index: 10;">
                                        <i class="fas fa-percentage me-2"></i>Great Deals
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Banner 8 - Beauty & Health -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%); min-height: 90vh; display: flex; align-items: center; padding: 120px 0 80px 0;">
                    <div class="container position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-white mb-5 mb-lg-0">
                                <span class="badge bg-white text-danger px-4 py-2 mb-3" style="font-size: 1rem;">
                                    <i class="fas fa-spa me-2"></i>BEAUTY & WELLNESS
                                </span>
                                <h1 class="display-2 fw-bold mb-4" style="line-height: 1.1;">
                                    Glow Up<br>
                                    <span class="text-warning">Radiate Beauty!</span>
                                </h1>
                                <p class="lead mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                                    Premium skincare, makeup, fragrances & wellness products. Look & feel amazing!
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="<?php echo SITE_URL; ?>/products.php?category=beauty" class="btn btn-warning btn-lg px-5 py-3 shadow-lg fw-bold hover-lift">
                                        <i class="fas fa-heart me-2"></i>Shop Beauty
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 text-center position-relative">
                                <div class="hero-image-wrapper position-relative">
                                    <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=700&h=500&fit=crop" 
                                         alt="Beauty Products" class="img-fluid rounded-4 shadow-lg hover-zoom" style="max-height: 500px;">
                                    
                                    <div class="floating-badge position-absolute bg-white text-danger rounded-pill px-4 py-3 shadow-lg pulse-badge" style="top: 30px; right: 20px; z-index: 10;">
                                        <i class="fas fa-star me-2"></i><strong>Top Rated</strong>
                                    </div>
                                    
                                    <div class="floating-badge position-absolute bg-success text-white rounded-pill px-3 py-2 shadow-lg" style="bottom: 30px; left: 20px; z-index: 10;">
                                        <i class="fas fa-leaf me-2"></i>Natural & Organic
                                    </div>
                                    
                                    <div class="floating-badge position-absolute bg-dark text-warning rounded-3 px-3 py-2 shadow-lg" style="top: 120px; left: -5px; z-index: 10;">
                                        <i class="fas fa-crown me-2"></i>Premium Brands
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Banner 9 - Sports & Fitness -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); min-height: 90vh; display: flex; align-items: center; padding: 120px 0 80px 0;">
                    <div class="container position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-white mb-5 mb-lg-0">
                                <span class="badge bg-dark text-warning px-4 py-2 mb-3" style="font-size: 1rem;">
                                    <i class="fas fa-running me-2"></i>SPORTS & FITNESS
                                </span>
                                <h1 class="display-2 fw-bold mb-4" style="line-height: 1.1;">
                                    Get Active<br>
                                    <span class="text-dark">Stay Fit!</span>
                                </h1>
                                <p class="lead mb-4 text-dark" style="font-size: 1.3rem; opacity: 0.95;">
                                    Workout gear, sports equipment, fitness trackers & more. Achieve your goals!
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="<?php echo SITE_URL; ?>/products.php?category=sports" class="btn btn-dark btn-lg px-5 py-3 shadow-lg fw-bold hover-lift">
                                        <i class="fas fa-dumbbell me-2"></i>Shop Sports
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 text-center position-relative">
                                <div class="hero-image-wrapper position-relative">
                                    <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=700&h=500&fit=crop" 
                                         alt="Fitness & Sports" class="img-fluid rounded-4 shadow-lg hover-zoom" style="max-height: 500px;">
                                    
                                    <div class="floating-badge position-absolute bg-danger text-white rounded-circle p-3 shadow-lg" style="top: 20px; right: 20px; width: 95px; height: 95px; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10; animation: pulse 2s infinite;">
                                        <i class="fas fa-fire fa-2x mb-1"></i>
                                        <small style="font-size: 0.75rem; font-weight: bold;">HOT</small>
                                    </div>
                                    
                                    <div class="floating-badge position-absolute bg-dark text-white rounded-pill px-4 py-2 shadow-lg" style="bottom: 30px; left: 20px; z-index: 10;">
                                        <i class="fas fa-medal me-2"></i>Pro Quality
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Banner 10 - Kids & Toys -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(135deg, #f77062 0%, #fe5196 100%); min-height: 90vh; display: flex; align-items: center; padding: 120px 0 80px 0;">
                    <div class="container position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-6 text-white mb-5 mb-lg-0">
                                <span class="badge bg-white text-danger px-4 py-2 mb-3" style="font-size: 1rem;">
                                    <i class="fas fa-baby me-2"></i>KIDS & TOYS
                                </span>
                                <h1 class="display-2 fw-bold mb-4" style="line-height: 1.1;">
                                    Fun & Learning<br>
                                    <span class="text-white">For Kids!</span>
                                </h1>
                                <p class="lead mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                                    Toys, games, educational items & kids fashion. Safe & certified products!
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="<?php echo SITE_URL; ?>/products.php?category=kids" class="btn btn-warning btn-lg px-5 py-3 shadow-lg fw-bold hover-lift">
                                        <i class="fas fa-smile me-2"></i>Shop Kids
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 text-center position-relative">
                                <div class="hero-image-wrapper position-relative">
                                    <img src="https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=700&h=500&fit=crop" 
                                         alt="Kids Shopping" class="img-fluid rounded-4 shadow-lg hover-zoom" style="max-height: 500px;">
                                    
                                    <div class="floating-badge position-absolute bg-warning text-dark rounded-pill px-4 py-3 shadow-lg pulse-badge" style="top: 30px; right: 20px; z-index: 10;">
                                        <i class="fas fa-gamepad me-2"></i><strong>Best Sellers</strong>
                                    </div>
                                    
                                    <div class="floating-badge position-absolute bg-success text-white rounded-pill px-4 py-2 shadow-lg" style="bottom: 30px; left: 20px; z-index: 10;">
                                        <i class="fas fa-shield-alt me-2"></i>Safe & Certified
                                    </div>
                                    
                                    <div class="floating-badge position-absolute bg-primary text-white rounded-3 px-3 py-2 shadow-lg" style="top: 100px; left: -5px; z-index: 10; font-size: 0.85rem;">
                                        <i class="fas fa-gift me-2"></i>Free Gift Wrap
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Navigation -->
        <div class="swiper-button-next hero-next"></div>
        <div class="swiper-button-prev hero-prev"></div>
        
        <!-- Pagination -->
        <div class="swiper-pagination hero-pagination"></div>
    </div>
</section>

<!-- Flash Sale Banner -->
<section class="py-3 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                    <i class="fas fa-bolt fa-2x"></i>
                    <div>
                        <h5 class="mb-0 fw-bold">⚡ FLASH SALE - 50% OFF</h5>
                        <small>Limited time offer on selected items!</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-center justify-content-md-end gap-3">
                    <span class="small">Ends in:</span>
                    <div class="d-flex gap-2" id="flashSaleCountdown">
                        <div class="bg-white bg-opacity-25 rounded px-3 py-2">
                            <div class="fw-bold" id="flash-hours">00</div>
                            <small style="font-size: 0.7rem;">Hours</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded px-3 py-2">
                            <div class="fw-bold" id="flash-minutes">00</div>
                            <small style="font-size: 0.7rem;">Mins</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded px-3 py-2">
                            <div class="fw-bold" id="flash-seconds">00</div>
                            <small style="font-size: 0.7rem;">Secs</small>
                        </div>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/products.php?sale=flash" class="btn btn-warning btn-sm fw-bold">
                        Shop Now <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('data:image/svg+xml,%3Csvg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M0 0h20L0 20z\"/%3E%3C/g%3E%3C/svg%3E'); opacity: 0.3;"></div>
</section>

<!-- Trust Badges Bar -->
<section id="features" class="py-3 bg-white shadow-sm">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-6 col-md-3">
                <a href="#shipping-info" class="text-decoration-none trust-badge-link">
                    <div class="d-flex align-items-center justify-content-center gap-2 p-2 rounded hover-lift-sm">
                        <i class="fas fa-globe-americas text-primary fa-2x"></i>
                        <div class="text-start">
                            <strong class="d-block text-dark" style="font-size: 0.9rem;">Worldwide Shipping</strong>
                            <small class="text-muted" style="font-size: 0.75rem;">To USA & <?php echo $stats['countries']; ?>+ countries</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?php echo SITE_URL; ?>/about.php#returns" class="text-decoration-none trust-badge-link">
                    <div class="d-flex align-items-center justify-content-center gap-2 p-2 rounded hover-lift-sm">
                        <i class="fas fa-undo text-success fa-2x"></i>
                        <div class="text-start">
                            <strong class="d-block text-dark" style="font-size: 0.9rem;">Easy Returns</strong>
                            <small class="text-muted" style="font-size: 0.75rem;">30-day money back</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?php echo SITE_URL; ?>/about.php#security" class="text-decoration-none trust-badge-link">
                    <div class="d-flex align-items-center justify-content-center gap-2 p-2 rounded hover-lift-sm">
                        <i class="fas fa-shield-alt text-warning fa-2x"></i>
                        <div class="text-start">
                            <strong class="d-block text-dark" style="font-size: 0.9rem;">Buyer Protection</strong>
                            <small class="text-muted" style="font-size: 0.75rem;">SSL & PCI compliant</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?php echo SITE_URL; ?>/support.php" class="text-decoration-none trust-badge-link">
                    <div class="d-flex align-items-center justify-content-center gap-2 p-2 rounded hover-lift-sm">
                        <i class="fas fa-headset text-info fa-2x"></i>
                        <div class="text-start">
                            <strong class="d-block text-dark" style="font-size: 0.9rem;">24/7 Support</strong>
                            <small class="text-muted" style="font-size: 0.75rem;">English support team</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Multi-Banner Carousel -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="swiper bannerSwiper">
            <div class="swiper-wrapper">
                <!-- Banner 1 - Flash Sale -->
                <div class="swiper-slide">
                    <div class="banner-card position-relative overflow-hidden rounded-4" style="background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%); min-height: 350px;">
                        <div class="row align-items-center h-100">
                            <div class="col-md-6 p-5 text-white">
                                <span class="badge bg-white text-danger px-3 py-2 mb-3">
                                    <i class="fas fa-bolt me-2"></i>LIMITED TIME OFFER
                                </span>
                                <h2 class="display-4 fw-bold mb-3">Flash Sale!</h2>
                                <h3 class="mb-3">Up to 70% OFF</h3>
                                <p class="lead mb-4">On selected items. Don't miss out on these amazing deals!</p>
                                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-light btn-lg px-5 py-3 shadow-lg hover-lift">
                                    <i class="fas fa-shopping-bag me-2"></i>Shop Now
                                </a>
                            </div>
                            <div class="col-md-6 text-center d-none d-md-block">
                                <img src="https://images.unsplash.com/photo-1607083206968-13611e3d76db?w=500&h=350&fit=crop" 
                                     alt="Happy American Shoppers" class="img-fluid" style="max-height: 300px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Banner 2 - New Arrivals -->
                <div class="swiper-slide">
                    <div class="banner-card position-relative overflow-hidden rounded-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 350px;">
                        <div class="row align-items-center h-100">
                            <div class="col-md-6 p-5 text-white">
                                <span class="badge bg-white text-primary px-3 py-2 mb-3">
                                    <i class="fas fa-star me-2"></i>NEW COLLECTION
                                </span>
                                <h2 class="display-4 fw-bold mb-3">New Arrivals</h2>
                                <h3 class="mb-3">Fresh & Trending</h3>
                                <p class="lead mb-4">Discover the latest products added to our collection</p>
                                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-warning btn-lg px-5 py-3 shadow-lg hover-lift">
                                    <i class="fas fa-eye me-2"></i>Explore Now
                                </a>
                            </div>
                            <div class="col-md-6 text-center d-none d-md-block">
                                <img src="https://images.unsplash.com/photo-1556742111-a301076d9d18?w=500&h=350&fit=crop" 
                                     alt="American Woman Shopping" class="img-fluid" style="max-height: 300px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Banner 3 - Free Shipping -->
                <div class="swiper-slide">
                    <div class="banner-card position-relative overflow-hidden rounded-4" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); min-height: 350px;">
                        <div class="row align-items-center h-100">
                            <div class="col-md-6 p-5 text-white">
                                <span class="badge bg-white text-success px-3 py-2 mb-3">
                                    <i class="fas fa-truck me-2"></i>SPECIAL OFFER
                                </span>
                                <h2 class="display-4 fw-bold mb-3">Free Shipping</h2>
                                <h3 class="mb-3">Worldwide Delivery</h3>
                                <p class="lead mb-4">Free shipping on all orders over $50 to USA & selected countries</p>
                                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-light btn-lg px-5 py-3 shadow-lg hover-lift">
                                    <i class="fas fa-gift me-2"></i>Get Offer
                                </a>
                            </div>
                            <div class="col-md-6 text-center d-none d-md-block">
                                <img src="https://images.unsplash.com/photo-1607082350899-7e105aa886ae?w=500&h=350&fit=crop" 
                                     alt="Happy Customer Receiving Package" class="img-fluid" style="max-height: 300px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Banner 4 - Premium Products -->
                <div class="swiper-slide">
                    <div class="banner-card position-relative overflow-hidden rounded-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 350px;">
                        <div class="row align-items-center h-100">
                            <div class="col-md-6 p-5 text-white">
                                <span class="badge bg-white text-danger px-3 py-2 mb-3">
                                    <i class="fas fa-crown me-2"></i>PREMIUM QUALITY
                                </span>
                                <h2 class="display-4 fw-bold mb-3">Premium Range</h2>
                                <h3 class="mb-3">Exclusive Products</h3>
                                <p class="lead mb-4">Handpicked premium products with guaranteed authenticity</p>
                                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-light btn-lg px-5 py-3 shadow-lg hover-lift">
                                    <i class="fas fa-gem me-2"></i>View Collection
                                </a>
                            </div>
                            <div class="col-md-6 text-center d-none d-md-block">
                                <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=500&h=350&fit=crop" 
                                     alt="American Shoppers" class="img-fluid" style="max-height: 300px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Banner 5 - Best Selling -->
                <div class="swiper-slide">
                    <div class="banner-card position-relative overflow-hidden rounded-4" style="background: linear-gradient(135deg, #FA8BFF 0%, #2BD2FF 50%, #2BFF88 100%); min-height: 350px;">
                        <div class="row align-items-center h-100">
                            <div class="col-md-6 p-5 text-white">
                                <span class="badge bg-white text-primary px-3 py-2 mb-3">
                                    <i class="fas fa-medal me-2"></i>BEST SELLING
                                </span>
                                <h2 class="display-4 fw-bold mb-3">Top Rated</h2>
                                <h3 class="mb-3">Customer Favorites</h3>
                                <p class="lead mb-4">Our most popular products loved by thousands of customers worldwide</p>
                                <a href="<?php echo SITE_URL; ?>/products.php?sort=popular" class="btn btn-light btn-lg px-5 py-3 shadow-lg hover-lift">
                                    <i class="fas fa-trophy me-2"></i>Shop Best Sellers
                                </a>
                            </div>
                            <div class="col-md-6 text-center d-none d-md-block">
                                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=500&h=350&fit=crop" 
                                     alt="Happy USA Customers" class="img-fluid" style="max-height: 300px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Banner 6 - Hot Selling -->
                <div class="swiper-slide">
                    <div class="banner-card position-relative overflow-hidden rounded-4" style="background: linear-gradient(135deg, #FD297B 0%, #FF655B 100%); min-height: 350px;">
                        <div class="row align-items-center h-100">
                            <div class="col-md-6 p-5 text-white">
                                <span class="badge bg-warning text-dark px-3 py-2 mb-3 pulse-animation">
                                    <i class="fas fa-fire me-2"></i>HOT SELLING 🔥
                                </span>
                                <h2 class="display-4 fw-bold mb-3">Trending Now</h2>
                                <h3 class="mb-3">Flying Off Shelves</h3>
                                <p class="lead mb-4">Don't miss out! These hot items are selling fast - grab yours today!</p>
                                <a href="<?php echo SITE_URL; ?>/products.php?sort=popular" class="btn btn-warning btn-lg px-5 py-3 shadow-lg hover-lift text-dark fw-bold">
                                    <i class="fas fa-fire me-2"></i>Shop Hot Items
                                </a>
                            </div>
                            <div class="col-md-6 text-center d-none d-md-block">
                                <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=500&h=350&fit=crop" 
                                     alt="Excited American Shopper" class="img-fluid" style="max-height: 300px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <div class="swiper-button-next banner-next"></div>
            <div class="swiper-button-prev banner-prev"></div>
            
            <!-- Pagination -->
            <div class="swiper-pagination banner-pagination"></div>
        </div>
    </div>
</section>

<!-- International Payment Methods & Certifications -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <p class="text-muted mb-3 small fw-bold">TRUSTED PAYMENT METHODS WORLDWIDE</p>
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-4 mb-4">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" style="height: 30px; opacity: 0.7;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" style="height: 25px; opacity: 0.7;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" style="height: 35px; opacity: 0.7;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/American_Express_logo_%282018%29.svg" alt="American Express" style="height: 30px; opacity: 0.7;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b0/Apple_Pay_logo.svg" alt="Apple Pay" style="height: 30px; opacity: 0.7;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/f2/Google_Pay_Logo.svg" alt="Google Pay" style="height: 25px; opacity: 0.7;">
                <div class="badge bg-success px-3 py-2">
                    <i class="fas fa-lock me-1"></i>256-bit SSL
                </div>
            </div>
        </div>
        
        <!-- International Certifications & Trust Badges -->
        <div class="row g-3 mt-3">
            <div class="col-6 col-md-2 text-center">
                <div class="bg-white rounded-3 p-3 shadow-sm">
                    <i class="fas fa-certificate text-primary fa-2x mb-2"></i>
                    <small class="d-block fw-bold">PCI DSS</small>
                    <small class="text-muted" style="font-size: 0.7rem;">Certified</small>
                </div>
            </div>
            <div class="col-6 col-md-2 text-center">
                <div class="bg-white rounded-3 p-3 shadow-sm">
                    <i class="fas fa-shield-alt text-success fa-2x mb-2"></i>
                    <small class="d-block fw-bold">SSL Secure</small>
                    <small class="text-muted" style="font-size: 0.7rem;">256-bit</small>
                </div>
            </div>
            <div class="col-6 col-md-2 text-center">
                <div class="bg-white rounded-3 p-3 shadow-sm">
                    <i class="fas fa-file-invoice text-info fa-2x mb-2"></i>
                    <small class="d-block fw-bold">Customs</small>
                    <small class="text-muted" style="font-size: 0.7rem;">Support</small>
                </div>
            </div>
            <div class="col-6 col-md-2 text-center">
                <div class="bg-white rounded-3 p-3 shadow-sm">
                    <i class="fas fa-dollar-sign text-warning fa-2x mb-2"></i>
                    <small class="d-block fw-bold">Multi-Currency</small>
                    <small class="text-muted" style="font-size: 0.7rem;">5+ Supported</small>
                </div>
            </div>
            <div class="col-6 col-md-2 text-center">
                <div class="bg-white rounded-3 p-3 shadow-sm">
                    <i class="fas fa-language text-danger fa-2x mb-2"></i>
                    <small class="d-block fw-bold">English</small>
                    <small class="text-muted" style="font-size: 0.7rem;">Support 24/7</small>
                </div>
            </div>
            <div class="col-6 col-md-2 text-center">
                <div class="bg-white rounded-3 p-3 shadow-sm">
                    <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                    <small class="d-block fw-bold">Verified</small>
                    <small class="text-muted" style="font-size: 0.7rem;">Business</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5" style="margin-top: -50px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary px-3 py-2 mb-2">CATEGORIES</span>
            <h2 class="fw-bold mb-2">Shop by Category</h2>
            <p class="text-muted">Find exactly what you're looking for</p>
        </div>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <div class="col-md-4 col-sm-6">
                    <a href="<?php echo SITE_URL; ?>/products.php?category=<?php echo $category['slug']; ?>" 
                       class="text-decoration-none">
                        <div class="card category-card h-100 text-center border-0 shadow-sm overflow-hidden" style="border-radius: 15px; transition: all 0.3s ease; background: white;">
                            <div class="category-image-wrapper" style="width: 100%; height: 200px; overflow: hidden;">
                                <?php if (isset($category['icon']) && !empty($category['icon'])): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/categories/<?php echo $category['icon']; ?>" 
                                         alt="<?php echo htmlspecialchars($category['name']); ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                        <i class="fas fa-folder-open fa-3x text-primary"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h5 class="fw-bold"><?php echo htmlspecialchars($category['name']); ?></h5>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($category['description']); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Themed Banners Carousel -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary px-3 py-2 mb-2">
                <i class="fas fa-calendar-alt me-2"></i>SPECIAL OCCASIONS & CATEGORIES
            </span>
            <h2 class="fw-bold mb-2">Shop by Season & Category</h2>
            <p class="text-muted">Exclusive collections for every occasion and lifestyle</p>
        </div>
        
        <div class="swiper themedBannersSwiper">
            <div class="swiper-wrapper">
                
                <!-- Halloween Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #ff6a00 0%, #ee0979 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1509557965875-b88c97052f0e?w=800&q=80') center/cover; opacity: 0.2;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-dark px-3 py-2 mb-3">🎃 HALLOWEEN SPECIAL</span>
                                <h2 class="display-4 fw-bold mb-3">Spooky Season Sale!</h2>
                                <p class="lead mb-4">Get your costumes, decorations & treats ready. Up to 50% OFF!</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=halloween" class="btn btn-warning btn-lg fw-bold px-5">
                                    <i class="fas fa-ghost me-2"></i>Shop Halloween
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Christmas Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #c92b2b 0%, #0f5132 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1512389142860-9c449e58a543?w=800&q=80') center/cover; opacity: 0.15;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-white text-danger px-3 py-2 mb-3">🎄 MERRY CHRISTMAS</span>
                                <h2 class="display-4 fw-bold mb-3">Holiday Magic!</h2>
                                <p class="lead mb-4">Spread joy with our festive gifts, decorations & special treats!</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=christmas" class="btn btn-light btn-lg fw-bold px-5">
                                    <i class="fas fa-gift me-2"></i>Shop Christmas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Independence Day Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #0b1f8b 0%, #dc2f3e 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1530911876082-76d38e9c7c0d?w=800&q=80') center/cover; opacity: 0.2;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-white text-primary px-3 py-2 mb-3">🇺🇸 INDEPENDENCE DAY</span>
                                <h2 class="display-4 fw-bold mb-3">Celebrate Freedom!</h2>
                                <p class="lead mb-4">Patriotic apparel, BBQ essentials & party supplies. Shop now!</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=independence-day" class="btn btn-warning btn-lg fw-bold px-5">
                                    <i class="fas fa-flag-usa me-2"></i>Shop 4th of July
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Health & Wellness Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&q=80') center/cover; opacity: 0.15;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-white text-success px-3 py-2 mb-3">💪 HEALTH & WELLNESS</span>
                                <h2 class="display-4 fw-bold mb-3">Your Best Self!</h2>
                                <p class="lead mb-4">Supplements, fitness gear & wellness products for a healthier you.</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=health-wellness" class="btn btn-dark btn-lg fw-bold px-5">
                                    <i class="fas fa-heartbeat me-2"></i>Shop Wellness
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Beauty Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&q=80') center/cover; opacity: 0.15;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-white text-danger px-3 py-2 mb-3">💄 BEAUTY & SKINCARE</span>
                                <h2 class="display-4 fw-bold mb-3">Glow Up!</h2>
                                <p class="lead mb-4">Premium skincare, makeup & fragrances for your beauty routine.</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=beauty" class="btn btn-light btn-lg fw-bold px-5">
                                    <i class="fas fa-spa me-2"></i>Shop Beauty
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Apparel Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #434343 0%, #000000 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=800&q=80') center/cover; opacity: 0.3;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-warning text-dark px-3 py-2 mb-3">👔 FASHION & APPAREL</span>
                                <h2 class="display-4 fw-bold mb-3">Style Statement!</h2>
                                <p class="lead mb-4">Trendy clothing, shoes & accessories for every occasion.</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=apparel" class="btn btn-warning btn-lg fw-bold px-5">
                                    <i class="fas fa-tshirt me-2"></i>Shop Fashion
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pets Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #f77062 0%, #fe5196 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1450778869180-41d0601e046e?w=800&q=80') center/cover; opacity: 0.2;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-white text-danger px-3 py-2 mb-3">🐾 PET SUPPLIES</span>
                                <h2 class="display-4 fw-bold mb-3">Pamper Your Pets!</h2>
                                <p class="lead mb-4">Everything your furry friends need - food, toys, accessories & more!</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=pets" class="btn btn-light btn-lg fw-bold px-5">
                                    <i class="fas fa-paw me-2"></i>Shop Pets
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Navigation -->
            <div class="swiper-button-next themed-next"></div>
            <div class="swiper-button-prev themed-prev"></div>
            
            <!-- Pagination -->
            <div class="swiper-pagination themed-pagination mt-4"></div>
        </div>
    </div>
</section>

<!-- Promotional Banner 1 - Double Banner -->
<section class="py-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6" data-aos="fade-right">
                <div class="promo-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 250px;">
                    <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=800&q=80') center/cover; opacity: 0.2;"></div>
                    <div class="position-relative h-100 d-flex flex-column justify-content-center p-5 text-white">
                        <span class="badge bg-white text-danger mb-3" style="width: fit-content;">LIMITED OFFER</span>
                        <h2 class="fw-bold mb-2">Summer Sale</h2>
                        <h3 class="display-4 fw-bold mb-3">Up to 60% OFF</h3>
                        <p class="mb-3">On fashion, electronics & more!</p>
                        <div>
                            <a href="<?php echo SITE_URL; ?>/products.php?sale=summer" class="btn btn-light btn-lg fw-bold">
                                Shop Now <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6" data-aos="fade-left">
                <div class="promo-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); min-height: 250px;">
                    <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1607082349566-187342175e2f?w=800&q=80') center/cover; opacity: 0.2;"></div>
                    <div class="position-relative h-100 d-flex flex-column justify-content-center p-5 text-white">
                        <span class="badge bg-warning text-dark mb-3" style="width: fit-content;">NEW ARRIVALS</span>
                        <h2 class="fw-bold mb-2">Latest Collection</h2>
                        <h3 class="display-4 fw-bold mb-3">Just Landed!</h3>
                        <p class="mb-3">Fresh picks for this season</p>
                        <div>
                            <a href="<?php echo SITE_URL; ?>/products.php?sort=newest" class="btn btn-dark btn-lg fw-bold">
                                Explore Now <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals Slider -->
<section class="py-5" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-success px-3 py-2 mb-2">NEW ARRIVALS</span>
                <h2 class="fw-bold mb-2">Latest Products</h2>
                <p class="text-muted mb-0">Check out our newest additions</p>
            </div>
            <div class="d-none d-md-flex gap-2">
                <button class="btn btn-outline-primary btn-sm new-arrivals-prev"><i class="fas fa-chevron-left"></i></button>
                <button class="btn btn-outline-primary btn-sm new-arrivals-next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        
        <div class="swiper newArrivalsSwiper">
            <div class="swiper-wrapper">
                <?php foreach ($newArrivals as $product): ?>
                    <div class="swiper-slide">
                        <?php 
                        $screenshots = json_decode($product['screenshots'], true);
                        $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/400x300/4CAF50/ffffff?text=' . urlencode($product['title']);
                        ?>
                        <a href="<?php echo SITE_URL; ?>/<?php echo $product['slug']; ?>" class="text-decoration-none">
                            <div class="product-box shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 15px; transition: all 0.3s ease; background: white;">
                                <span class="badge bg-success position-absolute top-0 start-0 m-3 z-index-1">New</span>
                                <div class="product-image-wrapper" style="width: 100%; height: 250px; overflow: hidden;">
                                    <img src="<?php echo $firstImage; ?>" class="w-100 h-100" alt="<?php echo htmlspecialchars($product['title']); ?>" style="object-fit: cover;">
                                </div>
                                <div class="p-3">
                                    <h6 class="fw-bold mb-2 text-dark"><?php echo htmlspecialchars($product['title']); ?></h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 mb-0 text-primary fw-bold"><?php echo formatPrice($product['price']); ?></span>
                                        <span class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Trending Products Section -->
<section class="py-5 bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-white text-danger px-3 py-2 mb-2">
                <i class="fas fa-fire me-2"></i>TRENDING NOW
            </span>
            <h2 class="fw-bold mb-2 text-dark">Hot Selling Products</h2>
            <p class="mb-0 text-dark fw-bold">These items are flying off the shelves!</p>
        </div>
        
        <div class="row g-4">
            <?php 
            $trendingProducts = $db->query("SELECT * FROM products WHERE status = 'active' ORDER BY RAND() LIMIT 3")->fetch_all(MYSQLI_ASSOC);
            foreach ($trendingProducts as $index => $product): 
                $screenshots = json_decode($product['screenshots'], true);
                $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/400x300/4CAF50/ffffff?text=' . urlencode($product['title']);
            ?>
                <div class="col-md-4" data-aos="flip-left" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                    <a href="<?php echo SITE_URL; ?>/<?php echo $product['slug']; ?>" class="text-decoration-none">
                        <div class="trending-product-card bg-white rounded-4 overflow-hidden shadow-lg position-relative">
                            <div class="position-absolute top-0 start-0 m-3 z-index-1">
                                <span class="badge bg-danger pulse-animation px-3 py-2">
                                    <i class="fas fa-fire me-1"></i>#<?php echo $index + 1; ?> Trending
                                </span>
                            </div>
                            <div class="product-image-wrapper" style="height: 280px; overflow: hidden;">
                                <img src="<?php echo $firstImage; ?>" class="w-100 h-100" alt="<?php echo htmlspecialchars($product['title']); ?>" style="object-fit: cover;">
                            </div>
                            <div class="p-4">
                                <h5 class="fw-bold mb-3 text-dark"><?php echo htmlspecialchars($product['title']); ?></h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="h4 mb-0 text-danger fw-bold"><?php echo formatPrice($product['price']); ?></span>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-warning mb-1">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <small class="text-muted">4.5/5</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 align-items-center mb-3">
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>In Stock</span>
                                    <span class="badge bg-info"><i class="fas fa-truck me-1"></i>Fast Ship</span>
                                </div>
                                <button class="btn btn-danger w-100 fw-bold">
                                    <i class="fas fa-shopping-cart me-2"></i>View Details
                                </button>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Slider -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-primary px-3 py-2 mb-2">BEST SELLERS</span>
                <h2 class="fw-bold mb-2">Featured Products</h2>
                <p class="text-muted mb-0">Discover our most popular items</p>
            </div>
            <div class="d-none d-md-flex gap-2">
                <button class="btn btn-outline-primary btn-sm featured-prev"><i class="fas fa-chevron-left"></i></button>
                <button class="btn btn-outline-primary btn-sm featured-next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        
        <div class="swiper featuredSwiper">
            <div class="swiper-wrapper">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="swiper-slide">
                        <?php 
                        $screenshots = json_decode($product['screenshots'], true);
                        $firstImage = !empty($screenshots) ? UPLOADS_URL . '/screenshots/' . $screenshots[0] : 'https://via.placeholder.com/400x300/4CAF50/ffffff?text=' . urlencode($product['title']);
                        ?>
                        <a href="<?php echo SITE_URL; ?>/<?php echo $product['slug']; ?>" class="text-decoration-none">
                            <div class="product-box shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 15px; transition: all 0.3s ease; background: white;">
                                <span class="badge bg-warning position-absolute top-0 start-0 m-3 z-index-1">Best Seller</span>
                                <div class="product-image-wrapper" style="width: 100%; height: 250px; overflow: hidden;">
                                    <img src="<?php echo $firstImage; ?>" class="w-100 h-100" alt="<?php echo htmlspecialchars($product['title']); ?>" style="object-fit: cover;">
                                </div>
                                <div class="p-3">
                                    <h6 class="fw-bold mb-2 text-dark"><?php echo htmlspecialchars($product['title']); ?></h6>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="h5 mb-0 text-primary fw-bold"><?php echo formatPrice($product['price']); ?></span>
                                        <small class="text-muted">
                                            <i class="fas fa-shopping-bag me-1"></i><?php echo $product['sold']; ?> sold
                                        </small>
                                    </div>
                                    <span class="btn btn-sm btn-primary w-100">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Full Width Banner - Deal of the Day -->
<section class="py-0">
    <div class="container-fluid px-0">
        <div class="full-banner position-relative overflow-hidden" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); min-height: 300px;" data-aos="zoom-in">
            <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1607083206968-13611e3d76db?w=1920&q=80') center/cover; opacity: 0.15;"></div>
            <div class="container position-relative h-100 py-5">
                <div class="row align-items-center h-100">
                    <div class="col-md-8 text-white">
                        <span class="badge bg-warning text-dark px-4 py-2 mb-3 fs-6">
                            ⚡ DEAL OF THE DAY
                        </span>
                        <h1 class="display-3 fw-bold mb-3">Special Weekend Offers!</h1>
                        <h3 class="mb-4">Save Big on Premium Products - Limited Time Only</h3>
                        <div class="d-flex gap-3 align-items-center mb-4">
                            <div class="bg-white bg-opacity-25 rounded px-4 py-3">
                                <h4 class="fw-bold mb-0">50%</h4>
                                <small>Discount</small>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded px-4 py-3">
                                <h4 class="fw-bold mb-0">FREE</h4>
                                <small>Shipping</small>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded px-4 py-3">
                                <h4 class="fw-bold mb-0">24/7</h4>
                                <small>Support</small>
                            </div>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/products.php?deal=daily" class="btn btn-warning btn-lg fw-bold px-5 py-3">
                            <i class="fas fa-shopping-bag me-2"></i>Shop Deals Now
                        </a>
                    </div>
                    <div class="col-md-4 text-center d-none d-md-block">
                        <div class="position-relative">
                            <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 200px; height: 200px; animation: pulse 2s infinite;">
                                <div class="text-center">
                                    <h2 class="display-4 fw-bold text-dark mb-0">50%</h2>
                                    <p class="fw-bold text-dark mb-0">OFF</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Global Shipping Section -->
<section id="shipping-info" class="py-5 bg-gradient" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary px-3 py-2 mb-2"><i class="fas fa-globe me-2"></i>WORLDWIDE DELIVERY</span>
            <h2 class="fw-bold mb-2">We Ship to <?php echo $stats['countries']; ?>+ Countries</h2>
            <p class="text-muted">Fast, reliable international shipping to your doorstep</p>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-6 text-center" data-aos="zoom-in" data-aos-delay="100">
                <div class="bg-white rounded-3 p-3 shadow-sm">
                    <span style="font-size: 3rem;">🇺🇸</span>
                    <h6 class="fw-bold mt-3 mb-2">United States</h6>
                    <small class="text-muted">3-7 business days</small>
                </div>
            </div>
            <div class="col-md-3 col-6 text-center" data-aos="zoom-in" data-aos-delay="200">
                <div class="bg-white rounded-3 p-3 shadow-sm">
                    <span style="font-size: 3rem;">🇬🇧</span>
                    <h6 class="fw-bold mt-3 mb-2">United Kingdom</h6>
                    <small class="text-muted">4-8 business days</small>
                </div>
            </div>
            <div class="col-md-3 col-6 text-center" data-aos="zoom-in" data-aos-delay="300">
                <div class="bg-white rounded-3 p-3 shadow-sm">
                    <span style="font-size: 3rem;">🇨🇦</span>
                    <h6 class="fw-bold mt-3 mb-2">Canada</h6>
                    <small class="text-muted">4-9 business days</small>
                </div>
            </div>
            <div class="col-md-3 col-6 text-center" data-aos="zoom-in" data-aos-delay="400">
                <div class="bg-white rounded-3 p-3 shadow-sm">
                    <span style="font-size: 3rem;">🌍</span>
                    <h6 class="fw-bold mt-3 mb-2">Worldwide</h6>
                    <small class="text-muted">5-15 business days</small>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <div class="row g-3 justify-content-center">
                <div class="col-md-4">
                    <div class="bg-white rounded-3 p-3 shadow-sm">
                        <i class="fas fa-shipping-fast text-success fa-2x mb-2"></i>
                        <p class="mb-0 small"><strong>Free Shipping to USA</strong><br><span class="text-muted">On orders over $50</span></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white rounded-3 p-3 shadow-sm">
                        <i class="fas fa-box text-primary fa-2x mb-2"></i>
                        <p class="mb-0 small"><strong>Real-Time Tracking</strong><br><span class="text-muted">All international orders</span></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white rounded-3 p-3 shadow-sm">
                        <i class="fas fa-file-invoice text-warning fa-2x mb-2"></i>
                        <p class="mb-0 small"><strong>Customs Assistance</strong><br><span class="text-muted">We handle paperwork</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trusted by Countries Section -->
<section class="py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-success px-4 py-2 mb-3" style="font-size: 1rem;">
                <i class="fas fa-globe me-2"></i>GLOBAL REACH
            </span>
            <h2 class="display-5 fw-bold mb-3">Trusted by Customers Worldwide</h2>
            <p class="lead text-muted">Delivering excellence to every corner of the globe</p>
            <div class="mx-auto mt-3" style="width: 100px; height: 4px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></div>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card bg-white rounded-4 p-4 text-center shadow-sm h-100">
                    <div class="stat-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-globe-americas fa-2x text-primary"></i>
                    </div>
                        <h2 class="fw-bold text-primary mb-2 counter" data-target="<?php echo $stats['countries']; ?>">0</h2>
                    <p class="text-muted mb-0 fw-bold">Countries Served</p>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card bg-white rounded-4 p-4 text-center shadow-sm h-100">
                    <div class="stat-icon bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-users fa-2x text-success"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-2 counter" data-target="<?php echo $stats['customers']; ?>">0</h2>
                    <p class="text-muted mb-0 fw-bold">Global Customers</p>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card bg-white rounded-4 p-4 text-center shadow-sm h-100">
                    <div class="stat-icon bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-star fa-2x text-warning"></i>
                    </div>
                    <h2 class="fw-bold text-warning mb-2">4.7★</h2>
                    <p class="text-muted mb-0 fw-bold">Average Rating</p>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card bg-white rounded-4 p-4 text-center shadow-sm h-100">
                    <div class="stat-icon bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-smile-beam fa-2x text-info"></i>
                    </div>
                    <h2 class="fw-bold text-info mb-2">98%</h2>
                    <p class="text-muted mb-0 fw-bold">Satisfaction Rate</p>
                </div>
            </div>
        </div>
        
        <!-- Popular Countries -->
        <div class="text-center">
            <p class="text-muted mb-3 small">POPULAR SHIPPING DESTINATIONS</p>
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-3">
                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.9rem;">🇺🇸 United States</span>
                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.9rem;">🇬🇧 United Kingdom</span>
                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.9rem;">🇨🇦 Canada</span>
                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.9rem;">🇦🇺 Australia</span>
                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.9rem;">🇩🇪 Germany</span>
                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.9rem;">🇫🇷 France</span>
                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.9rem;">🇯🇵 Japan</span>
                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.9rem;">🇸🇬 Singapore</span>
                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.9rem;">🇦🇪 UAE</span>
                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.9rem;">+ 40 More</span>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary px-3 py-2 mb-2">BENEFITS</span>
            <h2 class="fw-bold mb-2">Why International Customers Choose Us</h2>
            <p class="text-muted">Trusted by customers in USA, UK, Canada, Australia & more</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                    <i class="fas fa-shield-alt fa-3x text-primary"></i>
                </div>
                <h5 class="fw-bold mb-3">Buyer Protection</h5>
                <p class="text-muted">100% secure checkout with SSL encryption, PCI compliance, and international payment protection</p>
            </div>
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                    <i class="fas fa-plane fa-3x text-success"></i>
                </div>
                <h5 class="fw-bold mb-3">Global Shipping</h5>
                <p class="text-muted">Express international delivery with customs support and real-time tracking to <?php echo $stats['countries']; ?>+ countries</p>
            </div>
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                    <i class="fas fa-language fa-3x text-info"></i>
                </div>
                <h5 class="fw-bold mb-3">English Support</h5>
                <p class="text-muted">24/7 customer service in English via email, chat, and phone for international customers</p>
            </div>
        </div>
    </div>
</section>

<!-- Video Showcase Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <span class="badge bg-danger px-3 py-2 mb-3">
                    <i class="fas fa-play-circle me-2"></i>WATCH NOW
                </span>
                <h2 class="display-5 fw-bold mb-3">See How We Deliver Excellence</h2>
                <p class="lead text-muted mb-4">From order to delivery, watch our seamless process that ensures your satisfaction.</p>
                
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-box text-center p-3 bg-light rounded-3">
                            <h3 class="fw-bold text-primary mb-1">24hrs</h3>
                            <small class="text-muted">Fast Processing</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box text-center p-3 bg-light rounded-3">
                            <h3 class="fw-bold text-success mb-1">99%</h3>
                            <small class="text-muted">On-Time Delivery</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="video-wrapper position-relative rounded-4 overflow-hidden shadow-lg" style="padding-bottom: 56.25%;">
                    <div class="position-absolute w-100 h-100 top-0 start-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <button class="btn btn-light btn-lg rounded-circle" style="width: 80px; height: 80px;">
                                <i class="fas fa-play fa-2x text-primary"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Single Promotional Banner -->
<section class="py-4 bg-white">
    <div class="container">
        <div class="promo-banner-single position-relative overflow-hidden rounded-4 shadow-lg p-5" style="background: linear-gradient(135deg, #f857a6 0%, #ff5858 100%); min-height: 200px;" data-aos="fade-up">
            <div class="position-absolute" style="top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255, 255, 255, 0.1); border-radius: 50%;"></div>
            <div class="position-absolute" style="bottom: -80px; left: -80px; width: 250px; height: 250px; background: rgba(255, 255, 255, 0.1); border-radius: 50%;"></div>
            <div class="row align-items-center position-relative">
                <div class="col-md-8">
                    <div class="text-white">
                        <span class="badge bg-white text-danger mb-3 px-4 py-2">🔥 CLEARANCE SALE</span>
                        <h2 class="display-4 fw-bold mb-3">End of Season Sale!</h2>
                        <p class="lead mb-4">Get up to <span class="fs-1 fw-bold">80% OFF</span> on last season's collection. Don't miss out!</p>
                        <a href="<?php echo SITE_URL; ?>/products.php?sale=clearance" class="btn btn-light btn-lg fw-bold px-5 py-3">
                            <i class="fas fa-tag me-2"></i>Shop Clearance
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="sale-badge-large">
                        <div class="bg-white text-danger rounded-circle d-inline-flex flex-column align-items-center justify-content-center shadow-lg" style="width: 180px; height: 180px;">
                            <span class="display-3 fw-bold mb-0">80%</span>
                            <span class="fw-bold fs-5">OFF</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us - Comparison Section -->
<section class="py-5 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-white text-primary px-3 py-2 mb-2">
                <i class="fas fa-award me-2"></i>WHY CHOOSE US
            </span>
            <h2 class="fw-bold mb-2 text-dark">We Stand Out from the Competition</h2>
            <p class="mb-0 text-dark fw-bold">See why thousands of customers prefer us</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="benefit-card bg-white rounded-4 p-4 text-center h-100 shadow-lg">
                    <div class="benefit-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-rocket fa-2x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Super Fast Shipping</h5>
                    <p class="text-muted small mb-0">Orders processed within 24 hours. Express delivery available worldwide.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="benefit-card bg-white rounded-4 p-4 text-center h-100 shadow-lg">
                    <div class="benefit-icon bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-shield-alt fa-2x text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Buyer Protection</h5>
                    <p class="text-muted small mb-0">Money-back guarantee. Secure payments. Full buyer protection on all orders.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="benefit-card bg-white rounded-4 p-4 text-center h-100 shadow-lg">
                    <div class="benefit-icon bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-crown fa-2x text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Premium Quality</h5>
                    <p class="text-muted small mb-0">100% authentic products. Quality checked before shipping. No fakes.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="benefit-card bg-white rounded-4 p-4 text-center h-100 shadow-lg">
                    <div class="benefit-icon bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-headset fa-2x text-info"></i>
                    </div>
                    <h5 class="fw-bold mb-3">24/7 Support</h5>
                    <p class="text-muted small mb-0">Always here to help. Live chat, email, and phone support available anytime.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <div class="bg-white rounded-4 p-4 d-inline-block shadow-lg">
                <p class="text-dark mb-3 fw-bold">Trusted by <?php echo number_format($stats['customers']); ?>+ customers in <?php echo $stats['countries']; ?>+ countries</p>
                <div class="d-flex justify-content-center gap-2">
                    <i class="fas fa-star text-warning"></i>
                    <i class="fas fa-star text-warning"></i>
                    <i class="fas fa-star text-warning"></i>
                    <i class="fas fa-star text-warning"></i>
                    <i class="fas fa-star text-warning"></i>
                    <span class="text-dark ms-2 fw-bold">4.9/5.0</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Live Activity Feed -->
<section class="py-4 border-top border-bottom" style="background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-success px-3 py-2 mb-2">
                <i class="fas fa-circle blink-dot me-2"></i>LIVE ACTIVITY
            </span>
            <h4 class="fw-bold mb-2">Recent Orders Worldwide</h4>
            <p class="text-muted small">Real-time purchases from our global customers</p>
        </div>
        
        <div class="swiper recentOrdersSwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="activity-card bg-light rounded-3 p-3 d-flex align-items-center gap-3">
                        <div class="activity-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; min-width: 50px;">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-bold small">Someone from <strong>New York, USA 🇺🇸</strong></p>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">Just purchased a product</p>
                        </div>
                        <small class="text-muted">2 min ago</small>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="activity-card bg-light rounded-3 p-3 d-flex align-items-center gap-3">
                        <div class="activity-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; min-width: 50px;">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-bold small">Customer from <strong>London, UK 🇬🇧</strong></p>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">Left a 5-star review</p>
                        </div>
                        <small class="text-muted">5 min ago</small>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="activity-card bg-light rounded-3 p-3 d-flex align-items-center gap-3">
                        <div class="activity-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; min-width: 50px;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-bold small">Order shipped to <strong>Toronto, Canada 🇨🇦</strong></p>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">2 items delivered today</p>
                        </div>
                        <small class="text-muted">8 min ago</small>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="activity-card bg-light rounded-3 p-3 d-flex align-items-center gap-3">
                        <div class="activity-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; min-width: 50px;">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-bold small">Buyer from <strong>Sydney, Australia 🇦🇺</strong></p>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">Added 3 items to cart</p>
                        </div>
                        <small class="text-muted">12 min ago</small>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="activity-card bg-light rounded-3 p-3 d-flex align-items-center gap-3">
                        <div class="activity-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; min-width: 50px;">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-bold small">Shopper from <strong>Los Angeles, USA 🇺🇸</strong></p>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">Added items to wishlist</p>
                        </div>
                        <small class="text-muted">15 min ago</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Customer Testimonials -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary px-3 py-2 mb-2">TESTIMONIALS</span>
            <h2 class="fw-bold mb-2">What Our Customers Say</h2>
            <p class="text-muted">Don't just take our word for it</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm h-100 testimonial-card">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-4 text-muted">"Fast international shipping to California! Product quality is excellent and customer service responded within hours. Highly recommend!"</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    JD
                                </div>
                                <div>
                                    <strong class="d-block">John Davis</strong>
                                    <small class="text-muted">Verified Buyer</small>
                                </div>
                            </div>
                            <span class="text-muted" style="font-size: 1.5rem;" title="United States">🇺🇸</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm h-100 testimonial-card">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-4 text-muted">"Ordered from UK, received in 5 days! Authentic products, secure payment, and great prices. Will order again!"</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    SM
                                </div>
                                <div>
                                    <strong class="d-block">Sarah Mitchell</strong>
                                    <small class="text-muted">Verified Buyer</small>
                                </div>
                            </div>
                            <span class="text-muted" style="font-size: 1.5rem;" title="United Kingdom">🇬🇧</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card border-0 shadow-sm h-100 testimonial-card">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-4 text-muted">"Shipped to Australia without any issues. Great variety, competitive prices, and excellent customer support!"</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    MJ
                                </div>
                                <div>
                                    <strong class="d-block">Michael Johnson</strong>
                                    <small class="text-muted">Verified Buyer</small>
                                </div>
                            </div>
                            <span class="text-muted" style="font-size: 1.5rem;" title="Australia">🇦🇺</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Special Deals/Limited Offers Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-danger px-3 py-2 mb-2">
                <i class="fas fa-fire me-2"></i>HOT DEALS
            </span>
            <h2 class="fw-bold mb-2">Today's Special Offers</h2>
            <p class="text-muted">Limited time deals - don't miss out!</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="deal-card position-relative rounded-4 overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #FA8BFF 0%, #2BD2FF 100%); padding: 40px;">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-danger px-3 py-2">
                            <i class="fas fa-bolt me-1"></i>Limited Time
                        </span>
                    </div>
                    <div class="text-white">
                        <h3 class="fw-bold mb-3">Weekend Sale</h3>
                        <h2 class="display-4 fw-bold mb-3">40% OFF</h2>
                        <p class="lead mb-4">On all electronics and gadgets. Worldwide shipping available!</p>
                        <a href="<?php echo SITE_URL; ?>/products.php?category=electronics" class="btn btn-light btn-lg px-4 py-3 fw-bold hover-lift">
                            <i class="fas fa-shopping-bag me-2"></i>Shop Electronics
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="deal-card position-relative rounded-4 overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #FD297B 0%, #FF655B 100%); padding: 40px;">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="fas fa-clock me-1"></i>Today Only
                        </span>
                    </div>
                    <div class="text-white">
                        <h3 class="fw-bold mb-3">Free Shipping</h3>
                        <h2 class="display-4 fw-bold mb-3">To USA</h2>
                        <p class="lead mb-4">No minimum purchase! Get free shipping on all orders to USA today.</p>
                        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-warning btn-lg px-4 py-3 fw-bold hover-lift text-dark">
                            <i class="fas fa-truck me-2"></i>Shop Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Triple Small Banners -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4" data-aos="flip-left" data-aos-delay="100">
                <div class="small-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); min-height: 200px; cursor: pointer;" onmouseover="this.style.transform='scale(1.05)'; this.style.transition='all 0.3s ease'" onmouseout="this.style.transform='scale(1)'">
                    <div class="position-absolute" style="top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.2); border-radius: 50%;"></div>
                    <div class="p-4 text-white">
                        <i class="fas fa-gift fa-3x mb-3"></i>
                        <h4 class="fw-bold mb-2">Free Gift</h4>
                        <p class="mb-3">With every purchase over $100</p>
                        <a href="<?php echo SITE_URL; ?>/products.php" class="text-white fw-bold">
                            Learn More <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="flip-left" data-aos-delay="200">
                <div class="small-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); min-height: 200px; cursor: pointer;" onmouseover="this.style.transform='scale(1.05)'; this.style.transition='all 0.3s ease'" onmouseout="this.style.transform='scale(1)'">
                    <div class="position-absolute" style="bottom: -20px; left: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.2); border-radius: 50%;"></div>
                    <div class="p-4 text-white">
                        <i class="fas fa-truck-fast fa-3x mb-3"></i>
                        <h4 class="fw-bold mb-2">Express Delivery</h4>
                        <p class="mb-3">Get your order in 24 hours</p>
                        <a href="<?php echo SITE_URL; ?>/shipping.php" class="text-white fw-bold">
                            Track Order <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="flip-left" data-aos-delay="300">
                <div class="small-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); min-height: 200px; cursor: pointer;" onmouseover="this.style.transform='scale(1.05)'; this.style.transition='all 0.3s ease'" onmouseout="this.style.transform='scale(1)'">
                    <div class="position-absolute" style="top: 50%; right: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.3); border-radius: 50%;"></div>
                    <div class="p-4 text-dark">
                        <i class="fas fa-percent fa-3x mb-3 text-danger"></i>
                        <h4 class="fw-bold mb-2">Member Discount</h4>
                        <p class="mb-3">Extra 10% OFF for members</p>
                        <a href="<?php echo SITE_URL; ?>/register.php" class="text-dark fw-bold">
                            Join Now <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5" style="background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-info px-3 py-2 mb-2">
                <i class="fas fa-question-circle me-2"></i>FAQ
            </span>
            <h2 class="fw-bold mb-2">Frequently Asked Questions</h2>
            <p class="text-muted">Got questions? We've got answers!</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 shadow-sm mb-3" data-aos="fade-up" data-aos-delay="100">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq1">
                                <i class="fas fa-shipping-fast text-primary me-2"></i>
                                Do you ship internationally?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-mdb-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes! We ship to <?php echo $stats['countries']; ?>+ countries worldwide including USA, UK, Canada, Australia, and more. We offer free shipping to USA on orders over $50. International shipping typically takes 5-15 business days depending on your location.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3" data-aos="fade-up" data-aos-delay="200">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq2">
                                <i class="fas fa-credit-card text-success me-2"></i>
                                What payment methods do you accept?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                            <div class="accordion-body">
                                We accept all major payment methods including Visa, Mastercard, American Express, PayPal, Apple Pay, and Google Pay. All transactions are secured with 256-bit SSL encryption and are PCI DSS compliant. We also support multiple currencies including USD, GBP, EUR, CAD, and AUD.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3" data-aos="fade-up" data-aos-delay="300">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq3">
                                <i class="fas fa-undo text-warning me-2"></i>
                                What is your return policy?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                            <div class="accordion-body">
                                We offer a 30-day money-back guarantee on all purchases. If you're not satisfied with your order, you can return it within 30 days for a full refund. Items must be unused and in original packaging. Return shipping costs vary by location.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3" data-aos="fade-up" data-aos-delay="400">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq4">
                                <i class="fas fa-box text-info me-2"></i>
                                How can I track my order?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                            <div class="accordion-body">
                                Once your order ships, you'll receive a tracking number via email. You can track your package in real-time through our website or directly on the carrier's website. We provide tracking for all international orders.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3" data-aos="fade-up" data-aos-delay="500">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq5">
                                <i class="fas fa-shield-alt text-danger me-2"></i>
                                Are your products authentic?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutely! We guarantee 100% authentic products from verified sellers. All items are quality-checked before shipping. We provide buyer protection and stand behind every purchase with our authenticity guarantee.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted mb-2">Still have questions?</p>
                    <a href="<?php echo SITE_URL; ?>/support.php" class="btn btn-outline-primary">
                        <i class="fas fa-headset me-2"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partner Brands & Trust Logos -->
<section class="py-5 bg-gradient" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary px-3 py-2 mb-2">
                <i class="fas fa-handshake me-2"></i>TRUSTED PARTNERS
            </span>
            <h2 class="fw-bold mb-2">Official Partners & Certifications</h2>
            <p class="text-muted">We work with the world's leading brands and services</p>
        </div>
        
        <div class="row g-4 align-items-center justify-content-center">
            <div class="col-6 col-md-3 text-center" data-aos="zoom-in" data-aos-delay="100">
                <div class="partner-logo bg-white rounded-3 p-4 shadow-sm">
                    <i class="fab fa-fedex fa-3x text-primary"></i>
                    <p class="mt-2 mb-0 small fw-bold">FedEx Partner</p>
                </div>
            </div>
            <div class="col-6 col-md-3 text-center" data-aos="zoom-in" data-aos-delay="200">
                <div class="partner-logo bg-white rounded-3 p-4 shadow-sm">
                    <i class="fab fa-dhl fa-3x text-warning"></i>
                    <p class="mt-2 mb-0 small fw-bold">DHL Shipping</p>
                </div>
            </div>
            <div class="col-6 col-md-3 text-center" data-aos="zoom-in" data-aos-delay="300">
                <div class="partner-logo bg-white rounded-3 p-4 shadow-sm">
                    <i class="fab fa-usps fa-3x text-info"></i>
                    <p class="mt-2 mb-0 small fw-bold">USPS Certified</p>
                </div>
            </div>
            <div class="col-6 col-md-3 text-center" data-aos="zoom-in" data-aos-delay="400">
                <div class="partner-logo bg-white rounded-3 p-4 shadow-sm">
                    <i class="fas fa-certificate fa-3x text-success"></i>
                    <p class="mt-2 mb-0 small fw-bold">SSL Verified</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Reviews Showcase -->
<section class="py-5" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-warning px-3 py-2 mb-2">
                <i class="fas fa-star me-2"></i>REVIEWS
            </span>
            <h2 class="fw-bold mb-2">Recent Customer Reviews</h2>
            <p class="text-muted">See what our customers are saying about their purchases</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="review-card bg-white rounded-4 p-4 shadow-sm h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="review-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Emma Wilson</h6>
                            <small class="text-muted">Verified Purchase</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="small text-muted mb-2">"Amazing quality! Shipped fast to California. Exactly as described."</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">2 days ago</small>
                        <span class="text-muted" style="font-size: 1.2rem;">🇺🇸</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="review-card bg-white rounded-4 p-4 shadow-sm h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="review-avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">James Parker</h6>
                            <small class="text-muted">Verified Purchase</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="small text-muted mb-2">"Great service! Product arrived in perfect condition to London."</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">3 days ago</small>
                        <span class="text-muted" style="font-size: 1.2rem;">🇬🇧</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="review-card bg-white rounded-4 p-4 shadow-sm h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="review-avatar bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Sophie Martin</h6>
                            <small class="text-muted">Verified Purchase</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="small text-muted mb-2">"Love it! Fast delivery to Toronto. Will definitely order again!"</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">5 days ago</small>
                        <span class="text-muted" style="font-size: 1.2rem;">🇨🇦</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="review-card bg-white rounded-4 p-4 shadow-sm h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="review-avatar bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Alex Chen</h6>
                            <small class="text-muted">Verified Purchase</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="small text-muted mb-2">"Excellent quality and packaging. Happy with my purchase!"</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">1 week ago</small>
                        <span class="text-muted" style="font-size: 1.2rem;">🇦🇺</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mobile App Download Section -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <span class="badge bg-success px-3 py-2 mb-3">
                    <i class="fas fa-mobile-alt me-2"></i>DOWNLOAD APP
                </span>
                <h2 class="display-5 fw-bold mb-3">Shop Faster with Our Mobile App</h2>
                <p class="lead mb-4 opacity-90">Get exclusive app-only deals, faster checkout, and track your orders in real-time!</p>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="bg-success bg-opacity-25 rounded-3 p-3 me-3">
                                <i class="fas fa-bolt fa-2x text-success"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold">Lightning Fast</h6>
                                <small class="opacity-75">Optimized for speed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="bg-warning bg-opacity-25 rounded-3 p-3 me-3">
                                <i class="fas fa-bell fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold">Push Notifications</h6>
                                <small class="opacity-75">Never miss a deal</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex flex-wrap gap-3">
                    <a href="https://www.usashopper.site/wp-content/uploads/2025/11/globalsell.apk" class="btn btn-light btn-lg px-4 hover-lift">
                        <i class="fab fa-apple me-2"></i>App Store
                    </a>
                    <a href="https://www.usashopper.site/wp-content/uploads/2025/11/globalsell.apk" class="btn btn-outline-light btn-lg px-4 hover-lift">
                        <i class="fab fa-google-play me-2"></i>Google Play
                    </a>
                </div>
                
                <div class="mt-4">
                    <small class="opacity-75">
                        <i class="fas fa-star text-warning me-1"></i>
                        <i class="fas fa-star text-warning me-1"></i>
                        <i class="fas fa-star text-warning me-1"></i>
                        <i class="fas fa-star text-warning me-1"></i>
                        <i class="fas fa-star text-warning me-2"></i>
                        4.8/5 from 10,000+ reviews
                    </small>
                </div>
            </div>
            <div class="col-lg-6 text-center" data-aos="fade-left">
                <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=600&h=500&fit=crop" class="img-fluid rounded-4 shadow-lg" alt="Mobile App" style="max-height: 500px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="newsletter-card rounded-4 p-5 text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%), url('https://images.unsplash.com/photo-1513104890130-7bb5b05f2dcb?w=1920&q=80&fm=jpg&crop=entropy&cs=tinysrgb') no-repeat center center / cover;" data-aos="zoom-in">
                    <div class="position-absolute" style="top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                    <div class="position-absolute" style="bottom: -80px; left: -80px; width: 250px; height: 250px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                    
                    <div class="position-relative">
                        <div class="mb-4">
                            <i class="fas fa-envelope-open-text fa-4x text-white opacity-75"></i>
                        </div>
                        <h2 class="fw-bold text-white mb-3">Subscribe to Our Newsletter</h2>
                        <p class="text-white mb-4 opacity-90">Get exclusive deals, new product alerts, and special offers delivered to your inbox!</p>
                        
                        <form id="newsletterForm" class="row g-3 justify-content-center">
                            <div class="col-md-8">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-0">
                                        <i class="fas fa-envelope text-primary"></i>
                                    </span>
                                    <input type="email" class="form-control border-0" placeholder="Enter your email address" id="newsletterEmail" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold hover-lift">
                                    <i class="fas fa-paper-plane me-2"></i>Subscribe
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4">
                            <small class="text-white opacity-75">
                                <i class="fas fa-lock me-1"></i>We respect your privacy. Unsubscribe anytime.
                            </small>
                        </div>
                        
                        <div class="row g-3 mt-3 text-white">
                            <div class="col-4">
                                <i class="fas fa-gift fa-2x mb-2"></i>
                                <p class="small mb-0">Exclusive Offers</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-star fa-2x mb-2"></i>
                                <p class="small mb-0">Early Access</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-bell fa-2x mb-2"></i>
                                <p class="small mb-0">New Arrivals</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Price Comparison Section -->
<section class="py-5" style="background: linear-gradient(135deg, #fddb92 0%, #d1fdff 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-success px-3 py-2 mb-2">
                <i class="fas fa-dollar-sign me-2"></i>BEST VALUE
            </span>
            <h2 class="fw-bold mb-2">Why Pay More Elsewhere?</h2>
            <p class="text-muted">Compare our prices with competitors - we're always cheaper!</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="comparison-card bg-white rounded-4 p-4 shadow-sm h-100 text-center">
                    <div class="mb-3">
                        <span class="badge bg-danger">Competitor</span>
                    </div>
                    <h3 class="display-4 fw-bold text-muted mb-3"><s>$149</s></h3>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>Limited Selection</li>
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>High Shipping Costs</li>
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>Slow Delivery</li>
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>No Support</li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="comparison-card bg-gradient text-white rounded-4 p-4 shadow-lg h-100 text-center position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); transform: scale(1.05);">
                    <div class="position-absolute top-0 start-50 translate-middle">
                        <span class="badge bg-warning text-dark px-4 py-2">
                            <i class="fas fa-crown me-2"></i>BEST CHOICE
                        </span>
                    </div>
                    <div class="mb-3 mt-3">
                        <span class="badge bg-white text-primary">Our Store</span>
                    </div>
                    <h3 class="display-3 fw-bold mb-3">$99</h3>
                    <div class="mb-4">
                        <span class="badge bg-success px-3 py-2">Save $50!</span>
                    </div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-2"><i class="fas fa-check-circle me-2"></i>Huge Selection</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2"></i>FREE Shipping</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2"></i>Fast Delivery</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2"></i>24/7 Support</li>
                    </ul>
                    <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-warning btn-lg w-100 fw-bold">
                        <i class="fas fa-shopping-cart me-2"></i>Shop Now
                    </a>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="comparison-card bg-white rounded-4 p-4 shadow-sm h-100 text-center">
                    <div class="mb-3">
                        <span class="badge bg-danger">Other Sites</span>
                    </div>
                    <h3 class="display-4 fw-bold text-muted mb-3"><s>$139</s></h3>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>Hidden Fees</li>
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>Poor Quality</li>
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>Long Wait Times</li>
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>Limited Returns</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Themed Banners Carousel -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary px-3 py-2 mb-2">
                <i class="fas fa-calendar-alt me-2"></i>SPECIAL OCCASIONS & CATEGORIES
            </span>
            <h2 class="fw-bold mb-2">Shop by Season & Category</h2>
            <p class="text-muted">Exclusive collections for every occasion and lifestyle</p>
        </div>
        
        <div class="swiper themedBannersSwiper">
            <div class="swiper-wrapper">
                
                <!-- Halloween Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #ff6a00 0%, #ee0979 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1509557965875-b88c97052f0e?w=800&q=80') center/cover; opacity: 0.2;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-dark px-3 py-2 mb-3">🎃 HALLOWEEN SPECIAL</span>
                                <h2 class="display-4 fw-bold mb-3">Spooky Season Sale!</h2>
                                <p class="lead mb-4">Get your costumes, decorations & treats ready. Up to 50% OFF!</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=halloween" class="btn btn-warning btn-lg fw-bold px-5">
                                    <i class="fas fa-ghost me-2"></i>Shop Halloween
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Christmas Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #c92b2b 0%, #0f5132 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1512389142860-9c449e58a543?w=800&q=80') center/cover; opacity: 0.15;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-white text-danger px-3 py-2 mb-3">🎄 MERRY CHRISTMAS</span>
                                <h2 class="display-4 fw-bold mb-3">Holiday Magic!</h2>
                                <p class="lead mb-4">Spread joy with our festive gifts, decorations & special treats!</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=christmas" class="btn btn-light btn-lg fw-bold px-5">
                                    <i class="fas fa-gift me-2"></i>Shop Christmas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Independence Day Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #0b1f8b 0%, #dc2f3e 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1530911876082-76d38e9c7c0d?w=800&q=80') center/cover; opacity: 0.2;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-white text-primary px-3 py-2 mb-3">🇺🇸 INDEPENDENCE DAY</span>
                                <h2 class="display-4 fw-bold mb-3">Celebrate Freedom!</h2>
                                <p class="lead mb-4">Patriotic apparel, BBQ essentials & party supplies. Shop now!</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=independence-day" class="btn btn-warning btn-lg fw-bold px-5">
                                    <i class="fas fa-flag-usa me-2"></i>Shop 4th of July
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Health & Wellness Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&q=80') center/cover; opacity: 0.15;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-white text-success px-3 py-2 mb-3">💪 HEALTH & WELLNESS</span>
                                <h2 class="display-4 fw-bold mb-3">Your Best Self!</h2>
                                <p class="lead mb-4">Supplements, fitness gear & wellness products for a healthier you.</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=health-wellness" class="btn btn-dark btn-lg fw-bold px-5">
                                    <i class="fas fa-heartbeat me-2"></i>Shop Wellness
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Beauty Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&q=80') center/cover; opacity: 0.15;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-white text-danger px-3 py-2 mb-3">💄 BEAUTY & SKINCARE</span>
                                <h2 class="display-4 fw-bold mb-3">Glow Up!</h2>
                                <p class="lead mb-4">Premium skincare, makeup & fragrances for your beauty routine.</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=beauty" class="btn btn-light btn-lg fw-bold px-5">
                                    <i class="fas fa-spa me-2"></i>Shop Beauty
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Apparel Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #434343 0%, #000000 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=800&q=80') center/cover; opacity: 0.3;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-warning text-dark px-3 py-2 mb-3">👔 FASHION & APPAREL</span>
                                <h2 class="display-4 fw-bold mb-3">Style Statement!</h2>
                                <p class="lead mb-4">Trendy clothing, shoes & accessories for every occasion.</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=apparel" class="btn btn-warning btn-lg fw-bold px-5">
                                    <i class="fas fa-tshirt me-2"></i>Shop Fashion
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pets Banner -->
                <div class="swiper-slide">
                    <div class="themed-banner position-relative overflow-hidden rounded-4 shadow-lg" style="background: linear-gradient(135deg, #f77062 0%, #fe5196 100%); min-height: 350px;">
                        <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1450778869180-41d0601e046e?w=800&q=80') center/cover; opacity: 0.2;"></div>
                        <div class="position-relative p-5 text-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-white text-danger px-3 py-2 mb-3">🐾 PET SUPPLIES</span>
                                <h2 class="display-4 fw-bold mb-3">Pamper Your Pets!</h2>
                                <p class="lead mb-4">Everything your furry friends need - food, toys, accessories & more!</p>
                            </div>
                            <div>
                                <a href="<?php echo SITE_URL; ?>/products.php?category=pets" class="btn btn-light btn-lg fw-bold px-5">
                                    <i class="fas fa-paw me-2"></i>Shop Pets
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Navigation -->
            <div class="swiper-button-next themed-next"></div>
            <div class="swiper-button-prev themed-prev"></div>
            
            <!-- Pagination -->
            <div class="swiper-pagination themed-pagination mt-4"></div>
        </div>
    </div>
</section>

<!-- Customer Photo Gallery -->
<section class="py-5" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%, #feada6 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-pink px-3 py-2 mb-2" style="background: #e91e63;">
                <i class="fas fa-heart me-2"></i>CUSTOMER LOVE
            </span>
            <h2 class="fw-bold mb-2">Real Customers, Real Photos</h2>
            <p class="text-muted">See what our customers are sharing!</p>
        </div>
        
        <div class="row g-3">
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="100">
                <div class="customer-photo-card rounded-4 overflow-hidden shadow position-relative" style="height: 250px;">
                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&h=400&fit=crop" class="w-100 h-100" alt="Customer Photo" style="object-fit: cover;">
                    <div class="photo-overlay position-absolute w-100 h-100 top-0 start-0 d-flex align-items-end p-3">
                        <div class="text-white">
                            <p class="mb-1 small fw-bold">@sarah_m</p>
                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="200">
                <div class="customer-photo-card rounded-4 overflow-hidden shadow position-relative" style="height: 250px;">
                    <img src="https://images.unsplash.com/photo-1607083206325-caf1edba7a0f?w=400&h=400&fit=crop" class="w-100 h-100" alt="Customer Photo" style="object-fit: cover;">
                    <div class="photo-overlay position-absolute w-100 h-100 top-0 start-0 d-flex align-items-end p-3">
                        <div class="text-white">
                            <p class="mb-1 small fw-bold">@mike_j</p>
                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="300">
                <div class="customer-photo-card rounded-4 overflow-hidden shadow position-relative" style="height: 250px;">
                    <img src="https://images.unsplash.com/photo-1607082349566-187342175e2f?w=400&h=400&fit=crop" class="w-100 h-100" alt="Customer Photo" style="object-fit: cover;">
                    <div class="photo-overlay position-absolute w-100 h-100 top-0 start-0 d-flex align-items-end p-3">
                        <div class="text-white">
                            <p class="mb-1 small fw-bold">@emma_w</p>
                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="400">
                <div class="customer-photo-card rounded-4 overflow-hidden shadow position-relative" style="height: 250px;">
                    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=400&h=400&fit=crop" class="w-100 h-100" alt="Customer Photo" style="object-fit: cover;">
                    <div class="photo-overlay position-absolute w-100 h-100 top-0 start-0 d-flex align-items-end p-3">
                        <div class="text-white">
                            <p class="mb-1 small fw-bold">@john_d</p>
                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-muted mb-2">Share your photos and tag us!</p>
            <a href="<?php echo SITE_URL; ?>/upload-customer-photo.php" class="btn btn-outline-primary">
                <i class="fas fa-camera me-2"></i>Upload Your Photo
            </a>
        </div>
    </div>
</section>

<!-- Shipping Progress Timeline -->
<section class="py-5 bg-gradient" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-white text-success px-3 py-2 mb-2">
                <i class="fas fa-shipping-fast me-2"></i>DELIVERY PROCESS
            </span>
            <h2 class="fw-bold mb-2 text-dark">Your Order Journey</h2>
            <p class="mb-0 text-dark fw-bold">Track every step from order to delivery</p>
        </div>
        
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="timeline-container position-relative">
                    <div class="row g-4 text-center">
                        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                            <div class="timeline-step">
                                <div class="timeline-icon bg-white text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px;">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                                <h5 class="fw-bold mb-2 text-dark">1. Order Placed</h5>
                                <p class="small text-dark">Instant confirmation sent to your email</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                            <div class="timeline-step">
                                <div class="timeline-icon bg-white text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px;">
                                    <i class="fas fa-box-open fa-2x"></i>
                                </div>
                                <h5 class="fw-bold mb-2 text-dark">2. Processing</h5>
                                <p class="small text-dark">Packed with care within 24 hours</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                            <div class="timeline-step">
                                <div class="timeline-icon bg-white text-info rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px;">
                                    <i class="fas fa-truck fa-2x"></i>
                                </div>
                                <h5 class="fw-bold mb-2 text-dark">3. Shipped</h5>
                                <p class="small text-dark">Real-time tracking available</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                            <div class="timeline-step">
                                <div class="timeline-icon bg-white text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px;">
                                    <i class="fas fa-home fa-2x"></i>
                                </div>
                                <h5 class="fw-bold mb-2 text-dark">4. Delivered</h5>
                                <p class="small text-dark">Safe at your doorstep!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Brands Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-dark px-3 py-2 mb-2">
                <i class="fas fa-award me-2"></i>TRUSTED BRANDS
            </span>
            <h2 class="fw-bold mb-2">Shop From Top Brands</h2>
            <p class="text-muted">We partner with the world's leading brands</p>
        </div>
        
        <div class="swiper brandsSwiper">
            <div class="swiper-wrapper align-items-center">
                <div class="swiper-slide">
                    <div class="brand-logo-card text-center p-4 rounded-3 bg-light">
                        <i class="fab fa-apple fa-4x text-dark"></i>
                        <p class="mt-2 mb-0 small fw-bold">Apple</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="brand-logo-card text-center p-4 rounded-3 bg-light">
                        <i class="fab fa-microsoft fa-4x" style="color: #00A4EF;"></i>
                        <p class="mt-2 mb-0 small fw-bold">Microsoft</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="brand-logo-card text-center p-4 rounded-3 bg-light">
                        <i class="fab fa-google fa-4x" style="color: #4285F4;"></i>
                        <p class="mt-2 mb-0 small fw-bold">Google</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="brand-logo-card text-center p-4 rounded-3 bg-light">
                        <i class="fab fa-amazon fa-4x" style="color: #FF9900;"></i>
                        <p class="mt-2 mb-0 small fw-bold">Amazon</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="brand-logo-card text-center p-4 rounded-3 bg-light">
                        <i class="fab fa-facebook fa-4x" style="color: #1877F2;"></i>
                        <p class="mt-2 mb-0 small fw-bold">Meta</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="brand-logo-card text-center p-4 rounded-3 bg-light">
                        <i class="fab fa-samsung fa-4x" style="color: #1428A0;"></i>
                        <p class="mt-2 mb-0 small fw-bold">Samsung</p>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination brands-pagination mt-4"></div>
        </div>
    </div>
</section>

<!-- Money-Back Guarantee Section -->
<section class="py-5" style="background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center mb-4 mb-lg-0" data-aos="fade-right">
                <div class="guarantee-icon-wrapper">
                    <div class="position-relative d-inline-block">
                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg" style="width: 200px; height: 200px;">
                            <i class="fas fa-shield-alt fa-5x text-success"></i>
                        </div>
                        <div class="position-absolute top-0 end-0 bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fw-bold text-dark">30</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="display-5 fw-bold text-white mb-3">30-Day Money-Back Guarantee</h2>
                <p class="text-white mb-4 lead">Shop with confidence! If you're not 100% satisfied, we'll refund your money—no questions asked.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fas fa-check-circle fa-2x text-white"></i>
                            <div class="text-white">
                                <h6 class="fw-bold mb-1">Easy Returns</h6>
                                <small>Simple return process</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fas fa-check-circle fa-2x text-white"></i>
                            <div class="text-white">
                                <h6 class="fw-bold mb-1">Fast Refunds</h6>
                                <small>Money back in 3-5 days</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fas fa-check-circle fa-2x text-white"></i>
                            <div class="text-white">
                                <h6 class="fw-bold mb-1">No Hidden Fees</h6>
                                <small>100% transparent policy</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fas fa-check-circle fa-2x text-white"></i>
                            <div class="text-white">
                                <h6 class="fw-bold mb-1">Secure Process</h6>
                                <small>Safe & encrypted</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Category Navigation Icons -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary px-3 py-2 mb-2">
                <i class="fas fa-th-large me-2"></i>QUICK SHOP
            </span>
            <h2 class="fw-bold mb-2">Shop by Category</h2>
            <p class="text-muted">Browse our most popular categories</p>
        </div>
        
        <div class="row g-4">
            <div class="col-6 col-md-4 col-lg-2" data-aos="zoom-in" data-aos-delay="100">
                <a href="<?php echo SITE_URL; ?>/products.php?category=electronics" class="text-decoration-none">
                    <div class="category-quick-card text-center p-4 rounded-4 shadow-sm bg-light hover-lift-sm">
                        <div class="category-icon-circle bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-laptop fa-2x text-primary"></i>
                        </div>
                        <h6 class="fw-bold mb-0">Electronics</h6>
                    </div>
                </a>
            </div>
            
            <div class="col-6 col-md-4 col-lg-2" data-aos="zoom-in" data-aos-delay="150">
                <a href="<?php echo SITE_URL; ?>/products.php?category=fashion" class="text-decoration-none">
                    <div class="category-quick-card text-center p-4 rounded-4 shadow-sm bg-light hover-lift-sm">
                        <div class="category-icon-circle bg-danger bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-tshirt fa-2x text-danger"></i>
                        </div>
                        <h6 class="fw-bold mb-0">Fashion</h6>
                    </div>
                </a>
            </div>
            
            <div class="col-6 col-md-4 col-lg-2" data-aos="zoom-in" data-aos-delay="200">
                <a href="<?php echo SITE_URL; ?>/products.php?category=beauty" class="text-decoration-none">
                    <div class="category-quick-card text-center p-4 rounded-4 shadow-sm bg-light hover-lift-sm">
                        <div class="category-icon-circle bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-spa fa-2x text-warning"></i>
                        </div>
                        <h6 class="fw-bold mb-0">Beauty</h6>
                    </div>
                </a>
            </div>
            
            <div class="col-6 col-md-4 col-lg-2" data-aos="zoom-in" data-aos-delay="250">
                <a href="<?php echo SITE_URL; ?>/products.php?category=home" class="text-decoration-none">
                    <div class="category-quick-card text-center p-4 rounded-4 shadow-sm bg-light hover-lift-sm">
                        <div class="category-icon-circle bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-home fa-2x text-success"></i>
                        </div>
                        <h6 class="fw-bold mb-0">Home</h6>
                    </div>
                </a>
            </div>
            
            <div class="col-6 col-md-4 col-lg-2" data-aos="zoom-in" data-aos-delay="300">
                <a href="<?php echo SITE_URL; ?>/products.php?category=sports" class="text-decoration-none">
                    <div class="category-quick-card text-center p-4 rounded-4 shadow-sm bg-light hover-lift-sm">
                        <div class="category-icon-circle bg-info bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-dumbbell fa-2x text-info"></i>
                        </div>
                        <h6 class="fw-bold mb-0">Sports</h6>
                    </div>
                </a>
            </div>
            
            <div class="col-6 col-md-4 col-lg-2" data-aos="zoom-in" data-aos-delay="350">
                <a href="<?php echo SITE_URL; ?>/products.php?category=toys" class="text-decoration-none">
                    <div class="category-quick-card text-center p-4 rounded-4 shadow-sm bg-light hover-lift-sm">
                        <div class="category-icon-circle bg-pink bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-gamepad fa-2x" style="color: #e91e63;"></i>
                        </div>
                        <h6 class="fw-bold mb-0">Toys</h6>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Live Statistics Counter -->
<section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="position-absolute w-100 h-100" style="top: 0; left: 0; opacity: 0.1;">
        <div style="background: url('data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" fill="%23ffffff" fill-opacity="0.3" fill-rule="evenodd"/%3E%3C/svg%3E'); height: 100%;"></div>
    </div>
    <div class="container position-relative">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-warning text-dark px-3 py-2 mb-2">
                <i class="fas fa-chart-line me-2"></i>LIVE STATS
            </span>
            <h2 class="fw-bold mb-2">Our Growing Community</h2>
            <p class="opacity-90">Join thousands of happy shoppers worldwide</p>
        </div>
        
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card bg-white bg-opacity-10 backdrop-blur rounded-4 p-4 h-100">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-users fa-3x text-warning"></i>
                    </div>
                    <h2 class="display-4 fw-bold mb-2 live-counter" data-target="<?php echo $stats['customers']; ?>">0</h2>
                    <p class="mb-0 fw-bold">Happy Customers</p>
                </div>
            </div>
            
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card bg-white bg-opacity-10 backdrop-blur rounded-4 p-4 h-100">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-box fa-3x text-success"></i>
                    </div>
                    <h2 class="display-4 fw-bold mb-2 live-counter" data-target="<?php echo $stats['products']; ?>">0</h2>
                    <p class="mb-0 fw-bold">Products Available</p>
                </div>
            </div>
            
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card bg-white bg-opacity-10 backdrop-blur rounded-4 p-4 h-100">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-shopping-bag fa-3x text-info"></i>
                    </div>
                    <h2 class="display-4 fw-bold mb-2 live-counter" data-target="<?php echo $stats['orders']; ?>">0</h2>
                    <p class="mb-0 fw-bold">Orders Delivered</p>
                </div>
            </div>
            
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card bg-white bg-opacity-10 backdrop-blur rounded-4 p-4 h-100">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-globe fa-3x text-danger"></i>
                    </div>
                    <h2 class="display-4 fw-bold mb-2"><?php echo $stats['countries']; ?><span class="fs-3">+</span></h2>
                    <p class="mb-0 fw-bold">Countries Served</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Payment Methods & Security -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-success px-3 py-2 mb-2">
                <i class="fas fa-lock me-2"></i>100% SECURE
            </span>
            <h2 class="fw-bold mb-2">Safe & Secure Payments</h2>
            <p class="text-muted">We accept all major payment methods</p>
        </div>
        
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto">
                <div class="bg-white rounded-4 shadow-lg p-5">
                    <div class="row g-4 align-items-center justify-content-center text-center">
                        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="100">
                            <div class="payment-method-card p-3 rounded-3 bg-light">
                                <i class="fab fa-cc-visa fa-4x" style="color: #1A1F71;"></i>
                                <p class="mt-2 mb-0 small fw-bold">Visa</p>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="150">
                            <div class="payment-method-card p-3 rounded-3 bg-light">
                                <i class="fab fa-cc-mastercard fa-4x" style="color: #EB001B;"></i>
                                <p class="mt-2 mb-0 small fw-bold">Mastercard</p>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="200">
                            <div class="payment-method-card p-3 rounded-3 bg-light">
                                <i class="fab fa-cc-amex fa-4x" style="color: #006FCF;"></i>
                                <p class="mt-2 mb-0 small fw-bold">Amex</p>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="250">
                            <div class="payment-method-card p-3 rounded-3 bg-light">
                                <i class="fab fa-cc-paypal fa-4x" style="color: #003087;"></i>
                                <p class="mt-2 mb-0 small fw-bold">PayPal</p>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="300">
                            <div class="payment-method-card p-3 rounded-3 bg-light">
                                <i class="fab fa-apple-pay fa-4x text-dark"></i>
                                <p class="mt-2 mb-0 small fw-bold">Apple Pay</p>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="350">
                            <div class="payment-method-card p-3 rounded-3 bg-light">
                                <i class="fab fa-google-pay fa-4x" style="color: #4285F4;"></i>
                                <p class="mt-2 mb-0 small fw-bold">Google Pay</p>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="400">
                            <div class="payment-method-card p-3 rounded-3 bg-light">
                                <i class="fab fa-stripe fa-4x" style="color: #635BFF;"></i>
                                <p class="mt-2 mb-0 small fw-bold">Stripe</p>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="450">
                            <div class="payment-method-card p-3 rounded-3 bg-light">
                                <i class="fas fa-credit-card fa-4x text-primary"></i>
                                <p class="mt-2 mb-0 small fw-bold">All Cards</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-top text-center">
                        <div class="row g-3 justify-content-center">
                            <div class="col-auto">
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-shield-alt me-2"></i>SSL Encrypted
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-primary px-3 py-2">
                                    <i class="fas fa-check-circle me-2"></i>PCI Compliant
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    <i class="fas fa-lock me-2"></i>256-bit Security
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Video Testimonial Section -->
<section class="py-5" style="background: linear-gradient(135deg, #ee9ca7 0%, #ffdde1 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-danger px-3 py-2 mb-2">
                <i class="fas fa-video me-2"></i>VIDEO TESTIMONIALS
            </span>
            <h2 class="fw-bold mb-2">See What Our Customers Say</h2>
            <p class="text-muted">Real reviews from real customers</p>
        </div>
        
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <div class="video-testimonial-card rounded-4 overflow-hidden shadow-lg position-relative" style="height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center">
                        <div class="text-center text-white">
                            <div class="video-play-btn bg-white bg-opacity-25 backdrop-blur rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; cursor: pointer;">
                                <i class="fas fa-play fa-3x text-white"></i>
                            </div>
                            <p class="lead fw-bold">Watch Customer Reviews</p>
                            <small class="opacity-75">See why customers love shopping with us</small>
                        </div>
                    </div>
                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop" class="w-100 h-100" alt="Video Thumbnail" style="object-fit: cover; opacity: 0.3;">
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left">
                <div class="video-testimonial-quotes">
                    <div class="quote-card bg-white rounded-4 p-4 shadow-sm mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fas fa-quote-left fa-2x text-primary opacity-50"></i>
                            <div>
                                <p class="mb-2">"I've been ordering from this site for 2 years. Best prices, authentic products, and fast shipping to California!"</p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <strong class="d-block">Jennifer Martinez</strong>
                                        <small class="text-muted">Los Angeles, USA 🇺🇸</small>
                                    </div>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="quote-card bg-white rounded-4 p-4 shadow-sm mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fas fa-quote-left fa-2x text-success opacity-50"></i>
                            <div>
                                <p class="mb-2">"Excellent customer service! They helped me with customs clearance to UK. Highly recommend!"</p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <strong class="d-block">David Thompson</strong>
                                        <small class="text-muted">Manchester, UK 🇬🇧</small>
                                    </div>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="quote-card bg-white rounded-4 p-4 shadow-sm">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fas fa-quote-left fa-2x text-info opacity-50"></i>
                            <div>
                                <p class="mb-2">"Amazing deals! Got my order in Australia within a week. Very satisfied with the quality."</p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <strong class="d-block">Emily Cooper</strong>
                                        <small class="text-muted">Sydney, Australia 🇦🇺</small>
                                    </div>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 100px 0;">
    <div class="position-absolute w-100 h-100" style="top: 0; left: 0; opacity: 0.1;">
        <div style="background: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); height: 100%;"></div>
    </div>
    <div class="container text-center text-white position-relative" data-aos="zoom-in">
        <div class="mb-4">
            <i class="fas fa-gift fa-4x mb-3 opacity-75"></i>
        </div>
        <h2 class="display-4 fw-bold mb-3">Ready to Start Shopping?</h2>
        <p class="lead mb-5" style="max-width: 700px; margin: 0 auto; font-size: 1.3rem;">Join thousands of satisfied customers and discover amazing products with exclusive deals today!</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-warning btn-lg px-5 py-3 shadow-lg fw-bold hover-lift">
                <i class="fas fa-shopping-bag me-2"></i>Shop Now
            </a>
            <?php if (!isLoggedIn()): ?>
            <a href="<?php echo SITE_URL; ?>/signup.php" class="btn btn-outline-light btn-lg px-5 py-3 hover-lift">
                <i class="fas fa-user-plus me-2"></i>Sign Up Free
            </a>
            <?php endif; ?>
        </div>
        
        <!-- Trust Indicators -->
        <div class="row mt-5 pt-4 border-top border-white border-opacity-25">
            <div class="col-md-3 col-6 mb-3">
                <h3 class="fw-bold mb-1"><i class="fas fa-check-circle me-2"></i>100%</h3>
                <small>Authentic Products</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <h3 class="fw-bold mb-1"><i class="fas fa-shield-alt me-2"></i>Secure</h3>
                <small>Payment Gateway</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <h3 class="fw-bold mb-1"><i class="fas fa-truck me-2"></i>Fast</h3>
                <small>Delivery Service</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <h3 class="fw-bold mb-1"><i class="fas fa-headset me-2"></i>24/7</h3>
                <small>Customer Support</small>
            </div>
        </div>
    </div>
</section>

<!-- AOS (Animate On Scroll) CSS -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

<!-- AOS (Animate On Scroll) JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
// New Arrivals Swiper
const newArrivalsSwiper = new Swiper('.newArrivalsSwiper', {
    slidesPerView: 1,
    spaceBetween: 20,
    navigation: {
        nextEl: '.new-arrivals-next',
        prevEl: '.new-arrivals-prev',
    },
    breakpoints: {
        640: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        768: {
            slidesPerView: 3,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 4,
            spaceBetween: 30,
        },
    },
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    loop: true,
});

// Hero Banner Swiper
const heroSwiper = new Swiper('.heroSwiper', {
    slidesPerView: 1,
    spaceBetween: 0,
    loop: true,
    autoplay: {
        delay: 6000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.hero-pagination',
        clickable: true,
        dynamicBullets: true,
    },
    navigation: {
        nextEl: '.hero-next',
        prevEl: '.hero-prev',
    },
    effect: 'fade',
    fadeEffect: {
        crossFade: true
    },
    speed: 1000,
});

// Banner Swiper
const bannerSwiper = new Swiper('.bannerSwiper', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.banner-pagination',
        clickable: true,
        dynamicBullets: true,
    },
    navigation: {
        nextEl: '.banner-next',
        prevEl: '.banner-prev',
    },
    effect: 'fade',
    fadeEffect: {
        crossFade: true
    },
});

// Featured Products Swiper
const featuredSwiper = new Swiper('.featuredSwiper', {
    slidesPerView: 1,
    spaceBetween: 20,
    navigation: {
        nextEl: '.featured-next',
        prevEl: '.featured-prev',
    },
    breakpoints: {
        640: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        768: {
            slidesPerView: 3,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 4,
            spaceBetween: 30,
        },
    },
    autoplay: {
        delay: 3500,
        disableOnInteraction: false,
    },
    loop: true,
});

// Recent Orders Activity Swiper
const recentOrdersSwiper = new Swiper('.recentOrdersSwiper', {
    slidesPerView: 1,
    spaceBetween: 20,
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    breakpoints: {
        640: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 20,
        },
    },
    loop: true,
    speed: 800,
});
</script>

<style>
/* Hero Image Effects */
.hero-image-wrapper {
    position: relative;
    display: inline-block;
}

.hover-zoom {
    transition: transform 0.5s ease;
}

.hero-image-wrapper:hover .hover-zoom {
    transform: scale(1.05);
}

/* Floating Badges */
.floating-badge {
    animation: float-gentle 3s ease-in-out infinite;
}

.pulse-badge {
    animation: pulse-scale 2s ease-in-out infinite;
}

@keyframes float-gentle {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes pulse-scale {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 10px 40px rgba(255, 193, 7, 0.4);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 15px 50px rgba(255, 193, 7, 0.6);
    }
}

@keyframes slideInLeft {
    from {
        transform: translateX(-100px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideInRight {
    from {
        transform: translateX(100px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideInUp {
    from {
        transform: translateY(100px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes bounceIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes rotateIn {
    from {
        transform: rotate(-180deg) scale(0);
        opacity: 0;
    }
    to {
        transform: rotate(0) scale(1);
        opacity: 1;
    }
}

@keyframes fadeInRight {
    from {
        transform: translateX(50px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Floating Shapes Animation */
.floating-shapes {
    position: absolute;
    width: 100%;
    height: 100%;
}

.shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float-shapes 20s infinite;
}

.shape-1 {
    width: 100px;
    height: 100px;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.shape-2 {
    width: 150px;
    height: 150px;
    top: 60%;
    right: 10%;
    animation-delay: 2s;
}

.shape-3 {
    width: 80px;
    height: 80px;
    bottom: 20%;
    left: 20%;
    animation-delay: 4s;
}

.shape-4 {
    width: 120px;
    height: 120px;
    top: 30%;
    right: 30%;
    animation-delay: 6s;
}

@keyframes float-shapes {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-50px) rotate(180deg);
    }
}

/* Floating Animation */
@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

/* Pulse Animation */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }
}

/* Hover Lift Effect */
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
}

/* Scroll Down Indicator */
.scroll-down-indicator {
    animation: bounce 2s infinite;
    font-size: 1.5rem;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

/* Backdrop Blur */
.backdrop-blur {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* Counter Animation */
.counter-box {
    transition: all 0.3s ease;
}

.counter-box:hover {
    transform: scale(1.1);
}

/* Testimonial Cards */
.testimonial-card {
    transition: all 0.3s ease;
    border-radius: 15px;
}

.testimonial-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
}

/* Category Cards */
.category-card:hover {
    border-color: #667eea !important;
}

.category-image-wrapper img {
    transition: transform 0.5s ease;
}

.category-card:hover .category-image-wrapper img {
    transform: scale(1.1);
}

/* Swiper */
.swiper {
    width: 100%;
    padding: 10px 0 30px 0;
}

.swiper-slide {
    height: auto;
}

/* Product Box */
.product-box {
    height: 100%;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.product-box:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25) !important;
}

.product-image-wrapper {
    position: relative;
    overflow: hidden;
}

.product-image-wrapper img {
    transition: transform 0.5s ease;
}

.product-box:hover .product-image-wrapper img {
    transform: scale(1.15) rotate(2deg);
}

/* Hero Image Wrapper */
.hero-image-wrapper {
    animation: float 6s ease-in-out infinite;
}

.hero-main-image img {
    transition: all 0.3s ease;
}

.hero-main-image:hover img {
    transform: scale(1.05);
}

/* Gradient Text */
.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Z-index */
.z-index-1 {
    z-index: 1;
}

/* Trust Badge Links */
.trust-badge-link {
    transition: all 0.3s ease;
    display: block;
}

.trust-badge-link:hover .hover-lift-sm {
    transform: translateY(-3px);
    background-color: #f8f9fa;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.trust-badge-link:hover i {
    transform: scale(1.1);
}

.trust-badge-link i {
    transition: transform 0.3s ease;
}

.hover-lift-sm {
    transition: all 0.3s ease;
}

/* Smooth Scroll */
html {
    scroll-behavior: smooth;
}

/* Loading Animation */
@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

/* Hero Banner Carousel Styles */
.heroSwiper {
    width: 100%;
    height: 100%;
}

.hero-slide {
    position: relative;
}

.swiper-button-next.hero-next,
.swiper-button-prev.hero-prev {
    color: #fff;
    background: rgba(0, 0, 0, 0.3);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.swiper-button-next.hero-next:after,
.swiper-button-prev.hero-prev:after {
    font-size: 24px;
}

.swiper-button-next.hero-next:hover,
.swiper-button-prev.hero-prev:hover {
    background: rgba(0, 0, 0, 0.6);
    transform: scale(1.1);
}

.swiper-pagination.hero-pagination {
    bottom: 30px !important;
}

.swiper-pagination.hero-pagination .swiper-pagination-bullet {
    width: 14px;
    height: 14px;
    background: #fff;
    opacity: 0.5;
    margin: 0 6px;
    transition: all 0.3s ease;
}

.swiper-pagination.hero-pagination .swiper-pagination-bullet-active {
    opacity: 1;
    width: 40px;
    border-radius: 7px;
    background: #fff;
}

/* Banner Carousel Styles */
.bannerSwiper {
    padding: 0 0 50px 0;
}

.banner-card {
    transition: all 0.3s ease;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.banner-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
}

.swiper-button-next.banner-next,
.swiper-button-prev.banner-prev {
    color: #fff;
    background: rgba(0, 0, 0, 0.3);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    backdrop-filter: blur(10px);
}

.swiper-button-next.banner-next:after,
.swiper-button-prev.banner-prev:after {
    font-size: 20px;
}

.swiper-button-next.banner-next:hover,
.swiper-button-prev.banner-prev:hover {
    background: rgba(0, 0, 0, 0.5);
}

.swiper-pagination.banner-pagination .swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    background: #667eea;
    opacity: 0.5;
}

.swiper-pagination.banner-pagination .swiper-pagination-bullet-active {
    opacity: 1;
    width: 30px;
    border-radius: 6px;
}

/* Countdown Timer Styles */
.countdown-timer {
    animation: fadeInUp 0.8s ease;
}

.countdown-box {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    animation: pulse-box 2s ease-in-out infinite;
}

.countdown-box h3 {
    font-size: 2rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
}

.countdown-box:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

@keyframes pulse-box {
    0%, 100% {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    50% {
        box-shadow: 0 6px 25px rgba(255, 193, 7, 0.4);
    }
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

/* Backdrop Blur Support */
.backdrop-blur {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* Live Activity Section */
.activity-card {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.activity-card:hover {
    transform: translateX(5px);
    background: white !important;
    border-color: #667eea !important;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
}

.activity-icon {
    transition: all 0.3s ease;
}

.activity-card:hover .activity-icon {
    transform: scale(1.1) rotate(5deg);
}

.blink-dot {
    animation: blink 1.5s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

/* Deal Cards */
.deal-card {
    transition: all 0.3s ease;
}

.deal-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3) !important;
}

/* Partner Logos */
.partner-logo {
    transition: all 0.3s ease;
    cursor: pointer;
}

.partner-logo:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.partner-logo i {
    transition: transform 0.3s ease;
}

.partner-logo:hover i {
    transform: scale(1.2);
}

/* Newsletter Card */
.newsletter-card {
    transition: all 0.3s ease;
}

.newsletter-card:hover {
    transform: scale(1.02);
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.4);
}

/* FAQ Accordion */
.accordion-button:not(.collapsed) {
    background-color: #667eea;
    color: white;
}

.accordion-button:not(.collapsed) i {
    color: white !important;
}

.accordion-button {
    transition: all 0.3s ease;
}

.accordion-button:hover {
    background-color: #f8f9fa;
}

.accordion-item {
    border-radius: 10px !important;
    overflow: hidden;
}

/* Trending Product Cards */
.trending-product-card {
    transition: all 0.4s ease;
    cursor: pointer;
}

.trending-product-card:hover {
    transform: translateY(-15px) scale(1.03);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25) !important;
}

.trending-product-card .product-image-wrapper img {
    transition: transform 0.5s ease;
}

.trending-product-card:hover .product-image-wrapper img {
    transform: scale(1.15);
}

/* Review Cards */
.review-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.review-card:hover {
    transform: translateY(-8px);
    border-color: #ffc107;
    box-shadow: 0 10px 30px rgba(255, 193, 7, 0.3) !important;
}

.review-avatar {
    transition: transform 0.3s ease;
}

.review-card:hover .review-avatar {
    transform: rotate(10deg) scale(1.1);
}

/* Video Wrapper */
.video-wrapper button {
    transition: all 0.3s ease;
}

.video-wrapper button:hover {
    transform: scale(1.2);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

/* Stat Boxes */
.stat-box {
    transition: all 0.3s ease;
}

.stat-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Benefit Cards */
.benefit-card {
    transition: all 0.4s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.benefit-card:hover {
    transform: translateY(-15px);
    border-color: #667eea;
    box-shadow: 0 20px 50px rgba(102, 126, 234, 0.3) !important;
}

.benefit-icon {
    transition: all 0.3s ease;
}

.benefit-card:hover .benefit-icon {
    transform: rotate(360deg) scale(1.1);
}

/* Comparison Cards */
.comparison-card {
    transition: all 0.4s ease;
}

.comparison-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2) !important;
}

/* Customer Photo Gallery */
.customer-photo-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.customer-photo-card img {
    transition: transform 0.5s ease;
}

.photo-overlay {
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.customer-photo-card:hover .photo-overlay {
    opacity: 1;
}

.customer-photo-card:hover img {
    transform: scale(1.15);
}

.customer-photo-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
}

/* Timeline Steps */
.timeline-step {
    transition: all 0.3s ease;
}

.timeline-icon {
    transition: all 0.4s ease;
    animation: float-timeline 3s ease-in-out infinite;
}

@keyframes float-timeline {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-15px);
    }
}

.timeline-step:hover .timeline-icon {
    transform: scale(1.2) rotate(360deg);
    animation: none;
}

/* Back to Top Button */
#backToTop {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 1000;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

#backToTop.show {
    opacity: 1;
    visibility: visible;
}

#backToTop:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.6);
}

/* Live Chat Button */
#liveChatBtn {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 1000;
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.5);
    animation: pulse-chat 2s infinite;
}

#liveChatBtn:hover {
    transform: scale(1.1);
    box-shadow: 0 10px 30px rgba(17, 153, 142, 0.7);
}

@keyframes pulse-chat {
    0%, 100% {
        box-shadow: 0 5px 20px rgba(17, 153, 142, 0.5);
    }
    50% {
        box-shadow: 0 5px 30px rgba(17, 153, 142, 0.8);
    }
}

.chat-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4757;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    font-size: 12px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Brand Logo Cards */
.brand-logo-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.brand-logo-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

/* Promo Banners */
.promo-banner {
    transition: all 0.4s ease;
    cursor: pointer;
}

.promo-banner:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3) !important;
}

/* Themed Banners */
.themed-banner {
    transition: all 0.4s ease;
    cursor: pointer;
}

.themed-banner:hover {
    transform: scale(1.02);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4) !important;
}

.themed-banner .btn {
    transition: all 0.3s ease;
}

.themed-banner:hover .btn {
    transform: scale(1.1);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

.themedBannersSwiper .swiper-button-next,
.themedBannersSwiper .swiper-button-prev {
    color: #667eea;
    background: white;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.themedBannersSwiper .swiper-button-next:after,
.themedBannersSwiper .swiper-button-prev:after {
    font-size: 20px;
}

.themedBannersSwiper .swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    background: #667eea;
    opacity: 0.5;
}

.themedBannersSwiper .swiper-pagination-bullet-active {
    opacity: 1;
    background: #667eea;
}

.promo-banner .btn {
    transition: all 0.3s ease;
}

.promo-banner:hover .btn {
    transform: scale(1.1);
}

.full-banner {
    transition: all 0.3s ease;
}

.small-banner {
    transition: all 0.3s ease;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 20px 10px rgba(255, 193, 7, 0);
    }
}

/* Live Chat Widget */
.chat-widget {
    position: fixed;
    bottom: 180px;
    right: 30px;
    width: 350px;
    max-height: 500px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    display: none;
    flex-direction: column;
    z-index: 1001;
    overflow: hidden;
}

.chat-widget.active {
    display: flex;
    animation: slideInUp 0.3s ease;
}

@keyframes slideInUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.chat-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-body {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    max-height: 350px;
    background: #f8f9fa;
}

.chat-message {
    margin-bottom: 15px;
    display: flex;
}

.chat-message.received {
    justify-content: flex-start;
}

.chat-message.sent {
    justify-content: flex-end;
}

.message-bubble {
    max-width: 80%;
    padding: 10px 15px;
    border-radius: 15px;
    background: white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.chat-message.sent .message-bubble {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.message-bubble p {
    margin: 0;
    font-size: 14px;
}

.message-bubble small {
    font-size: 11px;
    opacity: 0.7;
}

.chat-footer {
    padding: 15px;
    background: white;
    border-top: 1px solid #dee2e6;
}

.chat-footer input {
    border-radius: 25px;
}

.chat-footer button {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    padding: 0;
}

@media (max-width: 768px) {
    .chat-widget {
        width: calc(100vw - 30px);
        right: 15px;
        bottom: 120px;
    }
    
    #liveChatBtn {
        bottom: 70px;
        right: 15px;
    }
    
    #backToTop {
        bottom: 20px;
        right: 15px;
    }
}

/* Purchase Notification Popup */
.purchase-notification {
    position: fixed;
    bottom: 30px;
    left: 30px;
    background: white;
    padding: 15px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    z-index: 9999;
    min-width: 320px;
    max-width: 380px;
    transform: translateX(-150%);
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border-left: 4px solid #10b981;
}

.purchase-notification.show {
    transform: translateX(0);
}

.purchase-notification:hover {
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25);
    transform: translateX(0) translateY(-3px);
}

/* Category Quick Cards */
.category-quick-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.category-quick-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15) !important;
    border-color: #667eea;
}

.category-quick-card h6 {
    color: #333;
    transition: color 0.3s ease;
}

.category-quick-card:hover h6 {
    color: #667eea;
}

.category-icon-circle {
    transition: all 0.3s ease;
}

.category-quick-card:hover .category-icon-circle {
    transform: scale(1.1);
}

/* Stats Section */
.stat-card {
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.15) !important;
}

/* Payment Method Cards */
.payment-method-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-method-card:hover {
    transform: translateY(-5px);
    background: #f8f9fa !important;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* Video Testimonial Section */
.video-play-btn {
    transition: all 0.3s ease;
}

.video-play-btn:hover {
    transform: scale(1.1);
    background: rgba(255, 255, 255, 0.35) !important;
}

.quote-card {
    transition: all 0.3s ease;
}

.quote-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15) !important;
}

/* Customer Photo Overlay */
.photo-overlay {
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.customer-photo-card:hover .photo-overlay {
    opacity: 1;
}

.customer-photo-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.customer-photo-card:hover {
    transform: scale(1.05);
}

/* Hover Lift Effects */
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2) !important;
}

.hover-lift-sm {
    transition: all 0.3s ease;
}

.hover-lift-sm:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
}

/* Backdrop Blur Effect */
.backdrop-blur {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* Pulsing Animation for Live Indicators */
@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }
    50% {
        box-shadow: 0 0 25px rgba(16, 185, 129, 0.8);
    }
}

.notification-icon {
    animation: pulse-glow 2s infinite;
}

/* Mobile Responsive Adjustments */
@media (max-width: 768px) {
    .purchase-notification {
        left: 15px;
        right: 15px;
        min-width: auto;
        max-width: calc(100vw - 30px);
        bottom: 120px;
    }
    
    .display-2 {
        font-size: 2.5rem !important;
    }
    
    .floating-card {
        display: none;
    }
    
    .hero-section {
        min-height: auto !important;
        padding: 80px 0 40px 0 !important;
    }
    
    .banner-card {
        min-height: 300px !important;
    }
    
    .banner-card .display-4 {
        font-size: 2rem !important;
    }
    
    .banner-card h3 {
        font-size: 1.3rem !important;
    }
    
    .swiper-button-next.banner-next,
    .swiper-button-prev.banner-prev {
        display: none;
    }
    
    .swiper-button-next.hero-next,
    .swiper-button-prev.hero-prev {
        width: 45px;
        height: 45px;
    }
    
    .swiper-button-next.hero-next:after,
    .swiper-button-prev.hero-prev:after {
        font-size: 18px;
    }
    
    /* Countdown Timer Mobile */
    .countdown-timer {
        padding: 1rem !important;
    }
    
    .countdown-box {
        min-width: 55px !important;
        padding: 0.5rem 0.75rem !important;
    }
    
    .countdown-box h3 {
        font-size: 1.5rem !important;
    }
    
    .countdown-timer .gap-3 {
        gap: 0.5rem !important;
    }
    
    .countdown-timer small {
        font-size: 0.7rem !important;
    }
    
    .countdown-timer .fs-3 {
        font-size: 1.2rem !important;
    }
    
    /* Activity Card Mobile */
    .activity-card {
        flex-direction: column;
        text-align: center;
    }
    
    .activity-card small {
        display: block;
        margin-top: 10px;
    }
    
    /* Hero Floating Badges Mobile */
    .floating-badge {
        font-size: 0.75rem !important;
        padding: 0.5rem 0.75rem !important;
    }
    
    .floating-badge.rounded-circle {
        width: 70px !important;
        height: 70px !important;
        padding: 0.5rem !important;
    }
    
    .floating-badge i {
        font-size: 0.9rem !important;
    }
    
    .hero-image-wrapper .floating-badge {
        transform: scale(0.85);
    }
}
</style>

<script>
// Countdown Timer (USA Eastern Time)
function startCountdown() {
    // Set end date in USA Eastern Time
    // Get current date and add 7 days
    const now = new Date();
    const endDate = new Date(now.getTime() + (7 * 24 * 60 * 60 * 1000));
    
    // Create end time string for USA Eastern Time (midnight)
    // Format: "Oct 30, 2025 23:59:59 GMT-0500" for EST or GMT-0400 for EDT
    const endTime = endDate.toLocaleString('en-US', { 
        timeZone: 'America/New_York',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
    
    const timer = setInterval(function() {
        // Get current time in USA Eastern Time
        const nowET = new Date().toLocaleString('en-US', { timeZone: 'America/New_York' });
        const currentTime = new Date(nowET).getTime();
        const targetTime = endDate.getTime();
        const distance = targetTime - new Date().getTime();
        
        // Calculate time units
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Update display
        const daysEl = document.getElementById('days');
        const hoursEl = document.getElementById('hours');
        const minutesEl = document.getElementById('minutes');
        const secondsEl = document.getElementById('seconds');
        
        if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
        if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
        
        // Check if countdown is finished
        if (distance < 0) {
            clearInterval(timer);
            if (daysEl) daysEl.textContent = '00';
            if (hoursEl) hoursEl.textContent = '00';
            if (minutesEl) minutesEl.textContent = '00';
            if (secondsEl) secondsEl.textContent = '00';
        }
    }, 1000);
}

// Counter Animation
function animateCounters() {
    const counters = document.querySelectorAll('.counter');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;
        
        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target + '+';
            }
        };
        
        updateCounter();
    });
}

// Live Counter Animation for Stats Section
function animateLiveCounters() {
    const liveCounters = document.querySelectorAll('.live-counter');
    
    liveCounters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2500;
        const increment = target / (duration / 16);
        let current = 0;
        
        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target.toLocaleString();
            }
        };
        
        updateCounter();
    });
}

// Real-time Purchase Notification Popup
function showPurchaseNotification() {
    const cities = [
        { city: 'New York, USA', flag: '🇺🇸', product: 'Wireless Headphones' },
        { city: 'London, UK', flag: '🇬🇧', product: 'Smart Watch' },
        { city: 'Toronto, Canada', flag: '🇨🇦', product: 'Running Shoes' },
        { city: 'Sydney, Australia', flag: '🇦🇺', product: 'Laptop Bag' },
        { city: 'Los Angeles, USA', flag: '🇺🇸', product: 'Phone Case' },
        { city: 'Manchester, UK', flag: '🇬🇧', product: 'Bluetooth Speaker' },
        { city: 'Vancouver, Canada', flag: '🇨🇦', product: 'Fitness Tracker' },
        { city: 'Melbourne, Australia', flag: '🇦🇺', product: 'Travel Backpack' }
    ];
    
    function displayNotification() {
        const randomItem = cities[Math.floor(Math.random() * cities.length)];
        const notification = document.createElement('div');
        notification.className = 'purchase-notification';
        notification.innerHTML = `
            <div class="d-flex align-items-center gap-3">
                <div class="notification-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="flex-grow-1">
                    <p class="mb-0 fw-bold small">Someone from <strong>${randomItem.city} ${randomItem.flag}</strong></p>
                    <p class="mb-0 text-muted" style="font-size: 0.8rem;">Just purchased ${randomItem.product}</p>
                </div>
                <button class="btn-close btn-sm" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 500);
        }, 5000);
    }
    
    // Show first notification after 3 seconds
    setTimeout(displayNotification, 3000);
    
    // Then show notifications every 8-15 seconds
    setInterval(displayNotification, Math.random() * 7000 + 8000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });
    
    // Rotating Announcement Messages
    const announcementSlides = document.querySelectorAll('.announcement-slide');
    let currentSlide = 0;
    
    if (announcementSlides.length > 0) {
        console.log('Found ' + announcementSlides.length + ' announcement slides');
        
        // Show first slide immediately
        announcementSlides.forEach((slide, index) => {
            slide.classList.remove('active');
        });
        announcementSlides[0].classList.add('active');
        
        // Rotate slides
        function rotateAnnouncement() {
            announcementSlides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % announcementSlides.length;
            announcementSlides[currentSlide].classList.add('active');
        }
        
        // Start rotation if more than one slide
        if (announcementSlides.length > 1) {
            setInterval(rotateAnnouncement, 4000);
        }
    } else {
        console.log('No announcement slides found!');
    }
    
    startCountdown();
    
    // Animate counters when they come into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.disconnect();
            }
        });
    });
    
    const counterSection = document.querySelector('.stat-card');
    if (counterSection) {
        observer.observe(counterSection);
    }
    
    // Animate live counters when they come into view
    const liveObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateLiveCounters();
                liveObserver.disconnect();
            }
        });
    }, { threshold: 0.3 });
    
    const liveCounterSection = document.querySelector('.live-counter');
    if (liveCounterSection) {
        liveObserver.observe(liveCounterSection);
    }
    
    // Start purchase notifications
    showPurchaseNotification();
    
    // Themed Banners Carousel
    const themedBannersSwiper = new Swiper('.themedBannersSwiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.themed-next',
            prevEl: '.themed-prev',
        },
        pagination: {
            el: '.themed-pagination',
            clickable: true,
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });
    
    // Brands Carousel
    const brandsSwiper = new Swiper('.brandsSwiper', {
        slidesPerView: 2,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 2000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.brands-pagination',
            clickable: true,
        },
        breakpoints: {
            640: { slidesPerView: 3 },
            768: { slidesPerView: 4 },
            1024: { slidesPerView: 6 },
        },
    });
    
    // Flash Sale Countdown
    function startFlashSaleCountdown() {
        const now = new Date().getTime();
        const endTime = now + (6 * 60 * 60 * 1000); // 6 hours
        
        setInterval(function() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            if (document.getElementById('flash-hours')) {
                document.getElementById('flash-hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('flash-minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('flash-seconds').textContent = seconds.toString().padStart(2, '0');
            }
        }, 1000);
    }
    startFlashSaleCountdown();
    
    // Back to Top Button Functionality
    window.addEventListener('scroll', function() {
        const backToTopBtn = document.getElementById('backToTop');
        if (backToTopBtn) {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        }
    });
    
    // Live Chat Widget Functionality
    const chatWidget = document.getElementById('chatWidget');
    const liveChatBtn = document.getElementById('liveChatBtn');
    const closeChatBtn = document.getElementById('closeChatBtn');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatBody = document.getElementById('chatBody');
    const chatBadge = document.querySelector('.chat-badge');
    
    // Toggle chat widget
    if (liveChatBtn) {
        liveChatBtn.addEventListener('click', function() {
            chatWidget.classList.toggle('active');
            if (chatWidget.classList.contains('active')) {
                chatInput.focus();
                if (chatBadge) chatBadge.style.display = 'none';
            }
        });
    }
    
    // Close chat widget
    if (closeChatBtn) {
        closeChatBtn.addEventListener('click', function() {
            chatWidget.classList.remove('active');
        });
    }
    
    // Handle chat form submission
    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = chatInput.value.trim();
            
            if (message) {
                // Add user message
                const userMessage = document.createElement('div');
                userMessage.className = 'chat-message sent';
                userMessage.innerHTML = `
                    <div class="message-bubble">
                        <p>${message}</p>
                        <small class="text-white">Just now</small>
                    </div>
                `;
                chatBody.appendChild(userMessage);
                chatInput.value = '';
                
                // Scroll to bottom
                chatBody.scrollTop = chatBody.scrollHeight;
                
                // Simulate bot response after 1 second
                setTimeout(function() {
                    const botResponses = [
                        "Thank you for your message! Our team will respond shortly.",
                        "We've received your inquiry. An agent will be with you in a moment.",
                        "Thanks for reaching out! How else can we help you?",
                        "Got it! Is there anything else you'd like to know?",
                        "Great question! Let me connect you with a specialist."
                    ];
                    const randomResponse = botResponses[Math.floor(Math.random() * botResponses.length)];
                    
                    const botMessage = document.createElement('div');
                    botMessage.className = 'chat-message received';
                    botMessage.innerHTML = `
                        <div class="message-bubble">
                            <p>${randomResponse}</p>
                            <small class="text-muted">Just now</small>
                        </div>
                    `;
                    chatBody.appendChild(botMessage);
                    chatBody.scrollTop = chatBody.scrollHeight;
                }, 1000);
            }
        });
    }
    
    // Newsletter Form Handler
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = document.getElementById('newsletterEmail');
            const email = emailInput.value.trim();
            
            if (email) {
                // Show success message
                const btn = this.querySelector('button[type="submit"]');
                const originalContent = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Subscribed!';
                btn.classList.remove('btn-warning');
                btn.classList.add('btn-success');
                btn.disabled = true;
                
                // Reset form after 3 seconds
                setTimeout(() => {
                    emailInput.value = '';
                    btn.innerHTML = originalContent;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-warning');
                    btn.disabled = false;
                }, 3000);
                
                // Here you would typically send the email to your backend
                console.log('Newsletter subscription:', email);
            }
        });
    }
});
</script>

<!-- Back to Top Button -->
<button id="backToTop" title="Back to Top">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Live Chat Button -->
<button id="liveChatBtn" title="Live Chat">
    <i class="fas fa-comments"></i>
    <span class="chat-badge">1</span>
</button>

<!-- Live Chat Widget -->
<div id="chatWidget" class="chat-widget">
    <div class="chat-header">
        <div class="d-flex align-items-center">
            <div class="chat-avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                <i class="fas fa-headset"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold">Customer Support</h6>
                <small class="text-success"><i class="fas fa-circle" style="font-size: 8px;"></i> Online</small>
            </div>
        </div>
        <button id="closeChatBtn" class="btn-close btn-close-white"></button>
    </div>
    
    <div class="chat-body" id="chatBody">
        <div class="chat-message received">
            <div class="message-bubble">
                <p class="mb-1">👋 Hello! Welcome to Rangpur Food!</p>
                <p class="mb-0">How can we help you today?</p>
                <small class="text-muted">Just now</small>
            </div>
        </div>
    </div>
    
    <div class="chat-footer">
        <form id="chatForm" class="d-flex gap-2">
            <input type="text" id="chatInput" class="form-control" placeholder="Type your message..." autocomplete="off">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
