<div class="surface surface-pad">
    <h5 class="mb-3">Create coupon</h5>
    <form method="post" action="<?= e(url('/admin/coupons')) ?>" class="form-grid cols-3">
        <?= csrf_field() ?>
        <div class="form-field"><label for="code">Code</label><input class="form-control" id="code" name="code" placeholder="WELCOME10" required></div>
        <div class="form-field">
            <label for="type">Type</label>
            <select class="form-select" id="type" name="type"><option value="percent">Percentage</option><option value="flat">Flat amount</option></select>
        </div>
        <div class="form-field"><label for="value">Value</label><input class="form-control" id="value" name="value" type="number" step="0.01" min="0" required></div>
        <div class="form-field"><label for="min_order_amount">Min order amount</label><input class="form-control" id="min_order_amount" name="min_order_amount" type="number" step="0.01" min="0" value="0"></div>
        <div class="form-field"><label for="max_discount">Max discount (percent only)</label><input class="form-control" id="max_discount" name="max_discount" type="number" step="0.01" min="0"></div>
        <div class="form-field"><label for="usage_limit">Usage limit</label><input class="form-control" id="usage_limit" name="usage_limit" type="number" min="1" placeholder="Unlimited"></div>
        <div class="form-field"><label for="starts_at">Starts at</label><input class="form-control" id="starts_at" name="starts_at" type="datetime-local"></div>
        <div class="form-field"><label for="expires_at">Expires at</label><input class="form-control" id="expires_at" name="expires_at" type="datetime-local"></div>
        <div class="form-field"><label for="description">Description</label><input class="form-control" id="description" name="description"></div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
        <div><button class="btn btn-primary-grad" type="submit">Create coupon</button></div>
    </form>
</div>

<div class="surface surface-pad">
    <h5 class="mb-3">All coupons</h5>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Used</th><th>Window</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($coupons as $coupon): ?>
                <tr>
                    <td><strong><?= e((string) $coupon['code']) ?></strong><div class="text-muted-soft" style="font-size:.78rem"><?= e((string) $coupon['description']) ?></div></td>
                    <td><?= $coupon['type'] === 'flat' ? 'Flat' : 'Percent' ?></td>
                    <td><?= $coupon['type'] === 'flat' ? money((float) $coupon['value']) : rtrim(rtrim(number_format((float) $coupon['value'], 2), '0'), '.') . '%' ?></td>
                    <td><?= (int) $coupon['used_count'] ?><?= $coupon['usage_limit'] !== null ? ' / ' . (int) $coupon['usage_limit'] : '' ?></td>
                    <td style="font-size:.8rem">
                        <?= $coupon['starts_at'] ? e(date('d M Y', strtotime((string) $coupon['starts_at']))) : 'Now' ?>
                        → <?= $coupon['expires_at'] ? e(date('d M Y', strtotime((string) $coupon['expires_at']))) : 'No expiry' ?>
                    </td>
                    <td><span class="badge-status badge-<?= (int) $coupon['is_active'] === 1 ? 'success' : 'secondary' ?>"><?= (int) $coupon['is_active'] === 1 ? 'active' : 'paused' ?></span></td>
                    <td class="d-flex gap-2">
                        <form method="post" action="<?= e(url('admin/coupons/' . $coupon['id'])) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="code" value="<?= e((string) $coupon['code']) ?>">
                            <input type="hidden" name="type" value="<?= e((string) $coupon['type']) ?>">
                            <input type="hidden" name="value" value="<?= e((string) $coupon['value']) ?>">
                            <input type="hidden" name="description" value="<?= e((string) $coupon['description']) ?>">
                            <input type="hidden" name="min_order_amount" value="<?= e((string) $coupon['min_order_amount']) ?>">
                            <input type="hidden" name="max_discount" value="<?= e((string) $coupon['max_discount']) ?>">
                            <input type="hidden" name="usage_limit" value="<?= e((string) $coupon['usage_limit']) ?>">
                            <?php if ((int) $coupon['is_active'] === 1): ?>
                                <button class="btn btn-sm btn-ghost" type="submit">Pause</button>
                            <?php else: ?>
                                <input type="hidden" name="is_active" value="1">
                                <button class="btn btn-sm btn-ghost" type="submit">Activate</button>
                            <?php endif; ?>
                        </form>
                        <form method="post" action="<?= e(url('admin/coupons/' . $coupon['id'] . '/delete')) ?>" data-confirm="Delete this coupon?">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($coupons === []): ?><tr><td colspan="7" class="text-muted-soft">No coupons yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
