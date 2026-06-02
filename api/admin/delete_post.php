<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Core\Database;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'forbidden']); exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'csrf']); exit;
}

$id = intval($_POST['post_id'] ?? 0);
if (!$id) { echo json_encode(['success' => false, 'error' => 'invalid_id']); exit; }

Database::getInstance()->getPdo()
    ->prepare("DELETE FROM posts WHERE id = ?")
    ->execute([$id]);

echo json_encode(['success' => true]);
