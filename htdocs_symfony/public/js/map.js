// --------------------------------------------------------------
// map.js
//
// © 2025 hxdimpf Research
//
// Licensed under the MIT License.
//
// You may use, copy, modify, and distribute this software
// under the terms of the MIT License.
//
// https://opensource.org/licenses/MIT
// --------------------------------------------------------------

import { traceLog, showToast } from './helpers.js';
import { init as initMapTracks, renderTrackIds, resetTrackColors} from './mapTracks.js';
import * as mapFilter from './mapFilter.js';
import * as mapRouting from './mapRouting.js';
import * as mapSelect from './mapSelect.js';
import { cacheTypes, getIcon, getWaypointIcon, gpsIcon } from './mapIcons.js';
import { baseLayers, Esri_WorldBoundariesPlaces } from './mapLayers.js';
import { t } from './i18n.js';
import {
  getCacheWPs,
  getTrackIds,
  getTrackById,
  ocSearchByBbox,
  findCity,
} from './mapApi.js';

const mapRoot = L.map('mapRoot', { zoomControl: false });

//-------------------------
// ScrollTopControl — must be registered FIRST, before all other controls
//
// Add a custom 'topcenter' control corner (Leaflet only has topleft/topright/bottomleft/bottomright)
const topCenter = L.DomUtil.create('div', 'leaflet-top leaflet-center', mapRoot._controlContainer);
mapRoot._controlCorners['topcenter'] = topCenter;

const ScrollTopControl = L.Control.extend({
  onAdd: function() {
    const btn = L.DomUtil.create('div', 'map-escape-top');
    btn.innerHTML = '⬆️';
    btn.title = 'Scroll to top of page';
    btn.setAttribute('role', 'button');
    btn.setAttribute('aria-label', 'Scroll to top');
    L.DomEvent.disableClickPropagation(btn);
    L.DomEvent.on(btn, 'click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    return btn;
  }
});
if (document.body.dataset.page !== 'mapServer' && document.body.dataset.page !== 'livemap') {
  new ScrollTopControl({ position: 'topcenter' }).addTo(mapRoot);
}

// Initialise Bootstrap popovers inside Leaflet popups when they open.
// Bootstrap is loaded as a global script before this module runs.
mapRoot.on('popupopen', function(e) {
  e.popup.getElement()?.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
    bootstrap.Popover.getOrCreateInstance(el, { sanitize: false });
  });
});
mapRoot.on('popupclose', function(e) {
  e.popup.getElement()?.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
    bootstrap.Popover.getInstance(el)?.dispose();
  });
});

