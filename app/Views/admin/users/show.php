<div class="admin-grid-2">
    <div class="surface surface-pad">
        <div class="section-head"><div><h2 style="font-size:1.05rem">Purchase history</h2></div><a class="btn btn-sm btn-ghost" href="<?= e(url('/admin/users')) ?>">Back</a></div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Order</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= e((string) $order['order_number']) ?></td>
                        <td><?= money((float) $order['total']) ?></td>
                        <td><span class="badge-status badge-<?= e(order_status_color((string) $order['payment_status'])) ?>"><?= e((string) $order['payment_status']) ?></span></td>
                        <td><?= e(date('d M Y', strtotime((string) $order['created_at']))) ?></td>
                        <td><a class="btn btn-sm btn-ghost" href="<?= e(url('admin/orders/' . $order['id'])) ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($orders === []): ?><tr><td colspan="5" class="text-muted-soft">No orders yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-grid gap-3">
        <div class="surface surface-pad">
            <h5 class="mb-2"><?= e((string) $account['name']) ?></h5>
            <dl class="text-muted-soft" style="font-size:.87rem">
                <dt>Email</dt><dd><?= e((string) $account['email']) ?></dd>
                <dt>Joined</dt><dd><?= e(date('d M Y', strtotime((string) $account['created_at']))) ?></dd>
                <dt>Status</dt><dd><span class="badge-status badge-<?= (int) $account['is_blocked'] === 1 ? 'danger' : 'success' ?>"><?= (int) $account['is_blocked'] === 1 ? 'blocked' : 'active' ?></span></dd>
            </dl>

            <form method="post" action="<?= e(url('admin/users/' . $account['id'] . '/block')) ?>" data-confirm="Change access for this user?">
                <?= csrf_field() ?>
                <button class="btn <?= (int) $account['is_blocked'] === 1 ? 'btn-primary-grad' : 'btn-outline-danger' ?> w-100" type="submit">
                    <?= (int) $account['is_blocked'] === 1 ? 'Unblock user' : 'Block user' ?>
                </button>
            </form>
        </div>

        <div class="surface surface-pad">
            <h5 class="mb-2">Role</h5>
            <form method="post" action="<?= e(url('admin/users/' . $account['id'] . '/role')) ?>" class="d-grid gap-2">
                <?= csrf_field() ?>
                <select class="form-select" name="role">
                    <?php foreach (['customer' => 'Customer', 'editor' => 'Editor', 'super_admin' => 'Super admin'] as $value => $label): ?>
                        <option value="<?= $value ?>" <?= $account['role'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-ghost" type="submit">Update role</button>
            </form>
        </div>
    </div>
</div>
