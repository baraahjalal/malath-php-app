<?php
function notify(PDO $pdo, int $user_id, int $actor_id, string $type, ?int $post_id = null): void {
    if ($user_id === $actor_id) return;
    $pdo->prepare("
        INSERT INTO notifications (user_id, actor_id, type, post_id)
        VALUES (?, ?, ?, ?)
    ")->execute([$user_id, $actor_id, $type, $post_id]);
}
