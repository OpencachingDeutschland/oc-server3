/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 *
 * api.js — stub for map3
 * GCxM-specific API functions have no OC equivalent; stubbed out.
 ***************************************************************************/

/** GCxM Xfer list — not available in OC. */
export async function saveToXferList(_codes) {
  console.log('saveToXferList: not implemented in OCmap');
  return false;
}

/** GCxM backend fetch — not available in OC. */
export async function apiFetch(_endpoint, _options, _flags) {
  console.log('apiFetch: not implemented in OCmap');
  return { status: 501 };
}
