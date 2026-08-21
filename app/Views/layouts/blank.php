<!doctype html>
<html lang="en">
<head>
    <?php require BASE_PATH . '/app/Views/partials/head.php'; ?>
</head>
<body class="blank-shell">
    <?= $content ?>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
