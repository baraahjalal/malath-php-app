<?php
  // نبدأ الجلسة في أعلى الهيدر لضمان توفرها في كل صفحات الموقع
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }
  $current_page = basename($_SERVER['PHP_SELF']);

  // --- الجزء الجديد المضاف ---
  $user_avatar = 'assets/images/default-avatar.png'; // الصورة الافتراضية
  
  if (isset($_SESSION['user_id'])) {
      include 'includes/db.php'; // تأكدي من مسار ملف الاتصال
      $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
      $stmt->execute([$_SESSION['user_id']]);
      $user_data = $stmt->fetch();
      
      if (!empty($user_data['avatar'])) {
          $user_avatar = $user_data['avatar'];
      }
  }

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
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- هذا الجزء يظهر فقط للمسجلات دخولهن -->
        <div style="position: relative;" id="notification-wrapper">
            <button class="icon-button" id="notification-btn">
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

        <a href="profile.php" class="profile-avatar" title="ملفي الشخصي">
            <img src="<?= htmlspecialchars($user_avatar); ?>" alt="Profile">
        </a>
        
        <!-- زر خروج -->
        <a href="logout.php" title="تسجيل الخروج" class="btn-logout-icon">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>

    <?php else: ?>
        <!-- هذا الجزء يظهر للزوار فقط -->
        <div class="auth-buttons-container">
            <a href="login.php?redirect=community.php" class="btn-login-outline">تسجيل الدخول</a>
            <a href="register.php" class="btn-join-header">إنضمي إلينا</a>
        </div>
    <?php endif; ?>
</div>
    </div>
</nav>

<style>
/* Header Auth Buttons */
.auth-buttons-container {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.btn-login-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1.2rem;
    border-radius: 2rem;
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--primary);
    background-color: transparent;
    border: 2px solid var(--primary-container);
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-login-outline:hover {
    background-color: var(--primary-container);
    color: var(--primary-dark);
}

.btn-join-header {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1.5rem;
    border-radius: 2rem;
    font-size: 0.9rem;
    font-weight: 700;
    color: #fff;
    background: var(--primary-gradient, linear-gradient(135deg, #db2777, #9d174d));
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(219, 39, 119, 0.2);
    transition: all 0.3s ease;
    border: none;
}

.btn-join-header:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(219, 39, 119, 0.3);
    color: #fff;
}

.btn-logout-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.4rem;
    height: 2.4rem;
    border-radius: 50%;
    background-color: var(--surface-container, #f3f4f6);
    color: var(--secondary, #4b5563);
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-logout-icon:hover {
    background-color: #fee2e2;
    color: #ef4444;
}
/* Temporary inline styles for media queries missing in main css */
@media (max-width: 768px) {
  .hidden-mobile { display: none !important; }
}
</style>
<main>
