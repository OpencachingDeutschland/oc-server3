/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 *
 * searchcaches.js — search-caches page module.
 ***************************************************************************/

import { TabulatorFull as Tabulator } from '/vendor/tabulator/tabulator_esm.min.js';
import { searchCaches } from './cacheApi.js';
import { findCity } from './mapApi.js';
import { initPageMap } from './pageMap.js';

// API returns uniCacheWP-format objects — use those field names throughout.

let table        = null;
let geocodedCity = null; // { lat, lon }
let foundCount   = 0;

export function init() {
  document.getElementById('searchForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await runSearch();
  });

  document.getElementById('fcity').addEventListener('blur', geocodeCity);
  document.getElementById('btnShowMap').addEventListener('click', showOnMap);
}

// ---------------------------------------------------------------
// City geocoding

async function geocodeCity() {
  const input    = document.getElementById('fcity');
  const feedback = document.getElementById('cityFeedback');
  const q = input.value.trim();

  if (!q) { geocodedCity = null; feedback.textContent = ''; return; }

  const results = await findCity(q);
  if (results.length > 0) {
    geocodedCity = { lat: parseFloat(results[0].lat), lon: parseFloat(results[0].lon) };
    const label  = results[0].display_name.split(',').slice(0, 2).join(',').trim();
    feedback.textContent = '→ ' + label;
    feedback.className   = 'form-text text-success';
  } else {
    geocodedCity = null;
    feedback.textContent = 'City not found';
    feedback.className   = 'form-text text-danger';
  }
}

// ---------------------------------------------------------------
// Search

async function runSearch() {
  const q          = document.getElementById('fq').value.trim();
  const type       = parseInt(document.getElementById('ftype').value, 10);
  const minDiff    = parseFloat(document.getElementById('fminDiff').value);
  const maxDiff    = parseFloat(document.getElementById('fmaxDiff').value);
  const activeOnly = document.getElementById('factiveOnly').checked;
  const radius     = parseFloat(document.getElementById('fradius').value);

  if (document.getElementById('fcity').value.trim() && !geocodedCity) {
    await geocodeCity();
  }

  setStatus('Searching…');

  const params = { q, type, minDiff, maxDiff, activeOnly };
  if (geocodedCity && radius > 0) {
    params.lat    = geocodedCity.lat;
    params.lon    = geocodedCity.lon;
    params.radius = radius;
  }

  const items = await searchCaches(params);

  foundCount = items.length;
  const cap  = foundCount === 1000 ? ' (limit reached — refine filters)' : '';
  setStatus(foundCount ? `Found ${foundCount} cache(s)${cap}` : 'No caches found.');

  renderTable(items);
  updateActionsPanel(0, foundCount > 0);
}

// ---------------------------------------------------------------
// Tabulator table

function renderTable(data) {
  if (table) {
    table.setData(data);
    return;
  }

  table = new Tabulator('#searchResults', {
    data,
    layout: 'fitColumns',
    height: '60vh',
    renderVertical: 'virtual',
    selectableRows: true,
    columns: [
      {
        formatter: 'rowSelection', titleFormatter: 'rowSelection',
        width: 40, widthGrow: 0, widthShrink: 0,
        hozAlign: 'center', headerHozAlign: 'center', headerSort: false, resizable: false,
      },
      {
        title: 'OC Code', field: 'referenceCode',
        width: 110, widthGrow: 0, widthShrink: 0,
        formatter: (cell) => { const wp = cell.getValue(); return `<a href="/cache/${wp}">${wp}</a>`; },
      },
      { title: 'Name',  field: 'name',       minWidth: 200, widthGrow: 3, widthShrink: 1, tooltip: true },
      { title: 'Owner', field: 'ownerAlias', width: 150,    widthGrow: 1, widthShrink: 1, tooltip: true },
      {
        title: 'Type', field: 'geocacheType', width: 140, widthGrow: 1, widthShrink: 1,
        tooltip:   (e, cell) => cell.getValue()?.name || '',
        formatter: (cell)    => cell.getValue()?.name || '',
        sorter:    (a, b)    => (a?.name || '').localeCompare(b?.name || ''),
      },
      { title: 'D', field: 'difficulty', width: 60, minWidth: 60, widthGrow: 0, widthShrink: 0, resizable: false, hozAlign: 'center', headerSort: false, formatter: (cell) => parseFloat(cell.getValue()).toFixed(1) },
      { title: 'T', field: 'terrain',    width: 60, minWidth: 60, widthGrow: 0, widthShrink: 0, resizable: false, hozAlign: 'center', headerSort: false, formatter: (cell) => parseFloat(cell.getValue()).toFixed(1) },
    ],
  });

  table.on('rowSelectionChanged', (_data, rows) => {
    updateActionsPanel(rows.length, foundCount > 0);
  });
}

// ---------------------------------------------------------------
// Status bar + actions panel

function setStatus(text) {
  document.getElementById('statusBar').textContent = text;
}

function updateActionsPanel(selCount, hasResults) {
  document.getElementById('actionsPanel').style.display = hasResults ? '' : 'none';

  const sel = selCount > 0 ? ` · ${selCount} selected` : '';
  const cap = foundCount === 1000 ? ' (limit reached)' : '';
  setStatus(foundCount
    ? `Found ${foundCount} cache(s)${cap}${sel}`
    : 'No caches found.'
  );
}

// ---------------------------------------------------------------
// Map — delegate entirely to pageMap / map.js

async function showOnMap() {
  const selected   = table ? table.getSelectedData() : [];
  const items      = selected.length > 0 ? selected : (table ? table.getData() : []);
  const withCoords = items.filter(r => r.lat && r.lon);

  if (!withCoords.length) {
    setStatus('No coordinates available for these results.');
    return;
  }

  // Items are already in uniCacheWP format — pass directly.
  await initPageMap(withCoords);

  document.getElementById('mapRoot').scrollIntoView({ behavior: 'smooth' });
}
