/***************************************************************************
 * cache.js — OC cache detail view
 *
 * OC-only paths. Cooks raw OKAPI-shaped data via uniCache.js
 * (ocToUniCacheWP) into a uniCacheWP for rendering.
 *
 * Page lifecycle:
 *   1. loader.js sees <body data-page="cache"> → imports this module → calls init().
 *   2. init() reads wp code from #explore-container[data-code], fetches
 *      /api/cache/{wp} which returns a fully cooked uniCacheWP object.
 *   3. The object is used directly — no client-side cooking needed.
 *   4. renderCache() populates the static HTML table.
 *   5. Logs are fed into a Tabulator; the most-recent own log (if any)
 *      pre-populates the edit textarea.
 *
 * Log edit morph logic (do NOT alter without care):
 *   - If user has an own log: textarea pre-populated, mode='editLog',
 *     deleteLogBtn visible, "New Log" checkbox visible (unchecked).
 *   - Typing one character in textarea reveals the controls bar.
 *   - Checking "New Log" morphs the dialog → mode='newLog' (fresh date,
 *     default type, no delete button). Unchecking reverts.
 *   - If user has no own log: directly in mode='newLog'; checkbox hidden.
 ***************************************************************************/

import { TabulatorFull as Tabulator } from '/vendor/tabulator/tabulator_esm.min.js';
import { coords2Dm, coords2LatLon } from './coords.js';
import { initPageMap } from './pageMap.js';
import { apiFetch } from './helpers.js';
import { t } from './i18n.js';

// -----------------------------------------------------------------
// OC log type metadata (matches okapiLogTypeNames in CachesController)

const OC_LOG_TYPE_NAMES = {
  1:  t('Found it'),
  2:  t("Didn't find it"),
  3:  t('Comment'),
  7:  t('Attended'),
  8:  t('Will attend'),
  9:  t('Archived'),
  10: t('Ready to search'),
  11: t('Temporarily unavailable'),
};

const OC_LOG_COLORS = {
  1:  '#c4e6c3',
  2:  '#f4c0c0',
  3:  '#d0e4f8',
  7:  '#c4e6c3',
  8:  '#e4d4f8',
  9:  '#d8d8d8',
  10: '#c4e6c3',
  11: '#ffe4b5',
};

const ICON_SIZE = 30;

// -----------------------------------------------------------------
// Module state

let gc = null;          // uniCacheWP (fully cooked by backend)
let context = null;     // { userId, userName, isOwner } (from gc._context)
let logs = [];          // gc.logs

let oldCoords = '';
let newCoords = '';
let oldLogPasswd = '';
let newLogPasswd = '';
let oldPCN = '';
let newPCN = '';
let oldLog = '';
let newLog = '';

let coordsChanged    = false;
let logPasswdChanged = false;
let pcnChanged       = false;
let editLogChanged   = false;

let logsTable;
let wptTable;
let editModeInitialized = false;
let newModeInitialized  = false;
let mode;   // 'editLog' | 'newLog'

// Map functions cached after initMap() resolves
let mapHandleWPs         = null;
let mapUpdateMarkerIcon  = null;

// DOM refs (populated by initEventHandlers)
let editCoords, editLogPasswd, editPCN, editLog, editLogMsg;
let editLogHeaderText, deleteLogBtn, editLogText, editLogDate;
let editLogType, editLogSave, editLogControls;
let editLogCheckbox, editLogCheckboxWrapper;

// -----------------------------------------------------------------
// Small helpers

function getById(id) { return document.getElementById(id); }
function setText(id, text) { const el = getById(id); if (el) el.textContent = text; }

function adjustHeight(el) {
  if (!el) return;
  el.style.height = '1px';
  const lh = parseInt(window.getComputedStyle(el).lineHeight, 10) || 20;
  el.style.height = Math.max(el.scrollHeight, lh) + 'px';
}

function flashSaved(el) {
  const msg = document.createElement('span');
  msg.textContent = t('Saved');
  msg.style.cssText = 'color:var(--oc-success);font-size:0.85em;margin-left:6px;transition:opacity 0.5s;';
  el.parentNode.insertBefore(msg, el.nextSibling);
  setTimeout(() => { msg.style.opacity = '0'; }, 1500);
  setTimeout(() => { msg.remove(); }, 2000);
}

function localDatetime(timezoneId) {
  const date = new Date();
  const options = {
    timeZone: timezoneId || 'Europe/Berlin',
    year: 'numeric', month: '2-digit', day: '2-digit',
    hour: '2-digit', minute: '2-digit', second: '2-digit',
    hourCycle: 'h23',
  };
  return new Intl.DateTimeFormat('sv-SE', options).format(date).replace(' ', 'T');
}

function getMyNewestLog(arr) {
  return arr
    .filter(l => l.itsMine)
    .reduce((best, l) => !best || l.date > best.date ? l : best, null);
}

function showError(msg) {
  const el = getById('cache-error');
  if (el) {
    el.textContent = msg;
    el.style.display = 'block';
  }
}

