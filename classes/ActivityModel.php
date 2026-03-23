<?php

class ActivityModel extends BaseModel implements ActivityRepositoryInterface
{
    public function getBinnen(): array
    {
        return $this->getByType('binnen');
    }

    public function getBuiten(): array
    {
        return $this->getByType('buiten');
    }

    public function getById(int $id): ?Activity
    {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? Activity::fromArray($result) : null;
    }

    public function getByType(string $type): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM bookings
            WHERE activity_type = ?
            ORDER BY datum ASC, tijd ASC
        ");
        $stmt->execute([$type]);

        return array_map(
            static fn(array $row): Activity => Activity::fromArray($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function create(Activity $activity): bool
    {
        $sql = "INSERT INTO bookings
                (activity_type, naam, email, telefoon, datum, tijd, gasten, opmerkingen, plaats)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $data = $activity->toArray();

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
        ]);
    }

    public function update(int $id, Activity $activity): bool
    {
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
        $data = $activity->toArray();

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
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