//-------------------------
// CitySearchControl — on all map pages; hidden until Live Map overlay is active
//
if (document.body.dataset.page !== 'mapServer') {
  const CitySearchControl = L.Control.extend({
    onAdd: function() {
      const container = L.DomUtil.create('div', 'city-search-control');
      container.style.display = 'none'; // shown only when Live Map overlay is active
      L.DomEvent.disableClickPropagation(container);
      L.DomEvent.disableScrollPropagation(container);

      const searchRow = L.DomUtil.create('div', 'city-search-row', container);

      const input = L.DomUtil.create('input', 'city-search-input', searchRow);
      input.type        = 'text';
      input.placeholder = t('City or village\u2026');
      input.maxLength   = 60;
      input.setAttribute('autocomplete', 'off');

      const btnSearch = L.DomUtil.create('button', 'map-btn', searchRow);
      btnSearch.innerHTML = '&#x1F50D;';
      btnSearch.title     = t('Search');
      btnSearch.type      = 'button';

      const btnHere = L.DomUtil.create('button', 'map-btn', searchRow);
      btnHere.innerHTML = '&#x1F4CD;';
      btnHere.title     = t('My location');
      btnHere.type      = 'button';

      const results = L.DomUtil.create('div', 'city-search-results', container);

      function panToAndFetch(lat, lon) {
        results.style.display = 'none';
        input.value = '';
        mapRoot.setView([parseFloat(lat), parseFloat(lon)], defaultZoom);
        // Update URL so a page reload restores this view
        const url = new URL(location);
        url.searchParams.set('lat', lat); url.searchParams.set('lon', lon);
        url.searchParams.set('zoom', mapRoot.getZoom());
        history.replaceState(null, '', url);
        if (!mapRoot.hasLayer(liveMap)) {
          liveMap.addTo(mapRoot);
        } else {
          fetchAndShowLiveMarkers();
        }
      }

      async function search() {
        const q = input.value.trim();
        if (!q) return;
        results.innerHTML     = `<div class="city-search-item city-search-status">${t('Searching\u2026')}</div>`;
        results.style.display = 'block';
        try {
          const data = await findCity(q);
          results.innerHTML = '';
          if (!data?.length) {
            results.innerHTML = `<div class="city-search-item city-search-status">${t('No results')}</div>`;
            return;
          }
          const items = data.slice(0, 8);
          items.forEach((item, i) => {
            const el = L.DomUtil.create('div', 'city-search-item', results);
            el.textContent = item.display_name;
            el.tabIndex   = 0;
            L.DomEvent.on(el, 'click',   () => panToAndFetch(item.lat, item.lon));
            L.DomEvent.on(el, 'keydown', e => {
              if (e.key === 'Enter')  { e.preventDefault(); panToAndFetch(item.lat, item.lon); }
              if (e.key === 'Escape') { results.style.display = 'none'; input.focus(); }
              if (e.key === 'Tab') {
                const atEdge = e.shiftKey ? i === 0 : i === items.length - 1;
                if (atEdge) { e.preventDefault(); results.style.display = 'none'; input.focus(); }
              }
            });
          });
        } catch {
          results.innerHTML = `<div class="city-search-item city-search-status">${t('Error searching')}</div>`;
        }
      }

      L.DomEvent.on(btnSearch, 'click', search);
      L.DomEvent.on(input, 'keydown', e => {
        if (e.key === 'Enter')  { e.preventDefault(); search(); }
        if (e.key === 'Escape') { results.style.display = 'none'; }
        if (e.key === 'Tab' && !e.shiftKey && results.style.display === 'block') {
          const first = results.querySelector('.city-search-item:not(.city-search-status)');
          if (first) { e.preventDefault(); first.focus(); }
        }
      });
      L.DomEvent.on(btnHere, 'click', () => {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(
          pos => panToAndFetch(pos.coords.latitude, pos.coords.longitude),
          ()  => {
            results.innerHTML     = `<div class="city-search-item city-search-status">${t('Location unavailable')}</div>`;
            results.style.display = 'block';
          }
        );
      });

      let initialFocusDone = false;
      mapRoot.on('overlayadd',    (e) => {
        if (e.name !== 'Live Map') return;
        container.style.display = '';
        if (!initialFocusDone && document.body.dataset.page === 'livemap') {
          initialFocusDone = true;
          setTimeout(() => input.focus(), 0);
        }
      });
      mapRoot.on('overlayremove', (e) => { if (e.name === 'Live Map') { container.style.display = 'none'; results.style.display = 'none'; } });

      return container;
    }
  });
  new CitySearchControl({ position: 'topleft' }).addTo(mapRoot);
}

L.control.zoom().addTo(mapRoot);

//-------------------------
// Map viewport height — use visualViewport on mobile for actual visible area
//
function getViewportHeight() {
  return window.visualViewport?.height || window.innerHeight;
}

window.addEventListener('resize', () => {
  const el = document.getElementById('mapRoot');
  if (el && el.style.height !== '0px') {
    el.style.height = getViewportHeight() + 'px';
    mapRoot.invalidateSize();
  }
});

if (window.visualViewport) {
  window.visualViewport.addEventListener('resize', () => {
    const el = document.getElementById('mapRoot');
    if (el && el.style.height !== '0px') {
      el.style.height = getViewportHeight() + 'px';
      mapRoot.invalidateSize();
    }
  });
}

export function getMyMap() {
  return mapRoot;
}

export function enableLiveMode() {
  if (!mapRoot.hasLayer(liveMap)) liveMap.addTo(mapRoot);
}

let citiesShown = true;
let liveEnabled = false;

//-------------------------
// some stupid helpers
//
const getById = id => document.getElementById(id);
const dShow = id => getById(id).style.display = 'block';
const dHide = id => getById(id).style.display = 'none';

const flagNames = ['isOwned', 'isFound', 'isDNF', 'isDisabled', 'hasCC', 'hasPCN'];
const cacheSizes = {
     0 : { name: ""        },
     1 : { name: "Unknown" },
     2 : { name: "Micro"   },
     3 : { name: "Regular" },
     4 : { name: "Large"   },
     5 : { name: "Virtual" },
     6 : { name: "Other"   },
     7 : { name: ""        },
     8 : { name: "Small"   },
     9 : { name: ""        },
};

const liveRegistry                 = new Map(); // cache all (live) markers here, only a subset may be put on the map based on filtering
const staticFoundRegistry          = new Map(); // static markers, loaded with the page
const staticUnfoundRegistry        = new Map(); // static markers, loaded with the page
const stageMarkerRegistry          = new Map();

