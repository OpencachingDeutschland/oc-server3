{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
	<div class="content2-pagetitle">
		Versionsgeschichte
	</div>
	<div class="content-txtbox-noshade" style="padding-right: 25px;">

	<p>Opencaching Version 1.0 ging im August 2005 online. In den nachfolgenden Jahren wurde die Website stetig verbessert und zur Version 2 weiterentwickelt. Anfang 2011 wurde die Entwicklung vorübergehend eingestellt, bis der neue Verein Opencaching Deutschland sie Mitte 2012 unter der Versionsnummer&nbsp;3.0 wieder aufnahm. Das neue Entwicklerteam arbeitet sich zunächst mit einfachen Aufgaben ein.</p>
	<p>Im Folgenden sind alle Veränderungen ab OC Version&nbsp;3.0 aufgelistet.</p>
	<br />

	 <p><strong>Version 3.0.2</strong> &ndash; (September)</p>
   <p>Neu:</p>
	 <ul class="changelog">
	   <li class="changelogitem"><a href="./articles.php?page=cacheinfo#difficulty">Schwierigkeitsgrade</a> erklärt, inklusive Tooltip und Link in den Cachelistings</li>
	   <li class="changelogitem"><a href="./articles.php?page=verein">Vereinsseite</a>
   </ul>
	 <p>Geändert / verbessert:</p>
	 <ul class="changelog">
     <li class="changelogitem"><a href="./articles.php?page=cacheinfo">Cachebeschreibungs-Info</a> überarbeitet</li>
	   <li class="changelogitem">Empfehlungssterne erscheinen nur noch bei Gefunden- und Teilgenommen-Logs.</li>
	   <li class="changelogitem">Logtypreihenfolge bei Event-Caches umgedreht</li>
     <li class="changelogitem"><a href="./index.php">Startseite</a> beschleunigt</li>
	   <li class="changelogitem"><a href="./doc/xml/">XML-Interface-Doku</a> und <a href="https://github.com/OpencachingDeutschland/oc-server3/blob/master/doc/license.txt">Quellcodelizenz</a> aktualisiert</li>
	   <li class="changelogitem"><a href="./articles.php?page=team">Teamliste</a> aktualisiert</li>
	   <li class="changelogitem">neue <a href="./articles.php?page=donations">Bankverbindung</a> bekanntgegeben</li>
	   <li class="changelogitem">übersichtlichere Darstellung von Cachemeldungen für das OC-Supportteam</li>
	   <li class="changelogitem">Platz für neue Caches: Beschränkung auf 65535 Listings (OCFFFF, inkl. archivierter Caches) aufgehoben</li>
		 <li class="changelogitem">spanische und italienische Übersetzung vervollständigt</li>
		 <li class="changelogitem">inaktive Caches auch auf der <a href="./newcachesrest.php">Alle-außer-Deutschland-Seite</a> ausgeblendet</li>
		 <li class="changelogitem">einheitliche Schreibweise für Attributnamen</li>
		 <li class="changelogitem">Cachelisting: &bdquo;Decrypt&ldquo; &rarr; &bdquo;Entschlüsseln&ldquo;</li>
   </ul>
	 <p>Korrigiert (Bugfixes):</p>
	 <ul class="changelog">
	   <li class="changelogitem">Anzeige von &bdquo;nicht gefunden&ldquo; und &bdquo;veröffentlicht am&ldquo; in der Druckansicht</li> 
	   <li class="changelogitem">Anzeige der Event-Teilnehmerzahl in der Logzusammenfassungszeile</li>
	   <li class="changelogitem">seltenen Fehler bei der Erzeugung von OC-Wegpunkten behoben</li>
	   <li class="changelogitem">Empfehlungen gehen beim mehrfachen Loggen eines Caches &ndash; z.B. Fund + Hinweis &ndash; nicht mehr verloren.</li>
	   <li class="changelogitem">Empfehlungen gehen beim Löschen eines von mehreren Logs des gleichen Benutzers oder beim Bearbeiten von einem der Logs nicht mehr verloren.</li>
	   <li class="changelogitem">Mehrfachlogs eines Benutzers zählen bei der Bewertungsübersicht auf der Startseite nur noch einmal.</li>
	   <li class="changelogitem"><a href="doc/xml/">Das XML-Interface</a> schneidet im Standardzeichensatz keine Daten mehr bei unbekannten Zeichen ab.</li>
	   <li class="changelogitem">Fehlermeldung bei ungültigem Logdatum korrigiert</li>
	   <li class="changelogitem">Schreibweisenabhängigkeit von Logpasswörtern korrigiert (Groß-/Kleinschreibung ist nun immer egal)</li>
	   <li class="changelogitem">Hinweis-Entschlüsselung bei abgeschaltetem JavaScript</li>
   </ul>
	<br />

	 <p><strong>Version 3.0.1</strong> &ndash; 8. August 2012</p>
   <p>Neu:</p>
	 <ul class="changelog">	   
	   <li class="changelogitem">Kurzadressen für Direktzugriff auf Cachelistings, z.B. <a href="http://www.opencaching.de/OCD93B">http://www.opencaching.de/OCD93B</a></li>
	   <li class="changelogitem">Anzeige &bdquo;Du hast dieses Event besucht&ldquo; in Karten-Popup-Fenstern
	   <li class="changelogitem">englische Übersetzung der Seiten <a href="./articles.php?page=geocaching">Über Geocaching</a>, <a href="./articles.php?page=cacheinfo">Cachebeschreibung</a>, <a href="./articles.php?page=impressum">Impressum &amp; Nutzungsbedingungen</a>, <a href="./articles.php?page=dsb">Datenschutzbelehrung</a>, <a href="./articles.php?page=donations">Spenden</a>, <a href="./articles.php?page=contact">Kontakt</a> und <a href="./articles.php?page=team">Teamliste</a> (umschalten auf englischsprachige Seite oben mit <img src="images/flag/EN.gif">)
     <li class="changelogitem">Versionsgeschichte</li>
   </ul>
	 <p>Geändert / verbessert:</p>
	 <ul class="changelog">
     <li class="changelogitem">Trennung opencaching.de/geocaching.de</li>
	   <li class="changelogitem">Umstellung <a href="./articles.php?page=impressum">Impressum</a> und <a href="./articles.php?page=donations">Spendenseite</a></li>
	   <li class="changelogitem">neue <a href="./articles.php?page=team">Teamliste</a></li>
	   <li class="changelogitem">Anzeige neue Caches auf der <a href="./index.php">Startseite</a> nach Veröffentlichungs- statt Versteckdatum, auf der <a href="./newcaches.php">Neue-Caches-Seite</a> nach Veröffentlichungs- statt Einstelldatum des Listings</li>
	   <li class="changelogitem">deaktivierte und archivierte Caches in der Liste der neuen Caches ausgeblendet</li>
	   <li class="changelogitem">keine abgeschnittenen GC-Wegpunkte mehr bei Copy&amp;Paste mit führenden Leerzeichen (häufiges Problem)</li>
	   <li class="changelogitem">eindeutige Bezeichnungen für Entfernungen und Wegstrecken</li>
	   <li class="changelogitem">Listinganzeige: &bdquo;not found&ldquo; &rarr; &bdquo;nicht gefunden&ldquo;, &bdquo;Maps&ldquo; &rarr; &bdquo;Karten&ldquo;</li>
	   <li class="changelogitem">Layoutangleichung von <a href="./search.php">Suchseite</a>, <a href="http://www.blog.opencaching.de">Blog-/Infoseite</a> und den übrigen Seiten</li>
	   <li class="changelogitem">Entfernungsangabe &bdquo;0.0 km&ldquo; in Suchlisten in ausgeloggtem Zustand ausgeblendet (keine Homekoordinaten verfügbar)</li>
   </ul>
	 <p>Korrigiert (Bugfixes):</p>
	 <ul class="changelog">
	   <li class="changelogitem">Skalierung der Cacheicons in exportierten KML-Dateien</li>
	   <li class="changelogitem">korrekte Ländervorgabe für neue Caches, &bdquo;Belgien/Afghanistan-Problem&ldquo; behoben</li>
	   <li class="changelogitem">erstes Log fehlte in der Druckansicht</li>
	   <li class="changelogitem">Anzeige der Teilnehmerzahl von Event-Caches</li>
	   <li class="changelogitem">Anzeige des Cachetyp-Icons für unveröffentlichte Caches (bei <a href="./myhome.php">Benutzerprofil</a> &rarr; Alle anzeigen)</li>
	   <li class="changelogitem">Link &bdquo;Geokrety-Verlauf&ldquo; und Empfehlungszahl im Cachelisting werden bei großer Schrift nicht mehr abgeschnitten</li>
	   <li class="changelogitem">vollständige, anklickbare opencaching.de-Links in Log-Benachrichtigungsmails</li>
	   <li class="changelogitem">fehlende Verweise auf <a href="http://www.opencaching.nl/">opencaching.nl</a> ergänzt</li>
	   <li class="changelogitem">korrekte Fehlermeldung bei falschem Email-Adress-Bestätigungscode</li>
	   <li class="changelogitem">einige Dutzend Rechtschreibfehler korrigiert</li>
   </ul>
	<br />

	</div>