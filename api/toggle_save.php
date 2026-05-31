<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/db.php';
require_once '../includes/csrf.php';

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

$chk = $pdo->prepare("SELECT id FROM post_saves WHERE post_id=? AND user_id=?");
$chk->execute([$post_id, $user_id]);

if ($chk->fetch()) {
    $pdo->prepare("DELETE FROM post_saves WHERE post_id=? AND user_id=?")->execute([$post_id, $user_id]);
    $saved = false;
} else {
    $pdo->prepare("INSERT INTO post_saves (post_id, user_id) VALUES (?,?)")->execute([$post_id, $user_id]);
    $saved = true;
}

echo json_encode(['success' => true, 'saved' => $saved]);
