{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
    <div class="content2-pagetitle">
        Versionsgeschichte
    </div>
    <div class="content-txtbox-noshade changelog" style="padding-right: 25px;">

    <p>Opencaching Version 1.0 ging im August 2005 online. In den nachfolgenden Jahren wurde die Website stetig verbessert und zur Version 2 weiterentwickelt. Anfang 2011 wurde die Entwicklung vorübergehend eingestellt, bis der neue Verein Opencaching Deutschland sie Mitte 2012 unter der Versionsnummer&nbsp;3.0 wieder aufnahm.</p>

    <p>Im Folgenden sind alle Veränderungen ab OC Version&nbsp;3.0 aufgelistet. Manche Kleinigkeiten wurden zur besseren Übersicht weggelassen; sie sind im <a href="http://redmine.opencaching.de/projects/oc-dev/roadmap">Issue-Tracker</a>, in der <a href="https://github.com/OpencachingDeutschland/oc-server3/commits/stable">Codehistorie</a> und im <a href="http://forum.opencaching.de/index.php?board=43.0">Entwicklerforum</a> nachlesbar. Neue Funktionen werden manchmal schon vorab freigegeben.</p>

    <p>Eine redaktionell aufbereitete Erläuterung neuer OC-Versionen gibt es auch im Opencaching- und im <em>Altmetall</em>-Blog:</p>
    <ul>
        <li><a href="http://blog.opencaching.de/2016/03/oc-3-0v17-wartungszustand-loggen-koordinatenhistorien/">Version 17</a>: Wartungszustand loggen, Koordinatenhistorien</li>
        <li><a href="http://blog.opencaching.de/2016/02/oc-30v16/">Version 16</a>: Sortierbare Suche, Unicode-Zeichen uvm.</li>
        <li><a href="http://blog.opencaching.de/2015/09/oc-30v14/">Version 15</a>: Weitergabe privater Cachelisten, Lesezeichen für Cachelisten, Anzeige der Empfehlungen</li>
        <li><a href="https://blog.dafb-o.de/opencaching-3v14/">Version 14</a>: Ausbau der Cachelistenfunktionen, Zähmung des HTML-Purifiers, Sprachsuche ...</li>
        <li><a href="https://blog.dafb-o.de/oc-3-0v13-listen-neue-suchoption-und-mehr/">Version 13</a>: Listen, neue Suchoption und mehr ...</li>
        <li><a href="https://blog.dafb-o.de/opencaching-3-0-version-11-veroeffentlicht/">Version 11</a>: Automatische Verkleinerung von Bildern, Links zu Logeinträgen ...</li>
        <li><a href="https://blog.dafb-o.de/opencaching-de-version-10-freigegeben/">Version 10</a>: Nachladen von Logeinträgen, ausführliche Statistik im Benutzerprofil ...</li>
        <li><a href="https://blog.dafb-o.de/alle-neune-oder-ein-update-fuer-opencachingde/">Version 9</a>: Suchfunktion, OConly-Features, Liste der eigenen Caches + Loghistorie, Schutzgebiete ...</li>
        <li><a href="https://blog.dafb-o.de/opencaching-3-0-version-8-veroeffentlicht/">Version 8</a>: Statuslogs, Listinglayout, Koordinaten für zusätzliche Wegpunkte, Safari-Caches, Kartenfilteroptionen speichern, automatische Archivierung</li>
        <li><a href="https://blog.dafb-o.de/okapi-jetzt-auch-fuer-opencaching-de/">Version 7</a>: OKAPI</li>
        <li><a href="https://blog.dafb-o.de/oc-3-0-6-loggen-mit-uhrzeit/">Version 6</a>: Loggen mit Uhrzeit, Schutz vor Listingvandalismus</li>
        <li><a href="https://blog.dafb-o.de/opencaching-3-0-5-veroeffentlicht/">Version 5</a>: neue Karte, Vorschaubilder, Bildgalerien, Online-Hilfe ...; vorab: <a href="http://blog.dafb-o.de/neue-icons-auf-der-cachekarte/">neue Kartensymbole</a></li>
        <li><a href="https://blog.dafb-o.de/neues-aus-dem-hause-opencaching-de/">Version 4</a>: GPX-Wegpunkte und -Bildeinbettung, Nano-Caches, neue Datenlizenz</li>
    </ul>
    <br />

    <div class="changelog-changes">

    <p id="current"><strong>Laufende Änderungen</strong></p>
    <ul>
        <li>Entschlüsselte Hinweise können wieder verschlüsselt weden.</li>
        <li>Hinweisteile in [ ] werden beim Entschlüsseln ignoriert.</li>
        <li>Man kann zwischen den angehängten Bildern einer Cachebeschreibung blättern.</li>
        <li>Vergrößerte Bilder werden mit einem Rechtsklick nicht mehr geschlossen.</li>
        <li>Der Opencaching-Chat entfällt.</li>
        <li>Bugfix: Druckansicht von Cachebeschreibungen erscheint in gewählter Sprache.</li>
        <li>Bugfix: Keine OConly-Benachrichtigungen für ignorierte Caches. [Bug von Version 9]</li>
    </ul>
    <br />

    <p id="v3.0.18"><strong>OC 3.0 Version 18</strong> &ndash; 31. März 2016</p>
    <p>Neu:</p>
    <ul>
        <li>Anzeige von Markern bei der Koordinatensuche auf der Karte</li>
        <li>Die Reihenfolge von Bildern in Cachbeschreibungen und Logs ist nachträglich änderbar.</li>
        <li>Koordinatenumrechnung für zusätzliche Wegpunkte</li>
        <li>OKAPI: Logbilder hochladen</li>
        <li>OKAPI: &bdquo;Cache benötigt Wartung&ldquo; ist abfragbar und logbar</li>
        <li>OKAPI: Consumer Keys können gesperrt werden (Admin-Funktion)</li>
    </ul>

    <p>Geändert / verbessert:</p>
    <ul>
        <li>Logs mit und ohne Uhrzeit am gleichen Tag werden sinnvoller geordnet.</li>
        <li>Beim Ändern des Typs des neuesten Logs durch den Owner wird ggf. auch der Status des Caches mit angepasst. Der Typ älterer Statuslogs ist nicht mehr änderbar.</li>
        <li>Smiley-Icons auch in Benachrichtigungsmails (Ausgabe als Text-Smileys)</li>
        <li>Komfortablere Abarbeitung von Cachemeldungen (Admin-Funktion)</li>
        <li>Detailverbesserungen und -Nachbesserungen bei den Wartungsstatus-Logs</li>
    </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>Koordinatensuche auf der Karte funktioniert wieder zuverlässig</li>
        <li>Korrekturen in der <a href="doc/xml">XML-Schnittstelle</a>. Wer die Replikationsfunktion ab Version 1.4 nutzt, sollte ein Komplettupdate einschieben.</li>
    </ul>
    <br />

    <p id="v3.0.17"><strong>OC 3.0 Version 17</strong> &ndash; 7. März 2016</p>
    <p>Neu:</p>
    <ul>
        <li><a href="http://wiki.opencaching.de/index.php/Das_Onlinelog#Der_Wartungszustand" target="_blank">Wartungszustände loggen</a></li>
        <li>Koordinatenänderungen werden zwischen den Logs angezeigt. Der Cachebesitzer kann Altkoordinaten über eine Einstellung im Cachelisting schützen; die letzten drei Stellen erscheinen dann als ***.</li>
        <li>Wenn man Logs am nächsten Tag oder später ändert, erscheint unten im Log das letzte Änderungsdatum. Dies gilt nur für Änderungen ab Version 17.</li>
        <li>In Logbenachrichtigungen sind ggf. auch die Namen von Cachelisten enthalten, über die man den Cache beobachtet.</li>
        <li>Beim Melden von Caches wird der Status des Meldesystems angezeigt.</li>
        <li>OKAPI: Die eigenen Heimkoordinaten aus dem Benutzerprofil können abgefragt werden.</li>
    </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>Teile von Cachelistenbeschreibungen können nicht mehr über den Rahmen hinauslaufen. [Bug von Version 13]</li>
        <li>kleine Korrekturen bei der Suchsortierung [Bugs von Version 16]</li>
        <li>Darstellungsfehler auf diversen Seiten behoben</li>
    </ul>
    <br />

    <p id="v3.0.16"><strong>OC 3.0 Version 16</strong> &ndash; 1. Februar 2016</p>
    <p>Neu:</p>
    <ul>
        <li>Die Ergebnisse der <a href="search.php">Cachesuche</a> sind per Klick auf die Spaltenüberschriften sortierbar.</li>
        <li>Bei der Suchsortierung nach Versteckdatum wird dieses auch in den Ergebnissen angezeigt.</li>
        <li>In der <a href="ownerlogs.php">Loghistorie für die eigenen Caches</a> erscheint eine Statistik nach Logtyp.</li>
        <li>Alte Nachlogs können in der <a href="newlogs.php">Liste der neuen Logs</a> ausgeblendet werden; Liste nun wahlweise sortierbar nach Logdatum oder Einstelldatum.</li>
        <li>Anzeige von OConly-Markierungen (<img src="resource2/ocstyle/images/misc/15x15-oc.png">) in der Liste der neuen und der eigenen Logs.</li>
        <li>Es können nun auch erweiterte Unicode-Zeichen in Cachebeschreibungen, Logs, Profiltexten und Cachelistenbeschreibungen verwendet werden, zum Beispiel Smileys (&rarr; <a href="http://forum.opencaching.de/index.php?topic=4342.0">weitere Informationen</a>).</li>
        <li>OKAPI: Abfrage der eigenen Liste der ignorierten Caches</li>
    </ul>

    <p>Geändert / verbessert:</p>
    <ul>
        <li>Die Liste der neuen Logs zeigt nun das Logdatum an statt des Datums, an dem das Log eingetragen wurde.</li>
        <li>Die Links zu navicache.com wurden aus der Cachebeschreibung entfernt; es können keine Navicache-Wegpunkte mehr eingetragen werden.</li>
        <li>Nach dem Bearbeiten von Bildern in einem Cachelisting geht es zurück zur Bearbeitungsseite statt zur Listinganzeige.</li>
        <li>Bei der Wahl des Statistikbildes wird zwischen aktuellen und sonstigen OC-Logos unterschieden.</li>
        <li>Deaktivierte Listings („momentan nicht verfügbar“) werden nun bereits nach 6 statt 12 Monaten automatisch archiviert, falls sie vom OC-Team deaktiviert wurden, oder falls der Cachebesitzer seit der Deaktivierung nicht mehr eingeloggt war. Ausgenommen davon sind Listings mit dem Attribut „Nur zu bestimmten Jahreszeiten suchbar“ und Listings mit Logs nach der Deaktivierung; hier bleibt es bei der 12-Monatsfrist. Eine Änderung des Listings durch den Besitzer setzt den „Countdown“ wieder zurück. Der Besitzer kann die Archivierung jederzeit rückgängig machen.</li>
        <li>Das Einstellen von Bildduplikaten per Ocprop wird (weitgehend) verhindert.</li>
        <li>OKAPI: Umkreissuche bei kleinem Radius (&lt;&lt; 100 km) stark beschleunigt</li>
        <li>Portierung von MySQL 5.1 auf MariaDB 5.5</li>
    </ul>
    <br />

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>Anzeige des Lupensymbols, wenn man über die eigene Logbildgalerie fährt [Bug von Version 5]</li>
        <li>Anzeige des korrekten Symbols für Cachelistings, die vom Opencaching-Team gesperrt/versteckt wurden (<img src="resource2/ocstyle/images/cachestatus/16x16-locked-invisible.png">) [Bug von Version 6]</li>
        <li>Behandlung von HTML-Code in Cache-Kurzbeschreibungen korrigiert</li>
        <li>Bei der Umkreissuche nach „suchbaren“ Caches aus einem Cachelisting heraus werden deaktivierte Caches ausgeblendet. [Bug von Version 9]</li>
        <li>englische Datumsdarstellung in der Liste der neuen Caches korrigiert</li>
        <li>Zurücksetzen der Cachetypen <i>Drive-In</i> und <i>Mathe/Physik</i> sowie der Größen <i>nano</i> und <i>extrem groß</i> durch Ocprop auf GC-Werte wird verhindert. [Bug von Version 6]</li>
        <li>Abschließende Korrekturen für den Start des HTTPS-Betriebs &rarr; <a href="https://www.opencaching.de">https://www.opencaching.de</a>; noch nicht stabil verfügbar für die OKAPI (&rarr; <a href="https://github.com/opencaching/okapi/issues/343">weitere Informationen</a>)</li>
        <li>OKAPI: Fehlermeldung beim Abruf gesperrter Caches korrigiert; weitere kleine Korrekturen</li>
    </ul>
    <br />

    <p id="v3.0.15"><strong>OC 3.0 Version 15</strong> &ndash; 19. September 2015</p>
    <p>Neu:</p>
    <ul>
        <li>französische Übersetzung fertiggstellt; Relaunch von <a href="http://www.opencaching.fr">Opencaching.fr</a></li>
        <li>volle Unterstützung von HTTPS (auf Opencaching.de noch nicht aktiv)</li>
        <li>erlaubte CSS-Stile <a href="articles.php?page=htmltags#cssstyles">dokumentiert</a></li>
    </ul>

    <p>Geändert / verbessert:</p>
    <ul>
        <li>bessere Präsentation der Seiten <a href="http://www.opencaching.it" target="_blank">www.opencaching.it</a> und <a href="http://www.opencachingspain.es" target="_blank">www.opencachingspain.es</a> (<a href="http://redmine.opencaching.de/issues/816">#816</a>, <a href="http://redmine.opencaching.de/issues/818">#818</a>)</li>
        <li>Verbesserungen im Übersetzungssystem</li>
    </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>sinnvolle Textkonvertierung beim Umschalten zwischen Text- und HTML-Editor (<a href="http://redmine.opencaching.de/issues/236">#236</a>)</li>
        <li>GPX- und TXT-Download liefert Cachebeschreibungen möglichst in der gewählten Sprache (<a href="http://redmine.opencaching.de/issues/852">#852</a>)</li>
        <li>HTML-Code in Logbenachrichtigungen beseitigt</li>
        <li>&bdquo;An GPS-Gerät senden&ldquo; auf www.opencaching.it, www.opencachingspain.es und www.opencaching.fr repariert.</li>
    </ul>
    <br />

    <p id="v3.0.14"><strong>OC 3.0 Version 14</strong> &ndash; 5. September 2015</p>
    <p>Neu:</p>
    <ul>
    <li>Weitergabe privater Cachelisten mit Kennwort</li>
    <li>Lesezeichen- und Favoritenfunktion für Cachelisten</li>
    <li>Anzahl der Empfehlungen in der Übersicht der eigenen Caches hinzugefügt,
    Anzeige der Empfehlungen in verschiedenen Listenanzeigen angepasst</li>
    <li>Französische Übersetzung vorbereitet</li>
    <li>Automatisches Drehen von Hochkant-Bilder beim Hochladen
    (<a href="http://redmine.opencaching.de/issues/732">#732</a>)
    </li>
    <li>
    Suchselektion nach Sprache in der Cachebeschreibung ermöglicht gezielte Suche
    nach Listings in einer bestimmten Sprache
    (<a href="http://redmine.opencaching.de/issues/48">#48</a>)
    </li>
    <li>
    Anzeige der neuen Logs für das gewählte Land
    (<a href="http://redmine.opencaching.de/issues/183">#183</a>)
    </li>
    <li>Koordinatenänderungen in der Admin-Cache-Historie (intern)</li>
    </ul>

    <p>Geändert / verbessert:</p>
    <ul>
    <li>Links auf Wiki und Geokrety.org rufen jetzt lokalisiertes Ziel auf</li>
    <li>Versand von Benachrichtigungs-Emails in der Sprache des Benutzers
    (<a href="http://redmine.opencaching.de/issues/813">#813</a>)
    </li>
    <li>Automatisches Nachladen von Logeinträgen für nicht eingeloggte Benutzer
    (<a href="http://redmine.opencaching.de/issues/785">#785</a>)
    </li>
    <li>Smilies im HTML-Editormodus einfügen</li>
    <li>Irrtümliches Anlegen dänischer Cachebeschreibungen vermeiden</li>
    <li>Neben deutschen Umlauten sind nun weitere europäische Buchstaben
    in Benutzernamen erlaubt.
    (<a href="http://redmine.opencaching.de/issues/212">#212</a>)
    </li>
    <li>JavaScript-Verbesserung: Icons in der Karten-Popups erscheinen jetzt auch
    in defekten Browsern. (<a href="http://redmine.opencaching.de/issues/11">#11</a>)
    </li>
    <li>
    Englische Übersetzung vervollständigt (
    <a href="http://redmine.opencaching.de/issues/163">#163</a>,
    <a href="http://redmine.opencaching.de/issues/184">#184</a>,
    <a href="http://redmine.opencaching.de/issues/817">#817</a>)
    </li>
    </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
    <li>Falschschreibung von Ortsnamen korrigiert
    (<a href="http://redmine.opencaching.de/issues/175">#175</a>)</li>
    <li>Flaggen in den Cachebeschreibungen durch Bezeichnungen der Sprache
    ersetzt</li>
    <li>Korrektur des in seltenen Fällen falsch angezeigten Datums einer
    Empfehlung
    (<a href="http://redmine.opencaching.de/issues/713">#713</a>) [Bug von Version 9]</li>
    <li>HTML-Filterfunktionen für Logs und Cachebeschreibungen verbessert
    (<a href="http://redmine.opencaching.de/issues/79">#79</a>),
    eine Liste der jetzt verwendbaren HTML-Elemente ist
    <a href="http://www.opencaching.de/articles.php?page=htmltags">hier</a>
    zu finden.
    </li>
    <li>
    Popup-Fenster des HTML-Editors werden bei großer Browser-Schrift nicht
    mehr abgeschnitten.
    (<a href="http://redmine.opencaching.de/issues/300">#300</a>)
    </li>
    <li>
    Nach dem Entfernen eines Eintrags aus der Liste ignorierter Caches landet man wieder
    auf der richtigen Seite
    (<a href="http://forum.opencaching.de/index.php?topic=4214">siehe Forum</a>).
    </li>
    <li>
    Fehler bei Hint-Eingabe in englischer und spanischer Sprache behoben
    (<a href="http://redmine.opencaching.de/issues/746">#746</a>, Ticket)
    </li>
    <li>
    OKAPI aktualisiert jetzt Login-Datum
    (<a href="http://forum.opencaching.de/index.php?topic=4260.0">siehe Forum</a>)
    </li>
    <li>
    OKAPI-Absturz bei '|' im Benutzernamen behoben
    (<a href="https://github.com/opencaching/okapi/issues/339">siehe github-Issue</a>)
    </li>
    <li>
    Direktanzeige von Suchergebnissen in Google Maps entfällt
    (<a href="http://redmine.opencaching.de/issues/31">#31</a>)
    </li>
    <li>
    Benachtichtiungsfunktion für neu gesetzte OConly-Attribute korrigiert
    (<a href="http://redmine.opencaching.de/issues/810">#810</a>) [Bug von Version 9]
    </li>
    <li>Außerdem wurde viele weitere, kleinere Fehler behoben - vollständige Liste
    <a href="http://redmine.opencaching.de/versions/10">hier</a>.</li>
    </ul>
    <br />

    <p id="v3.0.13"><strong>OC 3.0 Version 13</strong> &ndash; 4. Juli 2015</p>
    <p>Neu:</p>
    <ul>
        <li><a href="cachelists.php">Cachelisten</a></li>
        <li>Filtermöglichkeit für Caches mit Geokrets bei der Suche und auf der Karte</i></li>
        <li>Link zur Safari-Cache-Liste auf der Karte</li>
    </ul>

    <p>Geändert / verbessert:</p>
    <ul>
        <li>Bilder bis zu 250 KB Größe bleiben unverändert, erst bei &gt; 250 KB wird verkleinert.</li>
        <li>Um einen noch nicht veröffentlichten Cache zu veröffentlichen genügt es, unten im Listing „sofort veröffentlichen“ zu wählen.
        <li>Beobachtungs- und Ignorierlisten deaktivierter Benutzer werden gelöscht.</li>
        <li>kleine Design-Verbesserungen</li>
        <li>Liste der OC-Länderseiten aktualisiert: Rumänien ist neu, Schweden/Norwegen wurde eingestellt</li>
        <li>interne Verbesserungen für das Datenpflegeteam</li>
    </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>zuverlässigeres Ausblenden von GC-Doppellistings bei der Cachesuche und auf der Karte [Bug von Version 9]</li>
        <li>Kennzeichnung neuer Caches in der Suchergebnisliste korrigiert [Bug von Version 9]</li>
        <li>Attributanzeige im Internet Explorer korrigiert [Bug von Version 11]</li>
        <li>Event-Icons in der Logstatistik korrigiert</li>
        <li>GC- und NC-Wegpunkteingabe korrigiert [Bug von Version 12]</li>
    </ul>
    <br />

    <p id="v3.0.12"><strong>OC 3.0 Version 12</strong> &ndash; 31. Mai 2015</p>
    <p>Neu:</p>
    <ul>
        <li>Link „mehr...“ in der Eventliste auf der Startseite, falls mehr als zehn Events oder Events in anderen Ländern vorhanden sind</li>
        <li>Der Link „andere Koordinatensysteme“ im Listing zeigt auch <i>what3words</i>-Koordinaten an.</i></li>
        <li>Suche nach <i>what3words</i>-Koordinaten auf der Karte</li>
        <li>In den <a href="myprofile.php">Profildaten</a> kann man festlegen, dass beim Anschreiben anderer Benutzer standardmäßig die eigene Mailadresse mitgeschickt wird.</li>
        <li><a href="okapi">OKAPI</a>: experimentelle Unterstützung des neuen Garmin-GGZ-Datenformats</li>
    </ul>

    <p>Geändert / verbessert:</p>
    <ul>
        <li>versehentliche Doppellistings des gleichen Caches werden verhindert</li>
        <li>Plausibiltitätsprüfung für GC-Wegpunkteingabe im Listing</li>
        <li>Benachrichtigungsmails über gelöschte Logs enthalten nun auch Logdatum und -typ</li>
        <li>zuletzt eingegebenes Logdatum wird nur noch 12 Stunden lang für neue Logs vorgeschlagen, danach wieder das aktuelle Datum</li>
        <li>Persönliche Notiz wird auch bei Eingabe einer ungültigen Koordinate gespeichert</li>
        <li>Informationen zum Opencaching Deutschland e.V. aktualisiert</li>
    </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>Der An-GPS-Gerät-Senden-Knopf im Listing funktioniert wieder.</li>
        <li>Deaktivierte Events werden wie deaktivierte Caches nach einem Jahr automatisch archiviert. [Bug von Version 9]</li>
        <li>fehlende eigene Mailadresse beim Anschrieb anderer Benutzer ergänzt</li>
        <li>Fehler bei der Sprachumschaltung auf www.opencaching.it und www.opencachingspain.es behoben [Problem seit Version 10]</li>
        <li>kleinere Korrekturen an der OKAPI (&rarr; <a href="https://code.google.com/p/opencaching-api/source/list">Changelog</a>)</li>
    </ul>
    <br />

    <p id="v3.0.11"><strong>OC 3.0 Version 11</strong> &ndash; 21. Juni 2014</p>
    <p>Neu:</p>
    <ul>
        <li>direkter Link auf ein bestimmtes Log per Rechtsklick auf das zugeh&ouml;rige Symbol und „Link kopieren“</li>
        <li>Bilder beim Upload automatisch verkleinern</li>
        <li>R&uuml;ckfrage bevor ein Bild gel&ouml;scht wird</li>
        <li>Link „Umkreissuche auf geocaching.com“ im Adminbereich f&uuml;r gemeldete Caches</li>
        <li>Benutzeroption um den OC Newsletter zu erhalten (oder nicht)</li>
    </ul>

    <p>Geändert / verbessert:</p>
    <ul>
        <li>Buttondesign im IE verbessert</li>
        <li>Link zum Listing auf der Listingseite mit http:// versehen, um es als Link auf Webseiten nutzbar zu machen</li>
    </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>Positionierung des Hilfe-Buttons im IE verbessert</li>
        <li>Erstellung des direkten Links zu einem Logeintrag nach dem Laden aller Logs korrigiert [Bug von Version 10]</li>
        <li>einige Tippfehler in der deutschen &Uuml;bersetzung und der Teamliste korrigiert</li>
    </ul>
    <br />

    <p id="v3.0.10"><strong>OC 3.0 Version 10</strong> &ndash; 24. August 2013</p>
    <p>Neu:</p>
    <ul>
        <li>Wenn mehr als 5 Logeinträge vorhanden sind und man zum Ende des Listings blättert, werden die übrigen Logs automatisch nachgeladen.</li>
        <li>regionale Fundstatistik im Benutzerprofil</li>
        <li>OConly-Statistik im Benutzerprofil</li>
        <li><a href="oconly81.php">OConly-81</a></li>
        <li>&bdquo;alle&ldquo;-Funktion in der Liste der eigenen Logs</li>
        <li>zwischen den Bildern eines Logeintrags kann geblättert werden</li>
        <li>Verlinkung von Social-Media-Angeboten im &bdquo;Sidebar&ldquo; (links unten)</li>
        <li>Erläuterung der Cachegrößen auf der <a href="articles.php?page=cacheinfo">Beschreibungsseite</a></li>
    </ul>

    <p>Geändert / verbessert:</p>
    <ul>
        <li>Hyperlink-Design überarbeitet</li>
        <li>übersichtlichere Darstellung in der Listing-Druckansicht</li>
        <li>zahlreiche kleine Verbesserungen von Seitendarstellung und -layout</li>
        <li>interne Komplettüberarbeitung der CSS-Style-Sheets</li>
        <li>Startseite für www.opencaching.it und www.opencachingspain.es überarbeitet</li>
    </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>diverse Darstellungsfehler im Microsoft Internet Explorer korrigiert</li>
        <li>Zeilenabstände bei großer Schrift in Cachebeschreibungen korrigiert</li>
        <li>Bildtitel bei Logbildern wird immer angezeigt [Bug von Version 5]</li>
        <li>diverse Syntaxfehler im HTML-Code korrigiert</li>
        <li>Anzeige von Empfehlungssternen in Loglisten korrigiert [Bug von Version 9]</li>
        <li>mehrere Fehler beim Herunterladen von Suchergebnissen korrigiert [Bugs von Version 9]</li>
        <li>Rechtschreibkorrekturen in den OKAPI-Attributbeschreibungen [eingeführt mit Version 9]</li>
    </ul>
    <br />

    <p id="v3.0.9"><strong>OC 3.0 Version 9</strong> &ndash; 25. Juli 2013</p>
    <p><a href="search.php">Suchfunktion</a> überarbeitet:</p>
    <ul>
        <li>neues, übersichtlicheres Design</li>
        <li>Suchergebnisse auf Karte anzeigen</li>
        <li>deaktivierte und archivierte Caches sind separat ausblendbar (auch auf der Karte)</li>
        <li>Ausblendoption für Mehrfachlistings geändert in GC-Listings; dabei werden neben den Angaben des Owners zusätzliche, vom OC-Team eingepflegte GC-Wegpunkte berücksichtigt (auch auf der Karte).</li>
        <li>Cachearten oder -größen schneller auswählbar durch neue Schalter &bdquo;alle&ldquo; / &bdquo;keine&ldquo;</li>
        <li>Suche nach allen von einem Benutzer geloggten Caches (bisher nur einzeln nach Logtyp)</li>
    </ul>

    <p>Neues in der <a href="myhome.php#mycaches">Liste eigener Caches</a>:</p>
    <ul>
        <li>Anzeige von Cachetyp, Fundzahl und Typ/Datum des letzten Logs</li>
        <li>archivierte und gesperrte Caches sind ausblendbar</li>
        <li>Anzeige aller Caches statt nur der neuesten 20</li>
        <li><a href="ownerlogs.php">Logübersicht</a> für alle eigenen Caches</li>
    </ul>

    <p>Weitere neue Features:</p>
    <ul>
        <li>Empfehlungen werden in allen Loglisten mit <img src="images/rating-star.gif" /> markiert.</li>
        <li>OConly-Caches werden in allen Cachelisten mit <img src="resource2/ocstyle/images/misc/15x15-oc.png" /> markiert.</li>
        <li>OConly-Hinweis in Benachrichtigungen über neue Caches; Benachrichtigung über neu markierte OConlys im Profil aktivierbar</li>
        <li>Informationen über <a href="http://wiki.opencaching.de/index.php/Schutzgebiete">Schutzgebiete</a> in GPX-, OKAPI- und <a href="http://wiki.opencaching.de/index.php/XML-Schnittstelle">XML-Interface</a>-Downloads</li>
        <li>neuer Menüpunkt &bdquo;neue Features&ldquo; auf der Startseite</li>
        <li>neuer Menüpunkt &bdquo;neue Wiki-Artikel&ldquo; auf der Startseite</li>
        <li>Deaktivierte Caches werden nach einem Jahr automatisch archiviert, Events nach fünf Wochen.</li>
        <li>laufende Auswertung und Vermeidung unzustellbarer Emails; siehe auch Version 5 / unzustellbare Emails</li>
        <li>OKAPI: Abfrage von Cacheattributen über OKAPI-eigene, für alle OC-Seiten einheitliche Attribut-IDs</li>
        <li>OKAPI: GC- und OC.de-kompatible Cacheattribute in GPX-Dateien</li>
    </ul>

    <p>Geändert / verbessert:</p>
    <ul>
        <li>Bei Eingabe mehrerer Logs wird jeweils das Datum des letzten Logs vorgeschlagen.</li>
        <li>übersichtlicheres Startseiten-Menü, aufgeteilt in &bdquo;Aktuelles&ldquo; und &bdquo;Opencaching&ldquo;</li>
        <li>Anzeige eigener unveröffentlichter Caches auf der Karte (kann eine Stunde dauern, bis sie erscheinen)</li>
        <li>deutlichere Kennzeichnung neuer Caches in Suchergebnislisten; Neu-Zeitraum von 7 auf 14 Tage erweitert</li>
        <li>Probleme mit der Darstellung von Umlauten auf einigen Garmin-Geräten behoben</li>
        <li>Garmin-Download-Fenster kann mit nur einem Klick auf OK geschlossen werden</li>
        <li>einfachere Bestätigung der Benutzerregistrierung mit nur einem Klick</li>
        <li>Ausblenden ungültiger Dutch-Grid-Koordinaten bei &bdquo;andere Koordinatensysteme&ldquo; im Cachelisting</li>
        <li><a href="mytop5.php">eigene Empfehlungsliste</a> wird nach Datum sortiert</li>
        <li>neues OC.de-Logo an weiteren Stellen eingebaut</li>
        <li>Status-Logs (siehe Version 8) werden auch durch <a href="http://wiki.opencaching.de/index.php/Ocprop">Ocprop</a> und beim Deaktivieren von Benutzerkonten erzeugt</li>
        <li>diverse Verbesserungen auf der Adoptionsseite</li>
        <li>umfangreiche interne Umstrukturierungen / Aufräumarbeiten</li>
    </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>Inkonsistenzen in Geokret-Daten behoben (Problem mit als verloren gemeldeten Geokrets besteht noch)</li>
        <li>Seitenlayout bei Anzeige von Bildern in der Liste der neuen Logs korrigiert [Bug von Version 5]</li>
        <li>Noch ein Problem bei der Darstellung von Bildern mit ' im Titel behoben [Bug von Version 5]</li>
        <li>Übernahme von OC-Benutzernamen in den <a href="webchat.php">Chat</a> korrigiert</li>
        <li>Versionsinkonsistenz in GPX-Dateien behoben durch vollständige Umstellung auf Groundspeak-Version 1.0.1</li>
        <li>Datumsangabe in Email-Adress-Erinnerungsmails korrigiert</li>
        <li>Ausgabe von Empfehlungen in der XML-Schnittstelle korrigiert</li>
        <li>diverse OKAPI-Bugs korrigiert</li>
  </ul>
    <br />

    <p id="v3.0.8"><strong>OC 3.0 Version 8</strong> &ndash; 1. Juni 2013</p>
    <p>Neu:</p>
    <ul>
        <li>Der Cachestatus kann (nur noch) per Log geändert werden. Dazu gibt es die neuen Logtypen <em>momentan nicht verfügbar</em>, <em>archiviert</em>, <em>gesperrt</em> und <em>kann gesucht werden</em>. Der gleiche Status kann auch mehrmals geloggt werden, z.B. um zu signalieren dass mit dem Cache alles ok ist. Der Typ alter Logs kann nachträglich geändert werden.</li>
        <li>Benachrichtungen über Statusänderungen durch die neuen Statuslogs</li>
        <li>HTML-Beschreibung im Benutzerprofil, mit komfortablem Editor</li>
        <li><a href="http://wiki.opencaching.de/index.php/Reverse_%28%E2%80%9ELocationless%E2%80%9C%29_Caches">Safari-Caches</a></li>
        <li>Karte der neuesten Caches unten auf der Startseite</li>
        <li>zusätzliche Wegpunkte und persönliche Notiz in Listing-Ausdrucken</li>
        <li>Link &bdquo;geloggte Caches&ldquo; im Benutzerprofil; Auflistung der eigenen Logs sortiert nach Logdatum</li>
        <li>Zahl der aktiven Caches im Benutzerprofil, + Link &bdquo;anzeigen&ldquo;</li>
        <li>Suchergebnisse sind nach Datum des letzten eigenen Logs sortierbar; in der Suchergebnisliste erscheinen dann rechts nur die eigenen Logs</li>
        <li>Kartenfilter-Einstellungen sind nun permanent speicherbar</li>
        <li>OC-Supportmitarbeiter können ihr Logs als &bdquo;OC-Team-Log&ldquo; (<img src="resource2/ocstyle/images/oclogo/oc-team-comment.png" />) markieren.</li>
        <li>neuer Menüpunkt <a href="okapi/apps/">API-Anwendungen</a> im Benutzerprofil, zur Kontrolle von <a href="okapi">OKAPI</a>-Anwendungsrechten</li>
        <li><a href="http://wiki.opencaching.de/index.php/XML-Schnittstelle">XML-Interface</a>: zusätzliche Wegpunkte, Loguhrzeit, OC-Team-Log-Flag und Vorschaubild-Flag werden mitgeliefert</li>
        <li>OKAPI: GC-Codes von Caches und OC-Team-Log-Flag sind abfragbar</li>
        <li>OKAPI: Bilder in GPX-Dateien sind als &bdquo;Thumbnail&ldquo; einbettbar</li>
        <li>neue <a href="404.php">Fehlerseite</a> für ungültige Seitenabrufe</li>
    </ul>

    <p>Geändert / verbessert:</p>
    <ul>
        <li>Benutzerprofileinstellungen überarbeitet/vereinfacht</li>
        <li>Layout von Cachelisten im Benutzerprofil und von  Suchergebnislisten überarbeitet</li>
        <li>Beim Anlegen zusätzlicher Wegpunkte werden die Cachekoordinaten vorgeschlagen.</li>
        <li>Bei Loglöschungen wird auch der gelöschte Text und die Cache-URL mitgeschickt.</li>
        <li>Inaktive Caches werden in Suchlisten durchgestrichen.</li>
        <li>Layout/Design des Cachelisting-Kopfes überarbeitet, u.a. mit Anzeige der Kurz-URL, übersichtlicheren Druckbuttons und schönerer Wegstreckenanzeige</li>
        <li>Abgelaufene Events werden auf der Karte grau dargestellt, wie inaktive Caches.</li>
        <li>Zurücksetzen-Buttons aus allen Dialogen rausgeworfen; Ändern-Buttons in &bdquo;Speichern&ldquo; umbenannt</li>
        <li>Unveröffentlichte und gesperrt/versteckte Caches zählen nicht mehr in der Versteckstatistik mit.</li>
        <li>Log-, Beobachten- und Melde-Buttons sind auch für nicht angemeldete User sichtbar.</li>
        <li><a href="webchat.php">Chat</a> direkt in die Opencaching.de-Seite integriert</li>
        <li>maximale Größe für hochgeladene Bilder von 150 auf 250 KB erhöht</li>
        <li>(Nicht-)Ignorieren von Caches wirkt sich sofort auf die Kartendarstellung aus, statt wie bisher zeitverzögert.</li>
        <li>breiteres Editorfeld für Cachebeschreibungen</li>
        <li>Anzeige des Empfehlungsdatums in der <a href="mytop5.php">Empfehlungsliste</a></li>
        <li>Unterscheidung zwischen &bdquo;möchte teilnehmen&ldquo; und &bdquo;teilgenommen&ldquo; in Event-Teilnehmerlisten</li>
        <li>Update der <a href="articles.php?page=verein">Vereinsseite</a> und des Mitgliedsantrags</li>
        <li>Owner und OC-Supportmitarbeiter sehen gesperrt/versteckte Caches in Suchlisten.</li>
        <li>Verbesserungen für das Supportteam, insbesondere beim Abarbeiten von Cachemeldungen</li>
        <li>Suchmaschinenoptimierung (HTML Meta keywords &amp; description)</li>
    </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>Event-Log-Icons (<img src="resource2/ocstyle/images/log/16x16-will_attend.png" /> <img src="resource2/ocstyle/images/log/16x16-attended.png" />) in Suchlisten</li>
        <li>Bei Suchsortierung nach letzten Log fehlten alle ungeloggten Caches außer einem.</li>
        <li>Bildanzeigeproblem bei ' im Bildtitel behoben [Bug von Version 5]</li>
        <li>nicht funktionierenden in-GM-Link (Anzeige in Google Maps) beim Abruf gespeicherter Suchen entfernt</li>
        <li>seltene Fehlermeldungen nach dem Zurückziehen von Bewertungen beseitigt</li>
        <li>Datumsangabe in Email-Adress-Erinnerungsmails korrigiert</li>
        <li>Layoutkorrektur bei der Hint-Decodiertabelle</li>
        <li>Layout der Startseiten-Cachelisten im Internet Explorer korrigiert</li>
        <li>Persönliche Notizen verändern nicht mehr das Listing-Änderungsdatum; Datum der betroffenen Caches korrigiert. [Bug von Version 6]</li>
        <li>&bdquo;Cache verstecken&ldquo; führt nicht angemeldete User wieder auf die Loginseite. [Bug von Version 5]</li>
        <li>XML-Interface-DTDs korrigiert</li>
        <li>diverse OKAPI-Korrekturen</li>
        <li><a href="http://wiki.opencaching.de/index.php/Ocprop">Ocprop</a>-Problem beim Abgleich von Logs behoben [entstanden kurz nach Freigabe von Version 7]</li>
  </ul>
    <br />

    <p id="v3.0.7"><strong>OC 3.0 Version 7</strong> &ndash; 19. April 2013</p>
    <ul>
        <li>Neu: <a href="okapi">OKAPI</a></li>
        <li>&bdquo;Apple-Touch-Icons&ldquo; für Smartphones</li>
        <li>Beim Loggen eigener Caches wird &bdquo;Hinweis&ldquo; als Logtyp vorgeschlagen statt wie bisher &bdquo;gefunden&ldquo;.</li>
    </ul>
    <br />

    <p id="v3.0.6"><strong>OC 3.0 Version 6</strong> &ndash; 12. April 2013</p>
    <p>Neu:</p>
    <ul>
        <li>Loggen mit Uhrzeit</li>
        <li>neuer Menüpunkt &bdquo;Über Opencaching&ldquo; auf der Startseite</li>
        <li>neuer Menüpunkt &bdquo;Neue Logs / ohne Deutschland&ldquo; auf der Startseite</li>
        <li>neuer Menüpunkt &bdquo;Öffentliches Profil&ldquo; auf der Profilseite</li>
        <li>Logout-Knopf in der Vollbildkarte</li>
        <li>Datenlizenz-Anzeige links auf jeder Seite; Datenlizenz-Disclaimer wird in GPX-Dateien, TXT-Dateien und via <a href="http://wiki.opencaching.de/index.php/XML-Schnittstelle">XML-Schnittstelle</a> mitgeliefert.</li>
        <li>Symbole für gesperrte und unveröffentlichte Caches auf Benutzerprofil-Seiten</li>
        <li>RSS-Newsfeed-Link auf der Startseite</li>
        <li><a href="http://wiki.opencaching.de/index.php/Listingvandalismus">Listingvandalismus</a> kann vom Opencaching-Supportteam rückgängig gemacht werden</li>
    </ul>

    <p>Geändert / verbessert:</p>
    <ul>
        <li>Designverbesserungen bei den Listen neuer Caches und Logs</li>
        <li>In Benachrichtigungsmails werden die neuen Opencaching-Kurzlinks verwendet (siehe Version 1).</li>
        <li>doppeltes Einstellen identischer Logs wird verhindert</li>
        <li>genauere Zuordnung der Attribute zu GC-Attributen in GPX-Dateien</li>
        <li>Gezeiten-Attribut umdefiniert in &bdquo;nicht bei hohem Wasserstand&ldquo;</li>
        <li>neues Opencaching.de-Logo auf Listing-Ausdrucken</li>
        <li>News- und Forentexte auf der Startseite ausgeblendet, dafür doppelt so viele Einträge</li>
        <li>Teamliste aktualisiert</li>
        <li>Der Zähler für versandte Emails im Benutzerprofil entfällt aus technischen Gründen.</li>
  </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>Einblenden ignorierter Caches auf der Karte funktioniert jetzt (erstmals)</li>
        <li>Logpasswörter funktionieren jetzt auch bei Event-Caches</li>
        <li>Änderungen von zusätzlichen Wegpunkten und Bildern werden beim Änderungsdatum des Listings berücksichtigt.</li>
        <li>Cachesuche funktionierte manchmal unmittelbar nach dem Ausloggen nicht</li>
        <li>korrekte Typbezeichung zusätzlicher Wegpunkte (z.B. &bdquo;Parkplatz&ldquo;) in GPX-Dateien [Bug von Version 4]</li>
        <li>Beim Löschen von Logs werden auch die Bilder mitgelöscht; bisher blieben sie irgendwo im System stehen.</li>
        <li>Problem mit <a href="http://wiki.opencaching.de/index.php/Ocprop">Ocprop</a>-Logduplikate behoben [entstanden durch Version 5]</li>
        <li>Logeditor auf der englischen, italienischen und spanischen Seite in korrekter Sprache</li>
        <li>Übersetzung von Ländernamen (in denen die Caches liegen) auf der englischen, italienischen und spanischen Seite</li>
  </ul>
    <br />

    <p id="v3.0.5"><strong>OC 3.0 Version 5</strong> &ndash; 16. März 2013</p>
    <p>Karte komplett überarbeitet:</p>
    <ul>
        <li>Markierung von eigenen, gefundenen, nicht gefundenen und <a href="http://wiki.opencaching.de/index.php/OConly" target="_blank">OConly</a>-Caches</li>
        <li><a href="map2.php?mode=fullscreen">Vollbildkarte</a> mit ausklappbaren Filtereinstellungen</li>
        <li><a href="http://wiki.opencaching.de/index.php/Zusätzliche_Wegpunkte" target="_blank">zusätzliche Wegpunkte</a> des gewählten Caches werden angezeigt</li>
        <li>bis zu 4000 Caches auf einer Karte</li>
        <li>Home-Button springt zu den im <a href="myprofile.php">Profil</a> eingetragenen Heimkoordinaten</li>
        <li>alternative Cachesymbole wählbar (Opencaching.pl-Stil)</li>
        <li>pro Cache ist ein <a href="http://wiki.opencaching.de/index.php?title=Vorschaubilder" target="_blank">Vorschaubild</a> einstellbar, das bei Auswahl des Caches auf der Karte erscheint</li>
        <li>Verbesserungen bei der Cacheauswahl / Filter:
            <ul>
                <li>komfortablere Auswahl von einzelnen Cachetypen oder -größen</li>
                <li>Vorauswahl der wichtigsten Attribute wie auf der <a href="search.php">Suchseite</a></li>
                <li>Änderungen werden automatisch übernommen, ohne nochmal &bdquo;Ok&ldquo; klicken zu müssen</li>
                <li>Einstellungen bleiben bis zum Beenden des Browsers erhalten</li>
            </ul>
        </li>
        <li>schönere Popup-Fenster mit OConly-Icons, Schwierigkeits- und Geländesymbolen und größeren Cachesymbolen</li>
        <li>OConly-Caches werden oben angezeigt; inaktive und (nicht) gefundene im Hintergrund</li>
        <li>besser lesbare Koordinatenanzeige</li>
        <li>Bedienung der Suchfunktion verbessert</li>
        <li>schnellerer Abruf der Cachedaten</li>
        <li>zusätzliches Katenmaterial von <a href="http://www.thunderforest.com/opencyclemap/" target="_blank">OpenCycleMap</a> und <a href="http://www.mapquest.de/" target="_blank">MapQuest</a></li>
        <li>interne Umstellung von Google Maps Version 2 auf Version 3; <span class="redtext">im Microsoft Internet Explorer recht träge, Verwendung eines anderen Browsers wird empfohlen</span></li>
    </ul>

    <p>Sonstige Neuerungen und Änderungen:</p>
    <ul>
        <li>Auf den meisten Seiten gibt es nun rechts oben einen Hilfe-Knopf, der auf eine passende Seite im <a href="http://wiki.opencaching.de/" target="_blank">Opencaching-Wiki</a> verweist.</li>
        <li>Logbild-Galerien auf der Startseite, der neuen <a href="newlogpics.php">Galerieseite</a>, in den Cachelistings (erreichbar per Link &bdquo;Logbilder&ldquo;) und in den Benutzerprofilen. Die Profil-Bildgalerien sind per <a href="mydetails.php">Profileinstellungen</a> abschaltbar.</li>
        <li>Spoileroption für Logbilder reaktiviert (vgl. Version 2)</li>
        <li>Titel und Spoilereinstellung von Logbildern ist nachträglich änderbar</li>
        <li>Bilder werden in einem schicken Popup-Fenster dargestellt statt auf einer separaten Seite.</li>
        <li>neue Wegpunkttypen <em>Pfad</em>, <em>Ziel</em> und <em>interessanter Ort</em></li>
        <li>Bei unzustellbaren Emails erhalten Benutzer ggf. eine Aufforderung, ihre Mailadresse zu ändern oder zu bestätigen.</li>
        <li>Inaktive Caches werden in der Beobachtungsliste <s>durchgestrichen</s>, wie bereits in der Ignorierliste.</li>
        <li>verbesserte Bildeinbettung in GPX-Dateien, nun alles wie im Originallisting</li>
        <li>viele Detailverbesserungen bei Formuarlayouts</li>
        <li>alte HTML-Vorschaufunktion entsorgt</li>
    </ul>

    <p>Korrigiert (Bugfixes):</p>
    <ul>
        <li>Handhabung von Nano-Caches bei gespeicherten Suchen korrigiert [Bug von Version 4]</li>
        <li>Auswahl der Nano-Größe im Suchforumlar korrigiert [Bug von Version 4]</li>
        <li>Javascript-Warnung beim Loggen auf der italenischen Seite beseitigt</li>
        <li>dänische Flagge bei dänischen Cachebeschreibungen</li>
    </ul>

     <p id="v3.0.4"><strong>OC 3.0 Version 4</strong> &ndash; 17. Februar 2013</p>
   <p>Neu:</p>
     <ul>
     <li>neue Cachegröße &bdquo;nano&ldquo;</li>
     <li><a href="http://wiki.opencaching.de/index.php/Wegpunkte#Interne.2C_zus.C3.A4tzliche_Wegpunkte" target="_blank">Zusätzliche Wegpunkte</a> sind in <a href="http://wiki.opencaching.de/index.php/Wie_erhalte_ich_eine_GPX-Datei%3F" target="_blank">GPX-Dateien</a> enthalten und werden ans GPS-Gerät gesendet</li>
     <li>Bilder (inkl. Spoilerbildern) aus Cachelistings werden in GPX-Dateien eingebettet. Um sie unterwegs sehen zu können, ist eine Internetverbindung nötig.</li>
     <li><a href="articles.php?page=impressum#datalicense">Datenlizenz CC-BY-NC-ND</a></li>
     <li>Statistikbilder mit neuem Logo</a></li>
   </ul>

     <p>Geändert / verbessert:</p>
     <ul>
     <li>neues Design des Seitenkopfes mit neuem Logo</a></li>
     <li>Die <a href="map2.php">Karte</a> zeigt jetzt bis zu 600 statt 180 Cachesymbole an (MS Internet Explorer: bis zu 200).</a></li>
     <li>auch Hamburg ist jetzt im Menü der <a href="map2.php">Kartenseite</a> direkt anwählbar</a></li>
     <li>einheitliche Darstellung in der <a href="newlogs.php">Liste neuer Logs</a></li>
     <li>verbesserte Benutzerführung beim Ändern von Profildetails, Emailadresse oder Passwort</li>
     <li>verbesserte Verwaltung von Cachemeldungen (intern)</a></li>
     <li>Suchmaschinenoptimierung</a></li>
     <li><a href="articles.php?page=team">Team-</a> und <a href="articles.php?page=donations">Spendenseite</a> aktualisiert</a></li>
     <li><a href="articles.php?page=dsb">Datenschutzbelehrung</a> aktualisiert; Verbesserungen beim Datenschutz</a></li>
     <li>diverse interne Änderungen zur Umstellung von PHP 5.2 auf 5.3</li>
   </ul>

     <p>Korrigiert (Bugfixes):</p>
     <ul>
     <li>explizite Länderangabe für Cachelistings hat Vorrang vor automatischer Erkennung via Koordinaten</li>
     <li>Icons für Event-Logs in der <a href="newlogs.php">Liste neuer Logs</a></li>
   </ul>
    <br />

     <p id="v3.0.3"><strong>OC 3.0 Version 3</strong> &ndash; 18. November 2012</p>
   <p>Neu:</p>
     <ul>
     <li>Attribut &bdquo;nur zu bestimmten Jahreszeiten&ldquo;</li>
     <li>Anzeige der neuesten Forenbeiträge auf der Startseite</li>
   </ul>

     <p>Geändert / verbessert:</p>
     <ul>
     <li>Hilfsseiten ins <a href="http://wiki.opencaching.de/">Wiki</a> ausgelagert</li>
     <li><a href="./articles.php?page=team">Teamliste</a> aktualisiert</li>
     <li>Suchmaschinenoptimierung</li>
     <li>interne Softwarekonfiguration vereinfacht</li>
   </ul>

     <p>Korrigiert (Bugfixes):</p>
     <ul>
     <li>Fehler bei Wegpunktgenerierung behoben</li>
     <li>Fehler beim Speichern eines unveränderten Benutzerprofils behoben</li>
     <li>Menüdarstellung im ausgeloggten Zustand korrigiert</li>
     <li>GC-Wegpunktesuche funktioniert auch bei Duplikaten</li>
     <li>Layoutkorrekturen im Benutzerprofil und Suchformular</li>
   </ul>
    <br />

     <p id="v3.0.2"><strong>OC 3.0 Version 2</strong> &ndash; 26. August 2012</p>
   <p>Neu:</p>
     <ul>
       <li><a href="./articles.php?page=cacheinfo#difficulty">Schwierigkeitsgrade</a> erklärt, inklusive Tooltip und Link in den Cachelistings</li>
       <li><a href="./articles.php?page=verein">Vereinsseite</a>
   </ul>
     <p>Geändert / verbessert:</p>
     <ul>
     <li><a href="./articles.php?page=cacheinfo">Cachebeschreibungs-Info</a> überarbeitet</li>
     <li><a href="./doc/xml/">XML-Interface-Doku</a> und <a href="https://github.com/OpencachingDeutschland/oc-server3/blob/master/doc/license.txt">Quellcodelizenz</a> aktualisiert</li>
       <li><a href="./articles.php?page=team">Teamliste</a> aktualisiert</li>
       <li>neue <a href="./articles.php?page=donations">Bankverbindung</a> bekanntgegeben</li>
       <li>übersichtlichere Darstellung von Cachemeldungen für das OC-Supportteam</li>
       <li>Platz für neue Caches: Beschränkung auf 65535 Listings (OCFFFF, inkl. archivierter Caches) aufgehoben</li>
         <li>spanische und italienische Übersetzung vervollständigt</li>
         <li>inaktive Caches auch auf der <a href="./newcachesrest.php">Alle-außer-Deutschland-Seite</a> ausgeblendet</li>
         <li><a href="./index.php">Startseite</a> beschleunigt</li>
       <li>einheitliche Schreibweise für Attributnamen</li>
         <li>Cachelisting: &bdquo;Decrypt&ldquo; &rarr; &bdquo;Entschlüsseln&ldquo;</li>
       <li>bei gesperrten Caches den Logbutton ausgeblendet, statt auf eine leere Seite zu verlinken</li>
       <li>Empfehlungssterne erscheinen nur noch bei Gefunden- und Teilgenommen-Logs.</li>
       <li>Logtypreihenfolge bei Event-Caches umgedreht</li>
   </ul>
     <p>Korrigiert (Bugfixes):</p>
     <ul>
       <li>Anzeige von &bdquo;nicht gefunden&ldquo; und &bdquo;veröffentlicht am&ldquo; in der Druckansicht</li>
       <li>Anzeige der Event-Teilnehmerzahl in der Logzusammenfassungszeile</li>
       <li>Überschreiben von gespeicherten Suchen funktioniert jetzt</li>
       <li>seltenen Fehler bei der Erzeugung von OC-Wegpunkten behoben</li>
       <li>Empfehlungen gehen beim mehrfachen Loggen eines Caches &ndash; z.B. Fund + Hinweis &ndash; nicht mehr verloren.</li>
       <li>Empfehlungen gehen beim Löschen eines von mehreren Logs des gleichen Benutzers oder beim Bearbeiten von einem der Logs nicht mehr verloren.</li>
       <li>Mehrfachlogs eines Benutzers zählen bei der Bewertungsübersicht auf der Startseite nur noch einmal.</li>
       <li><a href="doc/xml/">Das XML-Interface</a> schneidet im Standardzeichensatz keine Daten mehr bei unbekannten Zeichen ab.</li>
       <li>Fehlermeldung bei ungültigem Logdatum korrigiert</li>
       <li>Schreibweisenabhängigkeit von Logpasswörtern korrigiert (Groß-/Kleinschreibung ist nun immer egal)</li>
       <li>Hinweis-Entschlüsselung bei abgeschaltetem JavaScript</li>
       <li>nicht funktionierenden Log-Bild-Löschlink für Cachebesitzer entfernt</li>
       <li>Logbearbeitungsberechtigungen für gesperrte Caches korrigiert</li>
       <li>wirkungslose Spoileroption beim Hochladen von Logbildern entfernt [&rarr; wieder eingebaut in Version 5]</li>
   </ul>
   <br />

     <p id="v3.0.1"><strong>OC 3.0 Version 1</strong> &ndash; 8. August 2012</p>
   <p>Neu:</p>
     <ul>
       <li>Kurzadressen für Direktzugriff auf Cachelistings, z.B. <a href="http://www.opencaching.de/OCD93B">http://opencaching.de/OCD93B</a></li>
       <li>Anzeige &bdquo;Du hast dieses Event besucht&ldquo; in Karten-Popup-Fenstern
       <li>englische Übersetzung der Seiten <a href="./articles.php?page=geocaching">Über Geocaching</a>, <a href="./articles.php?page=cacheinfo">Cachebeschreibung</a>, <a href="./articles.php?page=impressum">Impressum &amp; Nutzungsbedingungen</a>, <a href="./articles.php?page=dsb">Datenschutzbelehrung</a>, <a href="./articles.php?page=donations">Spenden</a>, <a href="./articles.php?page=contact">Kontakt</a> und <a href="./articles.php?page=team">Teamliste</a> (umschalten auf englischsprachige Seite oben mit <img src="images/flag/EN.png" />)
     <li>Versionsgeschichte</li>
   </ul>
     <p>Geändert / verbessert:</p>
     <ul>
     <li>Trennung opencaching.de/geocaching.de</li>
       <li>Umstellung <a href="./articles.php?page=impressum">Impressum</a> und <a href="./articles.php?page=donations">Spendenseite</a></li>
       <li>neue <a href="./articles.php?page=team">Teamliste</a></li>
       <li>Anzeige neue Caches auf der <a href="./index.php">Startseite</a> nach Veröffentlichungs- statt Versteckdatum, auf der <a href="./newcaches.php">Neue-Caches-Seite</a> nach Veröffentlichungs- statt Einstelldatum des Listings</li>
       <li>deaktivierte und archivierte Caches in der Liste der neuen Caches ausgeblendet</li>
       <li>keine abgeschnittenen GC-Wegpunkte mehr bei Copy&amp;Paste mit führenden Leerzeichen (häufiges Problem)</li>
       <li>eindeutige Bezeichnungen für Entfernungen und Wegstrecken</li>
       <li>Listinganzeige: &bdquo;not found&ldquo; &rarr; &bdquo;nicht gefunden&ldquo;, &bdquo;Maps&ldquo; &rarr; &bdquo;Karten&ldquo;</li>
       <li>Layoutangleichung von <a href="./search.php">Suchseite</a>, <a href="http://www.blog.opencaching.de">Blog-/Infoseite</a> und den übrigen Seiten</li>
       <li>Entfernungsangabe &bdquo;0.0 km&ldquo; in Suchlisten in ausgeloggtem Zustand ausgeblendet (keine Homekoordinaten verfügbar)</li>
   </ul>
     <p>Korrigiert (Bugfixes):</p>
     <ul>
       <li>Skalierung der Cacheicons in exportierten KML-Dateien</li>
       <li>korrekte Ländervorgabe für neue Caches, &bdquo;Belgien/Afghanistan-Problem&ldquo; behoben</li>
       <li>erstes Log fehlte in der Druckansicht</li>
       <li>Anzeige der Teilnehmerzahl von Event-Caches</li>
       <li>Anzeige des Cachetyp-Icons für unveröffentlichte Caches (bei <a href="./myhome.php">Benutzerprofil</a> &rarr; Alle anzeigen)</li>
       <li>Link &bdquo;Geokrety-Verlauf&ldquo; und Empfehlungszahl im Cachelisting werden bei großer Schrift nicht mehr abgeschnitten</li>
       <li>vollständige, anklickbare opencaching.de-Links in Log-Benachrichtigungsmails</li>
       <li>fehlende Verweise auf <a href="http://www.opencaching.nl/">opencaching.nl</a> ergänzt</li>
       <li>korrekte Fehlermeldung bei falschem Email-Adress-Bestätigungscode</li>
       <li>einige Dutzend Rechtschreibfehler korrigiert</li>
   </ul>
    <br />

    </div>
</div>
