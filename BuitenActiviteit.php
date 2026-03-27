<?php
require_once 'autoload.php';
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// User laden
$user = new User();
$user->loadById($_SESSION['user_id']);

// Activiteit model (BELANGRIJK!)
$model = new BuitenActiviteit();

$edit = null;
$isEditing = false;

// Check of we een activiteit bewerken
if (!empty($_GET['id'])) {

    if ($model->loadById((int)$_GET['id'])) {
        $edit = $model->getData();
        $isEditing = true;

        // User mag alleen eigen activiteiten bewerken, admin mag alles
        if (!$user->isAdmin() && $edit['email'] !== $user->email) {
            die("Je hebt geen rechten om deze activiteit te bewerken.");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $isEditing ? 'Bewerk buiten activiteit' : 'Boek buiten activiteit' ?></title>
    <link rel="stylesheet" href="index.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex flex-col" style="background-color:#D3B69C; color:#111111;">

<header class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">
            Activiteiten — <?= $isEditing ? 'Bewerk buiten activiteit' : 'Boek buiten activiteit' ?>
        </h1>
        <nav>
            <ul class="flex gap-4 text-sm">
                <li><a href="index.php" class="text-white">Overzicht</a></li>
                <li><a href="dashboard.php" class="text-white">Mijn activiteiten</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="flex-1 w-full">
    <div class="max-w-4xl mx-auto px-4 py-12">

        <section class="rounded-lg p-6 shadow-md" style="background-color:#Faebd7;">
            <h2 class="text-2xl font-bold mb-4">
                <?= $isEditing ? 'Bewerk deze buiten activiteit' : 'Boek een buiten activiteit' ?>
            </h2>

            <form action="save_booking.php" method="post">

                <?php if ($isEditing): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($edit['id']) ?>">
                <?php endif; ?>

                <input type="hidden" name="activity_type" value="buiten">

                <div>
                    <label class="block text-sm font-medium mb-1">Volledige naam *</label>
                    <input name="naam" required class="w-full p-3 rounded border"
                           type="text"
                           value="<?= htmlspecialchars($edit['naam'] ?? $user->username) ?>">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">E-mail *</label>
                        <input name="email" required class="w-full p-3 rounded border bg-gray-200"
                               type="email" readonly
                               value="<?= htmlspecialchars($user->email) ?>">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Telefoon</label>
                        <input name="telefoon" class="w-full p-3 rounded border"
                               type="tel"
                               value="<?= htmlspecialchars($edit['telefoon'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Datum *</label>
                        <input name="datum" required class="w-full p-3 rounded border"
                               type="date"
                               value="<?= htmlspecialchars($edit['datum'] ?? '') ?>">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Tijd *</label>
                        <input name="tijd" required class="w-full p-3 rounded border"
                               type="time"
                               value="<?= htmlspecialchars($edit['tijd'] ?? '') ?>">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Aantal gasten</label>
                        <input name="gasten" class="w-full p-3 rounded border"
                               type="number" min="1"
                               value="<?= htmlspecialchars($edit['gasten'] ?? 1) ?>">
                    </div>
                </div>

                <!-- WEER API -->
                <div class="weatherForm mt-6">
                    <label class="block text-sm font-medium mb-1">Weer opvragen</label>

                    <div class="flex gap-3">
                        <input type="text" id="plaats" name="plaats"
                               class="cityInput w-full p-3 rounded border"
                               value="<?= htmlspecialchars($edit['plaats'] ?? '') ?>"
                               placeholder="Voer een plaats in">

                        <button type="button" id="weather-button"
                                class="px-4 py-2 rounded font-medium primary-btn">
                            Weer opvragen
                        </button>
                    </div>

                    <div id="weather-result"
                         class="hidden flex flex-col gap-3 p-5 rounded-xl shadow-lg 
                                bg-gradient-to-b from-[#0B0B45] to-[#Faebd7] 
                                text-white font-sans mt-4 mx-auto max-w-md text-center">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Opmerkingen</label>
                    <textarea name="opmerkingen" class="w-full p-3 rounded border"
                              rows="4"><?= htmlspecialchars($edit['opmerkingen'] ?? '') ?></textarea>
                </div>

                <div class="flex gap-3 mt-2">
                    <button type="submit" class="px-6 py-3 rounded font-medium primary-btn">
                        <?= $isEditing ? 'Opslaan' : 'Aanmaken' ?>
                    </button>

                    <a href="index.php" class="px-6 py-3 rounded font-medium secondary-btn inline-flex items-center justify-center">
                        Annuleren
                    </a>

                    <a href="BinnenActiviteit.php" class="ml-auto text-sm underline">
                        Naar binnen activiteit
                    </a>
                </div>

            </form>

        </section>
    </div>
</main>

<script src="js/Weer.js"></script>

<?php if ($isEditing && !empty($edit['plaats'])): ?>
<script>
    window.addEventListener("DOMContentLoaded", () => {
        document.getElementById("weather-button").click();
    });
</script>
<?php endif; ?>

<footer class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
    <div class="max-w-4xl mx-auto px-4 py-4 text-sm text-center">
        &copy; <?= date('Y'); ?> FoTo-project — Boek buiten
    </div>
</footer>

<script type="module" src="/js/form.js"></script>
</body>
</html>