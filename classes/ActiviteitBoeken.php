<?php

class ActiviteitBoeken
{
    private int $id;
    private string $activity_type;

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

    // -------------------------
    //  Constructor
    // -------------------------
    private function __construct(
        int $id,
        string $activity_type,
        string $naam,
        string $email,
        string $telefoon,
        string $datum,
        string $tijd,
        int $gasten,
        string $locatie,
        string $opmerkingen,
        string $plaats,
        string $created_at
    ) {
        $this->id            = $id;
        $this->activity_type = $activity_type;
        $this->naam          = $naam;
        $this->email         = $email;
        $this->telefoon      = $telefoon;
        $this->datum         = $datum;
        $this->tijd          = $tijd;
        $this->gasten        = $gasten;
        $this->locatie       = $locatie;
        $this->opmerkingen   = $opmerkingen;
        $this->plaats        = $plaats;
        $this->created_at    = $created_at;
    }

    // -------------------------
    //  Static constructor: vanuit database
    // -------------------------
    public static function fromDatabase(array $row): ActiviteitBoeken
    {
        return new ActiviteitBoeken(
            $row['id'],
            $row['activity_type'],
            $row['naam'],
            $row['email'],
            $row['telefoon'],
            $row['datum'],
            $row['tijd'],
            (int)$row['gasten'],
            $row['locatie'],
            $row['opmerkingen'],
            $row['plaats'],
            $row['created_at']
        );
    }

    // -------------------------
    //  Static constructor: nieuwe boeking maken
    // -------------------------
    public static function create(array $data): ActiviteitBoeken
    {
        return new ActiviteitBoeken(
            0, // wordt door database ingevuld
            $data['activity_type'],
            $data['naam'],
            $data['email'],
            $data['telefoon'],
            $data['datum'],
            $data['tijd'],
            (int)$data['gasten'],
            $data['locatie'],
            $data['opmerkingen'],
            $data['plaats'],
            date("Y-m-d H:i:s")
        );
    }

    // -------------------------
    // Getters
    // -------------------------
    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->activity_type;
    }

    // -------------------------
    //  Domain methods (gedrag)
    // -------------------------

    public function wijzigDatum(string $nieuweDatum): void
    {
        $this->datum = $nieuweDatum;
    }

    public function wijzigTijd(string $nieuweTijd): void
    {
        $this->tijd = $nieuweTijd;
    }

    public function wijzigLocatie(string $nieuweLocatie): void
    {
        $this->locatie = $nieuweLocatie;
    }

    public function wijzigGasten(int $aantal): void
    {
        $this->gasten = $aantal;
    }

    public function wijzigOpmerkingen(string $tekst): void
    {
        $this->opmerkingen = $tekst;
    }

    public function wijzigPlaats(string $nieuwePlaats): void
    {
        $this->plaats = $nieuwePlaats;
    }
}