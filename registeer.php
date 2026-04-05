<?php
require_once 'autoload.php';
session_start();

$error = "";

// Als het formulier is verstuurd
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm'] ?? '');

    if ($password !== $confirm) {
        $error = "Wachtwoorden komen niet overeen";

    } 
    // 👇 HIER komt jouw nieuwe check
    elseif (!preg_match('/[A-Z].*[A-Z]/', $password)) {
        $error = "Wachtwoord moet minstens 2 hoofdletters bevatten";

    } elseif (!preg_match('/[\W]/', $password)) {
        $error = "Wachtwoord moet minstens 1 leesteken bevatten";

    }
    // database check + insert
    else {

        // Check of username of email al bestaat
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
        $stmt->bindParam(':u', $username);
        $stmt->bindParam(':e', $email);
        $stmt->execute();

        if ($stmt->fetch()) {
            $error = "Gebruikersnaam of email bestaat al";

        } else {
            $role = 'user';

            if ($email === "admin@foto.nl") {
                $role = 'admin';
                }
            $stmt = $db->prepare("
                INSERT INTO users (username, email, password, role)
                VALUES (:u, :e, :p, :r)
            ");

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt->bindParam(':u', $username);
            $stmt->bindParam(':e', $email);
            $stmt->bindParam(':p', $hash);
            $stmt->bindParam(':r', $role);

            if ($stmt->execute()) {
                header("Location: login.php?success=registered");
                exit;
            } else {
                $error = "Er ging iets mis bij het registreren";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registreren</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#D3B69C] shadow rounded p-6 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-xl rounded-lg p-8 w-full max-w-sm">

        <h2 class="text-2xl font-bold text-center mb-6 text-[#0B0B45]">
            Registreren
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
                <label class="block mb-1 font-medium">Email</label>
                <input type="email" name="email" required
                       class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#0B0B45]">
            </div>

            <div>
                <label class="block mb-1 font-medium">Wachtwoord</label>
                <input type="password" name="password" required
                       class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#0B0B45]">
            </div>

            <div>
                <label class="block mb-1 font-medium">Herhaal wachtwoord</label>
                <input type="password" name="confirm" required
                       class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#0B0B45]">
            </div>

            <button class="primary-btn w-full mt-4">
                Registreren
            </button>

        </form>

        <p class="text-center mt-4">
            Al een account?
            <a href="login.php" class="text-[#0B0B45] font-semibold">Inloggen</a>
        </p>

    </div>

</body>
</html>