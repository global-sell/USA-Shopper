<?php
$pageTitle = "Support Tickets";
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$db = Database::getInstance();
$error = '';
$success = '';

// Handle create ticket
if (isset($_POST['create_ticket'])) {
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    $priority = sanitizeInput($_POST['priority'] ?? 'medium');
    
    if (empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $db->prepare("INSERT INTO support_tickets (user_id, subject, message, priority) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $_SESSION['user_id'], $subject, $message, $priority);
        
        if ($stmt->execute()) {
            $success = 'Support ticket created successfully. We will respond soon.';
            $subject = $message = '';
        } else {
            $error = 'Failed to create ticket. Please try again.';
        }
    }
}

// Handle add reply
if (isset($_POST['add_reply'])) {
    $ticketId = (int)$_POST['ticket_id'];
    $replyMessage = sanitizeInput($_POST['reply_message'] ?? '');
    
    if (!empty($replyMessage)) {
        // Verify ticket belongs to user
        $stmt = $db->prepare("SELECT id FROM support_tickets WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $ticketId, $_SESSION['user_id']);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $stmt = $db->prepare("INSERT INTO ticket_replies (ticket_id, user_id, message, is_admin) VALUES (?, ?, ?, 0)");
            $stmt->bind_param("iis", $ticketId, $_SESSION['user_id'], $replyMessage);
            $stmt->execute();
            
            $success = 'Reply added successfully';
        }
    }
}

// Get user tickets
$stmt = $db->prepare("SELECT * FROM support_tickets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-headset me-2"></i>Support Tickets
    </h2>
    
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
    
    <div class="row">
        <!-- Create Ticket Form -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Ticket</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-outline mb-3">
                            <input type="text" id="subject" name="subject" class="form-control" 
                                   value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                            <label class="form-label" for="subject">Subject</label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="priority">Priority</label>
                            <select id="priority" name="priority" class="form-select">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        
                        <div class="form-outline mb-4">
                            <textarea id="message" name="message" class="form-control" rows="6" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                            <label class="form-label" for="message">Message</label>
                        </div>
                        
                        <button type="submit" name="create_ticket" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>Submit Ticket
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Help Resources -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Need Quick Help?</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <a href="<?php echo SITE_URL; ?>/faq.php" class="text-decoration-none">
                                <i class="fas fa-question-circle me-2"></i>Check FAQ
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo SITE_URL; ?>/contact.php" class="text-decoration-none">
                                <i class="fas fa-envelope me-2"></i>Contact Us
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/orders.php" class="text-decoration-none">
                                <i class="fas fa-box me-2"></i>My Orders
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Tickets List -->
        <div class="col-lg-8">
            <?php if (empty($tickets)): ?>
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                        <h4 class="mb-3">No Support Tickets</h4>
                        <p class="text-muted mb-0">You haven't created any support tickets yet. If you need help, create a ticket using the form.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                    <?php
                    $statusClass = match($ticket['status']) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'closed' => 'success',
                        default => 'secondary'
                    };
                    
                    $priorityClass = match($ticket['priority']) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'info',
                        default => 'secondary'
                    };
                    
                    // Get replies count
                    $stmt = $db->prepare("SELECT COUNT(*) as count FROM ticket_replies WHERE ticket_id = ?");
                    $stmt->bind_param("i", $ticket['id']);
                    $stmt->execute();
                    $repliesCount = $stmt->get_result()->fetch_assoc()['count'];
                    ?>
                    
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($ticket['subject']); ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo date('M j, Y g:i A', strtotime($ticket['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                    <span class="badge bg-<?php echo $statusClass; ?> me-1">
                                        <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                    </span>
                                    <span class="badge bg-<?php echo $priorityClass; ?>">
                                        <?php echo ucfirst($ticket['priority']); ?> Priority
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="mb-3"><?php echo nl2br(htmlspecialchars($ticket['message'])); ?></p>
                            
                            <?php
                            // Get replies
                            $stmt = $db->prepare("SELECT tr.*, u.name as user_name 
                                                 FROM ticket_replies tr 
                                                 JOIN users u ON tr.user_id = u.id 
                                                 WHERE tr.ticket_id = ? 
                                                 ORDER BY tr.created_at ASC");
                            $stmt->bind_param("i", $ticket['id']);
                            $stmt->execute();
                            $replies = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            ?>
                            
                            <?php if (!empty($replies)): ?>
                                <div class="border-top pt-3 mt-3">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-comments me-2"></i>Replies (<?php echo count($replies); ?>)
                                    </h6>
                                    <?php foreach ($replies as $reply): ?>
                                        <div class="mb-3 p-3 rounded <?php echo $reply['is_admin'] ? 'bg-light' : 'bg-white border'; ?>">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <strong class="<?php echo $reply['is_admin'] ? 'text-primary' : ''; ?>">
                                                    <?php echo $reply['is_admin'] ? '<i class="fas fa-user-shield me-1"></i>Support Team' : '<i class="fas fa-user me-1"></i>' . htmlspecialchars($reply['user_name']); ?>
                                                </strong>
                                                <small class="text-muted">
                                                    <?php echo date('M j, Y g:i A', strtotime($reply['created_at'])); ?>
                                                </small>
                                            </div>
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($reply['message'])); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($ticket['status'] !== 'closed'): ?>
                                <div class="border-top pt-3 mt-3">
                                    <form method="POST">
                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                        <div class="form-outline mb-3">
                                            <textarea id="reply_message_<?php echo $ticket['id']; ?>" 
                                                      name="reply_message" class="form-control" rows="3" required></textarea>
                                            <label class="form-label" for="reply_message_<?php echo $ticket['id']; ?>">Add Reply</label>
                                        </div>
                                        <button type="submit" name="add_reply" class="btn btn-sm btn-primary">
                                            <i class="fas fa-reply me-2"></i>Send Reply
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check-circle me-2"></i>This ticket has been closed.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
