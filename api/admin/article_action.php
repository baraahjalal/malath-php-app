<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Core\Database;
use App\Core\EmailService;
use App\Models\ArticleModel;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'forbidden']); exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'csrf']); exit;
}

$id     = intval($_POST['article_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$id || !in_array($action, ['approve', 'reject', 'delete'], true)) {
    echo json_encode(['success' => false, 'error' => 'invalid_params']); exit;
}

$pdo          = Database::getInstance()->getPdo();
$articleModel = new ArticleModel();

if ($action === 'delete') {
    $pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([$id]);
    echo json_encode(['success' => true, 'action' => 'delete']); exit;
}

// approve or reject — fetch author info for email
$stmt = $pdo->prepare("
    SELECT a.title, u.name AS author_name, u.email AS author_email
    FROM articles a JOIN users u ON a.user_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) { echo json_encode(['success' => false, 'error' => 'not_found']); exit; }

if ($action === 'approve') {
    $articleModel->approve($id);
    EmailService::sendArticleStatus(
        $article['author_email'], $article['author_name'], $article['title'], 'approved'
    );
    echo json_encode(['success' => true, 'action' => 'approve', 'new_status' => 'approved']); exit;
}

// reject
$articleModel->reject($id);
EmailService::sendArticleStatus(
    $article['author_email'], $article['author_name'], $article['title'], 'rejected'
);
echo json_encode(['success' => true, 'action' => 'reject', 'new_status' => 'rejected']);
