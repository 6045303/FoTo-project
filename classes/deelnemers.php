<?php

require_once __DIR__ . '/db.php';

class deelnemers
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance(); // Singleton
    }

    // Check of gebruiker is aangemeld
    public function isAangemeld(int $userId, int $activityId): bool
    {
        $stmt = $this->db->prepare("
            SELECT 1 FROM activity_participants
            WHERE user_id = ? AND activity_id = ?
        ");
        $stmt->execute([$userId, $activityId]);

        return (bool) $stmt->fetchColumn();
    }

    // Aanmelden
    public function aanmelden(int $userId, int $activityId): bool
    {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO activity_participants (user_id, activity_id)
            VALUES (?, ?)
        ");

        return $stmt->execute([$userId, $activityId]);
    }

    // Afmelden
    public function afmelden(int $userId, int $activityId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM activity_participants
            WHERE user_id = ? AND activity_id = ?
        ");

        return $stmt->execute([$userId, $activityId]);
    }

    // Deelnemers ophalen
    public function getDeelnemers(int $activityId): array
    {
        $stmt = $this->db->prepare("
            SELECT u.username, u.email
            FROM activity_participants ap
            JOIN users u ON ap.user_id = u.id
            WHERE ap.activity_id = ?
        ");

        $stmt->execute([$activityId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}