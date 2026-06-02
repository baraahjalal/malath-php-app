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

$pdo = Database::getInstance()->getPdo();

$stmt = $pdo->prepare("
    SELECT p.id, p.content, p.type, p.title, p.created_at,
           u.name AS user_name,
           c.name AS community_name,
           (SELECT COUNT(*) FROM post_likes    WHERE post_id = p.id) AS likes,
           (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) AS comments_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN communities c ON p.community_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) { echo json_encode(['success' => false, 'error' => 'not_found']); exit; }

$cStmt = $pdo->prepare("
    SELECT pc.id, pc.content, pc.created_at, u.name AS user_name
    FROM post_comments pc JOIN users u ON pc.user_id = u.id
    WHERE pc.post_id = ? ORDER BY pc.created_at ASC
");
$cStmt->execute([$id]);
$post['comments'] = $cStmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'post' => $post]);
