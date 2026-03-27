<?php
require_once 'autoload.php';
session_start();

$error = "";

// Als het formulier is verstuurd
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // User object
    $user = new User();

    // Probeer in te loggen
    if ($user->login($username, $password)) {

        // Login succesvol → redirect
        header("Location: dashboard.php");
        exit;

    } else {
        $error = "Onjuiste gebruikersnaam of wachtwoord";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Inloggen</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-xl rounded-lg p-8 w-full max-w-sm">

        <?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
            <div class="bg-green-200 border border-green-600 text-black p-3 rounded mb-4 text-center">
                ✔ Je account is aangemaakt. Je kunt nu inloggen.
            </div>
        <?php endif; ?>

        <h2 class="text-2xl font-bold text-center mb-6 text-[#0B0B45]">
            Inloggen
        </h2>

        <?php if (!empty($error)): ?>
            <div class="bg-red-200 border border-red-600 text-black p-3 rounded mb-4 text-center">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">

            <div>
                <label class="block mb-1 font-medium">Gebruikersnaam</label>
                <input type="text" name="username" required
                       class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#0B0B45]">
            </div>

            <div>
                <label class="block mb-1 font-medium">Wachtwoord</label>
                <input type="password" name="password" required
                       class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#0B0B45]">
            </div>

            <button class="primary-btn w-full mt-4">
                Inloggen
            </button>

            <p class="text-center mt-4">
                Nog geen account?
                <a href="registeer.php" class="text-[#0B0B45] font-semibold">Registreren</a>
            </p>

        </form>

    </div>

</body>
</html>