# `feature/ui-refresh` — change overview

A one-page brief for pulling this branch into their existing
ddev. Branch is 14 commits ahead of `origin/development` and touches
mostly `htdocs_symfony/` plus three small portability artifacts under
`.ddev/`. The legacy `htdocs/` tree is **not** modified.

This branch lays groundwork for what is intended to become the
**next-generation opencaching.de web application** — the eventual
replacement for the legacy PHP frontend.

---

## 1. What changes for users

### Core pages
- **Theming**: light/dark theme toggle in the navbar. Choice persists
  in `localStorage`. An inline `<head>` script applies the theme
  before paint, so reloads do not flash the wrong palette.
- **Login wall**: routes are gated behind `IS_AUTHENTICATED_FULLY`
  except `/login`, `/cache/{wp}` (read-only), and a few static
  assets. Authentication piggy-backs on the existing legacy session
  (see §2.5) — no second login.
- **Live map** (`/livemap`): full-viewport Leaflet map with OSM
  city/village search (top-center), scroll-to-top button, and marker
  clustering. Map popups display cache metadata with multilingual i18n.
- **Cache detail** (`/cache/{wp}`): client-driven detail view with
  Tabulator log grid, log submission, PCN / corrected-coordinate
  editing, live map-marker updates, Bootstrap popovers for PCN text
  display. Event caches (type 6) now display event date and duration.
  OC-only badge visible on cache cards and detail page.
- **Search caches** (`/search`): full-featured search with D/T sliders,
  Active/OC-only filters, Tabulator results grid, live map integration,
  select/export actions. Narrower, more responsive filter inputs.
  Monospaced OC codes in results. Full German localization.
- **Search users** (`/user`): Tabulator-based user search with
  role-aware columns. Support staff see email, joined date, user ID;
  regular users see username only. Find/hide count badges for each user.
- **User profile** (`/user/profile/{id}`): redesigned two-column card
  layout with public info (left) and team-only account info (right).
  Clean, modern presentation.
- **Reported caches** (`/backoffice/reported-caches`): Tabulator grid
  with status filtering (All/New/In Progress/Done), colored status
  badges, linked columns to cache and owner profiles.
- **Navbar**: rebuilt on KnpMenu with Bootstrap-5 template
  (`bootstrap_navbar_menu.html.twig`); user dropdown on right.
  Support dropdown visible to ROLE_SUPPORT_TRAINEE with nested
  administrative actions.

---

## 2. Architectural changes (and the reasoning)

### 2.1 Build system: Webpack Encore → source-controlled ESM

Webpack Encore is removed (`webpack.config.js`, `yarn.lock`, the
`assets/` build pipeline). Frontend modules now live as plain ES
modules under `htdocs_symfony/public/js/` and are loaded directly by
the browser.

**Why now:** the build step was the single most common cause of
"works for me / does not for me" friction. Removing it means zero
`npm install`, zero rebuild on edit, and the JS shipped to the
browser is exactly what is in git.

Vendor assets (Leaflet, Tabulator, Bootstrap, leaflet-draw,
leaflet.markercluster) are self-hosted under `public/vendor/<pkg>/`
and referenced via `asset('vendor/<pkg>/<file>')`. No third-party
network dependency at runtime.

### 2.2 Page module loader (`loader.js` + `base.html.twig`)

`base.html.twig` carries two attributes:

```twig
<body data-page="{% block data_page %}{% endblock %}"
      data-map-js="{% block data_map_js %}{% endblock %}">
```

`public/js/loader.js` reads them on `DOMContentLoaded`:

- `data-page="cache"` → dynamic `import('./cache.js')`, then `init()`
- `data-map-js="true"` → load Leaflet from `public/vendor/leaflet/`,
  then side-effect import `./map.js`

**Why:** one central loader, page authors override two Twig blocks
to wire their module. No per-page `<script>` tags, no global
registry, no JS-side route table. Adding a new page is: drop a file
in `public/js/`, set the `data_page` block in its template.

### 2.3 Map module

`public/js/map.js` and its `map*.js` siblings implement the Leaflet
map: icons, popups, shape selection, routing, GPX export.
Page-specific glue lives in the page module (`livemap.js`,
`cache.js`); the map module itself is a generic reusable component.

### 2.4 Theme system

