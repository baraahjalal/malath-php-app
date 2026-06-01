# Email Notifications & Remember Me — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add PHPMailer-based email notifications (welcome, admin alert, article status) and a "تذكرني" cookie-based auto-login feature to the Malath PHP app.

**Architecture:** PHPMailer included manually (3 source files, no Composer needed). EmailService wraps all mail logic. RememberMeService handles token lifecycle for cookies. Bootstrap checks cookie on every request.

**Tech Stack:** PHP 8.2, MariaDB 10.4, PHPMailer 6.x (manual), Gmail SMTP + App Password

---

## Pre-Flight: Gmail App Password Setup

Before coding, the user must generate a Gmail App Password:
1. Go to https://myaccount.google.com/security
2. Enable **2-Step Verification** if not already enabled
3. Go to **App passwords** → Select app: "Mail" → Select device: "Other" → name it "Malath"
4. Copy the 16-character password shown (format: `xxxx xxxx xxxx xxxx`)
5. Keep it ready — needed in Task 2

---

## File Map

| Action | Path | Responsibility |
|--------|------|---------------|
| Create | `includes/phpmailer/PHPMailer.php` | PHPMailer core class |
| Create | `includes/phpmailer/SMTP.php` | PHPMailer SMTP transport |
| Create | `includes/phpmailer/Exception.php` | PHPMailer exception class |
| Create | `includes/email_config.php` | Gmail credentials (App Password) |
| Create | `app/core/EmailService.php` | sendWelcome, sendNewUserNotification, sendArticleStatus |
| Create | `app/core/RememberMeService.php` | create, validate, delete token |
| Modify | `app/controllers/AuthController.php` | send emails on register; handle cookie on login/logout |
| Modify | `app/controllers/AdminController.php` | send email on article approve/reject |
| Modify | `app/models/ArticleModel.php` | add getArticleWithAuthor() method |
| Modify | `app/views/auth/login.php` | add "تذكرني" checkbox |
| Modify | `app/bootstrap.php` | auto-login from cookie |
| Modify | `Database/Malath SQL (5).sql` | add remember_tokens table |

---

## Task 1: Download PHPMailer Source Files

**Files:**
- Create: `includes/phpmailer/Exception.php`
- Create: `includes/phpmailer/PHPMailer.php`
- Create: `includes/phpmailer/SMTP.php`

- [ ] **Step 1: Create the phpmailer directory**

```bash
mkdir -p /Applications/XAMPP/xamppfiles/htdocs/malath-php-app/includes/phpmailer
```

- [ ] **Step 2: Download the 3 required PHPMailer files**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app/includes/phpmailer

curl -sL "https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php" -o Exception.php
curl -sL "https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php" -o PHPMailer.php
curl -sL "https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php" -o SMTP.php
```

- [ ] **Step 3: Verify the files were downloaded**

```bash
ls -lh /Applications/XAMPP/xamppfiles/htdocs/malath-php-app/includes/phpmailer/
```

Expected output: 3 files, each > 10KB:
```
Exception.php   ~1KB
PHPMailer.php   ~80KB
SMTP.php        ~30KB
```

- [ ] **Step 4: Commit**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app
git add includes/phpmailer/
git commit -m "chore: add PHPMailer source files (manual install)"
```

---

## Task 2: Create Email Configuration File

**Files:**
- Create: `includes/email_config.php`

- [ ] **Step 1: Create the config file**

Create `includes/email_config.php` with this exact content (replace `YOUR_APP_PASSWORD` with the 16-char App Password from Gmail, **remove spaces**):

```php
<?php
define('MAIL_HOST',     'smtp.gmail.com');
define('MAIL_PORT',     587);
define('MAIL_USERNAME', 'baraahjalall@gmail.com');
define('MAIL_PASSWORD', 'YOUR_APP_PASSWORD');  // e.g. 'abcdabcdabcdabcd'
define('MAIL_FROM',     'baraahjalall@gmail.com');
define('MAIL_FROM_NAME','ملاذ - Malath');
define('ADMIN_EMAIL',   'baraahjalall@gmail.com');
```

