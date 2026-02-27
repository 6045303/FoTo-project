<?php

namespace App;

use PDOException;

/**
 * Invitation Model Class
 * Handles invitation creation, validation, and user registration via invitations
 */
class Invitation
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new invitation
     */
    public function create(string $email, int $inviterId, string $role = 'guest'): array
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Ongeldig e-mailadres.');
        }

        try {
            $token = bin2hex(random_bytes(16));

            $stmt = $this->db->prepare(
                'INSERT INTO invitations (token, email, inviter_id, role) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$token, $email, $inviterId, $role]);

            return [
                'token' => $token,
                'email' => $email
            ];
        } catch (PDOException $e) {
            throw new \Exception('Kon uitnodiging niet aanmaken.');
        }
    }

    /**
     * Get invitation by token
     */
    public function getByToken(string $token): ?array
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM invitations WHERE token = ? AND used = 0');
            $stmt->execute([$token]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Mark invitation as used
     */
    public function markAsUsed(string $token): bool
    {
        try {
            $stmt = $this->db->prepare('UPDATE invitations SET used = 1 WHERE token = ?');
            return $stmt->execute([$token]);
        } catch (PDOException $e) {
            throw new \Exception('Kon uitnodiging niet verwerken.');
        }
    }

    /**
     * Get all invitations (admin only)
     */
    public function getAll(): array
    {
        try {
            $stmt = $this->db->query(
                'SELECT id, token, email, inviter_id, role, used, created_at FROM invitations ORDER BY created_at DESC'
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get invitations by inviter
     */
    public function getByInviter(int $inviterId): array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT id, token, email, inviter_id, role, used, created_at FROM invitations WHERE inviter_id = ? ORDER BY created_at DESC'
            );
            $stmt->execute([$inviterId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
