<?php
require __DIR__ . '/init.php';

$message = '';
$error = '';

// Check if admin exists
try {
    $db = get_db();
    $stmt = $db->prepare('SELECT id FROM users WHERE role = ?');
    $stmt->execute(['admin']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        $message = 'Admin account bestaat al!';
    } else {
        // Create default admin
        $email = 'admin@foto.local';
        $password = password_hash('AdminPass123', PASSWORD_BCRYPT);
        
        $stmt = $db->prepare('INSERT INTO users (email, first_name, last_name, password, role) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$email, 'Admin', 'Account', $password, 'admin']);
        
        $message = 'Admin account aangemaakt!<br><strong>Email:</strong> admin@foto.local<br><strong>Wachtwoord:</strong> AdminPass123';
    }
} catch (PDOException $e) {
    $error = 'Fout: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoTo Setup</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        :root{
            --gold: #D3B69C;
            --deepblack: #0B0B45;
            --textWhite: #FFFFFF;
        }
        body{ background: var(--deepblack); }
    </style>
</head>
<body class="flex justify-center items-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">FoTo Setup</h1>
        
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <p class="text-gray-600 mb-6">Setup successvol! Je kan nu inloggen met het admin account.</p>
        
        <a href="index.php?page=login" class="w-full block text-center py-3 rounded-lg font-semibold" style="background: var(--deepblack); color: var(--textWhite);">Terug naar login</a>
    </div>
</body>
</html>
