<section class="section container">
    <div class="section-head">
        <div>
            <h2>All products</h2>
            <p><?= (int) $total ?> product<?= $total === 1 ? '' : 's' ?> available<?= $filters['q'] !== '' ? ' for “' . e($filters['q']) . '”' : '' ?>.</p>
        </div>
    </div>

    <form class="surface surface-pad filter-bar mb-4" method="get" action="<?= e(url('/products')) ?>">
        <div class="form-field">
            <label for="q">Search</label>
            <input class="form-control" type="search" id="q" name="q" value="<?= e($filters['q']) ?>" placeholder="Search templates, kits, scripts…">
        </div>
        <div class="form-field">
            <label for="category">Category</label>
            <select class="form-select" id="category" name="category">
                <option value="">All categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e($category['slug']) ?>" <?= $filters['category'] === $category['slug'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="min_price">Min price</label>
            <input class="form-control" type="number" min="0" step="1" id="min_price" name="min_price" value="<?= e($filters['min_price']) ?>" placeholder="0">
        </div>
        <div class="form-field">
            <label for="max_price">Max price</label>
            <input class="form-control" type="number" min="0" step="1" id="max_price" name="max_price" value="<?= e($filters['max_price']) ?>" placeholder="9999">
        </div>
        <div class="form-field">
            <label for="sort">Sort</label>
            <select class="form-select" id="sort" name="sort">
                <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Newest</option>
                <option value="popular" <?= $filters['sort'] === 'popular' ? 'selected' : '' ?>>Popularity</option>
                <option value="price_asc" <?= $filters['sort'] === 'price_asc' ? 'selected' : '' ?>>Price: low to high</option>
                <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>>Price: high to low</option>
                <option value="title" <?= $filters['sort'] === 'title' ? 'selected' : '' ?>>A → Z</option>
            </select>
        </div>
        <button class="btn btn-primary-grad btn-block-mobile" type="submit"><i class="fas fa-filter me-1"></i>Apply</button>
    </form>

    <?php if ($products === []): ?>
        <div class="empty-state surface">
            <i class="fas fa-magnifying-glass"></i>
            No products match those filters.
            <div class="mt-3"><a class="btn btn-sm btn-ghost" href="<?= e(url('/products')) ?>">Clear filters</a></div>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $item): require BASE_PATH . '/app/Views/partials/product-card.php'; endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($pages > 1): ?>
        <?php
        $query = $filters;
        ?>
        <nav class="pagination-bar">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <?php $query['page'] = $i; ?>
                <?php if ($i === $page): ?>
                    <span class="current"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= e(url('/products?' . http_build_query($query))) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</section>
