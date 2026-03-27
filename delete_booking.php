<?php
require_once __DIR__ . '/classes/autoload.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
};

// Alleen POST toegestaan
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// User laden
$user = new User();
$user->loadById($_SESSION['user_id']);

// ID ophalen
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id > 0) {

    // Stap 1: tijdelijk object om type te bepalen
    $temp = new BinnenActiviteit(); // maakt niet uit welke, loadById werkt toch

    if ($temp->loadById($id)) {

        // Stap 2: juiste subclass kiezen
        if ($temp->activity_type === "binnen") {
            $model = new BinnenActiviteit();
        } else {
            $model = new BuitenActiviteit();
        }

        // Activiteit opnieuw laden in juiste subclass
        $model->loadById($id);

        // Stap 3: rechten controleren
        if ($user->isAdmin() || $model->email === $user->email) {

            $model->delete();

        } else {
            die("Je hebt geen rechten om deze activiteit te verwijderen.");
        }
    }
}

// Terug naar dashboard
header('Location: dashboard.php');
exit;