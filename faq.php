<?php
$pageTitle = "FAQ";
require_once 'includes/header.php';

$db = Database::getInstance();
$faqs = $db->query("SELECT * FROM faqs WHERE status = 'active' ORDER BY display_order, id")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-3">Frequently Asked Questions</h2>
        <p class="text-muted">Find answers to common questions about our platform</p>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="accordion" id="faqAccordion">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                            <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>" 
                                    type="button" 
                                    data-mdb-toggle="collapse" 
                                    data-mdb-target="#collapse<?php echo $index; ?>">
                                <?php echo htmlspecialchars($faq['question']); ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $index; ?>" 
                             class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                             data-mdb-parent="#faqAccordion">
                            <div class="accordion-body">
                                <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="card mt-5 text-center">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Still have questions?</h5>
                    <p class="text-muted mb-3">Can't find the answer you're looking for? Please contact our support team.</p>
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">
                        <i class="fas fa-envelope me-2"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
