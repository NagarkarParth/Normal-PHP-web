<div class="admin-grid-2">
    <div class="surface surface-pad">
        <h5 class="mb-3">Existing FAQs</h5>
        <?php foreach ($faqs as $faq): ?>
            <form method="post" action="<?= e(url('admin/faqs/' . $faq['id'])) ?>" class="form-grid cols-2 mb-4 pb-3" style="border-bottom:1px solid var(--border)">
                <?= csrf_field() ?>
                <div class="form-field span-2"><label>Question</label><input class="form-control" name="question" value="<?= e((string) $faq['question']) ?>"></div>
                <div class="form-field span-2"><label>Answer</label><textarea class="form-control" name="answer" rows="3"><?= e((string) $faq['answer']) ?></textarea></div>
                <div class="form-field"><label>Sort order</label><input class="form-control" name="sort_order" type="number" value="<?= (int) $faq['sort_order'] ?>"></div>
                <div class="form-check align-self-end">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="faqActive<?= (int) $faq['id'] ?>" <?= (int) $faq['is_active'] === 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="faqActive<?= (int) $faq['id'] ?>">Published</label>
                </div>
                <div class="span-2 d-flex gap-2">
                    <button class="btn btn-sm btn-primary-grad" type="submit">Save</button>
                    <button class="btn btn-sm btn-outline-danger" type="submit" formaction="<?= e(url('admin/faqs/' . $faq['id'] . '/delete')) ?>">Delete</button>
                </div>
            </form>
        <?php endforeach; ?>
        <?php if ($faqs === []): ?><p class="text-muted-soft mb-0">No FAQs yet.</p><?php endif; ?>
    </div>

    <div class="surface surface-pad">
        <h5 class="mb-3">Add FAQ</h5>
        <form method="post" action="<?= e(url('/admin/faqs')) ?>" class="d-grid gap-3">
            <?= csrf_field() ?>
            <div class="form-field"><label for="question">Question</label><input class="form-control" id="question" name="question" required></div>
            <div class="form-field"><label for="answer">Answer</label><textarea class="form-control" id="answer" name="answer" rows="4" required></textarea></div>
            <div class="form-field"><label for="sort_order">Sort order</label><input class="form-control" id="sort_order" name="sort_order" type="number" value="0"></div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                <label class="form-check-label" for="is_active">Published</label>
            </div>
            <button class="btn btn-primary-grad" type="submit">Add FAQ</button>
        </form>
    </div>
</div>
