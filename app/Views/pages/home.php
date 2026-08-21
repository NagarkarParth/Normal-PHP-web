<section class="hero">
    <div class="container hero-inner">
        <div>
            <span class="chip chip-brand mb-3"><i class="fas fa-bolt"></i> Instant digital delivery</span>
            <h1><?= e($branding['site_name']) ?> — digital products that ship the second you pay.</h1>
            <p class="lead"><?= e($branding['tagline']) ?> Templates, UI kits, scripts and playbooks with secure downloads and GST ready invoices.</p>
            <div class="hero-cta">
                <a class="btn btn-lg btn-primary-grad" href="<?= e(url('/products')) ?>">Explore Products</a>
                <a class="btn btn-lg btn-ghost" href="<?= e(url('/faq')) ?>">How it works</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><strong><?= count($featured) ?>+</strong><span>Curated products</span></div>
                <div class="hero-stat"><strong>2 min</strong><span>From cart to download</span></div>
                <div class="hero-stat"><strong>24/7</strong><span>Support tickets</span></div>
            </div>
        </div>
        <div class="hero-art">
            <div class="mini-row"><i class="fas fa-cart-shopping"></i><div><strong>Add to cart</strong><div class="text-muted-soft" style="font-size:.82rem">Coupons applied instantly</div></div></div>
            <div class="mini-row"><i class="fas fa-credit-card"></i><div><strong>Pay securely</strong><div class="text-muted-soft" style="font-size:.82rem">Razorpay · Stripe · PayPal</div></div></div>
            <div class="mini-row"><i class="fas fa-cloud-arrow-down"></i><div><strong>Download instantly</strong><div class="text-muted-soft" style="font-size:.82rem">Private, expiring links</div></div></div>
            <div class="mini-row"><i class="fas fa-file-invoice"></i><div><strong>Invoice in your inbox</strong><div class="text-muted-soft" style="font-size:.82rem">Tax breakdown included</div></div></div>
        </div>
    </div>
</section>

<?php if ($categories !== []): ?>
<section class="section container">
    <div class="section-head">
        <div><h2>Browse by category</h2><p>Find exactly the kind of asset you need.</p></div>
        <a class="btn btn-sm btn-ghost d-none d-md-inline-flex" href="<?= e(url('/products')) ?>">View all</a>
    </div>
    <div class="scroll-rail">
        <?php foreach ($categories as $category): ?>
            <a class="surface surface-pad" href="<?= e(url('/products?category=' . urlencode((string) $category['slug']))) ?>">
                <div class="d-flex align-items-center justify-content-between">
                    <strong><?= e($category['name']) ?></strong>
                    <span class="chip"><?= (int) $category['products_count'] ?></span>
                </div>
                <p class="text-muted-soft mb-0 mt-2" style="font-size:.85rem"><?= e(excerpt((string) ($category['description'] ?? ''), 70)) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="section container">
    <div class="section-head">
        <div><h2>Featured products</h2><p>Hand picked by our team this month.</p></div>
        <a class="btn btn-sm btn-ghost d-none d-md-inline-flex" href="<?= e(url('/products')) ?>">All products</a>
    </div>
    <?php if ($featured === []): ?>
        <div class="empty-state surface"><i class="fas fa-box-open"></i>No products published yet.</div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($featured as $item): require BASE_PATH . '/app/Views/partials/product-card.php'; endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php if ($testimonials !== []): ?>
<section class="section container">
    <div class="section-head"><div><h2>Loved by builders</h2><p>Real feedback from customers who shipped faster.</p></div></div>
    <div class="scroll-rail">
        <?php foreach ($testimonials as $testimonial): ?>
            <div class="testimonial">
                <div class="stars"><?= str_repeat('★', max(1, (int) $testimonial['rating'])) ?></div>
                <p class="mb-3">“<?= e($testimonial['message']) ?>”</p>
                <strong><?= e($testimonial['name']) ?></strong>
                <div class="text-muted-soft" style="font-size:.82rem"><?= e((string) $testimonial['role']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($faqs !== []): ?>
<section class="section container">
    <div class="section-head">
        <div><h2>Questions, answered</h2><p>Everything about payments, downloads and refunds.</p></div>
        <a class="btn btn-sm btn-ghost d-none d-md-inline-flex" href="<?= e(url('/faq')) ?>">Full FAQ</a>
    </div>
    <div class="accordion" id="homeFaq">
        <?php foreach ($faqs as $index => $faq): ?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" type="button" data-mdb-collapse-init data-mdb-target="#faq<?= (int) $faq['id'] ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>">
                        <?= e($faq['question']) ?>
                    </button>
                </h2>
                <div id="faq<?= (int) $faq['id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-mdb-parent="#homeFaq">
                    <div class="accordion-body"><?= nl2br(e((string) $faq['answer'])) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
