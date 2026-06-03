# CLAUDE.md — OC Symfony Port

Guidance for Claude when working in this tree. **READ THE WORKFLOW SECTION FIRST.**

## Project

Symfony 7.x rewrite of opencaching.de's legacy PHP codebase. Living in
`htdocs_symfony/` alongside the legacy `htdocs/` tree. Branch: `feature/ui-refresh`
(fork: `hxdimpf/oc-server3`). The longer-form change log lives in `ui-refresh-changes.md`.

OKAPI fork: `hxdimpf/okapi`, branch `oc4-combined`. Vendored into `htdocs/vendor/opencaching/okapi/`.

**Note:** this `CLAUDE.md` is local-only guidance for Claude — it is not committed to the repo.

---

## ⚠️  Development workflow

Two repos, same cycle for both:

```
edit (local) → commit (local) → push (local) → ssh ocde "git pull" → test in ddev
```

| Repo | Local path | ocde path | Branch |
|------|-----------|-----------|--------|
| `hxdimpf/oc-server3` | `/Users/baiti/src/oc-server3/` | `~/opencaching/oc-server3/` | `feature/ui-refresh` |
| `hxdimpf/okapi` | `/Users/baiti/src/okapi/` | `~/okapi/` | `oc4-combined` |

### oc-server3 cycle (legacy htdocs + htdocs_symfony)

```bash
# 1. edit locally, then:
git add <files>
git commit -m "feat(symfony): ..."
git push origin feature/ui-refresh

# 2. pull on ocde and test:
ssh ocde "cd ~/opencaching/oc-server3 && git pull"
# reload in browser — PHP changes are live immediately in ddev
```

### okapi cycle

```bash
# 1. edit locally in /Users/baiti/src/okapi/, then:
git add <files>
git commit -m "fix(...): ..."
git push origin oc4-combined

# 2. pull on ocde and deploy to vendor:
ssh ocde "cd ~/okapi && git pull"
ssh ocde "cd ~/opencaching/oc-server3 && ddev exec 'cd htdocs && composer update opencaching/okapi'"

# 3. commit the updated composer.lock through the oc-server3 cycle:
ssh ocde "cd ~/opencaching/oc-server3 && git add htdocs/composer.lock && git log --oneline -1"
# then pull that commit locally:
git pull origin feature/ui-refresh   # to stay in sync
```

Wait — step 3 above is the one exception where a commit still happens on ocde (the composer.lock
update is driven by ddev running there). Commit it on ocde, push, then pull locally to stay in sync:

```bash
# on ocde:
ssh ocde "cd ~/opencaching/oc-server3 && git add htdocs/composer.lock && git commit -m 'chore(okapi): update vendor to latest oc4-combined'"
ssh ocde "cd ~/opencaching/oc-server3 && git push origin feature/ui-refresh"
# locally:
git pull origin feature/ui-refresh
```

### Rules

- **Never edit files directly on ocde** — all edits happen locally
- **Never `git commit`/`git push` on ocde** — except the composer.lock exception above
- **ocde is the test environment only** — read-only SSH inspection is fine
- **Do not develop in `htdocs/vendor/opencaching/okapi/`** — that is composer-managed; edit in `/Users/baiti/src/okapi/` instead

### Remotes

**oc-server3** (local at `/Users/baiti/src/oc-server3/`):
```
origin    git@github.com-hxdimpf:hxdimpf/oc-server3.git
upstream  git@github.com:OpencachingDeutschland/oc-server3.git
```

**okapi** (local at `/Users/baiti/src/okapi/`):
```
origin    git@github.com-hxdimpf:hxdimpf/okapi.git
upstream  https://github.com/opencaching/okapi.git
```

SSH identity for both: `~/.ssh/id_rsa_hxdimpf` via host alias `github.com-hxdimpf`.

Per-repo git identity is set locally on both clones (`user.name=hxdimpf`, `user.email=hxdimpf@gmail.com`).
Do not use global git config for commits in these repos.

### ddev

ddev runs on ocde only. Restart needed only when `.ddev/config.yaml` changes:
```bash
ssh ocde "cd ~/opencaching/oc-server3 && ddev restart"
```

---

## Architecture

### Application

- **Framework:** Symfony 7.x, PHP 8.4, Doctrine DBAL (no ORM), Doctrine Migrations
- **Templates:** Twig under `templates/`
- **Frontend:** vanilla ES modules under `public/js/`, self-hosted vendor under `public/vendor/`
- **No Webpack/Encore** — bundling was removed; the page loads modules directly
- **DB:** MariaDB; legacy OC schema (`caches`, `cache_logs`, `cache_logs_archived`, `coordinates`, `log_types`, `sys_sessions`, …)
- **Migrations namespace:** `OcMigrations`

