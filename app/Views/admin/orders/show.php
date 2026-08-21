<div class="admin-grid-2">
    <div class="surface surface-pad">
        <div class="section-head"><div><h2 style="font-size:1.05rem">Items</h2></div><a class="btn btn-sm btn-ghost" href="<?= e(url('/admin/orders')) ?>">Back</a></div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Product</th><th>Price</th><th>Downloads</th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr><td><?= e((string) $item['title']) ?></td><td><?= money((float) $item['price']) ?></td><td><?= (int) $item['download_count'] ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <div class="summary-line"><span>Subtotal</span><span><?= money((float) $order['subtotal']) ?></span></div>
            <?php if ((float) $order['discount'] > 0): ?>
                <div class="summary-line"><span>Discount <?= $order['coupon_code'] ? '(' . e((string) $order['coupon_code']) . ')' : '' ?></span><span>-<?= money((float) $order['discount']) ?></span></div>
            <?php endif; ?>
            <div class="summary-line"><span><?= e((string) setting('tax_label', 'Tax')) ?></span><span><?= money((float) $order['tax']) ?></span></div>
            <div class="summary-line total"><span>Total</span><span><?= money((float) $order['total']) ?></span></div>
        </div>
    </div>

    <div class="d-grid gap-3">
        <div class="surface surface-pad">
            <h5 class="mb-3">Order <?= e((string) $order['order_number']) ?></h5>
            <dl class="text-muted-soft" style="font-size:.87rem">
                <dt>Customer</dt><dd><?= e((string) $order['customer_name']) ?> · <?= e((string) $order['customer_email']) ?></dd>
                <dt>Status</dt><dd><span class="badge-status badge-<?= e(order_status_color((string) $order['payment_status'])) ?>"><?= e((string) $order['payment_status']) ?></span></dd>
                <dt>Gateway</dt><dd class="text-capitalize"><?= e((string) $order['gateway']) ?></dd>
                <dt>Transaction ID</dt><dd><?= e((string) ($order['transaction_id'] ?? '—')) ?></dd>
                <dt>Placed</dt><dd><?= e(date('d M Y, H:i', strtotime((string) $order['created_at']))) ?></dd>
                <?php if ($order['paid_at']): ?><dt>Paid</dt><dd><?= e(date('d M Y, H:i', strtotime((string) $order['paid_at']))) ?></dd><?php endif; ?>
                <?php if (!empty($order['refund_note'])): ?><dt>Refund note</dt><dd><?= e((string) $order['refund_note']) ?></dd><?php endif; ?>
            </dl>
        </div>

        <?php if ($order['payment_status'] !== 'paid' && $order['payment_status'] !== 'refunded'): ?>
            <div class="surface surface-pad">
                <h5 class="mb-2">Mark as paid</h5>
                <p class="text-muted-soft" style="font-size:.85rem">Use this for bank transfers or manual settlements.</p>
                <form method="post" action="<?= e(url('admin/orders/' . $order['id'] . '/paid')) ?>" class="d-grid gap-2">
                    <?= csrf_field() ?>
                    <input class="form-control" name="transaction_id" placeholder="Transaction reference (optional)">
                    <button class="btn btn-primary-grad" type="submit">Mark paid &amp; notify</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($order['payment_status'] === 'paid'): ?>
            <div class="surface surface-pad">
                <h5 class="mb-2">Refund</h5>
                <form method="post" action="<?= e(url('admin/orders/' . $order['id'] . '/refund')) ?>" class="d-grid gap-2" data-confirm="Record this refund?">
                    <?= csrf_field() ?>
                    <input class="form-control" name="amount" type="number" step="0.01" min="0" max="<?= e((string) $order['total']) ?>" value="<?= e((string) $order['total']) ?>">
                    <input class="form-control" name="note" placeholder="Internal note">
                    <button class="btn btn-outline-danger" type="submit">Record refund</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
