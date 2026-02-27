<?php
// Minimal DB connection helper — edit credentials as needed
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'foto_project';

$mysqli = @new mysqli($db_host, $db_user, $db_pass);
if ($mysqli->connect_errno) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Kon geen verbinding maken met database: '.$mysqli->connect_error]);
    exit;
}

// Ensure database exists
$create_db_sql = "CREATE DATABASE IF NOT EXISTS `".$mysqli->real_escape_string($db_name)."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (! $mysqli->query($create_db_sql)){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Kon database niet maken: '.$mysqli->error]);
    exit;
}

// Select database
if (! $mysqli->select_db($db_name)){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Kon database niet selecteren: '.$mysqli->error]);
    exit;
}

// ensure UTF-8
$mysqli->set_charset('utf8mb4');

// Create bookings table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_type VARCHAR(20) NOT NULL,
    naam VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT NULL,
    telefoon VARCHAR(50) DEFAULT NULL,
    datum DATE DEFAULT NULL,
    tijd TIME DEFAULT NULL,
    gasten INT DEFAULT 1,
    locatie VARCHAR(100) DEFAULT NULL,
    overdekt TINYINT(1) DEFAULT 0,
    opmerkingen TEXT DEFAULT NULL,
    plaats VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

if (! $mysqli->query($create_table)){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Kon tabel niet aanmaken: '.$mysqli->error]);
    exit;
}

// ready: $mysqli available for includes
?>
