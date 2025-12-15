<?php
$pageTitle = "Upload Your Photo";
require_once __DIR__ . '/config/config.php';

// Check if user is logged in BEFORE any output
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ' . SITE_URL . '/login.php');
    exit();
}

// Now include header after redirect check
require_once __DIR__ . '/includes/header.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['customer_photo'])) {
    $file = $_FILES['customer_photo'];
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] === 0) {
        if (in_array($file['type'], $allowed_types)) {
            if ($file['size'] <= $max_size) {
                // Create upload directory if it doesn't exist
                $upload_dir = __DIR__ . '/uploads/customer-photos/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'customer_' . time() . '_' . uniqid() . '.' . $extension;
                $destination = $upload_dir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    // Save to database
                    $db = Database::getInstance();
                    $user_id = $_SESSION['user_id'];
                    $caption = $_POST['caption'] ?? '';
                    $username = $_POST['username'] ?? '';
                    
                    $stmt = $db->prepare("INSERT INTO customer_photos (user_id, filename, caption, username, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
                    $stmt->bind_param("isss", $user_id, $filename, $caption, $username);
                    
                    if ($stmt->execute()) {
                        $success = true;
                    } else {
                        $error = 'Failed to save photo information.';
                        unlink($destination);
                    }
                } else {
                    $error = 'Failed to upload file.';
                }
            } else {
                $error = 'File size must be less than 5MB.';
            }
        } else {
            $error = 'Only JPG, PNG, GIF, and WEBP images are allowed.';
        }
    } else {
        $error = 'Upload error occurred.';
    }
}
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-camera fa-3x text-primary"></i>
                            </div>
                            <h2 class="fw-bold mb-2">Upload Your Photo</h2>
                            <p class="text-muted">Share your shopping experience with our community!</p>
                        </div>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Thank you!</strong> Your photo has been uploaded successfully and is pending review.
                                <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                            </div>
                            <div class="text-center">
                                <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>Back to Home
                                </a>
                            </div>
                        <?php else: ?>
                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php echo htmlspecialchars($error); ?>
                                    <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST" enctype="multipart/form-data" id="photoUploadForm">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Choose Your Photo *</label>
                                    <div class="upload-area border-2 border-dashed rounded-3 p-5 text-center" id="uploadArea" style="border-color: #dee2e6; cursor: pointer; transition: all 0.3s;">
                                        <input type="file" name="customer_photo" id="customerPhoto" accept="image/*" required class="d-none">
                                        <div id="uploadPrompt">
                                            <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-3"></i>
                                            <p class="mb-2"><strong>Click to upload</strong> or drag and drop</p>
                                            <p class="text-muted small">JPG, PNG, GIF, WEBP (Max 5MB)</p>
                                        </div>
                                        <div id="imagePreview" class="d-none">
                                            <img src="" alt="Preview" class="img-fluid rounded-3 mb-3" style="max-height: 300px;">
                                            <p class="text-success"><i class="fas fa-check-circle me-2"></i>Photo selected</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="username" class="form-label fw-bold">Your Username (Optional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="e.g., sarah_m">
                                    </div>
                                    <small class="text-muted">This will be displayed with your photo</small>
                                </div>

                                <div class="mb-4">
                                    <label for="caption" class="form-label fw-bold">Caption (Optional)</label>
                                    <textarea class="form-control" id="caption" name="caption" rows="3" placeholder="Tell us about your experience..."></textarea>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>Your photo will be reviewed before appearing on the website. We may feature it in our customer gallery!</small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                        <i class="fas fa-upload me-2"></i>Upload Photo
                                    </button>
                                    <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Cancel
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('customerPhoto');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = imagePreview.querySelector('img');

    // Click to upload
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    // File selected
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                uploadPrompt.classList.add('d-none');
                imagePreview.classList.remove('d-none');
                uploadArea.style.borderColor = '#28a745';
            };
            reader.readAsDataURL(file);
        }
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.style.borderColor = '#007bff';
        uploadArea.style.backgroundColor = '#f8f9fa';
    });

    uploadArea.addEventListener('dragleave', function() {
        uploadArea.style.borderColor = '#dee2e6';
        uploadArea.style.backgroundColor = 'transparent';
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.style.borderColor = '#dee2e6';
        uploadArea.style.backgroundColor = 'transparent';
        
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            fileInput.files = e.dataTransfer.files;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                uploadPrompt.classList.add('d-none');
                imagePreview.classList.remove('d-none');
                uploadArea.style.borderColor = '#28a745';
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