const liveMap = L.markerClusterGroup({
  zoomToBoundsOnClick     : true,
  spiderfyOnMaxZoom       : false,
  disableClusteringAtZoom : 12
});

const foundMarkers = L.markerClusterGroup({
  zoomToBoundsOnClick     : true,
  spiderfyOnMaxZoom       : false,
  disableClusteringAtZoom : 10
});

const notfoundMarkers  = L.markerClusterGroup({
  zoomToBoundsOnClick     : true,
  spiderfyOnMaxZoom       : false,
  disableClusteringAtZoom : 10
});

foundMarkers.addTo(mapRoot);
notfoundMarkers.addTo(mapRoot);

// main map.js neede to toggle these off/on
//
export function hideFoundMarkers() {
  mapRoot.removeLayer(foundMarkers);
}

export function showFoundMarkers() {
  mapRoot.addLayer(foundMarkers);
}

// Update the icon on the single static marker (used on /explore after saving CC/PCN)
export function updateStaticMarkerIcon(referenceCode, updates) {
  for (const registry of [staticFoundRegistry, staticUnfoundRegistry]) {
    for (const marker of registry.values()) {
      if (marker.options.referenceCode === referenceCode) {
        Object.assign(marker.options, updates);
        marker.setIcon(getIcon(marker.options));
        return;
      }
    }
  }
}

export function removeStaticMarkers(referenceCodes) {
  const codes = new Set(referenceCodes);
  const pairs = [
    [staticFoundRegistry, foundMarkers],
    [staticUnfoundRegistry, notfoundMarkers],
  ];
  for (const [registry, clusterGroup] of pairs) {
    for (const [key, marker] of registry) {
      if (codes.has(marker.options.referenceCode)) {
        clusterGroup.removeLayer(marker);
        registry.delete(key);
      }
    }
  }
}

//-------------------------
// --- Overlay Layers
//
const overlayLayers = {
  "Live Map"  : liveMap
}

let defaultMapLayer = localStorage.getItem("defaultMapLayer");

if (!defaultMapLayer || !baseLayers[defaultMapLayer]) defaultMapLayer = "OpenStreetMap Default";

//-------------------------
// --- Base Layers
//
baseLayers[defaultMapLayer].addTo(mapRoot);
export const stageMarkers = L.layerGroup().addTo(mapRoot); // those markers will be cleared upon click on the map anywhere
const gpxTrackLayer       = L.layerGroup().addTo(mapRoot); // GPX tracks from field notes


//-------------------------
// --- controls
//
// we add a scale as well as layer controls

L.control.scale().addTo(mapRoot);
const layerControl = L.control.layers(baseLayers, overlayLayers).addTo(mapRoot);

// --- Zoom level display (positioned next to zoom control via CSS) ---
const ZoomLevelControl = L.Control.extend({
  onAdd: function(map) {
    const container = L.DomUtil.create('div', 'zoom-level-display');
    const zoom = map.getZoom();
    container.innerHTML = zoom ?? '-';
    container.title = 'Current zoom level';
    map.on('zoomend', () => { container.innerHTML = map.getZoom(); });
    return container;
  }
});
new ZoomLevelControl({ position: 'topleft' }).addTo(mapRoot);

//-------------------------
// --- Map events
//

// this one is supposed to scroll to the map but with a breakpoint set,
// the event never fired.
//mapRoot.on('load', () => {
//  if (location.hash) {
//    const el = document.querySelector(location.hash);
//    el?.scrollIntoView({ behavior: 'instant' });
//  }
//});

mapRoot.on('click', () => {
  if (state.rmRoutingModeEnabled) return;

  stageMarkers.clearLayers();          // clear markers
  stageMarkerRegistry.clear();         // clear registry as well

  setTimeout(() => {
    resetTrackColors();
  }, 100);
});

//-------------------------
// baselayerchange
//

// we only persist the user's selection for the following maps. This is to make
// sure we don't operate with Thunderforest maps all the time -> quota

const persistableMapLayers = [
  "OpenStreetMap Default",
  "OpenStreetMap German Style",
  "Opentopo Map",
  "Satellite view"
];

mapRoot.on('baselayerchange', (e) => {
  if (persistableMapLayers.includes(e.name)) localStorage.setItem("defaultMapLayer", e.name);
  if (e.name === "Satellite view") {
    if (citiesShown) {
      mapRoot.addLayer(Esri_WorldBoundariesPlaces);
		}
    layerControl.addOverlay(Esri_WorldBoundariesPlaces, "Cities");
  } else {
    layerControl.removeLayer(Esri_WorldBoundariesPlaces);
    mapRoot.removeLayer(Esri_WorldBoundariesPlaces);
  }
});

