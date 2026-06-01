<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../app/bootstrap.php';
use App\Models\ArticleModel;

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'login_required']); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false]); exit;
}

$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'csrf']); exit;
}

$article_id = intval($_POST['article_id'] ?? 0);
$user_id    = (int)$_SESSION['user_id'];

if (!$article_id) {
    echo json_encode(['success' => false, 'error' => 'invalid']); exit;
}

$model = new ArticleModel();
$saved = $model->toggleSave($user_id, $article_id);
echo json_encode(['success' => true, 'saved' => $saved]);
