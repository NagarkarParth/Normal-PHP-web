<!doctype html>
<html lang="en">
<head>
    <?php require BASE_PATH . '/app/Views/partials/head.php'; ?>
</head>
<body class="app-shell">
    <?php require BASE_PATH . '/app/Views/partials/navbar.php'; ?>
    <?php require BASE_PATH . '/app/Views/partials/flash.php'; ?>

    <main class="app-main">
        <?= $content ?>
    </main>

    <?php require BASE_PATH . '/app/Views/partials/footer.php'; ?>
    <?php require BASE_PATH . '/app/Views/partials/bottom-nav.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.2/mdb.umd.min.js"></script>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
