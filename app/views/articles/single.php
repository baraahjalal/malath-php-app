<?php include ROOT_PATH . '/includes/header.php'; ?>

<style>
.single-article-wrap { max-width: 800px; margin: 3rem auto; padding: 0 1.5rem 5rem; }
.article-meta-bar { display:flex;align-items:center;gap:1rem;margin-bottom:2rem;flex-wrap:wrap; }
.article-tag { background:var(--primary-container);color:var(--primary-dark);padding:.35rem 1.2rem;border-radius:2rem;font-size:.875rem;font-weight:800; }
.article-big-title { font-family:var(--font-headline);font-size:2.5rem;font-weight:900;color:var(--primary-dark);margin-bottom:2rem;line-height:1.3; }
@media(min-width:768px){ .article-big-title{font-size:3.5rem;} }
.author-bar { display:flex;align-items:center;gap:1rem;padding:1.25rem;background:#fff;border-radius:1rem;border:1px solid var(--outline-variant);margin-bottom:2.5rem; }
.author-bar img { width:3.5rem;height:3.5rem;border-radius:50%;object-fit:cover; }
.article-body { font-size:1.1rem;line-height:1.9;color:var(--on-surface); }
.article-body p { margin-bottom:1.75rem; }
.comments-section { margin-top:4rem;padding-top:2rem;border-top:1px solid var(--outline-variant); }
.comment-item { display:flex;gap:1rem;margin-bottom:1.5rem; }
.comment-item img { width:2.5rem;height:2.5rem;border-radius:50%;object-fit:cover;flex-shrink:0; }
.comment-bubble { background:#fff;padding:1rem 1.2rem;border-radius:1.5rem;border-top-right-radius:0;flex-grow:1;border:1px solid var(--outline-variant); }
</style>

<div class="single-article-wrap animate-fade-in-up">
    <div class="article-meta-bar">
        <span class="article-tag"><?= htmlspecialchars($article['community_name']) ?></span>
        <span style="color:var(--secondary);font-size:.95rem;"><?= date('d M Y', strtotime($article['created_at'])) ?></span>
        <span style="color:var(--secondary);font-size:.95rem;">•</span>
        <span style="color:var(--secondary);font-size:.95rem;">❤ <?= $article['likes_count'] ?> إعجاب</span>
    </div>

    <h1 class="article-big-title">
        <?= htmlspecialchars($article['title'] ?: mb_substr($article['content'], 0, 80)) ?>
    </h1>

    <div class="author-bar">
        <img src="<?= htmlspecialchars($article['author_avatar'] ?: 'assets/images/default-avatar.png') ?>" alt="">
        <div>
            <div style="font-weight:800;font-family:var(--font-headline);color:var(--primary-dark);font-size:1.1rem;"><?= htmlspecialchars($article['author_name']) ?></div>
            <div style="font-size:.85rem;color:var(--secondary);">كاتبة في مجتمع <?= htmlspecialchars($article['community_name']) ?></div>
        </div>
    </div>

    <article class="article-body">
        <?= nl2br(htmlspecialchars($article['content'])) ?>
    </article>

    <!-- التعليقات -->
    <div class="comments-section">
        <h3 class="font-headline font-bold text-primary-dark" style="font-size:1.4rem;margin-bottom:2rem;">
            التعليقات (<?= count($comments) ?>)
        </h3>
        <?php foreach($comments as $c): ?>
        <div class="comment-item">
            <img src="<?= htmlspecialchars($c['user_avatar'] ?: 'assets/images/default-avatar.png') ?>" alt="">
            <div class="comment-bubble">
                <div style="display:flex;justify-content:space-between;margin-bottom:.5rem;">
                    <b style="font-size:.9rem;color:var(--primary-dark);"><?= htmlspecialchars($c['user_name']) ?></b>
                    <small style="color:var(--secondary);font-size:.75rem;"><?= date('Y-m-d H:i', strtotime($c['created_at'])) ?></small>
                </div>
                <p style="margin:0;font-size:.95rem;line-height:1.6;"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if(!$comments): ?>
        <p style="text-align:center;color:var(--secondary);">لا توجد تعليقات بعد. كوني أول من يعلق في صفحة المجتمع!</p>
        <?php endif; ?>
    </div>

    <div style="margin-top:3rem;text-align:center;">
        <a href="articles.php" class="btn-outline" style="text-decoration:none;margin-left:1rem;">← جميع المقالات</a>
        <a href="community.php?c=all#post-<?= $article['id'] ?>" class="btn-primary" style="text-decoration:none;">التفاعل في المجتمع</a>
    </div>
</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
