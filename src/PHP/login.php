<?php
// login.php - expects init.php already included
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Vul alle velden in.';
    } else {
        try {
            $db = get_db();
            $stmt = $db->prepare("SELECT id, first_name, role, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'];
                $_SESSION['user_role'] = $user['role'];
                header('Location: index.php?page=home');
                exit;
            } else {
                $error = 'E-mailadres of wachtwoord is onjuist.';
            }
        } catch (PDOException $e) {
            $error = 'Er is een fout opgetreden.';
        }
    }
}
?>

<div class="w-full max-w-sm shadow-lg rounded-lg p-6" style="background:var(--textWhite);">
    <h3 class="text-xl font-semibold mb-2" style="color:var(--deepblack);">Mijn account</h3>
    <h2 class="text-2xl font-bold mb-4" style="color:var(--textblack);">Welkom terug! Log nu in</h2>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-4">
            <input id="email" name="email" type="email" required placeholder="E-mailadres" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none" />
        </div>

        <div class="mb-3 relative">
            <input id="login_password" name="password" type="password" required placeholder="Wachtwoord" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none" />
            <button type="button" data-target="#login_password" class="toggle-password absolute right-3 top-3 text-gray-500">👁️</button>
        </div>

        <div class="mb-4 text-sm">
            <a href="#" class="text-gray-600">Wachtwoord vergeten?</a>
        </div>

        <button type="submit" class="w-full py-3 rounded-lg font-semibold mb-4" style="background:var(--deepblack); color:var(--textWhite);">Inloggen</button>
    </form>

    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
        <p class="mb-3 font-medium">Heb je nog geen online account?</p>
        <a href="index.php?page=register" class="inline-block w-full border rounded-lg py-3 font-semibold" style="border-color:var(--deepblack); color:var(--deepblack);">Nu registreren</a>
    </div>
</div>
