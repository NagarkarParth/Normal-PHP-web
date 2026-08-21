<div class="auth-card">
    <h1>Choose a new password</h1>
    <p class="text-muted-soft">Pick something at least 8 characters long.</p>

    <form method="post" action="<?= e(url('reset/' . $token)) ?>" class="d-grid gap-3">
        <?= csrf_field() ?>
        <div class="form-field">
            <label for="password">New password</label>
            <input class="form-control" id="password" type="password" name="password" minlength="8" required autofocus>
        </div>
        <div class="form-field">
            <label for="password_confirmation">Confirm password</label>
            <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" minlength="8" required>
        </div>
        <button class="btn btn-lg btn-primary-grad" type="submit">Update password</button>
    </form>
</div>