- [ ] **Step 2: Add email_config.php to .gitignore to protect credentials**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app
echo "includes/email_config.php" >> .gitignore
```

- [ ] **Step 3: Commit**

```bash
git add .gitignore
git commit -m "chore: protect email credentials via gitignore"
```

---

## Task 3: Create EmailService Class

**Files:**
- Create: `app/core/EmailService.php`

- [ ] **Step 1: Create the file**

Create `app/core/EmailService.php`:

```php
<?php
namespace App\Core;

require_once ROOT_PATH . '/includes/phpmailer/Exception.php';
require_once ROOT_PATH . '/includes/phpmailer/PHPMailer.php';
require_once ROOT_PATH . '/includes/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {

    private static function buildMailer(): PHPMailer {
        require_once ROOT_PATH . '/includes/email_config.php';

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        return $mail;
    }

    public static function sendWelcome(string $name, string $email): void {
        try {
            $mail = self::buildMailer();
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = 'مرحباً بك في ملاذ!';
            $mail->Body    = "
                <div dir='rtl' style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
                    <h2 style='color:#c53a62;'>مرحباً {$name}! 🌸</h2>
                    <p>يسعدنا انضمامك إلى <strong>ملاذ</strong> — مجتمعنا الرقمي الآمن للمرأة.</p>
                    <p>يمكنك الآن:</p>
                    <ul>
                        <li>التفاعل مع مجتمعاتنا المتنوعة</li>
                        <li>نشر مقالاتك ومشاركة أفكارك</li>
                        <li>التواصل مع أخوات يشاركنك الاهتمامات</li>
                    </ul>
                    <p style='margin-top:2rem;color:#888;font-size:0.85rem;'>مع تحيات فريق ملاذ ❤️</p>
                </div>
            ";
            $mail->send();
        } catch (Exception $e) {
            error_log('EmailService::sendWelcome failed: ' . $e->getMessage());
        }
    }

    public static function sendNewUserNotification(string $name, string $email): void {
        try {
            require_once ROOT_PATH . '/includes/email_config.php';
            $mail = self::buildMailer();
            $mail->addAddress(ADMIN_EMAIL, 'المدير');
            $mail->isHTML(true);
            $mail->Subject = 'مستخدمة جديدة انضمت إلى ملاذ';
            $mail->Body    = "
                <div dir='rtl' style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
                    <h2 style='color:#c53a62;'>انضمام مستخدمة جديدة 📋</h2>
                    <p><strong>الاسم:</strong> {$name}</p>
                    <p><strong>البريد:</strong> {$email}</p>
                    <p><strong>التاريخ:</strong> " . date('Y-m-d H:i') . "</p>
                </div>
            ";
            $mail->send();
        } catch (Exception $e) {
            error_log('EmailService::sendNewUserNotification failed: ' . $e->getMessage());
        }
    }

    public static function sendArticleStatus(
        string $authorEmail,
        string $authorName,
        string $articleTitle,
        string $status
    ): void {
        try {
            $mail = self::buildMailer();
            $mail->addAddress($authorEmail, $authorName);
            $mail->isHTML(true);

            if ($status === 'approved') {
                $mail->Subject = 'تم قبول مقالتك في ملاذ ✅';
                $statusText    = 'تم <strong style="color:green;">قبول</strong>';
                $emoji         = '🎉';
                $extra         = '<p>مقالتك متاحة الآن للقراء في قسم المقالات.</p>';
            } else {
                $mail->Subject = 'تحديث حول مقالتك في ملاذ';
                $statusText    = 'تم <strong style="color:#c53a62;">رفض</strong>';
                $emoji         = '📝';
                $extra         = '<p>يمكنك تعديل مقالتك وإعادة إرسالها للمراجعة.</p>';
            }

            $mail->Body = "
                <div dir='rtl' style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
                    <h2 style='color:#c53a62;'>{$emoji} تحديث حالة المقالة</h2>
                    <p>مرحباً {$authorName}،</p>
                    <p>{$statusText} مقالتك: <strong>" . htmlspecialchars($articleTitle) . "</strong></p>
                    {$extra}
                    <p style='margin-top:2rem;color:#888;font-size:0.85rem;'>فريق ملاذ ❤️</p>
                </div>
            ";
            $mail->send();
        } catch (Exception $e) {
            error_log('EmailService::sendArticleStatus failed: ' . $e->getMessage());
        }
    }
}
```

- [ ] **Step 2: Commit**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app
git add app/core/EmailService.php
git commit -m "feat: add EmailService with welcome, admin alert, and article status emails"
```

