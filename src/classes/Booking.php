<?php

namespace App;

use PDOException;

/**
 * Booking Model Class
 * Handles activity bookings (binnen and buiten activities)
 */
class Booking
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new booking
     */
    public function create(array $data): bool
    {
        $this->validateBookingData($data);

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO bookings (activity_type, naam, email, telefoon, datum, tijd, gasten, locatie, overdekt, opmerkingen, plaats)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            return $stmt->execute([
                $data['activity_type'] ?? 'binnen',
                $data['naam'] ?? '',
                $data['email'] ?? null,
                $data['telefoon'] ?? null,
                $data['datum'] ?? null,
                $data['tijd'] ?? null,
                $data['gasten'] ?? 1,
                $data['locatie'] ?? null,
                $data['overdekt'] ?? 0,
                $data['opmerkingen'] ?? null,
                $data['plaats'] ?? null
            ]);
        } catch (PDOException $e) {
            throw new \Exception('Er is een fout opgetreden bij het opslaan van de boeking.');
        }
    }

    /**
     * Get all bookings
     */
    public function getAll(): array
    {
        try {
            $stmt = $this->db->query(
                'SELECT id, activity_type, naam, email, telefoon, datum, tijd, gasten, locatie, overdekt, opmerkingen, plaats, created_at
                 FROM bookings ORDER BY created_at DESC'
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get booking by ID
     */
    public function getById(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT id, activity_type, naam, email, telefoon, datum, tijd, gasten, locatie, overdekt, opmerkingen, plaats, created_at
                 FROM bookings WHERE id = ?'
            );
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get bookings by activity type
     */
    public function getByType(string $type): array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT id, activity_type, naam, email, telefoon, datum, tijd, gasten, locatie, overdekt, opmerkingen, plaats, created_at
                 FROM bookings WHERE activity_type = ? ORDER BY created_at DESC'
            );
            $stmt->execute([$type]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Delete booking
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM bookings WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new \Exception('Kon boeking niet verwijderen.');
        }
    }

    /**
     * Update booking
     */
    public function update(int $id, array $data): bool
    {
        try {
            $stmt = $this->db->prepare(
                'UPDATE bookings SET activity_type = ?, naam = ?, email = ?, telefoon = ?, datum = ?, tijd = ?, gasten = ?, locatie = ?, overdekt = ?, opmerkingen = ?, plaats = ?
                 WHERE id = ?'
            );

            return $stmt->execute([
                $data['activity_type'] ?? 'binnen',
                $data['naam'] ?? '',
                $data['email'] ?? null,
                $data['telefoon'] ?? null,
                $data['datum'] ?? null,
                $data['tijd'] ?? null,
                $data['gasten'] ?? 1,
                $data['locatie'] ?? null,
                $data['overdekt'] ?? 0,
                $data['opmerkingen'] ?? null,
                $data['plaats'] ?? null,
                $id
            ]);
        } catch (PDOException $e) {
            throw new \Exception('Kon boeking niet bijwerken.');
        }
    }

    /**
     * Validate booking data
     */
    private function validateBookingData(array $data): void
    {
        if (empty($data['naam'])) {
            throw new \Exception('Naam is verplicht.');
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Ongeldig e-mailadres.');
        }

        if (!empty($data['datum']) && !$this->isValidDate($data['datum'])) {
            throw new \Exception('Ongeldige datum.');
        }

        if (!in_array($data['activity_type'] ?? 'binnen', ['binnen', 'buiten'])) {
            throw new \Exception('Ongeldig activiteitstype.');
        }
    }

    /**
     * Validate date format
     */
    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
