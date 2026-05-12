/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 *
 * helpers.js — minimal stub for map3
 * Provides only the functions needed by mapSelect.js, mapRouting.js, gpx.js.
 ***************************************************************************/

/**
 * Show a brief toast notification at the bottom of the screen.
 */
export function showToast(message) {
  let toast = document.getElementById('ocmap-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'ocmap-toast';
    Object.assign(toast.style, {
      position: 'fixed', bottom: '30px', left: '50%', transform: 'translateX(-50%)',
      background: 'rgba(0,0,0,0.75)', color: '#fff', padding: '8px 16px',
      borderRadius: '4px', zIndex: '9999', fontSize: '14px', pointerEvents: 'none',
    });
    document.body.appendChild(toast);
  }
  toast.textContent = message;
  toast.style.display = 'block';
  clearTimeout(toast._hideTimer);
  toast._hideTimer = setTimeout(() => { toast.style.display = 'none'; }, 3000);
}

/**
 * Trigger a file download in the browser.
 */
export function downloadFile(content, filename, mimeType) {
  const blob = new Blob([content], { type: mimeType });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href     = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

/**
 * Escape special XML characters.
 */
export function escapeXml(str) {
  if (str == null) return '';
  return String(str)
    .replace(/&/g,  '&amp;')
    .replace(/</g,  '&lt;')
    .replace(/>/g,  '&gt;')
    .replace(/"/g,  '&quot;')
    .replace(/'/g,  '&apos;');
}
