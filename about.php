<?php
$pageTitle = "About Us";
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 60vh; display: flex; align-items: center; position: relative; overflow: hidden;">
    <!-- Animated Background Shapes -->
    <div class="position-absolute w-100 h-100" style="top: 0; left: 0; opacity: 0.1;">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>
    
    <div class="container text-center text-white position-relative">
        <div class="animate__animated animate__fadeInDown">
            <span class="badge bg-warning text-dark px-4 py-2 mb-3" style="font-size: 1rem;">
                <i class="fas fa-globe me-2"></i>SERVING 100+ COUNTRIES WORLDWIDE
            </span>
            <h1 class="display-2 fw-bold mb-4">About US Shopper</h1>
            <p class="lead mb-5" style="font-size: 1.4rem; max-width: 800px; margin: 0 auto;">Your trusted global destination for quality products, exceptional service, and worldwide shipping</p>
            <div class="row g-4 justify-content-center">
                <div class="col-md-3 col-6">
                    <div class="bg-white bg-opacity-10 backdrop-blur rounded-4 p-4">
                        <i class="fas fa-users fa-3x mb-3 d-block"></i>
                        <h3 class="fw-bold mb-1">50K+</h3>
                        <p class="mb-0 small">Global Customers</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-white bg-opacity-10 backdrop-blur rounded-4 p-4">
                        <i class="fas fa-box fa-3x mb-3 d-block"></i>
                        <h3 class="fw-bold mb-1">10K+</h3>
                        <p class="mb-0 small">Products</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-white bg-opacity-10 backdrop-blur rounded-4 p-4">
                        <i class="fas fa-globe-americas fa-3x mb-3 d-block"></i>
                        <h3 class="fw-bold mb-1">100+</h3>
                        <p class="mb-0 small">Countries</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-white bg-opacity-10 backdrop-blur rounded-4 p-4">
                        <i class="fas fa-star fa-3x mb-3 d-block"></i>
                        <h3 class="fw-bold mb-1">4.8/5</h3>
                        <p class="mb-0 small">Rating</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- International Trust Bar -->
