<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;
$pdo = Database::getInstance()->getPdo();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'notifications' => [], 'unread' => 0]); exit;
}

$user_id = (int)$_SESSION['user_id'];
$action  = $_GET['action'] ?? 'fetch';

if ($action === 'mark_read' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        echo json_encode(['success' => false]); exit;
    }
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$user_id]);
    echo json_encode(['success' => true]); exit;
}

$st = $pdo->prepare("
    SELECT n.id, n.type, n.is_read, n.created_at, n.post_id,
           u.name AS actor_name, u.avatar AS actor_avatar
    FROM notifications n JOIN users u ON n.actor_id=u.id
    WHERE n.user_id = ? ORDER BY n.created_at DESC LIMIT 15
");
$st->execute([$user_id]);
$rows = $st->fetchAll();

$unread = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$unread->execute([$user_id]);
$unread_count = (int)$unread->fetchColumn();

$labels = ['like'=>'أعجبت بمنشورك','comment'=>'علّقت على منشورك','join'=>'انضمت لنفس مجتمعك'];
$icons  = ['like'=>'fa-heart','comment'=>'fa-comment-dots','join'=>'fa-users'];

$items = [];
foreach ($rows as $r) {
    $items[] = [
        'id'           => (int)$r['id'],
        'type'         => $r['type'],
        'is_read'      => (bool)$r['is_read'],
        'actor_name'   => $r['actor_name'],
        'actor_avatar' => $r['actor_avatar'] ?: 'assets/images/default-avatar.png',
        'label'        => $labels[$r['type']] ?? $r['type'],
        'icon'         => $icons[$r['type']]  ?? 'fa-bell',
        'post_id'      => $r['post_id'],
        'time'         => date('Y-m-d H:i', strtotime($r['created_at'])),
    ];
}

echo json_encode(['success' => true, 'notifications' => $items, 'unread' => $unread_count]);