function hideSkeletons() {
  for (const id of ['cache-skeleton', 'description-skeleton']) {
    const el = getById(id); if (el) el.style.display = 'none';
  }
  const table = getById('cache-table');
  const desc  = getById('cache-description');
  if (table) table.style.display = '';
  if (desc)  desc.style.display  = '';
}
// -----------------------------------------------------------------
// loadCache()

async function loadCache(code) {
  let response;
  try {
    response = await apiFetch(`/api/cache/${code}`);
  } catch (err) {
    showError(`Failed to load cache: ${err.message}`);
    for (const id of ['cache-skeleton', 'description-skeleton', 'logsLoading']) {
      const el = getById(id); if (el) el.style.display = 'none';
    }
    return;
  }

  if (response.error) {
    showError(response.error);
    return;
  }

  // Response is already a cooked uniCacheWP — use it directly.
  gc      = response;
  context = gc._context || {};
  logs    = gc.logs || [];

  // Owner sees the cache's actual log password (already handled by backend).

  // Preserve originals for revert when user clears CC
  gc._origLat = gc.postedCoordinates?.latitude  ?? gc.lat;
  gc._origLon = gc.postedCoordinates?.longitude ?? gc.lon;

  renderCache();
  hideSkeletons();
  initEventHandlers();

  createLogsTable();
  if (context.userId) {
    handleLogTextarea();
  } else {
    // Anonymous: hide editor entirely
    const el = getById('editLog');
    if (el) el.style.display = 'none';
  }

  initMap();
}

// -----------------------------------------------------------------
// renderCache()

