<div class="surface surface-pad">
    <form class="filter-bar mb-3" method="get" action="<?= e(url('/admin/support')) ?>">
        <div class="form-field">
            <label for="status">Status</label>
            <select class="form-select" id="status" name="status" data-autosubmit>
                <option value="">All tickets</option>
                <?php foreach (['open', 'pending', 'closed'] as $option): ?>
                    <option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-ghost" type="submit">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>#</th><th>Subject</th><th>From</th><th>Replies</th><th>Status</th><th>Updated</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?= (int) $ticket['id'] ?></td>
                    <td><strong><?= e((string) $ticket['subject']) ?></strong><div class="text-muted-soft" style="font-size:.78rem"><?= e(excerpt((string) $ticket['message'], 70)) ?></div></td>
                    <td><?= e((string) ($ticket['user_name'] ?? $ticket['name'])) ?><div class="text-muted-soft" style="font-size:.78rem"><?= e((string) $ticket['email']) ?></div></td>
                    <td><?= (int) $ticket['replies_count'] ?></td>
                    <td><span class="badge-status badge-<?= $ticket['status'] === 'closed' ? 'secondary' : ($ticket['status'] === 'pending' ? 'info' : 'warning') ?>"><?= e((string) $ticket['status']) ?></span></td>
                    <td><?= e(date('d M, H:i', strtotime((string) $ticket['updated_at']))) ?></td>
                    <td><a class="btn btn-sm btn-ghost" href="<?= e(url('admin/support/' . $ticket['id'])) ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($tickets === []): ?><tr><td colspan="7" class="text-muted-soft">No tickets.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
        <nav class="pagination-bar">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <?php if ($i === $page): ?><span class="current"><?= $i ?></span>
                <?php else: ?><a href="<?= e(url('/admin/support?' . http_build_query(['status' => $status, 'page' => $i]))) ?>"><?= $i ?></a><?php endif; ?>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</div>
