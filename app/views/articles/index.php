<?php include ROOT_PATH . '/includes/header.php'; ?>

<style>
.articles-page { padding: 3rem 0 5rem; }
.articles-hero {
    background: linear-gradient(180deg, var(--surface-container-high) 0%, var(--surface) 100%);
    padding: 4rem 0 3rem; text-align: center; margin-bottom: 3rem;
}
.articles-grid { display: grid; grid-template-columns: 1fr; gap: 2rem; }
@media(min-width:640px)  { .articles-grid { grid-template-columns: repeat(2,1fr); } }
@media(min-width:1024px) { .articles-grid { grid-template-columns: repeat(3,1fr); } }
.article-card {
    background: #fff; border-radius: 1.5rem; overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.03); border: 1px solid rgba(0,0,0,.04);
    transition: all .3s ease; display: flex; flex-direction: column;
}
.article-card:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(155,98,112,.1); }
.article-card-img { width: 100%; height: 200px; object-fit: cover; background: var(--surface-container); }
.article-card-body { padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column; }
.article-card-tag { background: var(--primary-container); color: var(--primary-dark); padding: .3rem .9rem; border-radius: 2rem; font-size: .8rem; font-weight: 700; display: inline-block; margin-bottom: 1rem; }
.article-card-title { font-family: var(--font-headline); font-size: 1.15rem; font-weight: 800; color: var(--primary-dark); margin-bottom: .8rem; line-height: 1.4; flex-grow: 1; }
.article-card-excerpt { color: var(--secondary); font-size: .92rem; line-height: 1.7; margin-bottom: 1.2rem; }
.article-card-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--outline-variant); }
.article-card-author { display: flex; align-items: center; gap: .6rem; }
.article-card-author img { width: 2rem; height: 2rem; border-radius: 50%; object-fit: cover; }
.pagination { display: flex; justify-content: center; gap: .5rem; margin-top: 3rem; }
.page-btn { width: 2.5rem; height: 2.5rem; border-radius: 50%; border: 1px solid var(--outline-variant); background: #fff; color: var(--secondary); font-weight: 700; cursor: pointer; text-decoration: none; display: flex; align-items: center; justify-content: center; transition: all .2s; }
.page-btn.active, .page-btn:hover { background: var(--primary-gradient); color: white; border-color: transparent; }
</style>

<div class="articles-hero">
    <div class="container">
        <h1 class="font-headline font-black text-primary-dark" style="font-size:2.5rem;margin-bottom:1rem;">مقالات ملاذ</h1>
        <p class="text-secondary" style="font-size:1.1rem;max-width:600px;margin:0 auto;">
            محتوى مكتوب بقلم نساء من مجتمعنا — تجارب حقيقية، أفكار صادقة.
        </p>
        <div style="margin-top:1.5rem;color:var(--secondary);font-size:.9rem;"><?= $total ?> مقالة منشورة</div>
    </div>
</div>

<div class="container articles-page">
    <?php if($articles): ?>
    <div class="articles-grid">
        <?php foreach($articles as $a): ?>
        <div class="article-card animate-fade-in-up">
            <div class="article-card-img" style="background:linear-gradient(135deg,var(--primary-container),var(--surface-container-high));display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-feather-pointed" style="font-size:3rem;color:var(--primary);opacity:.4;"></i>
            </div>
            <div class="article-card-body">
                <span class="article-card-tag"><?= htmlspecialchars($a['community_name']) ?></span>
                <a href="articles-single.php?id=<?= $a['id'] ?>" style="text-decoration:none;">
                    <h3 class="article-card-title"><?= htmlspecialchars($a['title'] ?: mb_substr($a['content'], 0, 60) . '…') ?></h3>
                </a>
                <p class="article-card-excerpt"><?= htmlspecialchars(mb_substr(strip_tags($a['content']), 0, 120)) ?>…</p>
                <div class="article-card-footer">
                    <div class="article-card-author">
                        <img src="<?= htmlspecialchars($a['author_avatar'] ?: 'assets/images/default-avatar.png') ?>" alt="">
                        <span style="font-size:.85rem;font-weight:700;color:var(--primary-dark);"><?= htmlspecialchars($a['author_name']) ?></span>
                    </div>
                    <span style="font-size:.8rem;color:var(--secondary);"><?= date('d M Y', strtotime($a['created_at'])) ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if($totalPages > 1): ?>
    <div class="pagination">
        <?php for($i=1;$i<=$totalPages;$i++): ?>
        <a href="articles.php?page=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div style="text-align:center;padding:5rem 0;">
        <i class="fa-solid fa-book-open" style="font-size:3rem;color:var(--outline-variant);margin-bottom:1rem;display:block;"></i>
        <h3 style="color:var(--secondary);">لا توجد مقالات بعد.</h3>
        <a href="community.php" class="btn-primary" style="display:inline-block;margin-top:1rem;text-decoration:none;">انشري أول مقالة</a>
    </div>
    <?php endif; ?>
</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
