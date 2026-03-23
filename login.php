<?php

require_once __DIR__ . '/autoload.php';

$auth = new Auth();
$error = '';
$successMessage = '';

if (isset($_GET['success']) && $_GET['success'] === 'registered') {
    $successMessage = 'Je account is aangemaakt. Je kunt nu inloggen.';
}

if (isset($_GET['error']) && $_GET['error'] === 'login_required') {
    $error = 'Log eerst in om verder te gaan.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($auth->login($username, $password)) {
        header('Location: mijn_activiteiten.php');
        exit;
    }

    $error = 'Onjuiste gebruikersnaam of wachtwoord.';
}

$user = $auth->user();
$pageTitle = 'Inloggen';
$headerTitle = 'Inloggen';
$bodyClass = 'min-h-screen flex flex-col bg-gray-100';
$bodyStyle = '';
$footerText = 'FoTo-project - Inloggen';

require __DIR__ . '/includes/header.php';
?>
<main class="flex-1 flex items-center justify-center px-4 py-10">
    <div class="bg-white shadow-xl rounded-lg p-8 w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center mb-6 text-[#0B0B45]">Inloggen</h2>

        <?php if ($successMessage !== ''): ?>
            <div class="bg-green-200 border border-green-600 text-black p-3 rounded mb-4 text-center">
                <?= htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="bg-red-200 border border-red-600 text-black p-3 rounded mb-4 text-center">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">Gebruikersnaam</label>
                <input type="text" name="username" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#0B0B45]">
            </div>

            <div>
                <label class="block mb-1 font-medium">Wachtwoord</label>
                <input type="password" name="password" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#0B0B45]">
            </div>

            <button class="primary-btn w-full mt-4 py-2 rounded">Inloggen</button>

            <p class="text-center mt-4">
                Nog geen account? <a href="registeer.php" class="text-[#0B0B45] font-semibold">Registreren</a>
            </p>
        </form>
    </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