CSS custom properties in `public/css/oc-style.css` keyed off
`[data-theme="dark"]` on `<html>`. The inline script in the `<head>`
of `base.html.twig` resolves the theme before render so the body
never flashes the wrong palette. The navbar toggle writes
`localStorage.oc-theme` and updates `data-theme`.

**Why CSS variables:** one stylesheet serves both themes. Vendor
components (Leaflet, Tabulator) need only a small number of targeted
overrides under `[data-theme="dark"]` selectors. Adding a third
theme later is a matter of one more selector block.

### 2.5 Auth: legacy-session bridge

`src/Security/LegacyCookieAuthenticator.php` reads the legacy
`ocdevelopmentdata` cookie, validates it against `sys_sessions`, and
hands Symfony a `User` keyed on the legacy `user_id`.
`config/packages/security.yaml` gates everything except `/login` and
the cache detail page via `access_control`.

**Why a bridge instead of a fresh login:** the legacy PHP frontend
will keep writing the cookie for the foreseeable future. The Symfony
site needs to *see* the same login, not duplicate it. Validating
against `sys_sessions` (rather than just trusting the cookie) keeps
Symfony honest if the session is invalidated server-side. This also
makes the cutover incremental: pages can move to Symfony one at a
time and users notice nothing.

### 2.6 Directory layout under `public/`

```
public/js/                ← first-party JS, flat
public/vendor/<pkg>/      ← one directory per third-party package
                            (bootstrap, leaflet, leaflet-draw,
                             leaflet.markercluster, tabulator)
public/css/               ← first-party CSS
```

Vendor packages keep their own internal structure (e.g.
`vendor/leaflet/leaflet.css` + `vendor/leaflet/images/*.png`) so
CSS-relative URLs resolve without rewriting.

### 2.7 Dead code removed

- `MapsController`, `MapsControllerBackend`, `MapsRepository`,
  `templates/app/maps/index.html.twig`, the old "Map" navbar link —
  superseded by `LiveMapController` + `templates/app/maps/livemap.html.twig`.
- `templates/app/maps/maps.leaflet.js` — orphan fragment from the
  old maps page.
- `public/js/ag-grid.js` — only used for a demo, no longer needed.

### 2.8 jQuery removed

`feat: eliminate jQuery and replace with vanilla JS compatibility
layer`. No new code should import jQuery.

---

## 3. Dev environment (`.ddev/`)

Three new artifacts make a fresh `ddev restart` produce a working
environment without any manual SQL or config edits.

### `.ddev/mysql/no_strict.cnf`

```ini
[mysqld]
sql_mode = ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION
```

