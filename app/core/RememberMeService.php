<?php
namespace App\Core;

class RememberMeService {

    private const COOKIE_NAME = 'remember_token';
    private const COOKIE_DAYS = 30;
    private \PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getPdo();
    }

    public function create(int $userId): void {
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . self::COOKIE_DAYS . ' days'));

        $this->db->prepare(
            "INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)"
        )->execute([$userId, $tokenHash, $expiresAt]);

        setcookie(
            self::COOKIE_NAME,
            $token,
            [
                'expires'  => time() + (self::COOKIE_DAYS * 24 * 60 * 60),
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    public function validate(): ?int {
        $token = $_COOKIE[self::COOKIE_NAME] ?? null;
        if (!$token) {
            return null;
        }

        $tokenHash = hash('sha256', $token);
        $st = $this->db->prepare(
            "SELECT user_id FROM remember_tokens WHERE token_hash = ? AND expires_at > NOW()"
        );
        $st->execute([$tokenHash]);
        $row = $st->fetch();

        return $row ? (int)$row['user_id'] : null;
    }

    public function delete(): void {
        $token = $_COOKIE[self::COOKIE_NAME] ?? null;
        if ($token) {
            $tokenHash = hash('sha256', $token);
            $this->db->prepare(
                "DELETE FROM remember_tokens WHERE token_hash = ?"
            )->execute([$tokenHash]);
        }

        setcookie(self::COOKIE_NAME, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
