<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta author="Stefan de Groot">
    <meta description="Activiteiten overzicht - uitnodigen">
    <meta keywords="activiteiten, uitnodigen, boeken">
    <title>Activiteiten - Overzicht</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .primary-btn{background-color:#0B0B45;color:#ffffff}
        .secondary-btn{background-color:#D3B69C;color:#111111}
        .card-header{background-color:#0B0B45;color:#fff}
    </style>
</head>
<?php
require_once __DIR__ . '/db.php';

// helper to check for table presence
function table_exists($mysqli, $table){
    $t = $mysqli->real_escape_string($table);
    $res = $mysqli->query("SHOW TABLES LIKE '".$t."'");
    if (! $res) return false;
    $exists = $res->num_rows > 0;
    $res->free();
    return $exists;
}

// Collect bookings from both `bookings` (new) and `activiteiten` (legacy) and normalize rows
$bookings = [];

if (table_exists($mysqli, 'bookings')){
    $res = $mysqli->query("SELECT id, activity_type, naam, email, telefoon, datum, tijd, gasten, locatie, overdekt, opmerkingen, plaats, created_at FROM bookings");
    if ($res){
        while ($row = $res->fetch_assoc()){
            // ensure created_at exists for sorting
            if (empty($row['created_at'])) $row['created_at'] = date('Y-m-d H:i:s');
            $bookings[] = $row;
        }
        $res->free();
    }
}

if (table_exists($mysqli, 'activiteiten')){
    $res2 = $mysqli->query("SELECT id, naam, type, beschrijving, datum, tijd, plaats, aantal_gasten, aangemaakt_op FROM activiteiten");
    if ($res2){
        while ($r = $res2->fetch_assoc()){
            $mapped = [
                'id' => $r['id'],
                'activity_type' => $r['type'] ?? 'binnen',
                'naam' => $r['naam'] ?? '',
                'email' => null,
                'telefoon' => null,
                'datum' => $r['datum'] ?? null,
                'tijd' => $r['tijd'] ?? null,
                'gasten' => isset($r['aantal_gasten']) ? (int)$r['aantal_gasten'] : 1,
                'locatie' => null,
                'overdekt' => 0,
                'opmerkingen' => $r['beschrijving'] ?? null,
                'plaats' => $r['plaats'] ?? null,
                'created_at' => $r['aangemaakt_op'] ?? date('Y-m-d H:i:s')
            ];
            $bookings[] = $mapped;
        }
        $res2->free();
    }
}

// sort by created_at desc
usort($bookings, function($a, $b){
    $ta = strtotime($a['created_at'] ?? 0);
    $tb = strtotime($b['created_at'] ?? 0);
    return $tb <=> $ta;
});

$binnen = [];
$buiten = [];
foreach ($bookings as $b){
    if (isset($b['activity_type']) && $b['activity_type'] === 'buiten') $buiten[] = $b; else $binnen[] = $b;
}
?>
<body class="min-h-screen flex flex-col" style="background-color:#D3B69C; color:#111111;">
    <header class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold">Activiteiten</h1>
            <nav>
                <ul class="flex gap-4 text-sm">
                    <li><a href="#" class="text-white">Overzicht</a></li>
                    <li><a href="#" class="text-white">Mijn activiteiten</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="flex-1 w-full">
        <div class="max-w-4xl mx-auto px-4 py-12">
            <section class="rounded-lg p-6 shadow-md" style="background-color:#Faebd7;">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Binnen card -->
                    <div class="rounded-lg overflow-hidden shadow">
                        <div class="p-4 card-header">
                            <div class="flex items-center justify-between">
                                <div class="text-lg font-semibold">Binnen activiteiten</div>
                                <div class="text-sm"><?php echo count($binnen); ?> items</div>
                            </div>
                        </div>
                        <div class="p-4 bg-white space-y-3">
                            <?php if (count($binnen) === 0): ?>
                                <div class="text-gray-600">Nog geen binnen activiteiten.</div>
                            <?php else: ?>
                                <?php foreach ($binnen as $b): ?>
                                    <div class="p-3 rounded border shadow-sm flex items-start gap-3">
                                        <div class="flex-1">
                                            <div class="font-medium text-sm"><?php echo htmlspecialchars($b['naam']); ?></div>
                                            <div class="text-xs text-gray-600"><?php echo htmlspecialchars($b['datum'] ?: '-'); ?> <?php echo htmlspecialchars($b['tijd'] ?: ''); ?> · <?php echo htmlspecialchars($b['plaats'] ?: ''); ?></div>
                                            <?php if ($b['opmerkingen']): ?><div class="text-xs mt-1 text-gray-700"><?php echo nl2br(htmlspecialchars($b['opmerkingen'])); ?></div><?php endif; ?>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            <form method="post" action="delete_booking.php" onsubmit="return confirm('Weet je zeker dat je deze reservering wilt verwijderen?');">
                                                <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>">
                                                <button type="submit" class="px-3 py-1 rounded text-sm primary-btn">Verwijder</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="p-4 bg-gray-50 text-right">
                            <a href="binnen_activiteit.php" class="px-4 py-2 rounded secondary-btn">Nieuwe binnen activiteit</a>
                        </div>
                    </div>

                    <!-- Buiten card -->
                    <div class="rounded-lg overflow-hidden shadow">
                        <div class="p-4 card-header">
                            <div class="flex items-center justify-between">
                                <div class="text-lg font-semibold">Buiten activiteiten</div>
                                <div class="text-sm"><?php echo count($buiten); ?> items</div>
                            </div>
                        </div>
                        <div class="p-4 bg-white space-y-3">
                            <?php if (count($buiten) === 0): ?>
                                <div class="text-gray-600">Nog geen buiten activiteiten.</div>
                            <?php else: ?>
                                <?php foreach ($buiten as $b): ?>
                                    <div class="p-3 rounded border shadow-sm flex items-start gap-3">
                                        <div class="flex-1">
                                            <div class="font-medium text-sm"><?php echo htmlspecialchars($b['naam']); ?></div>
                                            <div class="text-xs text-gray-600"><?php echo htmlspecialchars($b['datum'] ?: '-'); ?> <?php echo htmlspecialchars($b['tijd'] ?: ''); ?> · <?php echo htmlspecialchars($b['plaats'] ?: ''); ?></div>
                                            <?php if ($b['opmerkingen']): ?><div class="text-xs mt-1 text-gray-700"><?php echo nl2br(htmlspecialchars($b['opmerkingen'])); ?></div><?php endif; ?>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            <form method="post" action="delete_booking.php" onsubmit="return confirm('Weet je zeker dat je deze reservering wilt verwijderen?');">
                                                <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>">
                                                <button type="submit" class="px-3 py-1 rounded text-sm primary-btn">Verwijder</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="p-4 bg-gray-50 text-right">
                            <a href="buiten_activiteit.php" class="px-4 py-2 rounded secondary-btn">Nieuwe buiten activiteit</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
        <div class="max-w-4xl mx-auto px-4 py-4 text-sm text-center">
            &copy; <?php echo date('Y'); ?> FoTo-project — Activiteiten overzicht
        </div>
    </footer>

    <script>
        // Eenvoudige client-side enhancement: bevestiging bij klikken (kan makkelijk verwijderd worden)
        document.querySelectorAll('.btn-offwhite').forEach(btn => {
            btn.addEventListener('click', (e)=>{
                // voorbeeld: geen blocking confirm, laat navigatie door voor eenvoud
            });
        });
    </script>
</body>
</html>