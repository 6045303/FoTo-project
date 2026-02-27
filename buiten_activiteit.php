<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Boek buiten activiteit</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="index.css">
</head>
<body class="min-h-screen flex flex-col" style="background-color:#D3B69C; color:#111111;">
	<header class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
		<div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
			<h1 class="text-xl font-semibold">Activiteiten — Boek buiten</h1>
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
				<h2 class="text-2xl font-bold mb-4">Boek een buiten activiteit</h2>
				<p class="mb-6">Vul het formulier in om een buiten activiteit te boeken. Velden met * zijn verplicht.</p>

				<form action="save_booking.php" method="post" class="space-y-4" novalidate>
					<input type="hidden" name="activity_type" value="buiten">

					<div>
						<label class="block text-sm font-medium mb-1">Volledige naam *</label>
						<input name="naam" required class="w-full p-3 rounded border" type="text" placeholder="Bijv. Jan Jansen">
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm font-medium mb-1">E-mail *</label>
							<input name="email" required class="w-full p-3 rounded border" type="email" placeholder="naam@voorbeeld.com">
						</div>
						<div>
							<label class="block text-sm font-medium mb-1">Telefoon</label>
							<input name="telefoon" class="w-full p-3 rounded border" type="tel" placeholder="0612345678">
						</div>
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
						<div>
							<label class="block text-sm font-medium mb-1">Datum *</label>
							<input name="datum" required class="w-full p-3 rounded border" type="date">
						</div>

						<div>
							<label class="block text-sm font-medium mb-1">Tijd *</label>
							<input name="tijd" required class="w-full p-3 rounded border" type="time">
						</div>

						<div>
							<label class="block text-sm font-medium mb-1">Aantal gasten</label>
							<input name="gasten" class="w-full p-3 rounded border" type="number" min="1" value="1">
						</div>
					</div>

					<!-- Nieuwe rij: Plaats en weerresultaat -->
					<section class="weatherForm">
						<div class="card rounded-md p-4 shadow-sm" style="background-color:#ffffff;">
							<div class="flex items-center justify-between mb-3">
								<h3 class="text-lg font-medium">Weer opvragen</h3>
								<small class="text-sm text-gray-600">Kort overzicht van de voorspelling</small>
							</div>
							<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
								<div class="sm:col-span-2">
									<label class="block text-sm font-medium mb-1" for="plaats">Plaats</label>
									<input id="plaats" name="plaats" type="text" class="w-full p-3 rounded border cityInput" placeholder="Plaats hier uw stad of dorp">
								</div>
								<div>
									<button type="button" id="weather-button" class="w-full px-4 py-2 rounded font-medium primary-btn">Weer opvragen</button>
								</div>
							</div>
							<div id="weather-result" class="mt-3 p-3 rounded border bg-white text-sm" style="display:none;"></div>
						</div>
					</section>

					<div class="flex items-center gap-3">
						<input id="overdekt" name="overdekt" type="checkbox" class="h-4 w-4">
						<label for="overdekt" class="text-sm">Overdekt nodig</label>
					</div>

					<div>
						<label class="block text-sm font-medium mb-1">Opmerkingen</label>
						<textarea name="opmerkingen" class="w-full p-3 rounded border" rows="4" placeholder="Bijv. parkeerinstructies of bijzonderheden"></textarea>
					</div>

					<div class="flex gap-3 mt-2">
						<button type="submit" class="px-6 py-3 rounded font-medium primary-btn">Boeken</button>
						<a href="index.php" class="px-6 py-3 rounded font-medium secondary-btn inline-flex items-center justify-center">Annuleren</a>
						<a href="binnen_activiteit.php" class="ml-auto text-sm underline">Naar binnen activiteit</a>
					</div>
				</form>
			</section>
		</div>
	</main>

	<!-- External weather script: handles button and Enter, fetches OpenWeatherMap -->
	<script src="Weer.js?v=2"></script>

	<footer class="w-full" style="background-color:#0B0B45; color:#FFFFFF;">
		<div class="max-w-4xl mx-auto px-4 py-4 text-sm text-center">
			&copy; <?php echo date('Y'); ?> FoTo-project — Boek buiten
		</div>
	</footer>
</body>
</html>
