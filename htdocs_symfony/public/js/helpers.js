// ---------------------------
// helpers.js
// Author:   hxdimpf Research
// ---------------------------

export const getById = id => document.getElementById(id);

// Visibility helpers
export const vShow = id => getById(id).style.visibility = 'visible';
export const vHide = id => getById(id).style.visibility = 'hidden';

// Display helpers
export const dShow = id => getById(id).style.display = 'block';
export const dHide = id => getById(id).style.display = 'none';

// padding
export function pad(n, width) {
  const s = n.toString();
  return s.length >= width ? s : '0'.repeat(width - s.length) + s;
}
//-------------------------------------------------------
// Utility function to create a JSON payload from a form

export function formToJson(form) {
  const formData = new FormData(form);
  const jsonPayload = {};

  for (const [key, value] of formData.entries()) {
    // Normalize the keys (remove '[]')
    const normalizedKey = key.replace(/\[\]$/, '');

    // Check if the key already exists in the jsonPayload
    if (jsonPayload[normalizedKey]) {
      // If it's already an array, push the new value
      if (Array.isArray(jsonPayload[normalizedKey])) {
        jsonPayload[normalizedKey].push(value);
      } else {
        // Convert to array
        jsonPayload[normalizedKey] = [jsonPayload[normalizedKey], value];
      }
    } else {
      // If not, just set the value
      jsonPayload[normalizedKey] = value; // note a single checked checkbox comes in like this, not an array
    }
  }

  return JSON.stringify(jsonPayload);
}

// ---------------------------------------
// show a toast message

export function showToast(message, duration = 2000) {
  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.innerHTML = message;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.classList.add('show');
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => {
        document.body.removeChild(toast);
      }, 300);
    }, duration);
  }, 100);
}

/**
 * traceLog()
 *
 * @param {*} start - start timestamp
 * @param {*} stop  - end timestamp
 * @param {int} take - number of objects processed by function (default 0)
 * @param {string} parm - optional parameters or context info (default empty)
 */

export function traceLog(start, stop, take = 0, parm = "") {
  let source = "unknown";
  try {
    const err = new Error();
    const stackLine = err.stack.split("\n")[2] || ""; // Attempt to get the caller location
    source = stackLine.match(/(?:at\s)(.*)/)?.[1] || "unknown"; // Extract the calling line information
  } catch {
    // If parsing fails, keep 'unknown' as the source
  }

  const elapsed = (stop - start) | 0;
  const now = new Date();
  const formattedTimestamp = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')} ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;

  const logMessage = `${formattedTimestamp} ${String(elapsed).padStart(7)} ms ${take.toString().padStart(6)} | ${source} | ${parm}`;
  console.info(logMessage);
}


//------------------------------------------------------------------------
// showError()
//
let errorTimer; // Global variable to track the active timer

export function showError(msg, targetId = 'errorMsg') {
  const err = document.getElementById(targetId);
  if (!err) return;

  // 1. Clear any existing timer so they don't overlap
  clearTimeout(errorTimer);

  // 2. Show the message
  err.textContent = msg;
  err.classList.remove('d-none');
  err.style.opacity = '1';

  // 3. Schedule the fade and hide
  errorTimer = setTimeout(() => {
    err.style.opacity = '0';

    errorTimer = setTimeout(() => {
      err.classList.add('d-none');
    }, 500); // Wait for the transition to finish
    
  }, 2500); // Show for 2.5 seconds
}

// -------------------------------------
// showFeedback()
// -------------------------------------

let feedbackTimer;

export function showFeedback(msg) {
  const info = document.getElementById('selection-info');
  if (!info) return;

  clearTimeout(feedbackTimer);
  const prev = info.textContent;
  info.textContent = msg;
  info.classList.add('oc-text-success');

  feedbackTimer = setTimeout(() => {
    info.classList.remove('oc-text-success');
    info.textContent = prev;
  }, 3000);
}

// -------------------------------------
// apiFetch — safe fetch wrapper for internal OC API calls
// -------------------------------------

/**
 * Fetches a URL, reads the body as text, attempts JSON parse.
 * Throws on non-2xx with an Error carrying .status and .body.
 * Returns parsed JSON (or {} for empty bodies) on success.
 */
export async function apiFetch(url, options = {}) {
  const res  = await fetch(url, { credentials: 'same-origin', ...options });
  const text = await res.text();
  const data = text.length
    ? (() => { try { return JSON.parse(text); } catch { return { _raw: text }; } })()
    : {};
  if (!res.ok)
    throw Object.assign(new Error(data?.error || `HTTP ${res.status}`), { status: res.status, body: data });
  return data;
}

// -------------------------------------
// File Download Helper
// -------------------------------------

/**
 * Downloads content as a file
 * @param {string} content - The file content
 * @param {string} filename - The download filename
 * @param {string} mimeType - The MIME type (e.g., 'application/gpx+xml', 'text/plain')
 */
export function downloadFile(content, filename, mimeType) {
  const blob = new Blob([content], { type: mimeType });
  const url = URL.createObjectURL(blob);

  const link = document.createElement('a');
  link.href = url;
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}

// -------------------------------------
// XML Helper
// -------------------------------------

/**
 * Escapes special characters for XML/GPX content
 * @param {string} str - The string to escape
 * @returns {string} The escaped string
 */
export function escapeXml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');
}

// code: language=javascript insertSpaces=true tabSize=2
// vim: ts=2:sw=2:et:ft=javascript
