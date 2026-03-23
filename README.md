# FoTo project

Dit project is een simpele PHP-webapp voor het bekijken, boeken en beheren van activiteiten.

## Starten met XAMPP

1. Zet de map in `c:\xampp\htdocs\foto_project`.
2. Start `Apache` en `MySQL` in XAMPP.
3. Open in de browser:

`http://localhost/foto_project/code/`

## Belangrijke onderdelen

- `autoload.php`: laadt classes automatisch.
- `classes/db.php`: databaseverbinding als singleton.
- `classes/BaseModel.php`: abstracte basisclass voor modellen.
- `classes/ActivityRepositoryInterface.php`: interface voor het activiteitenmodel.
- `classes/ActivityModel.php`: ophalen, toevoegen, aanpassen en verwijderen van activiteiten.
- `classes/Auth.php`: inloggen, registreren en uitloggen.
- `classes/User.php`: gebruiker met rollen zoals `guest`, `user` en `admin`.
- `js/form.js` en `js/FormValidator.js`: formuliercontrole met modules en classes.
- `js/Weer.js` en `js/WeatherService.js`: API-aanroep naar OpenWeather met JSON, DOM-manipulatie en HTML-template.

## Uitleg voor de rubric

### JS

- API en JSON:
  Het weer wordt opgehaald met `fetch()` uit de OpenWeather API. Het antwoord komt terug als JSON en wordt verwerkt in JavaScript.
- OOP:
  `WeatherApp` gebruikt `extends` van `WeatherService`. `FormValidator` gebruikt `extends` van `BaseValidator`. Er wordt ook een `static` methode gebruikt.
- Modules:
  De JS-bestanden gebruiken `import` en `export`.
- DOM en events:
  Met `addEventListener()` worden klikken en submits afgehandeld. De pagina wordt aangepast met `innerHTML` en nieuwe DOM-elementen.
- HTML templates:
  De weerkaart wordt opgebouwd met een template string in JavaScript.

### PHP

- OOP:
  Het project gebruikt classes zoals `Auth`, `User`, `ActivityModel`, `Database` en `deelnemers`.
- Interface en abstract:
  `ActivityModel` gebruikt de interface `ActivityRepositoryInterface` en er is een abstracte basisclass `BaseModel`.
- Database connectie:
  `Database` is een singleton zodat er steeds dezelfde databaseverbinding wordt hergebruikt.
- Autoloader:
  In `autoload.php` worden classes automatisch geladen.
