<?php

require_once __DIR__ . '/autoload.php';

$auth = new Auth();
$auth->requireUser();

$gebruiker = $auth->user();
$gebruikerId = $gebruiker->getId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activiteitId = (int) ($_POST['activiteit_id'] ?? 0);

    if ($activiteitId > 0 && $gebruikerId !== null) {
        $service = new ParticipationService(new Deelnemers());
        $service->aanmelden($gebruikerId, $activiteitId);
    }
}

header('Location: mijn_activiteiten.php');
exit;
