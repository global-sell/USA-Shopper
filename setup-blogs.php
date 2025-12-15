<?php
require_once 'config/config.php';

$db = Database::getInstance();

// Check if blogs table exists
$result = $db->query("SHOW TABLES LIKE 'blogs'");
$tableExists = $result->num_rows > 0;

if ($tableExists) {
    echo "✅ Blogs table already exists!<br>";
    echo "You can start adding blog posts.";
} else {
    // Create the blogs table
    $sql = "CREATE TABLE IF NOT EXISTS blogs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        excerpt TEXT,
        content LONGTEXT NOT NULL,
        featured_image VARCHAR(255),
        author VARCHAR(100),
        category VARCHAR(100),
        tags VARCHAR(255),
        status ENUM('draft', 'published') DEFAULT 'draft',
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_slug (slug),
        INDEX idx_status (status),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($db->query($sql)) {
        echo "✅ Success! Blogs table has been created.<br>";
        echo "You can now add blog posts from the admin panel.";
    } else {
        echo "❌ Error creating blogs table: " . $db->getConnection()->error;
    }
}

echo "<br><br>";
echo "<a href='admin/add-blog.php' class='btn btn-primary'>Add New Blog Post</a> | ";
echo "<a href='admin/blogs.php' class='btn btn-secondary'>View All Blogs</a> | ";
echo "<a href='blogs.php' class='btn btn-info'>View Blog Page</a>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 40px;
    background: #f5f5f5;
}
.btn {
    display: inline-block;
    padding: 10px 20px;
    margin: 5px;
    text-decoration: none;
    border-radius: 5px;
    color: white;
}
.btn-primary { background: #007bff; }
.btn-secondary { background: #6c757d; }
.btn-info { background: #17a2b8; }
.btn:hover { opacity: 0.8; }
</style>
