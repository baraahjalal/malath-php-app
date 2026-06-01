<?php
namespace App\Core;

class Database {
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct() {
        $host    = 'mysql.railway.internal';
        $db      = 'railway';
        $user    = 'root';
        $pass    = 'ozAWpQhQQFCuedbIpGNfzIbvVnTxRcvc';
        $charset = 'utf8mb4';
 $dsn     = "mysql:host=$host;port=3306;dbname=$db;charset=$charset";
        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            error_log("DB connection failed: " . $e->getMessage());
            die("خطأ في الاتصال بقاعدة البيانات.");
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): \PDO {
        return $this->pdo;
    }
}
