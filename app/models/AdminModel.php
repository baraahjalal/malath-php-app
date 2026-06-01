<?php
namespace App\Models;

use App\Core\Model;

class AdminModel extends Model {
    protected string $table = 'users';

    public function getStats(): array {
        return [
            'total_users'    => (int)$this->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_posts'    => (int)$this->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
            'total_comments' => (int)$this->query("SELECT COUNT(*) FROM post_comments")->fetchColumn(),
            'total_likes'    => (int)$this->query("SELECT COUNT(*) FROM post_likes")->fetchColumn(),
            'new_users_week' => (int)$this->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn(),
            'new_posts_week' => (int)$this->query("SELECT COUNT(*) FROM posts  WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn(),
        ];
    }

    public function getRecentPosts(int $limit = 8): array {
        return $this->query("
            SELECT p.*, u.name AS user_name, c.name AS community_name
            FROM posts p JOIN users u ON p.user_id=u.id JOIN communities c ON p.community_id=c.id
            ORDER BY p.created_at DESC LIMIT ?
        ", [$limit])->fetchAll();
    }

    public function getRecentUsers(int $limit = 6): array {
        return $this->query(
            "SELECT id,name,email,role,created_at FROM users ORDER BY created_at DESC LIMIT ?",
            [$limit]
        )->fetchAll();
    }

    public function getUsers(string $search = ''): array {
        if ($search) {
            return $this->query(
                "SELECT id,name,email,role,created_at FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC LIMIT 50",
                ["%$search%", "%$search%"]
            )->fetchAll();
        }
        return $this->query("SELECT id,name,email,role,created_at FROM users ORDER BY created_at DESC LIMIT 50")->fetchAll();
    }

    public function getPosts(): array {
        return $this->query("
            SELECT p.id, p.title, p.content, p.type, p.created_at,
                   u.name AS user_name, c.name AS community_name,
                   (SELECT COUNT(*) FROM post_likes    WHERE post_id=p.id) AS likes,
                   (SELECT COUNT(*) FROM post_comments WHERE post_id=p.id) AS comments
            FROM posts p JOIN users u ON p.user_id=u.id JOIN communities c ON p.community_id=c.id
            ORDER BY p.created_at DESC LIMIT 50
        ")->fetchAll();
    }

    public function deletePost(int $postId): void {
        $this->query("DELETE FROM posts WHERE id = ?", [$postId]);
    }

    public function deleteUser(int $userId): void {
        $this->query("DELETE FROM users WHERE id = ?", [$userId]);
    }

    public function setUserRole(int $userId, string $role): void {
        $this->query("UPDATE users SET role = ? WHERE id = ?", [$role, $userId]);
    }
}
