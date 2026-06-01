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
