<?php
// community.php

// 1) إعداد الجلسة وربط قاعدة البيانات
if (session_status() === PHP_SESSION_NONE) session_start();
include 'includes/db.php';

// ==== المتغيرات العامة ==== //
$user_id = $_SESSION['user_id'] ?? null;

// استخراج سلاَج المجتمع من الرابط (أو all)
$current_slug = $_GET['c'] ?? 'all';

// ==== معالجة أزرار الانضمام/الانسحاب ==== //
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    // انضمام
    if (isset($_POST['join_community']) && isset($_POST['community_id'])) {
        $st = $pdo->prepare("INSERT IGNORE INTO user_communities(user_id, community_id) VALUES(?, ?)");
        $st->execute([$user_id, intval($_POST['community_id'])]);
        header("Location: community.php?c=".urlencode($current_slug));
        exit;
    }
    // انسحاب
    if (isset($_POST['leave_community']) && isset($_POST['community_id'])) {
        $st = $pdo->prepare("DELETE FROM user_communities WHERE user_id=? AND community_id=?");
        $st->execute([$user_id, intval($_POST['community_id'])]);
        header("Location: community.php?c=".urlencode($current_slug));
        exit;
    }
}

// الآن نقوم بتضمين الهيدر لأننا انتهينا من التوجيه (Redirects)
include 'includes/header.php';

// جلب جميع المجتمعات من الجدول
$communities = $pdo->query("SELECT * FROM communities")->fetchAll();

$current_community = null;
foreach ($communities as $c) {
    if ($c['slug'] === $current_slug) $current_community = $c;
}

// جلب مجتمعات المستخدم المنضم لها (كمصفوفة أرقام)
$user_communities = [];
if ($user_id) {
    $st = $pdo->prepare("SELECT community_id FROM user_communities WHERE user_id=?");
    $st->execute([$user_id]);
    $user_communities = array_column($st->fetchAll(), 'community_id');
}