//-------------------------
// overlay add 
//

mapRoot.on('overlayadd', (e) => {
  if (e.name === "Cities") { citiesShown = true; }
  if (e.name === "Live Map") {
    liveEnabled = true;
    layerControl.getContainer().classList.add('live-active');
    dShow('filterButton');
    fetchAndShowLiveMarkers();
  }
});

//-------------------------
// overlay remove

mapRoot.on('overlayremove', (e) => {
  if (e.name === "Cities") { citiesShown = false; }
  if (e.name === "Live Map") {
    layerControl.getContainer().classList.remove('live-active');
    liveEnabled = false;
    dHide('filterButton');

    // Clear stage markers
    stageMarkers.clearLayers();
  }
});

//-------------------------
// zoom and drag — in live mode, auto-fetch viewport caches
// (replaces the old "Caches" refresh button which is hidden on OC)

mapRoot.on('zoomend', () => {
  if (liveEnabled) fetchAndShowLiveMarkers();
});

mapRoot.on('dragend', () => {
  if (liveEnabled) fetchAndShowLiveMarkers();
});

//-------------------------
// fetchAndShowLiveMarkers()
//
// called in the context of rendering a live map

function fetchAndShowLiveMarkers() {
  let mustClear = true;

  function handlePoints(points) {
    if (mustClear) {
      liveMap.clearLayers();
      mustClear = false;
    }
    points.forEach(p => {
      // Compute the key before creating the marker to check for existing selection state
      const lat = parseFloat(p.lat);
      const lon = parseFloat(p.lon);
      if (isNaN(lat) || isNaN(lon)) {
        console.warn("Skipping marker due to bad coords", p);
        return;
      }
      const ll = L.latLng(lat, lon).toString();
      const code = p.referenceCode;

      // Preserve isSelected state from any prior marker for this cache.
      // The prior marker may live under a stale key if coords changed (e.g. CC
      // edited in another tab), so fall back to a referenceCode lookup.
      let prior = liveRegistry.get(ll);
      let priorKey = ll;
      if (!prior && code) {
        for (const [k, mk] of liveRegistry) {
          if (mk.options.referenceCode === code) { prior = mk; priorKey = k; break; }
        }
      }
      if (prior?.options?.isSelected) p.isSelected = true;

      // Purge a stale entry for the same cache at a different key so we don't
      // end up with two markers for one referenceCode.
      if (code && priorKey !== ll) {
        liveRegistry.delete(priorKey);
      }

      const m = createMarker(p);
      if (!m) return;  // skip invalid point
      liveRegistry.set(ll, m);
    });

    refreshLiveMarkers();
  }

  const bounds = mapRoot.getBounds();
  const s = bounds.getSouthWest().lat.toFixed(3);
  const w = bounds.getSouthWest().lng.toFixed(3);
  const n = bounds.getNorthEast().lat.toFixed(3);
  const e = bounds.getNorthEast().lng.toFixed(3);

  const OC_BATCH_SIZE     = 500;
  const OC_TOTAL_OBJECTS  = 500;

  const fetchPoints = async (searchFunction, s, w, n, e, skip, take, batchSize, totalObjects) => {
    const points = await searchFunction(s, w, n, e, skip, take);
    handlePoints(points);

    if (points.length === batchSize && skip + batchSize < totalObjects) {
      const nextSkip = skip + batchSize;
      const nextTake = Math.min(batchSize, totalObjects - nextSkip);
      const additionalPoints = await fetchPoints(searchFunction, s, w, n, e, nextSkip, nextTake, batchSize, totalObjects);
      handlePoints(additionalPoints);
      return additionalPoints;
    }
    return points;
  };

  const ocPromise = fetchPoints(fetchOCSearchByBbox, s, w, n, e, 0, OC_BATCH_SIZE, OC_BATCH_SIZE, OC_TOTAL_OBJECTS);

  ocPromise.then(ocPoints => {
    console.debug("ocPoints length: ", ocPoints.length);
  });
};

//--------------------------------------------------------------------------------------
// handleWPs() - static waypoints
//
// Static Waypoints as opposed to live map waypoints are waypoints that are pre determined
// and don't have to be searched for by some sort of a live API.
// Static Waypoints come up:
// 1. in Tours -> Logging
// 2. in the cache explorer
// 3. in the ALC Player's "Search" Function
//
// Please note: handleWPs() must also be called in order to trigger the live map
// functionality. In the case of a live map only, we may not have anything in uniCacheWP.
//
// Note: handleWPs is in global namespace and operates on a global object named uniCacheWP
// uniCacheWP is provided by the backend via handlebars template expansion.

