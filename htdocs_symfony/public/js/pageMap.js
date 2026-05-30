/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 *
 * pageMap.js — shared map bootstrap for every non-livemap page.
 *
 * Any page that needs a map calls:
 *
 *   const mapMod = await initPageMap(cacheWPs);
 *
 * Requirements for the calling page:
 *   - <body data-map-js="true">  (Leaflet loaded by loader.js)
 *   - {% block page_content_after %}<div id="mapRoot"></div>{% endblock %}
 *
 * Height is governed by the CSS class page-map-active (defined in map.css):
 *   #mapRoot.page-map-active { height: calc(100vh - var(--oc-navbar-height)) !important }
 *
 * The !important overrides any inline style.height that map.js's resize
 * listeners write, so the displayed height is always the CSS calc value —
 * unaffected by visualViewport drift, live-mode toggles, or scroll position.
 * map.js still calls invalidateSize() in those listeners, keeping Leaflet in
 * sync with the actual DOM dimensions.
 ***************************************************************************/

let _mod = null;

export async function initPageMap(cacheWPs) {
  if (typeof L === 'undefined' || !L.markerClusterGroup) {
    console.log('pageMap: Leaflet not loaded');
    return null;
  }

  const mapEl = document.getElementById('mapRoot');
  if (!mapEl) {
    console.log('pageMap: #mapRoot missing');
    return null;
  }

  // Set height before import so L.map() initialises with correct dimensions.
  // Apply the CSS class immediately so map.js's inline style.height writes
  // (from handleWPs and resize listeners) are always overridden by CSS !important.
  // handleWPs() then runs with the correct height already in effect — no
  // corrective invalidateSize() needed afterwards.
  mapEl.style.height = window.innerHeight + 'px';
  mapEl.classList.add('page-map-active');

  window.uniCacheWP = cacheWPs;

  try {
    if (!_mod) {
      _mod = await import('./map.js');
    } else {
      _mod.clearMap();
    }

    // Leaflet was initialised by loader.js at page-load when #mapRoot had no
    // CSS height (search page) — its internal _size is {y:0}.  Correct it now
    // before handleWPs() calls fitBounds(), otherwise the first live-mode
    // toggle triggers Leaflet's deferred invalidation and causes a view jump.
    _mod.getMyMap().invalidateSize({ pan: false });

    await _mod.handleWPs();

    // When live mode is disabled any residual Leaflet re-centre can shift the
    // scroll position slightly.  Snap back to the map so the user stays put.
    _mod.getMyMap().on('overlayremove', (e) => {
      if (e.name === 'Live Map') {
        mapEl.scrollIntoView({ behavior: 'instant' });
      }
    });

    return _mod;
  } catch (err) {
    console.log('pageMap: failed', err);
    return null;
  }
}