---

## Task 4: Add getArticleWithAuthor() to ArticleModel

**Files:**
- Modify: `app/models/ArticleModel.php`

The `AdminController` needs the author's email and article title to send the status email. Add this method to `ArticleModel`.

- [ ] **Step 1: Add the method to ArticleModel**

In `app/models/ArticleModel.php`, add this method **after** the `reject()` method (after line 103):

```php
    public function getArticleWithAuthor(int $id): ?array {
        $row = $this->query("
            SELECT a.id, a.title, u.name AS author_name, u.email AS author_email
            FROM articles a
            JOIN users u ON a.user_id = u.id
            WHERE a.id = ?
        ", [$id])->fetch();
        return $row ?: null;
    }
```

- [ ] **Step 2: Commit**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app
git add app/models/ArticleModel.php
git commit -m "feat: add getArticleWithAuthor() to ArticleModel for email notifications"
```

---

## Task 5: Wire Email into AuthController (Registration)

**Files:**
- Modify: `app/controllers/AuthController.php`

- [ ] **Step 1: Add use statement for EmailService**

In `app/controllers/AuthController.php`, add after line 5 (`use App\Models\UserModel;`):

```php
use App\Core\EmailService;
```

- [ ] **Step 2: Trigger emails after successful registration**

In `handleRegister()`, find this block (around line 74):
```php
                $model->create($first_name . ' ' . $last_name, $email, password_hash($password, PASSWORD_DEFAULT));
                $success = "تم إنشاء الحساب بنجاح! سيتم توجيهك لصفحة الدخول...";
                $auto_redirect = true;
```

Replace it with:
```php
                $fullName = $first_name . ' ' . $last_name;
                $model->create($fullName, $email, password_hash($password, PASSWORD_DEFAULT));
                EmailService::sendWelcome($fullName, $email);
                EmailService::sendNewUserNotification($fullName, $email);
                $success = "تم إنشاء الحساب بنجاح! سيتم توجيهك لصفحة الدخول...";
                $auto_redirect = true;
```

- [ ] **Step 3: Commit**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app
git add app/controllers/AuthController.php
git commit -m "feat: send welcome and admin notification emails on registration"
```

---

## Task 6: Wire Email into AdminController (Article Approve/Reject)

**Files:**
- Modify: `app/controllers/AdminController.php`

- [ ] **Step 1: Add use statement for EmailService**

In `app/controllers/AdminController.php`, add after line 7 (`use App\Models\ArticleModel;`):

```php
use App\Core\EmailService;
```

- [ ] **Step 2: Send email on approve**

Find this block (around line 69):
```php
        if (isset($_POST['approve_article'])) {
            (new ArticleModel())->approve((int)$_POST['article_id']);
            $this->redirect('/malath-php-app/dashboard.php?tab=articles&msg=article_approved');
        }
```

Replace with:
```php
        if (isset($_POST['approve_article'])) {
            $articleModel = new ArticleModel();
            $articleId    = (int)$_POST['article_id'];
            $article      = $articleModel->getArticleWithAuthor($articleId);
            $articleModel->approve($articleId);
            if ($article) {
                EmailService::sendArticleStatus(
                    $article['author_email'],
                    $article['author_name'],
                    $article['title'],
                    'approved'
                );
            }
            $this->redirect('/malath-php-app/dashboard.php?tab=articles&msg=article_approved');
        }
```

- [ ] **Step 3: Send email on reject**

Find this block (around line 74):
```php
        if (isset($_POST['reject_article'])) {
            (new ArticleModel())->reject((int)$_POST['article_id']);
            $this->redirect('/malath-php-app/dashboard.php?tab=articles&msg=article_rejected');
        }
```

