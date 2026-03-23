<?php

require_once __DIR__ . '/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $bookingService = new BookingService(new ActivityModel());
    $bookingService->deleteById($id);
}

header('Location: index.php');
exit;