function renderCache() {
  // Persist a minimal state object so icon regeneration works after edits
  const gcState = {
    typeId:     gc.geocacheType?.id,
    isOwned:    !!gc.isOwned,
    isFound:    !!gc.isFound,
    isDNF:      !!gc.isDNF,
    isArchived: !!gc.isArchived,
    isDisabled: !!gc.isDisabled,
    hasPCN:     !!gc.hasPCN,
    hasCC:      !!gc.hasCC,
    isOcOnly:   !!gc.isOcOnly,
  };
  const gcStateEl = getById('gcState');
  if (gcStateEl) gcStateEl.textContent = JSON.stringify(gcState);

  // Cache icon — generated by mapIcons (dynamic import keeps initial load lean)
  import('./mapIcons.js').then(({ generateIconUrl }) => {
    const iconUrl = generateIconUrl(gcState.typeId, gcState, ICON_SIZE);
    const iconEl  = getById('cacheIcon');
    if (iconEl) {
      iconEl.innerHTML = `<img src="${iconUrl}" style="vertical-align:middle;margin-right:5px">`;
    }
  }).catch(() => { /* fallback: no icon */ });

  // Name + reference links
  const refUrl = `https://www.opencaching.de/viewcache.php?wp=${gc.referenceCode}`;
  const nameLink = getById('cacheNameLink');
  if (nameLink) { nameLink.href = refUrl; nameLink.textContent = gc.name; }
  const refLink = getById('cacheRefLink');
  if (refLink) {
    refLink.href = refUrl;
    refLink.textContent = gc.referenceCode;
    refLink.target = '_blank';
  }

  // Edit button (owner only)
  const editBtn = getById('editCacheBtn');
  const editLink = getById('editCacheLink');
  if (editBtn && editLink && context.isOwner) {
    editBtn.style.display = '';
    editLink.href = `/cache/new?edit=${gc.referenceCode}`;
  }

  // Status badge
  const statusEl = getById('cacheStatus');
  if (statusEl) {
    if (gc.isArchived) {
      statusEl.innerHTML = `<span class="badge bg-danger">${t('Archived')}</span>`;
    } else if (gc.isDisabled) {
      statusEl.innerHTML = `<span class="badge bg-warning">${t('Disabled')}</span>`;
    } else {
      statusEl.innerHTML = `<span class="badge bg-success">${t('Active')}</span>`;
    }
  }

  // Location
  setText('cacheCountry', gc.location?.country || '');
  setText('cacheState',   gc.location?.state   || '');

  // Coordinates
  setText('cachePostedCoords', gc.postedCoordsFmt);

  const editCoordsEl = getById('editCoords');
  if (editCoordsEl) {
    editCoordsEl.value = gc.correctedCoordsFmt;
    oldCoords = editCoordsEl.value;
  }

  // Log password row (for OC caches requiring password — only show to logged-in users)
  const logPasswdRow = getById('logPasswdRow');
  if (gc.requiresPasswd && context.userId) {
    if (logPasswdRow) logPasswdRow.style.display = '';
    const el = getById('editLogPasswd');
    if (el) {
      el.value = gc.logpw || '';
      oldLogPasswd = el.value;
    }
  } else {
    if (logPasswdRow) logPasswdRow.style.display = 'none';
  }

  // Type, D/T (with size), find count, favorites, dates
  setText('cacheType', gc.geocacheType?.name || '');
  const sizeBit = gc.geocacheSize?.name ? ` (Size ${gc.geocacheSize.name})` : '';
  setText('cacheDT', `${gc.difficulty || 0} / ${gc.terrain || 0}${sizeBit}`);

  setText('cacheFindCount', (gc.findCount || 0).toLocaleString());

  const favEl = getById('cacheFavorites');
  if (favEl) {
    const heart = gc.isFavorited ? ' \u{1F499}' : '';
    favEl.textContent = (gc.favoritePoints || 0).toLocaleString() + heart;
  }

  setText('cachePlacedDate', gc.placedDateFmt);

  const timeRow = getById('cacheTimeRow');
  if (timeRow) {
    if (gc.searchTime > 0) {
      const h = Math.floor(gc.searchTime);
      const m = Math.round((gc.searchTime - h) * 60);
      setText('cacheTime', `${h}:${m.toString().padStart(2, '0')}`);
      timeRow.style.display = '';
    } else {
      timeRow.style.display = 'none';
    }
  }

  setText('cachePublishedDate', gc.publishedDateFmt);

  // Found date row
  if (gc.foundDateFmt) {
    const foundRow = getById('foundRow');
    if (foundRow) foundRow.style.display = '';
    setText('cacheFoundDate', gc.foundDateFmt);
  } else if (gc.dnfDateFmt) {
    const foundRow   = getById('foundRow');
    const foundLabel = getById('foundLabel');
    if (foundRow)   foundRow.style.display = '';
    if (foundLabel) foundLabel.textContent = t('DNF Date');
    setText('cacheFoundDate', gc.dnfDateFmt);
  }

  // GC cross-link row
  if (gc.wpGc) {
    const row = getById('gcCodeRow');
    if (row) row.style.display = '';
    const cell = getById('cacheGcCode');
    if (cell) cell.innerHTML = `<a href="https://coord.info/${gc.wpGc}" target="_blank">${gc.wpGc}</a>`;
  }

  if (gc.needsMaintenance) {
    const r = getById('maintenanceRow'); if (r) r.style.display = '';
  }
  if (gc.listingOutdated) {
    const r = getById('outdatedRow'); if (r) r.style.display = '';
  }

  // Owner
  const ownerCell = getById('cacheOwner');
  if (ownerCell && gc.owner.username) {
    ownerCell.innerHTML = `<a href="${gc.owner.profileUrl}">${gc.owner.username}</a>`;
  }
  const ownerStats = getById('cacheOwnerStats');
  if (ownerStats) {
    const joined = gc.owner.joinedDateFmt || '';
    const finds  = (gc.owner.findCount || 0).toLocaleString();
    const hides  = (gc.owner.hideCount || 0).toLocaleString();
    ownerStats.innerHTML = `${t('Joined')}: ${joined}<br>${t('Finds')}: ${finds} | ${t('Hides')}: ${hides}`;
  }

  // Hint
  if (gc.hints) {
    const row = getById('hintRow');
    const cell = getById('cacheHint');
    if (row) row.style.display = '';
    if (cell) cell.innerHTML = `<b>${t('Hint')}:</b> ${gc.hints}`;
  }

  // Description
  const descEl = getById('cache-description');
  if (descEl) {
    descEl.innerHTML = gc.sanitizedDescription || '';
    descEl.classList.toggle('cache-description-light-island', !!gc.descDarkUnsafe);
  }

  renderAttributes();
  createWPTable();

  // PCN
  const editPCNEl = getById('editPCN');
  if (editPCNEl) {
    editPCNEl.value = gc.pcn || '';
    oldPCN = editPCNEl.value;
    adjustHeight(editPCNEl);
  }
}

// -----------------------------------------------------------------
// renderAttributes()

function renderAttributes() {
  const container = getById('attributes');
  if (!container || !gc.attributes?.length) return;

  gc.attributes.forEach(a => {
    if (!a.imageUrl) return;
    const col = document.createElement('div');
    col.className = 'col-1 mb-3';
    col.innerHTML = `<img src="${a.imageUrl}" class="attribute-container" title="${a.name || ''}" alt="${a.name || ''}">`;
    container.appendChild(col);
  });
}

// -----------------------------------------------------------------
// createWPTable()

