<?php

namespace App;

use PDO;
use PDOException;

/**
 * Database Singleton Class
 * Manages database connections (MySQL or SQLite fallback)
 */
class Database
{
    private static ?PDO $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $this->connection = $this->createConnection();
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createTables();
    }

    /**
     * Get database singleton instance
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $db = new self();
            self::$instance = $db->connection;
        }
        return self::$instance;
    }

    /**
     * Create database connection (MySQL or SQLite)
     */
    private function createConnection(): PDO
    {
        $mysqlConfig = __DIR__ . '/../../db/db.php';

        // Try MySQL first
        if (file_exists($mysqlConfig)) {
            require_once $mysqlConfig;
            if (isset($pdo) && $pdo instanceof PDO) {
                return $pdo;
            }
        }

        // Fallback to SQLite
        return $this->createSqliteConnection();
    }

    /**
     * Create SQLite connection
     */
    private function createSqliteConnection(): PDO
    {
        $dataDir = __DIR__ . '/../data';
        if (!is_dir($dataDir)) {
            @mkdir($dataDir, 0755, true);
        }

        $dbPath = $dataDir . '/users.db';
        return new PDO('sqlite:' . $dbPath);
    }

    /**
     * Create required tables if they don't exist
     */
    private function createTables(): void
    {
        $driver = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'mysql') {
            $this->createMysqlTables();
        } else {
            $this->createSqliteTables();
        }
    }

    /**
     * Create MySQL tables
     */
    private function createMysqlTables(): void
    {
        $this->connection->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'klant',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->connection->exec("CREATE TABLE IF NOT EXISTS invitations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            token VARCHAR(255) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL,
            inviter_id INT,
            role VARCHAR(20) NOT NULL DEFAULT 'guest',
            used TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->connection->exec("CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            activity_type VARCHAR(50),
            naam VARCHAR(255),
            email VARCHAR(255),
            telefoon VARCHAR(20),
            datum DATE,
            tijd TIME,
            gasten INT DEFAULT 1,
            locatie VARCHAR(255),
            overdekt TINYINT DEFAULT 0,
            opmerkingen TEXT,
            plaats VARCHAR(255),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Create SQLite tables
     */
    private function createSqliteTables(): void
    {
        $this->connection->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'klant',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $this->connection->exec("CREATE TABLE IF NOT EXISTS invitations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            token TEXT UNIQUE NOT NULL,
            email TEXT NOT NULL,
            inviter_id INTEGER,
            role TEXT NOT NULL DEFAULT 'guest',
            used INTEGER NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $this->connection->exec("CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            activity_type TEXT,
            naam TEXT,
            email TEXT,
            telefoon TEXT,
            datum DATE,
            tijd TIME,
            gasten INTEGER DEFAULT 1,
            locatie TEXT,
            overdekt INTEGER DEFAULT 0,
            opmerkingen TEXT,
            plaats TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }
}
