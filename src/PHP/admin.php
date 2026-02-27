<?php
// admin.php - Admin panel
// expects init.php already included

// Check if user is admin or staff
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['admin', 'staff'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">';
    echo '<p class="font-semibold">Toegang geweigerd</p>';
    echo '<p>Je hebt geen administratieve rechten.</p>';
    echo '</div>';
    return;
}

$error = '';
$success = '';
$action = $_GET['action'] ?? null;

// Handle role changes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'change_role') {
    $user_id = $_POST['user_id'] ?? null;
    $new_role = $_POST['role'] ?? null;
    
    if ($user_id && $new_role && in_array($new_role, ['admin', 'staff', 'klant', 'guest'])) {
        try {
            $db = get_db();
            $stmt = $db->prepare('UPDATE users SET role = ? WHERE id = ?');
            $stmt->execute([$new_role, $user_id]);
            $success = 'Rol succesvol gewijzigd!';
        } catch (PDOException $e) {
            $error = 'Er is een fout opgetreden bij het wijzigen van de rol.';
        }
    }
}

// Get all users
$users = [];
try {
    $db = get_db();
    $stmt = $db->prepare('SELECT id, email, first_name, last_name, role, created_at FROM users ORDER BY created_at DESC');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Er is een fout opgetreden bij het ophalen van gebruikers.';
}
?>

<div class="w-full max-w-6xl bg-white shadow-lg rounded-lg p-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-2">Admin Paneel</h2>
    <p class="text-gray-600 mb-6">Beheer gebruikers en hun rollen</p>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-300">
                    <th class="px-4 py-3 font-semibold text-gray-700">ID</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Voornaam</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Achternaam</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">E-mailadres</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Rol</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Geregistreerd</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700"><?php echo htmlspecialchars($user['id']); ?></td>
                        <td class="px-4 py-3 text-gray-700"><?php echo htmlspecialchars($user['first_name']); ?></td>
                        <td class="px-4 py-3 text-gray-700"><?php echo htmlspecialchars($user['last_name']); ?></td>
                        <td class="px-4 py-3 text-gray-700"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="px-4 py-3">
                            <form method="POST" action="index.php?page=admin&action=change_role" class="flex gap-2">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <select name="role" class="px-3 py-2 border border-gray-300 rounded text-sm" onchange="this.form.submit()">
                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>Medewerker</option>
                                    <option value="klant" <?php echo $user['role'] === 'klant' ? 'selected' : ''; ?>>Klant</option>
                                    <option value="guest" <?php echo $user['role'] === 'guest' ? 'selected' : ''; ?>>Gast</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-sm"><?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($user['created_at']))); ?></td>
                        <td class="px-4 py-3 text-sm">
                            <a href="index.php?page=participants" class="text-blue-600 hover:text-blue-800">Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (empty($users)): ?>
        <div class="text-center py-8 text-gray-600">
            <p>Geen gebruikers gevonden.</p>
        </div>
    <?php endif; ?>
</div>
