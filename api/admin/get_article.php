<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Core\Database;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'forbidden']); exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) { echo json_encode(['success' => false, 'error' => 'invalid_id']); exit; }

$pdo  = Database::getInstance()->getPdo();
$stmt = $pdo->prepare("
    SELECT a.id, a.title, a.content, a.image, a.status, a.created_at,
           u.name AS author_name, u.avatar AS author_avatar,
           c.name AS community_name, c.slug AS community_slug
    FROM articles a
    JOIN users u  ON a.user_id      = u.id
    JOIN communities c ON a.community_id = c.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) { echo json_encode(['success' => false, 'error' => 'not_found']); exit; }

echo json_encode(['success' => true, 'article' => $article]);
