<?php

class Activity
{
    private ?int $id;
    private string $activityType;
    private string $naam;
    private string $email;
    private string $telefoon;
    private string $datum;
    private string $tijd;
    private int $gasten;
    private string $opmerkingen;
    private string $plaats;

    public function __construct(
        ?int $id,
        string $activityType,
        string $naam,
        string $email,
        string $telefoon,
        string $datum,
        string $tijd,
        int $gasten,
        string $opmerkingen,
        string $plaats
    ) {
        $this->id = $id;
        $this->activityType = $activityType;
        $this->naam = $naam;
        $this->email = $email;
        $this->telefoon = $telefoon;
        $this->datum = $datum;
        $this->tijd = $tijd;
        $this->gasten = $gasten;
        $this->opmerkingen = $opmerkingen;
        $this->plaats = $plaats;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (string) ($data['activity_type'] ?? ''),
            trim((string) ($data['naam'] ?? '')),
            trim((string) ($data['email'] ?? '')),
            trim((string) ($data['telefoon'] ?? '')),
            (string) ($data['datum'] ?? ''),
            (string) ($data['tijd'] ?? ''),
            max(1, (int) ($data['gasten'] ?? 1)),
            trim((string) ($data['opmerkingen'] ?? '')),
            trim((string) ($data['plaats'] ?? ''))
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'activity_type' => $this->activityType,
            'naam' => $this->naam,
            'email' => $this->email,
            'telefoon' => $this->telefoon,
            'datum' => $this->datum,
            'tijd' => $this->tijd,
            'gasten' => $this->gasten,
            'opmerkingen' => $this->opmerkingen,
            'plaats' => $this->plaats,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivityType(): string
    {
        return $this->activityType;
    }

    public function getNaam(): string
    {
        return $this->naam;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelefoon(): string
    {
        return $this->telefoon;
    }

    public function getDatum(): string
    {
        return $this->datum;
    }

    public function getTijd(): string
    {
        return $this->tijd;
    }

    public function getGasten(): int
    {
        return $this->gasten;
    }

    public function getOpmerkingen(): string
    {
        return $this->opmerkingen;
    }

    public function getPlaats(): string
    {
        return $this->plaats;
    }

    public function isBinnen(): bool
    {
        return $this->activityType === 'binnen';
    }

    public function isBuiten(): bool
    {
        return $this->activityType === 'buiten';
    }

    public function canBeBookedFromTomorrow(): bool
    {
        $gekozenDatum = strtotime($this->datum);
        $morgen = strtotime('tomorrow');

        return $gekozenDatum !== false && $gekozenDatum >= $morgen;
    }
}
