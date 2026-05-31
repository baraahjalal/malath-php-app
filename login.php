<?php


// 1. البدء بالجلسة مع فحص إذا كانت مبدوءة مسبقاً
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/csrf.php';
csrf_generate();

include 'includes/db.php';



// 3. إعادة التوجيه إذا كانت مسجلة دخول أصلاً
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// تعريف المتغيرات لتجنب أخطاء Undefined Variable
$error = '';
$email = '';
// قائمة الصفحات الداخلية المسموح بالتوجيه إليها
$allowed_redirects = [
    'index.php', 'community.php', 'profile.php',
    'article.php', 'about.php', 'faq.php',
];
$raw_redirect  = $_GET['redirect'] ?? 'index.php';
$redirect_to   = in_array($raw_redirect, $allowed_redirects, true) ? $raw_redirect : 'index.php';

// 4. معالجة طلب تسجيل الدخول
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    csrf_verify();
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // استعلام قاعدة البيانات (تأكدي من أسماء الجداول لديكِ)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'] ?? 'user';
            $dest = ($_SESSION['user_role'] === 'admin') ? 'dashboard.php' : $redirect_to;
            header("Location: " . $dest);
            exit;
        } else {
            $error = "خطأ في البريد الإلكتروني أو كلمة المرور.";
        }
    } else {
        $error = "يرجى ملء جميع الحقول.";
    }
}



// 5. التضمين يأتي بعد المنطق
include 'includes/header.php'; 
?>

<style>
/* Refined Login Layout */
.login-page-wrapper {
  min-height: calc(100vh - 80px); /* minus nav height */
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  background: radial-gradient(circle at top right, var(--surface-container-high) 0%, var(--surface) 100%);
}

.login-glass-container {
  display: grid;
  grid-template-columns: 1fr;
  width: 100%;
  max-width: 1100px;
  background: rgba(255, 255, 255, 0.4);
  backdrop-filter: blur(20px);
  border-radius: 2rem;
  overflow: hidden;
  box-shadow: var(--shadow-lg);
  border: 1px solid rgba(255, 255, 255, 0.8);
}

@media (min-width: 992px) {
  .login-glass-container {
    grid-template-columns: 1fr 1fr;
  }
}

/* Left Side - Image & Branding */
.login-brand-side {
  position: relative;
  background: var(--primary-container);
  padding: 4rem;
  display: none;
  flex-direction: column;
  justify-content: center;
  overflow: hidden;
}

@media (min-width: 992px) {
  .login-brand-side { display: flex; }
}

.login-brand-side::before {
  content: '';
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=1284&auto=format&fit=crop') center/cover;
  opacity: 0.8;
  mix-blend-mode: multiply;
}

.login-brand-side::after {
  content: '';
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: linear-gradient(135deg, rgba(197,58,98,0.7) 0%, rgba(165,43,77,0.9) 100%);
}

.brand-content {
  position: relative;
  z-index: 10;
  color: white;
}

/* Right Side - Form */
.login-form-side {
  padding: 2rem 1.5rem;
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  flex-direction: column;
  justify-content: center;
}

@media (min-width: 768px) {
  .login-form-side { padding: 4rem 3.5rem; }
}

.form-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.form-header h2 {
  font-family: var(--font-headline);
  font-size: 2.25rem;
  color: var(--primary-dark);
  font-weight: 800;
  margin-bottom: 0.5rem;
}

.form-wrapper {
  max-width: 400px;
  margin: 0 auto;
  width: 100%;
}

.input-icon-wrapper {
  position: relative;
}

.input-icon-wrapper i {
  position: absolute;
  right: 1.25rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--secondary);
  transition: var(--transition-fast);
}

.input-icon-wrapper .form-control {
  padding-right: 3rem; /* Space for icon */
  background: rgba(255, 255, 255, 0.6);
}

.input-icon-wrapper .form-control:focus + i {
  color: var(--primary);
}

.checkbox-custom {
  appearance: none;
  width: 1.25rem;
  height: 1.25rem;
  border: 2px solid var(--outline);
  border-radius: 0.25rem;
  background-color: transparent;
  display: inline-block;
  position: relative;
  cursor: pointer;
  transition: var(--transition-fast);
}

.checkbox-custom:checked {
  background-color: var(--primary);
  border-color: var(--primary);
}

.checkbox-custom:checked::after {
  content: '\f00c';
  font-family: 'Font Awesome 6 Free';
  font-weight: 900;
  color: white;
  font-size: 0.75rem;
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
}

.separator {
  display: flex;
  align-items: center;
  text-align: center;
  color: var(--outline);
  margin: 2rem 0;
}

.separator::before, .separator::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid var(--outline-variant);
}

.separator span { padding: 0 1rem; font-size: 0.875rem; }
</style>


<div class="login-page-wrapper">
    <div class="login-glass-container animate-fade-in-up">
        <!-- جانب النموذج -->
        <div class="login-form-side">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2>مرحباً بكِ مجدداً</h2>
                    <p style="color: var(--secondary);">سجلي دخولك لمتابعة رحلتك في ملاذ</p>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-error" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:1rem;border-radius:8px;margin-bottom:12px;">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <?php csrf_field(); ?>
                    <div class="input-group">
                        <label class="input-label">البريد الإلكتروني</label>
                        <div class="input-icon-wrapper">
                            <input name="email"
                                   class="form-control"
                                   placeholder="example@malath.com"
                                   type="email"
                                   required
                                   value="<?php echo htmlspecialchars($email); ?>">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                    </div>
                    
                    <div class="input-group" style="margin-bottom: 1rem;">
                        <div class="flex justify-between items-center" style="margin-bottom: 0.5rem;">
                            <label class="input-label" style="margin-bottom: 0;">كلمة المرور</label>
                            <!-- يمكنك الربط لاحقاً مع صفحة استرجاع كلمة المرور -->
                            <a href="#" style="font-size: 0.85rem; color: var(--primary); text-decoration: none; font-weight: 600;">نسيتِ كلمة المرور؟</a>
                        </div>
                        <div class="input-icon-wrapper">
                            <input name="password"
                                   class="form-control"
                                   placeholder="••••••••"
                                   type="password"
                                   required>
                            <i class="fa-solid fa-lock"></i>
                        </div>
                    </div>
                    
                    <!-- تفعيل remember me لاحقاً (كوكيز)، الآن تجاهله -->
                    
                    <button type="submit" class="btn-primary" style="width: 100%;">تسجيل الدخول</button>
                </form>
                
                <div class="separator"><span>أو</span></div>
                <div style="text-align: center;">
                    <p style="color: var(--secondary); margin-bottom: 1rem;">ليس لديكِ حساب بعد؟</p>
                    <a href="register.php" class="btn-outline" style="display: block;
                            text-decoration: none;
                            text-align: center;">
                        إنشاء حساب جديد
                    </a>
                </div>
            </div>
        </div>

        <!-- الجانب الجمالي (يمكن إبقاؤه كما هو أو تعديله) -->
        <div class="login-brand-side">
            <div class="brand-content">
                <span class="badge" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4); margin-bottom: 1.5rem;">
                    مساحة آمنة لكل امرأة
                </span>
                <h1 class="font-headline font-black" style="font-size: 4.5rem; line-height: 1.1; margin-bottom: 1.5rem; text-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                    ملاذ
                </h1>
                <p style="font-size: 1.15rem; max-width: 28rem; line-height: 1.8; opacity: 0.9;">
                    اكتشفي الهدوء، الأمان، والتمكين في مجتمعنا الرقمي المصمم خصيصاً ليكون رفيقك في رحلة النمو والتواصل.
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>