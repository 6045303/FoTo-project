<?php
require_once __DIR__ . '../config.php';

class Database
{
    private static ?PDO $instance = null;

    // Constructor blokkeren zodat niemand new Database() kan doen
    private function __construct() {}

    // Singleton: altijd dezelfde PDO-verbinding teruggeven
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {

            $dsn = "mysql:host=" . DB_HOST .
                   ";dbname=" . DB_NAME .
                   ";port=" . DB_PORT .
                   ";charset=utf8mb4";

            try {
                self::$instance = new PDO(
                    $dsn,
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );

            } catch (PDOException $e) {
                die("Databasefout: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}