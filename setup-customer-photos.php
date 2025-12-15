<?php
require_once 'config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    die('Access denied. Admin only.');
}

$db = Database::getInstance();

// Create customer_photos table
$sql = "CREATE TABLE IF NOT EXISTS `customer_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `caption` text DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

try {
    if ($db->query($sql)) {
        echo "<h2>Success!</h2>";
        echo "<p>Customer photos table created successfully.</p>";
        
        // Create upload directory
        $upload_dir = __DIR__ . '/uploads/customer-photos/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
            echo "<p>Upload directory created: /uploads/customer-photos/</p>";
        } else {
            echo "<p>Upload directory already exists.</p>";
        }
        
        echo "<p><a href='" . SITE_URL . "'>Go to Home</a></p>";
    } else {
        echo "<h2>Error!</h2>";
        echo "<p>Failed to create table. Please check the SQL syntax.</p>";
    }
} catch (Exception $e) {
    echo "<h2>Error!</h2>";
    echo "<p>Failed to create table: " . $e->getMessage() . "</p>";
}
?>
