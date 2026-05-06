# Tabulator, AG Grid & Twig-SSR-Demo — Dokumentation & Sprechernotizen

## Kurzreferenz

| | |
|---|---|
| **Server starten** | `ddev start` (oder `ddev restart` falls bereits gestartet) |
| **Tabulator-Demo** | `https://try-opencaching.ddev.site/backend/tabulator-demo` |
| **AG-Grid-Demo**   | `https://try-opencaching.ddev.site/backend/ag-grid-demo` |
| **Twig-SSR-Demo**  | `https://try-opencaching.ddev.site/backend/twig-ssr-demo` |
| **Login** | `root` / `developer` |
| **In der Navigation?** | Nein — direkt per URL aufrufen |

## Ressourcen

| | |
|---|---|
| **Opencaching Wiki** | https://wiki.opencaching.de/index.php/Hauptseite |
| **Entwicklung** | https://wiki.opencaching.de/index.php/Entwicklung |
| **Git-Workflow** | https://wiki.opencaching.de/index.php/Entwicklung/Git |
| **Issue-Tracker** | https://opencaching.atlassian.net/jira/core/projects/RED/issues |
| **Mattermost** | https://devchat.opencaching.earth |

---

## Was wurde gebaut

Drei Proof-of-Concept-Seiten im neuen Symfony-basierten Opencaching-Backend, die drei
verschiedene Architekturansatze fur Tabellen-Rendering demonstrieren und vergleichen:

