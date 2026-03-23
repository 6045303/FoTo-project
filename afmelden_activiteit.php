<?php

require_once __DIR__ . '/autoload.php';

$auth = new Auth();
$auth->requireUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activiteitId = (int) ($_POST['activiteit_id'] ?? 0);
    $gebruikerId = $auth->user()->getId();

    if ($activiteitId > 0 && $gebruikerId !== null) {
        $service = new ParticipationService(new Deelnemers());
        $service->afmelden($gebruikerId, $activiteitId);
    }
}

header('Location: mijn_activiteiten.php');
exit;
