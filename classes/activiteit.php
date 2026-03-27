<?php

abstract class Activiteit
{
    protected PDO $db;

    
    public int $id;
    public string $activity_type;
    public string $naam;
    public string $email;
    public string $telefoon;
    public string $datum;
    public string $tijd;
    public int $gasten;
    public string $locatie;
    public string $opmerkingen;
    public string $plaats;
    public string $created_at;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->activity_type = $this->getType();
    }

    // Elke subclass MOET dit implementeren
    abstract protected function getType(): string;

    // ------------------------------
    // LOAD
    // ------------------------------
    public function loadById(int $id): bool
    {
        $sql = "SELECT * FROM bookings WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $data = $stmt->fetch();

        if ($data) {
            $this->fill($data);
            return true;
        }

        return false;
    }

    protected function fill(array $data): void
    {
        $this->id            = (int)$data['id'];
        $this->activity_type = $data['activity_type'];
        $this->naam          = $data['naam'];
        $this->email         = $data['email'];
        $this->telefoon      = $data['telefoon'];
        $this->datum         = $data['datum'];
        $this->tijd          = $data['tijd'];
        $this->gasten        = (int)$data['gasten'];
        $this->locatie       = $data['locatie'];
        $this->opmerkingen   = $data['opmerkingen'];
        $this->plaats        = $data['plaats'];
        $this->created_at    = $data['created_at'];
    }

    // ------------------------------
    // SAVE
    // ------------------------------
    public function save(): bool
    {
        if (!empty($this->id)) {
            return $this->update();
        }
        return $this->insert();
    }

    protected function insert(): bool
    {
        $sql = "INSERT INTO bookings 
                (activity_type, naam, email, telefoon, datum, tijd, gasten, locatie, opmerkingen, plaats, created_at)
                VALUES 
                (:activity_type, :naam, :email, :telefoon, :datum, :tijd, :gasten, :locatie, :opmerkingen, :plaats, NOW())";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':activity_type', $this->activity_type);
        $stmt->bindParam(':naam', $this->naam);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':telefoon', $this->telefoon);
        $stmt->bindParam(':datum', $this->datum);
        $stmt->bindParam(':tijd', $this->tijd);
        $stmt->bindParam(':gasten', $this->gasten);
        $stmt->bindParam(':locatie', $this->locatie);
        $stmt->bindParam(':opmerkingen', $this->opmerkingen);
        $stmt->bindParam(':plaats', $this->plaats);

        if ($stmt->execute()) {
            $this->id = (int)$this->db->lastInsertId();
            return true;
        }

        return false;
    }

    protected function update(): bool
    {
        $sql = "UPDATE bookings SET
                activity_type = :activity_type,
                naam = :naam,
                email = :email,
                telefoon = :telefoon,
                datum = :datum,
                tijd = :tijd,
                gasten = :gasten,
                locatie = :locatie,
                opmerkingen = :opmerkingen,
                plaats = :plaats
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':activity_type', $this->activity_type);
        $stmt->bindParam(':naam', $this->naam);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':telefoon', $this->telefoon);
        $stmt->bindParam(':datum', $this->datum);
        $stmt->bindParam(':tijd', $this->tijd);
        $stmt->bindParam(':gasten', $this->gasten);
        $stmt->bindParam(':locatie', $this->locatie);
        $stmt->bindParam(':opmerkingen', $this->opmerkingen);
        $stmt->bindParam(':plaats', $this->plaats);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // ------------------------------
    // DELETE
    // ------------------------------
    public function delete(): bool
    {
        if (empty($this->id)) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = :id");
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // ------------------------------
    // EXPORT DATA
    // ------------------------------
    public function getData(): array
    {
        return [
            'id'            => $this->id,
            'activity_type' => $this->activity_type,
            'naam'          => $this->naam,
            'email'         => $this->email,
            'telefoon'      => $this->telefoon,
            'datum'         => $this->datum,
            'tijd'          => $this->tijd,
            'gasten'        => $this->gasten,
            'locatie'       => $this->locatie,
            'opmerkingen'   => $this->opmerkingen,
            'plaats'        => $this->plaats,
            'created_at'    => $this->created_at
        ];
    }
    public static function getAllByType(string $type): array
{
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT * FROM bookings WHERE activity_type = :type ORDER BY datum, tijd");
    $stmt->bindParam(':type', $type);
    $stmt->execute();
    return $stmt->fetchAll();
}

public static function getAll(): array
{
    $type = (new static())->getType(); // haalt type uit subclass
    return static::getAllByType($type);
}
}