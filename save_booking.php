<?php
require_once __DIR__ . '/autoload.php';

$model = new ActivityModel();

// POST-waarden ophalen (veilig en met defaults)
$data = [
    'activity_type' => $_POST['activity_type'] ?? '',
    'naam'          => trim($_POST['naam'] ?? ''),
    'email'         => trim($_POST['email'] ?? ''),
    'telefoon'      => trim($_POST['telefoon'] ?? ''),
    'datum'         => $_POST['datum'] ?? '',
    'tijd'          => $_POST['tijd'] ?? '',
    'gasten'        => (int)($_POST['gasten'] ?? 1),
    'opmerkingen'   => trim($_POST['opmerkingen'] ?? ''),
    'plaats'        => $_POST['plaats'] ?? null
];

// Datumcontrole: datum moet minimaal morgen zijn
$gekozenDatum = strtotime($data['datum']);
$morgen = strtotime('tomorrow');

if (!$gekozenDatum || $gekozenDatum < $morgen) {
    header("Location: ../index.php?error=datum");
    exit;
}

// BEWERKEN (UPDATE)
if (!empty($_POST['id'])) {
    $model->update((int)$_POST['id'], $data);
}
// NIEUW (INSERT)
else {
    $model->create($data);
}

// Redirect terug naar overzicht
header("Location: ../index.php?success=1");
exit;