<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Core\Database;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'forbidden']); exit;
}

$id  = intval($_GET['id'] ?? 0);
if (!$id) { echo json_encode(['success' => false, 'error' => 'invalid_id']); exit; }

$pdo  = Database::getInstance()->getPdo();

$stmt = $pdo->prepare(
    "SELECT id, name, email, role, avatar, created_at FROM users WHERE id = ?"
);
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) { echo json_encode(['success' => false, 'error' => 'not_found']); exit; }

$pCount = $pdo->prepare("SELECT COUNT(*) FROM posts    WHERE user_id = ?");
$aCount = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE user_id = ? AND status = 'approved'");
$pCount->execute([$id]);
$aCount->execute([$id]);

// last activity = latest created_at across posts and articles
$lastStmt = $pdo->prepare("
    SELECT MAX(ts) FROM (
        SELECT MAX(created_at) AS ts FROM posts    WHERE user_id = ?
        UNION ALL
        SELECT MAX(created_at) AS ts FROM articles WHERE user_id = ?
    ) t
");
$lastStmt->execute([$id, $id]);

$user['post_count']    = (int)$pCount->fetchColumn();
$user['article_count'] = (int)$aCount->fetchColumn();
$user['last_active']   = $lastStmt->fetchColumn() ?: null;

echo json_encode(['success' => true, 'user' => $user]);