Replace with:
```php
        if (isset($_POST['reject_article'])) {
            $articleModel = new ArticleModel();
            $articleId    = (int)$_POST['article_id'];
            $article      = $articleModel->getArticleWithAuthor($articleId);
            $articleModel->reject($articleId);
            if ($article) {
                EmailService::sendArticleStatus(
                    $article['author_email'],
                    $article['author_name'],
                    $article['title'],
                    'rejected'
                );
            }
            $this->redirect('/malath-php-app/dashboard.php?tab=articles&msg=article_rejected');
        }
```

- [ ] **Step 4: Commit**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app
git add app/controllers/AdminController.php
git commit -m "feat: send article status email to author on approve/reject"
```

---

## Task 7: Create remember_tokens Table in Database

**Files:**
- Modify: `Database/Malath SQL (5).sql`

- [ ] **Step 1: Run SQL in phpMyAdmin**

Open phpMyAdmin at `http://localhost/phpmyadmin`, select the `malath` database, go to SQL tab, and run:

```sql
CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `token_hash` (`token_hash`),
  CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

- [ ] **Step 2: Add the table definition to the schema SQL file**

In `Database/Malath SQL (5).sql`, add the table definition in the tables section (before the Indexes section). Find `-- Table structure for table \`users\`` and add this block just before it:

```sql
-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `token_hash` (`token_hash`),
  CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
```

- [ ] **Step 3: Commit**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app
git add "Database/Malath SQL (5).sql"
git commit -m "feat: add remember_tokens table to schema"
```

---

## Task 8: Create RememberMeService Class

**Files:**
- Create: `app/core/RememberMeService.php`

- [ ] **Step 1: Create the file**

Create `app/core/RememberMeService.php`:

```php
<?php
namespace App\Core;

class RememberMeService {

