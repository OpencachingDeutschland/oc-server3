/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 ***************************************************************************/

/**
 * Shared coordinate conversion utilities (ESM module).
 *
 * Used by coord_input.js (unified coordinate input) and map.
 */

/** Zero-pad an integer to the given width. */
export function pad(n, width) {
  const s = n.toString();
  return s.length >= width ? s : '0'.repeat(width - s.length) + s;
}

/** Format decimal lat/lon as "N49 01.012 E008 22.444". */
export function coords2Dm(lat, lon) {
  const latOut = lat < 0 ? 'S' : 'N';
  const lonOut = lon < 0 ? 'W' : 'E';
  lat = Math.abs(lat);
  lon = Math.abs(lon);

  let latDeg = Math.trunc(lat);
  let lonDeg = Math.trunc(lon);

  let latDegFrac = Number((lat - latDeg).toFixed(14));
  let lonDegFrac = Number((lon - lonDeg).toFixed(14));

  let latMin = 60 * latDegFrac;
  let lonMin = 60 * lonDegFrac;

  let latMinInt = Math.trunc(latMin);
  let lonMinInt = Math.trunc(lonMin);

  let latMinFrac = Number((latMin - latMinInt).toFixed(3));
  let lonMinFrac = Number((lonMin - lonMinInt).toFixed(3));

  if (latMinFrac === 1) {
    latMinFrac = 0;
    latMinInt++;
    if (latMinInt === 60) { latMinInt = 0; latDeg++; }
  }
  if (lonMinFrac === 1) {
    lonMinFrac = 0;
    lonMinInt++;
    if (lonMinInt === 60) { lonMinInt = 0; lonDeg++; }
  }

  return `${latOut}${pad(latDeg, 2)} ${pad(latMinInt, 2)}.${pad(1000 * latMinFrac, 3)} ` +
         `${lonOut}${pad(lonDeg, 3)} ${pad(lonMinInt, 2)}.${pad(1000 * lonMinFrac, 3)}`;
}

/** Parse a "N49 01.012 E008 22.444" string back to decimal {latitude, longitude}. */
export function coords2LatLon(coords) {
  const parts = coords.split(' ');

  const ns = parts[0][0];
  const ew = parts[2][0];

  const ndeg = parts[0].substr(1, parts[0].length - 1);
  const nmin = parts[1][0] + parts[1][1];

  const edeg = parts[2].substr(1, parts[2].length - 1);
  const emin = parts[3][0] + parts[3][1];

  const nfrac = parts[1][3] + parts[1][4] + parts[1][5];
  const efrac = parts[3][3] + parts[3][4] + parts[3][5];

  const latSign = (ns == 'N') ? 1 : -1;
  const lonSign = (ew == 'E') ? 1 : -1;

  const latitude  = ((Number(ndeg) + Number(nmin) / 60 + Number(nfrac) / 60000) * latSign).toFixed(14);
  const longitude = ((Number(edeg) + Number(emin) / 60 + Number(efrac) / 60000) * lonSign).toFixed(14);

  return { latitude, longitude };
}
