# PROGRESS.md — مشروع ملاذ
> آخر تحديث: 2026-05-31

---

## القسم 1: الهيكل الحالي (Folder Structure)

```
malath-php-app/
├── index.php               ← الصفحة الرئيسية
├── login.php               ← تسجيل الدخول
├── register.php            ← إنشاء حساب
├── logout.php              ← تسجيل الخروج
├── community.php           ← صفحة المجتمعات والمنشورات
├── profile.php             ← الملف الشخصي (3 تبويبات)
├── update_profile.php      ← معالج تحديث بيانات المستخدم
├── dashboard.php           ← لوحة تحكم المشرف (UI فقط)
├── article.php             ← صفحة المقال (محتوى ثابت)
├── about.php               ← من نحن
├── contact.php             ← تواصل معنا
├── faq.php                 ← الأسئلة الشائعة
├── privacy.php             ← سياسة الخصوصية
├── terms.php               ← شروط الاستخدام
├── includes/
│   ├── db.php              ← اتصال PDO بقاعدة البيانات
│   ├── header.php          ← الهيدر + بدء الجلسة + nav
│   ├── footer.php          ← الفوتر + تضمين app.js
│   └── admin_sidebar.php   ← sidebar المشرف (غير مستخدم)
├── assets/
│   ├── css/
│   │   └── style.css       ← الأنماط الرئيسية
│   ├── js/
│   │   └── app.js          ← JavaScript (الإشعارات، التبويبات)
│   ├── images/
│   │   └── womanPic.jpg    ← صورة الـ hero
│   └── uploads/
│       └── avatars/        ← صور المستخدمات المرفوعة
└── README.md
```

---

## القسم 2: ما يعمل حالياً ✅

| الصفحة / الميزة | الحالة |
|---|---|
| الصفحة الرئيسية (index.php) | تعمل — محتوى ثابت جميل |
| تسجيل الدخول (login.php) | يعمل — PDO + password_verify + session |
| إنشاء حساب (register.php) | يعمل — PDO + password_hash + تحقق من التكرار |
| تسجيل الخروج (logout.php) | يعمل — session_destroy |
| عرض المجتمعات (community.php) | يعمل — جلب من قاعدة البيانات |
| نشر منشور | يعمل — INSERT مع prepared statements |
| الانضمام / الانسحاب من مجتمع | يعمل |
| الإعجاب بمنشور (toggle) | يعمل |
| حفظ منشور (toggle) | يعمل |
| التعليق على منشور | يعمل |
| عرض التعليقات (toggle JS) | يعمل |
| صفحة الملف الشخصي (profile.php) | تعمل — 3 تبويبات |
| تعديل بيانات الحساب | يعمل (اسم + bio) |
| رفع صورة الملف الشخصي | يعمل |
| حذف منشور من البروفايل | يعمل |
| تعديل منشور من البروفايل | يعمل |
| عرض النشاطات (مشاركات + تعليقات + إعجابات) | يعمل |
| المكتبة الخاصة (المحفوظات) | تعمل — من post_saves |
| صفحات ثابتة (about, faq, privacy, terms, contact) | تعمل — محتوى ثابت |
| عرض avatar في الهيدر | يعمل |

---

## القسم 3: ما هو ناقص أو مكسور ❌

### ثغرات أمنية (Security Vulnerabilities):

1. **❌ لا يوجد CSRF Token في أي form** — كل النماذج عرضة لهجمات Cross-Site Request Forgery:
   - login.php, register.php, community.php (نشر، إعجاب، تعليق، انضمام)
   - profile.php (حذف منشور، تعديله)، update_profile.php

2. **❌ Open Redirect في login.php** — السطر 41:
   ```php
   header("Location: " . $redirect_to); // $redirect_to = $_GET['redirect'] بدون تحقق
   ```
   المهاجم يمكنه توجيه المستخدم لأي موقع خارجي.

3. **❌ PDOException يكشف معلومات قاعدة البيانات** — في register.php السطر 49:
   ```php
   $error = "خطأ في قاعدة البيانات: " . $e->getMessage(); // يعرض تفاصيل الخطأ للمستخدم
   ```

4. **❌ ini_set('display_errors', 1) في update_profile.php** — يكشف أخطاء PHP في بيئة الإنتاج.

5. **❌ التحقق من نوع الملف بالامتداد فقط** في update_profile.php — ضعيف، يجب التحقق من MIME type الحقيقي.

6. **❌ لا تحقق من حجم الملف** عند رفع الصورة.

### مشاكل معمارية (Architecture):

7. **❌ لا يوجد MVC** — كل المنطق والعرض وقاعدة البيانات مختلطة في نفس الملفات.

8. **❌ CSS مدمج داخل صفحات PHP** — كل صفحة تحتوي على `<style>` block ضخم.

