// --------------------------------------------------------------
// mapFilter.js — OC live map filter
//
// Adapted for OC Symfony port.
// Licensed under the MIT License.
// --------------------------------------------------------------

let state = {};
let raw = JSON.parse(localStorage.getItem('filter'));
// Discard old GC-format filters (has isGC/isOC or GC type IDs like 11,12,13,137,1858,3333)
if (raw && (raw.isGC !== undefined || raw.isOC !== undefined || raw[3333] !== undefined)) {
  localStorage.removeItem('filter');
  raw = null;
}
let filter = raw;
if (!filter) {
  filter = {
    '2'  : true, '3'  : true, '4'  : true, '5'  : true,
    '6'  : true, '7'  : true, '8'  : true, '9'  : true, '10' : true,
    'isDisabled' : true, 'isOwned' : true, 'isFound' : true,
    'isDNF'      : true, 'hasCC'   : true, 'hasPCN'  : true,
    'minDiff' : 2, 'maxDiff' : 10,
  };
  localStorage.setItem('filter', JSON.stringify(filter));
}

// ---------------------------------------------------------------
// Modal form content
// ---------------------------------------------------------------

function generateFormContent() {
  let html = '<form>';

  // --- Cache types ---
  html += '<div class="mb-2 fw-bold">Cache Types</div><div class="row">';
  const types = [
    ['2', 'Traditional'], ['3', 'Multi'], ['4', 'Virtual'], ['5', 'Webcam'],
    ['6', 'Event'], ['7', 'Quiz'], ['8', 'Math/Physics'], ['9', 'Moving'],
    ['10', 'Drive-in'],
  ];
  types.forEach(([id, label]) => {
    html += `<div class="col-6"><div class="form-check">
      <input type="checkbox" class="form-check-input" id="${id}">
      <label class="form-check-label" for="${id}">${label}</label>
    </div></div>`;
  });
  html += '</div><hr class="my-2">';

  // --- Status flags ---
  html += '<div class="mb-2 fw-bold">Status</div><div class="row">';
  const flags = [
    ['isOwned', 'Owned'], ['isFound', 'Found'], ['isDNF', 'DNF'],
    ['isDisabled', 'Disabled'], ['hasCC', 'Corrected coords'], ['hasPCN', 'Personal note'],
  ];
  flags.forEach(([id, label]) => {
    html += `<div class="col-6"><div class="form-check">
      <input type="checkbox" class="form-check-input" id="${id}">
      <label class="form-check-label" for="${id}">${label}</label>
    </div></div>`;
  });
  html += '</div><hr class="my-2">';

  // --- Difficulty range ---
  html += '<div class="mb-2 fw-bold">Difficulty</div>';
  html += '<div class="d-flex align-items-center gap-2 mb-1">';
  html += '<label for="minDiff" class="form-label mb-0" style="width:40px">Min</label>';
  html += '<select id="minDiff" class="form-select form-select-sm" style="width:90px">';
  for (let v = 2; v <= 10; v++) html += `<option value="${v}">${(v/2).toFixed(1)}</option>`;
  html += '</select>';
  html += '<label for="maxDiff" class="form-label mb-0 ms-2" style="width:40px">Max</label>';
  html += '<select id="maxDiff" class="form-select form-select-sm" style="width:90px">';
  for (let v = 2; v <= 10; v++) html += `<option value="${v}">${(v/2).toFixed(1)}</option>`;
  html += '</select></div>';

  html += '</form>';
  return html;
}

// ---------------------------------------------------------------
// Modal DOM (injected at load time)
// ---------------------------------------------------------------

const modalWrapper = document.createElement('div');
modalWrapper.innerHTML = `
  <div class="modal fade" id="filterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="filterModalLabel">Filter Options</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="filterModalBody"></div>
        <div class="modal-footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6">
                <button type="button" class="btn btn-sm btn-success" id="allButton">All</button>
                <button type="button" class="btn btn-sm btn-warning" id="noneButton">None</button>
              </div>
              <div class="col-md-6 text-right">
                <button type="button" class="btn btn-primary" id="saveButton" data-bs-dismiss="modal">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeButton">Close</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>`;

