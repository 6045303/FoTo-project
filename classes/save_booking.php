<?php

require_once __DIR__ . '/../autoload.php';

$bookingService = new BookingService(new ActivityModel());

if (!$bookingService->saveFromPost($_POST)) {
    header('Location: ../index.php?error=datum');
    exit;
}

header('Location: ../index.php');
exit;
