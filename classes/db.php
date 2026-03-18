<?php
require_once __DIR__ . '/config.php';

class Database {

    private $pdo;

    public function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST .
                    ";dbname=" . DB_NAME .
                    ";port=" . DB_PORT;

            $this->pdo = new PDO(
                $dsn,
                DB_USER,
                DB_PASS
            );

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die("Databasefout: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}