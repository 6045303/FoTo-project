<?php

class Auth
{
    private PDO $conn;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->conn = Database::getInstance()->getConnection();
    }

    public function login(string $username, string $password): bool
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];

        return true;
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function user(): User
    {
        if (!isset($_SESSION['user'])) {
            return User::guest();
        }

        return User::fromSession($_SESSION['user']);
    }

    public function requireUser(): void
    {
        if ($this->user()->isGuest()) {
            header('Location: login.php?error=login_required');
            exit;
        }
    }

    public function requireAdmin(): void
    {
        if (!$this->user()->isAdmin()) {
            header('Location: index.php?error=no_permission');
            exit;
        }
    }

    public function register(string $username, string $password): bool
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");

        return $stmt->execute([$username, $hashedPassword]);
    }
}
