<?php
require_once __DIR__ . '/ActivityModel.php';

$model = new ActivityModel();

// POST-waarden ophalen
$data = [
    'activity_type' => $_POST['activity_type'] ?? '',
    'naam'          => $_POST['naam'] ?? '',
    'email'         => $_POST['email'] ?? '',
    'telefoon'      => $_POST['telefoon'] ?? '',
    'datum'         => $_POST['datum'] ?? '',
    'tijd'          => $_POST['tijd'] ?? '',
    'gasten'        => $_POST['gasten'] ?? 1,
    'opmerkingen'   => $_POST['opmerkingen'] ?? '',
    'plaats'        => $_POST['plaats'] ?? null
];

// BEWERKEN (UPDATE)
if (!empty($_POST['id'])) {
    $model->update((int)$_POST['id'], $data);
}
// Datum mag niet vóór morgen zijn
$gekozenDatum = strtotime($data['datum']);
$morgen = strtotime('tomorrow');

if ($gekozenDatum < $morgen) {
    header("Location: ../index.php?error=datum");
    exit;
}
// NIEUW (INSERT)
else {
    $model->create($data);
}

// Redirect terug naar overzicht
header("Location: ../index.php");
exit;