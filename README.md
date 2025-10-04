spPortalSystem
===============

Einfacher Einstieg und Installation (für PHP‑Anfänger)

Überblick
- spPortalSystem ist ein kleines, modulares PHP‑Portal/CMS aus der Zeit um 2009.
- Es bringt einen einfachen Web‑Installer mit, der die Datenbank einrichtet und eine Konfigurationsdatei erstellt.
- Standardtemplate: template/blueSteel.

Voraussetzungen
- Webserver: Apache oder Nginx (Apache mit mod_php ist am einfachsten). 
- PHP: Am besten PHP 5.6 (ältere mysql_* Funktionen werden genutzt). Neuere PHP‑Versionen (7.x/8.x) werden vermutlich nicht funktionieren, außer Sie passen den Code auf mysqli/PDO an.
- Datenbank: MySQL oder MariaDB.
- Dateizugriff: Der Webserver‑Benutzer muss während der Installation Schreibrechte im Projekt‑Hauptordner haben, damit die Datei config.inc.php erstellt werden kann.

Schnellstart (empfohlen: Web‑Installer)
1) Dateien hochladen
   - Laden Sie alle Projektdateien in das Document‑Root Ihres Webservers hoch, z. B. nach /var/www/html/spportalsystem oder in ein Unterverzeichnis Ihrer Domain.

2) Datenbank anlegen
   - Erstellen Sie eine leere MySQL/MariaDB‑Datenbank (z. B. spportal) und einen Benutzer mit allen Rechten auf diese DB.

3) Schreibrechte prüfen
   - Der Projekt‑Hauptordner muss temporär für den Webserver beschreibbar sein, damit der Installer die config.inc.php erzeugen kann.
   - Wenn Sie Apache unter Linux verwenden, reicht häufig: chown -R www-data:www-data /var/www/html/spportalsystem und ggf. chmod 775 (im Zweifel kurzzeitig 777; danach wieder zurücksetzen).

4) Web‑Installer starten
   - Öffnen Sie im Browser: http://IHRE-DOMAIN/spportalsystem/install/step1.php
   - Füllen Sie die Felder aus:
     • Database Settings: Server (meist localhost), DB‑User, Passwort, Datenbankname.
     • System Settings: Template-Pfad (Standard: template/blueSteel). HTTPS fürs Admincenter optional.
     • Logging: Für Anfänger am besten alles auf „off“ lassen, später bei Bedarf aktivieren.
     • Log Level: SP_LOG_NOTHING für den Anfang.
   - Klicken Sie auf „Next -> Step 2“.

5) Konfiguration schreiben & Datenbank befüllen (Step 2)
   - Der Installer erstellt die Datei config.inc.php im Projekt‑Hauptordner.
   - Er importiert die Tabelle/Grunddaten aus install/installSQL/install_sp_portal.sql.
   - Es wird ein Standard‑Adminbenutzer angelegt:
     • Benutzername: root
     • Passwort: test
   - Danach werden Sie zum Admin‑Bereich weitergeleitet.

6) Erster Login
   - Öffnen Sie Ihre Startseite, z. B. http://IHRE-DOMAIN/spportalsystem/
   - Melden Sie sich mit root / test an (Admin‑Modul).
   - Ändern Sie das Passwort sofort nach dem ersten Login.

7) Sicherheit & Aufräumen
   - Setzen Sie die Schreibrechte am Projektordner wieder restriktiv (z. B. 755/750, je nach Setup).
   - Löschen oder sperren Sie den Ordner install, wenn die Installation abgeschlossen ist.

Alternative: Manuelle Installation (ohne Web‑Installer)
- Nur nötig, wenn der Web‑Installer nicht funktioniert.
1) Datenbank anlegen (wie oben).
2) SQL manuell importieren: importieren Sie install/installSQL/install_sp_portal.sql in Ihre leere Datenbank.
3) config.inc.php anpassen: Öffnen/bearbeiten Sie die Datei config.inc.php im Projekt‑Hauptordner und tragen Sie Ihre Zugangsdaten ein:
   define('SP_CORE_DB_SERVER','localhost');
   define('SP_CORE_DB_USER','root');
   define('SP_CORE_DB_PASS','pass');
   define('SP_CORE_DB_DATABASE','spportal');
   Weitere wichtige Einstellungen:
   - Sprache/Kodierung: SP_CORE_LANG ('de'), SP_CORE_ENCODING ('UTF-8')
   - Template: SP_CORE_TEMPLATE_PATH ('template/blueSteel/')
   - URL‑Rewrite: SP_PORTAL_SYSTEM_URL_REWRITE (standardmäßig false). Aktivieren Sie dies nur, wenn Ihr Webserver korrekt konfiguriert ist (Apache mod_rewrite oder entsprechendes Nginx‑Rewrite).
