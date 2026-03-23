<?php

require_once __DIR__ . '/autoload.php';

$auth = new Auth();
$user = $auth->user();
$bookingService = new BookingService(new ActivityModel());

$binnen = $bookingService->getBinnenActivities();
$buiten = $bookingService->getBuitenActivities();

function renderActivityCard(Activity $activity, string $editPage): string
{
    ob_start();
    ?>
    <article class="p-3 rounded border shadow-sm flex items-start gap-3">
        <div class="flex-1">
            <div class="font-semibold text-base">
                <?= PageViewHelper::activityTitle($activity); ?>
            </div>
            <div class="text-xs text-gray-600">
                Geboekt door: <?= htmlspecialchars($activity->getNaam()); ?>
            </div>
            <div class="text-xs text-gray-600 mt-1">
                <?= htmlspecialchars($activity->getDatum()); ?> - <?= htmlspecialchars($activity->getTijd()); ?>
                <?php if ($activity->getPlaats() !== ''): ?>
                    | <?= htmlspecialchars($activity->getPlaats()); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex flex-col items-end gap-2">
            <a href="<?= $editPage; ?>?id=<?= (int) $activity->getId(); ?>" class="px-3 py-1 rounded text-sm secondary-btn">Bewerken</a>
            <a href="ActiviteitDetail.php?id=<?= (int) $activity->getId(); ?>" class="px-3 py-1 rounded text-sm secondary-btn">Details</a>
            <form method="post" action="delete_booking.php" onsubmit="return confirm('Weet je zeker dat je deze reservering wilt verwijderen?');">
                <input type="hidden" name="id" value="<?= (int) $activity->getId(); ?>">
                <button type="submit" class="px-3 py-1 rounded text-sm primary-btn">Verwijderen</button>
            </form>
        </div>
    </article>
    <?php

    return (string) ob_get_clean();
}

$pageTitle = 'Activiteiten overzicht';
$headerTitle = 'Activiteiten';
$footerText = 'FoTo-project - Activiteiten overzicht';

require __DIR__ . '/includes/header.php';
?>

<main class="flex-1 w-full">
    <div class="max-w-4xl mx-auto px-4 py-12">
        <section class="rounded-lg p-6 shadow-md" style="background-color:#FAEBD7;">
            <div class="grid grid-cols-1 gap-6">
                <div class="rounded-lg overflow-hidden shadow">
                    <div class="p-4 card-header flex items-center justify-between">
                        <div class="text-lg font-semibold">Binnen activiteiten</div>
                        <div class="text-sm"><?= count($binnen); ?> items</div>
                    </div>
                    <div class="p-4 bg-white space-y-3">
                        <?php if (empty($binnen)): ?>
                            <div class="text-gray-600">Nog geen binnen activiteiten.</div>
                        <?php else: ?>
                            <?php foreach ($binnen as $activity): ?>
                                <?= renderActivityCard($activity, 'BinnenActiviteit.php'); ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 bg-gray-50 text-right">
                        <a href="BinnenActiviteit.php" class="px-4 py-2 rounded secondary-btn">Nieuwe binnen activiteit</a>
                    </div>
                </div>

                <div class="rounded-lg overflow-hidden shadow">
                    <div class="p-4 card-header flex items-center justify-between">
                        <div class="text-lg font-semibold">Buiten activiteiten</div>
                        <div class="text-sm"><?= count($buiten); ?> items</div>
                    </div>
                    <div class="p-4 bg-white space-y-3">
                        <?php if (empty($buiten)): ?>
                            <div class="text-gray-600">Nog geen buiten activiteiten.</div>
                        <?php else: ?>
                            <?php foreach ($buiten as $activity): ?>
                                <?= renderActivityCard($activity, 'BuitenActiviteit.php'); ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 bg-gray-50 text-right">
                        <a href="BuitenActiviteit.php" class="px-4 py-2 rounded secondary-btn">Nieuwe buiten activiteit</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
