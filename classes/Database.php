<?php
require_once __DIR__ . '/config.php';

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {} // voorkomt dat er meerdere connecties worden gemaakt

    // Hergebruik van database connectie:
    // Via getInstance() wordt steeds dezelfde PDO connectie teruggegeven.
    // Dit maakt het project efficiënter en voorkomt dubbele verbindingen.
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {

            $dsn = "mysql:host=" . DB_HOST .
                   ";dbname=" . DB_NAME .
                   ";port=" . DB_PORT;

            try {
                self::$instance = new PDO(
                    $dsn,
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
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