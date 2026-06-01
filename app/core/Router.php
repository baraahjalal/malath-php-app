<?php
namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, callable $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri    = '/' . trim(str_replace('/malath-php-app', '', $uri), '/');

        // تنظيف الروابط القديمة من امتداد .php لتعمل بسلاسة مع الـ MVC
        $uri = preg_replace('/\.php$/', '', $uri);
        if ($uri === '/index') $uri = '/'; // توحيد مسار الرئيسية

        if (isset($this->routes[$method][$uri])) {
            call_user_func($this->routes[$method][$uri]);
            return;
        }

        http_response_code(404);
        echo '<h1>404 — الصفحة غير موجودة</h1>';
    }
}
