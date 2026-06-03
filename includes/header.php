<?php
  if (session_status() === PHP_SESSION_NONE) session_start();
  require_once 'includes/csrf.php';
  csrf_generate();
  $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $current_page = trim(str_replace('/malath-php-app', '', $uri), '/');
  if ($current_page === '') $current_page = 'index';

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
        <div style="display:flex; align-items:center; gap:0.5rem;">
            <!-- Mobile Menu Button -->
            <button class="mobile-menu-btn hidden-desktop" id="mobileMenuBtn" title="القائمة">
                <i class="fa-solid fa-bars"></i>
            </button>
            <!-- Logo -->
            <a href="/malath-php-app/index" class="text-primary font-black font-headline" style="font-size: 1.875rem; text-decoration: none; letter-spacing: -0.025em;">ملاذ</a>
        </div>

        <div class="hidden-mobile flex items-center gap-10 font-headline">
            <a href="/malath-php-app/index" class="nav-link <?php echo ($current_page == 'index' || $current_page == '') ? 'active' : ''; ?>">الرئيسية</a>
            <a href="/malath-php-app/community" class="nav-link <?php echo ($current_page == 'community') ? 'active' : ''; ?>">المجتمع</a>
            <a href="/malath-php-app/articles" class="nav-link <?php echo in_array($current_page, ['articles','article-create','articles-single']) ? 'active' : ''; ?>">المقالات</a>
            <a href="/malath-php-app/about" class="nav-link <?php echo ($current_page == 'about') ? 'active' : ''; ?>">من نحن</a>
        </div>

        <!-- User Actions -->
    <div class="flex items-center gap-4">
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- الإشعارات — يُحمَّل بـ AJAX -->
        <div style="position: relative;" id="notification-wrapper">
            <button class="icon-button" id="notification-btn" title="الإشعارات">
                <i class="fa-regular fa-bell" style="font-size: 1.25rem;"></i>
                <span class="notification-badge" id="notif-badge" style="display:none;"></span>
            </button>
            <div class="notification-dropdown" id="notification-dropdown">
                <div class="notification-header">
                    <h4 class="font-headline font-bold" style="margin:0;">التنبيهات</h4>
                    <span id="mark-all-read" style="font-size:0.8rem;color:var(--primary);cursor:pointer;">تحديد الكل كمقروء</span>
                </div>
                <div class="notification-list" id="notif-list">
                    <div style="text-align:center;padding:1.5rem;color:var(--secondary);font-size:.9rem;">جاري التحميل...</div>
                </div>
                <a href="/malath-php-app/community" class="notification-footer">الذهاب للمجتمع</a>
            </div>
        </div>
        <script>
        (function(){
            const CSRF = '<?= htmlspecialchars(csrf_generate()) ?>';
            const btn  = document.getElementById('notification-btn');
            const drop = document.getElementById('notification-dropdown');
            const list = document.getElementById('notif-list');
            const badge= document.getElementById('notif-badge');
            let loaded = false;

            async function loadNotifs() {
                const res  = await fetch('api/notifications.php?action=fetch');
                const data = await res.json();
                if (!data.success) return;
                badge.style.display = data.unread > 0 ? 'inline-block' : 'none';
                badge.textContent   = data.unread > 9 ? '9+' : data.unread;
                if (!data.notifications.length) {
                    list.innerHTML = '<div style="text-align:center;padding:1.5rem;color:var(--secondary);font-size:.9rem;">لا توجد إشعارات بعد.</div>';
                    return;
                }
                list.innerHTML = data.notifications.map(n => `
                    <div class="notification-item ${n.is_read ? '' : 'unread'}">
                        <div class="notification-icon"><i class="fa-solid ${n.icon}"></i></div>
                        <div class="notification-content">
                            <p><strong>${n.actor_name}</strong> ${n.label}</p>
                            <span class="notification-time">${n.time}</span>
                        </div>
                    </div>`).join('');
            }

            btn.addEventListener('click', async (e) => {
                e.preventDefault(); e.stopPropagation();
                drop.classList.toggle('active');
                if (drop.classList.contains('active') && !loaded) {
                    await loadNotifs(); loaded = true;
                }
            });

            document.getElementById('mark-all-read').addEventListener('click', async () => {
                const fd = new FormData();
                await fetch('api/notifications.php?action=mark_read', {
                    method: 'POST', body: fd,
                    headers: { 'X-CSRF-Token': CSRF }
                });
                badge.style.display = 'none';
                list.querySelectorAll('.notification-item').forEach(el => el.classList.remove('unread'));
            });

            document.addEventListener('click', (e) => {
                if (!drop.contains(e.target) && e.target !== btn) drop.classList.remove('active');
            });

            // فحص دوري كل 60 ثانية
            loadNotifs();
            setInterval(loadNotifs, 60000);
        })();
        </script>

        <a href="/malath-php-app/profile" class="profile-avatar" title="ملفي الشخصي">
            <img src="<?= htmlspecialchars($user_avatar); ?>" alt="Profile">
        </a>
        
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a href="/malath-php-app/dashboard" title="لوحة التحكم" class="btn-logout-icon" style="background-color: var(--primary-container); color: var(--primary-dark);">
                <i class="fa-solid fa-chart-line"></i>
            </a>
        <?php endif; ?>
        
        <!-- زر خروج -->
        <a href="/malath-php-app/logout" title="تسجيل الخروج" class="btn-logout-icon">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>

    <?php else: ?>
        <!-- هذا الجزء يظهر للزوار فقط -->
        <div class="auth-buttons-container">
            <a href="/malath-php-app/login?redirect=community.php" class="btn-login-outline">تسجيل الدخول</a>
            <a href="/malath-php-app/register" class="btn-join-header">إنضمي إلينا</a>
        </div>
    <?php endif; ?>
