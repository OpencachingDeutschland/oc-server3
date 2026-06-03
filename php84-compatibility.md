# PHP 8.4 Kompatibilitätsanalyse — opencaching.de

**Datum:** 2026-06-03
**Umfang:** Legacy-App (`htdocs`), Symfony-Port (`htdocs_symfony`), OKAPI (`hxdimpf/okapi`, Branch `oc4-combined`)

---

## Zusammenfassung

Der Symfony-Port und OKAPI sind nach kleineren Korrekturen vollständig PHP 8.4-kompatibel.
Die Legacy-App läuft im Produktionsmodus unter PHP 8.4, ist aber im Debug-Modus
(Entwicklungsumgebung) nicht betriebsfähig — eine direkte Folge veralteter Abhängigkeiten
(Symfony 3.x Vendor-Code).

| Komponente | PHP 8.4 Status |
|---|---|
| `htdocs_symfony` (Symfony-Port) | ✅ Vollständig kompatibel |
| OKAPI (`oc4-combined`) | ✅ Kompatibel nach Korrekturen |
| `htdocs` (Legacy) — Produktionsmodus | ⚠️ Lauffähig, Deprecation-Warnungen im Log |
| `htdocs` (Legacy) — Debug-Modus | ❌ Nicht betriebsfähig |

---

## Probleme im Detail

### 1. Konstante `E_STRICT` ist deprecated

Die PHP-Konstante `E_STRICT` wurde in PHP 8.4 als veraltet markiert. Jede
Verwendung dieser Konstante — auch in Vergleichen oder Bitmasken — erzeugt eine
`E_DEPRECATED` Notice.

**Betroffene Dateien (Auswahl):**
- `vendor/symfony/symfony/.../ErrorHandler.php` (Zeilen 58, 76)
- `vendor/symfony/symfony/.../ExceptionCaster.php` (Zeile 44)
- OKAPI: `okapi/core/OkapiErrorHandler.php`

**Hintergrund:** `E_STRICT` Fehler wurden bereits in PHP 8.0 abgeschafft — die Konstante
selbst existierte aber weiterhin ohne Konsequenzen. PHP 8.4 markiert nun auch die
Verwendung der Konstante selbst als veraltet.

**Behebung in OKAPI:** `E_STRICT` wurde aus dem Error-Handler-Vergleich entfernt.
In der Legacy-App ist eine Behebung ohne Aktualisierung der Symfony 3.x Abhängigkeiten
nicht möglich.

---

### 2. Implizite Nullable-Parameter sind deprecated

Funktionssignaturen, die einen typisierten Parameter mit Standardwert `null` deklarieren,
ohne den Typ explizit als nullable zu markieren, sind in PHP 8.4 deprecated.

```php
// Deprecated in PHP 8.4:
function foo(string $bar = null) { ... }

// Korrekte Schreibweise:
function foo(?string $bar = null) { ... }
```

**Betroffene Abhängigkeiten (Auswahl):**
- `symfony/symfony` 3.x — hunderte Stellen
- `twig/twig` — mehrere Stellen
- `sensio/framework-extra-bundle` — mehrere Stellen
- `symfony/swiftmailer-bundle` — mehrere Stellen

**Auswirkung:** Bei aktivierter Fehlerausgabe (`display_errors = On`) erscheinen diese
Notices als HTML-Ausgabe *vor* dem eigentlichen Response-Inhalt und korrumpieren die
HTTP-Response.

---

### 3. Legacy-App im Debug-Modus nicht betriebsfähig

Dies ist eine direkte Folge der Punkte 1 und 2. Die Legacy-App setzt im Debug-Modus:

```php
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL);
```

Unter PHP 8.4 erzeugt der gesamte Symfony 3.x Vendor-Code beim Laden massenhaft
`E_DEPRECATED` Notices. Der Symfony `ErrorHandler` eskaliert diese in bestimmten
Kontexten zu Exceptions, sodass bereits die Startseite nur noch eine Fehlerseite anzeigt.

**Wichtig:** Im Produktionsmodus (`display_errors = Off`) ist die Legacy-App unter
PHP 8.4 grundsätzlich lauffähig. Die Deprecation-Warnungen landen im Error-Log ohne
funktionalen Impact.

---

## Empfehlungen

### Kurzfristig

- **Entwicklungsumgebung (ddev):** Bleibt auf PHP 8.2. Die Legacy-App ist dort stabil
  und voll funktionsfähig.

### Mittelfristig

Für vollständige PHP 8.4-Kompatibilität der Legacy-App auch im Debug-Modus müssen
die betroffenen Abhängigkeiten aktualisiert werden:

- Symfony 3.x → Symfony 5.x oder 6.x
- Twig auf aktuelle Version
- Sensio FrameworkExtraBundle ersetzen (abgekündigt)
- SwiftMailer ersetzen (abgekündigt, Nachfolger: `symfony/mailer`)

Symfony 3.x hat seit November 2021 keinen Support mehr. Ein Upgrade ist unabhängig
von PHP 8.4 überfällig.

### Bereits erledigt

- **OKAPI** (`hxdimpf/okapi`, Branch `oc4-combined`): Vollständig PHP 8.4-kompatibel.
  Zwei Korrekturen wurden eingepflegt: `E_STRICT` aus dem Error-Handler entfernt,
  `E_STRICT` aus dem OAuth-Server entfernt.
- **Symfony-Port** (`htdocs_symfony`): Vollständig PHP 8.4-kompatibel.
  Alle Plattform-Anforderungen (`composer check-platform-reqs`) werden erfüllt.

---

## Weiterführende Links

- PHP 8.4 Migrationsleitfaden: https://www.php.net/manual/de/migration84.php
- PHP 8.4 Veraltete Funktionen: https://www.php.net/manual/de/migration84.deprecated.php
- Symfony 3.x End of Life: November 2021
