<section class="section container">
    <div class="section-head"><div><h2>Contact support</h2><p>Tell us what happened and we will reply by email.</p></div></div>

    <div class="detail-grid">
        <div class="surface surface-pad">
            <form method="post" action="<?= e(url('/contact')) ?>" class="form-grid cols-2">
                <?= csrf_field() ?>
                <div class="form-field"><label for="name">Name</label><input class="form-control" id="name" name="name" value="<?= e((string) old('name', auth()['name'] ?? '')) ?>" required></div>
                <div class="form-field"><label for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="<?= e((string) old('email', auth()['email'] ?? '')) ?>" required></div>
                <div class="form-field span-2"><label for="subject">Subject</label><input class="form-control" id="subject" name="subject" value="<?= e((string) old('subject')) ?>" required></div>
                <div class="form-field span-2"><label for="message">Message</label><textarea class="form-control" id="message" name="message" rows="6" required><?= e((string) old('message')) ?></textarea></div>
                <div class="span-2"><button class="btn btn-primary-grad" type="submit">Send message</button></div>
            </form>
        </div>

        <aside class="d-grid gap-3">
            <div class="surface surface-pad">
                <h5 class="mb-2">Email us</h5>
                <a href="mailto:<?= e($branding['support_email']) ?>"><?= e($branding['support_email']) ?></a>
                <p class="text-muted-soft mt-2 mb-0" style="font-size:.85rem">Typical reply time: under 24 hours on business days.</p>
            </div>
            <div class="surface surface-pad">
                <h5 class="mb-2">Check the FAQ first</h5>
                <p class="text-muted-soft" style="font-size:.85rem">Most download and payment questions are answered there.</p>
                <a class="btn btn-sm btn-ghost w-100" href="<?= e(url('/faq')) ?>">Open FAQ</a>
            </div>
        </aside>
    </div>
</section>
