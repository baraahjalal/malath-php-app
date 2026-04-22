<?php include 'includes/header.php'; ?>

<style>
/* Register Page Layout reusing login styles but adjusted */
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
    /* Reverse order for variety */
    grid-template-columns: 1fr 1fr;
    direction: ltr; /* Temporarily switch direction to place image on right side visually */
  }
  
  .login-glass-container > * {
    direction: rtl; /* Reset internal direction */
  }
}

/* Right Side - Image & Branding */
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
  background: url('https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?q=80&w=1480&auto=format&fit=crop') center/cover;
  opacity: 0.8;
  mix-blend-mode: multiply;
}

.login-brand-side::after {
  content: '';
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: linear-gradient(135deg, rgba(155,98,112,0.8) 0%, rgba(197,58,98,0.9) 100%);
}

.brand-content {
  position: relative;
  z-index: 10;
  color: white;
}

/* Left Side - Form */
.login-form-side {
  padding: 2rem 1.5rem;
  background-color: rgba(255, 255, 255, 0.9);
  display: flex;
  flex-direction: column;
  justify-content: center;
}

@media (min-width: 768px) {
  .login-form-side { padding: 3rem 3.5rem; }
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
  padding-right: 3rem;
  background: rgba(255, 255, 255, 0.6);
}

.input-icon-wrapper .form-control:focus + i {
  color: var(--primary);
}

.separator {
  display: flex;
  align-items: center;
  text-align: center;
  color: var(--outline);
  margin: 1.5rem 0;
}

.separator::before, .separator::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid var(--outline-variant);
}

.separator span { padding: 0 1rem; font-size: 0.875rem; }

.form-row {
  display: flex;
  gap: 1rem;
}
.form-row .input-group { width: 100%; }
</style>

<div class="login-page-wrapper">
    <div class="login-glass-container animate-fade-in-up">
        
        <!-- Image Side -->
        <div class="login-brand-side">
            <div class="brand-content">
                <span class="badge" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4); margin-bottom: 1.5rem;">
                    انضمي لعائلتنا
                </span>
                <h1 class="font-headline font-black" style="font-size: 3.5rem; line-height: 1.2; margin-bottom: 1.5rem; text-shadow: 0 4px 10px rgba(0,0,0,0.2);">بداية جديدة <br>نحو الأفضل</h1>
                <p style="font-size: 1.15rem; max-width: 25rem; line-height: 1.8; opacity: 0.9;">
                    أنشئي حسابك الآن وكوني جزءاً من مجتمع إيجابي وداعم، حيث تجدين المساحة للتعبير، التعلم، والنمو.
                </p>
            </div>
        </div>
        
        <!-- Form Side -->
        <div class="login-form-side">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2>إنشاء حساب جديد</h2>
                    <p style="color: var(--secondary);">الخطوة الأولى في رحلتك معنا</p>
                </div>
                
                <form action="register.php" method="POST">
                    
                    <div class="form-row">
                        <div class="input-group">
                            <label class="input-label">الاسم الأول</label>
                            <div class="input-icon-wrapper">
                                <input name="first_name" class="form-control" placeholder="مثال: سارة" type="text" required>
                                <i class="fa-regular fa-user"></i>
                            </div>
                        </div>
                        <div class="input-group">
                            <label class="input-label">اسم العائلة</label>
                            <div class="input-icon-wrapper">
                                <input name="last_name" class="form-control" placeholder="مثال: أحمد" type="text" required>
                                <i class="fa-regular fa-user"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">البريد الإلكتروني</label>
                        <div class="input-icon-wrapper">
                            <input name="email" class="form-control" placeholder="example@malath.com" type="email" required>
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
                    
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">إنشاء الحساب</button>
                </form>
                
                <div class="separator"><span>أو</span></div>
                
                <div style="text-align: center;">
                    <p style="color: var(--secondary); margin-bottom: 1rem;">أتمتلكين حساباً مسبقاً؟</p>
                    <a href="login.php" class="btn-outline" style="display: block; text-decoration: none; text-align: center;">
                        تسجيل الدخول
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
