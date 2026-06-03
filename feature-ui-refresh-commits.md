# feature/ui-refresh — Commit Log
**Branch:** `feature/ui-refresh` on `hxdimpf/oc-server3`
**Base:** `upstream/development`
**Commits ahead:** 93

---

## Foundation

| Hash | Description |
|------|-------------|
| `739816d9` | feat: eliminate jQuery and replace with vanilla JS compatibility layer |
| `082a5822` | chore: remove webpack encore build system; add frontend source to version control |
| `eb7298a1` | chore(ddev): portable bootstrap for fresh ddev restart |
| `211522f4` | remove: dead Map navbar link, MapsController, MapsControllerBackend, MapsRepository, maps/index template |
| `ad20a5dd` | fix: correct route name prefixes (app_ auto-applied by namePrefix config) |
| `6bc5f835` | feat: add light/dark theme system with CSS custom properties |
| `c1b5bfa8` | feat(symfony): bridge legacy OC session; gate routes; map+log fixes |
| `41a3669d` | refactor(symfony): self-host vendor; flatten public/js; theme login |

---

## Live Map (`/livemap`)

| Hash | Description |
|------|-------------|
| `75728976` | feat: live map in proper LiveMapController, fix navbar routes |
| `5ffe64a4` | fix: live map layout offset, add map.css with dark mode Leaflet control styles |
| `b93b257c` | fix: waypoint markers now render correct colored icons instead of grey '...' boxes |
| `edf725c4` | feat: OSM city/village search on live map |
| `87875f4d` | feat: add scroll-to-top button to live map (top-center Leaflet control) |
| `4360b999` | fix: skip scroll-to-top control on /livemap to avoid mobile city-search collision |
| `c472cc7c` | feat(symfony): /livemap city/village search keyboard UX |
| `53d15482` | fix(symfony): livemap badges — resolve userId correctly, drop duplicate route |
| `3c2bbd28` | chore(livemap): mark insertion point for future per-user settings |
| `d8b58db8` | feat(symfony): shift/ctrl-click on /livemap to create cache; guard L.GPX |
| `f7a2abd3` | feat(symfony): OC-only badge on map markers |
| `82afc80f` | fix(symfony): livemap — plot markers at corrected coordinates when user has CC |

---

## Cache Detail (`/cache/{wp}`)

| Hash | Description |
|------|-------------|
| `cba53154` | feat: client-driven cache detail page (/cache/{wp}) with Tabulator |
| `78afc883` | feat: port gcxm /explore cache detail to /cache/{wp}; rename map3 → map |
| `a902bfac` | fix(symfony): cache detail — log polish after live testing |
| `ef915a4f` | feat(symfony): cache detail — log password + in-place state refresh |
| `7fee5847` | feat(symfony): cache detail — expose search_time as "Time required" |
| `18d58978` | fix(symfony): map popup — PCN hover popover, Published undefined, detail row order |
| `07ab19f9` | feat(symfony): newcache — event type locks size/D/T, fix dark mode disabled select |
| `63221bf3` | feat(symfony): light-island for dark-mode-unsafe cache listings |
| `b1f1a5f1` | refactor(symfony): gate desc-dark-unsafe light-island on dark theme only |
| `b4905b86` | feat: add copy-to-clipboard button to routing marker popup |

---

## Cache Search (`/caches/`)

| Hash | Description |
|------|-------------|
| `4fa55a4a` | feat(symfony): search-caches page — filter form, Tabulator results, pageMap |
| `a93a9bf9` | feat(symfony): search page — OC Only filter, narrower D/T, richer map data |
| `b44c4657` | feat(symfony): search table — virtual scroll, raise result cap to 1000 |

---

## Landing Page (`/`)

| Hash | Description |
|------|-------------|
| `2f2d4731` | feat(symfony): landing page — 20/60/20 grid with stats, i18n, dark mode buttons; fix /cache/new route |

---

## New Cache (`/cache/new`)

| Hash | Description |
|------|-------------|
| `640f2eec` | feat(symfony): new cache creation page with unified coordinate input and map picker |

---

## User Search & Profile

| Hash | Description |
|------|-------------|
| `8dc748c0` | refactor(symfony): search-users — client-side Tabulator, JSON API, full i18n |
| `339d0b52` | fix(symfony): search-users — open to all auth users, drop ROLE_TEAM guard |
| `05f452dd` | feat(symfony): navbar Support dropdown + role-aware user search |
| `534c9cd4` | feat(symfony): user profile refactor, map popup i18n, layout/density improvements |

---

## Support / Backoffice

| Hash | Description |
|------|-------------|
| `f50b0390` | feat(symfony): reported caches — client-side Tabulator, JSON API, status filter |
| `023afa69` | refactor(symfony): rename /backend → /backoffice throughout |
| `b31e75fd` | fix(symfony): teamlist title uses h4 matching other pages |

---

## Internationalisation (i18n)

| Hash | Description |
|------|-------------|
| `6ce8bb99` | feat(symfony): add EN/DE translation catalogues |
| `d32f4e5a` | feat(symfony): add locale switcher with session persistence |
| `b27b4d58` | feat(symfony): i18n infrastructure — window.OCI18n + t() helper for JS strings |
| `8c305079` | feat(symfony): i18n — search page fully translated (DE) |
| `fb9480df` | fix(symfony): wrap remaining hardcoded strings in login and cache detail |
| `b6abc04f` | fix(symfony): remove duplicate User ID key in DE translations |
| `2905b8ad` | fix(symfony): remove 8 duplicate keys from DE translation file |

