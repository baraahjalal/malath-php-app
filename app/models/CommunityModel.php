<?php
namespace App\Models;

use App\Core\Model;

class CommunityModel extends Model {
    protected string $table = 'communities';

    public function getAll(): array {
        return $this->query("SELECT * FROM communities")->fetchAll();
    }

    public function getUserCommunities(int $userId): array {
        $st = $this->query("SELECT community_id FROM user_communities WHERE user_id = ?", [$userId]);
        return array_column($st->fetchAll(), 'community_id');
    }

    public function join(int $userId, int $communityId): void {
        $this->query(
            "INSERT IGNORE INTO user_communities (user_id, community_id) VALUES (?,?)",
            [$userId, $communityId]
        );
    }

    public function leave(int $userId, int $communityId): void {
        $this->query(
            "DELETE FROM user_communities WHERE user_id = ? AND community_id = ?",
            [$userId, $communityId]
        );
    }
}