<section class="py-3 bg-light border-bottom">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-6 col-md-3">
                <small class="fw-bold text-muted">
                    <i class="fas fa-shipping-fast text-primary me-2"></i>Free Shipping $50+
                </small>
            </div>
            <div class="col-6 col-md-3">
                <small class="fw-bold text-muted">
                    <i class="fas fa-shield-alt text-success me-2"></i>SSL Encrypted
                </small>
            </div>
            <div class="col-6 col-md-3">
                <small class="fw-bold text-muted">
                    <i class="fas fa-undo text-warning me-2"></i>30-Day Returns
                </small>
            </div>
            <div class="col-6 col-md-3">
                <small class="fw-bold text-muted">
                    <i class="fas fa-headset text-info me-2"></i>24/7 Support
                </small>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    
    <!-- Our Story -->
    <div class="row align-items-center mb-5 animate__animated animate__fadeInUp">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="position-relative">
                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&h=400&fit=crop" 
                     alt="Our Story" class="img-fluid rounded-4 shadow-lg" style="transition: transform 0.3s ease; object-fit: cover; height: 400px; width: 100%;" 
                     onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"
                     onerror="this.src='https://placehold.co/600x400/667eea/ffffff?text=Our+Story'">
                <div class="position-absolute top-0 start-0 w-100 h-100 rounded-4" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <h2 class="fw-bold mb-4">Our Story</h2>
            <p class="mb-3">
                Founded with a vision to revolutionize online shopping in the USA, US Shopper has grown to become 
                one of the most trusted e-commerce platforms. We started with a simple mission: to provide customers 
                with access to quality products at competitive prices, backed by exceptional customer service.
            </p>
            <p class="mb-3">
                Today, we serve thousands of satisfied customers across the United States, offering everything from 
                electronics and fashion to home goods and more. Our commitment to quality, authenticity, and customer 
                satisfaction remains at the heart of everything we do.
            </p>
            <p>
                We believe shopping should be easy, enjoyable, and trustworthy. That's why we've built a platform 
                that prioritizes user experience, secure transactions, and fast delivery.
            </p>
        </div>
    </div>
    
    <!-- Company Timeline -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Our Journey</h2>
                <p class="lead text-muted">From local startup to global e-commerce leader</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-rocket fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">2020</h5>
                        <p class="text-muted small">Founded in USA with a vision to revolutionize online shopping</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                        <h5 class="fw-bold">2021</h5>
                        <p class="text-muted small">Reached 10,000+ satisfied customers across North America</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-globe fa-2x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">2023</h5>
                        <p class="text-muted small">Expanded to 100+ countries with international shipping</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-trophy fa-2x text-info"></i>
                        </div>
                        <h5 class="fw-bold">2025</h5>
                        <p class="text-muted small">Recognized as a leading global e-commerce platform</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Our Mission -->
    <div class="row align-items-center mb-5 flex-lg-row-reverse animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="position-relative">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=400&fit=crop" 
                     alt="Our Mission" class="img-fluid rounded-4 shadow-lg" style="transition: transform 0.3s ease; object-fit: cover; height: 400px; width: 100%;" 
                     onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"
                     onerror="this.src='https://placehold.co/600x400/4CAF50/ffffff?text=Our+Mission'">
                <div class="position-absolute top-0 start-0 w-100 h-100 rounded-4" style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(56, 142, 60, 0.1) 100%);"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <h2 class="fw-bold mb-4">Our Mission</h2>
            <p class="mb-3">
                Our mission is to empower customers with choice, convenience, and confidence. We strive to:
            </p>
            <ul class="list-unstyled">
                <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Offer a wide selection of authentic, high-quality products</li>
                <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Provide competitive prices and exclusive deals</li>
                <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Ensure fast and reliable shipping to 100+ countries</li>
                <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Deliver exceptional multilingual customer support</li>
                <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Create a seamless and secure global shopping experience</li>
                <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Support international payment methods and currencies</li>
            </ul>
        </div>
    </div>
    
    <!-- Why Choose Us -->
    <div class="text-center mb-5 animate__animated animate__fadeIn">
        <h2 class="display-5 fw-bold mb-3">Why Choose US Shopper?</h2>
        <p class="lead text-muted mb-5">We're more than just an online store – we're your shopping partner</p>
        <div class="mx-auto" style="width: 100px; height: 4px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></div>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 text-center border-0 shadow-sm animate__animated animate__fadeInUp" 
                 style="border-radius: 15px; transition: all 0.3s ease; animation-delay: 0.1s;" 
                 onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)'" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.08)'">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="d-inline-block p-3 rounded-circle" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3">Secure Shopping</h5>
                    <p class="text-muted small mb-0">100% secure payment processing and data protection</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 text-center border-0 shadow-sm animate__animated animate__fadeInUp" 
                 style="border-radius: 15px; transition: all 0.3s ease; animation-delay: 0.2s;" 
                 onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)'" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.08)'">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="d-inline-block p-3 rounded-circle" style="background: rgba(76, 175, 80, 0.1);">
                            <i class="fas fa-shipping-fast fa-3x text-success"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3">Fast Delivery</h5>
                    <p class="text-muted small mb-0">Quick shipping across all 50 states</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 text-center border-0 shadow-sm animate__animated animate__fadeInUp" 
                 style="border-radius: 15px; transition: all 0.3s ease; animation-delay: 0.3s;" 
                 onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)'" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.08)'">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="d-inline-block p-3 rounded-circle" style="background: rgba(255, 152, 0, 0.1);">
                            <i class="fas fa-undo fa-3x text-warning"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3">Easy Returns</h5>
                    <p class="text-muted small mb-0">30-day hassle-free return policy</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 text-center border-0 shadow-sm animate__animated animate__fadeInUp" 
                 style="border-radius: 15px; transition: all 0.3s ease; animation-delay: 0.4s;" 
                 onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)'" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.08)'">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="d-inline-block p-3 rounded-circle" style="background: rgba(23, 162, 184, 0.1);">
                            <i class="fas fa-headset fa-3x text-info"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3">24/7 Support</h5>
                    <p class="text-muted small mb-0">Always here to help you</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- International Certifications -->
    <div id="security" class="card mb-5 border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-5">
            <h2 class="display-6 fw-bold text-center mb-4">International Certifications & Security</h2>
            <div class="row g-4 text-center">
                <div class="col-md-2 col-4">
                    <div class="p-3">
                        <i class="fas fa-certificate fa-3x text-primary mb-2"></i>
                        <p class="small fw-bold mb-0">PCI DSS</p>
                        <small class="text-muted">Certified</small>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-3">
                        <i class="fas fa-shield-alt fa-3x text-success mb-2"></i>
                        <p class="small fw-bold mb-0">SSL</p>
                        <small class="text-muted">256-bit</small>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-3">
                        <i class="fas fa-lock fa-3x text-warning mb-2"></i>
                        <p class="small fw-bold mb-0">GDPR</p>
                        <small class="text-muted">Compliant</small>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-3">
                        <i class="fas fa-check-circle fa-3x text-info mb-2"></i>
                        <p class="small fw-bold mb-0">Verified</p>
                        <small class="text-muted">Business</small>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-3">
                        <i class="fas fa-globe fa-3x text-danger mb-2"></i>
                        <p class="small fw-bold mb-0">ISO</p>
                        <small class="text-muted">Certified</small>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-3">
                        <i class="fas fa-star fa-3x text-warning mb-2"></i>
                        <p class="small fw-bold mb-0">BBB</p>
                        <small class="text-muted">A+ Rated</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Returns Policy -->
    <div id="returns" class="card mb-5 border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, rgba(76, 175, 80, 0.05) 0%, rgba(56, 142, 60, 0.05) 100%);">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="fas fa-undo fa-4x text-success mb-3"></i>
                <h2 class="display-6 fw-bold">30-Day Money Back Guarantee</h2>
                <p class="lead text-muted">Shop with confidence - We've got you covered worldwide</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold"><i class="fas fa-check text-success me-2"></i>Easy Returns</h6>
                    <p class="text-muted small">Return any product within 30 days, no questions asked</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold"><i class="fas fa-check text-success me-2"></i>Free Return Shipping</h6>
                    <p class="text-muted small">We cover return shipping costs for defective items</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold"><i class="fas fa-check text-success me-2"></i>Quick Refunds</h6>
                    <p class="text-muted small">Refunds processed within 5-7 business days</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Our Values -->
    <div class="card mb-5 border-0 shadow-lg animate__animated animate__fadeInUp" style="border-radius: 20px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);">
        <div class="card-body p-5">
            <h2 class="display-6 fw-bold text-center mb-2">Our Core Values</h2>
            <div class="text-center mb-4">
                <div class="mx-auto" style="width: 80px; height: 3px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5 class="fw-bold"><i class="fas fa-heart text-danger me-2"></i>Customer First</h5>
                    <p class="text-muted">Your satisfaction is our top priority. We listen, adapt, and continuously improve.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="fw-bold"><i class="fas fa-star text-warning me-2"></i>Quality Assurance</h5>
                    <p class="text-muted">We partner only with trusted brands and verify every product we sell.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="fw-bold"><i class="fas fa-handshake text-primary me-2"></i>Trust & Transparency</h5>
                    <p class="text-muted">Honest pricing, clear policies, and open communication always.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="row text-center mb-5 g-4">
        <div class="col-md-3 col-6">
            <div class="p-4 rounded-4 animate__animated animate__zoomIn" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); animation-delay: 0.1s;">
                <h2 class="fw-bold text-primary display-4 mb-2">50K+</h2>
                <p class="text-muted mb-0 fw-500">Happy Customers</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-4 rounded-4 animate__animated animate__zoomIn" style="background: rgba(76, 175, 80, 0.1); animation-delay: 0.2s;">
                <h2 class="fw-bold text-success display-4 mb-2">100K+</h2>
                <p class="text-muted mb-0 fw-500">Products Sold</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-4 rounded-4 animate__animated animate__zoomIn" style="background: rgba(255, 152, 0, 0.1); animation-delay: 0.3s;">
                <h2 class="fw-bold text-warning display-4 mb-2">10K+</h2>
                <p class="text-muted mb-0 fw-500">Products Available</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-4 rounded-4 animate__animated animate__zoomIn" style="background: rgba(23, 162, 184, 0.1); animation-delay: 0.4s;">
                <h2 class="fw-bold text-info display-4 mb-2">4.8/5</h2>
                <p class="text-muted mb-0 fw-500">Average Rating</p>
            </div>
        </div>
    </div>
    
    <!-- CTA -->
    <div class="text-center py-5 px-4 animate__animated animate__fadeInUp" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);">
        <h2 class="text-white display-5 fw-bold mb-3">Ready to Start Shopping?</h2>
        <p class="text-white mb-4 lead">Join thousands of satisfied customers today!</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-light btn-lg" style="border-radius: 25px; padding: 15px 40px; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 25px rgba(255,255,255,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <i class="fas fa-shopping-bag me-2"></i>Browse Products
            </a>
            <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-outline-light btn-lg" style="border-radius: 25px; padding: 15px 40px; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.background='transparent'">
                <i class="fas fa-envelope me-2"></i>Contact Us
            </a>
        </div>
    </div>
</div>

<!-- Add Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
.animate__animated {
    animation-duration: 0.8s;
}

.badge {
    font-size: 1rem;
    font-weight: 500;
}

.display-2, .display-3 {
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

h2, h5 {
    color: #2c3e50;
}

.fw-500 {
    font-weight: 500;
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

@keyframes float-shapes {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-50px) rotate(180deg);
    }
}

/* Backdrop Blur */
.backdrop-blur {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* Smooth Transitions */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

/* Responsive */
@media (max-width: 768px) {
    .display-2 {
        font-size: 2.5rem !important;
    }
    
    .shape {
        display: none;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
