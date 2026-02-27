<?php
// invite.php — create guest invitations
// requires init.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$error = '';
$success = '';
$link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geef een geldig e-mailadres op.';
    } else {
        try {
            $db = get_db();
            $token = bin2hex(random_bytes(16));
            $stmt = $db->prepare('INSERT INTO invitations (token, email, inviter_id, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$token, $email, $_SESSION['user_id'], 'guest']);
            $link = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . dirname($_SERVER['REQUEST_URI']) . '/index.php?page=register&token=' . $token;
            // Try to send email (may not be configured on local)
            $subject = 'Uitnodiging om te registreren';
            $message = "Je bent uitgenodigd om je te registreren: " . $link;
            @mail($email, $subject, $message);
            $success = 'Uitnodiging aangemaakt.';
        } catch (Exception $e) {
            $error = 'Kon uitnodiging niet aanmaken.';
        }
    }
}
?>

<div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">
    <h2 class="text-2xl font-bold mb-4">Nodig een gast uit</h2>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Gasten e-mail</label>
            <input name="email" type="email" required class="w-full px-4 py-2 border border-gray-300 rounded">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Maak uitnodiging</button>
    </form>

    <?php if ($link): ?>
        <div class="mt-4">
            <label class="block text-sm text-gray-700 mb-1">Kopieer uitnodigingslink</label>
            <input readonly value="<?php echo htmlspecialchars($link); ?>" class="w-full px-3 py-2 border rounded bg-gray-100">
        </div>
    <?php endif; ?>
</div>
