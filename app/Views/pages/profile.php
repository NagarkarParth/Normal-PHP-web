<section class="section container">
    <div class="section-head"><div><h2>Your profile</h2><p>Manage your details and password.</p></div></div>

    <div class="detail-grid">
        <div class="d-grid gap-4">
            <div class="surface surface-pad">
                <h5 class="mb-3">Account details</h5>
                <form method="post" action="<?= e(url('/profile')) ?>" class="form-grid cols-2">
                    <?= csrf_field() ?>
                    <div class="form-field"><label for="name">Name</label><input class="form-control" id="name" name="name" value="<?= e((string) $profile['name']) ?>" required></div>
                    <div class="form-field"><label for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="<?= e((string) $profile['email']) ?>" required></div>
                    <div class="form-field span-2"><label for="phone">Phone (optional)</label><input class="form-control" id="phone" name="phone" value="<?= e((string) ($profile['phone'] ?? '')) ?>"></div>
                    <div class="span-2"><button class="btn btn-primary-grad" type="submit">Save changes</button></div>
                </form>
            </div>

            <div class="surface surface-pad">
                <h5 class="mb-3">Change password</h5>
                <form method="post" action="<?= e(url('/profile/password')) ?>" class="form-grid cols-2">
                    <?= csrf_field() ?>
                    <div class="form-field span-2"><label for="current_password">Current password</label><input class="form-control" id="current_password" type="password" name="current_password" required></div>
                    <div class="form-field"><label for="password">New password</label><input class="form-control" id="password" type="password" name="password" minlength="8" required></div>
                    <div class="form-field"><label for="password_confirmation">Confirm new password</label><input class="form-control" id="password_confirmation" type="password" name="password_confirmation" minlength="8" required></div>
                    <div class="span-2"><button class="btn btn-ghost" type="submit">Update password</button></div>
                </form>
            </div>
        </div>

        <aside class="d-grid gap-3">
            <div class="surface surface-pad">
                <h5 class="mb-3">Recent orders</h5>
                <?php if ($orders === []): ?>
                    <p class="text-muted-soft mb-0">No orders yet.</p>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <a class="summary-line" href="<?= e(url('orders/' . $order['id'])) ?>">
                            <span><?= e((string) $order['order_number']) ?></span>
                            <span><?= money((float) $order['total']) ?></span>
                        </a>
                    <?php endforeach; ?>
                    <a class="btn btn-sm btn-ghost w-100 mt-3" href="<?= e(url('/orders')) ?>">All orders &amp; downloads</a>
                <?php endif; ?>
            </div>
            <div class="surface surface-pad">
                <h5 class="mb-2">Need help?</h5>
                <p class="text-muted-soft" style="font-size:.86rem">Open a ticket and our team replies by email.</p>
                <a class="btn btn-sm btn-primary-grad w-100" href="<?= e(url('/contact')) ?>">Contact support</a>
            </div>
        </aside>
    </div>
</section>