---

## Frontend Architecture

| Hash | Description |
|------|-------------|
| `2298a0b4` | refactor(map): adopt gcxm-verbatim loader pattern |
| `e5e13737` | refactor(symfony): rename app.js → loader.js |
| `45ddfdc9` | refactor(symfony): api.js → mapApi.js + cacheApi.js; introduce pageMap.js |
| `995267e0` | refactor(symfony): drop upstream-project naming from frontend modules |
| `d8ef5741` | refactor(symfony): OC waypoint icons and drop multi-platform scaffolding |
| `123fb722` | refactor(symfony): strip GC cruft, OC-native filter, waypoint PNGs |
| `84e0a980` | docs(symfony): frontend architecture patterns — API modules, pageMap, uniCacheWP, Tabulator |

---

## Tabulator Styling

| Hash | Description |
|------|-------------|
| `25a6165f` | refactor(symfony): tabulator density — compact rows, proper alignment, no borders |
| `423f8afa` | fix(symfony): tabulator layout — revert flex overrides, match GCxM minimalism |
| `23748f5a` | refactor(symfony): port tabulator_oc.css from GCxM proven design |
| `e85ef514` | fix(symfony): apply monospace to anchor inside cell-occode cells |
| `ffa15d82` | fix(symfony): remove underline from all links inside tabulator cells |

---

## Early-Access Toggle (Legacy ↔ Symfony)

| Hash | Description |
|------|-------------|
| `70065f29` | feat: add new UI early-access toggle for Symfony rebuild |
| `4d98173a` | fix: prevent infinite redirect loop in new UI toggle |
| `108865b3` | fix: redirect to try-opencaching.ddev.site for new UI |
| `1a2e8cbc` | fix: map /search.php to /caches/ route in Symfony |
| `79de3ba0` | feat: route legacy /map.php to Symfony /livemap via toggle |
| `40bcf3bf` | feat: route /map3.php to Symfony /livemap |
| `11f17dec` | fix: style 'Try new UI' toggle to match legacy navbar |

---

## Legacy Navigation Cleanup

| Hash | Description |
|------|-------------|
| `ca5842ae` | fix: update Map menu to point to map.php instead of map2.php |
| `16f86753` | refactor(symfony): apply GCxM tabulator styling and add map.php entry point |

---

## UI / Theme Polish

| Hash | Description |
|------|-------------|
| `11306369` | style(symfony): navbar active link gets background tint and rounded corners |
| `f0c194e7` | fix(symfony): mobile — remove user-scalable, touch-action on map, responsive padding |
| `212ba05c` | fix(symfony): init Leaflet map with default center to prevent drag errors before fitBounds |
| `70dae91b` | revert(symfony): remove touch-action:none and default center from map — broke desktop |
| `7a6d2b0e` | fix(symfony): remove underline from links in Leaflet map popups |
| `00ba200c` | fix(symfony): remove link underlines globally via base a rule in oc-style.css |
| `b46e5ff7` | fix(symfony): consistent left/right margin on all content pages; livemap stays full-width |
| `964fcc06` | fix(symfony): consistent top margin on all content pages via main.content padding |
| `41185137` | fix(symfony): popover background and text always track OC theme variables |
| `5cf6e3d5` | fix(symfony): popover bg distinct from Leaflet popup — darker in light, lighter in dark |

---

## Housekeeping

| Hash | Description |
|------|-------------|
| `964c7ed4` | chore(symfony): remove kitchensink — dev scaffolding, not needed |
| `314aeee2` | fix(symfony): remove kitchensink menu entry — route was deleted |
| `4bbd550c` | refactor(symfony): rename Backend → Backoffice in files, namespaces, and template paths |
| `c241f463` | chore(symfony): remove stray files accidentally staged in wrong locations |
| `6a2fff8d` | fix(symfony): update route scanner path from Backend to Backoffice |

---

## Code Quality

| Hash | Description |
|------|-------------|
| `b72aa89a` | refactor(symfony): gpx.js — OC-only type table, delete GC-specific decode functions |
| `1e1920c3` | refactor(symfony): uniCache.js — rename ocToGC* maps, drop OKAPI status strings |
| `55a0992d` | refactor(symfony): iconPaths.js — remove GC-only and GC-conflicting type icons |
| `42a06da8` | refactor(symfony): apiFetch wrapper — text() first, safe JSON parse, throws on non-2xx |

---

## Tooling

| Hash | Description |
|------|-------------|
| `04b2c5ac` | chore(symfony): add check-translations.py to catch duplicate YAML keys |

---

## Docs

| Hash | Description |
|------|-------------|
| `a2ac97b9` | docs(symfony): add ui-refresh-changes.md team brief |
| `811f10fe` | docs(symfony): design notes for desc_dark_unsafe / light-island |
| `960a2f16` | docs(symfony): drop unrelated platform discussion from desc-dark-unsafe notes |
| `dff6cc2c` | docs(symfony): architecture decision — uniCache as canonical API contract |
