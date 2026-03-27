<?php
require_once 'autoload.php';
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=login_required");
    exit;
}

// User laden
$user = new User();
$user->loadById($_SESSION['user_id']);

$db = Database::getInstance();

// Admin ziet ALLES
if ($user->isAdmin()) {
    $stmt = $db->prepare("SELECT * FROM bookings ORDER BY datum ASC");
    $stmt->execute();
    $activiteiten = $stmt->fetchAll();
} else {
    // User ziet alleen eigen activiteiten
    $stmt = $db->prepare("SELECT * FROM bookings WHERE email = :email ORDER BY datum ASC");
    $stmt->bindParam(':email', $user->email);
    $stmt->execute();
    $activiteiten = $stmt->fetchAll();
}

// Functie om juiste subclass te maken
function loadActiviteitSubclass(array $row): Activiteit {
    if ($row['activity_type'] === 'binnen') {
        $obj = new BinnenActiviteit();
    } else {
        $obj = new BuitenActiviteit();
    }
    $obj->loadById((int)$row['id']);
    return $obj;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-5xl mx-auto mt-10">

    <h1 class="text-3xl font-bold mb-4">
        Welkom, <?= htmlspecialchars($user->username) ?>
    </h1>

    <?php if ($user->isAdmin()): ?>
        <p class="text-red-600 font-semibold mb-4">Je bent ingelogd als ADMIN</p>
    <?php endif; ?>

    <div class="flex gap-4 mb-6">
        <a href="BinnenActiviteit.php" class="px-4 py-2 bg-blue-600 text-white rounded shadow">Binnenactiviteit Aanmaken</a>
        <a href="BuitenActiviteit.php" class="px-4 py-2 bg-green-600 text-white rounded shadow">Buitenactiviteit Aanmaken</a>
        <a href="index.php" class="px-4 py-2 bg-orange-700 text-white rounded shadow">Overzicht</a>
       
        <a href="logout.php" class="px-4 py-2 bg-gray-700 text-white rounded shadow">Uitloggen</a>
    </div>

    <div class="bg-white shadow rounded p-6">
        <h2 class="text-2xl font-semibold mb-4">Jouw activiteiten</h2>

        <?php if (empty($activiteiten)): ?>
            <p class="text-gray-600">Je hebt nog geen activiteiten geboekt.</p>
        <?php else: ?>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-3">Type</th>
                        <th class="p-3">Datum</th>
                        <th class="p-3">Tijd</th>
                        <th class="p-3">Locatie</th>
                        <th class="p-3">Gasten</th>
                        <th class="p-3">Acties</th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($activiteiten as $a): ?>
                    <tr class="border-b">
                        <td class="p-3"><?= htmlspecialchars($a['activity_type']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($a['datum']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($a['tijd']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($a['locatie']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($a['gasten']) ?></td>

                        <td class="p-3 flex gap-2">

                            <!-- Uitnodigen (mag altijd) -->
                            <a href="uitnodigen.php?id=<?= $a['id'] ?>"
                               class="px-3 py-1 bg-purple-600 text-white rounded">
                                Uitnodigen
                            </a>

                            <?php if ($user->isAdmin()): ?>
                                <!-- Admin CRUD -->
                                <a href="BinnenActiviteit.php?id=<?= $a['id'] ?>"
                                   class="px-3 py-1 bg-yellow-500 text-white rounded">
                                    Bewerken
                                </a>

                                <a href="delete_booking.php?id=<?= $a['id'] ?>"
                                   onclick="return confirm('Weet je het zeker?')"
                                   class="px-3 py-1 bg-red-600 text-white rounded">
                                    Verwijderen
                                </a>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>

        <?php endif; ?>
    </div>

</div>

</body>
</html>