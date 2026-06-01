<?php include ROOT_PATH . '/includes/header.php'; ?>

<style>
.create-page-wrap {
    max-width: 760px;
    margin: 0 auto;
    padding: 3rem 1.5rem 6rem;
}
.create-page-header {
    text-align: center;
    margin-bottom: 2.5rem;
}
.create-page-header h1 {
    font-family: var(--font-headline);
    font-size: 2rem;
    font-weight: 900;
    color: var(--primary-dark);
    margin-bottom: 0.6rem;
}
.create-page-header p {
    color: var(--secondary);
    font-size: 1rem;
    line-height: 1.7;
}
.create-card {
    background: #fff;
    border-radius: 1.8rem;
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 6px 32px rgba(155,98,112,0.07);
    padding: 2.5rem 2rem;
}
.form-group {
    margin-bottom: 1.8rem;
}
.form-label {
    display: block;
    font-family: var(--font-headline);
    font-weight: 800;
    font-size: 0.92rem;
    color: var(--primary-dark);
    margin-bottom: 0.55rem;
}
.form-label span {
    color: var(--secondary);
    font-weight: 600;
    font-size: 0.82rem;
    margin-right: 0.4rem;
}
.form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 0.85rem 1.1rem;
    border: 1.5px solid var(--outline-variant);
    border-radius: 0.85rem;
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--on-surface);
    background: var(--surface-container);
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
    outline: none;
    direction: rtl;
}
.form-input:focus, .form-select:focus, .form-textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(197,58,98,0.1);
    background: #fff;
}
.form-input.large-title {
    font-family: var(--font-headline);
    font-size: 1.25rem;
    font-weight: 700;
    padding: 1rem 1.2rem;
}
.form-textarea {
    resize: vertical;
    min-height: 260px;
    line-height: 1.85;
}
.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23888' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 1rem center;
    padding-left: 2.5rem;
    cursor: pointer;
}
.char-count {
    text-align: left;
    font-size: 0.78rem;
    color: var(--secondary);
    margin-top: 0.4rem;
    opacity: 0.75;
}
/* Image upload */
.upload-zone {
    border: 2px dashed var(--outline-variant);
    border-radius: 1.1rem;
    padding: 2rem 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s ease;
    background: var(--surface-container);
    position: relative;
}
.upload-zone:hover { border-color: var(--primary); background: var(--primary-container); }
.upload-zone input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.upload-zone i { font-size: 2rem; color: var(--primary); opacity: 0.5; display: block; margin-bottom: 0.75rem; }
.upload-zone-text { color: var(--secondary); font-size: 0.88rem; line-height: 1.6; }
.upload-zone-text strong { color: var(--primary-dark); display: block; margin-bottom: 0.3rem; }
.upload-preview-wrap { display: none; margin-top: 1rem; position: relative; }
.upload-preview-wrap img {
    width: 100%;
    max-height: 240px;
    object-fit: cover;
    border-radius: 0.85rem;
    display: block;
}
.upload-preview-remove {
    position: absolute;
    top: 0.5rem; left: 0.5rem;
    background: rgba(0,0,0,0.5);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 2rem; height: 2rem;
    cursor: pointer;
    font-size: 1rem;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.2s;
}
.upload-preview-remove:hover { background: rgba(197,58,98,0.85); }
/* Errors */
.form-errors {
    background: #fff0f3;
    border: 1px solid #f9b8c8;
    border-radius: 0.85rem;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}
.form-errors li {
    color: #c53058;
    font-size: 0.9rem;
    list-style: none;
    padding: 0.25rem 0;
}
.form-errors li::before { content: '← '; }
/* Submit */
.btn-submit-article {
    width: 100%;
    padding: 1rem;
    border-radius: 3rem;
    border: none;
    background: var(--primary-gradient);
    color: #fff;
    font-family: var(--font-headline);
    font-size: 1.1rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    margin-top: 0.5rem;
}
.btn-submit-article:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(197,58,98,0.3); }
.btn-submit-article:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }
/* Success state */
.create-success {
    text-align: center;
    padding: 4rem 2rem;
}
.create-success-icon {
    width: 5rem; height: 5rem;
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2.2rem;
}
.create-success h2 {
    font-family: var(--font-headline);
    font-size: 1.6rem;
    font-weight: 900;
    color: var(--primary-dark);
    margin-bottom: 0.8rem;
}
.create-success p {
    color: var(--secondary);
    font-size: 0.98rem;
    line-height: 1.75;
    max-width: 420px;
    margin: 0 auto 2rem;
}
.create-success-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
</style>

