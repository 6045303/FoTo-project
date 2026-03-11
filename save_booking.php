<?php
require_once __DIR__ . '/src/PHP/init.php';
require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $bookingObj = new \App\Booking();

    // Read POST fields with basic sanitization
    $data = [
        'activity_type' => isset($_POST['activity_type']) ? trim($_POST['activity_type']) : 'binnen',
        'naam' => isset($_POST['naam']) ? trim($_POST['naam']) : '',
        'email' => isset($_POST['email']) ? trim($_POST['email']) : null,
        'telefoon' => isset($_POST['telefoon']) ? trim($_POST['telefoon']) : null,
        'datum' => isset($_POST['datum']) ? trim($_POST['datum']) : null,
        'tijd' => isset($_POST['tijd']) ? trim($_POST['tijd']) : null,
        'gasten' => isset($_POST['gasten']) ? (int)$_POST['gasten'] : 1,
        'locatie' => isset($_POST['locatie']) ? trim($_POST['locatie']) : null,
        'overdekt' => isset($_POST['overdekt']) ? 1 : 0,
        'opmerkingen' => isset($_POST['opmerkingen']) ? trim($_POST['opmerkingen']) : null
    ];

    if ($data['naam'] === '') {
        throw new \Exception('Naam is verplicht.');
    }

    $bookingObj->create($data);
    header('Location: website.php?page=bookings-overview');
} catch (\Exception $e) {
    header('Location: website.php?page=bookings-overview&error=' . urlencode($e->getMessage()));
}

