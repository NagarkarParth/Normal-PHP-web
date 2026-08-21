<section class="section container">
    <div class="empty-state surface">
        <i class="fas fa-triangle-exclamation"></i>
        <h2>Something went wrong</h2>
        <p>The error has been logged. Please try again in a moment.</p>
        <a class="btn btn-primary-grad" href="<?= e(url('/')) ?>">Back to home</a>
        <?php if (!empty($exception)): ?>
            <pre class="text-start mt-4 p-3" style="background:var(--surface-2);border-radius:12px;overflow:auto;font-size:.78rem"><?= e((string) $exception) ?></pre>
        <?php endif; ?>
    </div>
</section>
