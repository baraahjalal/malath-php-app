<?php
namespace App\Core;

abstract class Model {
    protected \PDO $db;
    protected string $table = '';

    public function __construct() {
        $this->db = Database::getInstance()->getPdo();
    }

    protected function query(string $sql, array $params = []): \PDOStatement {
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st;
    }

    public function findById(int $id): ?array {
        $st = $this->query("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        $row = $st->fetch();
        return $row ?: null;
    }
}
