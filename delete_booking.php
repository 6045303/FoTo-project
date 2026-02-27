<?php
require_once __DIR__ . '/src/PHP/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

try {
    $bookingObj = new \App\Booking();
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($id > 0) {
        $bookingObj->delete($id);
    }
} catch (\Exception $e) {
    // Log error silently
}

header('Location: website.php?page=bookings-overview');
exit;

