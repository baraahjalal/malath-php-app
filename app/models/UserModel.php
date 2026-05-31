<?php
namespace App\Models;

use App\Core\Model;

class UserModel extends Model {
    protected string $table = 'users';

    public function findByEmail(string $email): ?array {
        $st = $this->query("SELECT * FROM users WHERE email = ?", [$email]);
        return $st->fetch() ?: null;
    }

    public function create(string $name, string $email, string $passwordHash): int {
        $this->query(
            "INSERT INTO users (name, email, password) VALUES (?,?,?)",
            [$name, $email, $passwordHash]
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
}