    private const COOKIE_NAME   = 'remember_token';
    private const COOKIE_DAYS   = 30;
    private \PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getPdo();
    }

    public function create(int $userId): void {
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . self::COOKIE_DAYS . ' days'));

        $this->db->prepare(
            "INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)"
        )->execute([$userId, $tokenHash, $expiresAt]);

        setcookie(
            self::COOKIE_NAME,
            $token,
            [
                'expires'  => time() + (self::COOKIE_DAYS * 24 * 60 * 60),
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    public function validate(): ?int {
        $token = $_COOKIE[self::COOKIE_NAME] ?? null;
        if (!$token) {
            return null;
        }

        $tokenHash = hash('sha256', $token);
        $st = $this->db->prepare(
            "SELECT user_id FROM remember_tokens WHERE token_hash = ? AND expires_at > NOW()"
        );
        $st->execute([$tokenHash]);
        $row = $st->fetch();

        return $row ? (int)$row['user_id'] : null;
    }

    public function delete(): void {
        $token = $_COOKIE[self::COOKIE_NAME] ?? null;
        if ($token) {
            $tokenHash = hash('sha256', $token);
            $this->db->prepare(
                "DELETE FROM remember_tokens WHERE token_hash = ?"
            )->execute([$tokenHash]);
        }

        setcookie(self::COOKIE_NAME, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
```

- [ ] **Step 2: Commit**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app
git add app/core/RememberMeService.php
git commit -m "feat: add RememberMeService for cookie-based auto-login"
```

---

## Task 9: Add "تذكرني" Checkbox to Login View

**Files:**
- Modify: `app/views/auth/login.php`

- [ ] **Step 1: Add checkbox between password field and submit button**

In `app/views/auth/login.php`, find this line (around line 214):
```php
                    <button type="submit" class="btn-primary" style="width: 100%;">تسجيل الدخول</button>
```

Add this block **before** it:

```php
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1.25rem;">
                        <input type="checkbox"
                               name="remember_me"
                               id="remember_me"
                               value="1"
                               class="checkbox-custom">
                        <label for="remember_me" style="cursor:pointer; font-size:0.9rem; color:var(--secondary); user-select:none;">
                            تذكريني لمدة 30 يوماً
                        </label>
                    </div>
```

- [ ] **Step 2: Commit**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app
git add app/views/auth/login.php
git commit -m "feat: add remember me checkbox to login form"
```

---

## Task 10: Wire Remember Me into AuthController (Login & Logout)

**Files:**
- Modify: `app/controllers/AuthController.php`

- [ ] **Step 1: Add use statement for RememberMeService**

In `app/controllers/AuthController.php`, add after `use App\Core\EmailService;`:

```php
use App\Core\RememberMeService;
```

- [ ] **Step 2: Set cookie on successful login**

In `handleLogin()`, find this block (around line 35):
```php
                $_SESSION['user_role'] = $user['role'] ?? 'user';
                $dest = ($_SESSION['user_role'] === 'admin') ? 'dashboard' : $redirect_to;
                $this->redirect('/malath-php-app/' . $dest);
```

Replace with:
```php
                $_SESSION['user_role'] = $user['role'] ?? 'user';
                if (!empty($_POST['remember_me'])) {
                    (new RememberMeService())->create((int)$user['id']);
                }
                $dest = ($_SESSION['user_role'] === 'admin') ? 'dashboard' : $redirect_to;
                $this->redirect('/malath-php-app/' . $dest);
```

- [ ] **Step 3: Clear cookie on logout**

In `logout()`, find:
```php
    public function logout(): void {
        session_unset();
        session_destroy();
        $this->redirect('/malath-php-app/index');
    }
```

Replace with:
```php
    public function logout(): void {
        (new RememberMeService())->delete();
        session_unset();
        session_destroy();
        $this->redirect('/malath-php-app/index');
    }
```

- [ ] **Step 4: Commit**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app
git add app/controllers/AuthController.php
git commit -m "feat: set/clear remember_token cookie on login and logout"
```

---

## Task 11: Auto-Login from Cookie in Bootstrap

**Files:**
- Modify: `app/bootstrap.php`

- [ ] **Step 1: Add auto-login logic**

In `app/bootstrap.php`, the current content is:
```php
<?php
define('ROOT_PATH', dirname(__DIR__));

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/autoload.php';
require_once ROOT_PATH . '/includes/csrf.php';

csrf_generate();
```

Replace the full file content with:
```php
<?php
define('ROOT_PATH', dirname(__DIR__));

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/autoload.php';
require_once ROOT_PATH . '/includes/csrf.php';

csrf_generate();

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $rememberMe = new \App\Core\RememberMeService();
    $userId     = $rememberMe->validate();
    if ($userId) {
        $userModel = new \App\Models\UserModel();
        $user      = $userModel->findById($userId);
        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'] ?? 'user';
        } else {
            $rememberMe->delete();
        }
    } else {
        $rememberMe->delete();
    }
}
```

- [ ] **Step 2: Add findById() to UserModel**

The bootstrap calls `$userModel->findById($userId)`. Add this method to `app/models/UserModel.php` after the `findByEmail()` method:

```php
    public function findById(int $id): ?array {
        $st = $this->query("SELECT * FROM users WHERE id = ?", [$id]);
        return $st->fetch() ?: null;
    }
```

- [ ] **Step 3: Commit**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/malath-php-app
git add app/bootstrap.php app/models/UserModel.php
git commit -m "feat: auto-login from remember_token cookie in bootstrap"
```

---

## Verification Checklist

### Email Tests
1. **Welcome + Admin alert**: Register a new account → check inbox of the new email AND `baraahjalall@gmail.com` for admin notification
2. **Article approved**: From admin dashboard (tab=articles), approve a pending article → author gets approval email
3. **Article rejected**: From admin dashboard, reject a pending article → author gets rejection email
4. **Error resilience**: Temporarily put a wrong password in `email_config.php` → registration should still succeed (email failure is silent)

### Remember Me Tests
1. **Cookie set**: Login with "تذكريني" checked → open DevTools → Application → Cookies → find `remember_token` cookie (30-day expiry)
2. **Auto-login**: After step 1, close browser completely, reopen, go to `/malath-php-app/index` → should be logged in without entering password
3. **Cookie cleared on logout**: Click logout → check DevTools → `remember_token` cookie should be gone
4. **DB token deleted**: After logout, check `remember_tokens` table in phpMyAdmin → row should be deleted
5. **No cookie = no auto-login**: Login WITHOUT checking "تذكريني" → close and reopen browser → should not be logged in

---
