<?php

namespace App;

use PDOException;

/**
 * User Model Class
 * Handles user authentication, registration, and profile management
 */
class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Register a new user
     */
    public function register(string $email, string $firstName, string $lastName, string $password, string $role = 'klant'): bool
    {
        if (!$this->validateEmail($email)) {
            throw new \Exception('Ongeldig e-mailadres.');
        }

        if (strlen($password) < 8) {
            throw new \Exception('Wachtwoord moet minimaal 8 karakters lang zijn.');
        }

        if (!preg_match('/[A-Z].*[A-Z]/', $password)) {
            throw new \Exception('Wachtwoord moet minimaal 2 hoofdletters bevatten.');
        }

        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare(
                'INSERT INTO users (email, first_name, last_name, password, role) VALUES (?, ?, ?, ?, ?)'
            );

            return $stmt->execute([$email, $firstName, $lastName, $hashedPassword, $role]);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE constraint') !== false) {
                throw new \Exception('Dit e-mailadres is al in gebruik.');
            }
            throw new \Exception('Er is een fout opgetreden bij registratie.');
        }
    }

    /**
     * Login user
     */
    public function login(string $email, string $password): ?array
    {
        try {
            $stmt = $this->db->prepare('SELECT id, first_name, last_name, role, password FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                return [
                    'id' => $user['id'],
                    'email' => $email,
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'role' => $user['role']
                ];
            }

            return null;
        } catch (PDOException $e) {
            throw new \Exception('Er is een fout opgetreden bij inloggen.');
        }
    }

    /**
     * Get user by ID
     */
    public function getById(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare('SELECT id, email, first_name, last_name, role, created_at FROM users WHERE id = ?');
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get all users
     */
    public function getAll(): array
    {
        try {
            $stmt = $this->db->query('SELECT id, email, first_name, last_name, role, created_at FROM users ORDER BY created_at DESC');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Update user role
     */
    public function updateRole(int $userId, string $newRole): bool
    {
        if (!in_array($newRole, ['admin', 'staff', 'klant', 'guest'])) {
            throw new \Exception('Ongeldige rol.');
        }

        try {
            $stmt = $this->db->prepare('UPDATE users SET role = ? WHERE id = ?');
            return $stmt->execute([$newRole, $userId]);
        } catch (PDOException $e) {
            throw new \Exception('Er is een fout opgetreden bij het bijwerken van de rol.');
        }
    }

    /**
     * Email exists check
     */
    public function emailExists(string $email): bool
    {
        try {
            $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) !== null;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Validate email format
     */
    private function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