export async function handleWPs() {

  document.getElementById('mapRoot').style.height = getViewportHeight() + 'px';

  if (!uniCacheWP?.length) { // in livemap we don't have static markers, however we got lat, lng
    if (typeof lat !== 'undefined' && typeof lon !== 'undefined' && lat && lon) {
      mapRoot.setView([lat, lon], defaultZoom); // zoom level 13: at this height we go live
      if (!mapRoot.hasLayer(liveMap)) {
        liveMap.addTo(mapRoot);                 // Initial add triggers overlayadd event which fetches markers
      } else {
        fetchAndShowLiveMarkers();              // Subsequent city searches: fetch directly since layer already added
      }
    }
  } else {
    await initStaticMarkers();
    await refreshStaticMarkers();
    const bounds = L.latLngBounds([]);
    bounds.extend(foundMarkers.getBounds());
    bounds.extend(notfoundMarkers.getBounds());
    if (bounds.isValid()) mapRoot.fitBounds(bounds);
  }

  //----------------- My Position --------------------

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(position => {
      const { coords: { latitude, longitude }} = position;
      const marker = new L.marker([latitude, longitude], {
        draggable : false,
        autoPan   : true,
        icon      : gpsIcon,
      }).bindTooltip("Here I am", {direction: "left"}).addTo(mapRoot);
    });
  } else console.log("Geolocation not supported");

};

//------------------------------------------------------------------------
//
async function initStaticMarkers() {
  staticFoundRegistry.clear();
  staticUnfoundRegistry.clear();

  // Remove cluster groups from map, clear, then re-add after populating.
  // MarkerClusterGroup.clearLayers() alone can leave stale markers when
  // followed by individual addLayer() calls in the same frame.
  mapRoot.removeLayer(foundMarkers);
  mapRoot.removeLayer(notfoundMarkers);
  foundMarkers.clearLayers();
  notfoundMarkers.clearLayers();

  for (const p of uniCacheWP) {
    const m = createMarker(p);
    if (!m) continue;

    const ll = m.getLatLng().toString();

    const markerRegistry = p.isFound
      ? staticFoundRegistry
      : staticUnfoundRegistry;

    markerRegistry.set(ll, m);

    // Remove any live marker for the same cache so static and live don't duplicate.
    // The live map shows caches at listing coords; the static marker uses CC coords.
    const code = p.referenceCode;
    if (code) {
      for (const [liveKey, liveMarker] of liveRegistry) {
        if (liveMarker.options.referenceCode === code) {
          liveMap.removeLayer(liveMarker);
          liveRegistry.delete(liveKey);
          break;
        }
      }
    }
  }
  // --- handle GPX if present ---
  if (typeof fieldNote !== 'undefined' && fieldNote?.hasGPX) {
    await handleGPX(fieldNote.name);
  }
}

//------------------------------------------------------------------------
// handleGPX()
//

export async function handleGPX(gpx) {
  const data = await getTrackById(gpx);
  if (!data) return;

  // Clear any existing GPX track
  clearGPXTrack();

  let distance = 0;
  let duration = 0;

  new L.GPX(data.gpx, {
    gpx_options : { joinTrackSegments: false },
    async: true,
    markers: {
      startIcon: 'images/marker-start.png',
      endIcon:   'images/marker-end.png',
    }
  }).on('loaded', (e) => {
    let mygpx = e.target;
    distance  = mygpx.get_distance().toFixed(2);
    duration  = mygpx.get_duration_string(mygpx.get_total_time());

    L.Control.textbox = L.Control.extend({
      onAdd: (map) => {
        const text = L.DomUtil.create('div');
        text.id = "info_text";
        text.innerHTML  = "Distance: " + distance + "m ";
        text.innerHTML += "Duration: " + duration;
        return text;
      },
    });
    L.control.textbox = (opts) => { return new L.Control.textbox(opts);}
    L.control.textbox({ position: 'bottomleft' }).addTo(mapRoot);
    mapRoot.fitBounds(e.target.getBounds());
  }).addTo(gpxTrackLayer);
}

//------------------------------------------------------------------------
// clearGPXTrack()
//

export function clearGPXTrack() {
  gpxTrackLayer.clearLayers();
  // Remove info text control if present
  const infoText = document.getElementById('info_text');
  if (infoText) infoText.parentElement.remove();
}

