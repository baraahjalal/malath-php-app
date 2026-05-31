<?php
spl_autoload_register(function (string $class): void {
    $base = __DIR__ . '/';
    $map  = [
        'App\\Core\\'        => $base . 'core/',
        'App\\Models\\'      => $base . 'models/',
        'App\\Controllers\\' => $base . 'controllers/',
    ];
    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (file_exists($file)) require_once $file;
            return;
        }
    }
});
