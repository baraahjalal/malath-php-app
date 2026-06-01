<?php
namespace App\Models;

use App\Core\Model;

class UserModel extends Model {
    protected string $table = 'users';

    public function findByEmail(string $email): ?array {
        $st = $this->query("SELECT * FROM users WHERE email = ?", [$email]);
        return $st->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        $st = $this->query("SELECT * FROM users WHERE id = ?", [$id]);
        return $st->fetch() ?: null;
    }

    public function create(string $name, string $email, string $passwordHash): int {
        $this->query(
            "INSERT INTO users (name, email, password, avatar) VALUES (?,?,?,?)",
            [$name, $email, $passwordHash, 'assets/images/default-avatar.png']
        );
        return (int)$this->db->lastInsertId();
    }

    public function emailExists(string $email): bool {
        $st = $this->query("SELECT COUNT(*) FROM users WHERE email = ?", [$email]);
        return (int)$st->fetchColumn() > 0;
    }

    public function update(int $id, array $fields): void {
        $sets   = implode(', ', array_map(fn($k) => "$k = ?", array_keys($fields)));
        $values = array_values($fields);
        $values[] = $id;
        $this->query("UPDATE users SET $sets WHERE id = ?", $values);
    }

    public function getContributionsCount(int $userId): int {
        $posts    = (int)$this->query("SELECT COUNT(*) FROM posts WHERE user_id = ?", [$userId])->fetchColumn();
        $articles = (int)$this->query("SELECT COUNT(*) FROM posts WHERE user_id = ? AND type = 'article'", [$userId])->fetchColumn();
        return $posts;
    }

    public function getSavedCount(int $userId): int {
        return (int)$this->query("SELECT COUNT(*) FROM post_saves WHERE user_id = ?", [$userId])->fetchColumn();
    }

    public function getActivities(int $userId): array {
        return $this->query("
            SELECT 'post' AS activity_type, p.id AS activity_id, p.created_at AS activity_date,
                   p.id AS post_id, p.title AS post_title, p.content AS post_content,
                   p.type AS post_type, c.name AS community_name,
                   u.name AS author_name, u.avatar AS author_avatar, p.content AS activity_content
            FROM posts p JOIN communities c ON p.community_id=c.id JOIN users u ON p.user_id=u.id
            WHERE p.user_id = :uid1
            UNION ALL
            SELECT 'comment', pc.id, pc.created_at,
                   p.id, p.title, p.content, p.type, c.name,
                   u.name, u.avatar, pc.content COLLATE utf8mb4_unicode_ci
            FROM post_comments pc JOIN posts p ON pc.post_id=p.id
            JOIN communities c ON p.community_id=c.id JOIN users u ON p.user_id=u.id
            WHERE pc.user_id = :uid2
            UNION ALL
            SELECT 'like', pl.id, pl.created_at,
                   p.id, p.title, p.content, p.type, c.name,
                   u.name, u.avatar, '' COLLATE utf8mb4_unicode_ci
            FROM post_likes pl JOIN posts p ON pl.post_id=p.id
            JOIN communities c ON p.community_id=c.id JOIN users u ON p.user_id=u.id
            WHERE pl.user_id = :uid3
            ORDER BY activity_date DESC
        ", ['uid1' => $userId, 'uid2' => $userId, 'uid3' => $userId])->fetchAll();
    }

    public function getSavedPosts(int $userId): array {
        return $this->query("
            SELECT p.*, c.name AS community_name, u.name AS user_name, u.avatar AS user_avatar
            FROM post_saves ps
            JOIN posts p ON ps.post_id=p.id
            JOIN communities c ON p.community_id=c.id
            JOIN users u ON p.user_id=u.id
            WHERE ps.user_id = ?
            ORDER BY ps.created_at DESC
        ", [$userId])->fetchAll();
    }
}
