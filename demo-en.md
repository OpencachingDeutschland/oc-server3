# Tabulator & AG Grid Demo — Documentation & Speaker Notes

## Quick Reference

| | |
|---|---|
| **Start server** | `ddev start` (or `ddev restart` if already running) |
| **Tabulator demo** | `https://try-opencaching.ddev.site/backend/tabulator-demo` |
| **AG Grid demo**   | `https://try-opencaching.ddev.site/backend/ag-grid-demo` |
| **Login** | `root` / `developer` |
| **In navbar?** | No — navigate directly via URL |
| **PR** | https://github.com/OpencachingDeutschland/oc-server3/pull/957 |
| **Checkout** | `gh pr checkout 957` |

## Resources

| | |
|---|---|
| **Opencaching Wiki** | https://wiki.opencaching.de/index.php/Hauptseite |
| **Development** | https://wiki.opencaching.de/index.php/Entwicklung |
| **Git workflow** | https://wiki.opencaching.de/index.php/Entwicklung/Git |
| **Issue tracker** | https://opencaching.atlassian.net/jira/core/projects/RED/issues |
| **Mattermost** | https://devchat.opencaching.earth |

---

## What Was Built

Two proof-of-concept pages in the new Symfony-based Opencaching backend, showing
client-side table rendering with two libraries side by side:
[Tabulator](https://tabulator.info) and [AG Grid Community](https://www.ag-grid.com).
Both pages show the same data (up to 200 caches) and the same features — sorting, column
filters, pagination, movable columns — using the identical JSON endpoint pattern.

---

## Files Changed

### `htdocs_symfony/templates/base.html.twig`
**Bootstrap 5 added to the global base layout.**

- Added Bootstrap 5.3.3 CSS (`<link>` in the `stylesheets` block)
- Added Bootstrap 5.3.3 JS bundle (`<script>` in a new `javascripts` block)

**Why:** No CSS framework was loaded globally before. This makes Bootstrap available to all
pages that extend `base.html.twig`. Child templates can override or extend the `javascripts`
block to add their own scripts.

---

### `htdocs_symfony/src/Controller/Backend/TabulatorDemoControllerBackend.php` *(new)*
**A Symfony controller with two routes.**

| Route | Purpose |
|---|---|
| `GET /backend/tabulator-demo` | Renders the HTML page |
| `GET /backend/tabulator-demo/data` | Returns JSON (up to 200 caches) |

The JSON endpoint queries the `caches`, `user`, and `cache_status` tables via Doctrine DBAL
and returns: OC code, cache name, difficulty, terrain, owner username, status label.
Difficulty and terrain are stored as integers×2 in the DB and are divided by 2.0 here.

**Why two routes:** Separating the data endpoint from the page route is the standard "Ajax
table" pattern. The browser loads the page first, then Tabulator fetches the data
autonomously. The data endpoint is independently testable and reusable.

---

### `htdocs_symfony/templates/backend/tabulatorDemo/index.html.twig` *(new)*
**The demo page template.**

- Extends `backend/base.html.twig` (inherits navbar, sidebar, layout)
- Adds Tabulator 6.3.0 CSS (Bootstrap 5 theme) via CDN
- Adds Tabulator 6.3.0 JS via CDN
- A `<div id="caches-table">` that Tabulator mounts onto, configured with:
  - `ajaxURL` pointing to the `/data` route — Tabulator fetches data itself
  - Client-side pagination (25 rows/page)
  - Sortable and movable columns
  - Header filter inputs on Code, Name, Owner, Status columns
  - Number formatter for Difficulty and Terrain (one decimal place)

**Why in the template, not the controller:** Page-specific JS belongs in the Twig
`javascripts` block — this is the Symfony/Twig convention. The controller stays free of
HTML concerns.

---

### `htdocs_symfony/src/Controller/Backend/AgGridDemoControllerBackend.php` *(new)*
**Identical controller structure to the Tabulator demo.**

| Route | Purpose |
|---|---|
| `GET /backend/ag-grid-demo` | Renders the HTML page |
| `GET /backend/ag-grid-demo/data` | Returns JSON (up to 200 caches) |

Same SQL query and data shape as the Tabulator endpoint — the comparison is intentionally
fair: same data, same structure.

---

### `htdocs_symfony/templates/backend/agGridDemo/index.html.twig` *(new)*
**The AG Grid demo template.**

- Extends `backend/base.html.twig`
- AG Grid Community 31.3.4 CSS (`ag-theme-quartz`) via CDN
- AG Grid Community 31.3.4 JS via CDN
- A `<div id="caches-table" class="ag-theme-quartz">` configured with:
  - `domLayout: 'autoHeight'` — grid auto-sizes to page height
  - Client-side pagination (25 rows/page)
  - `floatingFilter: true` — filter inputs below column headers
  - `sortable`, `resizable`, movable columns by default
  - Number formatter for Difficulty and Terrain via `valueFormatter`
  - Data loaded via `fetch()` and passed via `gridApi.setGridOption('rowData', ...)`

**Difference from Tabulator:** AG Grid has no built-in `ajaxURL` option — you fetch the
data yourself with `fetch()` and hand it to the grid API explicitly. More code, but also
more control over loading state and error handling.

---

## What is NOT done (intentionally)

- No navbar entry — the demo is a dev/evaluation page, not a production feature
- No authentication bypass — the route requires `ROLE_TEAM` like all backend routes
- No data editing — read-only demo only

---

## Speaker Notes

"What you're looking at is a proof-of-concept for client-side table rendering in the new
Opencaching backend — the Symfony-based app that will eventually replace the legacy PHP site.

The page you see is rendered server-side by Symfony and Twig — just a shell with a navbar
and an empty div. As soon as the page loads, Tabulator fetches the data itself from a
dedicated JSON API endpoint at `/backend/tabulator-demo/data`. That endpoint returns up to
200 geocaches from our local database as plain JSON. No page reload, no server-side
pagination logic.

On the client side, Tabulator takes that JSON and gives us sorting on every column, header
filters on the text fields (try typing in the Name box), pagination with 25 rows per page,
and movable columns — all with zero custom code for those features.

We chose Tabulator because it integrates well with Bootstrap, has a permissive MIT license,
and requires minimal configuration to get production-grade UX. The entire JavaScript
initialisation is about 15 lines.

The architectural pattern here — a thin JSON API route plus a client-side table library —
is what we would use consistently if we adopt this approach. The backend just serialises
data; all the table behaviour lives in the browser. This makes it easy to add filtering,
export, or inline editing later without touching the server.

Now we have a direct comparison. The second page at `/backend/ag-grid-demo` shows exactly
the same thing built with AG Grid Community — a library used extensively in enterprise
environments, with no jQuery dependency. AG Grid is more powerful, but requires a bit more
explicit code: you fetch the data yourself and hand it to the grid API rather than just
supplying a URL. In return you get more control.

The core question for us is: which pattern do we want to use long-term? The pattern is
identical in both — a thin JSON API endpoint, a client-side table. Only the library differs.
Your feedback on UX, developer experience, and licensing helps us make that call."