</div>
    </div>
</nav>

<!-- Mobile Drawer -->
<div class="mobile-drawer-overlay" id="mobileDrawerOverlay"></div>
<div class="mobile-drawer" id="mobileDrawer">
    <div class="mobile-drawer-header">
        <a href="/malath-php-app/index" class="text-primary font-black font-headline" style="font-size: 1.875rem; text-decoration: none;">ملاذ</a>
        <button class="close-drawer-btn" id="closeDrawerBtn" title="إغلاق القائمة"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="mobile-drawer-links font-headline">
        <a href="/malath-php-app/index" class="mobile-nav-link <?php echo ($current_page == 'index' || $current_page == '') ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> الرئيسية</a>
        <a href="/malath-php-app/community" class="mobile-nav-link <?php echo ($current_page == 'community') ? 'active' : ''; ?>"><i class="fa-solid fa-users"></i> المجتمع</a>
        <a href="/malath-php-app/articles" class="mobile-nav-link <?php echo in_array($current_page, ['articles','article-create','articles-single']) ? 'active' : ''; ?>"><i class="fa-solid fa-newspaper"></i> المقالات</a>
        <a href="/malath-php-app/about" class="mobile-nav-link <?php echo ($current_page == 'about') ? 'active' : ''; ?>"><i class="fa-solid fa-circle-info"></i> من نحن</a>
    </div>
</div>

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

/* Mobile Drawer Styles */
.mobile-menu-btn {
    display: none;
    background: transparent;
    border: none;
    font-size: 1.5rem;
    color: var(--primary);
    cursor: pointer;
    padding: 0.5rem;
    transition: all 0.2s;
}

.mobile-menu-btn:active { transform: scale(0.9); }

.mobile-drawer-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    z-index: 99;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.mobile-drawer-overlay.active {
    opacity: 1;
    visibility: visible;
}

.mobile-drawer {
    position: fixed;
    top: 0; right: -300px; /* Start hidden */
    width: 280px;
    height: 100vh;
    background: var(--surface);
    z-index: 100;
    box-shadow: -5px 0 25px rgba(0,0,0,0.1);
    transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}

.mobile-drawer.active {
    right: 0;
}

.mobile-drawer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--outline-variant);
}

.close-drawer-btn {
    background: var(--surface-container);
    border: none;
    width: 2.5rem; height: 2.5rem;
    border-radius: 50%;
    font-size: 1.2rem;
    color: var(--secondary);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
}

.mobile-drawer-links {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.mobile-nav-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.2rem;
    text-decoration: none;
    color: var(--on-surface);
    font-weight: 700;
    border-radius: 1rem;
    transition: all 0.2s;
    font-size: 1.05rem;
}

.mobile-nav-link i { width: 24px; text-align: center; color: var(--secondary); }

.mobile-nav-link.active {
    background: var(--primary-gradient);
    color: white;
}
.mobile-nav-link.active i { color: white; }

/* Responsive Adjustments */
@media (max-width: 768px) {
  .hidden-mobile { display: none !important; }
  .mobile-menu-btn { display: flex; align-items: center; justify-content: center; }
  .hidden-desktop { display: flex !important; }
}
@media (min-width: 769px) {
  .hidden-desktop { display: none !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const mobileOverlay = document.getElementById('mobileDrawerOverlay');
    const closeDrawerBtn = document.getElementById('closeDrawerBtn');

    function openDrawer() {
        mobileDrawer.classList.add('active');
        mobileOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }

    function closeDrawer() {
        mobileDrawer.classList.remove('active');
        mobileOverlay.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    }

    if (mobileBtn && mobileDrawer) {
        mobileBtn.addEventListener('click', openDrawer);
        closeDrawerBtn.addEventListener('click', closeDrawer);
        mobileOverlay.addEventListener('click', closeDrawer);
    }
});
</script>
<main>
