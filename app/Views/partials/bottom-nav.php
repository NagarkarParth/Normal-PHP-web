<?php /** @var int $cartCount */ ?>
<nav class="bottom-nav" aria-label="Mobile">
    <a href="<?= e(url('/')) ?>" class="bottom-nav-item <?= active_class('/') ?>">
        <i class="fas fa-house"></i><span>Home</span>
    </a>
    <a href="<?= e(url('/products')) ?>" class="bottom-nav-item <?= active_class('/product') ?>">
        <i class="fas fa-grip"></i><span>Products</span>
    </a>
    <a href="<?= e(url('/cart')) ?>" class="bottom-nav-item <?= active_class('/cart') ?>">
        <span class="bottom-nav-icon">
            <i class="fas fa-cart-shopping"></i>
            <?php if ($cartCount > 0): ?><span class="cart-badge"><?= (int) $cartCount ?></span><?php endif; ?>
        </span>
        <span>Cart</span>
    </a>
    <a href="<?= e(url(auth() ? '/profile' : '/login')) ?>" class="bottom-nav-item <?= active_class('/profile') ?>">
        <i class="fas fa-user"></i><span>Profile</span>
    </a>
</nav>