/**
 * clearMap() - Clear all static markers and GPX tracks from the map.
 * Used by the Map Server Clear button.
 */
export function clearMap() {
  staticFoundRegistry.clear();
  staticUnfoundRegistry.clear();
  mapRoot.removeLayer(foundMarkers);
  mapRoot.removeLayer(notfoundMarkers);
  foundMarkers.clearLayers();
  notfoundMarkers.clearLayers();
  clearGPXTrack();
}

// --------------------------------------------
// refresh control
//
// OC build: Tracks/Fetch AL/Caches buttons are hidden — Tracks/Caches backend
// not yet ported; Fetch AL is GC-platform-only and will never exist here.
// Viewport caches are auto-fetched on dragend/zoomend instead of via Caches button.
// Mode/Clear remain (hidden by default, shown by mapServer.js when applicable).

const RefreshControl = L.Control.extend({
  onAdd: function (mapRoot) {
    const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');

    // Mode button - hidden by default, shown by mapServer.js
    const modeButton = L.DomUtil.create('button', 'btn btn-sm btn-dark', container);
    modeButton.id            = 'modeButton';
    modeButton.innerHTML     = 'Private';
    modeButton.title         = 'Toggle between Private (own data only) and Public (all broadcast data)';
    modeButton.style.display = 'none';
    modeButton.style.marginBottom = '5px';

    // Clear button - hidden by default, shown by mapServer.js
    const clearButton = L.DomUtil.create('button', 'btn btn-sm btn-dark', container);
    clearButton.id            = 'clearButton';
    clearButton.innerHTML     = 'Clear';
    clearButton.title         = 'Clear all markers and tracks from the map';
    clearButton.style.display = 'none';
    clearButton.style.marginBottom = '5px';

    container.style.display         = 'flex';
    container.style.flexDirection   = 'column';

    return container;
  }
});

const refreshControl = new RefreshControl({ position: 'topright' });
refreshControl.addTo(mapRoot);

// --------------------------------------------
// refreshStaticMarkers()
//
// This function operates on static markers provided by the backend
// upon page load. The markers are in the uniCacheWP array.
//
// A specific scenario is to render a static map based on a field note.
// In this case, field notes may come with a GPX track atteched to it.
// If there is one, we render it. The liveMap on the other hand is not
// associated with any GPX track.

async function refreshStaticMarkers() {
  // --- check whether to hide found caches ---
  const hideFinished = document.getElementById('hideFinished')?.checked ?? false;

  // --- Remove from map, clear, re-populate, re-add ---
  mapRoot.removeLayer(foundMarkers);
  mapRoot.removeLayer(notfoundMarkers);
  foundMarkers.clearLayers();
  notfoundMarkers.clearLayers();

  // --- helper to project a marker to a layer group ---
  const processMarker = (marker, layerGroup) => {
    // click handler - only rebind if not in selection mode
    if (!state.smSelectionModeEnabled) {
      marker.off('click');
      marker.on('click', e => handleMarkerClick(e.target, stageMarkers));
    }

    // add marker to layer
    layerGroup.addLayer(marker);
  };

  // --- project unfound markers ---
  for (const marker of staticUnfoundRegistry.values()) {
    processMarker(marker, notfoundMarkers);
  }

  // --- project found markers ---
  if (!hideFinished) {
    for (const marker of staticFoundRegistry.values()) {
      processMarker(marker, foundMarkers);
    }
  }

  // --- Re-add populated cluster groups to map ---
  foundMarkers.addTo(mapRoot);
  notfoundMarkers.addTo(mapRoot);

  // --- If selection mode is active, rebind selection handlers for new markers ---
  if (state.smSelectionModeEnabled && state.bindMarkersForSelection) {
    state.bindMarkersForSelection();
  }
}

// --------------------------------------------
// refreshLiveMarkers()
//
// liveMap ist populated based on what's on liverMarkerMap. 
//
// Some of the flags we can't include into the search, so what that means is that we
// may have retrieved objects from the platforms which we in fact decided we don't
// want to see. This is where "shouldShow" comes into play.
//
// Type 6, "Events", enables or disables all the various special events listed in [9, ... 7005]

