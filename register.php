<?php
require_once 'includes/csrf.php';
include 'includes/header.php';
include 'includes/db.php';

$error = '';
$success = '';

// تعريف المتغيرات فارغة في البداية لمنع ظهور أخطاء في الـ HTML
$first_name = '';
$last_name = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_verify();
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';

    // 1. التحقق من الحقول
    if ($first_name === "" || $last_name === "" || $email === "" || $password === "") {
        $error = "جميع الحقول مطلوبة!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "صيغة البريد الإلكتروني غير صحيحة!";
    } elseif (strlen($password) < 6) {
        $error = "كلمة المرور يجب أن تكون 6 أحرف أو أكثر.";
    } else {
        // 2. التحقق من تكرار البريد الإلكتروني
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetchColumn() > 0) {
            $error = "البريد الإلكتروني مستخدم بالفعل، يرجى استخدام بريد آخر.";
        } else {
            // 3. إدخال البيانات
            $name = $first_name . ' ' . $last_name;
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $insert = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $result = $insert->execute([$name, $email, $password_hash]);

                if($result) {
                    $success = "تم إنشاء الحساب بنجاح! سيتم توجيهك لصفحة الدخول...";
                    echo '<script>setTimeout(function(){ window.location.href = "login.php"; }, 3000);</script>';
                } else {
                    $error = "حدث خطأ غير متوقع، يرجى المحاولة لاحقاً.";
                }
            } catch (PDOException $e) {
                error_log("Register error: " . $e->getMessage());
                $error = "حدث خطأ في الخادم، يرجى المحاولة لاحقاً.";
            }
        }
    }
}
?>

<style>
/* تجميع الستايل الخاص بك هنا */
.login-page-wrapper {
  min-height: calc(100vh - 80px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  background: radial-gradient(circle at top left, var(--surface-container-high) 0%, var(--surface) 100%);
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
    direction: ltr;
  }
  .login-glass-container > * { direction: rtl; }
}

.login-brand-side {
  position: relative;
  background: var(--primary-container);
  padding: 4rem;
  display: none;
  flex-direction: column;
  justify-content: center;
  overflow: hidden;
}

@media (min-width: 992px) { .login-brand-side { display: flex; } }

.login-brand-side::after {
  content: '';
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: linear-gradient(135deg, rgba(155,98,112,0.8) 0%, rgba(197,58,98,0.9) 100%);
}

.brand-content { position: relative; z-index: 10; color: white; }

.login-form-side {
  padding: 2rem 1.5rem;
  background-color: rgba(255, 255, 255, 0.9);
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.form-header { text-align: center; margin-bottom: 2rem; }
.form-header h2 { color: var(--primary-dark); font-weight: 800; }

.form-wrapper { max-width: 400px; margin: 0 auto; width: 100%; }

.alert {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    text-align: center;
    font-size: 0.9rem;
}
.alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }

.input-icon-wrapper { position: relative; }
.input-icon-wrapper i {
  position: absolute;
  right: 1.25rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--secondary);
}
.input-icon-wrapper .form-control { padding-right: 3rem; }

.form-row { display: flex; gap: 1rem; }
.form-row .input-group { width: 100%; }

.separator {
  display: flex;
  align-items: center;
  text-align: center;
  color: var(--outline);
  margin: 1.5rem 0;
}
.separator::before, .separator::after { content: ''; flex: 1; border-bottom: 1px solid var(--outline-variant); }
.separator span { padding: 0 1rem; }
</style>

<div class="login-page-wrapper">
    <div class="login-glass-container animate-fade-in-up">
        
        <!-- الجانب الجمالي -->
        <div class="login-brand-side">
            <div class="brand-content">
                <span class="badge" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4); margin-bottom: 1.5rem; padding: 5px 15px; border-radius: 20px;">
                    انضمي لعائلتنا
                </span>
                <h1 style="font-size: 3rem; line-height: 1.2; margin-bottom: 1.5rem;">بداية جديدة <br>نحو الأفضل</h1>
                <p style="opacity: 0.9; line-height: 1.8;">
                    أنشئي حسابك الآن وكوني جزءاً من مجتمع إيجابي وداعم، حيث تجدين المساحة للتعبير، التعلم، والنمو.
                </p>
            </div>
        </div>
        
        <!-- جانب النموذج -->
        <div class="login-form-side">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2>إنشاء حساب جديد</h2>
                    <p style="color: var(--secondary);">الخطوة الأولى في رحلتك معنا</p>
                </div>

                <!-- عرض رسائل الخطأ والنجاح -->
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form action="register.php" method="POST">
                    <?php csrf_field(); ?>
                    <div class="form-row">
                        <div class="input-group">
                            <label class="input-label">الاسم الأول</label>
                            <div class="input-icon-wrapper">
                                <!-- htmlspecialchars تحمي الموقع من الاختراق وتُرجع القيمة التي كتبها المستخدم -->
                                <input name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" class="form-control" placeholder="مثال: سارة" type="text" required>
                                <i class="fa-regular fa-user"></i>
                            </div>
                        </div>
                        <div class="input-group">
                            <label class="input-label">اسم العائلة</label>
                            <div class="input-icon-wrapper">
                                <input name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" class="form-control" placeholder="مثال: أحمد" type="text" required>
                                <i class="fa-regular fa-user"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">البريد الإلكتروني</label>
                        <div class="input-icon-wrapper">
                            <input name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" placeholder="example@malath.com" type="email" required>
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">كلمة المرور</label>
                        <div class="input-icon-wrapper">
                            <input name="password" class="form-control" placeholder="••••••••" type="password" required>
                            <i class="fa-solid fa-lock"></i>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem; background-color: var(--primary); color: white; padding: 12px; border: none; border-radius: 10px; cursor: pointer;">
                        إنشاء الحساب
                    </button>
                </form>
                
                <div class="separator"><span>أو</span></div>
                
                <div style="text-align: center;">
                    <p style="color: var(--secondary); margin-bottom: 1rem;">أتمتلكين حساباً مسبقاً؟</p>
                    <a href="login.php" class="btn-outline" style="display: block; text-decoration: none; text-align: center; color: var(--primary); border: 1px solid var(--primary); padding: 10px; border-radius: 10px;">
                        تسجيل الدخول
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>