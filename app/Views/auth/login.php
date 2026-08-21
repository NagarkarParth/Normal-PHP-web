<div class="auth-card">
    <h1>Welcome back</h1>
    <p class="text-muted-soft">Sign in to access your downloads and invoices.</p>

    <form method="post" action="<?= e(url('/login')) ?>" class="d-grid gap-3">
        <?= csrf_field() ?>
        <div class="form-field">
            <label for="email">Email</label>
            <input class="form-control" id="email" type="email" name="email" value="<?= e((string) old('email')) ?>" autocomplete="email" required autofocus>
        </div>
        <div class="form-field">
            <label for="password">Password</label>
            <input class="form-control" id="password" type="password" name="password" autocomplete="current-password" required>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <a href="<?= e(url('/forgot')) ?>" style="font-size:.86rem">Forgot password?</a>
        </div>
        <button class="btn btn-lg btn-primary-grad" type="submit">Sign in</button>
    </form>

    <p class="text-center text-muted-soft mb-0" style="font-size:.9rem">
        New here? <a href="<?= e(url('/signup')) ?>">Create an account</a>
    </p>
    <p class="text-center"><a class="text-muted-soft" style="font-size:.84rem" href="<?= e(url('/')) ?>">← Back to store</a></p>
</div>
