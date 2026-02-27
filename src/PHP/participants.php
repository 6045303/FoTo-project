<?php
// participants.php — show participants with role distinction
try {
    $db = get_db();
    $stmt = $db->query('SELECT id, email, first_name, last_name, role, created_at FROM users ORDER BY created_at DESC');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $users = [];
}
?>

<div class="w-full max-w-4xl bg-white shadow-lg rounded-lg p-8">
    <h2 class="text-2xl font-bold mb-4">Deelnemerslijst</h2>
    <p class="text-sm text-gray-600 mb-4">Onderstaande lijst toont medewerkers en gasten afzonderlijk.</p>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="p-2">Naam</th>
                <th class="p-2">E-mail</th>
                <th class="p-2">Rol</th>
                <th class="p-2">Geregistreerd</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr class="border-t">
                    <td class="p-2"><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></td>
                    <td class="p-2"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td class="p-2">
                        <?php 
                            $roleBadges = [
                                'admin' => ['bg-red-100', 'text-red-800', 'Admin'],
                                'staff' => ['bg-blue-100', 'text-blue-800', 'Medewerker'],
                                'klant' => ['bg-green-100', 'text-green-800', 'Klant'],
                                'guest' => ['bg-yellow-100', 'text-yellow-800', 'Gast']
                            ];
                            $badge = $roleBadges[$u['role']] ?? ['bg-gray-100', 'text-gray-800', ucfirst($u['role'])];
                        ?>
                        <span class="text-sm px-2 py-1 rounded <?php echo $badge[0] . ' ' . $badge[1]; ?>">
                            <?php echo $badge[2]; ?>
                        </span>
                    </td>
                    <td class="p-2"><?php echo htmlspecialchars($u['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
