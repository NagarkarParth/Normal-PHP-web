<?php $isEdit = $product !== null; ?>
<form class="surface surface-pad" method="post" enctype="multipart/form-data"
      action="<?= e(url($isEdit ? 'admin/products/' . $product['id'] : '/admin/products')) ?>">
    <?= csrf_field() ?>

    <div class="form-grid cols-2">
        <div class="form-field span-2">
            <label for="title">Title</label>
            <input class="form-control" id="title" name="title" value="<?= e((string) ($product['title'] ?? old('title'))) ?>" required>
        </div>

        <div class="form-field">
            <label for="category_id">Category</label>
            <select class="form-select" id="category_id" name="category_id">
                <option value="">Uncategorised</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-field">
            <label for="status">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="active" <?= ($product['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($product['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <div class="form-field">
            <label for="price">Price (<?= e((string) setting('currency', 'INR')) ?>)</label>
            <input class="form-control" id="price" name="price" type="number" step="0.01" min="0" value="<?= e((string) ($product['price'] ?? '')) ?>" required>
        </div>

        <div class="form-field">
            <label for="compare_at_price">Compare at price (optional)</label>
            <input class="form-control" id="compare_at_price" name="compare_at_price" type="number" step="0.01" min="0" value="<?= e((string) ($product['compare_at_price'] ?? '')) ?>">
        </div>

        <div class="form-field span-2">
            <label for="short_description">Short description</label>
            <input class="form-control" id="short_description" name="short_description" maxlength="255" value="<?= e((string) ($product['short_description'] ?? '')) ?>">
        </div>

        <div class="form-field span-2">
            <label for="description">Full description</label>
            <textarea class="form-control" id="description" name="description" rows="6"><?= e((string) ($product['description'] ?? '')) ?></textarea>
        </div>

        <div class="form-field">
            <label for="demo_url">Demo URL (optional)</label>
            <input class="form-control" id="demo_url" name="demo_url" value="<?= e((string) ($product['demo_url'] ?? '')) ?>">
        </div>

        <div class="form-field">
            <label for="thumbnail">Thumbnail image</label>
            <input class="form-control" id="thumbnail" name="thumbnail" type="file" accept="image/*">
            <?php if (!empty($product['thumbnail'])): ?>
                <img src="<?= e(uploaded((string) $product['thumbnail'])) ?>" alt="thumbnail" style="max-height:70px;border-radius:10px;margin-top:8px">
            <?php endif; ?>
        </div>

        <div class="form-field span-2">
            <label for="product_file">Digital file (stored outside the web root)</label>
            <input class="form-control" id="product_file" name="product_file" type="file">
            <?php if (!empty($product['file_name'])): ?>
                <small class="text-muted-soft">Current: <?= e((string) $product['file_name']) ?> (<?= number_format(((int) $product['file_size']) / 1024, 0) ?> KB)</small>
            <?php endif; ?>
        </div>

        <div class="form-field span-2">
            <label for="screenshots">Screenshots (multiple)</label>
            <input class="form-control" id="screenshots" name="screenshots[]" type="file" accept="image/*" multiple>
        </div>

        <div class="span-2 form-check">
            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_featured">Show in featured products</label>
        </div>

        <div class="span-2 d-flex gap-2">
            <button class="btn btn-primary-grad" type="submit"><?= $isEdit ? 'Save changes' : 'Create product' ?></button>
            <a class="btn btn-ghost" href="<?= e(url('/admin/products')) ?>">Cancel</a>
        </div>
    </div>
</form>

<?php if ($isEdit && $screenshots !== []): ?>
    <div class="surface surface-pad">
        <h5 class="mb-3">Screenshots</h5>
        <div class="d-flex flex-wrap gap-3">
            <?php foreach ($screenshots as $shot): ?>
                <div class="text-center">
                    <img src="<?= e(uploaded((string) $shot['path'])) ?>" alt="screenshot" style="width:140px;height:90px;object-fit:cover;border-radius:12px">
                    <form method="post" action="<?= e(url('admin/products/' . $product['id'] . '/screenshots/' . $shot['id'] . '/delete')) ?>" class="mt-1">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-link text-danger p-0" type="submit">Remove</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
