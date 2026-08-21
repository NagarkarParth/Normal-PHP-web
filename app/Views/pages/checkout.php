<section class="section container">
    <div class="section-head"><div><h2>Checkout</h2><p>Secure payment, instant delivery.</p></div></div>

    <div class="detail-grid">
        <div class="surface surface-pad">
            <h5 class="mb-3">Billing details</h5>
            <div class="form-grid cols-2">
                <div class="form-field">
                    <label>Name</label>
                    <input class="form-control" value="<?= e((string) $user['name']) ?>" readonly>
                </div>
                <div class="form-field">
                    <label>Email</label>
                    <input class="form-control" value="<?= e((string) $user['email']) ?>" readonly>
                </div>
            </div>
            <p class="text-muted-soft mt-2" style="font-size:.83rem">Downloads and the invoice are sent to this address. Update it from your <a href="<?= e(url('/profile')) ?>">profile</a>.</p>

            <h5 class="mt-4 mb-3">Payment method</h5>
            <div class="list-row">
                <div class="thumb-fallback"><i class="fas fa-credit-card"></i></div>
                <div>
                    <strong><?= e($gateway->label()) ?></strong>
                    <div class="text-muted-soft" style="font-size:.83rem">
                        <?php if ($gateway->key() === 'offline'): ?>
                            No live gateway keys are configured yet, so payments are recorded manually for testing.
                        <?php else: ?>
                            You will be redirected to complete the payment securely.
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <h5 class="mt-4 mb-3">Items</h5>
            <div class="list-rows">
                <?php foreach ($totals['items'] as $item): ?>
                    <div class="list-row">
                        <?php if (!empty($item['thumbnail'])): ?>
                            <img src="<?= e(uploaded((string) $item['thumbnail'])) ?>" alt="<?= e($item['title']) ?>">
                        <?php else: ?>
                            <div class="thumb-fallback"><i class="fas fa-cube"></i></div>
                        <?php endif; ?>
                        <div class="flex-grow-1"><strong><?= e($item['title']) ?></strong></div>
                        <div class="price"><?= money((float) $item['price']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <aside class="buy-box">
            <div class="surface surface-pad">
                <h5 class="mb-3">Order summary</h5>
                <div class="summary-line"><span>Subtotal</span><span><?= money($totals['subtotal']) ?></span></div>
                <?php if ($totals['coupon'] !== null): ?>
                    <div class="summary-line"><span>Discount (<?= e((string) $totals['coupon']['code']) ?>)</span><span>-<?= money($totals['discount']) ?></span></div>
                <?php endif; ?>
                <div class="summary-line"><span><?= e((string) setting('tax_label', 'Tax')) ?> (<?= rtrim(rtrim(number_format($totals['tax_rate'], 2), '0'), '.') ?>%)</span><span><?= money($totals['tax']) ?></span></div>
                <div class="summary-line total"><span>Payable</span><span><?= money($totals['total']) ?></span></div>

                <?php if ($totals['coupon'] === null): ?>
                    <form method="post" action="<?= e(url('/cart/coupon')) ?>" class="d-flex gap-2 mt-3">
                        <?= csrf_field() ?>
                        <input class="form-control" type="text" name="code" placeholder="Coupon code">
                        <button class="btn btn-ghost" type="submit">Apply</button>
                    </form>
                <?php endif; ?>

                <form method="post" action="<?= e(url('/checkout')) ?>" class="mt-3">
                    <?= csrf_field() ?>
                    <button class="btn btn-lg btn-primary-grad w-100" type="submit">Pay <?= money($totals['total']) ?></button>
                </form>
                <a class="btn btn-sm btn-link w-100 mt-2" href="<?= e(url('/cart')) ?>">Back to cart</a>
            </div>
        </aside>
    </div>
</section>