function createWPTable() {
  if (!gc.additionalWaypoints?.length) return;

  // OC subtype (1-5) → PNG filename (from legacy OC)
  const subtypeToPng = {
    1: 'wp_parking.png', 2: 'wp_reference.png',
    3: 'wp_path.png', 4: 'wp_final.png', 5: 'wp_poi.png',
  };

  const wps = gc.additionalWaypoints.map(wp => {
    const [lat, lon] = (wp.location || '').split('|');
    const png = subtypeToPng[wp.typeId] ? `/images/waypoints/${subtypeToPng[wp.typeId]}` : '';
    return {
      myCoords: lat && lon ? coords2Dm(Number(lat), Number(lon)) : '',
      prefix:   wp.type?.substring(0, 2)?.toUpperCase() || '',
      name:     wp.name || wp.type || '',
      typeName: wp.type_name || wp.type || '',
      description: wp.description || '',
      icon:     png,
    };
  });

  getById('additionalWP')?.classList.remove('d-none');

  wptTable = new Tabulator('#wptTable', {
    headerVisible: true,
    borders: false,
    layout: 'fitColumns',
    selectableRows: false,
    data: wps,
    rowFormatter: row => {
      row.getElement().classList.add('row-transparent');
      row.getElement().style.display = 'flex';
      row.getElement().style.alignItems = 'center';
    },
    columnDefaults: { resizable: false },
    columns: [
      { title: '',            field: 'icon',        headerSort: false, width: 28,
        formatter: cell => cell.getValue() ? `<img src="${cell.getValue()}" width="20" height="20" style="vertical-align:middle">` : '' },
      { title: t('Coordinates'), field: 'myCoords',    headerSort: false, width: 170 },
      { title: t('Prefix'),      field: 'prefix',      headerSort: true,  width: 60 },
      { title: t('Name'),        field: 'name',        headerSort: false, width: 190 },
      { title: t('Type'),        field: 'typeName',    headerSort: false, width: 140 },
      { title: t('Note'),        field: 'description', headerSort: false, tooltip: true,
        formatter: cell => `<div style="white-space:normal">${cell.getValue() || ''}</div>` },
    ],
  });
}

// -----------------------------------------------------------------
// initEventHandlers()

function initEventHandlers() {
  editCoords             = getById('editCoords');
  editLogPasswd          = getById('editLogPasswd');
  editPCN                = getById('editPCN');
  editLog                = getById('editLog');
  editLogMsg             = getById('editLogMsg');
  editLogHeaderText      = getById('editLogHeaderText');
  deleteLogBtn           = getById('deleteLogBtn');
  editLogText            = getById('editLogText');
  editLogDate            = getById('editLogDate');
  editLogType            = getById('editLogType');
  editLogSave            = getById('editLogSave');
  editLogControls        = getById('editLogControls');
  editLogCheckbox        = getById('editLogCheckbox');
  editLogCheckboxWrapper = getById('editLogCheckboxWrapper');

  // CC input — only available if logged in
  if (editCoords && context.userId) {
    editCoords.addEventListener('input', handleCoordsInput);
    editCoords.addEventListener('blur',  handleSaveCoords);
  } else if (editCoords) {
    editCoords.disabled = true;
  }

  // Log password
  if (editLogPasswd && context.userId) {
    editLogPasswd.addEventListener('input', handleLogPasswdInput);
    editLogPasswd.addEventListener('blur',  handleSaveLogPasswd);
  }

  // PCN
  if (editPCN && context.userId) {
    editPCN.addEventListener('input', handlePCNInput);
    editPCN.addEventListener('blur',  handleSavePCN);
  } else if (editPCN) {
    editPCN.disabled = true;
  }
}

// -----------------------------------------------------------------
// CC handlers

function handleCoordsInput() {
  newCoords = editCoords.value;
  const Regex = /^([NS])(\d{2})\s([0-5]\d\.\d{3})\s([EW])(\d{1,3})\s([0-5]\d\.\d{3})$/;
  if (!newCoords || Regex.test(newCoords)) {
    editCoords.style.color = 'green';
    coordsChanged = newCoords !== oldCoords;
  } else {
    editCoords.style.color = 'red';
    coordsChanged = false;
  }
}

async function handleSaveCoords() {
  if (!coordsChanged) return;

  const raw    = newCoords.trim();
  const parsed = raw === '' ? { latitude: 0, longitude: 0 } : coords2LatLon(raw);
  const lat    = parseFloat(parsed.latitude);
  const lon    = parseFloat(parsed.longitude);

  try {
    await apiFetch(`/api/cache/${gc.referenceCode}/coords`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ lat, lon }),
    });

    gc.hasCC = raw !== '';
    gc.lat = gc.hasCC ? lat : gc._origLat;
    gc.lon = gc.hasCC ? lon : gc._origLon;

    oldCoords = newCoords;
    coordsChanged = false;
    editCoords.style.color = 'var(--oc-text-primary)';
    flashSaved(editCoords);

    // Update map marker position and icon
    console.log('CC save: mapHandleWPs=', mapHandleWPs, 'mapUpdateMarkerIcon=', mapUpdateMarkerIcon, 'gc.lat=', gc.lat, 'gc.lon=', gc.lon, 'hasCC=', gc.hasCC);
    if (mapHandleWPs) {
      window.uniCacheWP = [gc];
      window.lat = gc.lat;
      window.lon = gc.lon;
      try { await mapHandleWPs(); } catch(e) { console.log('mapHandleWPs threw:', e); }
    }
    if (mapUpdateMarkerIcon) {
      try { mapUpdateMarkerIcon(gc.referenceCode, { hasCC: gc.hasCC, hasPCN: gc.hasPCN }); } catch(e) { console.log('mapUpdateMarkerIcon threw:', e); }
    }

    // Refresh title icon
    refreshCacheIcon();
  } catch (err) {
    console.log('saveCoords:', err.message);
  }
}

// -----------------------------------------------------------------
// Log password handlers

