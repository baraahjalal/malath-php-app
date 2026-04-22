<?php
  // Get current file name for active links
  $current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملاذ - الملاذ الرقمي للمرأة العربية</title>
    <!-- Use FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<nav class="glass-nav">
    <div class="container flex justify-between items-center">
        <!-- Logo -->
        <a href="index.php" class="text-primary font-black font-headline" style="font-size: 1.875rem; text-decoration: none; letter-spacing: -0.025em;">ملاذ</a>

        <!-- Desktop Links -->
        <div class="hidden-mobile flex items-center gap-10 font-headline">
            <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php' || $current_page == '') ? 'active' : ''; ?>">الرئيسية</a>
            <a href="community.php" class="nav-link <?php echo ($current_page == 'community.php') ? 'active' : ''; ?>">المجتمع</a>
            <a href="article.php" class="nav-link <?php echo ($current_page == 'article.php') ? 'active' : ''; ?>">المقالات</a>
            <a href="about.php" class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">من نحن</a>
        </div>

        <!-- User Actions -->
        <div class="flex items-center gap-4">
            <div style="position: relative;" id="notification-wrapper">
                <button class="icon-button" id="notification-btn" style="position: relative;">
                    <i class="fa-regular fa-bell" style="font-size: 1.25rem;"></i>
                    <span class="notification-badge"></span>
                </button>
                <div class="notification-dropdown" id="notification-dropdown">
                    <div class="notification-header">
                        <h4 class="font-headline font-bold" style="margin:0;">التنبيهات</h4>
                        <span style="font-size: 0.8rem; color: var(--primary); cursor: pointer;">تحديد الكل كمقروء</span>
                    </div>
                    <div class="notification-list">
                        <div class="notification-item unread">
                            <div class="notification-icon"><i class="fa-solid fa-heart"></i></div>
                            <div class="notification-content">
                                <p>أعجبت سارة بمقالك "رحلة التعافي والنمو"</p>
                                <span class="notification-time">منذ ٥ دقائق</span>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon"><i class="fa-solid fa-comment-dots"></i></div>
                            <div class="notification-content">
                                <p>هناك رد جديد على استفسارك في مجتمع السكينة</p>
                                <span class="notification-time">منذ ساعتين</span>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon"><i class="fa-solid fa-star"></i></div>
                            <div class="notification-content">
                                <p>أهلاً بكِ في ملاذ! اكتشفي المساحات المصممة خصيصاً لكِ.</p>
                                <span class="notification-time">منذ يوم</span>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="notification-footer">عرض كل التنبيهات</a>
                </div>
            </div>
            <a href="profile.php" class="profile-avatar">
                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCx9oi_CffQzQD7xh703WXtQv5Ljs9d-NL5lYKUeMo6f_XnT7ObVTaLv7KUvY-Y6kgsX9iIBk2MHZeWFh2RE7cUrZtReZauD2YFdiksobrJkfQk1VBEJVSKk7cAOk1Mb32f_jcHEQ_sJxy_L9eQFMEuQPGC7zRbdt4roxItlcBpnzVrGB2m74yODejLFaGS0Mpw5VERpLn_CZf-VaZKdkFvIWXUJB1y-S-BniGp3pGOUz_EF2V-oxPWK6KDfKlqLnAdO9ZPq1-Qx_U" alt="Profile">
            </a>
        </div>
    </div>
</nav>

<style>
/* Temporary inline styles for media queries missing in main css */
@media (max-width: 768px) {
  .hidden-mobile { display: none !important; }
}
</style>
<main>