**Why:** the legacy `htdocs/newcache.php` issues
`INSERT INTO caches (cache_id, ...) VALUES ('', ...)` to get an
AUTO_INCREMENT id. Under `STRICT_TRANS_TABLES` (ddev's default)
MariaDB rejects the `''` with `Incorrect integer value`, producing
HTTP 500 on cache submission. Production MariaDB does not run in
strict mode, so dev needs to match.

### `.ddev/db-patches/sp_update_logstat.sql`

Idempotent `DROP PROCEDURE IF EXISTS … CREATE PROCEDURE …` rewriting
the legacy stored procedure with two `IFNULL(..., 0)` wraps around
the `needs_maintenance` / `listing_outdated` subqueries (the
unwrapped subqueries returned NULL when a user had no prior log on
a cache, producing HTTP 500 on the first log). Safe to run on every
`ddev start`.

**Why not upstream the fix?** This branch is scoped to the Symfony
work; touching `sql/stored-proc/maintain-current.inc.php` would land
in any release cut from this branch. The dev-only patch isolates the
fix until we have signoff to merge it upstream.

### `.ddev/config.yaml` — post-start hook

```yaml
hooks:
  post-start:
    - exec: cp htdocs/app/config/parameters.yml.dist htdocs/app/config/parameters.yml
    - exec: cp htdocs/config2/settings-sample-dev.inc.php htdocs/config2/settings.inc.php
    - exec: cp htdocs_symfony/.env.local_dist htdocs_symfony/.env.local
    - exec: "mysql db < /mnt/ddev_config/db-patches/sp_update_logstat.sql"
      service: db
```

The new last entry re-applies the procedure patch every start. The
`.ddev/` directory is mounted at `/mnt/ddev_config/` inside the db
container.

---

## 4. Pulling the branch and verifying it

### 4.1 Where the branch lives

There is no PR yet. The branch lives on the contributor's fork:

```
git@github.com:hxdimpf/oc-server3.git    feature/ui-refresh
```

If your checkout already has that fork as a remote, skip ahead to
**4.2**. Otherwise add it. Pick the name you prefer — `hxdimpf`,
`ui-refresh`, anything; the examples below use `hxdimpf`.

```bash
git remote -v                            # do you already have the fork?
git remote add hxdimpf git@github.com:hxdimpf/oc-server3.git
```

### 4.2 Checking the branch out

```bash
git fetch hxdimpf
git checkout -b feature/ui-refresh hxdimpf/feature/ui-refresh
ddev restart            # picks up no_strict.cnf and runs the SP patch
```

`ddev restart` is required, not optional: it applies the portable
`.ddev/` bootstrap from §3 (the `no_strict.cnf` MariaDB config and the
`sp_update_logstat.sql` patch). Without it the cache-submission and
first-log flows still 500.

No `composer install` is required for testing — the legacy PHP and
the Symfony app share the same vendor tree, already populated by your
existing checkout. If you do run `composer install`, point it at the
Symfony tree:

```bash
ddev composer install -d htdocs_symfony
```

### 4.3 Smoke tests

1. Visit `/` → redirected to `/login` (gated).
2. Log in via the legacy frontend, return to the Symfony site → you
   are authenticated, navbar shows your user dropdown.
3. `/livemap` → map fills the viewport, OSM search top-center,
   scroll-to-top button visible.
4. `/cache/<any-OC-waypoint>` → detail page with log grid; submit a
   new log (this previously 500'd on first log). Try the duplicate
   guard: post a Found, then attempt a second — the dropdown should
   no longer offer "Found it".
5. Toggle theme via the sun/moon button → no flash on reload.

### 4.4 Returning to your usual branch

The branch only touches `htdocs_symfony/` plus the three `.ddev/`
artifacts. Switching back is just `git checkout <yourbranch>`; the
legacy `htdocs/` tree is unchanged either way. Re-run `ddev restart`
if the other branch needs a different DB state.

---

## 5. Recent enhancements (May 2026 — UI refinement & i18n)

### 5.1 Internationalization (i18n) infrastructure

A scalable, decoupled i18n system for dynamic strings:

- **Server-side locale detection**: Symfony injects user's locale into
  `window.OCI18n` object as one-shot initialization in Twig.
- **Client-side lookup**: `t(key, {params})` helper resolves strings
  from `window.OCI18n` at runtime with safe placeholder substitution
  (`%key%` syntax, avoiding Twig/ICU conflicts).
- **No language detection in JS**: avoids browser-locale sniffing;
  server is canonical. Scales to unlimited languages without code changes.
- **Full German localization**: search pages, user pages, map popups,
  reported caches grid, navbar dropdown labels, all human-facing strings.
- **Weblate-ready**: string structure supports external translation
  management tools; see `translations/messages+intl-icu.de.yaml`.

### 5.2 Event cache support

Event caches (type 6) now render event metadata:

- Event date stored in `caches.date_hidden` (DATE field, not DATETIME).
- Event duration stored in `caches.search_time` (float, hours).
- Constraint enforcement: event type locks size=7 (other), D/T=1.0 both.
- Frontend constraint sync: `newcache.js` prevents form tampering.
- Backend constraint validation: `CachesController` rejects mismatched
  event submissions.

### 5.3 Table styling & layout

Tabulator tables now render with professional alignment and typography:

- **CSS theme fix**: base `tabulator.min.css` (not Bootstrap variant)
  provides proper row height (22px), alignment, and centering.
- **OC code typography**: columns with OC codes (`referenceCode`,
  `wpOc`, etc.) render in monospaced font for clarity and distinction
  from readable text.
- **Global alignment**: header and data rows align perfectly; no
  horizontal shift artifacts.
- **Density tuned to GCxM standards**: compact rows, proper padding,
  professional appearance.

### 5.4 Map and detail-page improvements

- **OC-only badge**: propagated through APIs and map icons. Visible
  on cache detail, search results, and live map.
- **PCN text display**: Bootstrap popovers instead of `<details>`
  element; renders in monospace with proper line-wrapping for
  corrected-coordinate notes.
- **Map popup i18n**: cache metadata labels ("by:", "Published:",
  "Finds:", "Found:") translated via `window.OCI18n`.
- **Mobile viewport**: removed `user-scalable=no` and `maximum-scale=1`
  to enable pinch-zoom and proper touch interaction.

### 5.5 Navigation and role-based UI

- **Support dropdown**: visible only to `ROLE_SUPPORT_TRAINEE`. Nested
  menu groups administrative functions without cluttering the main
  navbar.
- **Role-aware columns**: user search displays different columns based
  on role (support staff see PII; regular users see public info only).
- **Route rename**: `/backend` → `/backoffice` for semantic clarity
  across 17 files (routes, security rules, navbar, templates).

---

## 6. Known issues / open items on this branch

- **Symfony schema migrations**: none added on this branch. The only
  schema-shaped change is the stored-procedure patch, which lives
  under `.ddev/` (dev-only) and is intentionally **not** a Doctrine
  migration.
- **Navbar items visible while logged out**: the Login link still
  shows alongside menu items. Logged-out users hitting a gated page
  are redirected to `/login` regardless, but the navbar itself is
  not yet conditional.

---

## 7. Will this become THE next-generation opencaching.de?

A frank self-assessment. This branch is **foundation work plus two
flagship pages**, not a complete replacement.

### 6.1 Solid foundation — keep building on it

- **Symfony 7.x backbone**: industry-standard, long-term maintainable.
- **Page module loader pattern**: clean, scalable, zero ceremony to
  add pages. This is the right shape for the eventual full app.
- **Legacy session bridge**: enables incremental cutover, page by
  page, with no user-visible disruption. This is the right migration
  strategy.
- **Self-hosted vendor assets**: no third-party network dependency,
  no GDPR exposure from outbound CDN requests.
- **CSS-variable theme system**: extends to any component without
  per-page work.
- **No build step**: ESM-first posture, modern browsers handle this
  natively. The page-module loader was designed so a bundler can be
  introduced later as a pure optimization, without rewriting code.
- **No jQuery**: forward-looking, reduces dependency surface.

### 6.2 Gaps to close before this can replace production

- **Bundling / minification.** Without it, a cold page load fetches
  many small modules, blocking each subsequent import on round-trip
  latency. The architecture supports adding a bundler as a build-time
  optimization later; it has not been needed yet.
- **Test pipeline.** No automated tests are added on this branch
  (frontend or backend). For something that will sit in front of
  every user, this needs a baseline before the first non-trivial
  rollout: PHPUnit on controllers, at minimum smoke tests on the
  page modules.
- **Page coverage.** Two pages are ported (`/cache/{wp}`, `/livemap`).
  Replacing the legacy frontend means home, search, profile, lists,
  log lists, owner views, statistics, admin, registration, password
  reset, account settings, notifications, and more. The pattern
  scales, but the work to apply it is sizeable.
- **Doctrine migrations.** None on this branch. Any real schema work
  the new app introduces will need them; the precedent we set with
  the stored-procedure patch (kept under `.ddev/` as dev-only) is
  the right call for *that specific case* and should not be
  generalized.
- **Strict-mode SQL in production-grade code.** The dev-only
  `no_strict.cnf` papers over a legacy bug in
  `htdocs/newcache.php`. New Symfony code should be written assuming
  strict mode and tested under it — that is the standard the
  next-gen app should hold itself to, even while the legacy tree
  cannot.
- **Accessibility / i18n discipline.** Twig and the existing trans
  filters give us the right tools; we have not yet set a baseline
  for which locales are first-class, what level of WCAG conformance
  we target, or who reviews PRs against it.
- **Observability.** The legacy site has its own logging story; the
  Symfony side needs structured logging, error reporting, and
  performance telemetry before it can stand on its own.

### 6.3 Verdict

The foundation is sound and the migration strategy (legacy-session
bridge + page-by-page cutover) is realistic. The two pages on this
branch demonstrate the pattern works. But this branch alone is not
the next-gen product — it is the platform on which that product can
be built. Treat §6.2 as the agenda for getting there.
