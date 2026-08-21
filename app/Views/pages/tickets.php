<section class="section container">
    <div class="section-head">
        <div><h2>Support tickets</h2><p>Track conversations with our team.</p></div>
        <a class="btn btn-sm btn-primary-grad" href="<?= e(url('/contact')) ?>">New ticket</a>
    </div>

    <?php if ($tickets === []): ?>
        <div class="empty-state surface"><i class="fas fa-life-ring"></i>No tickets yet.</div>
    <?php else: ?>
        <div class="list-rows">
            <?php foreach ($tickets as $ticket): ?>
                <a class="list-row" href="<?= e(url('support/' . $ticket['id'])) ?>">
                    <div class="thumb-fallback"><i class="fas fa-comment-dots"></i></div>
                    <div class="flex-grow-1">
                        <strong>#<?= (int) $ticket['id'] ?> · <?= e($ticket['subject']) ?></strong>
                        <div class="text-muted-soft" style="font-size:.82rem"><?= e(excerpt((string) $ticket['message'], 90)) ?></div>
                    </div>
                    <span class="badge-status badge-<?= $ticket['status'] === 'closed' ? 'secondary' : ($ticket['status'] === 'pending' ? 'info' : 'warning') ?>"><?= e($ticket['status']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
