<div class="admin-grid-2">
    <div class="d-grid gap-3">
        <div class="timeline-msg">
            <strong><?= e((string) $ticket['name']) ?></strong>
            <div class="text-muted-soft" style="font-size:.78rem"><?= e((string) $ticket['email']) ?> · <?= e(date('d M Y, H:i', strtotime((string) $ticket['created_at']))) ?></div>
            <p class="mb-0 mt-2"><?= nl2br(e((string) $ticket['message'])) ?></p>
        </div>

        <?php foreach ($replies as $reply): ?>
            <div class="timeline-msg <?= (int) $reply['is_admin'] === 1 ? 'admin' : '' ?>">
                <strong><?= (int) $reply['is_admin'] === 1 ? 'Support team' : e((string) ($reply['author_name'] ?? $ticket['name'])) ?></strong>
                <div class="text-muted-soft" style="font-size:.78rem"><?= e(date('d M Y, H:i', strtotime((string) $reply['created_at']))) ?></div>
                <p class="mb-0 mt-2"><?= nl2br(e((string) $reply['message'])) ?></p>
            </div>
        <?php endforeach; ?>

        <form class="surface surface-pad" method="post" action="<?= e(url('admin/support/' . $ticket['id'] . '/reply')) ?>">
            <?= csrf_field() ?>
            <div class="form-field mb-3"><label for="message">Reply to customer</label><textarea class="form-control" id="message" name="message" rows="5" required></textarea></div>
            <button class="btn btn-primary-grad" type="submit">Send reply</button>
        </form>
    </div>

    <div class="d-grid gap-3">
        <div class="surface surface-pad">
            <h5 class="mb-2">Ticket #<?= (int) $ticket['id'] ?></h5>
            <p class="text-muted-soft mb-3" style="font-size:.87rem"><?= e((string) $ticket['subject']) ?></p>
            <form method="post" action="<?= e(url('admin/support/' . $ticket['id'] . '/status')) ?>" class="d-grid gap-2">
                <?= csrf_field() ?>
                <select class="form-select" name="status">
                    <?php foreach (['open' => 'Open', 'pending' => 'Pending customer', 'closed' => 'Closed'] as $value => $label): ?>
                        <option value="<?= $value ?>" <?= $ticket['status'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-ghost" type="submit">Update status</button>
            </form>
            <a class="btn btn-sm btn-link w-100 mt-2" href="<?= e(url('/admin/support')) ?>">Back to tickets</a>
        </div>
    </div>
</div>
