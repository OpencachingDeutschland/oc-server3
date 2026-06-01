# Architecture Decision: uniCache as the canonical API contract

**Status:** Decided  
**Scope:** All API endpoints in `src/Controller/App/`

---

## Decision

**uniCache is the one and only object exchanged between backend and frontend.**

- The backend is always responsible for producing a properly shaped uniCache object.
- All frontend modules operate exclusively on uniCache — they never decode raw DB rows or OKAPI payloads.
- When the frontend sends data to the backend (cache creation, log submission, coordinate update), the backend receives a uniCache-shaped payload and is responsible for mapping it to the database schema.

Where the transformation happens does not matter to the frontend. What matters is that the contract is guaranteed.

---

## Rationale

The original `uniCache.js` transformation layer was inherited from GCxM, where it was necessary because data arrived from multiple external providers (GC, OC, AL) with incompatible shapes. In this application:

- There is only one data source: the OC MariaDB schema.
- We control both the backend and the frontend.
- Moving the transformation to PHP eliminates the OKAPI intermediary from the frontend entirely.
- Additional clients (future mobile app, other OC country instances) can consume the same API without each reimplementing the transformation.

---

## The uniCache shape (OC edition)

| Field | Type | Notes |
|-------|------|-------|
| `_id` | string | OC waypoint code (`OCABC123`) |
| `platform` | `'OC'` | Always `'OC'` in this application |
| `referenceCode` | string | Same as `_id` |
| `name` | string | Cache name |
| `shortName` | string | Truncated at 25 chars for map labels |
| `geocacheType` | `{ id: number, name: string }` | OC type id 1-10 per `OC_CACHE_TYPES` |
| `geocacheSize` | `{ id: number, name: string }` | OC size id per `OC_SIZE_TYPES` |
| `difficulty` | number | 1–5 |
| `terrain` | number | 1–5 |
| `lat` | number | Effective latitude (corrected if hasCC, else posted) |
| `lon` | number | Effective longitude |
| `postedCoordinates` | `{ latitude, longitude }` | Always the listing coordinates |
| `correctedCoordinates` | `{ latitude, longitude } \| null` | User's corrected coordinates, or null |
| `hasCC` | boolean | True if correctedCoordinates is set |
| `publishedDate` | string | ISO date `YYYY-MM-DD` |
| `ownerAlias` | string | Owner username |
| `ownerCode` | string | Owner username (same source, kept for compat) |
| `status` | `'Active' \| 'Disabled' \| 'Archived'` | Native OC status strings — no OKAPI translation |
| `isArchived` | boolean | Derived from status |
| `isDisabled` | boolean | Derived from status |
| `isFound` | boolean | Current user has a Found log |
| `isDNF` | boolean | Current user has a DNF, no Found |
| `hasPCN` | boolean | Current user has a personal note |
| `hasCC` | boolean | Current user has corrected coordinates |
| `isOwned` | boolean | Current user is the cache owner |
| `isOcOnly` | boolean | Cache has the OC-only attribute |
| `pcn` | string \| null | Personal cache note text |
| `findCount` | number \| null | Total found logs |
| `favoritePoints` | number \| null | Recommendation count |
| `foundDate` | string \| null | Date of current user's Found log |
| `dnfDate` | string \| null | Date of current user's DNF log |
| `location` | `{ country, state, countryCode }` | Geographic location |
| `ianaTimezoneId` | string \| null | Timezone (not yet populated) |
| `requiresPasswd` | boolean | Cache has a log password |
| `logPasswd` | string \| null | Log password (only sent to owner) |

### Fields inherited from GCxM multi-platform origin (vestigial in OC-only context)

These fields are present for frontend compatibility but have no meaningful value in an OC-only deployment:

| Field | Always | Reason kept |
|-------|--------|-------------|
| `isCached` | `false` | GCxM offline cache concept |
| `isGuessable` | `false` | GCxM mystery-cache solver |
| `isPartial` | `false` | GCxM partial data marker |
| `hasDraft` | `false` | GCxM field note drafts |
| `isIgnored` | `false` | Platform-level ignore list |
| `isWatched` | `false` | Not yet implemented |
| `isFavorited` | `false` | Not yet implemented |

These can be removed from the contract once the frontend no longer references them, or promoted to real OC features.

---

## Current compliance state

| Endpoint | State | Notes |
|----------|-------|-------|
| `GET /api/caches/live` | Partial | Returns close to uniCache shape but not via `ocToUniCache` |
| `GET /api/cache/{wp}` | Non-compliant | Returns OKAPI shape; `uniCache.js` transforms in frontend |
| `GET /api/caches/search` | Partial | Returns flat rows, not full uniCache |
| `GET /api/caches/waypoints` | Out of scope | Waypoint objects, not cache objects |
| Write endpoints (log, coords, note) | Partial | Accept ad-hoc payloads, not uniCache |

---

## Migration path

1. **`GET /api/cache/{wp}`** — highest priority. Move `ocToUniCache()` logic to PHP. Frontend stops calling `uniCache.js`'s `ocToUniCacheWP()`.
2. **`GET /api/caches/live` and `/api/caches/search`** — align the returned shape to full uniCache. Currently close but not identical.
3. **Write endpoints** — define which uniCache fields are relevant for each write operation and validate them in PHP.
4. **`uniCache.js`** — once all endpoints comply, this file reduces to just the `OC_CACHE_TYPES` / `OC_SIZE_TYPES` lookup tables shared with the frontend for rendering.

---

## PHP implementation notes

- A `UniCacheBuilder` service (or static factory) should own the mapping from DB row to uniCache array.
- Status mapping: DB integer → `'Active' | 'Disabled' | 'Archived'` directly — no OKAPI strings.
- The builder receives the authenticated user's ID to populate `isFound`, `isDNF`, `hasPCN`, `hasCC`, `isOwned`.
- For write operations, a corresponding `UniCacheMapper` translates the incoming uniCache fields to SQL column names.
