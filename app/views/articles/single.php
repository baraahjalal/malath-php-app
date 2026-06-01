<?php include ROOT_PATH . '/includes/header.php'; ?>

<style>
.article-single-wrap {
    max-width: 780px;
    margin: 0 auto;
    padding: 3rem 1.5rem 6rem;
}
.article-hero-img {
    width: 100%;
    height: 420px;
    object-fit: cover;
    border-radius: 1.5rem;
    display: block;
    margin-bottom: 2.5rem;
    box-shadow: 0 8px 32px rgba(155,98,112,0.12);
}
.article-hero-placeholder {
    width: 100%;
    height: 320px;
    background: linear-gradient(135deg, var(--primary-container) 0%, var(--surface-container-high) 100%);
    border-radius: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2.5rem;
}
.article-hero-placeholder i {
    font-size: 5rem;
    color: var(--primary);
    opacity: 0.25;
}
.article-meta-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}
.article-community-tag {
    background: var(--primary-container);
    color: var(--primary-dark);
    padding: 0.3rem 1rem;
    border-radius: 2rem;
    font-size: 0.82rem;
    font-weight: 800;
    font-family: var(--font-headline);
}
.article-date {
    color: var(--secondary);
    font-size: 0.88rem;
}
.article-big-title {
    font-family: var(--font-headline);
    font-size: 2.2rem;
    font-weight: 900;
    color: var(--primary-dark);
    line-height: 1.3;
    margin-bottom: 2rem;
}
@media(min-width:768px) { .article-big-title { font-size: 2.8rem; } }
.article-author-bar {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: #fff;
    border-radius: 1rem;
    border: 1px solid var(--outline-variant);
    margin-bottom: 2.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}
.article-author-bar img {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--primary-container);
}
.article-author-name {
    font-weight: 800;
    font-family: var(--font-headline);
    color: var(--primary-dark);
    font-size: 1rem;
}
.article-author-sub {
    font-size: 0.82rem;
    color: var(--secondary);
    margin-top: 0.15rem;
}
.article-body-text {
    font-size: 1.1rem;
    color: var(--on-surface);
    line-height: 1.95;
    font-family: var(--font-body);
}
.article-body-text p { margin-bottom: 1.5rem; }
.article-actions-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 3.5rem;
    padding-top: 2rem;
    border-top: 1px solid var(--outline-variant);
    flex-wrap: wrap;
    gap: 1rem;
}
.btn-save-article {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.7rem 1.6rem;
    border-radius: 2rem;
    font-size: 0.95rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    border: 1.5px solid var(--outline-variant);
    background: #fff;
    color: var(--secondary);
    transition: all 0.25s ease;
}
.btn-save-article:hover {
    border-color: #0284c7;
    color: #0284c7;
    background: #e0f2fe;
}
.btn-save-article.active-save {
    border-color: #0284c7;
    color: #0284c7;
    background: #e0f2fe;
}
</style>

<div class="article-single-wrap animate-fade-in-up">

    <!-- صورة الغلاف -->
    <?php if (!empty($article['image'])): ?>
        <img src="<?= htmlspecialchars($article['image']) ?>" alt="" class="article-hero-img">
    <?php else: ?>
        <div class="article-hero-placeholder">
            <i class="fa-solid fa-feather-pointed"></i>
        </div>
    <?php endif; ?>

    <!-- الميتا -->
    <div class="article-meta-row">
        <span class="article-community-tag">
            <?= htmlspecialchars($article['community_name']) ?>
        </span>
        <span class="article-date"><?= date('d M Y', strtotime($article['created_at'])) ?></span>
    </div>

    <!-- العنوان -->
    <h1 class="article-big-title">
        <?= htmlspecialchars($article['title'] ?: mb_substr($article['content'], 0, 80)) ?>
    </h1>

    <!-- الكاتبة -->
    <div class="article-author-bar">
        <img src="<?= htmlspecialchars($article['author_avatar'] ?: 'assets/images/default-avatar.png') ?>" alt="">
        <div>
            <div class="article-author-name"><?= htmlspecialchars($article['author_name']) ?></div>
            <div class="article-author-sub">
                كاتبة في <?= htmlspecialchars($article['community_name']) ?>
            </div>
        </div>
    </div>

    <!-- نص المقالة -->
    <article class="article-body-text">
        <?= nl2br(htmlspecialchars($article['content'])) ?>
    </article>

    <!-- شريط الأكشن -->
    <div class="article-actions-bar">
        <a href="/malath-php-app/articles" class="btn-outline" style="text-decoration:none;">
            ← جميع المقالات
        </a>
        <button id="save-btn"
                class="btn-save-article <?= $isSaved ? 'active-save' : '' ?>"
                onclick="toggleArticleSave(this)">
            <i class="<?= $isSaved ? 'fa-solid' : 'fa-regular' ?> fa-bookmark"></i>
            <span><?= $isSaved ? 'مُحفوظة' : 'حفظ المقالة' ?></span>
        </button>
    </div>
</div>

<script>
const CSRF_TOKEN   = '<?= htmlspecialchars(csrf_generate()) ?>';
const IS_LOGGED_IN = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
const ARTICLE_ID   = <?= (int)$article['id'] ?>;

async function toggleArticleSave(btn) {
    if (!IS_LOGGED_IN) {
        window.location.href = '/malath-php-app/login.php';
        return;
    }
    btn.disabled = true;
    const fd = new FormData();
    fd.append('article_id', ARTICLE_ID);
    try {
        const res  = await fetch('/malath-php-app/api/toggle_article_save.php', {
            method: 'POST', body: fd, headers: { 'X-CSRF-Token': CSRF_TOKEN }
        });
        const data = await res.json();
        if (!data.success) return;
        const icon  = btn.querySelector('i');
        const label = btn.querySelector('span');
        if (data.saved) {
            btn.classList.add('active-save');
            icon.className  = 'fa-solid fa-bookmark';
            label.textContent = 'مُحفوظة';
        } else {
            btn.classList.remove('active-save');
            icon.className  = 'fa-regular fa-bookmark';
            label.textContent = 'حفظ المقالة';
        }
    } finally {
        btn.disabled = false;
    }
}
</script>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
