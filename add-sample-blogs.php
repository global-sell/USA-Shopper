<?php
require_once 'config/config.php';

$db = Database::getInstance();

// Check if blogs table exists
$tableCheck = $db->query("SHOW TABLES LIKE 'blogs'");
if ($tableCheck->num_rows == 0) {
    echo "❌ Error: Blogs table doesn't exist. Please run setup-blogs.php first.<br>";
    echo "<a href='setup-blogs.php'>Setup Blogs Table</a>";
    exit;
}

// Sample blog posts
$sampleBlogs = [
    [
        'title' => 'Top 10 Shopping Tips for Smart Buyers in 2025',
        'slug' => 'top-10-shopping-tips-for-smart-buyers-in-2025',
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

<p>By following these tips, you\'ll become a more informed and confident shopper, saving both time and money while getting the products you truly want.</p>',
        'author' => 'Sarah Johnson',
        'category' => 'Shopping Guide',
        'tags' => 'shopping, tips, smart buying, online shopping',
        'status' => 'published'
    ],
    [
        'title' => 'How to Choose the Perfect Electronics for Your Home',
        'slug' => 'how-to-choose-the-perfect-electronics-for-your-home',
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

<p>Making smart choices when buying electronics ensures you get the best value for your money and products that truly enhance your daily life.</p>',
        'author' => 'Mike Chen',
        'category' => 'Electronics',
        'tags' => 'electronics, home, gadgets, technology',
        'status' => 'published'
    ],
    [
        'title' => 'Sustainable Shopping: Eco-Friendly Products You Need',
        'slug' => 'sustainable-shopping-eco-friendly-products-you-need',
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

<p>Together, we can make a difference through conscious shopping decisions that benefit both our planet and future generations.</p>',
        'author' => 'Emma Green',
        'category' => 'Sustainability',
        'tags' => 'sustainability, eco-friendly, green living, environment',
        'status' => 'published'
    ],
    [
        'title' => 'Fashion Trends 2025: What\'s Hot This Season',
        'slug' => 'fashion-trends-2025-whats-hot-this-season',
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

<p>Stay fashionable while expressing your unique personality with these trending styles.</p>',
        'author' => 'Lisa Martinez',
        'category' => 'Fashion',
        'tags' => 'fashion, trends, style, clothing',
        'status' => 'published'
    ],
    [
        'title' => 'Home Organization Hacks That Actually Work',
        'slug' => 'home-organization-hacks-that-actually-work',
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

<p>With these simple hacks, you can create a more organized, peaceful living environment.</p>',
        'author' => 'David Wilson',
        'category' => 'Home & Living',
        'tags' => 'home, organization, declutter, storage',
        'status' => 'published'
    ],
    [
        'title' => 'The Ultimate Gift Guide for Every Occasion',
        'slug' => 'the-ultimate-gift-guide-for-every-occasion',
        'excerpt' => 'Find the perfect gift for your loved ones with our curated selection of thoughtful and unique presents.',
        'content' => '<p>Gift-giving doesn\'t have to be stressful. This comprehensive guide will help you find the perfect present for any occasion.</p>

<h3>For the Tech Enthusiast</h3>
<p>Latest gadgets, smart home devices, and innovative tech accessories always make great gifts for technology lovers.</p>

<h3>For the Homebody</h3>
<p>Cozy blankets, scented candles, premium coffee or tea sets, and comfortable loungewear are perfect for those who love staying home.</p>

<h3>For the Fitness Fanatic</h3>
<p>Workout equipment, fitness trackers, athletic wear, and healthy meal prep containers support their active lifestyle.</p>

<h3>Personalized Gifts</h3>
<p>Custom items with names, photos, or special messages add a personal touch that shows you care.</p>

<p>Remember, the best gifts come from the heart and show that you truly know and appreciate the recipient.</p>',
        'author' => 'Rachel Adams',
        'category' => 'Gift Ideas',
        'tags' => 'gifts, shopping, occasions, ideas',
        'status' => 'published'
    ]
];

$added = 0;
$skipped = 0;

foreach ($sampleBlogs as $blog) {
    // Check if blog with same slug already exists
    $stmt = $db->prepare("SELECT id FROM blogs WHERE slug = ?");
    $stmt->bind_param("s", $blog['slug']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $skipped++;
        continue;
    }
    
    // Insert blog
    $stmt = $db->prepare("INSERT INTO blogs (title, slug, excerpt, content, author, category, tags, status) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", 
        $blog['title'], 
        $blog['slug'], 
        $blog['excerpt'], 
        $blog['content'], 
        $blog['author'], 
        $blog['category'], 
        $blog['tags'], 
        $blog['status']
    );
    
    if ($stmt->execute()) {
        $added++;
    }
}

echo "<div style='font-family: Arial; padding: 40px; background: #f5f5f5;'>";
echo "<div style='background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto;'>";
echo "<h2 style='color: #28a745;'>✅ Sample Blogs Added Successfully!</h2>";
echo "<p><strong>$added</strong> new blog posts have been added to your database.</p>";
if ($skipped > 0) {
    echo "<p><strong>$skipped</strong> blog posts were skipped (already exist).</p>";
}
echo "<hr>";
echo "<h3>What's Next?</h3>";
echo "<ul>";
echo "<li><a href='blogs.php'>View Blog Page</a> - See your new blog posts</li>";
echo "<li><a href='admin/blogs.php'>Manage Blogs</a> - Edit or delete posts</li>";
echo "<li><a href='admin/add-blog.php'>Add More Blogs</a> - Create your own content</li>";
echo "</ul>";
echo "</div>";
echo "</div>";
?>

<style>
a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    text-decoration: underline;
}
ul {
    line-height: 2;
}
</style>
