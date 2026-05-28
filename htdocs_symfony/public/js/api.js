/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 *
 * api.js — backend bindings for the verbatim gcxm map modules.
 *
 * map.js, mapTracks.js, mapSelect.js and gpx.js all import named functions
 * from './api.js'. This file provides OC-specific implementations or no-op
 * stubs so the gcxm sources can run unmodified.
 ***************************************************************************/

// ---------------------------------------------------------------
// City search → Nominatim via Symfony backend.

export async function findCity(q) {
  try {
    const res = await fetch('/api/geocode/city?q=' + encodeURIComponent(q));
    if (!res.ok) return [];
    return await res.json();
  } catch (err) {
    console.log('findCity:', err);
    return [];
  }
}

// ---------------------------------------------------------------
// Live viewport caches → /api/caches/live.
//
// gcxm signature: (s, w, n, e, skip, take, filter)
// OC backend ignores skip/take and is bounded server-side (5000 max).
// We honor the contract by returning [] for any skip > 0.

function calcShortName(name) {
  if (!name) return '';
  return name.length > 25 ? name.substring(0, 25) + '\u2026' : name;
}

export async function ocSearchByBbox(s, w, n, e, skip, _take, filter) {
  if (skip > 0) return [];

  const params = new URLSearchParams({
    lat1: s, lat2: n, lon1: w, lon2: e,
  });
  if (filter?.minDiff) params.set('minDiff', filter.minDiff);
  if (filter?.maxDiff) params.set('maxDiff', filter.maxDiff);

  try {
    const res = await fetch('/api/caches/live?' + params.toString());
    if (!res.ok) return [];
    const data = await res.json();
    const items = data.items || [];
    return items.map(p => ({
      ...p,
      isOC: true,
      isGC: false,
      shortName: calcShortName(p.name),
    }));
  } catch (err) {
    console.log('ocSearchByBbox:', err);
    return [];
  }
}

// ---------------------------------------------------------------
// Waypoints (additional WPs) for a given cache → /api/caches/waypoints.
//
// Backend returns { wpts: [{ lat, lon, name, description, typeId }] }
// Adapted to match what gcxm's handleMarkerClick expects (coordinates +
// typeId; map.js sets w.type = w.typeId itself).

export async function getCacheWPs(referenceCode) {
  if (!referenceCode) return null;
  try {
    const res = await fetch('/api/caches/waypoints?wp=' + encodeURIComponent(referenceCode));
    if (!res.ok) return null;
    const data = await res.json();
    const wpts = (data.wpts || []).map(w => ({
      ...w,
      typeId: w.typeId,
      coordinates: { latitude: w.lat, longitude: w.lon },
    }));
    return { wpts };
  } catch (err) {
    console.log('getCacheWPs:', err);
    return null;
  }
}

// ---------------------------------------------------------------
// Platform-specific stubs: GC and AL are not part of the OC port.

export async function gcSearchByBbox() { return []; }
export async function alSearchByBbox() { return []; }
export async function persistALsById() { return false; }
export async function getCachedAlStates() { return {}; }

// ---------------------------------------------------------------
// Field-note GPX tracks — not supported in OC.

export async function getTrackIds() { return []; }
export async function getTrackById() { return null; }

// ---------------------------------------------------------------
// PCN auxiliary fetch (gcxm legacy; map.js imports but doesn't call it).

export async function getPCN() { return null; }

// ---------------------------------------------------------------
// Xfer list (mapSelect) — not supported in OC.

export async function saveToXferList(_codes) { return false; }

// ---------------------------------------------------------------
// Generic backend fetch used by gpx.js. Not used in OC paths.

export async function apiFetch(_endpoint, _options, _flags) { return { status: 501 }; }
