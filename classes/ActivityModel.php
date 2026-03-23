<?php

class ActivityModel
{
    private PDO $db;

    public function __construct()
    {
        // Hergebruik van de database connectie via Singleton
        $this->db = Database::getInstance();
    }

    // Haal alle binnen-activiteiten op
    public function getBinnen(): array
    {
        $sql = "
            SELECT * FROM bookings
            WHERE activity_type = 'binnen'
            ORDER BY datum ASC, tijd ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Haal alle buiten-activiteiten op
    public function getBuiten(): array
    {
        $sql = "
            SELECT * FROM bookings
            WHERE activity_type = 'buiten'
            ORDER BY datum ASC, tijd ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Haal één boeking op via ID
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    // Nieuwe boeking opslaan
    public function create(array $data): bool
    {
        $sql = "
            INSERT INTO bookings 
            (activity_type, naam, email, telefoon, datum, tijd, gasten, opmerkingen, plaats)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['activity_type'],
            $data['naam'],
            $data['email'],
            $data['telefoon'],
            $data['datum'],
            $data['tijd'],
            $data['gasten'],
            $data['opmerkingen'],
            $data['plaats']
        ]);
    }

    // Bestaande boeking updaten
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE bookings SET
                activity_type = ?, 
                naam = ?, 
                email = ?, 
                telefoon = ?, 
                datum = ?, 
                tijd = ?, 
                gasten = ?, 
                opmerkingen = ?, 
                plaats = ?
            WHERE id = ?
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['activity_type'],
            $data['naam'],
            $data['email'],
            $data['telefoon'],
            $data['datum'],
            $data['tijd'],
            $data['gasten'],
            $data['opmerkingen'],
            $data['plaats'],
            $id
        ]);
    }

    // Verwijder boeking
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Haal boekingen op per type
    public function getByType(string $type): array
    {
        $sql = "
            SELECT * FROM bookings
            WHERE activity_type = ?
            ORDER BY datum ASC, tijd ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$type]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}