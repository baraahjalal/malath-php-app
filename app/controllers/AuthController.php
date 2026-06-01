<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\EmailService;
use App\Core\RememberMeService;
use App\Models\UserModel;

class AuthController extends Controller {

    public function showLogin(): void {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/malath-php-app/index');
        }
        $allowed     = ['index','community','profile','articles'];
        $raw         = $_GET['redirect'] ?? 'index';
        $redirect_to = in_array($raw, $allowed, true) ? $raw : 'index';
        $error       = '';
        $email       = '';
        $this->view('auth.login', compact('error', 'email', 'redirect_to'));
    }

    public function handleLogin(): void {
        \csrf_verify();
        $model    = new UserModel();
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $allowed  = ['index','community','profile','articles'];
        $raw      = $_POST['redirect_to'] ?? ($_GET['redirect'] ?? 'index');
        $redirect_to = in_array($raw, $allowed, true) ? $raw : 'index';

        $error = '';
        if (empty($email) || empty($password)) {
            $error = "يرجى ملء جميع الحقول.";
        } else {
            $user = $model->findByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'] ?? 'user';
                if (!empty($_POST['remember_me'])) {
                    (new RememberMeService())->create((int)$user['id']);
                }
                $dest = ($_SESSION['user_role'] === 'admin') ? 'dashboard' : $redirect_to;
                $this->redirect('/malath-php-app/' . $dest);
            } else {
                $error = "خطأ في البريد الإلكتروني أو كلمة المرور.";
            }
        }
        $this->view('auth.login', compact('error', 'email', 'redirect_to'));
    }

    public function showRegister(): void {
        $error = ''; $success = ''; $auto_redirect = false;
        $first_name = ''; $last_name = ''; $email = '';
        $this->view('auth.register', compact('error', 'success', 'auto_redirect', 'first_name', 'last_name', 'email'));
    }

    public function handleRegister(): void {
        \csrf_verify();
        $model      = new UserModel();
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name']  ?? '');
        $email      = trim($_POST['email']      ?? '');
        $password   = $_POST['password']        ?? '';
        $error      = ''; $success = ''; $auto_redirect = false;

        if (!$first_name || !$last_name || !$email || !$password) {
            $error = "جميع الحقول مطلوبة!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "صيغة البريد الإلكتروني غير صحيحة!";
        } elseif (strlen($password) < 6) {
            $error = "كلمة المرور يجب أن تكون 6 أحرف أو أكثر.";
        } elseif ($model->emailExists($email)) {
            $error = "البريد الإلكتروني مستخدم بالفعل.";
        } else {
            try {
                $fullName = $first_name . ' ' . $last_name;
                $model->create($fullName, $email, password_hash($password, PASSWORD_DEFAULT));
                EmailService::sendWelcome($fullName, $email);
                EmailService::sendNewUserNotification($fullName, $email);
                $success = "تم إنشاء الحساب بنجاح! سيتم توجيهك لصفحة الدخول...";
                $auto_redirect = true;
            } catch (\PDOException $e) {
                error_log("Register: " . $e->getMessage());
                $error = "حدث خطأ في الخادم، يرجى المحاولة لاحقاً.";
            }
        }
        $this->view('auth.register', compact('error', 'success', 'auto_redirect', 'first_name', 'last_name', 'email'));
    }

    public function logout(): void {
        (new RememberMeService())->delete();
        session_unset();
        session_destroy();
        $this->redirect('/malath-php-app/index');
    }
}
