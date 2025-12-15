<?php
require_once 'config/config.php';

$db = Database::getInstance();

// Check if reset_token column exists
$columns = $db->query("SHOW COLUMNS FROM users LIKE 'reset_token'")->num_rows;

if ($columns > 0) {
    echo "✅ Password reset columns already exist!<br>";
    echo "The password reset system is ready to use.";
} else {
    // Add password reset columns
    $sql1 = "ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL AFTER password";
    $sql2 = "ALTER TABLE users ADD COLUMN reset_token_expiry DATETIME NULL AFTER reset_token";
    
    if ($db->query($sql1) && $db->query($sql2)) {
        echo "✅ Success! Password reset columns have been added.<br>";
        echo "You can now use the forgot password feature!";
    } else {
        echo "❌ Error adding password reset columns: " . $db->getConnection()->error;
    }
}

echo "<br><br>";
echo "<a href='forgot-password.php'>Test Forgot Password</a> | ";
echo "<a href='login.php'>Go to Login</a>";
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