function handleLogPasswdInput() {
  newLogPasswd = editLogPasswd.value;
  if (newLogPasswd !== oldLogPasswd) {
    logPasswdChanged = true;
    editLogPasswd.style.color = 'green';
  } else {
    logPasswdChanged = false;
    editLogPasswd.style.color = 'var(--oc-text-primary)';
  }
}

async function handleSaveLogPasswd() {
  if (!logPasswdChanged) return;
  try {
    await apiFetch(`/api/cache/${gc.referenceCode}/logpw`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ logpw: newLogPasswd.trim() }),
    });
    gc.logpw = newLogPasswd.trim();
    oldLogPasswd = newLogPasswd;
    logPasswdChanged = false;
    flashSaved(editLogPasswd);
    editLogPasswd.style.color = 'var(--oc-text-primary)';
  } catch (err) {
    console.log('saveLogPasswd:', err.message);
    editLogPasswd.style.color = 'red';
  }
}

// -----------------------------------------------------------------
// PCN handlers

function handlePCNInput() {
  newPCN = editPCN.value;
  adjustHeight(editPCN);
  pcnChanged = newPCN !== oldPCN;
}

async function handleSavePCN() {
  if (!pcnChanged) return;

  try {
    await apiFetch(`/api/cache/${gc.referenceCode}/note`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ text: newPCN.trim() }),
    });

    gc.hasPCN = newPCN.trim().length > 0;
    oldPCN = newPCN;
    pcnChanged = false;
    flashSaved(editPCN);
    if (mapUpdateMarkerIcon) mapUpdateMarkerIcon(gc.referenceCode, { hasCC: gc.hasCC, hasPCN: gc.hasPCN });
    refreshCacheIcon();
  } catch (err) {
    console.log('savePCN:', err.message);
  }
}

// -----------------------------------------------------------------
// refreshCacheIcon()

async function refreshCacheIcon() {
  try {
    const { generateIconUrl } = await import('./mapIcons.js');
    const stateEl = getById('gcState');
    if (!stateEl) return;
    const state = JSON.parse(stateEl.textContent);
    state.hasCC  = !!gc.hasCC;
    state.hasPCN = !!gc.hasPCN;
    state.isFound = !!gc.isFound;
    state.isDNF   = !!gc.isDNF;
    stateEl.textContent = JSON.stringify(state);

    const url = generateIconUrl(state.typeId, state, ICON_SIZE);
    const img = getById('cacheIcon')?.querySelector('img');
    if (img) img.src = url;
  } catch { /* silent */ }
}

// -----------------------------------------------------------------
// createLogsTable()

function createLogsTable() {
  const loadingEl = getById('logsLoading');
  if (loadingEl) loadingEl.style.display = 'none';

  logsTable = new Tabulator('#logsTable', {
    headerVisible: false,
    borders: false,
    layout: 'fitColumns',
    selectableRows: false,
    data: logs,
    rowFormatter: row => {
      row.getElement().classList.add('row-transparent');
      row.getElement().style.marginBottom = '12px';
    },
    columnDefaults: { resizable: false },
    columns: [{
      title: '',
      field: '',
      formatter: cell => {
        const d = cell.getData();
        const color    = OC_LOG_COLORS[d.type] || '#e8e8e8';
        const typeName = OC_LOG_TYPE_NAMES[d.type] || d.typeName || `${t('Type')} ${d.type}`;
        const text     = d.textHtml ? (d.text || '') : (d.text || '').replace(/\n/g, '<br>');

        return `
          <div style="display:flex;flex-direction:column;background-color:${color};color:#333;margin:0;padding:2px 6px;width:100%;">
            <span><b>${d.date} ${d.username} &mdash; ${typeName}</b></span>
          </div>
          <div style="margin-top:3px">
            <div class="wrap-text" style="overflow:hidden;line-height:1.4;font-size:1.00em">${text}</div>
          </div>
        `;
      },
    }],
  });
}

// -----------------------------------------------------------------
// handleLogTextarea() — morph-logic entry point.
//
// The "New Log" checkbox flips the dialog between editing the user's
// existing log and composing a fresh one. Do not alter this behavior.

