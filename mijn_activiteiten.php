<?php

require_once __DIR__ . '/autoload.php';

$auth = new Auth();
$auth->requireUser();

$user = $auth->user();
$gebruikerId = (int) $user->getId();

$bookingService = new BookingService(new ActivityModel());
$participationService = new ParticipationService(new Deelnemers());

$alleActiviteiten = $bookingService->getAllActivities();
$mijnActiviteiten = $participationService->filterActivitiesForUser($gebruikerId, $alleActiviteiten);

$pageTitle = 'Mijn activiteiten';
$headerTitle = 'Mijn activiteiten';
$bodyClass = 'min-h-screen flex flex-col bg-gray-100';
$bodyStyle = '';
$footerText = 'FoTo-project - Mijn activiteiten';

require __DIR__ . '/includes/header.php';
?>

<main class="flex-1 max-w-4xl w-full mx-auto px-4 py-6">
    <h2 class="text-xl font-semibold mb-4 text-[#0B0B45]">Jouw aanmeldingen</h2>

    <?php if (empty($mijnActiviteiten)): ?>
        <p class="text-gray-600">Je bent nog niet aangemeld voor een activiteit.</p>
    <?php else: ?>
        <?php foreach ($mijnActiviteiten as $activiteit): ?>
            <article class="bg-white shadow p-4 mb-4 rounded border border-gray-200">
                <h3 class="font-semibold text-lg"><?= PageViewHelper::activityTitle($activiteit); ?></h3>
                <p class="text-gray-700">Type: <?= htmlspecialchars($activiteit->getActivityType()); ?></p>
                <p class="text-gray-700">Datum: <?= htmlspecialchars($activiteit->getDatum()); ?></p>
                <p class="text-gray-700">Tijd: <?= htmlspecialchars($activiteit->getTijd()); ?></p>
                <p class="text-gray-700">Plaats: <?= htmlspecialchars($activiteit->getPlaats() !== '' ? $activiteit->getPlaats() : 'Nog niet ingevuld'); ?></p>

                <div class="flex gap-3 mt-3">
                    <a href="ActiviteitDetail.php?id=<?= (int) $activiteit->getId(); ?>" class="px-4 py-2 secondary-btn rounded">Details</a>
                    <form method="post" action="afmelden_activiteit.php">
                        <input type="hidden" name="activiteit_id" value="<?= (int) $activiteit->getId(); ?>">
                        <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Afmelden</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
