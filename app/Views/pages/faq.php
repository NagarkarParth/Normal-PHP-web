<section class="section container">
    <div class="section-head"><div><h2>Frequently asked questions</h2><p>Payments, downloads, invoices and refunds.</p></div></div>

    <?php if ($faqs === []): ?>
        <div class="empty-state surface"><i class="fas fa-circle-question"></i>No FAQs published yet.</div>
    <?php else: ?>
        <div class="accordion" id="faqList">
            <?php foreach ($faqs as $index => $faq): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" type="button" data-mdb-collapse-init data-mdb-target="#faqItem<?= (int) $faq['id'] ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>">
                            <?= e($faq['question']) ?>
                        </button>
                    </h2>
                    <div id="faqItem<?= (int) $faq['id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-mdb-parent="#faqList">
                        <div class="accordion-body"><?= nl2br(e((string) $faq['answer'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="surface surface-pad text-center mt-4">
        <h5>Still stuck?</h5>
        <p class="text-muted-soft">Open a ticket and a human will get back to you.</p>
        <a class="btn btn-primary-grad" href="<?= e(url('/contact')) ?>">Contact support</a>
    </div>
</section>
