<?php
require "db.php";

$email = $_POST['email'];
$voornaam = $_POST['voornaam'];
$achternaam = $_POST['achternaam'];
$wachtwoord = $_POST['wachtwoord'];

/* Wachtwoord hashen (HEEL BELANGRIJK) */
$hash = password_hash($wachtwoord, PASSWORD_DEFAULT);

/* SQL query */
$sql = "INSERT INTO users (email, voornaam, achternaam, wachtwoord)
        VALUES (:email, :voornaam, :achternaam, :wachtwoord)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':email' => $email,
    ':voornaam' => $voornaam,
    ':achternaam' => $achternaam,
    ':wachtwoord' => $hash
]);

echo "Registratie gelukt!";
