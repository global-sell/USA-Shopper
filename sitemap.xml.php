<?php
// Turn off all error reporting (XML break prevent)
error_reporting(0);
ini_set('display_errors', 0);

// Set content type
header("Content-Type: application/xml; charset=utf-8");

// Database credentials (InfinityFree)
define('DB_HOST', 'sql307.infinityfree.com');
define('DB_USER', 'if0_39879936');
define('DB_PASS', 'Surjo253692');
define('DB_NAME', 'if0_39879936_ybt_digital');

// Connect to database
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    // If DB connection fails, return minimal XML
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    echo '<url><loc>https://us.usashopper.site/</loc></url>';
    echo '</urlset>';
    exit;
}

// Domain name
$domain = "https://us.usashopper.site";

// Start XML
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Home
echo "<url>
    <loc>{$domain}/</loc>
    <priority>1.0</priority>
    <changefreq>daily</changefreq>
</url>";

// Blog links
$blogs = $conn->query("SELECT slug, updated_at FROM blogs ORDER BY id DESC");
if ($blogs) {
    while ($row = $blogs->fetch_assoc()) {
        $slug = htmlspecialchars($row['slug']);
        $lastmod = !empty($row['updated_at']) ? date('Y-m-d', strtotime($row['updated_at'])) : date('Y-m-d');
        echo "<url>
            <loc>{$domain}/blog/{$slug}</loc>
            <lastmod>{$lastmod}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>";
    }
}

// Product links
$products = $conn->query("SELECT slug, updated_at FROM products ORDER BY id DESC");
if ($products) {
    while ($row = $products->fetch_assoc()) {
        $slug = htmlspecialchars($row['slug']);
        $lastmod = !empty($row['updated_at']) ? date('Y-m-d', strtotime($row['updated_at'])) : date('Y-m-d');
        echo "<url>
            <loc>{$domain}/{$slug}</loc>
            <lastmod>{$lastmod}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>";
    }
}

// End XML
echo '</urlset>';

$conn->close();
?>