<div class="create-page-wrap animate-fade-in-up">

    <?php if ($sent): ?>
    <!-- حالة النجاح -->
    <div class="create-card">
        <div class="create-success">
            <div class="create-success-icon">✓</div>
            <h2>مقالتك قيد المراجعة</h2>
            <p>
                شكراً لك — استلمنا مقالتك وستُنشر على صفحة المقالات بعد مراجعتها والموافقة عليها من قِبل الإدارة.
                <br>سنُعلمك عند نشرها إن شاء الله.
            </p>
            <div class="create-success-actions">
                <a href="/malath-php-app/articles.php" class="btn-outline" style="text-decoration:none;">
                    تصفّح المقالات
                </a>
                <a href="/malath-php-app/article-create.php" class="btn-primary" style="text-decoration:none;">
                    اكتبي مقالة أخرى
                </a>
            </div>
        </div>
    </div>

    <?php else: ?>

    <!-- ترويسة الصفحة -->
    <div class="create-page-header">
        <h1>✍ اقترحي مقالة</h1>
        <p>
            اكتبي مقالتك وأرسليها للمراجعة — تُنشر بعد موافقة الإدارة في القسم المناسب.
        </p>
    </div>

    <!-- الفورم -->
    <div class="create-card">

        <?php if (!empty($errors)): ?>
        <ul class="form-errors">
            <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <form method="POST" action="/malath-php-app/article-create.php" enctype="multipart/form-data"
              id="create-article-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_generate()) ?>">

            <!-- العنوان -->
            <div class="form-group">
                <label class="form-label" for="title">عنوان المقالة</label>
                <input type="text"
                       name="title"
                       id="title"
                       class="form-input large-title"
                       placeholder="اكتبي عنواناً واضحاً ومعبّراً..."
                       value="<?= htmlspecialchars($title ?? '') ?>"
                       required
                       autocomplete="off">
            </div>

            <!-- التصنيف -->
            <div class="form-group">
                <label class="form-label" for="community_id">
                    التصنيف <span>(اختاري المجتمع المناسب)</span>
                </label>
                <select name="community_id" id="community_id" class="form-select" required>
                    <option value="">— اختاري التصنيف —</option>
                    <?php foreach ($communities as $c): ?>
                    <option value="<?= $c['id'] ?>"
                        <?= isset($communityId) && $communityId == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- المحتوى -->
            <div class="form-group">
                <label class="form-label" for="content">
                    نص المقالة <span>(50 حرفاً على الأقل)</span>
                </label>
                <textarea name="content"
                          id="content"
                          class="form-textarea"
                          placeholder="اكتبي مقالتك هنا — خذي وقتك في التعبير عن أفكارك وتجربتك..."
                          required
                          oninput="updateCount(this)"><?= htmlspecialchars($content ?? '') ?></textarea>
                <div class="char-count" id="content-count">0 حرف</div>
            </div>

            <!-- صورة الغلاف (اختياري) -->
            <div class="form-group">
                <label class="form-label">
                    صورة الغلاف <span>(اختياري — jpg, png, webp)</span>
                </label>
                <div class="upload-zone" id="upload-zone">
                    <input type="file" name="image" id="image-input" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this)">
                    <i class="fa-regular fa-image"></i>
                    <div class="upload-zone-text">
                        <strong>اضغطي لرفع صورة</strong>
                        أو اتركي الحقل فارغاً لاستخدام الغلاف الافتراضي
                    </div>
                </div>
                <div class="upload-preview-wrap" id="preview-wrap">
                    <img src="" alt="" id="preview-img">
                    <button type="button" class="upload-preview-remove" onclick="removeImage()" title="إزالة الصورة">✕</button>
                </div>
            </div>

            <!-- زر الإرسال -->
            <button type="submit" class="btn-submit-article" id="submit-btn">
                <i class="fa-solid fa-paper-plane"></i>
                إرسال للمراجعة
            </button>

        </form>
    </div>
    <?php endif; ?>

</div>

<script>
function updateCount(el) {
    const count = el.value.length;
    const display = document.getElementById('content-count');
    display.textContent = count + ' حرف';
    display.style.color = count < 50 ? '#e03052' : 'var(--secondary)';
}

function previewImage(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('preview-img').src = e.target.result;
        document.getElementById('preview-wrap').style.display = 'block';
        document.getElementById('upload-zone').style.display  = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}

function removeImage() {
    document.getElementById('image-input').value  = '';
    document.getElementById('preview-img').src    = '';
    document.getElementById('preview-wrap').style.display = 'none';
    document.getElementById('upload-zone').style.display  = 'block';
}

document.getElementById('create-article-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.disabled     = true;
    btn.innerHTML    = '<i class="fa-solid fa-spinner fa-spin"></i> جارٍ الإرسال...';
});

// تحديث العداد على المحتوى الموجود (في حالة الأخطاء)
window.addEventListener('DOMContentLoaded', () => {
    const ta = document.getElementById('content');
    if (ta && ta.value) updateCount(ta);
});
</script>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
