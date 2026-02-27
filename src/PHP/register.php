<?php
// register.php — separate registration handling
// expects init.php already included
$error = '';
$success = '';
$token = $_GET['token'] ?? null;
$invitation = null;
$show_personal = false;

if ($token) {
    try {
        $db = get_db();
        $stmt = $db->prepare('SELECT * FROM invitations WHERE token = ? AND used = 0');
        $stmt->execute([$token]);
        $invitation = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($invitation) $show_personal = true; // skip email step when invited
    } catch (PDOException $e) {
        // ignore
    }
}

// If form submitted (personal step) do server-side registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['first_name'])) {
    $email = trim($_POST['email'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($email === '' || $first_name === '' || $last_name === '' || $password === '') {
        $error = 'Alle velden zijn verplicht.';
        $show_personal = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'E-mailadres is niet geldig.';
        $show_personal = true;
    } elseif ($password !== $password_confirm) {
        $error = 'Wachtwoorden komen niet overeen.';
        $show_personal = true;
    } elseif (strlen($password) < 8) {
        $error = 'Wachtwoord moet minimaal 8 karakters lang zijn.';
        $show_personal = true;
    } elseif (!preg_match('/[A-Z].*[A-Z]/', $password)) {
        $error = 'Wachtwoord moet minimaal 2 hoofdletters bevatten.';
        $show_personal = true;
    } else {
        try {
            $db = get_db();
            $role = 'klant';
            $inv_id = null;
            if (!empty($_POST['invite_token'])) {
                $t = $_POST['invite_token'];
                $stmt = $db->prepare('SELECT * FROM invitations WHERE token = ? AND used = 0');
                $stmt->execute([$t]);
                $inv = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($inv) {
                    $role = $inv['role'] ?: 'guest';
                    $inv_id = $inv['id'];
                }
            }

            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare('INSERT INTO users (email, first_name, last_name, password, role) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$email, $first_name, $last_name, $password_hash, $role]);

            if ($inv_id) {
                $u = $db->lastInsertId();
                $stmt = $db->prepare('UPDATE invitations SET used = 1 WHERE id = ?');
                $stmt->execute([$inv_id]);
            }

            $_SESSION['user_id'] = $db->lastInsertId();
            $_SESSION['user_name'] = $first_name;
            $_SESSION['user_role'] = $role;
            $success = 'Registratie geslaagd!';
            header('Location: index.php?page=home');
            exit;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                $error = 'Dit e-mailadres is al geregistreerd.';
            } else {
                $error = 'Er is een fout opgetreden.';
            }
            $show_personal = true;
        }
    }
}
?>

<div class="w-full max-w-md shadow-lg rounded-lg p-8" style="background:var(--textWhite);">
    <h2 class="text-3xl font-bold text-gray-800 mb-2">Nieuwe klant?</h2>
    <p class="text-gray-600 mb-6">Vul hieronder je e-mailadres in. Je gaat direct door naar persoonlijke gegevens.</p>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Step 1: Email -->
    <div id="step-email" class="<?php echo $show_personal ? 'hidden' : ''; ?>">
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">E-mailadres</label>
            <input id="email_step" type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg" placeholder="E-mailadres">
        </div>
        <div class="flex gap-2">
            <button id="btn-email-next" type="button" class="w-full py-3 rounded-lg font-semibold" style="background:var(--deepblack); color:var(--textWhite);">Verder</button>
        </div>
        <p class="text-center text-sm text-gray-600 mt-4">Heb je al een account? <a href="index.php?page=login" class="text-blue-600">Log in</a></p>
    </div>

    <!-- Step 2: Personal details (actual form) -->
    <form id="step-personal" method="POST" class="<?php echo $show_personal ? '' : 'hidden'; ?> mt-2">
        <input type="hidden" name="invite_token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">E-mailadres</label>
            <input id="email_final" name="email" type="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg" value="<?php echo htmlspecialchars($invitation['email'] ?? ($_POST['email'] ?? '')); ?>">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Voornaam</label>
            <input name="first_name" type="text" required class="w-full px-4 py-3 border border-gray-300 rounded-lg" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Achternaam</label>
            <input name="last_name" type="text" required class="w-full px-4 py-3 border border-gray-300 rounded-lg" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Wachtwoord</label>
            <input name="password" type="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            <small class="text-gray-500">Minimaal 8 tekens en 2 hoofdletters.</small>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 mb-2">Wachtwoord bevestigen</label>
            <input name="password_confirm" type="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
        </div>
        <button type="submit" class="w-full px-4 py-3 rounded-lg" style="background:var(--gold); color:var(--textWhite);">Registreren</button>
    </form>

</div>

<script>
// Simple step handling: from email -> personal
document.getElementById('btn-email-next').addEventListener('click', function(){
    const email = document.getElementById('email_step').value.trim();
    if (!email) { alert('Vul een geldig e-mailadres in.'); return; }
    // basic clientside validation
    const re = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
    if (!re.test(email)) { alert('Vul een geldig e-mailadres in.'); return; }
    document.getElementById('email_final').value = email;
    document.getElementById('step-email').classList.add('hidden');
    document.getElementById('step-personal').classList.remove('hidden');
});

// If server-side had error and showed personal step, focus first input
if (!document.getElementById('step-personal').classList.contains('hidden')) {
    const el = document.querySelector('input[name="first_name"]'); if (el) el.focus();
}
</script>
