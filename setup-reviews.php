<?php
require_once 'config/config.php';

$db = Database::getInstance();

// Create reviews table
$sql = "CREATE TABLE IF NOT EXISTS product_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_title VARCHAR(255),
    review_text TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($db->query($sql)) {
    echo "✅ Success! Product reviews table created successfully.<br>";
    echo "Users can now leave reviews on products!<br><br>";
    echo "<a href='products.php'>Browse Products</a> | <a href='index.php'>Go to Homepage</a>";
} else {
    echo "❌ Error creating reviews table: " . $db->getConnection()->error;
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 40px;
    background: #f5f5f5;
}
a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    text-decoration: underline;
}
</style>
