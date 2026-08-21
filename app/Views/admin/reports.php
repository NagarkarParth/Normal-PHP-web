<div class="stat-grid">
    <div class="stat-card"><div class="label">Lifetime revenue</div><div class="value"><?= money($stats['revenue_total']) ?></div><div class="trend"><?= (int) $stats['orders_paid'] ?> paid orders</div></div>
    <div class="stat-card"><div class="label">This month</div><div class="value"><?= money($stats['revenue_month']) ?></div><div class="trend">Today <?= money($stats['revenue_today']) ?></div></div>
    <div class="stat-card"><div class="label"><?= e((string) setting('tax_label', 'Tax')) ?> collected</div><div class="value"><?= money($stats['tax_total']) ?></div><div class="trend">Remitted separately</div></div>
    <div class="stat-card"><div class="label">Pending orders</div><div class="value"><?= (int) $stats['orders_pending'] ?></div><div class="trend">of <?= (int) $stats['orders_total'] ?> total</div></div>
</div>

<div class="admin-grid-2">
    <div class="surface surface-pad">
        <h5 class="mb-3">Daily sales (30 days)</h5>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Day</th><th>Orders</th><th>Revenue</th><th>Tax</th></tr></thead>
                <tbody>
                <?php foreach (array_reverse($daily) as $row): ?>
                    <tr><td><?= e(date('d M Y', strtotime((string) $row['day']))) ?></td><td><?= (int) $row['orders'] ?></td><td><?= money((float) $row['revenue']) ?></td><td><?= money((float) $row['tax']) ?></td></tr>
                <?php endforeach; ?>
                <?php if ($daily === []): ?><tr><td colspan="4" class="text-muted-soft">No sales in this window.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="surface surface-pad">
        <h5 class="mb-3">Monthly summary (12 months)</h5>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Month</th><th>Orders</th><th>Revenue</th><th>Discounts</th><th>Tax</th></tr></thead>
                <tbody>
                <?php foreach (array_reverse($monthly) as $row): ?>
                    <tr>
                        <td><?= e(date('M Y', strtotime($row['month'] . '-01'))) ?></td>
                        <td><?= (int) $row['orders'] ?></td>
                        <td><?= money((float) $row['revenue']) ?></td>
                        <td><?= money((float) $row['discount']) ?></td>
                        <td><?= money((float) $row['tax']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($monthly === []): ?><tr><td colspan="5" class="text-muted-soft">No sales yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="surface surface-pad">
    <h5 class="mb-3">Top selling products</h5>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>#</th><th>Product</th><th>Units</th><th>Revenue</th></tr></thead>
            <tbody>
            <?php foreach ($topProducts as $index => $product): ?>
                <tr><td><?= $index + 1 ?></td><td><?= e((string) $product['title']) ?></td><td><?= (int) $product['units'] ?></td><td><?= money((float) $product['revenue']) ?></td></tr>
            <?php endforeach; ?>
            <?php if ($topProducts === []): ?><tr><td colspan="4" class="text-muted-soft">No sales yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
