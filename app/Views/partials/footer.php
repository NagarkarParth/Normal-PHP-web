<?php /** @var array $branding */ ?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <div class="brand mb-2">
                <span class="brand-mark">YB</span>
                <span class="brand-name"><?= e($branding['site_name']) ?></span>
            </div>
            <p class="text-muted-soft"><?= e($branding['tagline']) ?></p>
        </div>
        <div>
            <h6>Shop</h6>
            <a href="<?= e(url('/products')) ?>">All products</a>
            <a href="<?= e(url('/products?sort=popular')) ?>">Best sellers</a>
            <a href="<?= e(url('/products?sort=newest')) ?>">New releases</a>
        </div>
        <div>
            <h6>Account</h6>
            <a href="<?= e(url('/orders')) ?>">Orders &amp; downloads</a>
            <a href="<?= e(url('/profile')) ?>">Profile</a>
            <a href="<?= e(url('/support')) ?>">Support tickets</a>
        </div>
        <div>
            <h6>Help</h6>
            <a href="<?= e(url('/faq')) ?>">FAQ</a>
            <a href="<?= e(url('/contact')) ?>">Contact us</a>
            <a href="mailto:<?= e($branding['support_email']) ?>"><?= e($branding['support_email']) ?></a>
        </div>
    </div>
    <div class="container footer-bottom">
        <span><?= e($branding['footer_text']) ?></span>
        <span>&copy; <?= date('Y') ?> <?= e($branding['site_name']) ?></span>
    </div>
</footer>