export function refreshLiveMarkers() {
  const filter = state.filter;
  console.debug('refreshLiveMarkers: filter keys', Object.keys(filter), 'liveRegistry size', liveRegistry.size, 'filter sample', { '2': filter['2'], '5': filter['5'], isOwned: filter.isOwned });

  // Clear all layers
  liveMap.clearLayers();

  const processMarker = (marker, isStage = false) => {

    const type = marker.options?.type ?? "unknown";
    const isTypeEnabled = !!filter[type];
    let shouldShow = isTypeEnabled && flagNames.every(flag => filter[flag] || !marker.options?.[flag]);

    // Remove previous click handlers only if not in selection mode
    if (!state.smSelectionModeEnabled) {
      marker.off('click');
    }

    if (isStage) shouldShow = true;
    if (!shouldShow) return;

    if (!isStage) liveMap.addLayer(marker);

    // Rebind click only if not in selection mode
    if (!state.smSelectionModeEnabled) {
      marker.on('click', e => handleMarkerClick(e.target, stageMarkers));
    }
  };

  // --- Process live markers ---
  for (const marker of liveRegistry.values()) {
    processMarker(marker, false);
  }

  // --- Process stage markers ---
  for (const marker of stageMarkerRegistry.values()) {
    processMarker(marker, true);
  }

  // --- If selection mode is active, rebind selection handlers for new markers ---
  if (state.smSelectionModeEnabled && state.bindMarkersForSelection) {
    state.bindMarkersForSelection();
  }
}

// --------------------------------------------
//
async function handleMarkerClick(marker, stageMarkers) {
  if (state.smSelectionModeEnabled) return;
  marker.openPopup();
  const popupEl = marker.getPopup()?.getElement();
  if (!popupEl) return;

  const { referenceCode, hasPCN } = marker.options;
  if (!referenceCode) return; // Incomplete marker, nothing to fetch
  const gc = await getCacheWPs(referenceCode);
  if (!gc) return;

  if (hasPCN) {
    const tooltipContent = popupEl.querySelector('.pcn-tooltip');
    if (tooltipContent) tooltipContent.title = marker.options.pcn || '';
  }

  const { lat, lng } = marker.getLatLng();

  gc.wpts?.forEach(w => {
    if (!w.coordinates) return;

    const mylat = w.coordinates.latitude;
    const mylon = w.coordinates.longitude;
    const icon = getWaypointIcon(w.subtype);
    if (!icon) return;

    // Create the child marker
    const childMarker = L.marker([mylat, mylon], { icon });
    stageMarkers.addLayer(childMarker);

    // Add to registry keyed by lat,lng
    const key = L.latLng(mylat, mylon).toString();
    stageMarkerRegistry.set(key, childMarker);

    // Tooltip and popup
    const description = `${w.name}<br /><br />${w.description}`;
    childMarker.bindTooltip(w.name, { direction: "left" });
    childMarker.bindPopup(description);

    // Polyline from parent to child
    L.polyline([[lat, lng], [mylat, mylon]], { color: 'red', weight: 1 }).addTo(stageMarkers);
  });
}

// --------------------------------------------
// createMarker()
//
// create a Leaflet marker object based on a geocaching waypoint
// object

