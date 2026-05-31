<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/db.php';
require_once '../includes/csrf.php';
require_once '../includes/notify.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'login_required']);
    exit;
}

$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'csrf']);
    exit;
}

$post_id = intval($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');
$user_id = $_SESSION['user_id'];

if (!$post_id || $content === '') {
    echo json_encode(['success' => false, 'error' => 'empty']);
    exit;
}

$pdo->prepare("INSERT INTO post_comments (post_id, user_id, content) VALUES (?,?,?)")
    ->execute([$post_id, $user_id, $content]);

$comment_id = $pdo->lastInsertId();

// إشعار صاحب المنشور
$owner = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
$owner->execute([$post_id]);
$owner_id = (int)($owner->fetchColumn() ?: 0);
if ($owner_id) notify($pdo, $owner_id, $user_id, 'comment', $post_id);

$st = $pdo->prepare("
    SELECT pc.id, pc.content, pc.created_at, u.name, u.avatar
    FROM post_comments pc JOIN users u ON pc.user_id = u.id
    WHERE pc.id = ?
");
$st->execute([$comment_id]);
$row = $st->fetch();

echo json_encode([
    'success'    => true,
    'comment_id' => (int)$comment_id,
    'user_name'  => $row['name'],
    'user_avatar'=> $row['avatar'] ?: 'assets/images/default-avatar.png',
    'content'    => $row['content'],
    'created_at' => date('Y-m-d H:i', strtotime($row['created_at'])),
]);
