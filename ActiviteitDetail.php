<?php

require_once __DIR__ . '/autoload.php';

$auth = new Auth();
$user = $auth->user();
$activityId = (int) ($_GET['id'] ?? 0);

if ($activityId <= 0) {
    header('Location: index.php');
    exit;
}

$bookingService = new BookingService(new ActivityModel());
$participationService = new ParticipationService(new Deelnemers());
$activiteit = $bookingService->getActivityById($activityId);

if ($activiteit === null) {
    echo 'Activiteit niet gevonden.';
    exit;
}

$isAangemeld = false;

if (!$user->isGuest() && $user->getId() !== null) {
    $isAangemeld = $participationService->isAangemeld($user->getId(), $activityId);
}

$deelnemersLijst = $participationService->getDeelnemers($activityId);

$pageTitle = 'Activiteit details';
$headerTitle = 'Activiteit details';
$bodyClass = 'min-h-screen flex flex-col bg-gray-100';
$bodyStyle = '';
$footerText = 'FoTo-project - Activiteit details';

require __DIR__ . '/includes/header.php';
?>

<main class="flex-1 max-w-4xl w-full mx-auto px-4 py-6">
    <h2 class="text-3xl font-bold text-[#0B0B45] mb-4">
        <?= PageViewHelper::activityTitle($activiteit); ?>
    </h2>

    <div class="bg-white shadow p-6 rounded border border-gray-200 mb-6">
        <p class="text-gray-700 mb-2"><strong>Naam:</strong> <?= htmlspecialchars($activiteit->getNaam()); ?></p>
        <p class="text-gray-700 mb-2"><strong>Type:</strong> <?= htmlspecialchars($activiteit->getActivityType()); ?></p>
        <p class="text-gray-700 mb-2"><strong>Datum:</strong> <?= htmlspecialchars($activiteit->getDatum()); ?></p>
        <p class="text-gray-700 mb-2"><strong>Tijd:</strong> <?= htmlspecialchars($activiteit->getTijd()); ?></p>
        <p class="text-gray-700 mb-2"><strong>Plaats:</strong> <?= htmlspecialchars($activiteit->getPlaats() !== '' ? $activiteit->getPlaats() : 'Nog niet ingevuld'); ?></p>
        <p class="text-gray-700 mb-2"><strong>Aantal gasten:</strong> <?= htmlspecialchars((string) $activiteit->getGasten()); ?></p>
        <p class="text-gray-700"><strong>Contact:</strong> <?= htmlspecialchars($activiteit->getEmail()); ?></p>
    </div>

    <?php if (!$user->isGuest()): ?>
        <div class="mb-6">
            <?php if ($isAangemeld): ?>
                <form method="post" action="afmelden_activiteit.php">
                    <input type="hidden" name="activiteit_id" value="<?= $activityId; ?>">
                    <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Afmelden</button>
                </form>
            <?php else: ?>
                <form method="post" action="aanmelden_activiteit.php">
                    <input type="hidden" name="activiteit_id" value="<?= $activityId; ?>">
                    <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Aanmelden</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <h3 class="text-xl font-semibold text-[#0B0B45] mb-3">Deelnemers</h3>

    <?php if (empty($deelnemersLijst)): ?>
        <p class="text-gray-600">Nog geen deelnemers.</p>
    <?php else: ?>
        <ul class="bg-white shadow p-4 rounded border border-gray-200">
            <?php foreach ($deelnemersLijst as $deelnemer): ?>
                <li class="border-b py-2"><?= htmlspecialchars($deelnemer['username']); ?> (<?= htmlspecialchars($deelnemer['email']); ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
