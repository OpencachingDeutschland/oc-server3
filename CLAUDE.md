# CLAUDE.md — oc-server3

Guidance for Claude Code when working in this repository.

## Project Overview

oc-server3 is the PHP backend for opencaching.de. This branch (`feature/map3`) adds a
Leaflet-based live map at  (OC-only).

## Key Paths

| Path | Purpose |
|------|---------|
| `htdocs/map3.php` | Backend: page render + JSON API modes |
| `htdocs/resource2/misc/map3/` | ESM frontend JS modules |
| `htdocs/templates2/ocstyle/map3.tpl` | Smarty page template |
| `htdocs/lib2/web.inc.php` | Included at top of every page (sets up `$tpl`, `$login`, `$opt`) |
| `htdocs/lib2/OcSmarty.class.php` | Smarty subclass — `$tpl->assign()`, `$tpl->display()` |
| `htdocs/lib2/db.inc.php` | DB helpers: `sql()`, `sql_slave()`, `sql_fetch_assoc()`, `sql_value()` |
| `htdocs/templates2/ocstyle/sys_main.tpl` | Main layout template |

## Template System

- **Smarty** templates in `htdocs/templates2/ocstyle/*.tpl`
- Page controller sets `$tpl->name = 'map3'` then calls `$tpl->display()`
- `$tpl->assign('key', $value)` passes data to template as `{$key}`
- `$tpl->add_header_javascript($src)` adds `<script type="text/javascript">` — does NOT support `type="module"`
- For ESM modules: add `<script type="module" src="...">` directly in the page `.tpl` file

## Database Access

```php
// Query (uses sql() from db.inc.php):
$rs = sql("SELECT ... FROM caches WHERE latitude>'&1' AND latitude<'&2'", $lat1, $lat2);
while ($r = sql_fetch_assoc($rs)) { ... }
sql_free_result($rs);

// Single value:
$val = sql_value("SELECT COUNT(*) FROM caches WHERE ...", 0, $param);

// Read replica:
$rs = sql_slave("SELECT ...", $param);
```

Parameters use `'&1'`, `'&2'` etc. placeholders (NOT PDO `?` or named params).

## map3 Architecture

### Backend (map3.php)

Handles two modes via `?mode=` parameter:

- **`mode=live`** — JSON API: fetch caches in bounding box, return uniCache array
  - Params: `lat1`, `lat2`, `lon1`, `lon2` (floats)
  - Returns: `{ items: uniCache[] }`

- **`mode=cache`** — JSON API: fetch single cache detail
  - Params: `wp` (e.g. `OC1234`)
  - Returns: `{ uniCache: {...} }`

- **no mode** — Render the map3 page (Smarty template)

### uniCache Format

The frontend JS expects this shape (uniCache shape with transient WP fields):

```json
{
  "_id": "OC1234",
  "platform": "OC",
  "referenceCode": "OC1234",
  "name": "Cache name",
  "lat": 52.5,
  "lon": 13.4,
  "geocacheType": { "id": 2, "name": "Traditional" },
  "geocacheSize": { "id": 2, "name": "Small" },
  "difficulty": 1.5,
  "terrain": 2.0,
  "isFound": false,
  "isOwned": false,
  "isArchived": false,
  "isDisabled": false,
  "ownerAlias": "username",
  "publishedDate": "2020-01-01",
  "favoritePoints": 3,
  "shortName": "Cache n",
  "isOC": true,
  "isGC": false,
  "isSelected": false
}
```

### Frontend JS (ESM modules in htdocs/resource2/misc/map3/)

Entry point: `map3.js` (loaded via `<script type="module">` in map3.tpl)

JS modules in  (OC-only subset):

| File | Purpose |
|------|---------|
| `map3.js` | Main map init, live map, marker handling |
| `mapIcons.js` | SVG cache type icons |
| `mapLayers.js` | Leaflet tile layer definitions |
| `mapHelpers.js` | `MapButtonControl`, `downloadFile`, `escapeXml` |
| `mapCircles.js` | Draw circle/rectangle shapes |
| `mapSelect.js` | Selection mode |
| `mapFilter.js` | Filter UI (OC-only) |

Removed (not ported): `mapRouting.js`, `mapBroadcast.js`, `mapServer.js`, `mapTracks.js`

## Leaflet

Loaded via CDN in `map3.tpl`. MarkerCluster also via CDN.
Do not add Leaflet to the repo.

## Naming Conventions

- Replace `ocmap` → `ocmap` in CSS classes, IDs, localStorage keys
- Replace `OCmap` → `OCmap` in comments/strings
- Replace `isGC` → always `false` for OC-only data; `isOC` → always `true`
- Keep author credit `hxdimpf` in file headers; add OC license block from `LICENSE.md`

## License Header for New Files

```php
<?php
/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 ***************************************************************************/
```

```javascript
/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 ***************************************************************************/
```
