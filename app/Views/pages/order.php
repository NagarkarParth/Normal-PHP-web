<section class="section container">
    <a class="text-muted-soft" href="<?= e(url('/orders')) ?>" style="font-size:.85rem"><i class="fas fa-arrow-left me-1"></i>All orders</a>

    <div class="section-head mt-2">
        <div>
            <h2>Order <?= e((string) $order['order_number']) ?></h2>
            <p>Placed <?= e(date('d M Y, H:i', strtotime((string) $order['created_at']))) ?> · <span class="badge-status badge-<?= e(order_status_color((string) $order['payment_status'])) ?>"><?= e((string) $order['payment_status']) ?></span></p>
        </div>
        <a class="btn btn-sm btn-ghost" href="<?= e(url('orders/' . $order['id'] . '/invoice')) ?>" target="_blank"><i class="fas fa-file-invoice me-1"></i>Invoice</a>
    </div>

    <div class="detail-grid">
        <div class="list-rows">
            <?php foreach ($items as $item): ?>
                <div class="list-row">
                    <?php if (!empty($item['thumbnail'])): ?>
                        <img src="<?= e(uploaded((string) $item['thumbnail'])) ?>" alt="<?= e($item['title']) ?>">
                    <?php else: ?>
                        <div class="thumb-fallback"><i class="fas fa-cube"></i></div>
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <strong><?= e($item['title']) ?></strong>
                        <div class="text-muted-soft" style="font-size:.82rem">
                            <?= money((float) $item['price']) ?>
                            <?php if ((int) $item['download_count'] > 0): ?> · downloaded <?= (int) $item['download_count'] ?>×<?php endif; ?>
                        </div>
                    </div>
                    <?php if ($order['payment_status'] === 'paid'): ?>
                        <a class="btn btn-sm btn-primary-grad" href="<?= e(url('downloads/' . $item['download_token'])) ?>"><i class="fas fa-download me-1"></i>Download</a>
                    <?php else: ?>
                        <span class="chip">Locked</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <aside>
            <div class="surface surface-pad">
                <h5 class="mb-3">Payment</h5>
                <div class="summary-line"><span>Subtotal</span><span><?= money((float) $order['subtotal']) ?></span></div>
                <?php if ((float) $order['discount'] > 0): ?>
                    <div class="summary-line"><span>Discount <?= $order['coupon_code'] ? '(' . e((string) $order['coupon_code']) . ')' : '' ?></span><span>-<?= money((float) $order['discount']) ?></span></div>
                <?php endif; ?>
                <div class="summary-line"><span><?= e((string) setting('tax_label', 'Tax')) ?> (<?= rtrim(rtrim(number_format((float) $order['tax_rate'], 2), '0'), '.') ?>%)</span><span><?= money((float) $order['tax']) ?></span></div>
                <div class="summary-line total"><span>Total</span><span><?= money((float) $order['total']) ?></span></div>

                <dl class="mt-3 mb-0 text-muted-soft" style="font-size:.85rem">
                    <dt>Gateway</dt><dd class="text-capitalize"><?= e((string) $order['gateway']) ?></dd>
                    <?php if ($order['transaction_id']): ?><dt>Transaction ID</dt><dd><?= e((string) $order['transaction_id']) ?></dd><?php endif; ?>
                    <?php if ($order['paid_at']): ?><dt>Paid at</dt><dd><?= e(date('d M Y, H:i', strtotime((string) $order['paid_at']))) ?></dd><?php endif; ?>
                </dl>

                <?php if ($order['payment_status'] === 'pending'): ?>
                    <a class="btn btn-primary-grad w-100 mt-3" href="<?= e(url('/checkout')) ?>">Complete payment</a>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</section>
