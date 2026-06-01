<?php
namespace App\Models;

use App\Core\Model;

class PostModel extends Model {
    protected string $table = 'posts';

    public function getPostsForFeed(?int $communityId, int $viewerUserId): array {
        $base = "
            SELECT p.*, u.name AS user_name, u.avatar AS user_avatar, c.name AS community_name,
                   (SELECT COUNT(*) FROM post_likes    WHERE post_id = p.id) AS likes_count,
                   (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) AS comments_count,
                   (SELECT COUNT(*) FROM post_likes    WHERE post_id = p.id AND user_id = :uid1) AS is_liked,
                   (SELECT COUNT(*) FROM post_saves    WHERE post_id = p.id AND user_id = :uid2) AS is_saved
            FROM posts p
            JOIN users u ON u.id = p.user_id
            JOIN communities c ON c.id = p.community_id
        ";
        if ($communityId) {
            $st = $this->query($base . " WHERE c.id = :cid ORDER BY p.created_at DESC", [
                'uid1' => $viewerUserId, 'uid2' => $viewerUserId, 'cid' => $communityId
            ]);
        } else {
            $st = $this->query($base . " ORDER BY p.created_at DESC", [
                'uid1' => $viewerUserId, 'uid2' => $viewerUserId
            ]);
        }
        return $st->fetchAll();
    }

    public function create(int $userId, int $communityId, string $type, string $title, string $content): int {
        $this->query(
            "INSERT INTO posts (user_id, community_id, type, title, content, created_at) VALUES (?,?,?,?,?,NOW())",
            [$userId, $communityId, $type, $title, $content]
        );
        return (int)$this->db->lastInsertId();
    }

    public function deleteOwned(int $postId, int $userId): void {
        $this->query("DELETE FROM posts WHERE id = ? AND user_id = ?", [$postId, $userId]);
    }

    public function updateContent(int $postId, int $userId, string $content): void {
        $this->query("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?", [$content, $postId, $userId]);
    }

    public function deleteComment(int $commentId, int $userId): bool {
        $st = $this->query(
            "DELETE FROM post_comments WHERE id = ? AND user_id = ?",
            [$commentId, $userId]
        );
        return $st->rowCount() > 0;
    }

    public function getComments(int $postId): array {
        return $this->query("
            SELECT pc.*, u.name AS user_name, u.avatar AS user_avatar
            FROM post_comments pc JOIN users u ON pc.user_id = u.id
            WHERE pc.post_id = ? ORDER BY pc.created_at ASC
        ", [$postId])->fetchAll();
    }

    public function getArticles(int $limit = 12, int $offset = 0): array {
        return $this->query("
            SELECT p.*, u.name AS author_name, u.avatar AS author_avatar, c.name AS community_name
            FROM posts p
            JOIN users u ON p.user_id = u.id
            JOIN communities c ON p.community_id = c.id
            WHERE p.type = 'article'
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ", [$limit, $offset])->fetchAll();
    }

    public function countArticles(): int {
        return (int)$this->query("SELECT COUNT(*) FROM posts WHERE type = 'article'")->fetchColumn();
    }

    public function getArticleById(int $id): ?array {
        $st = $this->query("
            SELECT p.*, u.name AS author_name, u.avatar AS author_avatar, c.name AS community_name,
                   (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) AS likes_count,
                   (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) AS comments_count
            FROM posts p
            JOIN users u ON p.user_id = u.id
            JOIN communities c ON p.community_id = c.id
            WHERE p.id = ? AND p.type = 'article'
        ", [$id]);
        return $st->fetch() ?: null;
    }
}