### Page-module loader pattern

- `templates/base.html.twig` includes `<script type="module" src="loader.js">`
- Pages set `<body data-page="<name>">` and optionally `data-map-js="true"`
- `loader.js` dynamic-imports `public/js/<name>.js` and calls its exported `init()`
- Map pages additionally trigger Leaflet bootstrapping via the verbatim gcxm `map.js`

### Self-hosted vendor

Frontend libraries under `public/vendor/<package>/`:
- Leaflet 1.9.4, leaflet-draw 1.0.4, leaflet.markercluster 1.5.3
- Tabulator 6.2.1
- Bootstrap 5.3.0
- FontAwesome (free)

No CDN references. If adding a vendor lib, drop it into `public/vendor/<package>/`.

### Auth bridge

`LegacyCookieAuthenticator` reads the `ocdevelopmentdata` cookie set by the
legacy PHP, validates against the `sys_sessions` table, and authenticates
the Symfony request. `access_control` in `config/packages/security.yaml`
gates everything except `/login`. The navbar shows a username dropdown
when authenticated.

See memory: `project_oc_symfony_session_bridge.md`.

### OKAPI integration

OKAPI is vendored at `htdocs/vendor/opencaching/okapi/` from `hxdimpf/okapi` on branch
`oc4-combined`. That branch merges three features pending upstream:
- `issue_#601_fieldnotes` — draft log / fieldnotes upload
- `listservices` — 7 geocache list management services
- `save_user-coords` — save user coordinates

Do not edit vendor directly. Edit in `/Users/baiti/src/okapi/` and follow the okapi cycle above.

### Theming

CSS custom properties on `:root` and `[data-theme="dark"]`:
- `--oc-bg-body`, `--oc-bg-surface`, `--oc-bg-navbar`
- `--oc-text-primary`, `--oc-text-secondary`, `--oc-text-inverse`
- `--oc-link-color`, `--oc-link-hover-color`
- etc.

Use variables in base styles, override only inside `[data-theme="dark"]`.
Bootstrap `.form-select` / `.form-control` need explicit cascade and
`color-scheme: dark` for native browser controls.

---

## Conventions

### Commit messages

Conventional commit prefixes:
- `feat(symfony):` — new feature in the Symfony port
- `fix(symfony):` — bug fix
- `refactor(symfony):` — refactor with no behavior change
- `docs(symfony):` — documentation only
- `chore(ddev):` — ddev / dev-environment changes
- `chore(okapi):` — okapi vendor/composer changes
- `fix(okapi):` — bug fix in the okapi fork (commit in `/Users/baiti/src/okapi/`)

### Commit splitting

When a change spans multiple concerns, propose a split with rationale
before executing. Bundle only when patch-level surgery to separate hunks
adds no review value.

See memory: `feedback_commit_splitting.md`.

### Frontend

- Vanilla ES modules, no build step
- One module per page under `public/js/<page>.js`, must export `init()`
- Leaflet controls: always use `L.Control.extend()`, never append raw DOM to the map container
- Use `L.DomEvent.disableClickPropagation()` and `L.DomEvent.on()`
- Mobile: use `window.visualViewport.height` for viewport sizing

See memory: `feedback_leaflet_controls.md`.

### Tabulator tables

- **CSS theme:** Load `/vendor/tabulator/tabulator.min.css` (base theme, proven alignment).
  - Use `tabulator.min.css`, NOT `tabulator_bootstrap5.min.css` (causes header/data misalignment)
  - Overrides go in `/css/tabulator_oc.css` (color/border rules only, no padding/height)
- **OC codes:** Use `cssClass: 'cell-occode'` on columns that display OC codes (e.g., `referenceCode`, `wpOc`)
- **Initialization:** `new Tabulator(id, { layout: 'fitColumns', height: '70vh', renderVertical: 'virtual', selectableRows: true, columns: [...] })`

See memory: `feedback_tabulator_styling.md`.

### Backend

- Doctrine DBAL `Connection`, not ORM
- Controllers under `src/Controller/App/`
- Stored procedures from the legacy schema are still in play — `sp_update_logstat` has a known NULL-handling bug patched on dev, not upstreamed (see memory `project_oc_sp_update_logstat_null_bug.md`)
- Password-protected logs: verify `caches.logpw` matches the request `logpw` field for POST and PUT log endpoints

---

## When in doubt

- Read `ui-refresh-changes.md` for the longer narrative of what shipped
- `git log --oneline -20` locally (the clone is authoritative now)
- Check memory files for OC Symfony context (`project_oc_*`, `feedback_oc_*`)
- Ask the user before any action that writes to ocde beyond `git pull`
