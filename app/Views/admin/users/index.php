<div class="admin-grid-2">
    <div class="surface surface-pad">
        <form class="filter-bar mb-3" method="get" action="<?= e(url('/admin/users')) ?>">
            <div class="form-field"><label for="q">Search</label><input class="form-control" id="q" name="q" value="<?= e($search) ?>" placeholder="Name or email"></div>
            <button class="btn btn-ghost" type="submit">Search</button>
        </form>

        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Orders</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($users as $account): ?>
                    <tr>
                        <td><strong><?= e((string) $account['name']) ?></strong></td>
                        <td><?= e((string) $account['email']) ?></td>
                        <td><span class="chip"><?= e(str_replace('_', ' ', (string) $account['role'])) ?></span></td>
                        <td><?= (int) ($account['orders_count'] ?? 0) ?></td>
                        <td><span class="badge-status badge-<?= (int) $account['is_blocked'] === 1 ? 'danger' : 'success' ?>"><?= (int) $account['is_blocked'] === 1 ? 'blocked' : 'active' ?></span></td>
                        <td><a class="btn btn-sm btn-ghost" href="<?= e(url('admin/users/' . $account['id'])) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($users === []): ?><tr><td colspan="6" class="text-muted-soft">No users found.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pages > 1): ?>
            <nav class="pagination-bar">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <?php if ($i === $page): ?><span class="current"><?= $i ?></span>
                    <?php else: ?><a href="<?= e(url('/admin/users?' . http_build_query(['q' => $search, 'page' => $i]))) ?>"><?= $i ?></a><?php endif; ?>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    </div>

    <div class="surface surface-pad">
        <h5 class="mb-3">Add team member</h5>
        <form method="post" action="<?= e(url('/admin/users')) ?>" class="d-grid gap-3">
            <?= csrf_field() ?>
            <div class="form-field"><label for="name">Name</label><input class="form-control" id="name" name="name" required></div>
            <div class="form-field"><label for="email">Email</label><input class="form-control" id="email" type="email" name="email" required></div>
            <div class="form-field"><label for="password">Temporary password</label><input class="form-control" id="password" type="password" name="password" minlength="8" required></div>
            <div class="form-field">
                <label for="role">Role</label>
                <select class="form-select" id="role" name="role">
                    <option value="customer">Customer</option>
                    <option value="editor">Editor</option>
                    <option value="super_admin">Super admin</option>
                </select>
            </div>
            <button class="btn btn-primary-grad" type="submit">Create account</button>
        </form>
    </div>
</div>
