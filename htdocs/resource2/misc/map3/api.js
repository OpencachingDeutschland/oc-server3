/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 *
 * api.js — stub for map3
 * OC-specific stubs — no backend API available.
 ***************************************************************************/

/** Xfer list — not available in OC. */
export async function saveToXferList(_codes) {
  console.log('saveToXferList: not implemented in OCmap');
  return false;
}

/** Backend fetch — not available in OC. */
export async function apiFetch(_endpoint, _options, _flags) {
  console.log('apiFetch: not implemented in OCmap');
  return { status: 501 };
}
