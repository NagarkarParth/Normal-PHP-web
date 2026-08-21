<?php /** @var array $flashes */ ?>
<?php if (!empty($flashes)): ?>
    <div class="flash-stack" id="flashStack">
        <?php foreach ($flashes as $flash): ?>
            <div class="flash-toast flash-<?= e($flash['type']) ?>" role="alert">
                <i class="fas <?= $flash['type'] === 'success' ? 'fa-circle-check' : ($flash['type'] === 'danger' ? 'fa-circle-exclamation' : 'fa-circle-info') ?>"></i>
                <span><?= $flash['message'] ?></span>
                <button type="button" class="flash-close" aria-label="Dismiss">&times;</button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
