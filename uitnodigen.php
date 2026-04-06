<?php
require_once 'autoload.php';
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user = new User();
$user->loadById($_SESSION['user_id']);

$db = Database::getInstance();

// Haal JE EIGEN activiteiten op zodat je anderen kunt uitnodigen
$stmt = $db->prepare("SELECT * FROM bookings WHERE email = :email ORDER BY datum ASC");
$stmt->bindParam(':email', $user->email);
$stmt->execute();
$activiteiten = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Uitnodigen voor activiteiten</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="index.css">
</head>
<body class="bg-[#D3B69C] min-h-screen">

<header class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">Uitnodigen</h1>
        <nav>
            <ul class="flex gap-4 text-sm">
                <li><a href="dashboard.php" class="text-white">Dashboard</a></li>
                <li><a href="index.php" class="text-white">Overzicht</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="max-w-4xl mx-auto px-4 py-12">
    <section class="rounded-lg p-6 shadow-md" style="background-color:#Faebd7;">
        <h2 class="text-2xl font-bold mb-6">Deel je activiteiten met anderen</h2>

        <?php if (empty($activiteiten)): ?>
            <p class="text-gray-600">Je hebt nog geen activiteiten aangemaakt. <a href="BinnenActiviteit.php" class="text-blue-600 font-semibold">Maak er nu eentje aan</a></p>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-4">
                <?php foreach ($activiteiten as $a): ?>
                    <div class="bg-white p-4 rounded border border-gray-300">
                        <h3 class="font-bold text-lg"><?= htmlspecialchars($a['naam']) ?></h3>
                        <p class="text-sm text-gray-600">
                            <strong><?= htmlspecialchars($a['activity_type']) ?></strong> - 
                            <?= htmlspecialchars($a['datum']) ?> om <?= htmlspecialchars($a['tijd']) ?>
                        </p>
                        <p class="text-sm mb-3">Locatie: <?= htmlspecialchars($a['locatie']) ?></p>
                        
                        <!-- Invite button (dit wordt gevuld door JS) -->
                        <div id="invite-container-<?= $a['id'] ?>"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </section>
</main>

<script type="module">
    import InviteManager from './js/InviteManager.js';

    document.addEventListener('DOMContentLoaded', () => {
        const activiteiten = <?= json_encode($activiteiten) ?>;

        activiteiten.forEach(activiteit => {
            const manager = new InviteManager(activiteit.id, activiteit.naam);
            const container = document.getElementById(`invite-container-${activiteit.id}`);
            if (container) {
                container.appendChild(manager.container);
            }
        });
    });
</script>

</body>
</html>
