<?php
require_once 'config/config.php';

$db = Database::getInstance();

// Check if icon column exists
$result = $db->query("SHOW COLUMNS FROM categories LIKE 'icon'");
$columnExists = $result->num_rows > 0;

if ($columnExists) {
    echo "✅ Icon column already exists in categories table!";
} else {
    // Add the icon column
    $sql = "ALTER TABLE categories ADD COLUMN icon VARCHAR(255) NULL AFTER description";
    
    if ($db->query($sql)) {
        echo "✅ Success! Icon column has been added to categories table.<br>";
        echo "You can now upload category icons from the admin panel.";
    } else {
        echo "❌ Error adding icon column: " . $db->getConnection()->error;
    }
}

echo "<br><br>";
echo "<a href='admin/categories.php'>Go to Categories Admin</a> | ";
echo "<a href='index.php'>Go to Homepage</a>";
?>