4) Adminbenutzer: Falls nicht durch den Installer angelegt, können Sie den obigen Standarduser per SQL anlegen (siehe Step 2 Logik) oder im Adminbereich Benutzer erstellen.

Ordnerstruktur (Kurzüberblick)
- index.php: Einstiegspunkt der Webseite.
- config.inc.php: Zentrale Konfigurationsdatei (wird vom Installer erzeugt/überschrieben).
- install/: Web‑Installer und SQL‑Skript.
- module/: Funktionsmodule (admin, portal, sptwitter, ...).
- includes/: Kernklassen (Datenbank, Benutzer, Helper, ...).
- template/: Frontend‑Templates (Standard: template/blueSteel).
- ext_include/: Drittbibliotheken (z. B. PHPMailer).

Häufige Probleme & Lösungen
- Leere Seite/Fehler nach Installation:
  • Stellen Sie sicher, dass die PHP‑Version kompatibel ist (idealerweise 5.6). Bei PHP 7/8 treten oft Fehler auf.
  • Prüfen Sie die Datenbankverbindung in config.inc.php.
  • Zum Debuggen können Sie in config.inc.php das Log‑Level erhöhen (SP_CORE_DEBUG auf SP_LOG_DEBUG setzen) und SP_CORE_LOG_WEB auf true.
- config.inc.php wird nicht erstellt:
  • Schreibrechte des Projektordners prüfen. Führen Sie die Installation erneut aus.
- URL‑Rewrite funktioniert nicht:
  • Lassen Sie SP_PORTAL_SYSTEM_URL_REWRITE auf false oder konfigurieren Sie mod_rewrite/Nginx‑Rewrites korrekt, bevor Sie es aktivieren.

Update/Upgrade (kurz)
- Backup von Dateien und Datenbank erstellen.
- Dateien durch neue Version ersetzen (config.inc.php behalten).
- Falls vorhanden, Hinweise im Ordner update lesen und ausführen.

Lizenz und Hinweise
- Lizenz: Siehe LICENSE (LGPL).
- Autor: Daniel Stecker (2009), www.sploindy.de / www.sp-portalsystem.com (historisch).

Viel Erfolg! Wenn Sie Fragen haben, starten Sie am besten mit der Web‑Installation und halten Sie Ihre PHP‑Version möglichst nahe an 5.6, um Kompatibilitätsprobleme zu vermeiden.


Docker-Entwicklung (PHP aktuell + Xdebug + PhpStorm)
---------------------------------------------------
Hinweis: Das Projekt selbst wurde ursprünglich für PHP 5.6 erstellt. Das folgende Docker-Setup stellt bewusst die aktuellste PHP-Version (8.x) bereit, um modernes Debugging mit PhpStorm zu ermöglichen. Alte mysql_* Funktionen dieses Projekts sind auf 8.x nicht lauffähig, außer der Code wird angepasst (mysqli/PDO). Nutzen Sie das Docker-Setup also primär für moderne PHP-Entwicklung oder nach Code-Anpassungen.

Schnellstart
- Voraussetzungen: Docker (>= 20.10) und optional Docker Desktop.
- Starten:
  1) docker compose up --build -d
  2) Öffnen: http://localhost:8080

PhpStorm Xdebug-Setup (Xdebug 3)
- Debug-Port: 9003 (PhpStorm-Standard)
- IDE Key: PHPSTORM
- Container ist vorkonfiguriert mit client_host=host.docker.internal
  • Unter Linux ggf. zusätzlich in docker-compose.yml die Zeile extra_hosts aktiv lassen (host-gateway).

PhpStorm konfigurieren
1) File | Settings | PHP:
   - CLI Interpreter: nicht zwingend nötig; Web-Server via Docker reicht.
   - Server anlegen: Name „spportalsystem“, Host „localhost“, Port „8080“, Häkchen bei „Use path mappings“ und Projektstamm -> /var/www/html
2) Run | Edit Configurations | PHP Remote Debug:
   - Server: „spportalsystem“
   - IDE key: PHPSTORM
3) Starten Sie den Debug-Listener (Telefonhörer-Symbol) und laden Sie die Seite neu.

Dateien
- Dockerfile: PHP 8.4 + Apache + Xdebug 3, mod_rewrite aktiv
- docker-compose.yml: Startet Container auf Port 8080, bind-mount des Projektordners
- .docker/php.ini: Dev-Defaults (E_ALL, display_errors=On etc.)
- .docker/xdebug.ini: Xdebug 3-Einstellungen

Nützliche Kommandos
- Starten (Build + Hintergrund): docker compose up --build -d
- Logs ansehen: docker compose logs -f
- Neu starten: docker compose restart
- Stoppen: docker compose down
