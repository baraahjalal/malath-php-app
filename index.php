<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Router;
use App\Controllers\PageController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\CommunityController;
use App\Controllers\AdminController;
use App\Controllers\ArticleController;

$router = new Router();

// مسارات الصفحات الثابتة (Pages)
$router->get('', [new PageController(), 'index']);
$router->get('/', [new PageController(), 'index']);
$router->get('/index', [new PageController(), 'index']);
$router->get('/about', [new PageController(), 'about']);
$router->get('/contact', [new PageController(), 'contact']);
$router->get('/faq', [new PageController(), 'faq']);
$router->get('/privacy', [new PageController(), 'privacy']);
$router->get('/terms', [new PageController(), 'terms']);

// مسارات المصادقة (Auth)
$router->get('/login', [new AuthController(), 'showLogin']);
$router->post('/login', [new AuthController(), 'handleLogin']);
$router->get('/register', [new AuthController(), 'showRegister']);
$router->post('/register', [new AuthController(), 'handleRegister']);
$router->get('/logout', [new AuthController(), 'logout']);

// مسارات المجتمع (Community)
$router->get('/community', [new CommunityController(), 'index']);
$router->post('/community', [new CommunityController(), 'handlePost']);

// مسارات الملف الشخصي (Profile)
$router->get('/profile', [new ProfileController(), 'index']);
$router->post('/profile', [new ProfileController(), 'handlePostAction']);
$router->post('/update_profile', [new ProfileController(), 'handleUpdate']);

// مسارات المقالات (Articles)
$router->get('/articles', [new ArticleController(), 'index']);
$router->get('/article-create', [new ArticleController(), 'create']);
$router->post('/article-create', [new ArticleController(), 'handleCreate']);
$router->get('/articles-single', [new ArticleController(), 'show']);

// مسارات الإدارة (Admin Dashboard)
$router->get('/dashboard', [new AdminController(), 'index']);
$router->post('/dashboard', [new AdminController(), 'handlePost']);

// تشغيل الموجه (Dispatch)
$router->dispatch();
