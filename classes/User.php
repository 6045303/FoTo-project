<?php

class User
{
    public int $id;
    public string $username;
    public string $role; // admin | user | guest

    public function __construct($id = 0, $username = "Gast", $role = "guest")
    {
        $this->id = $id;
        $this->username = $username;
        $this->role = $role;
    }

    public function isAdmin(): bool
    {
        return $this->role === "admin";
    }

    public function isUser(): bool
    {
        return $this->role === "user";
    }

    public function isGuest(): bool
    {
        return $this->role === "guest";
    }
}