// ==== منطق النشر ==== //
$post_message = '';
if ($user_id && isset($_POST['submit_post'])) {
    $type = $_POST['type'] ?? 'vent';
    $content = trim($_POST['content'] ?? '');
    $community_id = intval($_POST['post_community_id'] ?? 0);
    $title = trim($_POST['post_title'] ?? '');

    // تحقق المستخدم منضم للمجتمع
    if ($community_id && in_array($community_id, $user_communities) && $content) {
        $st = $pdo->prepare("INSERT INTO posts (user_id, community_id, type, title, content, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $st->execute([$user_id, $community_id, $type, $title, $content]);
        $post_message = "✔ تم نشر المحتوى بنجاح";
    } else {
        $post_message = "⚠ يجب أن تكوني ضمن المجتمع المختار وأن تكتبي محتوى.";
    }
}

// ==== جلب جميع المنشورات للمجتمع المختار (أو الكل) ==== //
if ($current_community) {
    $st = $pdo->prepare("
      SELECT p.*, u.name AS user_name, u.avatar AS user_avatar, c.name AS community_name
      FROM posts p 
      JOIN users u ON u.id = p.user_id
      JOIN communities c ON c.id = p.community_id
      WHERE c.id = ?
      ORDER BY p.created_at DESC
    ");
    $st->execute([$current_community['id']]);
} else {
    $st = $pdo->query("
      SELECT p.*, u.name AS user_name, u.avatar AS user_avatar, c.name AS community_name
      FROM posts p
      JOIN users u ON u.id = p.user_id
      JOIN communities c ON c.id = p.community_id
      ORDER BY p.created_at DESC
    ");
}
$posts = $st->fetchAll();

?>
<style>
/* Community Page Specific Styles - Feminine, Clean & Polished */
.community-header {
    background: linear-gradient(180deg, var(--surface-container-high) 0%, var(--surface) 100%);
    padding: 3rem 0 2rem;
    text-align: center;
    border-bottom: 1px solid rgba(197, 58, 98, 0.05);
}

.community-title {
    font-size: 2.25rem;
    font-weight: 900;
    color: var(--primary-dark);
    font-family: var(--font-headline);
    margin-bottom: 1rem;
}

/* Tabs Navigation */
.community-tabs-container {
    display: flex;
    justify-content: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 2rem;
}

.community-tab {
    padding: 0.75rem 1.5rem;
    border-radius: 2rem;
    font-weight: 700;
    font-size: 0.95rem;
    text-decoration: none;
    color: var(--secondary);
    background-color: var(--surface-container);
    transition: all 0.3s ease;
    border: 1px solid transparent;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.community-tab:hover {
    background-color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    color: var(--primary);
}

.community-tab.active {
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 4px 15px rgba(197, 58, 98, 0.2);
}

/* Main Layout */
.community-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 3rem;
    padding: 3rem 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

@media (min-width: 1024px) {
    .community-layout {
        grid-template-columns: 3.5fr 8.5fr; /* Sidebar Left, Feed Right (RTL means Sidebar is visually on the right) */
    }
}

/* Create Post Box */
.create-post-box {
    background-color: #ffffff;
    border-radius: 1.5rem;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    border: 1px solid var(--outline-variant);
    margin-bottom: 2.5rem;
}

.create-post-top {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.create-post-input {
    flex-grow: 1;
    background-color: var(--surface-container-high);
    border-radius: 1.2rem;
    padding: 1.2rem 1.5rem;
    color: var(--on-surface);
    border: 1px solid transparent;
    transition: all 0.3s ease;
    font-family: inherit;
    resize: none;
    min-height: 80px;
    font-size: 1rem;
}

.create-post-input:focus {
    background-color: #ffffff;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(197, 58, 98, 0.08);
    outline: none;
}

.create-post-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    align-items: center;
    padding-top: 1.2rem;
    border-top: 1px solid var(--outline-variant);
}

.create-post-select, .create-post-text {
    background-color: var(--surface-container);
    border: 1px solid transparent;
    padding: 0.6rem 1.2rem;
    border-radius: 2rem;
    font-size: 0.9rem;
    color: var(--secondary);
    font-family: inherit;
    transition: all 0.3s ease;
    outline: none;
}

.create-post-text {
    flex-grow: 1;
    min-width: 150px;
}

.create-post-select:focus, .create-post-text:focus {
    background-color: #ffffff;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(197, 58, 98, 0.08);
}

.create-post-btn {
    padding: 0.6rem 1.8rem;
    border-radius: 2rem;
    font-weight: 800;
    margin-right: auto;
}

.post-success-msg {
    background-color: #ecfdf5;
    color: #059669;
    padding: 0.8rem 1.2rem;
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-btn-group {
    display: flex;
    gap: 1rem;
}

.type-btn {
    background: none;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--secondary);
    font-weight: 600;
    cursor: pointer;
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    transition: all 0.2s ease;
}

.type-btn:hover {
    background-color: var(--surface-container);
    color: var(--primary);
}

/* Feed Cards (Posts & Articles) */
.feed-card {
    background-color: #ffffff;
    border-radius: 1.5rem;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    border: 1px solid rgba(0,0,0,0.04);
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.feed-card:hover {
    box-shadow: 0 10px 30px rgba(155, 98, 112, 0.08);
}

.post-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    object-fit: cover;
}

.user-name {
    font-weight: 800;
    color: var(--primary-dark);
    font-family: var(--font-headline);
    text-decoration: none;
}

.post-meta {
    font-size: 0.8rem;
    color: var(--secondary);
    margin-top: 0.2rem;
}

/* Badges for Post Types */
.badge-type {
    padding: 0.3rem 0.8rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 700;
}
.badge-question { background: #e0f2fe; color: #0284c7; } /* سؤال */
.badge-vent { background: #fce7f3; color: #be185d; } /* فضفضة */
.badge-advice { background: #fef3c7; color: #d97706; } /* طلب نصيحة */
.badge-article { background: var(--primary-container); color: var(--primary-dark); } /* مقالة */

/* Content */
.post-content {
    color: var(--on-surface);
    line-height: 1.7;
    margin-bottom: 1.5rem;
    font-size: 1.05rem;
}

.article-image-preview {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 1rem;
    margin-bottom: 1rem;
}

/* Footer & Actions */
.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--outline-variant);
}

.interaction-btns {
    display: flex;
    gap: 1.5rem;
}

.interact-btn {
    background: none;
    border: none;
    color: var(--secondary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-weight: 600;
    transition: color 0.2s ease;
}

.interact-btn:hover { color: var(--primary); }

/* Sidebar */
.sidebar-widget {
    background-color: #ffffff;
    border-radius: 1.5rem;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    border: 1px solid rgba(0,0,0,0.04);
    margin-bottom: 2rem;
}

.widget-title {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--primary-dark);
    margin-bottom: 1.5rem;
    font-family: var(--font-headline);
}

.community-list-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--outline-variant);
    text-decoration: none;
    color: var(--on-surface);
    transition: all 0.2s ease;
}

.community-list-item:last-child { border-bottom: none; }
.community-list-item:hover { color: var(--primary); padding-right: 0.5rem; }

.community-icon-small {
    width: 2.5rem;
    height: 2.5rem;
    background-color: var(--surface-container);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
}

.rules-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.rules-list li {
    position: relative;
    padding-right: 1.5rem;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    color: var(--secondary);
    line-height: 1.6;
}
.rules-list li::before {
    content: '•';
    position: absolute;
    right: 0;
    color: var(--primary);
    font-weight: bold;
}
/* Join and Leave Buttons */
.btn-join {
    background-color: var(--primary-container, #fce7f3);
    color: var(--primary-dark, #be185d);
    border: 1px solid transparent;
    padding: 0.4rem 1.2rem;
    border-radius: 2rem;
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
}

.btn-join:hover {
    background-color: var(--primary, #db2777);
    color: white;
    box-shadow: 0 4px 12px rgba(219, 39, 119, 0.2);
    transform: translateY(-1px);
}

.btn-leave {
    background-color: var(--surface-container, #f3f4f6);
    color: var(--secondary, #4b5563);
    border: 1px solid var(--outline-variant, #e5e7eb);
    padding: 0.4rem 1.2rem;
    border-radius: 2rem;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
}

.btn-leave:hover {
    background-color: #fee2e2;
    color: #ef4444;
    border-color: #fca5a5;
}

</style>

<div class="community-header">
  <div class="container animate-fade-in-up">
    <h1 class="community-title">مجتمعات ملاذ</h1>
    <p>مساحتك الآمنة للتفاعل، التعلم، ومشاركة تجاربك في بيئة نسائية داعمة ومحفزة.</p>
    <!-- تبويبات المجتمعات -->
    <div class="community-tabs-container">
      <a href="community.php?c=all" class="community-tab<?= $current_slug === 'all' ? ' active' : '' ?>">الأحدث</a>
      <?php foreach($communities as $c): ?>
        <a href="community.php?c=<?=htmlspecialchars($c['slug'])?>" class="community-tab<?= $current_slug === $c['slug'] ? ' active' : '' ?>">
          <?=htmlspecialchars($c['name'])?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div class="community-layout">
  <!-- الشريط الجانبي -->
  <aside>
    <div class="sidebar-widget">
      <h3 class="widget-title">كل المجتمعات</h3>
      <?php foreach($communities as $c): ?>
        <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
          <a href="community.php?c=<?=htmlspecialchars($c['slug'])?>" class="community-list-item" style="flex:1;">
            <?=htmlspecialchars($c['name'])?>
          </a>
          <?php if($user_id): ?>
            <form method="post" style="display:inline;">
              <input type="hidden" name="community_id" value="<?=$c['id']?>">
              <?php if (!in_array($c['id'], $user_communities)): ?>
                <button type="submit" name="join_community" class="btn-join">انضمام</button>
              <?php else: ?>
                <button type="submit" name="leave_community" class="btn-leave">انسحاب</button>
              <?php endif; ?>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="sidebar-widget">
      <h3 class="widget-title">مجتمعاتي (المنضمة لها)</h3>
      <?php if($user_id && $user_communities): ?>
        <?php foreach($communities as $c):
          if(!in_array($c['id'], $user_communities)) continue; ?>
          <a href="community.php?c=<?=htmlspecialchars($c['slug'])?>" class="community-list-item">
            <?=htmlspecialchars($c['name'])?>
          </a>
        <?php endforeach; ?>
      <?php elseif(!$user_id): ?>
        <p style="font-size:0.95rem; color:#a78;">سجلي دخولك لتخصيص تجربتك والانضمام للمجتمعات.</p>
        <a href="login.php?redirect=community.php" class="btn-outline">تسجيل الدخول</a>
      <?php else: ?>
        <div>لم تنضمي لأي مجتمع بعد.</div>
      <?php endif; ?>
    </div>

    <div class="sidebar-widget" style="background:var(--surface-container-high); border:none;">
      <h3 class="widget-title"><i class="fa-solid fa-shield-heart" style="color:var(--primary);"></i> مساحة آمنة</h3>
      <ul class="rules-list">
        <li>احترمي آراء الأخريات وتجنبي الأحكام.</li>
        <li>يُمنع نشر أي محتوى يسيء للأديان أو الأشخاص.</li>
        <li>حافظي على سرية الفضفضة والقصص المطروحة هنا.</li>
        <li>المقالات يجب أن تُنشر في مجتمعها الصحيح.</li>
      </ul>
    </div>
  </aside>

  <!-- القسم الرئيسي -->
  <main>
    <!-- نموذج نشر منشور -->
    <?php if($user_id && $user_communities): ?>
      <div class="create-post-box animate-fade-in-up">
        <?php if($post_message): ?>
          <div class="post-success-msg"><i class="fa-solid fa-circle-check"></i> <?=htmlspecialchars($post_message)?></div>
        <?php endif; ?>
        <form method="post">
          <div class="create-post-top">
            <img src="<?=htmlspecialchars($user_avatar ?? 'assets/images/default-avatar.png')?>" alt="Profile" class="user-avatar" style="width:3rem;height:3rem;">
            <textarea name="content" class="create-post-input" required placeholder="بم تفكرين؟ شاركي أفكارك، أسئلتك، أو تجربتك هنا..."></textarea>
          </div>
          <div class="create-post-actions">
            <select name="type" class="create-post-select" required>
              <option value="vent">🗣 فضفضة</option>
              <option value="advice">💡 طلب نصيحة</option>
              <option value="question">❓ سؤال</option>
              <option value="article">📝 مقالة رسمية</option>
            </select>
            <select name="post_community_id" class="create-post-select" required>
              <?php foreach($communities as $c): if(in_array($c['id'],$user_communities)): ?>
                <option value="<?=$c['id']?>"<?= ($current_community && $c['id']==$current_community['id']) ? ' selected' : '' ?>>
                  <?=htmlspecialchars($c['name'])?>
                </option>
              <?php endif; endforeach;?>
            </select>
            <input type="text" name="post_title" class="create-post-text" placeholder="عنوان المقال (اختياري)">
            <button type="submit" name="submit_post" class="btn-primary create-post-btn">نشر الآن</button>
          </div>
        </form>
      </div>
    <?php elseif($user_id): ?>
      <div class="create-post-box" style="text-align:center; opacity:.85;">
        <b>انضمي لمجتمع أولاً لتتمكني من النشر فيه</b>
      </div>
    <?php else: ?>
      <div class="create-post-box" style="text-align:center;">
        <p>لتتمكني من النشر، قومي بتسجيل الدخول أو إنشاء حساب جديد.</p>
        <a href="login.php?redirect=community.php" class="btn-primary">تسجيل الدخول</a>
        <a href="register.php" class="btn-outline">إنشاء حساب جديد</a>
      </div>
    <?php endif; ?>

    <!-- عرض المنشورات -->
    <div class="feed-list">
      <?php if($posts): ?>
        <?php foreach($posts as $post): ?>
          <div class="feed-card animate-fade-in-up" style="margin-bottom:2.3rem;">
            <div class="post-header">
              <div class="user-info">
                <img src="<?=htmlspecialchars($post['user_avatar'] ?: 'assets/images/default-avatar.png')?>" alt="U" class="user-avatar">
                <div>
                  <b class="user-name"><?=htmlspecialchars($post['user_name'])?></b>
                  <div class="post-meta">
                    نُشر في <b><?=htmlspecialchars($post['community_name'])?></b>
                    • <?=date('Y-m-d H:i', strtotime($post['created_at']))?>
                  </div>
                </div>
              </div>
              <span class="badge-type badge-<?=htmlspecialchars($post['type'])?>">
                <?= ['vent'=>'فضفضة','advice'=>'نصيحة','question'=>'سؤال','article'=>'مقالة'][$post['type']] ?? $post['type'] ?>
              </span>
            </div>
            <?php if($post['title']): ?>
              <h3 style="margin-bottom: .6rem;"><?=htmlspecialchars($post['title'])?></h3>
            <?php endif;?>
            <div class="post-content"><?=nl2br(htmlspecialchars($post['content']))?></div>
          </div>
        <?php endforeach; ?>
      <?php else:?>
        <div style="text-align:center; color:#888;">لا توجد منشورات بعد في هذا المجتمع.</div>
      <?php endif;?>
    </div>

  </main>
</div>

<?php include 'includes/footer.php'; ?>