<?php

class User
{
    private ?int $id;
    private string $username;
    private string $role;

    public function __construct(?int $id = null, string $username = 'Gast', string $role = 'guest')
    {
        $this->id = $id;
        $this->username = $username;
        $this->role = $role;
    }

    public static function guest(): self
    {
        return new self();
    }

    public static function fromSession(array $user): self
    {
        return new self(
            isset($user['id']) ? (int) $user['id'] : null,
            (string) ($user['username'] ?? 'Gast'),
            (string) ($user['role'] ?? 'guest')
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function isGuest(): bool
    {
        return $this->role === 'guest';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
