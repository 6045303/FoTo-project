<?php

class Database
{
    private PDO $_pdo;

    public function __construct()
    {
        // Laad .env uit dezelfde map als dit bestand
        $this->loadEnv(__DIR__ . '/.env');

        // Maak databaseverbinding
        $this->connectLocalDatabase();
    }

    private function loadEnv(string $path): void
    {
        if (!file_exists($path)) return;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, '#') === 0) continue; // commentaar
            if (!str_contains($line, '=')) continue;

            [$name, $value] = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }

    private function connectLocalDatabase(): void
    {
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $db   = $_ENV['DB_NAME'] ?? 'project_foto';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? 'root';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->_pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die("Database connectie mislukt: " . $e->getMessage());
        }
    }

    public function getPdo(): PDO
    {
        return $this->_pdo;
    }
}

// Gebruik:
$db = new Database();
$pdo = $db->getPdo();

echo "Database connectie geslaagd!";