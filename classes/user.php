<?php

class User
{
    private int $id;
    private string $username;
    private string $email;
    private string $password; // hashed password

    // Constructor is private zodat je altijd via static constructors werkt
    private function __construct(int $id, string $username, string $email, string $password)
    {
        $this->id       = $id;
        $this->username = $username;
        $this->email    = $email;
        $this->password = $password;
    }

    // -------------------------
    // ⭐ Static constructors
    // -------------------------

    // Maak een user vanuit database-rij
    public static function fromDatabase(array $row): User
    {
        return new User(
            $row['id'],
            $row['username'],
            $row['email'],
            $row['password']
        );
    }

    // Maak een nieuwe user (bij registratie)
    public static function create(string $username, string $email, string $password): User
    {
        return new User(
            0, // wordt later door database ingevuld
            $username,
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        );
    }

    // -------------------------
    // ⭐ Getters
    // -------------------------

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->password;
    }

    // -------------------------
    // ⭐ Domain methods (gedrag)
    // -------------------------

    // Check of wachtwoord klopt
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    // Verander wachtwoord
    public function changePassword(string $newPassword): void
    {
        $this->password = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    // Update email
    public function updateEmail(string $newEmail): void
    {
        $this->email = $newEmail;
    }

    // Update username
    public function updateUsername(string $newUsername): void
    {
        $this->username = $newUsername;
    }
}