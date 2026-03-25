<?php

class User
{
    private PDO $db;

    public int $id;
    public string $username;
    public string $email;
    public string $role;          
    private string $passwordHash;

    public function __construct()
    {
        // Database Singleton
        $this->db = Database::getInstance();
    }

    //  Login functie
    public function login(string $username, string $password): bool
    {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data && password_verify($password, $data['password'])) {

            // Properties vullen
            $this->id = (int)$data['id'];
            $this->username = $data['username'];
            $this->email = $data['email'];
            $this->passwordHash = $data['password'];
            $this->role = $data['role'];   // ⭐ Nieuw toegevoegd

            // Session starten
            $_SESSION['user_id'] = $this->id;
            session_regenerate_id(true);

            return true;
        }

        return false;
    }

    // Haal user op via ID (bijv. uit session)
    public function loadById(int $id): bool
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id = (int)$data['id'];
            $this->username = $data['username'];
            $this->email = $data['email'];
            $this->passwordHash = $data['password'];
            $this->role = $data['role'];  
            return true;
        }

        return false;
    }

    //  Toon user data
    public function getData(): array
    {
        return [
            'id'       => $this->id,
            'username' => $this->username,
            'email'    => $this->email,
            'role'     => $this->role      
        ];
    }

   
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // 🚪 Logout
    public function logout(): void
    {
        session_unset();
        session_destroy();
    }
}