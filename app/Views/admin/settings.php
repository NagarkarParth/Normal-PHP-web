<?php $value = static fn (string $key, string $fallback = ''): string => (string) ($settings[$key] ?? $fallback); ?>

<div class="surface surface-pad" id="branding">
    <h5 class="mb-3">Website branding</h5>
    <form method="post" action="<?= e(url('/admin/settings/branding')) ?>" enctype="multipart/form-data" class="form-grid cols-2">
        <?= csrf_field() ?>
        <div class="form-field"><label for="site_name">Site name</label><input class="form-control" id="site_name" name="site_name" value="<?= e($value('site_name', 'YBT Digital')) ?>"></div>
        <div class="form-field"><label for="support_email">Support email</label><input class="form-control" id="support_email" name="support_email" type="email" value="<?= e($value('support_email')) ?>"></div>
        <div class="form-field span-2"><label for="site_tagline">Tagline</label><input class="form-control" id="site_tagline" name="site_tagline" value="<?= e($value('site_tagline')) ?>"></div>
        <div class="form-field span-2"><label for="footer_text">Footer text</label><input class="form-control" id="footer_text" name="footer_text" value="<?= e($value('footer_text')) ?>"></div>
        <div class="form-field span-2">
            <label for="site_logo">Logo</label>
            <input class="form-control" id="site_logo" name="site_logo" type="file" accept="image/*">
            <?php if ($value('site_logo') !== ''): ?><img src="<?= e(uploaded($value('site_logo'))) ?>" alt="logo" style="max-height:48px;margin-top:8px"><?php endif; ?>
        </div>
        <div class="span-2"><button class="btn btn-primary-grad" type="submit">Save branding</button></div>
    </form>
</div>

<div class="surface surface-pad" id="payments">
    <h5 class="mb-1">Payment gateways</h5>
    <p class="text-muted-soft" style="font-size:.85rem">Keys are stored in the database and never printed back in full. Leave the offline gateway selected while testing.</p>
    <form method="post" action="<?= e(url('/admin/settings/payments')) ?>" class="form-grid cols-2">
        <?= csrf_field() ?>
        <div class="form-field span-2">
            <label for="payment_gateway">Active gateway</label>
            <select class="form-select" id="payment_gateway" name="payment_gateway">
                <?php foreach ($gateways as $gateway): ?>
                    <option value="<?= e($gateway->key()) ?>" <?= $value('payment_gateway', 'offline') === $gateway->key() ? 'selected' : '' ?>>
                        <?= e($gateway->label()) ?><?= $gateway->isConfigured() ? '' : ' (keys missing)' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field"><label for="razorpay_key_id">Razorpay key ID</label><input class="form-control" id="razorpay_key_id" name="razorpay_key_id" value="<?= e($value('razorpay_key_id')) ?>" autocomplete="off"></div>
        <div class="form-field"><label for="razorpay_key_secret">Razorpay key secret</label><input class="form-control" id="razorpay_key_secret" type="password" name="razorpay_key_secret" value="<?= e($value('razorpay_key_secret')) ?>" autocomplete="new-password"></div>
        <div class="form-field"><label for="stripe_publishable_key">Stripe publishable key</label><input class="form-control" id="stripe_publishable_key" name="stripe_publishable_key" value="<?= e($value('stripe_publishable_key')) ?>" autocomplete="off"></div>
        <div class="form-field"><label for="stripe_secret_key">Stripe secret key</label><input class="form-control" id="stripe_secret_key" type="password" name="stripe_secret_key" value="<?= e($value('stripe_secret_key')) ?>" autocomplete="new-password"></div>
        <div class="form-field"><label for="paypal_client_id">PayPal client ID</label><input class="form-control" id="paypal_client_id" name="paypal_client_id" value="<?= e($value('paypal_client_id')) ?>" autocomplete="off"></div>
        <div class="form-field"><label for="paypal_secret">PayPal secret</label><input class="form-control" id="paypal_secret" type="password" name="paypal_secret" value="<?= e($value('paypal_secret')) ?>" autocomplete="new-password"></div>
        <div class="form-field">
            <label for="paypal_mode">PayPal mode</label>
            <select class="form-select" id="paypal_mode" name="paypal_mode">
                <option value="sandbox" <?= $value('paypal_mode', 'sandbox') === 'sandbox' ? 'selected' : '' ?>>Sandbox</option>
                <option value="live" <?= $value('paypal_mode') === 'live' ? 'selected' : '' ?>>Live</option>
            </select>
        </div>
        <div class="span-2"><button class="btn btn-primary-grad" type="submit">Save gateways</button></div>
    </form>
</div>

<div class="surface surface-pad" id="tax">
    <h5 class="mb-3">Currency, tax &amp; downloads</h5>
    <form method="post" action="<?= e(url('/admin/settings/tax')) ?>" class="form-grid cols-2">
        <?= csrf_field() ?>
        <div class="form-field">
            <label for="currency">Currency</label>
            <select class="form-select" id="currency" name="currency">
                <?php foreach (['INR', 'USD', 'EUR', 'GBP', 'AUD'] as $code): ?>
                    <option value="<?= $code ?>" <?= $value('currency', 'INR') === $code ? 'selected' : '' ?>><?= $code ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field"><label for="tax_label">Tax label</label><input class="form-control" id="tax_label" name="tax_label" value="<?= e($value('tax_label', 'GST')) ?>"></div>
        <div class="form-field"><label for="tax_rate">Tax rate (%)</label><input class="form-control" id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" value="<?= e($value('tax_rate', '18')) ?>"></div>
        <div class="form-field"><label for="download_expiry_days">Download link expiry (days, 0 = never)</label><input class="form-control" id="download_expiry_days" name="download_expiry_days" type="number" min="0" value="<?= e($value('download_expiry_days', '0')) ?>"></div>
        <div class="span-2"><button class="btn btn-primary-grad" type="submit">Save</button></div>
    </form>
</div>

<div class="surface surface-pad" id="notifications">
    <h5 class="mb-3">Email notifications</h5>
    <form method="post" action="<?= e(url('/admin/settings/notifications')) ?>" class="d-grid gap-2">
        <?= csrf_field() ?>
        <?php foreach ([
            'order_paid'   => 'Order confirmation with download links',
            'order_failed' => 'Failed or cancelled payment notice',
            'welcome'      => 'Welcome email on signup',
            'ticket_reply' => 'Support ticket replies',
        ] as $key => $label): ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="notify_<?= $key ?>" name="notify_<?= $key ?>" value="1" <?= $value('notify_' . $key, '1') === '1' ? 'checked' : '' ?>>
                <label class="form-check-label" for="notify_<?= $key ?>"><?= e($label) ?></label>
            </div>
        <?php endforeach; ?>
        <div><button class="btn btn-primary-grad mt-2" type="submit">Save notifications</button></div>
    </form>
</div>
