<div class="admin-grid-2">
    <div class="surface surface-pad">
        <h5 class="mb-3">All categories</h5>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Name</th><th>Slug</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>
                            <form method="post" action="<?= e(url('admin/categories/' . $category['id'])) ?>" class="d-flex gap-2 align-items-center">
                                <?= csrf_field() ?>
                                <input class="form-control" name="name" value="<?= e($category['name']) ?>" style="min-width:150px">
                                <input class="form-control" name="description" value="<?= e((string) $category['description']) ?>" placeholder="Description" style="min-width:170px">
                                <label class="form-check-label d-flex align-items-center gap-1">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= (int) $category['is_active'] === 1 ? 'checked' : '' ?>> Active
                                </label>
                                <button class="btn btn-sm btn-ghost" type="submit">Save</button>
                            </form>
                        </td>
                        <td><?= e($category['slug']) ?></td>
                        <td><span class="badge-status badge-<?= (int) $category['is_active'] === 1 ? 'success' : 'secondary' ?>"><?= (int) $category['is_active'] === 1 ? 'active' : 'hidden' ?></span></td>
                        <td>
                            <form method="post" action="<?= e(url('admin/categories/' . $category['id'] . '/delete')) ?>" data-confirm="Delete this category?">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($categories === []): ?><tr><td colspan="4" class="text-muted-soft">No categories yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="surface surface-pad">
        <h5 class="mb-3">Add category</h5>
        <form method="post" action="<?= e(url('/admin/categories')) ?>" class="d-grid gap-3">
            <?= csrf_field() ?>
            <div class="form-field"><label for="name">Name</label><input class="form-control" id="name" name="name" required></div>
            <div class="form-field"><label for="description">Description</label><input class="form-control" id="description" name="description"></div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                <label class="form-check-label" for="is_active">Visible in the storefront</label>
            </div>
            <button class="btn btn-primary-grad" type="submit">Add category</button>
        </form>
    </div>
</div>
