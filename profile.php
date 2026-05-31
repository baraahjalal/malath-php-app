<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'includes/db.php'; 

// 1. حماية الصفحة
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// ==== معالجة مسح وتعديل المنشورات ====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_post_id'])) {
        $del_id = intval($_POST['delete_post_id']);
        $st = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $st->execute([$del_id, $user_id]);
        header("Location: profile.php");
        exit;
    }

    if (isset($_POST['edit_post_id']) && isset($_POST['edit_content'])) {
        $edit_id = intval($_POST['edit_post_id']);
        $new_content = trim($_POST['edit_content']);
        $st = $pdo->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
        $st->execute([$new_content, $edit_id, $user_id]);
        header("Location: profile.php");
        exit;
    }
}

// 2. تضمين الهيدر بعد التوجيهات
include 'includes/header.php';

// 3. جلب بيانات المستخدمة
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// 3. جلب عدد المساهمات (المنشورات + المقالات) من قاعدة البيانات
$stmt_posts = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
$stmt_posts->execute([$user_id]);
$posts_count = $stmt_posts->fetchColumn();

$stmt_articles = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE user_id = ?");
$stmt_articles->execute([$user_id]);
$articles_count = $stmt_articles->fetchColumn();

$contributions_count = $posts_count + $articles_count;

// 4. جلب عدد المحفوظات (Bookmarks)
$stmt_saved = $pdo->prepare("SELECT COUNT(*) FROM bookmarks WHERE user_id = ?");
$stmt_saved->execute([$user_id]);
$saved_count = $stmt_saved->fetchColumn();