function handleLogTextarea() {
  const log = getMyNewestLog(logs);
  if (editLog) editLog.style.display = 'block';

  if (log) {
    setupEditModeUI(log);
    editLogText?.addEventListener('input', () => {
      newLog = editLogText.value;
      adjustHeight(editLogText);

      if (newLog !== oldLog) {
        editLogChanged = true;
        if (editLogControls) editLogControls.style.visibility = 'visible';
        if (editLogSave) {
          editLogSave.style.backgroundColor = 'red';
          editLogSave.textContent = t('Submit');
          editLogSave.disabled = false;
        }
      } else {
        editLogChanged = false;
        if (editLogControls) editLogControls.style.visibility = 'hidden';
      }
    });

  } else {
    setupNewModeUI(log);
    if (editLogCheckboxWrapper) editLogCheckboxWrapper.style.display = 'none';

    editLogText?.addEventListener('input', () => {
      newLog = editLogText.value;
      adjustHeight(editLogText);

      if (newLog.trim().length > 0) {
        editLogChanged = true;
        if (editLogControls) editLogControls.style.visibility = 'visible';
        if (editLogSave) {
          editLogSave.style.backgroundColor = 'red';
          editLogSave.textContent = t('Post Log');
          editLogSave.disabled = false;
        }
      } else {
        editLogChanged = false;
        if (editLogControls) editLogControls.style.visibility = 'hidden';
      }
    });
  }

  // Always register — re-read current log at event time, never use stale closure.
  // Bug: when page loads with zero logs, log=null and the checkbox appeared after
  // the first POST but had no listener, so mode stayed 'editLog' and the next
  // submit tried PUT on null.id.
  editLogCheckbox?.addEventListener('change', () => {
    const activeLog = getMyNewestLog(logs);
    if (mode === 'newLog') {
      if (activeLog) setupEditModeUI(activeLog);
      if (editLogControls) editLogControls.style.visibility = 'hidden';
    } else {
      setupNewModeUI(activeLog);
    }
  });

  editLogSave?.addEventListener('click', async () => {
    // Re-read at click time: stale closure had null when page loaded with no logs.
    const activeLog = mode === 'newLog' ? null : getMyNewestLog(logs);
    await updateLog(activeLog, editLogSave, mode);
  });
}

// -----------------------------------------------------------------
// setupEditModeUI() — populate UI for editing the user's own log

function setupEditModeUI(log) {
  mode = 'editLog';
  if (!editModeInitialized) {
    editModeInitialized = true;
    oldLog = log.text || '';
  }

  const typeName = OC_LOG_TYPE_NAMES[log.type] || log.typeName || '';
  if (editLogHeaderText) editLogHeaderText.textContent = `${log.date} ${log.username} ${typeName}`;
  if (deleteLogBtn) {
    deleteLogBtn.style.display = 'inline-block';
    deleteLogBtn.onclick = () => deleteLog(log);
  }
  if (editLogText) editLogText.value = log.text || '';
  if (editLogDate) editLogDate.value = log.date + 'T00:00:00';
  if (editLogCheckboxWrapper) editLogCheckboxWrapper.style.display = 'block';
  if (editLogCheckbox) editLogCheckbox.checked = false;

  if (editLogType) {
    const allowed = allowedLogTypes(log);
    editLogType.innerHTML = '';
    allowed.forEach(typeId => {
      const opt = document.createElement('option');
      opt.value = typeId;
      opt.textContent = OC_LOG_TYPE_NAMES[typeId] || `${t('Type')} ${typeId}`;
      if (typeId === log.type) opt.selected = true;
      editLogType.appendChild(opt);
    });
  }
  if (editLogText) adjustHeight(editLogText);
}

// -----------------------------------------------------------------
// allowedLogTypes() — strip Found (1) / Attended (7) from the dropdown
// when the user already has one of those on this cache (mirrors the
// backend gate). `excludeLog` is the log being edited, if any — its
// type must remain available so the option stays selected.

function allowedLogTypes(excludeLog) {
  const types = gc.logTypes || [];
  const mine = logs.filter(l => l.itsMine && (!excludeLog || l.id !== excludeLog.id));
  const hasFound    = mine.some(l => l.type === 1);
  const hasAttended = mine.some(l => l.type === 7);
  return types.filter(typeId => {
    if (typeId === 1 && hasFound)    return false;
    if (typeId === 7 && hasAttended) return false;
    return true;
  });
}

// -----------------------------------------------------------------
// setupNewModeUI() — populate UI for composing a new log

function setupNewModeUI(log) {
  mode = 'newLog';
  if (!newModeInitialized) newModeInitialized = true;

  if (editLogHeaderText) editLogHeaderText.textContent = t('Compose New Log');
  if (deleteLogBtn) deleteLogBtn.style.display = 'none';
  if (editLogText) {
    editLogText.placeholder = t('Enter your log here...');
    editLogText.value = '';
  }
  if (editLogDate) editLogDate.value = localDatetime(gc.ianaTimezoneId);
  if (editLogCheckbox) editLogCheckbox.checked = true;

  if (editLogType) {
    const prevType = parseInt(editLogType.value, 10); // preserve user's current selection
    const allowed = allowedLogTypes(null);
    editLogType.innerHTML = '';
    const defaultId = allowed.includes(prevType) ? prevType
      : (allowed.includes(1) ? 1 : (allowed.includes(7) ? 7 : (allowed[0] ?? 3)));
    allowed.forEach(typeId => {
      const opt = document.createElement('option');
      opt.value = typeId;
      opt.textContent = OC_LOG_TYPE_NAMES[typeId] || `${t('Type')} ${typeId}`;
      if (typeId === defaultId) opt.selected = true;
      editLogType.appendChild(opt);
    });
  }
  if (editLogText) adjustHeight(editLogText);
}

// -----------------------------------------------------------------
// updateLog() — create new (POST) or edit existing (PUT)

