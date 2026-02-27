<?php
require __DIR__ . '/init.php';

$message = '';
$error = '';

try {
    $db = get_db();
    
    // Fix all NULL roles to 'klant'
    $stmt = $db->prepare('UPDATE users SET role = ? WHERE role IS NULL OR role = ""');
    $stmt->execute(['klant']);
    
    // Ensure at least one admin exists
    $stmt = $db->prepare('SELECT COUNT(*) as cnt FROM users WHERE role = ?');
    $stmt->execute(['admin']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['cnt'] == 0) {
        $email = 'admin@foto.local';
        $password = password_hash('AdminPass123', PASSWORD_BCRYPT);
        
        $stmt = $db->prepare('INSERT INTO users (email, first_name, last_name, password, role) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$email, 'Admin', 'Account', $password, 'admin']);
        
        $message = '<strong>✓ Migratie voltooid!</strong><br><br>';
        $message .= 'Admin account aangemaakt:<br>';
        $message .= '<strong>Email:</strong> admin@foto.local<br>';
        $message .= '<strong>Wachtwoord:</strong> AdminPass123<br><br>';
        $message .= 'Alle accounts zonder rol zijn nu ingesteld op "klant"';
    } else {
        $message = '<strong>✓ Migratie voltooid!</strong><br><br>';
        $message .= 'Alle accounts zonder rol zijn nu ingesteld op "klant"<br>';
        $message .= 'Admin account bestaat al!';
    }
    
    // Show all users and their roles
    $stmt = $db->prepare('SELECT id, email, first_name, last_name, role FROM users ORDER BY id');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = 'Fout: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoTo Migrate</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        :root{
            --gold: #D3B69C;
            --deepblack: #0B0B45;
            --textWhite: #FFFFFF;
        }
        body{ background: var(--offwhite); }
    </style>
</head>
<body class="p-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">FoTo Migratie</h1>
        
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded mb-6">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($users)): ?>
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Gebruikers in database:</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Voornaam</th>
                                <th class="px-4 py-2">Achternaam</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Rol</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2"><?php echo $user['id']; ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($user['first_name']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($user['last_name']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="px-4 py-2">
                                        <span class="px-3 py-1 rounded font-semibold text-white" style="background: <?php 
                                            echo ($user['role'] === 'admin' ? '#DC2626' : 
                                                 ($user['role'] === 'staff' ? '#2563EB' : 
                                                 ($user['role'] === 'klant' ? '#059669' : '#6B7280'))); 
                                        ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <a href="index.php?page=login" class="inline-block px-6 py-3 rounded-lg font-semibold" style="background: var(--deepblack); color: var(--textWhite);">Terug naar login</a>
    </div>
</body>
</html>
