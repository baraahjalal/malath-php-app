<?php include ROOT_PATH . '/includes/header.php'; ?>

<style>
.articles-hero {
    background: linear-gradient(180deg, var(--surface-container-high) 0%, var(--surface) 100%);
    padding: 4rem 0 0;
    text-align: center;
    border-bottom: 1px solid rgba(197,58,98,0.06);
}
.articles-hero-title {
    font-family: var(--font-headline);
    font-size: 2.5rem;
    font-weight: 900;
    color: var(--primary-dark);
    margin-bottom: 0.75rem;
}
.articles-hero-sub {
    color: var(--secondary);
    font-size: 1.05rem;
    max-width: 540px;
    margin: 0 auto 2rem;
    line-height: 1.7;
}
.articles-filter-tabs {
    display: flex;
    justify-content: center;
    gap: 0.6rem;
    flex-wrap: wrap;
    padding-bottom: 1px;
}
.articles-tab {
    padding: 0.65rem 1.4rem;
    border-radius: 2rem 2rem 0 0;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    color: var(--secondary);
    background-color: var(--surface-container);
    transition: all 0.25s ease;
    border: 1px solid transparent;
    border-bottom: none;
}
.articles-tab:hover { background-color: #fff; color: var(--primary); }
.articles-tab.active {
    background: #fff;
    color: var(--primary-dark);
    border-color: rgba(197,58,98,0.12);
    font-weight: 800;
}
.articles-body { padding: 3rem 0 5rem; }
.articles-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.8rem;
}
@media(min-width:640px)  { .articles-grid { grid-template-columns: repeat(2,1fr); } }
@media(min-width:1024px) { .articles-grid { grid-template-columns: repeat(3,1fr); } }
.article-card {
    background: #fff;
    border-radius: 1.5rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    border: 1px solid rgba(0,0,0,0.045);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    text-decoration: none;
}
.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 32px rgba(155,98,112,0.1);
    border-color: rgba(197,58,98,0.1);
}
.article-card-thumb {
    width: 100%;
    height: 190px;
    object-fit: cover;
    display: block;
}
.article-card-placeholder {
    width: 100%;
    height: 190px;
    background: linear-gradient(135deg, var(--primary-container) 0%, var(--surface-container-high) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}
.article-card-placeholder i {
    font-size: 3rem;
    color: var(--primary);
    opacity: 0.35;
}
.article-card-body {
    padding: 1.4rem 1.5rem 1.5rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
.article-card-tag {
    display: inline-block;
    background: var(--primary-container);
    color: var(--primary-dark);
    padding: 0.28rem 0.85rem;
    border-radius: 2rem;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 0.85rem;
    font-family: var(--font-headline);
}
.article-card-title {
    font-family: var(--font-headline);
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--primary-dark);
    line-height: 1.45;
    margin-bottom: 0.7rem;
    flex-grow: 1;
}
.article-card-excerpt {
    color: var(--secondary);
    font-size: 0.88rem;
    line-height: 1.65;
    margin-bottom: 1.2rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.article-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--outline-variant);
    margin-top: auto;
}
.article-card-author {
    display: flex;
    align-items: center;
    gap: 0.55rem;
}
.article-card-author img {
    width: 1.9rem;
    height: 1.9rem;
    border-radius: 50%;
    object-fit: cover;
}
.article-card-author span {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--on-surface);
}
.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 3rem;
    flex-wrap: wrap;
}
.page-btn {
    width: 2.4rem;
    height: 2.4rem;
    border-radius: 50%;
    border: 1px solid var(--outline-variant);
    background: #fff;
    color: var(--secondary);
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    transition: all 0.2s;
}
.page-btn.active, .page-btn:hover {
    background: var(--primary-gradient);
    color: #fff;
    border-color: transparent;
}
</style>

<!-- Hero -->
<div class="articles-hero">
    <div class="container">
        <h1 class="articles-hero-title">مقالات ملاذ</h1>
        <p class="articles-hero-sub">محتوى محكّم ومراجَع، يُنشر بعد موافقة الإدارة — تجارب حقيقية وأفكار صادقة من نساء مجتمعنا.</p>
        <a href="/malath-php-app/article-create.php"
           class="btn-primary"
           style="display:inline-flex;align-items:center;gap:0.5rem;text-decoration:none;margin-bottom:1.5rem;padding:0.75rem 1.8rem;border-radius:3rem;font-size:0.95rem;">
            ✍ اقترحي مقالة
        </a>
        <div style="color:var(--secondary);font-size:0.88rem;margin-bottom:1.5rem;">
            <?= $total ?> مقالة منشورة<?= $currentSlug ? ' في هذا التصنيف' : '' ?>
        </div>
        <!-- فلاتر التصنيف -->
        <div class="articles-filter-tabs">
            <a href="/malath-php-app/articles.php"
               class="articles-tab <?= $currentSlug === null ? 'active' : '' ?>">الكل</a>
            <?php foreach ($communities as $c): ?>
            <a href="/malath-php-app/articles.php?c=<?= htmlspecialchars($c['slug']) ?>"
               class="articles-tab <?= $currentSlug === $c['slug'] ? 'active' : '' ?>">
                <?= htmlspecialchars($c['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- المحتوى -->
<div class="articles-body">
    <div class="container">
        <?php if ($articles): ?>
        <div class="articles-grid">
            <?php foreach ($articles as $a): ?>
            <a href="/malath-php-app/articles-single.php?id=<?= $a['id'] ?>" class="article-card animate-fade-in-up">
                <?php if (!empty($a['image'])): ?>
                    <img src="<?= htmlspecialchars($a['image']) ?>" alt="" class="article-card-thumb">
                <?php else: ?>
                    <div class="article-card-placeholder">
                        <i class="fa-solid fa-feather-pointed"></i>
                    </div>
                <?php endif; ?>
                <div class="article-card-body">
                    <span class="article-card-tag"><?= htmlspecialchars($a['community_name']) ?></span>
                    <h3 class="article-card-title">
                        <?= htmlspecialchars($a['title'] ?: mb_substr($a['content'], 0, 65) . '…') ?>
                    </h3>
                    <p class="article-card-excerpt">
                        <?= htmlspecialchars(mb_substr(strip_tags($a['content']), 0, 130)) ?>…
                    </p>
                    <div class="article-card-footer">
                        <div class="article-card-author">
                            <img src="<?= htmlspecialchars($a['author_avatar'] ?: 'assets/images/default-avatar.png') ?>"
                                 alt="">
                            <span><?= htmlspecialchars($a['author_name']) ?></span>
                        </div>
                        <span style="font-size:0.78rem;color:var(--secondary);">
                            <?= date('d M Y', strtotime($a['created_at'])) ?>
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php
            $qs = $currentSlug ? '&c=' . urlencode($currentSlug) : '';
            for ($i = 1; $i <= $totalPages; $i++):
            ?>
            <a href="/malath-php-app/articles.php?page=<?= $i . $qs ?>"
               class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div style="text-align:center;padding:5rem 0;">
            <i class="fa-solid fa-feather-pointed" style="font-size:3rem;color:var(--outline-variant);display:block;margin-bottom:1.2rem;"></i>
            <h3 style="color:var(--secondary);font-family:var(--font-headline);">لا توجد مقالات <?= $currentSlug ? 'في هذا التصنيف' : '' ?> بعد.</h3>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
