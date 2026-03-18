<?php
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/db.php';

class Auth
{
    private $conn;

    public function __construct()
    {
        session_start();
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function login(string $username, string $password): bool
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) return false;

        if (!password_verify($password, $user['password'])) return false;

        $_SESSION['user'] = [
            "id" => $user['id'],
            "username" => $user['username'],
            "role" => $user['role']
        ];

        return true;
    }

    public function logout()
    {
        session_destroy();
    }

    public function user(): User
    {
        if (!isset($_SESSION['user'])) {
            return new User(); // guest
        }

        $u = $_SESSION['user'];
        return new User($u['id'], $u['username'], $u['role']);
    }

    public function requireUser()
    {
        if ($this->user()->isGuest()) {
            header("Location: login.php?error=login_required");
            exit;
        }
    }

    public function requireAdmin()
    {
        if (!$this->user()->isAdmin()) {
            header("Location: index.php?error=no_permission");
            exit;
        }
    }
}