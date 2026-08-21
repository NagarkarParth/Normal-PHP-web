<section class="section container">
    <div class="section-head"><div><h2>Your cart</h2><p><?= count($totals['items']) ?> item<?= count($totals['items']) === 1 ? '' : 's' ?> ready for checkout.</p></div></div>

    <?php if ($totals['items'] === []): ?>
        <div class="empty-state surface">
            <i class="fas fa-cart-shopping"></i>
            Your cart is empty.
            <div class="mt-3"><a class="btn btn-primary-grad" href="<?= e(url('/products')) ?>">Explore products</a></div>
        </div>
    <?php else: ?>
        <div class="detail-grid">
            <div class="list-rows">
                <?php foreach ($totals['items'] as $item): ?>
                    <div class="list-row">
                        <?php if (!empty($item['thumbnail'])): ?>
                            <img src="<?= e(uploaded((string) $item['thumbnail'])) ?>" alt="<?= e($item['title']) ?>">
                        <?php else: ?>
                            <div class="thumb-fallback"><i class="fas fa-cube"></i></div>
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <a href="<?= e(url('product/' . $item['slug'])) ?>"><strong><?= e($item['title']) ?></strong></a>
                            <div class="text-muted-soft" style="font-size:.83rem"><?= e((string) ($item['category_name'] ?? 'Digital product')) ?></div>
                        </div>
                        <div class="text-end">
                            <div class="price"><?= money((float) $item['price']) ?></div>
                            <form method="post" action="<?= e(url('/cart/remove')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                                <button class="btn btn-sm btn-link text-danger p-0" type="submit">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <aside>
                <div class="surface surface-pad">
                    <h5 class="mb-3">Order summary</h5>
                    <div class="summary-line"><span>Subtotal</span><span><?= money($totals['subtotal']) ?></span></div>
                    <?php if ($totals['coupon'] !== null): ?>
                        <div class="summary-line"><span>Discount (<?= e((string) $totals['coupon']['code']) ?>)</span><span>-<?= money($totals['discount']) ?></span></div>
                    <?php endif; ?>
                    <div class="summary-line"><span><?= e((string) setting('tax_label', 'Tax')) ?> (<?= rtrim(rtrim(number_format($totals['tax_rate'], 2), '0'), '.') ?>%)</span><span><?= money($totals['tax']) ?></span></div>
                    <div class="summary-line total"><span>Total</span><span><?= money($totals['total']) ?></span></div>

                    <?php if ($totals['coupon'] === null): ?>
                        <form method="post" action="<?= e(url('/cart/coupon')) ?>" class="d-flex gap-2 mt-3">
                            <?= csrf_field() ?>
                            <input class="form-control" type="text" name="code" placeholder="Coupon code" aria-label="Coupon code">
                            <button class="btn btn-ghost" type="submit">Apply</button>
                        </form>
                    <?php else: ?>
                        <form method="post" action="<?= e(url('/cart/coupon/remove')) ?>" class="mt-3">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-ghost w-100" type="submit">Remove coupon <?= e((string) $totals['coupon']['code']) ?></button>
                        </form>
                    <?php endif; ?>

                    <a class="btn btn-lg btn-primary-grad w-100 mt-3" href="<?= e(url('/checkout')) ?>">Proceed to checkout</a>
                    <a class="btn btn-sm btn-link w-100 mt-2" href="<?= e(url('/products')) ?>">Continue shopping</a>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</section>