### ميزات ناقصة:

9. **❌ لوحة تحكم المشرف (dashboard.php)** — UI فقط، بيانات مزيفة hardcoded، لا تحقق من صلاحية المشرف، جميع الروابط في الـ sidebar تشير إلى `#`.

10. **❌ admin_sidebar.php** — موجود لكن غير مستخدم في dashboard.php (الـ dashboard لديه sidebar مضمن خاص به).

11. **❌ صفحة المقالات (article.php)** — محتوى ثابت بالكامل، لا يسحب من قاعدة البيانات.

12. **❌ نموذج التواصل (contact.php)** — الزر من نوع `type="button"` والـ form بدون action، لا يرسل شيئاً.

13. **❌ نظام الإشعارات** — بيانات مزيفة hardcoded في header.php، لا يقرأ من قاعدة البيانات.

14. **❌ "نسيتِ كلمة المرور؟"** — يشير إلى `#`، غير مُنفَّذ.

15. **❌ النشرة البريدية** — form في article.php يشير إلى `#`، غير مُنفَّذ.

### أخطاء منطقية في قاعدة البيانات:

16. **❌ جدول bookmarks غير موجود / مختلف** — profile.php يستعلم `bookmarks` في السطر 52، لكن المنشورات تُحفظ في `post_saves`. عداد المحفوظات خاطئ.

17. **❌ جدول articles** — profile.php يستعلم جدول `articles` منفصل في السطر 45، لكن المقالات تُخزَّن في جدول `posts` مع type='article'.

18. **❌ عدم تزامن JavaScript** — app.js يستخدم `data-tab` attribute لكن profile.php يستخدم `onclick="switchTab()"` — المنطقان لا يتطابقان.

---

## القسم 4: هل يوجد MVC؟

**لا يوجد MVC.** الهيكل المعماري الحالي هو **Flat PHP**:

- كل صفحة `.php` تحتوي على **المنطق + الاستعلامات + HTML** معاً في نفس الملف.
- الكود المشترك الوحيد موجود في `includes/` (db.php، header.php، footer.php).
- لا يوجد أي فصل بين Model / View / Controller.
- لا يوجد Router، لا توجد Classes/Models.
- الاستعلامات مكتوبة مباشرةً في ملفات الصفحات.

**الهيكل الموصى به (للمرحلة القادمة):**
```
app/
├── controllers/   ← منطق التطبيق (AuthController, PostController...)
├── models/        ← استعلامات DB فقط (User.php, Post.php...)
├── views/         ← HTML فقط
├── core/          ← Router, Database, Session classes
└── config/        ← الإعدادات
```

---

## TASK LIST

---

- [x] TASK-01: إضافة CSRF Token لجميع النماذج | Priority: HIGH | Est: 2-3 ساعات
  - وصف: إنشاء دالة مشتركة لتوليد والتحقق من CSRF tokens، وإضافتها لكل form في الموقع
  - الملفات المتأثرة: `includes/header.php` (توليد token)، `login.php`، `register.php`، `community.php`، `profile.php`، `update_profile.php`، `contact.php`
  - يعتمد على: لا يوجد
  ✅ منجزة | التاريخ: 2026-05-31
  - تم إنشاء `includes/csrf.php` مع `csrf_generate()`, `csrf_field()`, `csrf_verify()`
  - تمت إضافة CSRF لـ login, register, community (كل forms), profile, update_profile
  - header.php يولّد token تلقائياً في كل صفحة

- [x] TASK-02: إصلاح ثغرة Open Redirect في login.php | Priority: HIGH | Est: 30 دقيقة
  - وصف: التحقق من أن `$redirect_to` يشير لصفحة داخلية فقط (whitelist)
  - الملفات المتأثرة: `login.php`
  - يعتمد على: لا يوجد
  ✅ منجزة | التاريخ: 2026-05-31
  - تمت إضافة `$allowed_redirects` whitelist
  - أي redirect خارج القائمة يُعيَّن تلقائياً لـ index.php

- [x] TASK-03: إصلاح كشف معلومات DB وإعدادات الأخطاء | Priority: HIGH | Est: 30 دقيقة
  - الملفات المتأثرة: `register.php`، `update_profile.php`
  ✅ منجزة | التاريخ: 2026-05-31
  - حُذف `display_errors=1` من update_profile.php
  - رسائل PDOException تُسجَّل بـ `error_log()` بدلاً من عرضها للمستخدم
  - تمت إضافة `trim()` لمدخلات الـ POST في update_profile.php

- [ ] TASK-04: تعزيز تحقق رفع الصورة | Priority: HIGH | Est: 1 ساعة
  - مؤجلة

