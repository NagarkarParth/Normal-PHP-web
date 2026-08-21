<section class="section container">
    <div class="surface surface-pad text-center" style="max-width:520px;margin-inline:auto">
        <div class="spinner-border text-primary mb-3" role="status" aria-hidden="true"></div>
        <h2 style="font-size:1.3rem">Opening <?= e($gateway->label()) ?>…</h2>
        <p class="text-muted-soft">Complete the payment in the popup to unlock your downloads for order <strong><?= e((string) $order['order_number']) ?></strong>.</p>

        <form id="razorpayConfirmForm" method="post" action="<?= e(url('/checkout/confirm/' . $gateway->key())) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
            <input type="hidden" name="razorpay_payment_id" value="">
            <input type="hidden" name="razorpay_order_id" value="">
            <input type="hidden" name="razorpay_signature" value="">
        </form>

        <a class="btn btn-sm btn-ghost mt-2" href="<?= e(url('/checkout/cancel/' . $order['id'])) ?>">Cancel payment</a>
    </div>
</section>

<script src="<?= e((string) $checkout['script']) ?>"></script>
<script type="application/json" id="razorpayCheckout" data-cancel-url="<?= e(url('/checkout/cancel/' . $order['id'])) ?>">
    <?= json_encode($checkout['options'], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>
</script>
