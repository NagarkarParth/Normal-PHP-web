<div class="invoice-sheet">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h2 style="margin:0"><?= e($branding['site_name']) ?></h2>
            <div style="color:#6b7280;font-size:.86rem"><?= e($branding['support_email']) ?></div>
        </div>
        <div style="text-align:right">
            <h3 style="margin:0">INVOICE</h3>
            <div style="font-size:.86rem"><?= e((string) $order['order_number']) ?></div>
            <div style="font-size:.86rem;color:#6b7280"><?= e(date('d M Y', strtotime((string) $order['created_at']))) ?></div>
        </div>
    </div>

    <hr style="margin:22px 0">

    <div class="d-flex justify-content-between" style="gap:24px">
        <div>
            <strong>Billed to</strong>
            <div><?= e((string) $order['customer_name']) ?></div>
            <div style="color:#6b7280"><?= e((string) $order['customer_email']) ?></div>
        </div>
        <div style="text-align:right">
            <strong>Payment</strong>
            <div style="text-transform:capitalize"><?= e((string) $order['gateway']) ?> · <?= e((string) $order['payment_status']) ?></div>
            <?php if ($order['transaction_id']): ?><div style="color:#6b7280"><?= e((string) $order['transaction_id']) ?></div><?php endif; ?>
        </div>
    </div>

    <table>
        <thead><tr><th>Product</th><th style="text-align:right">Amount</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr><td><?= e($item['title']) ?></td><td style="text-align:right"><?= money((float) $item['price']) ?></td></tr>
            <?php endforeach; ?>
            <tr><td style="text-align:right">Subtotal</td><td style="text-align:right"><?= money((float) $order['subtotal']) ?></td></tr>
            <?php if ((float) $order['discount'] > 0): ?>
                <tr><td style="text-align:right">Discount <?= $order['coupon_code'] ? '(' . e((string) $order['coupon_code']) . ')' : '' ?></td><td style="text-align:right">-<?= money((float) $order['discount']) ?></td></tr>
            <?php endif; ?>
            <tr><td style="text-align:right"><?= e((string) setting('tax_label', 'Tax')) ?> @ <?= rtrim(rtrim(number_format((float) $order['tax_rate'], 2), '0'), '.') ?>%</td><td style="text-align:right"><?= money((float) $order['tax']) ?></td></tr>
            <tr><td style="text-align:right"><strong>Total</strong></td><td style="text-align:right"><strong><?= money((float) $order['total']) ?></strong></td></tr>
        </tbody>
    </table>

    <p style="color:#6b7280;font-size:.82rem;margin-top:24px">This is a computer generated invoice for a digital product and does not require a signature.</p>

    <div class="no-print" style="margin-top:24px;display:flex;gap:10px">
        <button class="btn btn-primary-grad" onclick="window.print()">Print / Save as PDF</button>
        <a class="btn btn-ghost" href="<?= e(url('orders/' . $order['id'])) ?>">Back to order</a>
    </div>
</div>
