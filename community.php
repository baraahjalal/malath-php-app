<?php 
// بدء الجلسة إذا لم تكن مبدوءة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php'; 

// التحقق من المجتمع المختار في الرابط (مثال: community.php?c=health)
// إذا لم يتم تحديد مجتمع، نعرض "كل المجتمعات"
$current_community = $_GET['c'] ?? 'all';
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
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.create-post-input {
    flex-grow: 1;
    background-color: var(--surface-container-high);
    border-radius: 2rem;
    padding: 1rem 1.5rem;
    color: var(--secondary);
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: var(--font-body);
}

.create-post-input:hover {
    background-color: var(--surface-container);
    border-color: rgba(197, 58, 98, 0.1);
}

.create-post-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--outline-variant);
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
</style>

<!-- Header Section -->
<div class="community-header">
    <div class="container animate-fade-in-up">
        <h1 class="community-title">مجتمعات ملاذ</h1>
        <p style="color: var(--secondary); font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
            مساحتك الآمنة للتفاعل، التعلم، ومشاركة تجاربك في بيئة نسائية داعمة ومحفزة.
        </p>
        
        <!-- Tabs for the 4 Pillars -->
        <div class="community-tabs-container">
            <a href="community.php?c=all" class="community-tab <?= $current_community == 'all' ? 'active' : '' ?>">
                <i class="fa-solid fa-layer-group"></i> الأحدث
            </a>
            <a href="community.php?c=health" class="community-tab <?= $current_community == 'health' ? 'active' : '' ?>">
                <i class="fa-solid fa-seedling"></i> الصحي
            </a>
            <a href="community.php?c=psychology" class="community-tab <?= $current_community == 'psychology' ? 'active' : '' ?>">
                <i class="fa-solid fa-heart-pulse"></i> النفسي
            </a>
            <a href="community.php?c=religion" class="community-tab <?= $current_community == 'religion' ? 'active' : '' ?>">
                <i class="fa-solid fa-book-open"></i> الديني
            </a>
            <a href="community.php?c=academic" class="community-tab <?= $current_community == 'academic' ? 'active' : '' ?>">
                <i class="fa-solid fa-star"></i> الأكاديمي
            </a>
        </div>
    </div>
</div>

