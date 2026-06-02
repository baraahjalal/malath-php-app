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

$targetId = intval($_POST['user_id'] ?? 0);
$action   = $_POST['action'] ?? '';

if (!$targetId || !in_array($action, ['delete', 'toggle_role'], true)) {
    echo json_encode(['success' => false, 'error' => 'invalid_params']); exit;
}

if ($targetId === (int)$_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'cannot_modify_self']); exit;
}

$pdo = Database::getInstance()->getPdo();

if ($action === 'delete') {
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$targetId]);
    echo json_encode(['success' => true, 'action' => 'delete']); exit;
}

// toggle_role
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$targetId]);
$current = $stmt->fetchColumn();
if (!$current) { echo json_encode(['success' => false, 'error' => 'not_found']); exit; }

$newRole = ($current === 'admin') ? 'user' : 'admin';
$pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$newRole, $targetId]);
echo json_encode(['success' => true, 'action' => 'toggle_role', 'new_role' => $newRole]);
