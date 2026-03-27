<?php
require_once 'autoload.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
};

// Check login (optioneel)
$user = null;

if (isset($_SESSION['user_id'])) {
    $user = new User();
    $user->loadById($_SESSION['user_id']);
}

// Activiteit modellen
$binnenModel = new BinnenActiviteit();
$buitenModel = new BuitenActiviteit();

// Activiteiten ophalen
$binnen = $binnenModel->getAllByType('binnen') ?? [];
$buiten = $buitenModel->getAllByType('buiten') ?? [];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activiteiten - Overzicht</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex flex-col" style="background-color:#D3B69C; color:#111111;">

<header class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">Activiteiten</h1>

        <nav>
            <ul class="flex gap-4 text-sm">

                <li><a href="index.php" class="text-white">Overzicht</a></li>

                <?php if ($user): ?>
                    <li><a href="dashboard.php" class="text-white">Mijn activiteiten</a></li>

                    <li>
                        <form action="logout.php" method="post">
                            <button class="text-white underline">Uitloggen</button>
                        </form>
                    </li>

                <?php else: ?>
                    <li><a href="login.php" class="text-white">Inloggen</a></li>
                    <li><a href="register.php" class="text-white">Registreren</a></li>
                <?php endif; ?>

            </ul>
        </nav>
    </div>
</header>

<main class="flex-1 w-full">
    <div class="max-w-4xl mx-auto px-4 py-12">

        <section class="rounded-lg p-6 shadow-md" style="background-color:#Faebd7;">

            <div class="grid grid-cols-1 gap-6">

                <!-- ⭐ BINNEN ACTIVITEITEN -->
                <div class="rounded-lg overflow-hidden shadow">
                    <div class="p-4 card-header flex items-center justify-between">
                        <div class="text-lg font-semibold">Binnen activiteiten</div>
                        <div class="text-sm"><?= count($binnen); ?> items</div>
                    </div>

                    <div class="p-4 bg-white space-y-3">
                        <?php if (empty($binnen)): ?>
                            <div class="text-gray-600">Nog geen binnen activiteiten.</div>
                        <?php else: ?>
                            <?php foreach ($binnen as $b): ?>
                                <div class="p-3 rounded border shadow-sm flex items-start gap-3">

                                    <div class="flex-1">

                                        <div class="font-semibold text-base">
                                            <?= htmlspecialchars($b['opmerkingen']); ?>
                                        </div>

                                        <div class="text-xs text-gray-600">
                                            Type: <?= htmlspecialchars($b['activity_type']); ?>
                                        </div>

                                        <div class="text-xs text-gray-600">
                                            Geboekt door: <?= htmlspecialchars($b['naam']); ?>
                                        </div>

                                        <div class="text-xs text-gray-600 mt-1">
                                            <?= htmlspecialchars($b['datum']); ?> — <?= htmlspecialchars($b['tijd']); ?>
                                            <?php if (!empty($b['plaats'])): ?>
                                                · <?= htmlspecialchars($b['plaats']); ?>
                                            <?php endif; ?>
                                        </div>

                                    </div>

                                    <div class="flex flex-col items-end gap-2">

                                        <a href="BinnenActiviteit.php?id=<?= (int)$b['id']; ?>"
                                           class="px-3 py-1 rounded text-sm secondary-btn">
                                            Bekijken
                                        </a>

                                        <?php if ($user && $user->isAdmin()): ?>
                                            <form method="post" action="delete_booking.php"
                                                  onsubmit="return confirm('Weet je zeker dat je deze reservering wilt verwijderen?');">
                                                <input type="hidden" name="id" value="<?= (int)$b['id']; ?>">
                                                <button type="submit" class="px-3 py-1 rounded text-sm primary-btn">
                                                    Verwijder
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                    </div>

                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="p-4 bg-gray-50 text-right">
                        <?php if ($user): ?>
                            <a href="BinnenActiviteit.php" class="px-4 py-2 rounded secondary-btn">
                                Nieuwe binnen activiteit
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ⭐ BUITEN ACTIVITEITEN -->
                <div class="rounded-lg overflow-hidden shadow">
                    <div class="p-4 card-header flex items-center justify-between">
                        <div class="text-lg font-semibold">Buiten activiteiten</div>
                        <div class="text-sm"><?= count($buiten); ?> items</div>
                    </div>

                    <div class="p-4 bg-white space-y-3">
                        <?php if (empty($buiten)): ?>
                            <div class="text-gray-600">Nog geen buiten activiteiten.</div>
                        <?php else: ?>
                            <?php foreach ($buiten as $b): ?>
                                <div class="p-3 rounded border shadow-sm flex items-start gap-3">

                                    <div class="flex-1">

                                        <div class="font-semibold text-base">
                                            <?= htmlspecialchars($b['opmerkingen']); ?>
                                        </div>

                                        <div class="text-xs text-gray-600">
                                            Type: <?= htmlspecialchars($b['activity_type']); ?>
                                        </div>

                                        <div class="text-xs text-gray-600">
                                            Geboekt door: <?= htmlspecialchars($b['naam']); ?>
                                        </div>

                                        <div class="text-xs text-gray-600 mt-1">
                                            <?= htmlspecialchars($b['datum']); ?> — <?= htmlspecialchars($b['tijd']); ?>
                                            <?php if (!empty($b['plaats'])): ?>
                                                · <?= htmlspecialchars($b['plaats']); ?>
                                            <?php endif; ?>
                                        </div>

                                    </div>

                                    <div class="flex flex-col items-end gap-2">

                                        <a href="BuitenActiviteit.php?id=<?= (int)$b['id']; ?>"
                                           class="px-3 py-1 rounded text-sm secondary-btn">
                                            Bekijken
                                        </a>

                                        <?php if ($user && $user->isAdmin()): ?>
                                            <form method="post" action="delete_booking.php"
                                                  onsubmit="return confirm('Weet je zeker dat je deze reservering wilt verwijderen?');">
                                                <input type="hidden" name="id" value="<?= (int)$b['id']; ?>">
                                                <button type="submit" class="px-3 py-1 rounded text-sm primary-btn">
                                                    Verwijder
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                    </div>

                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="p-4 bg-gray-50 text-right">
                        <?php if ($user): ?>
                            <a href="BuitenActiviteit.php" class="px-4 py-2 rounded secondary-btn">
                                Nieuwe buiten activiteit
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </section>
    </div>
</main>

<footer class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
    <div class="max-w-4xl mx-auto px-4 py-4 text-sm text-center">
        &copy; <?= date('Y'); ?> FoTo-project — Activiteiten overzicht
    </div>
</footer>

</body>
</html>