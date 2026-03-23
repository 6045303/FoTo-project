<?php

require_once __DIR__ . '/autoload.php';

$auth = new Auth();
$user = $auth->user();
$bookingService = new BookingService(new ActivityModel());
$edit = null;
$isEditing = false;

if (isset($_GET['id'])) {
    $edit = $bookingService->getActivityById((int) $_GET['id']);
    $isEditing = $edit !== null && $edit->isBuiten();
}

$pageTitle = $isEditing ? 'Bewerk buiten activiteit' : 'Boek buiten activiteit';
$headerTitle = 'Buiten activiteit';
$footerText = 'FoTo-project - Buiten activiteit';

require __DIR__ . '/includes/header.php';
?>

<main class="flex-1 w-full">
    <div class="max-w-4xl mx-auto px-4 py-12">
        <section class="rounded-lg p-6 shadow-md" style="background-color:#FAEBD7;">
            <h2 class="text-2xl font-bold mb-4"><?= $isEditing ? 'Bewerk deze buiten activiteit' : 'Boek een buiten activiteit'; ?></h2>

            <?php if ($user->isGuest()): ?>
                <div class="p-6 bg-yellow-200 border border-yellow-600 rounded mb-6">
                    <p class="text-black font-medium">Je moet ingelogd zijn om een activiteit te boeken.</p>
                    <a href="login.php" class="primary-btn mt-3 inline-block px-4 py-2 rounded">Inloggen</a>
                    <a href="registeer.php" class="secondary-btn mt-3 inline-block px-4 py-2 rounded">Registreren</a>
                </div>
            <?php else: ?>
                <form action="classes/save_booking.php" method="post" data-form-type="booking">
                    <?php if ($isEditing): ?>
                        <input type="hidden" name="id" value="<?= (int) $edit?->getId(); ?>">
                    <?php endif; ?>

                    <input type="hidden" name="activity_type" value="buiten">

                    <div>
                        <label class="block text-sm font-medium mb-1">Volledige naam *</label>
                        <input name="naam" required class="w-full p-3 rounded border" type="text" value="<?= $edit ? htmlspecialchars($edit->getNaam()) : ''; ?>" placeholder="Bijv. Jan Jansen">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">E-mail *</label>
                            <input name="email" required class="w-full p-3 rounded border" type="email" value="<?= $edit ? htmlspecialchars($edit->getEmail()) : ''; ?>" placeholder="naam@voorbeeld.nl">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Telefoon</label>
                            <input name="telefoon" class="w-full p-3 rounded border" type="tel" value="<?= $edit ? htmlspecialchars($edit->getTelefoon()) : ''; ?>" placeholder="0612345678">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Datum *</label>
                            <input name="datum" required class="w-full p-3 rounded border" type="date" value="<?= $edit ? htmlspecialchars($edit->getDatum()) : ''; ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tijd *</label>
                            <input name="tijd" required class="w-full p-3 rounded border" type="time" value="<?= $edit ? htmlspecialchars($edit->getTijd()) : ''; ?>">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Aantal gasten</label>
                            <input name="gasten" class="w-full p-3 rounded border" type="number" min="1" value="<?= htmlspecialchars((string) ($edit?->getGasten() ?? 1)); ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Plaats *</label>
                            <input name="plaats" required class="w-full p-3 rounded border" type="text" value="<?= $edit ? htmlspecialchars($edit->getPlaats()) : ''; ?>" placeholder="Bijv. Amsterdamse Bos">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Opmerkingen</label>
                        <textarea name="opmerkingen" class="w-full p-3 rounded border" rows="4" placeholder="Bijvoorbeeld: extra uitleg of bijzonderheden"><?= $edit ? htmlspecialchars($edit->getOpmerkingen()) : ''; ?></textarea>
                    </div>

                    <div class="flex gap-3 mt-4">
                        <button type="submit" class="px-6 py-3 rounded font-medium primary-btn"><?= $isEditing ? 'Opslaan' : 'Boeken'; ?></button>
                        <a href="index.php" class="px-6 py-3 rounded font-medium secondary-btn inline-flex items-center justify-center">Annuleren</a>
                        <a href="BinnenActiviteit.php" class="ml-auto text-sm underline">Naar binnen activiteit</a>
                    </div>
                </form>
            <?php endif; ?>
        </section>
    </div>
</main>
<script type="module" src="js/form.js"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
