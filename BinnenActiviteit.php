<?php
require_once 'classes/ActivityModel.php';

$model = new ActivityModel();
$edit = null;
$isEditing = false;

// Als er een ID is → bewerken
if (isset($_GET['id'])) {
    $edit = $model->getById((int)$_GET['id']);
    if ($edit && $edit['activity_type'] === 'binnen') {
        $isEditing = true;
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title><?= $isEditing ? 'Bewerk binnen activiteit' : 'Boek binnen activiteit' ?></title>
	<link rel="stylesheet" href="index.css">
	<script src="https://cdn.tailwindcss.com"></script>
	<style>
		.primary-btn{background-color:#0B0B45;color:#ffffff}
		.secondary-btn{background-color:#D3B69C;color:#111111}
	</style>
</head>

<body class="min-h-screen flex flex-col" style="background-color:#D3B69C; color:#111111;">

	<header class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
		<div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
			<h1 class="text-xl font-semibold">
                Activiteiten — <?= $isEditing ? 'Bewerk binnen activiteit' : 'Boek binnen activiteit' ?>
            </h1>
			<nav>
				<ul class="flex gap-4 text-sm">
					<li><a href="index.php" class="text-white">Overzicht</a></li>
					<li><a href="#" class="text-white">Mijn activiteiten</a></li>
				</ul>
			</nav>
		</div>
	</header>

	<main class="flex-1 w-full">
		<div class="max-w-4xl mx-auto px-4 py-12">

			<section class="rounded-lg p-6 shadow-md" style="background-color:#Faebd7;">
				<h2 class="text-2xl font-bold mb-4">
                    <?= $isEditing ? 'Bewerk deze binnen activiteit' : 'Boek een binnen activiteit' ?>
                </h2>

				<form action="classes/save_booking.php" method="post" class="space-y-4" novalidate>

                    <!-- Verborgen ID voor bewerken -->
                    <?php if ($isEditing): ?>
                        <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                    <?php endif; ?>

					<input type="hidden" name="activity_type" value="binnen">

					<div>
						<label class="block text-sm font-medium mb-1">Volledige naam *</label>
						<input name="naam" required class="w-full p-3 rounded border"
                               type="text"
                               value="<?= $edit['naam'] ?? '' ?>"
                               placeholder="Bijv. Kees de Vries">
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm font-medium mb-1">E-mail *</label>
							<input name="email" required class="w-full p-3 rounded border"
                                   type="email"
                                   value="<?= $edit['email'] ?? '' ?>"
                                   placeholder="6045303@mborijnland.nl">
						</div>
						<div>
							<label class="block text-sm font-medium mb-1">Telefoon</label>
							<input name="telefoon" class="w-full p-3 rounded border"
                                   type="tel"
                                   value="<?= $edit['telefoon'] ?? '' ?>"
                                   placeholder="06-12345678">
						</div>
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
						<div>
							<label class="block text-sm font-medium mb-1">Datum *</label>
							<input name="datum" required class="w-full p-3 rounded border"
                                   type="date"
                                   value="<?= $edit['datum'] ?? '' ?>">
						</div>
						<div>
							<label class="block text-sm font-medium mb-1">Tijd *</label>
							<input name="tijd" required class="w-full p-3 rounded border"
                                   type="time"
                                   value="<?= $edit['tijd'] ?? '' ?>">
						</div>
						<div>
							<label class="block text-sm font-medium mb-1">Aantal gasten</label>
							<input name="gasten" class="w-full p-3 rounded border"
                                   type="number" min="1"
                                   value="<?= $edit['gasten'] ?? 1 ?>">
						</div>
					</div>

					<div>
						<label class="block text-sm font-medium mb-1">Opmerkingen</label>
						<textarea name="opmerkingen" class="w-full p-3 rounded border"
                                  rows="4"
                                  placeholder="Bijv. dieetwensen of bijzonderheden"><?= $edit['opmerkingen'] ?? '' ?></textarea>
					</div>

					<div class="flex gap-3 mt-2">
						<button type="submit" class="px-6 py-3 rounded font-medium primary-btn">
                            <?= $isEditing ? 'Opslaan' : 'Boeken' ?>
                        </button>

						<a href="index.php" class="px-6 py-3 rounded font-medium secondary-btn inline-flex items-center justify-center">
                            Annuleren
                        </a>

						<a href="BuitenActiviteit.php" class="ml-auto text-sm underline">
                            Naar buiten activiteit
                        </a>
					</div>

				</form>
			</section>

		</div>
	</main>

	<footer class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
		<div class="max-w-4xl mx-auto px-4 py-4 text-sm text-center">
			&copy; <?= date('Y'); ?> FoTo-project — Boek binnen
		</div>
	</footer>
	
</body>
<script type="module" src="/code/js/form.js">defer</script>
</html>