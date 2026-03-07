/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 ***************************************************************************/

// map3.js — OCmap live map
// OCmap live map — OC-only. All GC/AL/BroadcastChannel code removed.

import { getIcon, gpsIcon } from './mapIcons.js';
import { baseLayers, Esri_WorldBoundariesPlaces } from './mapLayers.js';
import { init as initMapCircles } from './mapCircles.js';
import * as mapSelect from './mapSelect.js';
import * as mapRouting from './mapRouting.js';

const mapRoot = L.map('mapRoot');

window.addEventListener('resize', () => {
  const el = document.getElementById('mapRoot');
  if (el) el.style.height = (window.innerHeight - 10) + 'px';
  mapRoot.invalidateSize();
});

let citiesShown = true;
let liveEnabled = false;

const getById = id => document.getElementById(id);
const dShow   = id => { const el = getById(id); if (el) el.style.display = 'block'; };
const dHide   = id => { const el = getById(id); if (el) el.style.display = 'none'; };

const flagNames = ['isOwned', 'isFound', 'isDNF', 'isDisabled'];

// Marker registries
const liveRegistry              = new Map();
const staticFoundRegistry       = new Map();
const staticUnfoundRegistry     = new Map();
const liveCirclesRegistry       = new Map();
const stageMarkerRegistry       = new Map();
const staticFoundCirclesRegistry   = new Map();
const staticUnfoundCirclesRegistry = new Map();

const liveMap = L.markerClusterGroup({
  zoomToBoundsOnClick     : true,
  spiderfyOnMaxZoom       : false,
  disableClusteringAtZoom : 10
});

const foundMarkers = L.markerClusterGroup({
  zoomToBoundsOnClick     : true,
  spiderfyOnMaxZoom       : false,
  disableClusteringAtZoom : 10
});

const notfoundMarkers = L.markerClusterGroup({
  zoomToBoundsOnClick     : true,
  spiderfyOnMaxZoom       : false,
  disableClusteringAtZoom : 10
});

foundMarkers.addTo(mapRoot);
notfoundMarkers.addTo(mapRoot);

//-------------------------
// Overlay layers

const overlayLayers = { "Live Map": liveMap };

let defaultMapLayer = localStorage.getItem("ocmap-defaultMapLayer");
if (!defaultMapLayer) defaultMapLayer = "OpenStreetMap Default";

baseLayers[defaultMapLayer].addTo(mapRoot);
export const stageMarkers  = L.layerGroup().addTo(mapRoot);
const stageMarkerCircles   = L.layerGroup().addTo(mapRoot);

//-------------------------
// Controls

L.control.scale().addTo(mapRoot);
const layerControl = L.control.layers(baseLayers, overlayLayers).addTo(mapRoot);

const ZoomLevelControl = L.Control.extend({
  onAdd: function(map) {
    const container = L.DomUtil.create('div', 'zoom-level-display');
    container.innerHTML = map.getZoom() ?? '-';
    container.title = 'Current zoom level';
    map.on('zoomend', () => { container.innerHTML = map.getZoom(); });
    return container;
  }
});
new ZoomLevelControl({ position: 'topleft' }).addTo(mapRoot);

//-------------------------
// Map events

mapRoot.on('click', () => {
  if (state.rmRoutingModeEnabled) return;
  stageMarkers.clearLayers();
  stageMarkerCircles.clearLayers();
  stageMarkerRegistry.clear();
});

const persistableMapLayers = [
  "OpenStreetMap Default",
  "OpenStreetMap German Style",
  "Opentopo Map",
  "Satellite view"
];

mapRoot.on('baselayerchange', (e) => {
  if (persistableMapLayers.includes(e.name)) {
    localStorage.setItem("ocmap-defaultMapLayer", e.name);
  }
  if (e.name === "Satellite view") {
    if (citiesShown) mapRoot.addLayer(Esri_WorldBoundariesPlaces);
    layerControl.addOverlay(Esri_WorldBoundariesPlaces, "Cities");
  } else {
    layerControl.removeLayer(Esri_WorldBoundariesPlaces);
    mapRoot.removeLayer(Esri_WorldBoundariesPlaces);
  }
});

mapRoot.on('overlayadd', (e) => {
  if (e.name === "Cities") { citiesShown = true; }
  if (e.name === "Live Map") {
    liveEnabled = true;
    layerControl.getContainer().classList.add('live-active');
    fetchAndShowLiveMarkers();
  }
});

