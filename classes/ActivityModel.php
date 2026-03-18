<?php

require_once __DIR__ . '/db.php';

class ActivityModel {

    private PDO $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    //select
    public function getBinnen(): array {
        $stmt = $this->db->prepare("
            SELECT * FROM bookings 
            WHERE activity_type = 'binnen'
            ORDER BY datum ASC, tijd ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBuiten(): array {
        $stmt = $this->db->prepare("
            SELECT * FROM bookings 
            WHERE activity_type = 'buiten'
            ORDER BY datum ASC, tijd ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    //insert
    public function create(array $data): bool {
        $sql = "INSERT INTO bookings 
                (activity_type, naam, email, telefoon, datum, tijd, gasten, opmerkingen, plaats)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
    //update
    public function update(int $id, array $data): bool {
        $sql = "UPDATE bookings SET
                activity_type = ?, 
                naam = ?, 
                email = ?, 
                telefoon = ?, 
                datum = ?, 
                tijd = ?, 
                gasten = ?, 
                opmerkingen = ?, 
                plaats = ?
                WHERE id = ?";

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

    //delete
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
        return $stmt->execute([$id]);
    }
}