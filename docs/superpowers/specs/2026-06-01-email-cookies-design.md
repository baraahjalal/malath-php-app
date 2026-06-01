# تصميم خاصية الإيميل وتذكرني — مشروع ملاذ
**Date:** 2026-06-01

---

## 1. نظرة عامة

إضافة ميزتين ناقصتين لإكمال متطلبات المشروع:
1. **إرسال إيميلات** عبر PHPMailer + Gmail SMTP (3 سيناريوهات)
2. **تذكرني (Remember Me)** عبر Cookies + توكن مشفّر في DB

---

## 2. خاصية الإيميل

### المكتبة
- **PHPMailer** مثبّت عبر Composer
- إعدادات Gmail في `includes/email_config.php` (App Password)
- المرسِل: `baraahjalall@gmail.com`

### الكلاس
**`app/core/EmailService.php`**
- دالة خاصة `buildMailer()` تُعدّ اتصال SMTP
- ثلاث دوال عامة:

| الدالة | يُشغَّل من | المستلم |
|--------|------------|---------|
| `sendWelcome(name, email)` | `AuthController::handleRegister()` | المستخدم الجديد |
| `sendNewUserNotification(name, email)` | `AuthController::handleRegister()` | المدير |
| `sendArticleStatus(authorEmail, authorName, title, status)` | `AdminController` (قبول/رفض) | صاحبة المقال |

### معالجة الأخطاء
- إخفاقات الإيميل **لا توقف** عملية التسجيل أو قبول المقال
- يُسجَّل الخطأ فقط عبر `error_log()`

---

## 3. خاصية تذكرني

### جدول DB الجديد
```sql
CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### آلية العمل
1. **تسجيل الدخول مع "تذكرني":**
   - يُنشأ توكن عشوائي بـ `bin2hex(random_bytes(32))`
   - يُخزَّن `hash('sha256', $token)` في DB مع `expires_at = now() + 30 days`
   - يُرسَل التوكن الخام في Cookie: `remember_token` لمدة 30 يوم (httponly, samesite=Lax)

2. **عند كل زيارة (في bootstrap):**
   - إذا لا يوجد `$_SESSION['user_id']` لكن يوجد `$_COOKIE['remember_token']`
   - يُبحث في DB عن `hash('sha256', $cookie_value)` غير منتهي الصلاحية
   - إذا صحيح → يُبدأ session للمستخدم تلقائياً

3. **تسجيل الخروج:**
   - حذف السطر من `remember_tokens` في DB
   - مسح الـ Cookie بتاريخ ماضٍ

### المكوّنات
- `app/core/RememberMeService.php` — منطق إنشاء/التحقق/حذف التوكن
- تعديل `AuthController::handleLogin()` — حفظ التوكن إذا الـ checkbox محدد
- تعديل `AuthController::logout()` — حذف التوكن
- تعديل `app/bootstrap.php` — التحقق من الـ Cookie عند كل طلب
- تعديل `app/views/auth/login.php` — إضافة checkbox "تذكرني"

---

## 4. الملفات المتأثرة

| الملف | التغيير |
|-------|---------|
| `composer.json` | إضافة PHPMailer |
| `includes/email_config.php` | ملف جديد — إعدادات Gmail |
| `app/core/EmailService.php` | ملف جديد — كلاس الإيميل |
| `app/core/RememberMeService.php` | ملف جديد — كلاس تذكرني |
| `app/controllers/AuthController.php` | تعديل login/logout/register |
| `app/controllers/AdminController.php` | إضافة إرسال إيميل عند قبول/رفض مقال |
| `app/views/auth/login.php` | إضافة checkbox |
| `app/bootstrap.php` | إضافة auto-login من Cookie |
| `Database/schema.sql` | إضافة جدول remember_tokens |

---

## 5. كيفية التحقق (اختبار يدوي)

### اختبار الإيميل
1. سجّل حساباً جديداً → تحقق من وصول إيميل ترحيبي
2. تحقق من وصول إيميل للمدير بنفس الوقت
3. من لوحة التحكم: اقبلي مقالاً → تحقق من وصول إيميل لصاحبته
4. من لوحة التحكم: ارفضي مقالاً → تحقق من وصول إيميل لصاحبته

### اختبار تذكرني
1. سجّل دخول مع تفعيل "تذكرني" → تحقق من وجود Cookie في المتصفح
2. أغلق المتصفح وأعد فتحه → يجب أن تكوني مسجّلة دخول
3. سجّل خروج → تحقق من اختفاء Cookie وحذف التوكن من DB
