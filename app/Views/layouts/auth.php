<!doctype html>
<html lang="en">
<head>
    <?php require BASE_PATH . '/app/Views/partials/head.php'; ?>
</head>
<body class="auth-shell">
    <?php require BASE_PATH . '/app/Views/partials/flash.php'; ?>

    <div class="auth-wrap">
        <aside class="auth-aside">
            <a class="brand" href="<?= e(url('/')) ?>">
                <span class="brand-mark">YB</span>
                <span class="brand-name"><?= e($branding['site_name']) ?></span>
            </a>
            <h2>Buy once. Download forever.</h2>
            <p><?= e($branding['tagline']) ?></p>
            <ul class="auth-points">
                <li><i class="fas fa-bolt"></i> Instant delivery after payment</li>
                <li><i class="fas fa-file-invoice"></i> GST ready invoices</li>
                <li><i class="fas fa-shield-halved"></i> Secure, expiring download links</li>
            </ul>
        </aside>
        <section class="auth-panel">
            <button class="icon-btn theme-toggle auth-theme" type="button" aria-label="Toggle dark mode">
                <i class="fas fa-moon theme-icon-dark"></i>
                <i class="fas fa-sun theme-icon-light"></i>
            </button>
            <?= $content ?>
        </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.2/mdb.umd.min.js"></script>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
