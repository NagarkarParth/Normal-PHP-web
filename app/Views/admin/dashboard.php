<?php $max = max(1, ...array_map(static fn (array $row): float => (float) $row['revenue'], $daily ?: [['revenue' => 1]])); ?>

<div class="stat-grid">
    <div class="stat-card"><div class="label">Revenue today</div><div class="value"><?= money($stats['revenue_today']) ?></div><div class="trend"><?= (int) $stats['orders_paid'] ?> paid orders all time</div></div>
    <div class="stat-card"><div class="label">Revenue this month</div><div class="value"><?= money($stats['revenue_month']) ?></div><div class="trend">Lifetime <?= money($stats['revenue_total']) ?></div></div>
    <div class="stat-card"><div class="label"><?= e((string) setting('tax_label', 'Tax')) ?> collected</div><div class="value"><?= money($stats['tax_total']) ?></div><div class="trend">Across all paid orders</div></div>
    <div class="stat-card"><div class="label">Open tickets</div><div class="value"><?= (int) $stats['tickets_open'] ?></div><div class="trend"><?= (int) $stats['users_total'] ?> customers · <?= (int) $stats['products_active'] ?> live products</div></div>
</div>

<div class="admin-grid-2">
    <div class="surface surface-pad">
        <div class="section-head"><div><h2 style="font-size:1.05rem">Revenue, last 14 days</h2></div></div>
        <?php if ($daily === []): ?>
            <p class="text-muted-soft mb-0">No paid orders yet.</p>
        <?php else: ?>
            <div class="bar-chart">
                <?php foreach ($daily as $row): ?>
                    <div class="bar" style="height: <?= max(3, (int) round((float) $row['revenue'] / $max * 100)) ?>%" title="<?= e((string) $row['day']) ?>: <?= money((float) $row['revenue']) ?>">
                        <span><?= e(date('d/m', strtotime((string) $row['day']))) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="height:26px"></div>
        <?php endif; ?>
    </div>

    <div class="surface surface-pad">
        <div class="section-head"><div><h2 style="font-size:1.05rem">Top sellers</h2></div></div>
        <?php if ($topProducts === []): ?>
            <p class="text-muted-soft mb-0">No sales yet.</p>
        <?php else: ?>
            <?php foreach ($topProducts as $product): ?>
                <div class="summary-line"><span><?= e($product['title']) ?></span><span><?= (int) $product['units'] ?> · <?= money((float) $product['revenue']) ?></span></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="surface surface-pad">
    <div class="section-head"><div><h2 style="font-size:1.05rem">Recent orders</h2></div><a class="btn btn-sm btn-ghost" href="<?= e(url('/admin/orders')) ?>">All orders</a></div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Placed</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($recentOrders as $order): ?>
                <tr>
                    <td><?= e((string) $order['order_number']) ?></td>
                    <td><?= e((string) ($order['user_name'] ?? $order['customer_email'])) ?></td>
                    <td><?= money((float) $order['total']) ?></td>
                    <td><span class="badge-status badge-<?= e(order_status_color((string) $order['payment_status'])) ?>"><?= e((string) $order['payment_status']) ?></span></td>
                    <td><?= e(date('d M, H:i', strtotime((string) $order['created_at']))) ?></td>
                    <td><a class="btn btn-sm btn-ghost" href="<?= e(url('admin/orders/' . $order['id'])) ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($recentOrders === []): ?><tr><td colspan="6" class="text-muted-soft">No orders yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
