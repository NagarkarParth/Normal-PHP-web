<?php /** @var array $branding */ /** @var array|null $user */ /** @var int $cartCount */ ?>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= e(url('/')) ?>">
            <?php if ($branding['logo'] !== ''): ?>
                <img src="<?= e(uploaded($branding['logo'])) ?>" alt="<?= e($branding['site_name']) ?>" class="brand-logo">
            <?php else: ?>
                <span class="brand-mark">YB</span>
            <?php endif; ?>
            <span class="brand-name"><?= e($branding['site_name']) ?></span>
        </a>

        <nav class="main-nav" aria-label="Primary">
            <a href="<?= e(url('/')) ?>" class="<?= active_class('/') ?>">Home</a>
            <a href="<?= e(url('/products')) ?>" class="<?= active_class('/product') ?>">Products</a>
            <a href="<?= e(url('/faq')) ?>" class="<?= active_class('/faq') ?>">FAQ</a>
            <a href="<?= e(url('/contact')) ?>" class="<?= active_class('/contact') ?>">Contact</a>
            <?php if (is_admin()): ?>
                <a href="<?= e(url('/admin')) ?>" class="<?= active_class('/admin') ?>">Admin</a>
            <?php endif; ?>
        </nav>

        <div class="header-actions">
            <button class="icon-btn theme-toggle" type="button" aria-label="Toggle dark mode">
                <i class="fas fa-moon theme-icon-dark"></i>
                <i class="fas fa-sun theme-icon-light"></i>
            </button>
            <a class="icon-btn cart-link" href="<?= e(url('/cart')) ?>" aria-label="Cart">
                <i class="fas fa-cart-shopping"></i>
                <?php if ($cartCount > 0): ?><span class="cart-badge"><?= (int) $cartCount ?></span><?php endif; ?>
            </a>
            <?php if ($user !== null): ?>
                <div class="dropdown">
                    <button class="btn btn-sm btn-ghost dropdown-toggle" type="button" data-mdb-dropdown-init aria-expanded="false">
                        <i class="fas fa-circle-user me-1"></i><?= e(explode(' ', (string) $user['name'])[0]) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= e(url('/profile')) ?>">Profile</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('/orders')) ?>">Orders &amp; downloads</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('/support')) ?>">Support tickets</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="<?= e(url('/logout')) ?>" class="px-3 py-1">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger w-100" type="submit">Sign out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <a class="btn btn-sm btn-ghost d-none d-md-inline-flex" href="<?= e(url('/login')) ?>">Log in</a>
                <a class="btn btn-sm btn-primary-grad" href="<?= e(url('/signup')) ?>">Sign up</a>
            <?php endif; ?>
        </div>
    </div>
</header>
