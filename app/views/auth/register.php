<?php include ROOT_PATH . '/includes/header.php'; ?>
<?php if (!empty($auto_redirect)): ?>
<script>setTimeout(function(){ window.location.href="/malath-php-app/login.php"; }, 3000);</script>
<?php endif; ?>

<style>
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
.alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
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

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form action="/malath-php-app/register.php" method="POST">
                    <?php csrf_field(); ?>
                    <div class="form-row">
                        <div class="input-group">
                            <label class="input-label">الاسم الأول</label>
                            <div class="input-icon-wrapper">
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

                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">
                        إنشاء الحساب
                    </button>
                </form>

                <div class="separator"><span>أو</span></div>

                <div style="text-align: center;">
                    <p style="color: var(--secondary); margin-bottom: 1rem;">أتمتلكين حساباً مسبقاً؟</p>
                    <a href="/malath-php-app/login.php" class="btn-outline" style="display: block; text-decoration: none; text-align: center;">
                        تسجيل الدخول
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
