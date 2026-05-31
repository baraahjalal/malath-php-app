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
$user_id = $_SESSION['user_id'];

if (!$post_id) {
    echo json_encode(['success' => false]);
    exit;
}

$chk = $pdo->prepare("SELECT id FROM post_likes WHERE post_id=? AND user_id=?");
$chk->execute([$post_id, $user_id]);

if ($chk->fetch()) {
    $pdo->prepare("DELETE FROM post_likes WHERE post_id=? AND user_id=?")->execute([$post_id, $user_id]);
    $liked = false;
} else {
    $pdo->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?,?)")->execute([$post_id, $user_id]);
    $liked = true;
    // إشعار صاحب المنشور
    $owner = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $owner->execute([$post_id]);
    $owner_id = (int)($owner->fetchColumn() ?: 0);
    if ($owner_id) notify($pdo, $owner_id, $user_id, 'like', $post_id);
}

$count = $pdo->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id=?");
$count->execute([$post_id]);

echo json_encode([
    'success' => true,
    'liked'   => $liked,
    'count'   => (int)$count->fetchColumn(),
]);
