<section class="section container">
    <a class="text-muted-soft" href="<?= e(url('/support')) ?>" style="font-size:.85rem"><i class="fas fa-arrow-left me-1"></i>All tickets</a>

    <div class="section-head mt-2">
        <div>
            <h2>#<?= (int) $ticket['id'] ?> · <?= e($ticket['subject']) ?></h2>
            <p>Opened <?= e(date('d M Y', strtotime((string) $ticket['created_at']))) ?> · <span class="badge-status badge-<?= $ticket['status'] === 'closed' ? 'secondary' : ($ticket['status'] === 'pending' ? 'info' : 'warning') ?>"><?= e($ticket['status']) ?></span></p>
        </div>
    </div>

    <div class="d-grid gap-3" style="max-width:760px">
        <div class="timeline-msg">
            <strong><?= e($ticket['name']) ?></strong>
            <div class="text-muted-soft" style="font-size:.78rem"><?= e(date('d M Y, H:i', strtotime((string) $ticket['created_at']))) ?></div>
            <p class="mb-0 mt-2"><?= nl2br(e((string) $ticket['message'])) ?></p>
        </div>

        <?php foreach ($replies as $reply): ?>
            <div class="timeline-msg <?= (int) $reply['is_admin'] === 1 ? 'admin' : '' ?>">
                <strong><?= (int) $reply['is_admin'] === 1 ? 'Support team' : e((string) $reply['author_name']) ?></strong>
                <div class="text-muted-soft" style="font-size:.78rem"><?= e(date('d M Y, H:i', strtotime((string) $reply['created_at']))) ?></div>
                <p class="mb-0 mt-2"><?= nl2br(e((string) $reply['message'])) ?></p>
            </div>
        <?php endforeach; ?>

        <?php if ($ticket['status'] !== 'closed'): ?>
            <form class="surface surface-pad" method="post" action="<?= e(url('support/' . $ticket['id'] . '/reply')) ?>">
                <?= csrf_field() ?>
                <div class="form-field mb-3">
                    <label for="message">Add a reply</label>
                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                </div>
                <button class="btn btn-primary-grad" type="submit">Send reply</button>
            </form>
        <?php else: ?>
            <div class="surface surface-pad text-muted-soft">This ticket is closed. <a href="<?= e(url('/contact')) ?>">Open a new one</a> if you need more help.</div>
        <?php endif; ?>
    </div>
</section>