- [x] TASK-05: إصلاح أخطاء قاعدة البيانات في profile.php | Priority: HIGH | Est: 1 ساعة
  - الملفات المتأثرة: `profile.php`
  ✅ منجزة | التاريخ: 2026-05-31
  - عداد المحفوظات: `bookmarks` → `post_saves`
  - عداد المقالات: `articles` → `posts WHERE type='article'`

- [x] TASK-05b: إضافة session_regenerate_id في login | Priority: HIGH
  ✅ منجزة | التاريخ: 2026-05-31
  - تمت إضافة `session_regenerate_id(true)` بعد تسجيل الدخول الناجح
  - حفظ `user_role` في الجلسة للتحقق من الصلاحيات

- [x] TASK-06: تحويل المشروع لهيكل MVC | Priority: HIGH | Est: 6-8 ساعات
  - الملفات المتأثرة: مجلد `app/` الجديد بالكامل
  ✅ منجزة | التاريخ: 2026-05-31
  - تم إنشاء `app/core/` — Database (Singleton), Router, Controller, Model
  - تم إنشاء `app/models/` — UserModel, PostModel, CommunityModel
  - تم إنشاء `app/controllers/` — AuthController, CommunityController, ArticleController
  - تم إنشاء `app/views/` — articles/index.php, articles/single.php, layouts/
  - تم إنشاء `app/autoload.php` و `app/bootstrap.php`
  - الصفحات القديمة (flat PHP) لا تزال تعمل كـ entry points للتوافق

- [x] TASK-07: بناء لوحة تحكم المشرف الحقيقية | Priority: HIGH | Est: 5-7 ساعات
  - الملفات المتأثرة: `dashboard.php`، `includes/admin_auth.php` (جديد)، `admin_setup.sql`
  ✅ منجزة | التاريخ: 2026-05-31
  - لوحة التحكم محمية بـ `includes/admin_auth.php` (يتحقق من role=admin)
  - إحصائيات حقيقية من DB: عدد المستخدمات، المنشورات، التعليقات، الإعجابات
  - صفحة إدارة المستخدمين: عرض + بحث + تغيير role + حذف
  - صفحة إدارة المنشورات: عرض كامل + حذف
  - تم إنشاء `admin_setup.sql` لإضافة عمود role وجدول notifications
  - المشرف يُوجَّه تلقائياً لـ dashboard.php بعد تسجيل الدخول

- [x] TASK-08: بناء صفحة المقالات الديناميكية | Priority: MEDIUM | Est: 3-4 ساعات
  - الملفات المتأثرة: `articles.php` (جديد)، `articles-single.php` (جديد)، `app/views/articles/`
  ✅ منجزة | التاريخ: 2026-05-31
  - `articles.php` — قائمة مقالات ديناميكية من `posts WHERE type='article'` مع pagination
  - `articles-single.php?id=X` — صفحة مقال واحد مع تعليقاته
  - يستخدم `ArticleController` و `PostModel` من MVC
  - رابط المقالات في الناف بار يشير للصفحة الجديدة

- [ ] TASK-09: تنفيذ نظام الإشعارات الحقيقي | Priority: MEDIUM | Est: 3-4 ساعات
  - الملفات المتأثرة: `includes/header.php`، `api/notifications.php`، `includes/notify.php`
  ✅ منجزة | التاريخ: 2026-05-31
  - تم إنشاء `includes/notify.php` — دالة `notify()` لتوليد الإشعارات
  - تم إنشاء `api/notifications.php` — يجلب الإشعارات ويدعم mark_as_read
  - header.php يجلب الإشعارات بـ AJAX عند فتح القائمة + كل 60 ثانية
  - الإشعارات تُولَّد تلقائياً عند الإعجاب والتعليق من api/toggle_like.php وapi/submit_comment.php
  - ملاحظة: يتطلب تنفيذ SQL من admin_setup.sql أولاً

- [ ] TASK-10: تنفيذ نموذج التواصل | Priority: MEDIUM — مؤجلة

- [x] TASK-11: تنفيذ AJAX للإعجاب والتعليق والحفظ | Priority: MEDIUM | Est: 3-4 ساعات
  - الملفات المتأثرة: `community.php`، `api/toggle_like.php`، `api/toggle_save.php`، `api/submit_comment.php`
  ✅ منجزة | التاريخ: 2026-05-31
  - تم إنشاء `api/` مع 3 endpoints مؤمنة بـ CSRF
  - community.php: نماذج form استُبدلت بأزرار AJAX (fetch API)
  - الإعجاب/الحفظ: يتغير الـ icon والعداد فورياً بدون reload
  - التعليق: يُضاف للـ DOM فوراً بدون reload

- [ ] TASK-12: إصلاح JS التبويبات — مؤجلة
- [ ] TASK-13: نسيت كلمة المرور — مؤجلة
- [ ] TASK-14: نقل CSS للملف المشترك — مؤجلة
- [ ] TASK-15: توحيد admin_sidebar — مؤجلة