// 5. جلب النشاطات (منشوراتي + ما أعجبت به + ما علقت عليه)
$stmt_activities = $pdo->prepare("
    SELECT 
        'post' COLLATE utf8mb4_unicode_ci AS activity_type,                   
        p.id AS activity_id, 
        p.created_at AS activity_date, 
        p.id AS post_id, 
        p.title COLLATE utf8mb4_unicode_ci AS post_title,
        p.content COLLATE utf8mb4_unicode_ci AS post_content,
        p.type COLLATE utf8mb4_unicode_ci AS post_type,
        c.name COLLATE utf8mb4_unicode_ci AS community_name,
        u.name COLLATE utf8mb4_unicode_ci AS author_name,
        u.avatar COLLATE utf8mb4_unicode_ci AS author_avatar,
        p.content COLLATE utf8mb4_unicode_ci AS activity_content
    FROM posts p
    JOIN communities c ON p.community_id = c.id
    JOIN users u ON p.user_id = u.id
    WHERE p.user_id = :uid1
    
    UNION ALL
    
    SELECT 
        'comment' COLLATE utf8mb4_unicode_ci AS activity_type, 
        pc.id AS activity_id, 
        pc.created_at AS activity_date, 
        p.id AS post_id, 
        p.title COLLATE utf8mb4_unicode_ci AS post_title,
        p.content COLLATE utf8mb4_unicode_ci AS post_content,
        p.type COLLATE utf8mb4_unicode_ci AS post_type,
        c.name COLLATE utf8mb4_unicode_ci AS community_name,
        u.name COLLATE utf8mb4_unicode_ci AS author_name,
        u.avatar COLLATE utf8mb4_unicode_ci AS author_avatar,
        pc.content COLLATE utf8mb4_unicode_ci AS activity_content
    FROM post_comments pc
    JOIN posts p ON pc.post_id = p.id
    JOIN communities c ON p.community_id = c.id
    JOIN users u ON p.user_id = u.id
    WHERE pc.user_id = :uid2
    
    UNION ALL
    
    SELECT 
        'like' COLLATE utf8mb4_unicode_ci AS activity_type, 
        pl.id AS activity_id, 
        pl.created_at AS activity_date, 
        p.id AS post_id, 
        p.title COLLATE utf8mb4_unicode_ci AS post_title,
        p.content COLLATE utf8mb4_unicode_ci AS post_content,
        p.type COLLATE utf8mb4_unicode_ci AS post_type,
        c.name COLLATE utf8mb4_unicode_ci AS community_name,
        u.name COLLATE utf8mb4_unicode_ci AS author_name,
        u.avatar COLLATE utf8mb4_unicode_ci AS author_avatar,
        '' COLLATE utf8mb4_unicode_ci AS activity_content
    FROM post_likes pl
    JOIN posts p ON pl.post_id = p.id
    JOIN communities c ON p.community_id = c.id
    JOIN users u ON p.user_id = u.id
    WHERE pl.user_id = :uid3
    
    ORDER BY activity_date DESC
");
$stmt_activities->execute([
    'uid1' => $user_id, 
    'uid2' => $user_id, 
    'uid3' => $user_id
]);
$activities = $stmt_activities->fetchAll();

// 6. جلب المقالات والمنشورات المحفوظة (المكتبة الخاصة)
$stmt_saved_posts = $pdo->prepare("
    SELECT p.*, c.name AS community_name, u.name AS user_name, u.avatar AS user_avatar
    FROM post_saves ps
    JOIN posts p ON ps.post_id = p.id
    JOIN communities c ON p.community_id = c.id
    JOIN users u ON p.user_id = u.id
    WHERE ps.user_id = ?
    ORDER BY ps.created_at DESC
");
$stmt_saved_posts->execute([$user_id]);
$saved_posts = $stmt_saved_posts->fetchAll();
?>

<style>
/* Feed Cards styling to match community */
.feed-card {
    background-color: #ffffff;
    border-radius: 1.5rem;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    border: 1px solid rgba(0,0,0,0.04);
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    text-align: right;
}
.feed-card:hover { box-shadow: 0 10px 30px rgba(155, 98, 112, 0.08); }
.post-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
.user-info { display: flex; align-items: center; gap: 1rem; }
.user-avatar { width: 3rem; height: 3rem; border-radius: 50%; object-fit: cover; }
.user-name { font-weight: 800; color: var(--primary-dark); font-family: var(--font-headline); }
.post-meta { font-size: 0.8rem; color: var(--secondary); margin-top: 0.2rem; }
.badge-type { padding: 0.3rem 0.8rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; }
.badge-question { background: #e0f2fe; color: #0284c7; }
.badge-vent { background: #fce7f3; color: #be185d; }
.badge-advice { background: #fef3c7; color: #d97706; }
.badge-article { background: var(--primary-container); color: var(--primary-dark); }
.post-content { color: var(--on-surface); line-height: 1.7; font-size: 1.05rem; }

/* CRUD Buttons */
.post-actions-crud {
    display: flex;
    gap: 0.8rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0,0,0,0.04);
}
.btn-crud-edit, .btn-crud-delete {
    background: none;
    border: none;
    padding: 0.4rem 1rem;
    border-radius: 1.5rem;
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.2s ease;
    font-family: inherit;
}
.btn-crud-edit {
    color: #0284c7;
    background-color: #e0f2fe;
}
.btn-crud-edit:hover { background-color: #bae6fd; color: #0369a1; }
.btn-crud-delete {
    color: #be185d;
    background-color: #fce7f3;
}
.btn-crud-delete:hover { background-color: #fbcfe8; color: #9d174d; }
</style>

<!-- Profile Header Section -->
<section class="profile-header-section">
    <div class="profile-cover">
        <!-- غلاف افتراضي أنيق لملاذ -->
        <img src="https://images.unsplash.com/photo-1579546929518-9e396f3cc809?q=80&w=2070&auto=format&fit=crop" alt="Cover">
        <div class="cover-overlay"></div>
    </div>
    
    <div class="container relative">
        <div class="profile-info-card animate-fade-in-up">
            <div class="profile-avatar-large">
                <!-- عرض الصورة الشخصية أو صورة افتراضية -->
                <img src="<?= !empty($user['avatar']) ? $user['avatar'] : 'assets/images/default-avatar.png'; ?>" alt="Profile Picture">
            </div>
            
            <div class="profile-details flex justify-between items-center w-full">
                <div>
                    <h1 class="font-headline font-black text-primary-dark" style="font-size: 2.2rem; margin-bottom: 0.25rem;">
                        <?= htmlspecialchars($user['name']); ?>
                    </h1>
                    <p class="text-secondary" style="font-size: 1.1rem;"><?= htmlspecialchars($user['email']); ?></p>
                    <p id="user-bio-display" style="color: var(--on-surface); max-width: 600px; margin-top: 0.75rem; line-height: 1.6;">
                        <?= !empty($user['bio']) ? htmlspecialchars($user['bio']) : "مرحباً بكِ في مساحتكِ الخاصة في ملاذ. يمكنكِ إضافة نبذة عنكِ من الإعدادات."; ?>
                    </p>
                </div>
                
                <div class="hidden-mobile" style="display: flex; gap: 1.5rem; background: var(--surface-container-low); padding: 1rem 2rem; border-radius: 1.5rem;">
                    <div style="text-align: center;">
                        <span class="font-headline font-bold text-primary" style="font-size: 1.5rem; display: block;"><?= $contributions_count; ?></span>
                        <span style="font-size: 0.85rem; color: var(--secondary);">مساهمة</span>
                    </div>
                    <div style="width: 1px; background-color: var(--outline-variant);"></div>
                    <div style="text-align: center;">
                        <span class="font-headline font-bold text-primary" style="font-size: 1.5rem; display: block;"><?= $saved_count; ?></span>
                        <span style="font-size: 0.85rem; color: var(--secondary);">محفوظة</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tabs Navigation -->
<section style="background-color: #FFFFFF; border-bottom: 1px solid var(--outline-variant); position: sticky; top: 4.5rem; z-index: 30; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
    <div class="container">
        <div class="profile-tabs flex gap-8">
            <button class="profile-tab active" onclick="switchTab('personal-info')">إعدادات الحساب</button>
            <button class="profile-tab" onclick="switchTab('contributions')">نشاطاتي في المجتمع</button>
            <button class="profile-tab" onclick="switchTab('saved-items')">المكتبة الخاصة</button>
        </div>
    </div>
</section>

<!-- Profile Body -->
<section style="padding: 3rem 0; min-height: 60vh; background-color: var(--surface);">
    <div class="container">
        
        <!-- Tab 1: Personal Info & Settings -->
        <div class="tab-content active animate-fade-in-up" id="personal-info">
            <div class="card" style="max-width: 800px; margin: 0 auto; border-radius: 2rem;">
                <h3 class="font-headline font-bold text-primary-dark" style="font-size: 1.4rem; margin-bottom: 1.5rem;">تعديل الملف الشخصي</h3>
                
                <!-- سنوجه الفورم لنفس الصفحة لمعالجة التحديث لاحقاً -->
                <form action="update_profile.php" method="POST" enctype="multipart/form-data">

<!-- قسم الصورة الشخصية داخل الفورم -->
    <div class="profile-avatar-large" style="margin-bottom: 2rem;">
        <img src="<?= !empty($user['avatar']) ? $user['avatar'] : 'assets/images/default-avatar.png'; ?>" id="avatar-preview" alt="Profile">
        
        <!-- حقل اختيار الملف مخفي ويتم تفعيله بالضغط على الأيقونة -->
        <input type="file" name="profile_image" id="profile_image_input" accept="image/png, image/jpeg, image/jpg" style="display: none;" onchange="previewImage(this)">
        
        <button type="button" class="edit-avatar-btn" onclick="document.getElementById('profile_image_input').click()">
            <i class="fa-solid fa-camera"></i>
        </button>
    </div>

                    <div class="input-group">
                        <label class="input-label">الاسم الكامل</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">البريد الإلكتروني (لا يمكن تغييره حالياً)</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" disabled style="background-color: var(--surface-container-high);">
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">نبذة عني (سيظهر في المجتمع)</label>
                        <textarea name="bio" class="form-control" rows="3" placeholder="اكتبي شيئاً عن نفسكِ..."><?= htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>

                    <div style="border-top: 1px dashed var(--outline-variant); margin: 2rem 0;"></div>
                    
                    <h3 class="font-headline font-bold text-primary-dark" style="font-size: 1.2rem; margin-bottom: 1rem;">تأمين الحساب</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="input-group">
                            <label class="input-label">كلمة مرور جديدة</label>
                            <input type="password" name="new_password" class="form-control" placeholder="اتركيه فارغاً إذا لا ترغبين بالتغيير">
                        </div>
                        <div class="input-group">
                            <label class="input-label">تأكيد كلمة المرور</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="أعيدي كتابتها">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 2rem; gap: 1rem;">
                         <button type="submit" class="btn-primary" style="padding: 0.8rem 2.5rem;">تحديث البيانات</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab 2: Contributions -->
        <div class="tab-content animate-fade-in-up" id="contributions" style="display:none;">
            <div style="max-width: 800px; margin: 0 auto;">
                <?php if($activities): ?>
                    <?php foreach($activities as $act): ?>
                        <div class="feed-card">
                            <!-- رأس النشاط -->
                            <?php if ($act['activity_type'] === 'like'): ?>
                                <div style="color:var(--secondary); font-size:0.9rem; margin-bottom:1rem; padding-bottom: 0.5rem; border-bottom: 1px dashed var(--outline-variant);">
                                    <i class="fa-solid fa-heart" style="color:#e11d48; margin-left: 0.3rem;"></i> 
                                    أعجبتِ بمنشور لـ <b><?=htmlspecialchars($act['author_name'])?></b> في <b><?=htmlspecialchars($act['community_name'])?></b>
                                    <span style="float: left; font-size: 0.8rem;"><?=date('Y-m-d H:i', strtotime($act['activity_date']))?></span>
                                </div>
                            <?php elseif ($act['activity_type'] === 'comment'): ?>
                                <div style="color:var(--secondary); font-size:0.9rem; margin-bottom:1rem; padding-bottom: 0.5rem; border-bottom: 1px dashed var(--outline-variant);">
                                    <i class="fa-solid fa-comment" style="color:var(--primary); margin-left: 0.3rem;"></i> 
                                    علقتِ على منشور لـ <b><?=htmlspecialchars($act['author_name'])?></b> في <b><?=htmlspecialchars($act['community_name'])?></b>
                                    <span style="float: left; font-size: 0.8rem;"><?=date('Y-m-d H:i', strtotime($act['activity_date']))?></span>
                                    <div style="margin-top: 0.5rem; background: var(--surface-container-high); padding: 0.8rem 1rem; border-radius: 1rem; color: var(--on-surface);">
                                        "<?=nl2br(htmlspecialchars($act['activity_content']))?>"
                                    </div>
                                </div>
                            <?php else: ?>
                                <div style="color:var(--secondary); font-size:0.9rem; margin-bottom:1rem; padding-bottom: 0.5rem; border-bottom: 1px dashed var(--outline-variant);">
                                    <i class="fa-solid fa-pen-nib" style="color:var(--primary-dark); margin-left: 0.3rem;"></i> 
                                    قمتِ بنشر مشاركة في <b><?=htmlspecialchars($act['community_name'])?></b>
                                    <span style="float: left; font-size: 0.8rem;"><?=date('Y-m-d H:i', strtotime($act['activity_date']))?></span>
                                </div>
                            <?php endif; ?>

                            <!-- تفاصيل المنشور الأصلي -->
                            <div style="opacity: <?= ($act['activity_type'] === 'post') ? '1' : '0.85' ?>; padding-right: <?= ($act['activity_type'] === 'post') ? '0' : '1rem' ?>; border-right: <?= ($act['activity_type'] === 'post') ? 'none' : '3px solid var(--outline-variant)' ?>;">
                                <div class="post-header" style="margin-top: 1rem;">
                                    <div class="user-info">
                                        <img src="<?=htmlspecialchars($act['author_avatar'] ?: 'assets/images/default-avatar.png')?>" alt="U" class="user-avatar" style="width: 2rem; height: 2rem;">
                                        <div>
                                            <b class="user-name" style="font-size: 0.9rem;"><?=htmlspecialchars($act['author_name'])?></b>
                                        </div>
                                    </div>
                                    <span class="badge-type badge-<?=htmlspecialchars($act['post_type'])?>">
                                        <?= ['vent'=>'فضفضة','advice'=>'نصيحة','question'=>'سؤال','article'=>'مقالة'][$act['post_type']] ?? $act['post_type'] ?>
                                    </span>
                                </div>
                                <?php if($act['post_title']): ?>
                                    <h3 style="margin-bottom: .6rem; font-size: 1.1rem;"><?=htmlspecialchars($act['post_title'])?></h3>
                                <?php endif;?>
                                <div class="post-content" style="font-size: 0.95rem;"><?=nl2br(htmlspecialchars($act['post_content']))?></div>
                                
                                <?php if ($act['activity_type'] === 'post'): ?>
                                <!-- أزرار التعديل والمسح (تظهر فقط إذا كان المنشور لي) -->
                                <div class="post-actions-crud">
                                    <button type="button" onclick="toggleEditForm(<?=$act['post_id']?>)" class="btn-crud-edit"><i class="fa-solid fa-pen-to-square"></i> تعديل</button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('هل أنتِ متأكدة من مسح هذا المنشور؟ لا يمكن التراجع.');">
                                        <input type="hidden" name="delete_post_id" value="<?=$act['post_id']?>">
                                        <button type="submit" class="btn-crud-delete"><i class="fa-solid fa-trash"></i> مسح</button>
                                    </form>
                                </div>

                                <!-- نموذج التعديل المخفي -->
                                <div id="edit-form-<?=$act['post_id']?>" style="display:none; margin-top: 1rem; border-top: 1px solid var(--outline-variant); padding-top: 1rem;">
                                    <form method="POST">
                                        <input type="hidden" name="edit_post_id" value="<?=$act['post_id']?>">
                                        <textarea name="edit_content" required style="width: 100%; min-height: 80px; padding: 0.8rem 1.2rem; border-radius: 1rem; border: 1px solid var(--outline-variant); margin-bottom: 0.5rem; font-family: inherit; font-size: 0.95rem; background-color: var(--surface-container-high); resize: none;"><?=htmlspecialchars($act['post_content'])?></textarea>
                                        <div style="display:flex; gap:0.5rem; justify-content: flex-end;">
                                            <button type="button" onclick="toggleEditForm(<?=$act['post_id']?>)" class="btn-outline" style="padding: 0.4rem 1.2rem; font-size: 0.85rem;">إلغاء</button>
                                            <button type="submit" class="btn-primary" style="padding: 0.4rem 1.2rem; font-size: 0.85rem;">حفظ التعديلات</button>
                                        </div>
                                    </form>
                                </div>
                                <?php else: ?>
                                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--outline-variant); text-align: left;">
                                        <a href="community.php?c=all#post-<?=$act['post_id']?>" class="btn-outline" style="font-size: 0.85rem; padding: 0.4rem 1rem;">عرض المنشور في المجتمع</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 4rem 0;">
                        <i class="fa-solid fa-chart-line" style="font-size: 3rem; color: var(--outline-variant); margin-bottom: 1rem;"></i>
                        <h3 style="color: var(--secondary);">لا توجد أي نشاطات بعد.</h3>
                        <a href="community.php" class="btn-outline" style="margin-top: 1rem; display: inline-block;">اذهبي للمجتمع وشاركي الآن</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tab 3: Saved Items -->
        <div class="tab-content animate-fade-in-up" id="saved-items" style="display:none;">
            <div style="max-width: 800px; margin: 0 auto;">
                <?php if($saved_posts): ?>
                    <?php foreach($saved_posts as $post): ?>
                        <div class="feed-card">
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
                            <div class="post-content"><?=nl2br(htmlspecialchars(mb_substr($post['content'], 0, 150)))?>...</div>
                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--outline-variant); text-align: left;">
                                <a href="community.php?c=all#post-<?=$post['id']?>" class="btn-outline" style="font-size: 0.85rem; padding: 0.4rem 1rem;">الذهاب للمنشور</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 4rem 0;">
                        <i class="fa-regular fa-bookmark" style="font-size: 3rem; color: var(--outline-variant); margin-bottom: 1rem;"></i>
                        <h3 style="color: var(--secondary);">مكتبتكِ الخاصة فارغة حالياً.</h3>
                        <p style="color: var(--secondary); margin-top: 0.5rem;">تصفحي المجتمع واضغطي على زر الحفظ للاحتفاظ بالمقالات المفيدة هنا.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<script>
function toggleEditForm(id) {
    var form = document.getElementById('edit-form-' + id);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

// كود بسيط لعرض الصورة فور اختيارها من الجهاز وقبل الرفع
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// وظيفة بسيطة للتنقل بين التبويبات بدون تحميل الصفحة
function switchTab(tabId) {
    // إخفاء كل المحتويات
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    // إزالة الصف الفعال من الأزرار
    document.querySelectorAll('.profile-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // إظهار المحتوى المختار
    document.getElementById(tabId).style.display = 'block';
    // تفعيل الزر المختار
    event.currentTarget.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>