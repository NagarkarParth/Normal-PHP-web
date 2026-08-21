<section class="section container">
    <nav class="mb-3" style="font-size:.85rem">
        <a href="<?= e(url('/products')) ?>" class="text-muted-soft"><i class="fas fa-arrow-left me-1"></i>Back to products</a>
    </nav>

    <div class="detail-grid">
        <div>
            <?php $cover = $product['thumbnail'] ?: ($screenshots[0]['path'] ?? ''); ?>
            <?php if ($cover !== ''): ?>
                <img id="galleryMain" class="gallery-main" src="<?= e(uploaded((string) $cover)) ?>" alt="<?= e($product['title']) ?>">
            <?php else: ?>
                <div class="gallery-main d-grid" style="place-items:center"><i class="fas fa-cube fa-3x" style="color:var(--brand-500)"></i></div>
            <?php endif; ?>

            <?php if ($screenshots !== []): ?>
                <div class="gallery-thumbs">
                    <?php foreach ($screenshots as $index => $shot): ?>
                        <img src="<?= e(uploaded((string) $shot['path'])) ?>" data-full="<?= e(uploaded((string) $shot['path'])) ?>" class="<?= $index === 0 ? 'active' : '' ?>" alt="Screenshot <?= $index + 1 ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="surface surface-pad mt-4">
                <h1 style="font-size:1.6rem" class="mb-2"><?= e($product['title']) ?></h1>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php if (!empty($product['category_name'])): ?><span class="chip chip-brand"><?= e($product['category_name']) ?></span><?php endif; ?>
                    <span class="chip"><i class="fas fa-download"></i> <?= (int) $product['sales_count'] ?> sales</span>
                    <span class="chip"><i class="fas fa-eye"></i> <?= (int) $product['views'] ?> views</span>
                </div>
                <p class="text-muted-soft"><?= nl2br(e((string) $product['description'])) ?></p>
                <?php if (!empty($product['demo_url'])): ?>
                    <a class="btn btn-sm btn-ghost mt-2" target="_blank" rel="noopener" href="<?= e((string) $product['demo_url']) ?>"><i class="fas fa-up-right-from-square me-1"></i>Live demo</a>
                <?php endif; ?>
            </div>
        </div>

        <aside class="buy-box">
            <div class="surface surface-pad">
                <div class="d-flex align-items-baseline gap-2">
                    <span class="price" style="font-size:1.9rem"><?= money((float) $product['price']) ?></span>
                    <?php if (!empty($product['compare_at_price'])): ?>
                        <span class="price-compare"><?= money((float) $product['compare_at_price']) ?></span>
                    <?php endif; ?>
                </div>
                <p class="text-muted-soft mt-2" style="font-size:.86rem">One-time payment · lifetime access · instant download</p>

                <form method="post" action="<?= e(url('/cart/add')) ?>" class="d-grid gap-2 mt-3">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                    <button class="btn btn-lg btn-primary-grad" type="submit" name="buy_now" value="1">Buy now</button>
                    <button class="btn btn-lg btn-ghost" type="submit"><i class="fas fa-cart-plus me-1"></i>Add to cart</button>
                </form>

                <ul class="list-unstyled mt-4 mb-0 text-muted-soft" style="font-size:.87rem">
                    <li class="mb-2"><i class="fas fa-shield-halved me-2" style="color:var(--brand-500)"></i>Secure private download link</li>
                    <li class="mb-2"><i class="fas fa-file-invoice me-2" style="color:var(--brand-500)"></i>Invoice with tax breakdown</li>
                    <li><i class="fas fa-life-ring me-2" style="color:var(--brand-500)"></i>Support tickets included</li>
                </ul>
            </div>
        </aside>
    </div>

    <?php if ($related !== []): ?>
        <div class="section-head mt-5"><div><h2>Related products</h2><p>Buyers of this product also picked these.</p></div></div>
        <div class="product-grid">
            <?php foreach ($related as $item): require BASE_PATH . '/app/Views/partials/product-card.php'; endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<div class="mobile-buy-bar">
    <div>
        <div class="price"><?= money((float) $product['price']) ?></div>
        <div class="text-muted-soft" style="font-size:.72rem">Instant download</div>
    </div>
    <form method="post" action="<?= e(url('/cart/add')) ?>" class="ms-auto d-flex gap-2">
        <?= csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
        <button class="btn btn-ghost" type="submit"><i class="fas fa-cart-plus"></i></button>
        <button class="btn btn-primary-grad" type="submit" name="buy_now" value="1">Buy now</button>
    </form>
</div>
