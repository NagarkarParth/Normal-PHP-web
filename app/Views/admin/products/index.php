<div class="surface surface-pad">
    <div class="section-head">
        <form class="filter-bar" method="get" action="<?= e(url('/admin/products')) ?>" style="flex:1">
            <div class="form-field"><label for="q">Search</label><input class="form-control" id="q" name="q" value="<?= e($search) ?>" placeholder="Product title"></div>
            <div class="form-field">
                <label for="status">Status</label>
                <select class="form-select" id="status" name="status" data-autosubmit>
                    <option value="">All</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <button class="btn btn-ghost" type="submit">Filter</button>
        </form>
        <a class="btn btn-primary-grad" href="<?= e(url('/admin/products/create')) ?>"><i class="fas fa-plus me-1"></i>New product</a>
    </div>

    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Sales</th><th>File</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><strong><?= e($product['title']) ?></strong></td>
                    <td><?= e((string) ($product['category_name'] ?? '—')) ?></td>
                    <td><?= money((float) $product['price']) ?></td>
                    <td><?= (int) $product['sales_count'] ?></td>
                    <td><?= $product['file_path'] ? '<i class="fas fa-circle-check" style="color:var(--success)"></i>' : '<i class="fas fa-circle-xmark" style="color:var(--danger)"></i>' ?></td>
                    <td><span class="badge-status badge-<?= $product['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($product['status']) ?></span></td>
                    <td class="d-flex gap-2">
                        <a class="btn btn-sm btn-ghost" href="<?= e(url('admin/products/' . $product['id'] . '/edit')) ?>">Edit</a>
                        <form method="post" action="<?= e(url('admin/products/' . $product['id'] . '/delete')) ?>" data-confirm="Delete this product?">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($products === []): ?><tr><td colspan="7" class="text-muted-soft">No products found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
        <nav class="pagination-bar">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <?php if ($i === $page): ?><span class="current"><?= $i ?></span>
                <?php else: ?><a href="<?= e(url('/admin/products?' . http_build_query(['q' => $search, 'status' => $status, 'page' => $i]))) ?>"><?= $i ?></a><?php endif; ?>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</div>
