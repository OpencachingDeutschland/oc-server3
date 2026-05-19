# desc_dark_unsafe — design and logic

## The problem

Cache listings on OC were authored over ~20 years assuming a light theme.
Many use hard-coded colors and backgrounds:

- `<font color="#000080">…</font>` — navy text on assumed-white
- `<font bgcolor="#FFFF00">` — yellow background
- inline `style="color:#000; background:#fff"` blocks
- `<table bgcolor="#eeeeee">` row backgrounds
- `<center><font size="3" color="darkgreen">` — combinations of the above

Rendered against a dark UI these are unreadable (dark text on dark page),
visually broken (jarring white-on-black islands inside otherwise-themed
chrome), or both.

A blanket "force every listing into a white card" works but is ugly: the
majority of listings use plain HTML that would render perfectly fine in
dark mode. Forcing every one into a light box makes the entire site look
like a flat-style PDF reader.

The answer chosen: **detect which listings carry dark-unsafe markup; only
those get rendered in a "light island" container, the rest follow the
site theme.**

---

## (1) How — the detection rules

A description is flagged dark-unsafe if its HTML contains any of:

| Pattern                             | Matches                                          |
|-------------------------------------|--------------------------------------------------|
| `<font[^>]*color=`                  | `<font color="…">`, `<FONT COLOR=…>`            |
| `<font[^>]*bgcolor=`                | `<font bgcolor="…">`                            |
| `style=.{0,300}color:`              | `style="color:#…"`, `style='…; color: red;'`    |
| `style=.{0,300}background`          | `style="background:…"`, `background-color:`, `background-image:` |
| `bgcolor=`                          | `<table bgcolor>`, `<td bgcolor>`, `<tr bgcolor>` |

Encoded as a single alternation, evaluated case-insensitively (MariaDB's
default `utf8mb4_general_ci` collation makes `REGEXP` case-insensitive):

```
<font[^>]*color=|<font[^>]*bgcolor=|style=.{0,300}color:|style=.{0,300}background|bgcolor=
```

### Calibration

The pattern is **intentionally broad**. False positives (flagging a row
that would actually render fine) are harmless — the listing renders in
a plain light card. False negatives (failing to flag a row that breaks)
leave a visibly broken page. So we err toward flagging.

Examples of *accepted false positives*:

- `style="color: inherit"` — would render fine, but we flag and light-card it
- `style="background: transparent"` — same
- `<font color="white">` on a `<div style="background:black">` (the rare case where the author *anticipated* a dark UI) — we still flag, still light-card; the listing looks the same as before

Examples we deliberately don't try to match:

- CSS-variable values (`color: var(--my-color)`) — not seen in legacy listings
- Background images via `<body background="…">` — listings don't have a `<body>`
- Authored `<style>` blocks — vanishingly rare on user-authored content

### Reach within the listing

`.{0,300}` bounds how far the regex scans after `style=` before giving up.
Inline styles in cache descriptions are short — 300 chars is generous
without making the engine work for nothing. The default MySQL/MariaDB
behaviour on `.` does not match newlines, which also helps.

---

## (2) When — the lifecycle of the flag

### One-time backfill

`migrations/Version20260519160000.php`:

```sql
ALTER TABLE cache_desc
  ADD COLUMN desc_dark_unsafe TINYINT(1) NOT NULL DEFAULT 0
  COMMENT 'Listing HTML carries hard-coded colors/backgrounds; render in light-island under dark theme';

UPDATE cache_desc
   SET desc_dark_unsafe = 1
 WHERE `desc` REGEXP '<font[^>]*color=|<font[^>]*bgcolor=|style=.{0,300}color:|style=.{0,300}background|bgcolor=';
```

Both statements run inside the same migration transaction. `down()`
drops the column for a clean rollback.

### On every cache_desc INSERT / UPDATE — **NOT YET WIRED**

When a user edits their listing, the save path must re-evaluate the
flag and write it. Otherwise: an author adds a `<font color>` after
the backfill, and the new content renders broken in dark mode because
the row stays flagged 0.

The Symfony port does not yet implement the listing-edit flow — that
still lives in the legacy PHP under `htdocs/`. When the edit flow is
ported, the same regex (implemented in PHP) must run before the
`UPDATE cache_desc` and the result written into `desc_dark_unsafe`.

Reference PHP implementation (to be added when the edit flow lands):

```php
function descIsDarkUnsafe(string $desc): bool
{
    return (bool)preg_match(
        '~<font[^>]*color=|<font[^>]*bgcolor=|style=.{0,300}color:|style=.{0,300}background|bgcolor=~i',
        $desc
    );
}
```

### Bulk re-detection (rules change)

If the rules grow or shrink, re-run the same `UPDATE … REGEXP` from
the migration body manually, or roll a new migration that recomputes.
The column is overwriteable; nothing depends on its history.

---

## (3) Where — the flow through the stack

### Database

`cache_desc.desc_dark_unsafe TINYINT(1) NOT NULL DEFAULT 0` — one bit
per description row, so a multi-language cache can have a dark-unsafe
German description and a clean English description and the right one
is picked at read time.

### Read path (Symfony, current wire-up)

