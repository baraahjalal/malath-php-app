<?php
namespace App\Models;

use App\Core\Model;

class ArticleModel extends Model {
    protected string $table = 'articles';

    public function createSuggestion(
        int $userId,
        int $communityId,
        string $title,
        string $content,
        ?string $image
    ): int {
        $this->query(
            "INSERT INTO articles (user_id, community_id, title, content, image)
             VALUES (?, ?, ?, ?, ?)",
            [$userId, $communityId, $title, $content, $image]
        );
        return (int)$this->db->lastInsertId();
    }

    public function getApproved(?string $communitySlug = null, int $limit = 9, int $offset = 0): array {
        $where  = "WHERE a.status = 'approved'";
        $params = [];

        if ($communitySlug !== null) {
            $where  .= " AND c.slug = ?";
            $params[] = $communitySlug;
        }

        $params[] = $limit;
        $params[] = $offset;

        return $this->query("
            SELECT a.id, a.title, a.content, a.image, a.created_at,
                   u.name AS author_name, u.avatar AS author_avatar,
                   c.name AS community_name, c.slug AS community_slug
            FROM articles a
            JOIN users u ON a.user_id = u.id
            JOIN communities c ON a.community_id = c.id
            $where
            ORDER BY a.created_at DESC
            LIMIT ? OFFSET ?
        ", $params)->fetchAll();
    }

    public function countApproved(?string $communitySlug = null): int {
        $where  = "WHERE a.status = 'approved'";
        $params = [];

        if ($communitySlug !== null) {
            $where  .= " AND c.slug = ?";
            $params[] = $communitySlug;
        }

        return (int)$this->query("
            SELECT COUNT(*) FROM articles a
            JOIN communities c ON a.community_id = c.id
            $where
        ", $params)->fetchColumn();
    }

    public function getById(int $id): ?array {
        $row = $this->query("
            SELECT a.id, a.title, a.content, a.image, a.created_at,
                   u.name AS author_name, u.avatar AS author_avatar,
                   c.name AS community_name, c.slug AS community_slug
            FROM articles a
            JOIN users u ON a.user_id = u.id
            JOIN communities c ON a.community_id = c.id
            WHERE a.id = ? AND a.status = 'approved'
        ", [$id])->fetch();

        return $row ?: null;
    }

    public function countPending(): int {
        return (int)$this->query(
            "SELECT COUNT(*) FROM articles WHERE status = 'pending'"
        )->fetchColumn();
    }

    public function getPending(): array {
        return $this->query("
            SELECT a.id, a.title, a.content, a.image, a.status, a.created_at,
                   u.name AS author_name, u.avatar AS author_avatar,
                   c.name AS community_name, c.slug AS community_slug
            FROM articles a
            JOIN users u ON a.user_id = u.id
            JOIN communities c ON a.community_id = c.id
            WHERE a.status = 'pending'
            ORDER BY a.created_at ASC
        ")->fetchAll();
    }

    public function approve(int $id): void {
        $this->query("UPDATE articles SET status = 'approved' WHERE id = ?", [$id]);
    }

    public function reject(int $id): void {
        $this->query("UPDATE articles SET status = 'rejected' WHERE id = ?", [$id]);
    }

    public function getArticleWithAuthor(int $id): ?array {
        $row = $this->query("
            SELECT a.id, a.title, u.name AS author_name, u.email AS author_email
            FROM articles a
            JOIN users u ON a.user_id = u.id
            WHERE a.id = ?
        ", [$id])->fetch();
        return $row ?: null;
    }

    public function toggleSave(int $userId, int $articleId): bool {
        $exists = (int)$this->query(
            "SELECT COUNT(*) FROM bookmarks WHERE user_id = ? AND article_id = ?",
            [$userId, $articleId]
        )->fetchColumn();

        if ($exists) {
            $this->query(
                "DELETE FROM bookmarks WHERE user_id = ? AND article_id = ?",
                [$userId, $articleId]
            );
            return false;
        }

        $this->query(
            "INSERT INTO bookmarks (user_id, article_id) VALUES (?, ?)",
            [$userId, $articleId]
        );
        return true;
    }

    public function isSaved(int $userId, int $articleId): bool {
        return (bool)$this->query(
            "SELECT COUNT(*) FROM bookmarks WHERE user_id = ? AND article_id = ?",
            [$userId, $articleId]
        )->fetchColumn();
    }

    public function getSavedArticles(int $userId): array {
        return $this->query("
            SELECT a.id, a.title, a.content, a.image, a.created_at,
                   u.name AS author_name, u.avatar AS author_avatar,
                   c.name AS community_name, c.slug AS community_slug
            FROM bookmarks b
            JOIN articles a ON b.article_id = a.id
            JOIN users u ON a.user_id = u.id
            JOIN communities c ON a.community_id = c.id
            WHERE b.user_id = ? AND a.status = 'approved'
            ORDER BY b.created_at DESC
        ", [$userId])->fetchAll();
    }
}
