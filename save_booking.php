<?php
require_once __DIR__ . '/db.php';

// helper to check if a table exists
function table_exists($mysqli, $table){
    $t = $mysqli->real_escape_string($table);
    $res = $mysqli->query("SHOW TABLES LIKE '".$t."'");
    if (! $res) return false;
    $exists = $res->num_rows > 0;
    $res->free();
    return $exists;
}

// Read POST fields with basic sanitization
$activity_type = isset($_POST['activity_type']) ? trim($_POST['activity_type']) : 'binnen';
$naam = isset($_POST['naam']) ? trim($_POST['naam']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$telefoon = isset($_POST['telefoon']) ? trim($_POST['telefoon']) : null;
$datum = isset($_POST['datum']) ? trim($_POST['datum']) : null;
$tijd = isset($_POST['tijd']) ? trim($_POST['tijd']) : null;
$gasten = isset($_POST['gasten']) ? (int)$_POST['gasten'] : 1;
$locatie = isset($_POST['locatie']) ? trim($_POST['locatie']) : null;
$overdekt = isset($_POST['overdekt']) ? 1 : 0;
$opmerkingen = isset($_POST['opmerkingen']) ? trim($_POST['opmerkingen']) : null;
$plaats = isset($_POST['plaats']) ? trim($_POST['plaats']) : null;

if ($naam === ''){
    header('Location: index.php');
    exit;
}

// If the legacy table `activiteiten` exists, insert there (map fields)
if (table_exists($mysqli, 'activiteiten')){
    $sql = "INSERT INTO activiteiten (naam, type, beschrijving, datum, tijd, plaats, aantal_gasten, aangemaakt_op) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $mysqli->prepare($sql);
    if ($stmt){
        $beschrijving = $opmerkingen ?: '';
        $aantal = $gasten ?: 1;
        $stmt->bind_param('sssssis', $naam, $activity_type, $beschrijving, $datum, $tijd, $plaats, $aantal);
        $stmt->execute();
        $stmt->close();
    }
} else {
    // fallback to our bookings table
    $stmt = $mysqli->prepare("INSERT INTO bookings (activity_type, naam, email, telefoon, datum, tijd, gasten, locatie, overdekt, opmerkingen, plaats) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt){
        $stmt->bind_param('ssssssissss', $activity_type, $naam, $email, $telefoon, $datum, $tijd, $gasten, $locatie, $overdekt, $opmerkingen, $plaats);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: index.php');
exit;

?>
