<?php
namespace App\Core;

abstract class Controller {
    protected \PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getPdo();
    }

    protected function view(string $view, array $data = []): void {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: $view");
        }
        require $viewFile;
    }

    protected function redirect(string $url): void {
        header("Location: $url");
        exit;
    }

    protected function json(array $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/malath-php-app/login');
        }
    }

    protected function requireAdmin(): void {
        $this->requireAuth();
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            include __DIR__ . '/../../includes/header.php';
            echo '<div style="text-align:center;padding:5rem;"><h2>403 — غير مصرح لك بالوصول</h2><a href="/malath-php-app/index" class="btn-login-outline" style="margin-top:1rem;">العودة للرئيسية</a></div>';
            include __DIR__ . '/../../includes/footer.php';
            exit;
        }
    }
}
