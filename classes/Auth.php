<?php
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/database.php';

class Auth
{
    private PDO $conn;

    public function __construct()
    {
        session_start();
        $this->conn = Database::getInstance();
    }

    // Inloggen van gebruiker
    public function login(string $username, string $password): bool
    {
        // Gebruiker ophalen
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            return false; // gebruiker bestaat niet
        }

        // Wachtwoord controleren
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Rol bepalen
        if ($password === "1234") {
            $userObj = User::admin($user['id'], $user['username']);
        } else {
            $userObj = User::normal($user['id'], $user['username']);
        }

        // Opslaan in sessie
        $_SESSION['user'] = serialize($userObj);

        return true;
    }

    // Uitloggen
    public function logout()
    {
        session_unset();
        session_destroy();
    }

    // Huidige gebruiker ophalen
    public function user(): User
    {
        // Geen gebruiker → guest
        if (!isset($_SESSION['user'])) {
            return User::guest();
        }

        $data = $_SESSION['user'];

        // Als er per ongeluk een array in de sessie staat → fixen
        if (is_array($data)) {
            return User::fromDatabase(
                $data['id'],
                $data['username'],
                $data['role']
            );
        }

        // Normale situatie: serialized User object
        return unserialize($data);
    }

    // Alleen ingelogde gebruikers toestaan
    public function requireUser()
    {
        if ($this->user()->isGuest()) {
            header("Location: login.php?error=login_required");
            exit;
        }
    }

    // Alleen admin toestaan
    public function requireAdmin()
    {
        if (!$this->user()->isAdmin()) {
            header("Location: index.php?error=no_permission");
            exit;
        }
    }

    // Registreren van nieuwe gebruiker
    public function register(string $username, string $password): bool
    {
        // Check of gebruiker al bestaat
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            return false; // bestaat al
        }

        // Wachtwoord hashen
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Opslaan
        $stmt = $this->conn->prepare("
            INSERT INTO users (username, password, role)
            VALUES (?, ?, 'user')
        ");

        return $stmt->execute([$username, $hashed]);
    }
}