| Demo | Ansatz | JS-Abhangigkeit |
|---|---|---|
| [Tabulator](https://tabulator.info) | Clientseitig, deklarative Konfig, eingebaute Ajax-URL | ~500 KB CDN |
| [AG Grid Community](https://www.ag-grid.com) | Clientseitig, explizites fetch(), machtiger | ~700 KB CDN |
| Twig SSR | Serverseitig, reines HTML, kein JS | Keine |

Alle drei Seiten zeigen dieselben Daten (bis zu 25 Caches pro Seite aus der lokalen DB) und
dieselben Funktionen: Sortierung, Spaltenfilter, Paginierung.

---

## Geanderte Dateien

### `htdocs_symfony/templates/base.html.twig`
**Bootstrap 5 wurde zum globalen Basis-Layout hinzugefugt.**

- Bootstrap 5.3.3 CSS (`<link>` im `stylesheets`-Block)
- Bootstrap 5.3.3 JS-Bundle (`<script>` in einem neuen `javascripts`-Block)

**Warum:** Bisher wurde kein CSS-Framework global geladen. Damit steht Bootstrap allen
Seiten zur Verfugung, die `base.html.twig` erweitern. Kindtemplates konnen den
`javascripts`-Block uberschreiben oder erweitern, um eigene Skripte einzubinden.

---

### `htdocs_symfony/src/Controller/Backend/TabulatorDemoControllerBackend.php` *(neu)*
**Ein Symfony-Controller mit zwei Routen.**

| Route | Zweck |
|---|---|
| `GET /backend/tabulator-demo` | Rendert die HTML-Seite |
| `GET /backend/tabulator-demo/data` | Liefert JSON (bis zu 200 Caches) |

Der JSON-Endpunkt fragt die Tabellen `caches`, `user` und `cache_status` uber Doctrine DBAL
ab und gibt zuruck: OC-Code, Cache-Name, Schwierigkeit, Gelandewertung, Besitzer-Username,
Status-Bezeichnung. Schwierigkeit und Gelandewertung sind in der DB als Ganzzahl×2
gespeichert und werden hier durch 2,0 geteilt.

**Warum zwei Routen:** Die Trennung von Daten-Endpunkt und Seiten-Route entspricht dem
Standard-"Ajax-Tabellen"-Muster. Der Browser ladt zunachst die Seite, dann holt Tabulator
die Daten selbstandig. Der Daten-Endpunkt ist unabhangig testbar und wiederverwendbar.

---

### `htdocs_symfony/templates/backend/tabulatorDemo/index.html.twig` *(neu)*
**Das Demo-Seiten-Template.**

- Erweitert `backend/base.html.twig` (erbt Navbar, Sidebar, Layout)
- Tabulator 6.3.0 CSS (Bootstrap-5-Theme) per CDN
- Tabulator 6.3.0 JS per CDN
- Ein `<div id="caches-table">`, auf das Tabulator gemountet wird, konfiguriert mit:
  - `ajaxURL` zeigt auf die `/data`-Route — Tabulator holt die Daten selbst
  - Clientseitige Paginierung (25 Zeilen/Seite)
  - Sortierbare und verschiebbare Spalten
  - Kopfzeilen-Filter auf den Spalten Code, Name, Besitzer, Status
  - Zahlenformatierung fur Schwierigkeit und Gelandewertung (eine Nachkommastelle)

**Warum im Template, nicht im Controller:** Seitenspezifisches JS gehort in den Twig-Block
`javascripts` — das ist die Symfony/Twig-Konvention. Der Controller bleibt frei von
HTML-Belangen.

---

### `htdocs_symfony/src/Controller/Backend/AgGridDemoControllerBackend.php` *(neu)*
**Identische Controller-Struktur wie die Tabulator-Demo.**

| Route | Zweck |
|---|---|
| `GET /backend/ag-grid-demo` | Rendert die HTML-Seite |
| `GET /backend/ag-grid-demo/data` | Liefert JSON (bis zu 200 Caches) |

Dieselbe SQL-Abfrage und Datenform wie beim Tabulator-Endpunkt — der Vergleich ist
absichtlich fair: gleiche Daten, gleiche Struktur.

---

### `htdocs_symfony/templates/backend/agGridDemo/index.html.twig` *(neu)*
**Das AG-Grid-Demo-Template.**

- Erweitert `backend/base.html.twig`
- AG Grid Community 31.3.4 CSS (`ag-theme-quartz`) per CDN
- AG Grid Community 31.3.4 JS per CDN
- Ein `<div id="caches-table" class="ag-theme-quartz">`, konfiguriert mit:
  - `domLayout: 'autoHeight'` — Grid passt sich der Seitenhohe an
  - Clientseitige Paginierung (25 Zeilen/Seite)
  - `floatingFilter: true` — Filtereingaben unterhalb der Spaltenuberschriften
  - `sortable`, `resizable`, verschiebbar per Standard
  - Zahlenformatierung fur Schwierigkeit und Gelandewertung via `valueFormatter`
  - Daten werden per `fetch()` geladen und per `gridApi.setGridOption('rowData', ...)` gesetzt

**Unterschied zu Tabulator:** AG Grid hat keine eingebaute `ajaxURL`-Option — stattdessen
holt man die Daten mit `fetch()` und ubergibt sie explizit uber die Grid-API. Mehr Code,
aber auch mehr Kontrolle uber Ladezustand und Fehlerbehandlung.

---

### `htdocs_symfony/src/Controller/Backend/TwigSsrDemoControllerBackend.php` *(neu)*
**Ein einzelner Controller mit einer Route — kein separater Daten-Endpunkt.**

| Route | Zweck |
|---|---|
| `GET /backend/twig-ssr-demo` | Rendert die vollstandige HTML-Seite mit Daten |

Der Controller liest `?sort=`, `?dir=`, `?q=` und `?page=` aus dem Query-String,
baut daraus zwei SQL-Abfragen (COUNT + Daten) mit LIMIT/OFFSET, und ubergibt alles
an das Template. Jede Interaktion — Sortierung, Filter, Seitenwechsel — ist ein neuer
HTTP-Request an dieselbe Route.

**Warum eine Route statt zwei:** Bei SSR gibt es keine Trennung zwischen "Seite" und
"Daten" — der Controller liefert beides in einem Schritt. Das ist das klassische
Request-Response-Modell.

---

### `htdocs_symfony/templates/backend/twigSsrDemo/index.html.twig` *(neu)*
**Das SSR-Demo-Template — reines Twig, kein JS.**

- Erweitert `backend/base.html.twig`
- Kein CDN, keine externe Abhangigkeit
- Ein Bootstrap-5-`<table>` mit `table-striped table-hover`
- Spaltenuberschriften als Sort-Links (`<a href="?sort=...&dir=...">`)
- Sort-Indikator (▲/▼) via Twig-Bedingung
- Suchformular (GET) mit versteckten `sort`/`dir`-Feldern, damit Filter und Sortierung
  gleichzeitig aktiv bleiben
- Bootstrap-Paginierung mit erster/letzter Seite und `…`-Trenner

**Warum kein JS:** Der Punkt dieser Demo ist zu zeigen, dass man Sortierung, Filter und
Paginierung komplett serverseitig abhandeln kann. Der Tradeoff: Jede Interaktion lost
einen Seitenneuladen aus und der Server muss die Abfrage wiederholen.

---

## Was bewusst NICHT gemacht wurde

- Kein Navbar-Eintrag — die Demo ist eine Entwicklungs-/Evaluierungsseite, kein
  Produktions-Feature
- Keine Umgehung der Authentifizierung — die Route erfordert `ROLE_TEAM` wie alle
  Backend-Routen
- Keine Datenbearbeitung — nur lesende Demo

---

## Sprechernotizen

"Was ihr hier seht, ist ein Proof-of-Concept fur clientseitiges Tabellen-Rendering im
neuen Opencaching-Backend — der Symfony-basierten App, die langfristig die alte PHP-Seite
ablosen soll.

Die Seite wird serverseitig von Symfony und Twig gerendert — nur ein Rahmen mit Navbar und
einem leeren div. Sobald die Seite geladen ist, holt Tabulator die Daten eigenstandig von
einem dedizierten JSON-API-Endpunkt unter `/backend/tabulator-demo/data`. Dieser Endpunkt
gibt bis zu 200 Geocaches aus unserer lokalen Datenbank als reines JSON zuruck. Kein
Seitenneuladen, keine serverseitige Paginierungslogik.

Auf der Clientseite nimmt Tabulator dieses JSON und liefert uns Sortierung auf jeder
Spalte, Kopfzeilen-Filter auf den Textfeldern (einfach mal in das Namensfeld tippen),
Paginierung mit 25 Zeilen pro Seite und verschiebbare Spalten — alles ohne eigenen Code
fur diese Funktionen.

Wir haben Tabulator gewahlt, weil es sich gut mit Bootstrap integriert, eine freizugige
MIT-Lizenz hat und mit minimaler Konfiguration eine produktionstaugliche UX liefert. Die
gesamte JavaScript-Initialisierung umfasst etwa 15 Zeilen.

Das architektonische Muster — ein schlanker JSON-API-Endpunkt plus eine clientseitige
Tabellenbibliothek — ist das, was wir konsequent einsetzen wurden, wenn wir diesen Ansatz
ubernehmen. Das Backend serialisiert nur Daten; das gesamte Tabellenverhalten lebt im
Browser. Das macht es spater einfach, Filterung, Export oder Inline-Bearbeitung
hinzuzufugen, ohne den Server anfassen zu mussen.

Jetzt haben wir einen direkten Vergleich. Die zweite Seite unter `/backend/ag-grid-demo`
zeigt exakt dasselbe mit AG Grid Community — einer Bibliothek, die in Unternehmensumgebungen
weit verbreitet ist und kein jQuery benotigt. AG Grid ist kraftvoller, erfordert aber etwas
mehr expliziten Code: Man holt die Daten selbst per fetch() und ubergibt sie der Grid-API,
anstatt einfach eine URL anzugeben. Dafur hat man mehr Kontrolle.

Die dritte Seite unter `/backend/twig-ssr-demo` zeigt den Gegenentwurf: kein JavaScript,
keine Bibliothek, kein CDN. Symfony und Twig rendern die gesamte Seite inklusive Daten —
Sortierung und Filter sind einfache Links mit Query-Parametern, Paginierung ist
LIMIT/OFFSET in SQL. Jede Interaktion ist ein neuer HTTP-Request. Das ist exakt das Muster,
das die alte PHP-Seite heute verwendet.

Was habt ihr gesehen? Die SSR-Seite fuhlt sich langsamer an — jeder Klick ladt die Seite
neu. Die JS-Bibliotheken reagieren sofort. Dafur hat die SSR-Seite null CDN-Abhangigkeiten
und funktioniert ohne JavaScript im Browser. Das ist der Tradeoff, den wir bewusst
diskutieren mussen: Interaktivitat gegen Einfachheit. Euer Feedback hilft uns, die richtige
Entscheidung fur die Zukunft des Backends zu treffen."
