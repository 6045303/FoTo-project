<?php

$pageTitle = $pageTitle ?? 'FoTo-project';
$headerTitle = $headerTitle ?? 'FoTo-project';
$bodyClass = $bodyClass ?? 'min-h-screen flex flex-col';
$bodyStyle = $bodyStyle ?? 'background-color:#D3B69C; color:#111111;';

if (!isset($user) && isset($auth) && $auth instanceof Auth) {
    $user = $auth->user();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="index.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="<?= htmlspecialchars($bodyClass); ?>" style="<?= htmlspecialchars($bodyStyle); ?>">
<header class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold"><?= htmlspecialchars($headerTitle); ?></h1>
        <nav>
            <ul class="flex gap-4 text-sm">
                <li><a href="index.php" class="text-white">Overzicht</a></li>
                <?php if (isset($user) && !$user->isGuest()): ?>
                    <li><a href="mijn_activiteiten.php" class="text-white">Mijn activiteiten</a></li>
                    <li>
                        <form action="logout.php" method="post">
                            <button class="text-white underline">Uitloggen</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li><a href="login.php" class="text-white">Inloggen</a></li>
                    <li><a href="registeer.php" class="text-white">Registreren</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