mapRoot.on('overlayremove', (e) => {
  if (e.name === "Cities") { citiesShown = false; }
  if (e.name === "Live Map") {
    layerControl.getContainer().classList.remove('live-active');
    liveEnabled = false;
    stageMarkers.clearLayers();
  }
});

mapRoot.on('zoomend', () => { if (liveEnabled) fetchAndShowLiveMarkers(); });
mapRoot.on('dragend', () => { if (liveEnabled) fetchAndShowLiveMarkers(); });

//-------------------------
// fetchOCByBbox() — calls map3.php?mode=live backend

async function fetchOCByBbox(s, w, n, e) {
  const params = new URLSearchParams({
    mode: 'live', lat1: s, lat2: n, lon1: w, lon2: e
  });
  try {
    const res = await fetch('map3.php?' + params);
    if (!res.ok) return { count: 0, items: [] };
    const data = await res.json();
    return { count: data.count || 0, items: data.items || [] };
  } catch (err) {
    console.log('fetchOCByBbox error:', err);
    return { count: 0, items: [] };
  }
}

//-------------------------
// Toast for "too many caches"

let toastEl = null;

function showToast(msg) {
  if (!toastEl) {
    toastEl = document.createElement('div');
    toastEl.className = 'map-toast';
    document.getElementById('mapRoot').appendChild(toastEl);
  }
  toastEl.textContent = msg;
  toastEl.style.display = 'block';
}

function hideToast() {
  if (toastEl) toastEl.style.display = 'none';
}

//-------------------------
// fetchAndShowLiveMarkers()

const MAX_LIVE = 5000;

function fetchAndShowLiveMarkers() {
  const bounds = mapRoot.getBounds();
  const s = bounds.getSouthWest().lat.toFixed(4);
  const w = bounds.getSouthWest().lng.toFixed(4);
  const n = bounds.getNorthEast().lat.toFixed(4);
  const e = bounds.getNorthEast().lng.toFixed(4);

  fetchOCByBbox(s, w, n, e).then(({ count, items }) => {
    if (count > MAX_LIVE) {
      liveMap.clearLayers();
      liveRegistry.clear();
      liveCirclesRegistry.clear();
      showToast(`${count} caches in view \u2014 zoom in to display`);
      return;
    }

    hideToast();
    liveMap.clearLayers();

    items.forEach(p => {
      const lat = parseFloat(p.lat);
      const lon = parseFloat(p.lon);
      if (isNaN(lat) || isNaN(lon)) return;

      const ll = L.latLng(lat, lon).toString();
      const existing = liveRegistry.get(ll);
      if (existing?.options?.isSelected) p.isSelected = true;

      const m = createMarker(p);
      if (!m) return;
      liveRegistry.set(ll, m);
      liveCirclesRegistry.set(ll, createCircle(m));
    });
    refreshLiveMarkers();
  });
}

//-------------------------
// handleWPs() — called by page to init map

export async function handleWPs() {
  document.getElementById('mapRoot').style.height = (window.innerHeight - 10) + 'px';

  // uniCacheWP is a global provided by the page template (may be empty)
  if (!window.uniCacheWP?.length) {
    const lat = window.initLat ?? 51.5;
    const lon = window.initLon ?? 10.0;
    const zoom = window.initZoom ?? 6;
    mapRoot.setView([lat, lon], zoom);
    if (!mapRoot.hasLayer(liveMap)) {
      liveMap.addTo(mapRoot);
    } else {
      fetchAndShowLiveMarkers();
    }
  } else {
    await initStaticMarkers();
    await refreshStaticMarkers();
    const bounds = L.latLngBounds([]);
    bounds.extend(foundMarkers.getBounds());
    bounds.extend(notfoundMarkers.getBounds());
    if (bounds.isValid()) mapRoot.fitBounds(bounds);
  }

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(pos => {
      const { latitude, longitude } = pos.coords;
      L.marker([latitude, longitude], {
        draggable: false,
        icon: gpsIcon,
      }).bindTooltip("My position", { direction: "left" }).addTo(mapRoot);
    });
  }
}

//-------------------------
// initStaticMarkers()

async function initStaticMarkers() {
  staticFoundRegistry.clear();
  staticUnfoundRegistry.clear();
  staticFoundCirclesRegistry.clear();
  staticUnfoundCirclesRegistry.clear();

  mapRoot.removeLayer(foundMarkers);
  mapRoot.removeLayer(notfoundMarkers);
  foundMarkers.clearLayers();
  notfoundMarkers.clearLayers();

  for (const p of (window.uniCacheWP || [])) {
    const m = createMarker(p);
    if (!m) continue;
    const ll = m.getLatLng().toString();
    if (p.isFound) {
      staticFoundRegistry.set(ll, m);
      staticFoundCirclesRegistry.set(ll, createCircle(m));
    } else {
      staticUnfoundRegistry.set(ll, m);
      staticUnfoundCirclesRegistry.set(ll, createCircle(m));
    }
  }
}