async function updateLog(log, saveBtn, currentMode) {
  if (saveBtn) saveBtn.disabled = true;
  // Clear any leftover error from a previous failed attempt.
  if (editLogMsg) {
    editLogMsg.textContent = '';
    editLogMsg.style.display = 'none';
  }
  const isNew = currentMode === 'newLog';
  const wp = gc.referenceCode;

  const payload = {
    type: parseInt(editLogType?.value || '3', 10),
    date: (editLogDate?.value || localDatetime(gc.ianaTimezoneId)).replace('T', ' '),
    text: (editLogText?.value || '').trim(),
    password: (editLogPasswd?.value || '').trim(),
  };

  try {
    const json = isNew
      ? await apiFetch(`/api/cache/${wp}/log`, {
          method:  'POST',
          headers: { 'Content-Type': 'application/json' },
          body:    JSON.stringify(payload),
        })
      : await apiFetch(`/api/cache/${wp}/log/${log.id}`, {
          method:  'PUT',
          headers: { 'Content-Type': 'application/json' },
          body:    JSON.stringify(payload),
        });

    if (saveBtn) {
      saveBtn.textContent = isNew ? t('Posted!') : t('Saved!');
      saveBtn.style.backgroundColor = 'green';
    }
    oldLog = editLogText?.value || '';
    editLogChanged = false;

    if (isNew) {
      const newRow = {
        id:       json.log?.id,
        uuid:     json.log?.uuid,
        type:     payload.type,
        typeName: OC_LOG_TYPE_NAMES[payload.type] || `${t('Type')} ${payload.type}`,
        date:     payload.date.slice(0, 10),
        username: context.userName,
        text:     payload.text,
        textHtml: false,
        itsMine:  true,
        user_id:  context.userId,
      };
      logs.unshift(newRow);
      if (logsTable) logsTable.addRow(newRow, true);

      // Reflect the new log in derived state + visible fields + marker.
      refreshAfterLogChange(payload.type, payload.date);

      // Switch the form into "edit your existing log" mode.
      mode = 'editLog';
      setupEditModeUI(newRow);
      if (editLogCheckboxWrapper) editLogCheckboxWrapper.style.display = '';
      if (editLogControls) editLogControls.style.visibility = 'hidden';
      if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.style.backgroundColor = '';
        saveBtn.textContent = t('Submit');
      }
    } else {
      // Edit: update the row in-place.
      const idx = logs.findIndex(l => l.id === log.id);
      const oldType = idx >= 0 ? logs[idx].type : null;
      let updated = null;
      if (idx >= 0) {
        updated = { ...logs[idx], type: payload.type, typeName: OC_LOG_TYPE_NAMES[payload.type] || logs[idx].typeName, date: payload.date.slice(0, 10), text: payload.text };
        logs[idx] = updated;
        if (logsTable) logsTable.updateRow(log.id, updated);
      }
      // Refresh the edit-form header so the type label tracks the edit.
      if (updated && editLogHeaderText) {
        const typeName = OC_LOG_TYPE_NAMES[updated.type] || updated.typeName || '';
        editLogHeaderText.textContent = `${updated.date} ${updated.username} ${typeName}`;
      }
      // If the user changed the type of their newest log, derived state may flip.
      if (oldType !== null && oldType !== payload.type) {
        refreshAfterLogChange(payload.type, payload.date, oldType);
      }
    }
  } catch (err) {
    if (editLogMsg) {
      editLogMsg.textContent = err.message;
      editLogMsg.style.display = 'inline-block';
    }
    if (saveBtn) {
      saveBtn.disabled = false;
      saveBtn.style.backgroundColor = 'red';
    }
  }
}

// -----------------------------------------------------------------
// refreshAfterLogChange() — keep page state, header icon, found-row,
// findCount and the map marker in sync with a freshly posted or edited
// log. `newType` is the log's new type id; `oldType` (optional) is the
// previous type when editing.

