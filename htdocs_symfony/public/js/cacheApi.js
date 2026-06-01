/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 *
 * cacheApi.js — backend bindings for cache-related pages (non-map).
 ***************************************************************************/

import { apiFetch } from './helpers.js';

export async function searchCaches({
  q = '', type = 0, minDiff = 1.0, maxDiff = 5.0, activeOnly = true, ocOnly = false,
  lat = null, lon = null, radius = 0,
} = {}) {
  const params = new URLSearchParams({ q, type, minDiff, maxDiff, activeOnly: activeOnly ? '1' : '0', ocOnly: ocOnly ? '1' : '0' });
  if (lat !== null && lon !== null && radius > 0) {
    params.set('lat', lat);
    params.set('lon', lon);
    params.set('radius', radius);
  }
  try {
    const data = await apiFetch('/api/caches/search?' + params);
    return data.items || [];
  } catch (err) {
    console.log('searchCaches:', err);
    return [];
  }
}