//-------------------------
// refreshStaticMarkers()

async function refreshStaticMarkers() {
  mapRoot.removeLayer(foundMarkers);
  mapRoot.removeLayer(notfoundMarkers);
  foundMarkers.clearLayers();
  notfoundMarkers.clearLayers();

  const processMarker = (marker, layerGroup, circleRegistry) => {
    if (!state.smSelectionModeEnabled) {
      marker.off('click');
      marker.on('click', e => handleMarkerClick(e.target));
    }
    layerGroup.addLayer(marker);
    if (state.circlesEnabled) {
      const circle = circleRegistry.get(marker.getLatLng().toString());
      if (circle) layerGroup.addLayer(circle);
    }
  };

  for (const m of staticUnfoundRegistry.values()) processMarker(m, notfoundMarkers, staticUnfoundCirclesRegistry);
  for (const m of staticFoundRegistry.values())   processMarker(m, foundMarkers,    staticFoundCirclesRegistry);

  foundMarkers.addTo(mapRoot);
  notfoundMarkers.addTo(mapRoot);

  if (state.smSelectionModeEnabled && state.bindMarkersForSelection) {
    state.bindMarkersForSelection();
  }
}

//-------------------------
// refreshLiveMarkers()

export function refreshLiveMarkers() {
  liveMap.clearLayers();
  stageMarkerCircles.clearLayers();

  const processMarker = (marker, isStage = false) => {
    const type = marker.options?.type ?? 0;
    const isTypeEnabled = state.filter[type] || (state.filter[6] && [453, 3653, 3673, 7005].includes(type));
    let shouldShow = isTypeEnabled && flagNames.every(f => state.filter[f] || !marker.options?.[f]);

    if (isStage) shouldShow = true;
    if (!shouldShow) return;

    if (!state.smSelectionModeEnabled) {
      marker.off('click');
      marker.on('click', e => handleMarkerClick(e.target));
    }

    if (!isStage) {
      liveMap.addLayer(marker);
      if (state.circlesEnabled) {
        const circle = liveCirclesRegistry.get(marker.getLatLng().toString());
        if (circle) liveMap.addLayer(circle);
      }
    } else {
      if (state.circlesEnabled) stageMarkerCircles.addLayer(createCircle(marker));
    }
  };

  for (const m of liveRegistry.values())       processMarker(m, false);
  for (const m of stageMarkerRegistry.values()) processMarker(m, true);

  if (state.smSelectionModeEnabled && state.bindMarkersForSelection) {
    state.bindMarkersForSelection();
  }
}

//-------------------------
// handleMarkerClick()

async function handleMarkerClick(marker) {
  if (state.smSelectionModeEnabled) return;
  marker.openPopup();

  const { referenceCode } = marker.options;
  if (!referenceCode) return;

  try {
    const res = await fetch(`map3.php?mode=cache&wp=${referenceCode}`);
    if (!res.ok) return;
    const data = await res.json();
    if (!data.wpts?.length) return;

    const { lat, lng } = marker.getLatLng();
    data.wpts.forEach(w => {
      const icon = getIcon({ geocacheType: { id: w.typeId }, isOC: true, isGC: false });
      const child = L.marker([w.lat, w.lon], { icon });
      child.bindTooltip(w.name || "?", { direction: "left" });
      child.bindPopup(`<b>${w.name || "?"}</b><br>${w.description || ""}`);
      stageMarkers.addLayer(child);
      stageMarkerRegistry.set(L.latLng(w.lat, w.lon).toString(), child);
      L.polyline([[lat, lng], [w.lat, w.lon]], { color: "red", weight: 1 }).addTo(stageMarkers);
      if (state.circlesEnabled) {
        stageMarkerCircles.addLayer(createCircle(child));
      }
    });
  } catch (err) {
    console.log("handleMarkerClick fetch error:", err);
  }
}

//-------------------------
// createMarker()