---

## ⚠️ خطوة مطلوبة قبل الاستخدام

تشغيل `admin_setup.sql` في phpMyAdmin (مرة واحدة فقط):

```sql
ALTER TABLE users ADD COLUMN IF NOT EXISTS `role` ENUM('user','admin') NOT NULL DEFAULT 'user';

CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `actor_id` INT UNSIGNED NOT NULL,
    `type` ENUM('like','comment','join') NOT NULL,
    `post_id` INT UNSIGNED DEFAULT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- لتحويل مستخدم لمشرف:
-- UPDATE users SET role = 'admin' WHERE id = 1;
```

---

## الملخص النهائي — ما تم إنجازه في جلسة العمل

| المهمة | الحالة | الوصف |
|---|---|---|
| TASK-05 | ✅ | إصلاح استعلامات bookmarks وarticles في profile.php |
| TASK-02 | ✅ | حماية redirect بـ whitelist في login.php |
| TASK-03 | ✅ | إزالة display_errors وإخفاء PDO errors |
| TASK-01 | ✅ | CSRF token لجميع النماذج (includes/csrf.php) |
| TASK-07 | ✅ | لوحة تحكم حقيقية بإحصائيات DB + إدارة users/posts |
| TASK-11 | ✅ | AJAX كامل للإعجاب/التعليق/الحفظ بدون reload |
| TASK-09 | ✅ | نظام إشعارات حقيقي (DB + AJAX polling) |
| TASK-06 | ✅ | هيكل MVC كامل: core, models, controllers, views, autoloader |
| TASK-08 | ✅ | صفحتا articles.php وarticles-single.php ديناميكيتان |

### الملفات الجديدة المُضافة
- `includes/csrf.php` — CSRF helper
- `includes/admin_auth.php` — admin middleware
- `includes/notify.php` — notification generator
- `api/toggle_like.php` — AJAX like endpoint
- `api/toggle_save.php` — AJAX save endpoint
- `api/submit_comment.php` — AJAX comment endpoint
- `api/notifications.php` — notifications API
- `admin_setup.sql` — SQL setup script
- `articles.php` — articles list page (MVC entry)
- `articles-single.php` — single article page (MVC entry)
- `app/core/Database.php` — Singleton PDO
- `app/core/Router.php` — URL router
- `app/core/Controller.php` — base controller
- `app/core/Model.php` — base model
- `app/models/UserModel.php`
- `app/models/PostModel.php`
- `app/models/CommunityModel.php`
- `app/controllers/AuthController.php`
- `app/controllers/CommunityController.php`
- `app/controllers/ArticleController.php`
- `app/views/articles/index.php`
- `app/views/articles/single.php`
- `app/autoload.php`
- `app/bootstrap.php`

- [ ] TASK-12: إصلاح JavaScript التبويبات في profile.php | Priority: MEDIUM | Est: 30 دقيقة
  - وصف: توحيد آلية التبويبات بين app.js (data-tab) وprofile.php (onclick) — إزالة inline onclick وإضافة data-tab attributes
  - الملفات المتأثرة: `profile.php`، `assets/js/app.js`
  - يعتمد على: لا يوجد

- [ ] TASK-13: تنفيذ "نسيتِ كلمة المرور" | Priority: LOW | Est: 2-3 ساعات
  - وصف: إنشاء reset_password.php، جدول password_resets، إرسال رابط إعادة تعيين بالبريد
  - الملفات المتأثرة: `login.php`، ملفات جديدة، قاعدة البيانات
  - يعتمد على: لا يوجد

- [ ] TASK-14: نقل CSS من داخل الصفحات لملف style.css | Priority: LOW | Est: 2 ساعات
  - وصف: كل صفحة تحتوي على `<style>` block ضخم — نقلها لـ style.css مع تسمية classes منظمة
  - الملفات المتأثرة: `index.php`، `login.php`، `register.php`، `community.php`، `profile.php`، `article.php`، `assets/css/style.css`
  - يعتمد على: لا يوجد

- [ ] TASK-15: توحيد admin_sidebar.php مع dashboard.php | Priority: LOW | Est: 1 ساعة
  - وصف: استخدام `includes/admin_sidebar.php` بدلاً من الـ sidebar المضمن في dashboard.php، وتوحيد الكود
  - الملفات المتأثرة: `dashboard.php`، `includes/admin_sidebar.php`
  - يعتمد على: TASK-07

---

## ملخص الأولويات

| الأولوية | المهام |
|---|---|
| 🔴 HIGH (أمان + أخطاء منطقية) | TASK-01 إلى TASK-07 |
| 🟡 MEDIUM (ميزات مفقودة + AJAX) | TASK-08 إلى TASK-12 |
| 🟢 LOW (تحسينات + تنظيف) | TASK-13 إلى TASK-15 |
