/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 *
 * mapApi.js — backend bindings for the map modules.
 ***************************************************************************/

import { apiFetch } from './helpers.js';

// ---------------------------------------------------------------
// City search → Nominatim via Symfony backend.

export async function findCity(q) {
  try {
    return await apiFetch('/api/geocode/city?q=' + encodeURIComponent(q));
  } catch (err) {
    console.log('findCity:', err);
    return [];
  }
}

// ---------------------------------------------------------------
// Live viewport caches → /api/caches/live.
//
// Signature: (s, w, n, e, skip, take, filter)
// Backend ignores skip/take and is bounded server-side (5000 max).
// We honor the contract by returning [] for any skip > 0.

function calcShortName(name) {
  if (!name) return '';
  return name.length > 25 ? name.substring(0, 25) + '…' : name;
}

export async function ocSearchByBbox(s, w, n, e, skip, _take, filter) {
  if (skip > 0) return [];

  const params = new URLSearchParams({
    lat1: s, lat2: n, lon1: w, lon2: e,
  });
  if (filter?.minDiff) params.set('minDiff', filter.minDiff);
  if (filter?.maxDiff) params.set('maxDiff', filter.maxDiff);

  try {
    const data = await apiFetch('/api/caches/live?' + params.toString());
    const items = data.items || [];
    return items.map(p => ({
      ...p,
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
// Backend returns { wpts: [{ lat, lon, name, description, subtype }] }.
// subtype is the OC `coordinates.subtype` (1-5) used to pick a waypoint icon.

export async function getCacheWPs(referenceCode) {
  if (!referenceCode) return null;
  try {
    const data = await apiFetch('/api/caches/waypoints?wp=' + encodeURIComponent(referenceCode));
    const wpts = (data.wpts || []).map(w => ({
      ...w,
      coordinates: { latitude: w.lat, longitude: w.lon },
    }));
    return { wpts };
  } catch (err) {
    console.log('getCacheWPs:', err);
    return null;
  }
}

// ---------------------------------------------------------------
// Field-note GPX tracks — stubbed pending reactivation.

export async function getTrackIds() { return []; }
export async function getTrackById() { return null; }
