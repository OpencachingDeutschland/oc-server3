# Tabulator, AG Grid & Twig SSR Demo — Documentation & Speaker Notes

## Quick Reference

| | |
|---|---|
| **Start server** | `ddev start` (or `ddev restart` if already running) |
| **Tabulator demo** | `https://try-opencaching.ddev.site/backend/tabulator-demo` |
| **AG Grid demo**   | `https://try-opencaching.ddev.site/backend/ag-grid-demo` |
| **Twig SSR demo**  | `https://try-opencaching.ddev.site/backend/twig-ssr-demo` |
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

Three proof-of-concept pages in the new Symfony-based Opencaching backend, comparing three
different architectural approaches to table rendering:

| Demo | Approach | JS dependency |
|---|---|---|
| [Tabulator](https://tabulator.info) | Client-side, declarative config, built-in Ajax URL | ~500 KB CDN |
| [AG Grid Community](https://www.ag-grid.com) | Client-side, explicit fetch(), more powerful | ~700 KB CDN |
| Twig SSR | Server-side, plain HTML, no JS | None |

All three pages show the same data (up to 25 caches per page from the local DB) and the
same features: sorting, column filters, pagination.

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

### `htdocs_symfony/src/Controller/Backend/TwigSsrDemoControllerBackend.php` *(new)*
**A single controller with one route — no separate data endpoint.**

| Route | Purpose |
|---|---|
| `GET /backend/twig-ssr-demo` | Renders the complete HTML page with data |

The controller reads `?sort=`, `?dir=`, `?q=`, and `?page=` from the query string,
builds two SQL queries (COUNT + data) with LIMIT/OFFSET, and passes everything to the
template. Every interaction — sort, filter, page change — is a new HTTP request to the
same route.

**Why one route instead of two:** With SSR there is no separation between "page" and
"data" — the controller delivers both in one step. This is the classic
request-response model.

---

### `htdocs_symfony/templates/backend/twigSsrDemo/index.html.twig` *(new)*
**The SSR demo template — pure Twig, no JS.**

- Extends `backend/base.html.twig`
- No CDN, no external dependency
- A Bootstrap 5 `<table>` with `table-striped table-hover`
- Column headers as sort links (`<a href="?sort=...&dir=...">`)
- Sort indicator (▲/▼) via Twig conditional
- Search form (GET) with hidden `sort`/`dir` fields so filter and sort stay active simultaneously
- Bootstrap pagination with first/last page links and `…` separators

**Why no JS:** The point of this demo is to show that sorting, filtering, and pagination
can be handled entirely server-side. The tradeoff: every interaction triggers a page
reload and the server must re-run the query.

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

The third page at `/backend/twig-ssr-demo` shows the opposite approach: no JavaScript, no
library, no CDN. Symfony and Twig render the complete page including data — sorting and
filtering are plain links with query parameters, pagination is LIMIT/OFFSET in SQL. Every
interaction is a new HTTP request. This is exactly the pattern the legacy PHP site uses today.

Now look at the templates side by side. The Tabulator template is 44 lines. AG Grid is 54.
The Twig SSR template is 116 — nearly three times as long — and that does not count the
extra 50 lines of PHP in the controller. And despite all that code it does *less*: no
movable columns, no instant response, every sort or filter change reloads the page.

What is all that extra code doing? It is reimplementing, by hand and badly, exactly what
the libraries give you for free: sort link generation, direction toggling, threading the
current filter through hidden form fields so it does not get lost when you click a column
header, and a paginator with ellipsis logic. That logic lives in the template and
controller instead of in a well-tested library — and every new feature you want (column
resize, row selection, CSV export) means writing more PHP and Twig by hand.

Twig SSR is absolutely the right tool for static content, forms, and read-once detail
pages — that is what it is designed for. But for a sortable, filterable data table it is
the wrong tool. The complexity ends up in the wrong place, the UX is worse, and the code
is harder to maintain.

So: **the path not to go is pure Twig SSR for interactive data tables.** The JS library
approach is not modern for its own sake — it is genuinely less code, more capability, and
a better separation of concerns. The remaining question is which library. That is what we
want your feedback on today."