export function createMarker(p) {
  const lat = parseFloat(p.lat);
  const lon = parseFloat(p.lon);
  if (isNaN(lat) || isNaN(lon)) {
    console.warn("Skipping marker due to bad coords", p);
    return null;
  }

  const div = document.createElement("div");

  // Generate link based on platform prefix
  // When on the Map Server, add &inline=1 so the target page renders
  // its map locally instead of sending it back to the Map Server
  const inlineParam = document.body.dataset.page === 'mapServer' ? '&inline=1' : '';
  let linkHtml;
  if (p.referenceCode?.startsWith("GC")) {
    linkHtml = `<a href="explore?gc=${p.referenceCode}${inlineParam}" title="${p.name}">${p.referenceCode} ${p.shortName}</a>`;
  } else if (p.referenceCode?.startsWith("OC")) {
    linkHtml = `<a href="/cache/${p.referenceCode}" title="${p.name}">${p.referenceCode} ${p.shortName}</a>`;
  } else if (p.referenceCode?.startsWith("AL")) {
    const alId = p.referenceCode.slice(2);
    const alShort = 'AL' + alId.substring(0, 6);
    linkHtml = `<a href="alexplore?id=${alId}${inlineParam}" title="${p.name}">${alShort} ${p.shortName}</a>`;
  } else {
    // Fallback for legacy AL UUIDs without prefix
    linkHtml = `<a href="alexplore?id=${p.referenceCode}${inlineParam}">${p.shortName}</a>`;
  }
  div.innerHTML = linkHtml;


  const ownerHTML = `<span class="owner-alias" data-owner="${p.userId || ""}">${p.ownerCode || "Unknown"}</span>`;
  const foundRow  = p.isFound ? `<tr class="found-row"><td>${t('Found:')}</td><td class="found-date">${p.foundDate || ''}</td></tr>` : "";
  const typeRow   = `${p.geocacheType?.name || "?"} / ${p.geocacheSize?.name || "?"} / ${p.difficulty} / ${p.terrain}`;
  const pcnEsc    = (p.pcn || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  const pcnPop    = `<pre style="margin:0;font-size:0.78em;font-family:monospace;white-space:pre-wrap;word-break:break-word;max-width:260px">${pcnEsc}</pre>`;
  const pcn       = p.hasPCN
    ? `<span data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="right" ` +
        `data-bs-html="true" data-bs-content="${pcnPop.replace(/"/g, '&quot;')}" ` +
        `style="cursor:help;color:var(--oc-link-color);text-decoration:underline dotted;margin-left:6px">PCN</span>`
    : "";

  let statsRow = '';
  if (p.findCount != null) {
    const fav = p.favoritePoints != null ? ` &ensp; Fav: ${p.favoritePoints}` : '';
    statsRow = `<tr><td>${t('Finds:')}</td><td>${p.findCount}${fav}</td></tr>`;
  }

  const meta = document.createElement("div");
  meta.innerHTML = `
    <div>${t('by:')} ${ownerHTML}</div>
    <table style="border-spacing: 0; font-family: inherit">
      <tr><td>${t('Published:')}</td><td>${p.publishedDate}</td></tr>
      ${statsRow}
      ${foundRow}
    </table>
    <div style="margin-top: 0.3em">${typeRow}${pcn}</div>
  `;

  div.appendChild(meta);

  const icon = getIcon(p);

  const marker = L.marker([lat, lon], {
    icon,
    // --- Map display flags ---
    referenceCode : p.referenceCode,
    type        : p.geocacheType?.id,
    isOwned     : p.isOwned,
    isFound     : p.isFound,
    isDNF       : p.isDNF,
    isDisabled  : p.isDisabled,
    isArchived  : p.isArchived,
    isCached    : p.isCached,
    hasPCN      : p.hasPCN,
    hasCC       : p.hasCC,
    isGuessable : p.isGuessable,
    isPartial   : p.isPartial,
    isSelected  : p.isSelected,
    // --- GPX generation fields ---
    name            : p.name,
    difficulty      : p.difficulty,
    terrain         : p.terrain,
    status          : p.status,
    geocacheType    : p.geocacheType,
    geocacheSize    : p.geocacheSize,
    ownerCode       : p.ownerCode,
    publishedDate   : p.publishedDate,
    foundDate       : p.foundDate,
    pcn             : p.pcn,
    correctedCoordinates : p.correctedCoordinates,
    postedCoordinates : p.postedCoordinates,
    favoritePoints  : p.favoritePoints,
    findCount       : p.findCount,
  }).bindPopup(() => div)
    .bindTooltip(p.shortName, { direction: 'left' });

  return marker;
}

// ----------------------------------------------------------------------------------
// Prevent clicks on controls from propagating to the map

document.querySelectorAll('.leaflet-control').forEach(control => {
  control.addEventListener('click', (event) => {
    event.stopPropagation(); // Prevent the click event from bubbling to the map
  });
});


//-------------------------
// fetchOCSearchByBbox()
//
// Wraps the pure API call by adding filter

async function fetchOCSearchByBbox(s, w, n, e, skip, take) {
  const filter = state.filter;
  try {
    const markers = await ocSearchByBbox(s, w, n, e, skip, take, filter);
    console.debug('fetchOCSearchByBbox: got', markers.length, 'markers');
    return markers;
  } catch (error) {
    console.log('ocSearchByBbox(): ', error);
    return [];
  }
}

//-------------------------
// global state
//
let state = {                     // Owner
  mapRoot,                        // map.js
  rmRoutingModeEnabled   : false,
  smSelectionModeEnabled : false,
  refreshStaticMarkers,           // map.js
  refreshLiveMarkers,             // map.js
  fetchAndShowLiveMarkers,        // map.js
  handleMarkerClick,              // map.js
  stageMarkers,                   // map.js
  liveRegistry,                   // map.js
  staticFoundRegistry,            // map.js
  staticUnfoundRegistry,          // map.js
};

// ----------------------------------------------------------------------------------
// init()
//
function init() {
  mapFilter.init(state);
  mapRouting.init(state);
  mapSelect.init(state);
  initMapTracks(state);
}

init();

// code: language=javascript insertSpaces=true tabSize=2
// vim: ts=2:sw=2:et:ft=javascript
