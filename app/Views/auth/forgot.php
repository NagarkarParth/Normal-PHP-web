<div class="auth-card">
    <h1>Reset your password</h1>
    <p class="text-muted-soft">We will email you a link that is valid for one hour.</p>

    <form method="post" action="<?= e(url('/forgot')) ?>" class="d-grid gap-3">
        <?= csrf_field() ?>
        <div class="form-field">
            <label for="email">Email</label>
            <input class="form-control" id="email" type="email" name="email" required autofocus>
        </div>
        <button class="btn btn-lg btn-primary-grad" type="submit">Send reset link</button>
    </form>

    <p class="text-center mb-0"><a href="<?= e(url('/login')) ?>" style="font-size:.9rem">Back to sign in</a></p>
</div>
