<?php
require_once 'config/config.php';

$db = Database::getInstance();

// Check if newsletter table exists
$tableCheck = $db->query("SHOW TABLES LIKE 'newsletter_subscribers'");
if ($tableCheck->num_rows > 0) {
    echo "✅ Newsletter subscribers table already exists!";
} else {
    // Create the newsletter_subscribers table
    $sql = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        name VARCHAR(100) NULL,
        status ENUM('active', 'unsubscribed') DEFAULT 'active',
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        unsubscribed_at TIMESTAMP NULL,
        ip_address VARCHAR(45) NULL,
        INDEX idx_email (email),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($db->query($sql)) {
        echo "✅ Success! Newsletter subscribers table has been created.<br>";
        echo "You can now accept newsletter subscriptions!";
    } else {
        echo "❌ Error creating newsletter table: " . $db->getConnection()->error;
    }
}

echo "<br><br>";
echo "<a href='index.php'>Go to Homepage</a> | ";
echo "<a href='admin/newsletter.php'>Manage Subscribers</a>";
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