document.body.appendChild(modalWrapper);

const filterModal     = document.getElementById('filterModal');
const filterModalBody = document.getElementById('filterModalBody');
filterModal.style.zIndex   = '10000';
filterModal.style.position = 'fixed';

const saveButton  = document.getElementById('saveButton');
const allButton   = document.getElementById('allButton');
const noneButton  = document.getElementById('noneButton');

// ---------------------------------------------------------------
// Modal open: populate from saved state
// ---------------------------------------------------------------

filterModal.addEventListener('show.bs.modal', () => {
  filterModalBody.innerHTML = generateFormContent();

  const saved = JSON.parse(localStorage.getItem('filter')) || {};
  filterModalBody.querySelectorAll('input[type="checkbox"]').forEach(cb => {
    cb.checked = !!saved[cb.id];
  });
  const minSel = filterModalBody.querySelector('#minDiff');
  const maxSel = filterModalBody.querySelector('#maxDiff');
  if (minSel) minSel.value = saved.minDiff ?? 2;
  if (maxSel) maxSel.value = saved.maxDiff ?? 10;
});

// ---------------------------------------------------------------
// Initialization
// ---------------------------------------------------------------

export function init(mapState) {
  state = mapState;
  state.filter = filter;

  // Leaflet Filter control button (top-right)
  const FilterControl = L.Control.extend({
    onAdd: function () {
      const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
      container.style.display = 'flex';
      container.style.flexDirection = 'column';
      container.style.gap = '4px';

      const btn = L.DomUtil.create('button', 'btn btn-sm btn-dark', container);
      btn.id = 'filterButton';
      btn.innerHTML = 'Filter';
      btn.style.display = 'none';
      btn.title = 'Open filter settings';

      btn.addEventListener('click', () => {
        new bootstrap.Modal(filterModal).show();
      });
      return container;
    }
  });
  new FilterControl({ position: 'topright' }).addTo(state.mapRoot);

  // Save handler
  saveButton.onclick = () => {
    const newFilter = {};
    filterModalBody.querySelectorAll('input[type="checkbox"]').forEach(cb => {
      newFilter[cb.id] = cb.checked;
    });
    const minSel = filterModalBody.querySelector('#minDiff');
    const maxSel = filterModalBody.querySelector('#maxDiff');
    newFilter.minDiff = minSel ? parseInt(minSel.value, 10) : 2;
    newFilter.maxDiff = maxSel ? parseInt(maxSel.value, 10) : 10;

    const diffChanged = filter.minDiff !== newFilter.minDiff || filter.maxDiff !== newFilter.maxDiff;

    localStorage.setItem('filter', JSON.stringify(newFilter));

    const oldTrueCount = countTrueProperties(filter);
    filter = newFilter;
    state.filter = filter;
    const newTrueCount = countTrueProperties(filter);

    // Difficulty change requires server re-fetch (can't filter client-side)
    if (diffChanged) {
      state.fetchAndShowLiveMarkers();
    } else if (newTrueCount >= oldTrueCount) {
      state.fetchAndShowLiveMarkers();
    } else {
      state.refreshLiveMarkers();
    }
  };
}

// ---------------------------------------------------------------
// Buttons
// ---------------------------------------------------------------

allButton.onclick  = () => toggleCheckboxes(true);
noneButton.onclick = () => toggleCheckboxes(false);

function toggleCheckboxes(checked) {
  filterModalBody.querySelectorAll('input[type="checkbox"]').forEach(cb => { cb.checked = checked; });
}

function countTrueProperties(obj) {
  return Object.values(obj).filter(v => v === true).length;
}
