# Frontend Architecture Patterns

## API modules

One file per domain, named `<domain>Api.js`:

| Module | Scope |
|--------|-------|
| `mapApi.js` | Map-specific endpoints: city geocoding, live bbox search, waypoints, GPX tracks |
| `cacheApi.js` | Cache endpoints not tied to the map: search |

Future modules follow the same pattern: `userApi.js`, `logApi.js`, etc.
The generic `api.js` is gone. A true app-wide fetch wrapper, if ever needed,
would live in a module named `api.js` at that point.

## pageMap.js — shared map bootstrap

Any non-livemap page that needs a map calls exactly:

```js
import { initPageMap } from './pageMap.js';
const mapMod = await initPageMap(uniCacheWPs);
```

Template requirements:
```twig
{% block data_map_js %}true{% endblock %}
{% block page_content_after %}<div id="mapRoot"></div>{% endblock %}
```

`initPageMap()` handles everything: setting height, applying `.page-map-active`
(which governs height via `map.css` with `!important` to resist `visualViewport`
drift), calling `invalidateSize({pan:false})` to correct Leaflet's internal state
(loader.js eagerly imports map.js at page load — on pages where `#mapRoot` has no
CSS height at that moment, Leaflet initialises with `_size.y = 0`), then
`handleWPs()`.

Returns the `map.js` module so callers can cache extra exports:
```js
const m = await initPageMap([gc]);
mapHandleWPs        = m.handleWPs;
mapUpdateMarkerIcon = m.updateStaticMarkerIcon;
```

### Known architectural debt

`loader.js` eagerly imports `map.js` on every `data-map-js="true"` page, running
`L.map('mapRoot')` before the user has asked for the map (search page). The
`invalidateSize` pre-call in `pageMap.js` corrects the state, and an
`overlayremove` scroll-snap guards against residual view jumps on live-mode
toggle. The real fix is a `data-map-js="deferred"` mode in `loader.js` that loads
Leaflet assets but skips the `import('./map.js')` — deferred for a future commit.

## uniCacheWP is the only interface to the map

`createMarker()` and `getIcon()` in `map.js` / `mapIcons.js` consume
`uniCacheWP`-format objects. Any backend endpoint that feeds data to the map must
return this shape. The `/api/caches/search` endpoint returns uniCacheWP directly,
including user-context fields (`isFound`, `isOwned`, `isDisabled`, `hasPCN`, etc.)
resolved via EXISTS subqueries. Passing raw DB rows and converting client-side is
an antipattern — the server owns the shape.

## Tabulator conventions

- Layout: `fitColumns` with explicit `widthGrow`/`widthShrink` on every column.
  `fitDataFill` expands the last column unconditionally — avoid it.
- Fixed-width columns (D, T, checkboxes): `widthGrow:0, widthShrink:0, resizable:false`.
- Truncatable columns: `tooltip:true` (or a custom tooltip function for nested fields).
  The Bootstrap5 Tabulator theme already applies `text-overflow:ellipsis` globally;
  `tabulator_oc.css` adds `.cell-truncate` as an opt-in utility if ever needed
  without the Bootstrap theme.
