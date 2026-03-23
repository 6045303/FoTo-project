<?php

class User
{
    public const ROLE_ADMIN = "admin";
    public const ROLE_USER  = "user";
    public const ROLE_GUEST = "guest";

    private int $id;
    private string $username;
    private string $role;

    // Constructor is privé zodat je altijd via static methods werkt
    private function __construct(int $id, string $username, string $role)
    {
        $this->id = $id;
        $this->username = $username;
        $this->role = $role;
    }

    // Static constructors
    public static function guest(): User
    {
        return new User(0, "Gast", self::ROLE_GUEST);
    }

    public static function admin(int $id, string $username): User
    {
        return new User($id, $username, self::ROLE_ADMIN);
    }

    public static function normal(int $id, string $username): User
    {
        return new User($id, $username, self::ROLE_USER);
    }

    // Wordt gebruikt als er per ongeluk een array in de sessie staat
    public static function fromDatabase(int $id, string $username, string $role): User
    {
        return new User($id, $username, $role);
    }

    // Getters
    public function getId(): int
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

    // Role checks
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isGuest(): bool
    {
        return $this->role === self::ROLE_GUEST;
    }
}