# README — Lokale site met XAMPP

Deze map bevat een PHP-site die je lokaal met XAMPP kunt openen.

**Voorwaarden:**
- XAMPP geïnstalleerd (Apache minimaal).
- Project staat in de XAMPP `htdocs`-map: `c:\xampp\htdocs\Foto project\code`

**Starten:**
1. Open de XAMPP Control Panel.
2. Start **Apache** (en MySQL als je database nodig hebt).

**Openen in de browser:**
- Als de map exact staat als `c:\xampp\htdocs\Foto project\code` (let op spaties), open in je browser:

  http://localhost/Foto%20project/code/

  of direct naar de index:

  http://localhost/Foto%20project/code/index.php

- Aanbevolen: verwijder spaties uit de mapnaam (`Foto_project`) en gebruik dan simpeler:

  http://localhost/Foto_project/code/

- Als Apache op een andere poort draait (bijv. 8080):

  http://localhost:8080/Foto%20project/code/

**Virtual Host (optioneel, netter):**
- Voeg een VirtualHost toe en een hosts-entry (`127.0.0.1 mysite.test`) als je liever een eigen domein gebruikt.

**Problemen oplossen:**
- Controleer in XAMPP Control Panel of Apache draait en op welke poort.
- Controleer firewall/antivirus die poort 80/8080 kan blokkeren.


