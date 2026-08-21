<section class="section container">
    <div class="section-head"><div><h2>Orders &amp; downloads</h2><p>Every purchase stays in your account.</p></div></div>

    <?php if ($purchases !== []): ?>
        <h5 class="mb-3">Your downloads</h5>
        <div class="list-rows mb-5">
            <?php foreach ($purchases as $purchase): ?>
                <div class="list-row">
                    <?php if (!empty($purchase['thumbnail'])): ?>
                        <img src="<?= e(uploaded((string) $purchase['thumbnail'])) ?>" alt="<?= e($purchase['title']) ?>">
                    <?php else: ?>
                        <div class="thumb-fallback"><i class="fas fa-cube"></i></div>
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <strong><?= e($purchase['title']) ?></strong>
                        <div class="text-muted-soft" style="font-size:.82rem">
                            <?= e((string) $purchase['order_number']) ?> · bought <?= e(date('d M Y', strtotime((string) $purchase['paid_at']))) ?>
                            <?php if ($purchase['expires_at'] !== null): ?>
                                · link valid till <?= e(date('d M Y', strtotime((string) $purchase['expires_at']))) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a class="btn btn-sm btn-primary-grad" href="<?= e(url('downloads/' . $purchase['download_token'])) ?>"><i class="fas fa-download me-1"></i>Download</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h5 class="mb-3">Order history</h5>
    <?php if ($orders === []): ?>
        <div class="empty-state surface">
            <i class="fas fa-receipt"></i>
            You have not placed an order yet.
            <div class="mt-3"><a class="btn btn-primary-grad" href="<?= e(url('/products')) ?>">Explore products</a></div>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Order</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong><?= e((string) $order['order_number']) ?></strong></td>
                        <td><?= e(date('d M Y', strtotime((string) $order['created_at']))) ?></td>
                        <td><?= (int) $order['items_count'] ?></td>
                        <td><?= money((float) $order['total']) ?></td>
                        <td><span class="badge-status badge-<?= e(order_status_color((string) $order['payment_status'])) ?>"><?= e((string) $order['payment_status']) ?></span></td>
                        <td><a class="btn btn-sm btn-ghost" href="<?= e(url('orders/' . $order['id'])) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