export function createMarker(p) {
  const lat = parseFloat(p.lat);
  const lon = parseFloat(p.lon);
  if (isNaN(lat) || isNaN(lon)) {
    console.log("Skipping marker — bad coords", p);
    return null;
  }

  const shortName = p.shortName || (p.name ? (p.name.length > 25 ? p.name.substring(0, 25) + '\u2026' : p.name) : '?');
  const linkHtml  = `<a href="viewcache.php?wp=${p.referenceCode}" title="${p.name}">${p.referenceCode} ${shortName}</a>`;

  const ownerHTML = `<span class="owner-alias">${p.ownerAlias || "Unknown"}</span>`;
  const foundRow  = p.isFound ? `<tr><td>Found:</td><td>${p.foundDate || ''}</td></tr>` : "";
  const typeRow   = `${p.geocacheType?.name || "?"} / ${p.geocacheSize?.name || "?"} / ${p.difficulty} / ${p.terrain}`;
  const pcnLink   = p.hasPCN ? ` &ensp; <a href="#" title="${(p.pcnText || '').replace(/"/g, '&quot;')}" style="color:crimson;font-weight:bold" onclick="return false">PCN</a>` : '';

  let statsRow = '';
  if (p.findCount != null) {
    const fav = p.favoritePoints != null ? ` &ensp; Fav: ${p.favoritePoints}` : '';
    statsRow = `<tr><td>Finds:</td><td>${p.findCount}${fav}</td></tr>`;
  }

  const popupDiv = document.createElement('div');
  popupDiv.innerHTML = `
    ${linkHtml}
    <div>by: ${ownerHTML}</div>
    <table style="border-spacing:0;font-family:inherit">
      <tr><td>Published:</td><td>${p.publishedDate || ''}</td></tr>
      ${statsRow}
      ${foundRow}
    </table>
    <div style="margin-top:0.3em">${typeRow}${pcnLink}</div>
  `;

  const icon   = getIcon({ ...p, isOC: true, isGC: false });
  const marker = L.marker([lat, lon], {
    icon,
    isOC          : true,
    isGC          : false,
    referenceCode : p.referenceCode,
    type          : p.geocacheType?.id,
    isOwned       : p.isOwned,
    isFound       : p.isFound,
    isDNF         : p.isDNF || false,
    isDisabled    : p.isDisabled,
    isArchived    : p.isArchived,
    isSelected    : p.isSelected || false,
    name          : p.name,
    difficulty    : p.difficulty,
    terrain       : p.terrain,
    ownerAlias    : p.ownerAlias,
    geocacheType  : p.geocacheType,
    geocacheSize  : p.geocacheSize,
    publishedDate : p.publishedDate,
    favoritePoints: p.favoritePoints,
    findCount     : p.findCount,
  }).bindPopup(() => popupDiv)
    .bindTooltip(shortName, { direction: 'left' });

  return marker;
}

//-------------------------
// createCircle()

function createCircle(marker) {
  return L.circle(marker.getLatLng(), {
    radius      : state.circleRadius || 160,
    color       : 'red',
    fillColor   : '#f03',
    fillOpacity : 0.3,
    weight      : 1
  });
}

function updateCircleRadii() {
  const r = state.circleRadius || 160;
  for (const c of staticFoundCirclesRegistry.values())   if (c) c.setRadius(r);
  for (const c of staticUnfoundCirclesRegistry.values()) if (c) c.setRadius(r);
  for (const c of liveCirclesRegistry.values())          if (c) c.setRadius(r);
}



//-------------------------
//-------------------------
// Filter defaults (OC only — no platform toggle needed)

function defaultFilter() {
  return {
    2: true, 3: true, 4: true, 5: true, 6: true,
    7: true, 8: true, 10: true, 11: true, 12: true, 13: true, 137: true,
    isDisabled : true,
    isOwned    : true,
    isFound    : true,
    isDNF      : true,
  };
}

let filter = (() => {
  try {
    return JSON.parse(localStorage.getItem('ocmap-filter')) || defaultFilter();
  } catch { return defaultFilter(); }
})();

//-------------------------
// Global state (shared with sub-modules)

let state = {
  mapRoot,
  smSelectionModeEnabled : false,
  rmRoutingModeEnabled   : false,
  circlesEnabled         : false,
  filter,
  refreshStaticMarkers,
  refreshLiveMarkers,
  fetchAndShowLiveMarkers,
  handleMarkerClick,
  stageMarkers,
  liveRegistry,
  staticFoundRegistry,
  staticUnfoundRegistry,
  updateCircleRadii,
};

//-------------------------
// init()

function init() {
  initMapCircles(state);
  mapRouting.init(state);
  mapSelect.init(state);
}

init();

// vim: ts=2:sw=2:et:ft=javascript
