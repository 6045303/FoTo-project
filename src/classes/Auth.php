<?php

namespace App;

/**
 * Auth Helper Class
 * Manages session, authentication checks, and user roles
 */
class Auth
{
    /**
     * Start session if not already started
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Check if user is admin or staff
     */
    public static function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'staff']);
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole(string $role): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }

    /**
     * Get current user ID
     */
    public static function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user name
     */
    public static function getUserName(): ?string
    {
        return $_SESSION['user_name'] ?? null;
    }

    /**
     * Get current user role
     */
    public static function getUserRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Login user (set session)
     */
    public static function login(array $user): void
    {
        if (!isset($user['id']) || !isset($user['first_name'])) {
            throw new \Exception('Ongeldige gebruikersgegevens.');
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'];
        $_SESSION['user_role'] = $user['role'] ?? 'klant';
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        session_destroy();
    }

    /**
     * Redirect to login if not authenticated
     */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    /**
     * Redirect to login if not admin
     */
    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            header('Location: index.php?page=home');
            exit;
        }
    }
}
