<!doctype html>
<html lang="en">
<head>
    <?php require BASE_PATH . '/app/Views/partials/head.php'; ?>
</head>
<body class="admin-shell">
    <?php require BASE_PATH . '/app/Views/partials/flash.php'; ?>

    <aside class="admin-sidebar" id="adminSidebar">
        <a class="brand" href="<?= e(url('/admin')) ?>">
            <span class="brand-mark">YB</span>
            <span class="brand-name">Admin</span>
        </a>
        <nav>
            <a href="<?= e(url('/admin')) ?>" class="<?= rtrim((string) parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') === '/admin' ? 'active' : '' ?>"><i class="fas fa-gauge"></i> Dashboard</a>
            <a href="<?= e(url('/admin/products')) ?>" class="<?= active_class('/admin/products') ?>"><i class="fas fa-box"></i> Products</a>
            <a href="<?= e(url('/admin/categories')) ?>" class="<?= active_class('/admin/categories') ?>"><i class="fas fa-tags"></i> Categories</a>
            <a href="<?= e(url('/admin/orders')) ?>" class="<?= active_class('/admin/orders') ?>"><i class="fas fa-receipt"></i> Orders</a>
            <a href="<?= e(url('/admin/coupons')) ?>" class="<?= active_class('/admin/coupons') ?>"><i class="fas fa-percent"></i> Coupons</a>
            <a href="<?= e(url('/admin/reports')) ?>" class="<?= active_class('/admin/reports') ?>"><i class="fas fa-chart-line"></i> Reports</a>
            <a href="<?= e(url('/admin/support')) ?>" class="<?= active_class('/admin/support') ?>"><i class="fas fa-life-ring"></i> Support</a>
            <a href="<?= e(url('/admin/faqs')) ?>" class="<?= active_class('/admin/faqs') ?>"><i class="fas fa-circle-question"></i> FAQ</a>
            <?php if (App\Core\Auth::can('users')): ?>
                <a href="<?= e(url('/admin/users')) ?>" class="<?= active_class('/admin/users') ?>"><i class="fas fa-users"></i> Users</a>
            <?php endif; ?>
            <?php if (App\Core\Auth::can('settings') || App\Core\Auth::can('payments')): ?>
                <a href="<?= e(url('/admin/settings')) ?>" class="<?= active_class('/admin/settings') ?>"><i class="fas fa-sliders"></i> Settings</a>
            <?php endif; ?>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="<?= e(url('/')) ?>"><i class="fas fa-arrow-left"></i> Back to store</a>
        </div>
    </aside>

    <div class="admin-content">
        <header class="admin-topbar">
            <button class="icon-btn d-lg-none" type="button" id="adminSidebarToggle" aria-label="Open menu"><i class="fas fa-bars"></i></button>
            <h1 class="admin-title"><?= e($pageTitle ?? 'Admin') ?></h1>
            <div class="admin-topbar-actions">
                <button class="icon-btn theme-toggle" type="button" aria-label="Toggle dark mode">
                    <i class="fas fa-moon theme-icon-dark"></i>
                    <i class="fas fa-sun theme-icon-light"></i>
                </button>
                <span class="admin-user">
                    <i class="fas fa-circle-user"></i>
                    <?= e((string) ($user['name'] ?? '')) ?>
                    <small class="badge-role"><?= e(str_replace('_', ' ', (string) ($user['role'] ?? ''))) ?></small>
                </span>
                <form method="post" action="<?= e(url('/logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger" type="submit">Sign out</button>
                </form>
            </div>
        </header>

        <main class="admin-main">
            <?= $content ?>
        </main>
    </div>

    <div class="admin-backdrop" id="adminBackdrop"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.2/mdb.umd.min.js"></script>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