1. `CachesController::detail()` — the `/api/cache/{wp}` action — selects
   `cd.desc_dark_unsafe` alongside `cd.desc, cd.hint, cd.short_desc,
   cd.desc_html, cd.language`.
2. The response payload includes `descDarkUnsafe: bool` next to
   `descHtml`. The `?? false` coalesce means the response degrades
   gracefully if the column is dropped (see Backout below).
3. `public/js/cache.js`:
   - `augmentForRender()` copies the flag onto the uniCacheWP as
     `uc.descDarkUnsafe`.
   - At render time (in `renderDetailPage()`-equivalent), the description
     element gets the class toggled:

     ```js
     descEl.classList.toggle('cache-description-light-island', !!gc.descDarkUnsafe);
     ```

4. CSS in `templates/app/caches/detail.html.twig`:

   ```css
   #cache-description.cache-description-light-island {
       background: #fff;
       color: #222;
       color-scheme: light;
       padding: 0.75rem 1rem;
       border-radius: 0.25rem;
   }
   ```

   `color-scheme: light` is the key bit — it tells the browser to use
   light-theme defaults for any native widgets (scrollbars, form
   controls, even SVG `currentColor` defaults) inside the container,
   regardless of the page-level `data-theme="dark"`.

### Backout

`ddev exec 'cd htdocs_symfony && bin/console doctrine:migrations:migrate prev --no-interaction'`

Runs `down()`, drops the column. Frontend keeps working because:

- The `?? false` in `CachesController` returns false for every cache.
- `descDarkUnsafe` on the JS side is always false.
- `.cache-description-light-island` is never applied; descriptions
  render in whatever theme the page is using — i.e., the pre-feature
  behaviour.

No code change required to back out; the migration `down()` alone is
sufficient.

---

## gcxm: where would this logic live?

gcxm has a fundamentally different data lifecycle. The OC Symfony port
owns the source of truth; gcxm caches provider responses in MongoDB and
serves them, sometimes raw (e.g. `/explore`), sometimes cooked through
`uniCacheWP`.

There are three viable architectural placements:

### Option A — at fetch time, store on the mongo doc

When a provider response arrives, run the detection regex on the
description before persisting:

```js
doc._meta = { descDarkUnsafe: detectDarkUnsafe(doc.description) };
await mongo.upsert(doc);
```

- **Pro**: detect once per fetch, fast reads, all consumers see the flag.
- **Pro**: works for `/explore` (raw doc rendering) — the raw doc carries
  `_meta` as a sibling, which the page can read without "cooking".
- **Con**: pollutes the "raw" doc with a derived field. Mitigated by
  namespacing under `_meta` so it's visibly *not* provider data.

### Option B — at render time in JS

The renderer runs the regex against the description string before
injecting it into the DOM. No database changes, no server logic.

- **Pro**: simplest. No mongo migration, no API changes.
- **Pro**: works regardless of where the description came from (mongo,
  fresh fetch, paste, test fixture).
- **Pro**: same code can be shared between gcxm and the OC port frontend.
- **Con**: detection runs on every render (microseconds — almost free).
- **Con**: every renderer must remember to apply it. Easy to forget for
  a new page.

### Option C — at API serve time

gcxm's Node API attaches the flag to the response payload before
returning to the client, without persisting to mongo.

- **Pro**: keeps mongo strictly raw.
- **Pro**: single server-side detection point.
- **Con**: per-request CPU cost (recoverable via HTTP cache).
- **Con**: doesn't help server-rendered pages (if any).

### Recommendation

For gcxm, **Option A with `_meta.descDarkUnsafe`** is the right call:

1. `/explore` renders the raw mongo doc — but `_meta` is a sibling field,
   so the page can read `doc._meta.descDarkUnsafe` directly. No cooking
   required.
2. Cooked paths (`uniCacheWP`-shaped views) can copy `_meta` onto the
   shape during cooking.
3. Detection runs once per fetch (and per refresh) — the same point
   where the description is already being parsed and stored.
4. The raw provider payload is preserved verbatim under the doc's
   primary fields; `_meta` is unmistakably a gcxm-derived sidecar.

The regex itself is identical to the SQL backfill — same five
alternations — implementable as a single 4-line helper in JS:

```js
const DARK_UNSAFE_RE = /<font[^>]*color=|<font[^>]*bgcolor=|style=.{0,300}color:|style=.{0,300}background|bgcolor=/i;
export const detectDarkUnsafe = (html) => DARK_UNSAFE_RE.test(html ?? '');
```

When gcxm gets ported, this helper can live in a shared module (e.g.
`src/lib/descDarkUnsafe.js`) and be called from the provider-fetch path.
The OC Symfony port could also call it from a future PHP edit-flow port —
having the same rule expressed in two places is annoying but unavoidable
across the language boundary; the regex is the contract.

---

## Open questions / future work

- Implementation of the PHP-side detector in the listing-edit flow when
  that flow is ported from legacy.
- Decide whether to surface a small "this listing is shown on a light
  card because it uses legacy color formatting" affordance for users
  who notice the visual seam, or just let it be.
- Consider extending `color-scheme: light` to `<img>` rendering — some
  PNGs with transparent backgrounds were authored assuming a light page
  and look wrong against dark UI even outside a light-island.
