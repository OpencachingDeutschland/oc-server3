// ---------------------------------------------------------------
// livemap.js — page module for /livemap.
//
// loader.js has already loaded Leaflet and side-effect-imported the verbatim
// gcxm map.js (which created the map and registered controls). We only
// need to call handleWPs() to enable live mode (window.uniCacheWP is
// empty, so map.js falls through to the live branch using window.lat /
// window.lon / window.defaultZoom).
// ---------------------------------------------------------------

import { handleWPs, getMyMap, enableLiveMode } from './map.js';

export async function init() {
    await handleWPs();
    if (window.lat && window.lon) {
        getMyMap().setView([window.lat, window.lon], window.defaultZoom || 13);
    }
    enableLiveMode();
}
