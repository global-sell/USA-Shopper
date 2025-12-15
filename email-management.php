<?php
$pageTitle = "Email Management";
require_once 'includes/admin-header.php';

$db = Database::getInstance();
$error = '';
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'send_bulk_email') {
        $recipients = $_POST['recipients'] ?? '';
        $subject = sanitizeInput($_POST['subject'] ?? '');
        $emailContent = $_POST['email_content'] ?? '';
        
        // Validation
        if (empty($recipients)) {
            $error = 'Please select recipients';
        } elseif (empty($subject)) {
            $error = 'Please enter email subject';
        } elseif (empty($emailContent)) {
            $error = 'Please enter email content';
        } else {
            $emailList = [];
            
            if ($recipients === 'all_users') {
                $result = $db->query("SELECT email, name FROM users WHERE role = 'user'");
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $emailList[] = ['email' => $row['email'], 'name' => $row['name']];
                    }
                }
            } elseif ($recipients === 'subscribers') {
                $result = $db->query("SELECT email FROM newsletter_subscribers");
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $emailList[] = ['email' => $row['email'], 'name' => 'Subscriber'];
                    }
                }
            }
            
            if (empty($emailList)) {
                $error = 'No recipients found in selected group';
            } else {
                $sentCount = 0;
                $failedCount = 0;
                
                foreach ($emailList as $recipient) {
                    $personalizedMessage = str_replace('{name}', $recipient['name'], $emailContent);
                    
                    // Wrap content in basic HTML if not already HTML
                    if (strpos($personalizedMessage, '<html') === false) {
                        $personalizedMessage = "
                        <!DOCTYPE html>
                        <html>
                        <head><meta charset='UTF-8'></head>
                        <body style='font-family: Arial, sans-serif; padding: 20px;'>
                            {$personalizedMessage}
                        </body>
                        </html>
                        ";
                    }
                    
                    if (sendEmail($recipient['email'], $subject, $personalizedMessage)) {
                        $sentCount++;
                    } else {
                        $failedCount++;
                    }
                }
                
                if ($sentCount > 0) {
                    $success = "Successfully sent {$sentCount} email(s) to " . count($emailList) . " recipient(s)";
                    if ($failedCount > 0) {
                        $success .= " ({$failedCount} failed - check logs/emails.log for details)";
                    }
                } else {
                    $error = "Failed to send emails. Check logs/emails.log for details.";
                }
            }
        }
    } elseif ($action === 'update_template') {
        $templateType = sanitizeInput($_POST['template_type'] ?? '');
        $templateContent = $_POST['template_content'] ?? '';
        
        updateSetting("email_template_{$templateType}", $templateContent);
        $success = 'Email template updated successfully';
    } elseif ($action === 'delete_subscriber') {
        $subscriberId = (int)($_POST['subscriber_id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM newsletter_subscribers WHERE id = ?");
        $stmt->bind_param("i", $subscriberId);
        $stmt->execute();
        $success = 'Subscriber deleted successfully';
    }
}

// Get statistics
$statsQuery = "
    SELECT 
        (SELECT COUNT(*) FROM users WHERE role = 'user') as total_users,
        (SELECT COUNT(*) FROM newsletter_subscribers) as total_subscribers,
        0 as new_subscribers_month
";
$stats = $db->query($statsQuery)->fetch_assoc();

// Get recent subscribers
$subscribersQuery = "SELECT * FROM newsletter_subscribers ORDER BY id DESC LIMIT 50";
$subscribers = $db->query($subscribersQuery)->fetch_all(MYSQLI_ASSOC);

// Get all users
$usersQuery = "SELECT id, name, email FROM users WHERE role = 'user' ORDER BY id DESC LIMIT 50";
$users = $db->query($usersQuery)->fetch_all(MYSQLI_ASSOC);
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="fas fa-envelope-open-text me-2 text-primary"></i>Email Management
            </h2>
            <p class="text-muted mb-0">Manage subscribers, send bulk emails, and customize email templates</p>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-gradient rounded-3 p-3">
                                <i class="fas fa-users fa-2x text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Users</h6>
                            <h3 class="mb-0 fw-bold"><?php echo number_format($stats['total_users']); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-gradient rounded-3 p-3">
                                <i class="fas fa-envelope fa-2x text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Newsletter Subscribers</h6>
                            <h3 class="mb-0 fw-bold"><?php echo number_format($stats['total_subscribers']); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-gradient rounded-3 p-3">
                                <i class="fas fa-chart-line fa-2x text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">New This Month</h6>
                            <h3 class="mb-0 fw-bold"><?php echo number_format($stats['new_subscribers_month']); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabbed Interface -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <!-- Nav Tabs -->
            <ul class="nav nav-tabs nav-fill" id="emailTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="bulk-email-tab" data-mdb-toggle="tab" 
                            data-mdb-target="#bulk-email" type="button" role="tab">
                        <i class="fas fa-paper-plane me-2"></i>Send Bulk Email
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="subscribers-tab" data-mdb-toggle="tab" 
                            data-mdb-target="#subscribers" type="button" role="tab">
                        <i class="fas fa-list me-2"></i>Newsletter Subscribers
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="users-tab" data-mdb-toggle="tab" 
                            data-mdb-target="#users" type="button" role="tab">
                        <i class="fas fa-user-friends me-2"></i>Registered Users
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="templates-tab" data-mdb-toggle="tab" 
                            data-mdb-target="#templates" type="button" role="tab">
                        <i class="fas fa-file-code me-2"></i>Email Templates
                    </button>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content p-4" id="emailTabContent">
                
                <!-- Send Bulk Email Tab -->
                <div class="tab-pane fade show active" id="bulk-email" role="tabpanel">
                    <h4 class="mb-4">
                        <i class="fas fa-paper-plane text-primary me-2"></i>Send Bulk Email Campaign
                    </h4>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="send_bulk_email">
                        
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Select Recipients *</label>
                                            <select name="recipients" class="form-select form-select-lg" required>
                                                <option value="">-- Choose Recipients Group --</option>
                                                <option value="all_users">📧 All Registered Users (<?php echo $stats['total_users']; ?>)</option>
                                                <option value="subscribers">📰 Newsletter Subscribers (<?php echo $stats['total_subscribers']; ?>)</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Email Subject *</label>
                                            <input type="text" name="subject" class="form-control form-control-lg" 
                                                   placeholder="e.g., Special Offer - 20% Off Today!" required>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Email Content *</label>
                                            <textarea name="email_content" id="emailEditor" rows="15" class="form-control" required 
                                                      placeholder="Enter your email content here..."><?php echo htmlspecialchars('<h2>Hello {name}!</h2>
<p>We have exciting news for you!</p>
<p>This is a sample email template. Replace this with your actual content.</p>
<p>Best regards,<br>The Rangpur Food Team</p>'); ?></textarea>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Use <code>{name}</code> to personalize with recipient name. Supports HTML formatting.
                                            </small>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-paper-plane me-2"></i>Send Email Campaign
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="card border border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Quick Tips</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                Test with small group first
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                Use responsive HTML
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                Include unsubscribe link
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                Personalize with {name}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Newsletter Subscribers Tab -->
                <div class="tab-pane fade" id="subscribers" role="tabpanel">
                    <h4 class="mb-4">
                        <i class="fas fa-list text-primary me-2"></i>Newsletter Subscribers (<?php echo count($subscribers); ?>)
                    </h4>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Email Address</th>
                                    <th>Status</th>
                                    <th>Subscribed Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subscribers as $subscriber): ?>
                                <tr>
                                    <td><?php echo $subscriber['id']; ?></td>
                                    <td>
                                        <i class="fas fa-envelope me-2 text-muted"></i>
                                        <?php echo htmlspecialchars($subscriber['email']); ?>
                                    </td>
                                    <td>
                                        <?php if ($subscriber['status'] === 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo isset($subscriber['created_at']) ? date('M d, Y', strtotime($subscriber['created_at'])) : 'N/A'; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="deleteSubscriber(<?php echo $subscriber['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Registered Users Tab -->
                <div class="tab-pane fade" id="users" role="tabpanel">
                    <h4 class="mb-4">
                        <i class="fas fa-user-friends text-primary me-2"></i>Registered Users (<?php echo count($users); ?>)
                    </h4>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email Address</th>
                                    <th>Registration Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td>
                                        <i class="fas fa-user me-2 text-muted"></i>
                                        <?php echo htmlspecialchars($user['name']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A'; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="composeEmail('<?php echo htmlspecialchars($user['email']); ?>')">
                                            <i class="fas fa-envelope"></i> Email
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Email Templates Tab -->
                <div class="tab-pane fade" id="templates" role="tabpanel">
                    <h4 class="mb-4">
                        <i class="fas fa-file-code text-primary me-2"></i>Manage Email Templates
                    </h4>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="accordion" id="templatesAccordion">
                                <!-- Welcome Email Template -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#welcomeTemplate">
                                            <i class="fas fa-hand-wave me-2"></i>Welcome Email Template
                                        </button>
                                    </h2>
                                    <div id="welcomeTemplate" class="accordion-collapse collapse show" data-mdb-parent="#templatesAccordion">
                                        <div class="accordion-body">
                                            <form method="POST">
                                                <input type="hidden" name="action" value="update_template">
                                                <input type="hidden" name="template_type" value="welcome">
                                                <p class="text-muted">Customize the welcome email sent to new users</p>
                                                <textarea name="template_content" rows="10" class="form-control mb-3" placeholder="Enter HTML template..."></textarea>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save me-2"></i>Save Template
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Newsletter Template -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#newsletterTemplate">
                                            <i class="fas fa-newspaper me-2"></i>Newsletter Template
                                        </button>
                                    </h2>
                                    <div id="newsletterTemplate" class="accordion-collapse collapse" data-mdb-parent="#templatesAccordion">
                                        <div class="accordion-body">
                                            <form method="POST">
                                                <input type="hidden" name="action" value="update_template">
                                                <input type="hidden" name="template_type" value="newsletter">
                                                <p class="text-muted">Customize the newsletter template</p>
                                                <textarea name="template_content" rows="10" class="form-control mb-3" placeholder="Enter HTML template..."></textarea>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save me-2"></i>Save Template
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Order Confirmation Template -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#orderTemplate">
                                            <i class="fas fa-receipt me-2"></i>Order Confirmation Template
                                        </button>
                                    </h2>
                                    <div id="orderTemplate" class="accordion-collapse collapse" data-mdb-parent="#templatesAccordion">
                                        <div class="accordion-body">
                                            <form method="POST">
                                                <input type="hidden" name="action" value="update_template">
                                                <input type="hidden" name="template_type" value="order">
                                                <p class="text-muted">Customize order confirmation emails</p>
                                                <textarea name="template_content" rows="10" class="form-control mb-3" placeholder="Enter HTML template..."></textarea>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save me-2"></i>Save Template
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
function deleteSubscriber(id) {
    if (confirm('Are you sure you want to delete this subscriber?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_subscriber">
            <input type="hidden" name="subscriber_id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function composeEmail(email) {
    document.getElementById('bulk-email-tab').click();
    // Could open a compose modal here
    alert('Compose email to: ' + email);
}
</script>

<?php require_once 'includes/admin-footer.php'; ?>
