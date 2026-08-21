<div class="auth-card">
    <h1>Create your account</h1>
    <p class="text-muted-soft">One account for every purchase, invoice and download.</p>

    <form method="post" action="<?= e(url('/signup')) ?>" class="d-grid gap-3">
        <?= csrf_field() ?>
        <div class="form-field">
            <label for="name">Full name</label>
            <input class="form-control" id="name" name="name" value="<?= e((string) old('name')) ?>" autocomplete="name" required autofocus>
        </div>
        <div class="form-field">
            <label for="email">Email</label>
            <input class="form-control" id="email" type="email" name="email" value="<?= e((string) old('email')) ?>" autocomplete="email" required>
        </div>
        <div class="form-field">
            <label for="password">Password</label>
            <input class="form-control" id="password" type="password" name="password" minlength="8" autocomplete="new-password" required>
        </div>
        <div class="form-field">
            <label for="password_confirmation">Confirm password</label>
            <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required>
        </div>
        <button class="btn btn-lg btn-primary-grad" type="submit">Create account</button>
    </form>

    <p class="text-center text-muted-soft mb-0" style="font-size:.9rem">
        Already registered? <a href="<?= e(url('/login')) ?>">Sign in</a>
    </p>
    <p class="text-center"><a class="text-muted-soft" style="font-size:.84rem" href="<?= e(url('/')) ?>">← Back to store</a></p>
</div>