<div class="community-layout">
    <!-- Sidebar (Visual Right) -->
    <aside class="animate-fade-in-up delay-100">
        <!-- مجتمعاتي -->
        <div class="sidebar-widget">
            <h3 class="widget-title">مجتمعاتي <span style="font-size:0.8rem; font-weight:normal; color:var(--secondary);">(المنضمة لها)</span></h3>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <!-- هنا سيتم جلب المجتمعات التي انضمت لها المستخدمة من الداتا بيز (جدول user_communities) -->
                <a href="community.php?c=psychology" class="community-list-item">
                    <div class="community-icon-small"><i class="fa-solid fa-heart-pulse"></i></div>
                    <div style="font-weight: 700;">المجتمع النفسي</div>
                </a>
                <a href="community.php?c=academic" class="community-list-item">
                    <div class="community-icon-small"><i class="fa-solid fa-star"></i></div>
                    <div style="font-weight: 700;">المجتمع الأكاديمي</div>
                </a>
            <?php else: ?>
                <div style="text-align: center; padding: 1rem 0;">
                    <p style="font-size: 0.9rem; color: var(--secondary); margin-bottom: 1rem;">سجلي دخولك لتتمكني من الانضمام للمجتمعات وتخصيص تجربتك.</p>
                    <a href="login.php?redirect=community.php" class="btn-outline" style="padding: 6px 15px; font-size: 0.9rem; text-decoration:none;">تسجيل الدخول</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- مساحة إلهام أو قوانين -->
        <div class="sidebar-widget" style="background: var(--surface-container-high); border: none;">
            <h3 class="widget-title" style="display:flex; align-items:center; gap:0.5rem;">
                <i class="fa-solid fa-shield-heart" style="color: var(--primary);"></i> مساحة آمنة
            </h3>
            <ul class="rules-list">
                <li>احترمي آراء الأخريات وتجنبي الأحكام.</li>
                <li>يُمنع نشر أي محتوى يسيء للأديان أو الأشخاص.</li>
                <li>حافظي على سرية الفضفضة والقصص المطروحة هنا.</li>
                <li>المقالات يجب أن تُنشر في مجتمعها الصحيح.</li>
            </ul>
        </div>
    </aside>

    <!-- Main Feed (Visual Left) -->
    <main>
        
        <!-- Create Post Box (Only for Logged In Users) -->
        <?php if(isset($_SESSION['user_id'])): ?>
        <div class="create-post-box animate-fade-in-up">
            <div class="create-post-top">
                <img src="assets/images/default-avatar.png" alt="Profile" class="user-avatar" style="width: 2.5rem; height: 2.5rem;">
                <div class="create-post-input" onclick="alert('سيتم برمجة نافذة منبثقة (Modal) لإضافة منشور لاحقاً')">
                    شاركِ فضفضة، سؤال، أو مقالة مفيدة...
                </div>
            </div>
            <div class="create-post-actions">
                <div class="action-btn-group">
                    <button class="type-btn"><i class="fa-solid fa-pen-nib"></i> مقالة رسمية</button>
                    <button class="type-btn"><i class="fa-regular fa-comment-dots"></i> نقاش وفضفضة</button>
                </div>
                <!-- زر اختيار المجتمع الذي سينشر فيه -->
                <button class="btn-primary" style="padding: 6px 15px; font-size: 0.9rem; border-radius: 1.5rem;">نشر محتوى</button>
            </div>
        </div>
        <?php else: ?>
        <div class="create-post-box animate-fade-in-up" style="text-align: center; padding: 2rem;">
            <h3 style="color: var(--primary-dark); margin-bottom: 0.5rem; font-family: var(--font-headline);">شاركي صوتك معنا</h3>
            <p style="color: var(--secondary); margin-bottom: 1rem;">لتتمكني من طرح الأسئلة أو نشر المقالات، يجب أن تكوني فرداً من عائلة ملاذ.</p>
            <a href="register.php" class="btn-primary" style="text-decoration:none; display:inline-block;">إنشاء حساب جديد</a>
        </div>
        <?php endif; ?>

        <!-- Feed List -->
        <div class="feed-list">
            
            <!-- نموذج 1: منشور فضفضة / طلب نصيحة (Post) -->
            <div class="feed-card animate-fade-in-up delay-100">
                <div class="post-header">
                    <div class="user-info">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=150&auto=format&fit=crop" alt="User" class="user-avatar">
                        <div>
                            <a href="#" class="user-name">نورة أحمد</a>
                            <div class="post-meta">نُشر في <strong>المجتمع النفسي</strong> • منذ ساعتين</div>
                        </div>
                    </div>
                    <span class="badge-type badge-vent">فضفضة</span>
                </div>
                <div class="post-content">
                    السلام عليكم بنات، أنا أمر بفترة ضغط دراسي ونفسي كبيرة جداً هذه الأيام، أحس إني فاقدة للشغف ومش قادرة أركز في أي شيء. هل في حد مر بنفس التجربة؟ وكيف قدرتوا تتجاوزوها وترجعوا لروتينكم؟ محتاجة دعمكم ونصائحكم 💔
                </div>
                <div class="post-footer">
                    <div class="interaction-btns">
                        <button class="interact-btn"><i class="fa-regular fa-heart"></i> ٤٥</button>
                        <button class="interact-btn"><i class="fa-regular fa-comment"></i> ١٢ تعليق</button>
                    </div>
                    <button class="interact-btn" title="حفظ"><i class="fa-regular fa-bookmark"></i></button>
                </div>
            </div>

            <!-- نموذج 2: مقالة رسمية (Article) -->
            <div class="feed-card animate-fade-in-up delay-200">
                <div class="post-header">
                    <div class="user-info">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=150&auto=format&fit=crop" alt="User" class="user-avatar">
                        <div>
                            <a href="#" class="user-name">د. سارة محمد</a>
                            <div class="post-meta">نُشر في <strong>المجتمع الصحي</strong> • أمس</div>
                        </div>
                    </div>
                    <span class="badge-type badge-article">مقالة</span>
                </div>
                <h2 style="font-family: var(--font-headline); font-weight: 800; color: var(--primary-dark); margin-bottom: 1rem;">دليلك الشامل لتغذية صحية خلال فترات التوتر</h2>
                <img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?q=80&w=1453&auto=format&fit=crop" alt="Healthy Food" class="article-image-preview">
                <div class="post-content" style="font-size: 0.95rem; color: var(--secondary);">
                    في أوقات التوتر والضغط، غالباً ما نلجأ إلى الأطعمة السريعة أو المليئة بالسكريات كنوع من التعويض العاطفي، لكن هذا يزيد الأمر سوءاً على المدى الطويل. في هذا المقال نتعرف على أطعمة تساهم في تعديل المزاج وتهدئة الأعصاب بشكل علمي وصحي...
                    <a href="article.php?id=1" style="color: var(--primary); font-weight: 700; text-decoration: none;">(اقرئي المزيد)</a>
                </div>
                <div class="post-footer">
                    <div class="interaction-btns">
                        <button class="interact-btn" style="color: var(--primary);"><i class="fa-solid fa-heart"></i> ٢١٠</button>
                        <button class="interact-btn"><i class="fa-regular fa-comment"></i> ٣٤ تعليق</button>
                    </div>
                    <button class="interact-btn" title="حفظ"><i class="fa-regular fa-bookmark"></i></button>
                </div>
            </div>

            <!-- نموذج 3: سؤال سريع (Post) -->
            <div class="feed-card animate-fade-in-up delay-300">
                <div class="post-header">
                    <div class="user-info">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=150&auto=format&fit=crop" alt="User" class="user-avatar">
                        <div>
                            <a href="#" class="user-name">ريم الخالدي</a>
                            <div class="post-meta">نُشر في <strong>المجتمع الأكاديمي</strong> • قبل ٤ ساعات</div>
                        </div>
                    </div>
                    <span class="badge-type badge-question">سؤال</span>
                </div>
                <div class="post-content">
                    بنات، شنو أفضل المصادر أو المواقع اللي ممكن أتعلم منها اللغة الإنجليزية من الصفر وتكون مجانية؟ دورت كثير واحترت، ياريت اللي عندها تجربة تفيدني.
                </div>
                <div class="post-footer">
                    <div class="interaction-btns">
                        <button class="interact-btn"><i class="fa-regular fa-heart"></i> ١٥</button>
                        <button class="interact-btn"><i class="fa-regular fa-comment"></i> ٨ تعليقات</button>
                    </div>
                    <button class="interact-btn" title="حفظ"><i class="fa-regular fa-bookmark"></i></button>
                </div>
            </div>

            <!-- Load More -->
            <div style="display: flex; justify-content: center; margin-top: 3rem;">
                <button class="btn-outline" style="background: white;">تحميل المزيد من المحتوى <i class="fa-solid fa-chevron-down" style="margin-right:0.5rem;"></i></button>
            </div>

        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>