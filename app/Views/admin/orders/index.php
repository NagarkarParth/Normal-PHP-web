<div class="surface surface-pad">
    <form class="filter-bar mb-3" method="get" action="<?= e(url('/admin/orders')) ?>">
        <div class="form-field"><label for="q">Search</label><input class="form-control" id="q" name="q" value="<?= e($search) ?>" placeholder="Order number, email, transaction"></div>
        <div class="form-field">
            <label for="status">Payment status</label>
            <select class="form-select" id="status" name="status" data-autosubmit>
                <option value="">All</option>
                <?php foreach (['pending', 'paid', 'failed', 'refunded'] as $option): ?>
                    <option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-ghost" type="submit">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Order</th><th>Customer</th><th>Gateway</th><th>Transaction</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong><?= e((string) $order['order_number']) ?></strong></td>
                    <td><?= e((string) ($order['user_name'] ?? $order['customer_name'])) ?><div class="text-muted-soft" style="font-size:.78rem"><?= e((string) $order['customer_email']) ?></div></td>
                    <td class="text-capitalize"><?= e((string) $order['gateway']) ?></td>
                    <td><?= e((string) ($order['transaction_id'] ?? '—')) ?></td>
                    <td><?= money((float) $order['total']) ?></td>
                    <td><span class="badge-status badge-<?= e(order_status_color((string) $order['payment_status'])) ?>"><?= e((string) $order['payment_status']) ?></span></td>
                    <td><?= e(date('d M Y', strtotime((string) $order['created_at']))) ?></td>
                    <td><a class="btn btn-sm btn-ghost" href="<?= e(url('admin/orders/' . $order['id'])) ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($orders === []): ?><tr><td colspan="8" class="text-muted-soft">No orders found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
        <nav class="pagination-bar">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <?php if ($i === $page): ?><span class="current"><?= $i ?></span>
                <?php else: ?><a href="<?= e(url('/admin/orders?' . http_build_query(['q' => $search, 'status' => $status, 'page' => $i]))) ?>"><?= $i ?></a><?php endif; ?>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</div>
