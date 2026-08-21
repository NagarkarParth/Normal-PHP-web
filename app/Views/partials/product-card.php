<?php /** @var array $item */ ?>
<article class="product-card">
    <a href="<?= e(url('product/' . $item['slug'])) ?>" aria-label="<?= e($item['title']) ?>">
        <?php if (!empty($item['thumbnail'])): ?>
            <img class="product-thumb" src="<?= e(uploaded((string) $item['thumbnail'])) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
        <?php else: ?>
            <div class="product-thumb"><i class="fas fa-cube"></i></div>
        <?php endif; ?>
    </a>
    <div class="product-body">
        <?php if (!empty($item['category_name'])): ?>
            <span class="chip chip-brand"><?= e($item['category_name']) ?></span>
        <?php endif; ?>
        <h3 class="product-title"><a href="<?= e(url('product/' . $item['slug'])) ?>"><?= e($item['title']) ?></a></h3>
        <p class="product-desc"><?= e(excerpt((string) ($item['short_description'] ?? $item['description'] ?? ''), 92)) ?></p>
        <div class="product-meta">
            <div>
                <span class="price"><?= money((float) $item['price']) ?></span>
                <?php if (!empty($item['compare_at_price'])): ?>
                    <span class="price-compare"><?= money((float) $item['compare_at_price']) ?></span>
                <?php endif; ?>
            </div>
            <form method="post" action="<?= e(url('/cart/add')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                <button class="btn btn-sm btn-primary-grad" type="submit"><i class="fas fa-cart-plus"></i></button>
            </form>
        </div>
    </div>
</article>
