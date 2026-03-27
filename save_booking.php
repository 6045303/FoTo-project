<?php
require_once __DIR__ . '/autoload.php';
session_start();

// Alleen POST toegestaan
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// User laden
$user = new User();
$user->loadById($_SESSION['user_id']);

// Activiteit-type bepalen
$type = $_POST['activity_type'] ?? '';
if ($type === 'binnen') {
    $model = new BinnenActiviteit();
} elseif ($type === 'buiten') {
    $model = new BuitenActiviteit();
} else {
    die("Ongeldig activiteitstype.");
}

// POST-waarden invullen
$model->naam        = trim($_POST['naam'] ?? '');
$model->email       = $user->email; // altijd ingelogde user
$model->telefoon    = trim($_POST['telefoon'] ?? '');
$model->datum       = $_POST['datum'] ?? '';
$model->tijd        = $_POST['tijd'] ?? '';
$model->gasten      = (int)($_POST['gasten'] ?? 1);
$model->opmerkingen = trim($_POST['opmerkingen'] ?? '');
$model->plaats      = $_POST['plaats'] ?? null;
$model->locatie     = $_POST['locatie'] ?? '';

// Datumcontrole: datum moet minimaal morgen zijn
$gekozenDatum = strtotime($model->datum);
$morgen = strtotime('tomorrow');

if (!$gekozenDatum || $gekozenDatum < $morgen) {
    header("Location: /index.php?error=datum");
    exit;
}

// BEWERKEN (UPDATE)
if (!empty($_POST['id'])) {

    // Activiteit opnieuw laden in juiste subclass
    if ($model->loadById((int)$_POST['id'])) {

        // User mag alleen eigen activiteiten bewerken, admin mag alles
        if (!$user->isAdmin() && $model->email !== $user->email) {
            die("Je hebt geen rechten om deze activiteit te bewerken.");
        }

        // Nieuwe waarden invullen
        $model->naam        = trim($_POST['naam']);
        $model->telefoon    = trim($_POST['telefoon']);
        $model->datum       = $_POST['datum'];
        $model->tijd        = $_POST['tijd'];
        $model->gasten      = (int)$_POST['gasten'];
        $model->opmerkingen = trim($_POST['opmerkingen']);
        $model->plaats      = $_POST['plaats'] ?? null;

        $model->save();
    }

}
// NIEUW (INSERT)
else {
    $model->save();
}

// Redirect terug naar dashboard
header("Location: ../dashboard.php?success=1");
exit;