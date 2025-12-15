<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    die('Unauthorized access');
}

$token = sanitizeInput($_GET['token'] ?? '');

if (empty($token)) {
    die('Invalid download token');
}

$db = Database::getInstance();

// Get download info
$stmt = $db->prepare("SELECT d.*, p.file_path, p.title 
                     FROM downloads d 
                     JOIN products p ON d.product_id = p.id 
                     WHERE d.download_token = ? AND d.user_id = ?");
$stmt->bind_param("si", $token, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($download = $result->fetch_assoc()) {
    // Check if download limit reached
    if ($download['download_count'] >= $download['max_downloads']) {
        die('Download limit reached. Please contact support for assistance.');
    }
    
    // Check if expired
    if ($download['expires_at'] && strtotime($download['expires_at']) < time()) {
        die('Download link has expired. Please contact support for assistance.');
    }
    
    $filePath = PRODUCTS_PATH . '/' . $download['file_path'];
    
    if (file_exists($filePath)) {
        // Update download count
        $db->query("UPDATE downloads SET download_count = download_count + 1 WHERE id = " . $download['id']);
        
        // Set headers for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($download['file_path']) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: public');
        
        // Clear output buffer
        ob_clean();
        flush();
        
        // Read and output file
        readfile($filePath);
        exit;
    } else {
        die('File not found. Please contact support.');
    }
} else {
    die('Invalid download token');
}
?>