function refreshAfterLogChange(newType, dateStr, oldType = null) {
  const isFoundType = (t) => t === 1 || t === 7;
  const isDnfType   = (t) => t === 2;

  // Status-changing log types: update isArchived / isDisabled
  if (newType === 9)      { gc.isArchived = true;  gc.isDisabled = false; }
  else if (newType === 10) { gc.isArchived = false; gc.isDisabled = false; }
  else if (newType === 11) { gc.isArchived = false; gc.isDisabled = true; }
  // Re-render the status badge
  const statusEl = getById('cacheStatus');
  if (statusEl) {
    if (gc.isArchived) {
      statusEl.innerHTML = `<span class="badge bg-danger">${t('Archived')}</span>`;
    } else if (gc.isDisabled) {
      statusEl.innerHTML = `<span class="badge bg-warning">${t('Disabled')}</span>`;
    } else {
      statusEl.innerHTML = `<span class="badge bg-success">${t('Active')}</span>`;
    }
  }
  // Update the gcState hidden element so refreshCacheIcon picks up the new flags
  const gcStateEl = getById('gcState');
  if (gcStateEl) {
    let state = {};
    try { state = JSON.parse(gcStateEl.textContent); } catch (e) {}
    state.isArchived = !!gc.isArchived;
    state.isDisabled = !!gc.isDisabled;
    gcStateEl.textContent = JSON.stringify(state);
  }

  // Adjust the cache-wide findCount: only Found/Attended counts.
  if (oldType === null) {
    if (isFoundType(newType)) gc.findCount = (gc.findCount || 0) + 1;
  } else {
    if (isFoundType(newType) && !isFoundType(oldType)) gc.findCount = (gc.findCount || 0) + 1;
    if (!isFoundType(newType) && isFoundType(oldType)) gc.findCount = Math.max(0, (gc.findCount || 0) - 1);
  }
  setText('cacheFindCount', (gc.findCount || 0).toLocaleString());

  // Determine the user's overall state from their remaining logs.
  const myLogs = logs.filter(l => l.itsMine);
  const myNewestFind = myLogs.find(l => isFoundType(l.type));
  const myNewestDnf  = myLogs.find(l => isDnfType(l.type));

  gc.isFound = !!myNewestFind;
  gc.isDNF   = !myNewestFind && !!myNewestDnf;
  gc.foundDate    = myNewestFind ? myNewestFind.date : '';
  gc.foundDateFmt = gc.foundDate;
  gc.dnfDate      = myNewestDnf ? myNewestDnf.date : '';
  gc.dnfDateFmt   = gc.dnfDate;

  // foundRow shows either the Found Date or, if no find, the DNF Date.
  const foundRow   = getById('foundRow');
  const foundLabel = getById('foundLabel');
  if (gc.foundDateFmt) {
    if (foundRow)   foundRow.style.display = '';
    if (foundLabel) foundLabel.textContent = t('Found Date');
    setText('cacheFoundDate', gc.foundDateFmt);
  } else if (gc.dnfDateFmt) {
    if (foundRow)   foundRow.style.display = '';
    if (foundLabel) foundLabel.textContent = t('DNF Date');
    setText('cacheFoundDate', gc.dnfDateFmt);
  } else {
    if (foundRow) foundRow.style.display = 'none';
  }

  // Header icon (uses isFound / isDNF / hasCC / hasPCN).
  refreshCacheIcon();

  // Map marker (also wired for CC edits — pass full state for status-aware coloring).
  if (mapUpdateMarkerIcon) {
    try { mapUpdateMarkerIcon(gc.referenceCode, { hasCC: gc.hasCC, hasPCN: gc.hasPCN, isFound: gc.isFound, isDNF: gc.isDNF, isArchived: gc.isArchived, isDisabled: gc.isDisabled }); }
    catch (e) { console.log('mapUpdateMarkerIcon threw:', e); }
  }
}

// -----------------------------------------------------------------
// deleteLog()

async function deleteLog(log) {
  if (!confirm(t('Delete this log from opencaching.de?'))) return;
  if (editLogMsg) {
    editLogMsg.textContent = '';
    editLogMsg.style.display = 'none';
  }

  try {
    await apiFetch(`/api/cache/${gc.referenceCode}/log/${log.id}`, {
      method: 'DELETE',
    });

    const idx = logs.findIndex(l => l.id === log.id);
    const deletedType = idx >= 0 ? logs[idx].type : null;
    if (idx >= 0) logs.splice(idx, 1);
    if (logsTable) {
      try { logsTable.deleteRow(log.id); } catch (e) { /* row may already be gone */ }
    }
    // If the deleted log was a Found/Attended, derived state may flip.
    if (deletedType !== null) {
      refreshAfterLogChange(/* newType */ 3, /* date */ '', /* oldType */ deletedType);
    }
    if (editLog) editLog.style.display = 'none';
    // After delete the user may have no own log left → switch the form to "new log" mode.
    const remaining = getMyNewestLog(logs);
    if (!remaining) {
      mode = 'newLog';
      setupNewModeUI(null);
      if (editLogCheckboxWrapper) editLogCheckboxWrapper.style.display = 'none';
      if (editLog) editLog.style.display = 'block';
    }
  } catch (err) {
    if (editLogMsg) {
      editLogMsg.textContent = `Delete failed: ${err.message}`;
      editLogMsg.style.display = 'inline-block';
    }
  }
}

// -----------------------------------------------------------------
// initMap() — feed this cache into the already-loaded map module and call handleWPs.
//
// loader.js has imported map.js (side effects: map, controls, registries set up).
// handleWPs() reads window.uniCacheWP — push the cooked cache in and call it.

async function initMap() {
  window.lat = gc.lat;
  window.lon = gc.lon;
  const m = await initPageMap([gc]);
  if (m) {
    mapHandleWPs        = m.handleWPs;
    mapUpdateMarkerIcon = m.updateStaticMarkerIcon;
  }
}

// -----------------------------------------------------------------
// init() — page entry point

export async function init() {
  const container = getById('explore-container');
  const code = container?.dataset?.code;
  if (!code) {
    showError('No cache code specified');
    return;
  }
  await loadCache(code);
}

// vim: ts=2:sw=2:et:ft=javascript
