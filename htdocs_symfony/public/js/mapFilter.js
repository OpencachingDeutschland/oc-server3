// --------------------------------------------------------------
// mapFilter.js
//
// © 2025 hxdimpf Research
//
// This file is part of GCxM.
// Licensed under the MIT License.
//
// You may use, copy, modify, and distribute this software
// under the terms of the MIT License.
//
// https://opensource.org/licenses/MIT
// --------------------------------------------------------------

// --------------------------------------------
// Filter variables
//
// Note: This is a two column table so each "row" must have an
// entry for two rows. If we do not need an entry in a specific
// column we need to pad it with an empty 'id' and 'label'

let state = {};
let filter = JSON.parse(localStorage.getItem('filter'));
if (!filter) {
 filter = {
      '2'       : true, // numbers correspond to cache types
      '3'       : true,
      '4'       : true,
      '5'       : true,
      '6'       : true,
      '8'       : true,
     '10'       : true,
     '11'       : true,
     '12'       : true,
     '13'       : true,
    '137'       : true,
   '1858'       : true,
   '3333'       : true,

   'isDisabled' : true,
   'isOwned'    : true,
   'isFound'    : true,
   'isDNF'      : true,
   'hasCC'      : true,
   'hasPCN'     : true,
   'isGC'       : true,
   'isOC'       : false,
  };
  localStorage.setItem('filter', JSON.stringify(filter));
}

// enabledPlatforms is a global variable set by the backend's template engine
// Determine platform configuration
const hasGC = enabledPlatforms.includes('GC');
const hasOC = enabledPlatforms.includes('OC');
const hasAL = enabledPlatforms.includes('AL');
const hasBothCachingPlatforms = hasGC && hasOC;

// For single-platform sessions, force the platform enabled (no choice needed)
// For multi-platform sessions, respect user's filter choice but disable unavailable platforms
if (!hasGC) {
  filter.isGC = false;
} else if (!hasOC) {
  filter.isGC = true;  // Force GC enabled when it's the only caching platform
}

if (!hasOC) {
  filter.isOC = false;
} else if (!hasGC) {
  filter.isOC = true;  // Force OC enabled when it's the only caching platform
}

if (!hasAL) {
  filter[3333] = false;
}


function generateFormContent() {
  const options = [
    // Platform section - only show checkboxes when there's a choice to make
    { id:   'isGC',        label: 'geocaching.com' , configured: hasBothCachingPlatforms },
    { id:   'isOC',        label: 'opencaching.de' , configured: hasBothCachingPlatforms },

    { id:   '3333',        label: 'Adventure Labs' , configured: hasAL },
    { id:   '',            label: ''               , configured: true },

    // Separator between platforms and types
    { separator: true },

    // Cache types and flags
    { id:    '2',          label: 'Tradi'          , configured: true },
    { id:    'isFound',    label: 'isFound'        , configured: true },

    { id:    '3',          label: 'Multi'          , configured: true },
    { id:    'isDNF',      label: 'isDNF'          , configured: true },

    { id:    '4',          label: 'Virtual'        , configured: true },
    { id:    'isDisabled', label: 'isDisabled'     , configured: true },

    { id:    '5',          label: 'Letterbox'      , configured: true },
    { id:    'hasCC',      label: 'hasCC'          , configured: true },

    { id:    '6',          label: 'Event'          , configured: true },
    { id:    'hasPCN',     label: 'hasPCN'         , configured: true },

    { id:    '8',          label: 'Unknown'        , configured: true },
    { id:   'isOwned',     label: 'isOwned'        , configured: true },

    { id:   '10',          label: 'Drive-in'       , configured: hasOC },
    { id:   '',            label: ''               , configured: hasOC },

    { id:   '11',          label: 'Webcam'         , configured: true },
    { id:   '',            label: ''               , configured: true },

    { id:   '12',          label: 'Locationless'   , configured: true },
    { id:   '',            label: ''               , configured: true },

    { id:   '13',          label: 'CITO'           , configured: hasGC },
    { id:   '',            label: ''               , configured: hasGC },

    { id:  '137',          label: 'Earthcache'     , configured: true },
    { id:  '',             label: ''               , configured: true },

    { id: '1858',          label: 'Wherigo'        , configured: true },
    { id: '',              label: ''               , configured: true },
  ];

  let formContent = '<form>';
  for (let i = 0; i < options.length; i++) {
    const opt = options[i];

    // Handle separator
    if (opt.separator) {
      formContent += '<hr class="my-2">';
      continue;
    }

    // Skip unconfigured or empty options
    if (!opt.configured || opt.id === '') continue;

    // Start a row
    formContent += `<div class="row"><div class="col-md-6">
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="${opt.id}">
        <label class="form-check-label" for="${opt.id}">${opt.label}</label>
      </div></div>`;

    // Check if next option should be in the same row
    const next = options[i + 1];
    if (next && !next.separator && next.configured && next.id !== '') {
      formContent += `<div class="col-md-6">
        <div class="form-check">
          <input type="checkbox" class="form-check-input" id="${next.id}">
          <label class="form-check-label" for="${next.id}">${next.label}</label>
        </div></div>`;
      i++; // Skip the next item since we processed it
    }

    formContent += `</div>`;
  }
  formContent += '</form>';
  return formContent;
}

// ---------------------------
// Filter modal
const modalWrapper = document.createElement('div');
modalWrapper.innerHTML = `
  <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="filterModalLabel">Filter Options</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="filterModalBody">
          <!-- Form content will be dynamically added here -->
        </div>
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

const filterModal = document.getElementById('filterModal');
filterModal.style.zIndex   = '10000';
filterModal.style.position = 'fixed';

const filterModalBody = document.getElementById('filterModalBody');

filterModal.addEventListener('show.bs.modal', (e) => {
  filterModalBody.innerHTML = generateFormContent();

  const savedState = JSON.parse(localStorage.getItem('filter')) || {};
  const checkboxes = document.querySelectorAll('.form-check-input');
  checkboxes.forEach((checkbox) => {
    checkbox.checked = savedState[checkbox.id] || false;
  });
});

const saveButton  = document.getElementById('saveButton');
const closeButton = document.getElementById('closeButton');
const allButton   = document.getElementById('allButton');
const noneButton  = document.getElementById('noneButton');

// --------------------------------------------
// filter form save button

/**
 * Initialize the save button with modal logic
 */
export function init(mapState) {
  state = mapState;
  state.filter = filter;

  // -------------------------------
  // Create Filter Control
  // -------------------------------
  const FilterControl = L.Control.extend({
    onAdd: function (mapRoot) {
      // Top-level container (Leaflet expects a div)
      const container = L.DomUtil.create(
        'div',
        'leaflet-bar leaflet-control leaflet-control-custom'
      );

      container.style.display = 'flex';
      container.style.flexDirection = 'column';
      container.style.gap = '4px';

      // Inner button
      const filterButton = L.DomUtil.create(
        'button',
        'btn btn-sm btn-dark',
        container
      );
      filterButton.id = 'filterButton';
      filterButton.innerHTML = 'Filter';
      filterButton.style.display = 'none'; // hidden until live map is enabled
      filterButton.title = 'Open filter settings';

      filterButton.addEventListener('click', () => {
        const filterModal = document.getElementById('filterModal');
        if (filterModal) {
          const modal = new bootstrap.Modal(filterModal);
          modal.show();
        }
      });

      return container;
    }
  });

  const filterControl = new FilterControl({ position: 'topright' });
  filterControl.addTo(state.mapRoot);

  // -------------------------------
  // Save filter button handler
  // -------------------------------
  saveButton.onclick = () => {
    const newFilter = {};
    const checkboxes = document.querySelectorAll('.form-check-input');
    checkboxes.forEach((checkbox) => {
      newFilter[checkbox.id] = checkbox.checked;
    });

    // For single-platform sessions, force the platform enabled
    // For multi-platform sessions, respect user's filter choice but disable unavailable platforms
    if (!hasGC) {
      newFilter.isGC = false;
    } else if (!hasOC) {
      newFilter.isGC = true;  // Force GC enabled when it's the only caching platform
    }

    if (!hasOC) {
      newFilter.isOC = false;
    } else if (!hasGC) {
      newFilter.isOC = true;  // Force OC enabled when it's the only caching platform
    }

    if (!hasAL) {
      newFilter[3333] = false;
    }

    localStorage.setItem('filter', JSON.stringify(newFilter));

    const oldFilter = filter;
    state.filter = newFilter;

    const oldTrueCount = countTrueProperties(oldFilter);
    const newTrueCount = countTrueProperties(newFilter);

    filterHandler(oldTrueCount, newTrueCount);
  };
}


function filterHandler(oldCount, newCount) {
  if (newCount > oldCount) state.fetchAndShowLiveMarkers();
  else {
    //console.log("mapFilter calling refreshLiveMarkers()");
    state.refreshLiveMarkers();
  }
};

// --------------------------------------------
// count the number of filter properties which are set to true

function countTrueProperties(filter) {
  return Object.values(filter).filter(value => value === true).length;
}

// --------------------------------------------
// filter form "All" button click handler

allButton.onclick = () => {
  toggleCheckboxes(true);
};

// --------------------------------------------
// filter form "None" button click handler

noneButton.onclick = () => {
  toggleCheckboxes(false);
};

// --------------------------------------------
//

function toggleCheckboxes(checked) {
  const checkboxes = document.querySelectorAll('#filterModalBody input[type="checkbox"]');
  checkboxes.forEach(checkbox => { checkbox.checked = checked; });
}


// code: language=javascript insertSpaces=true tabSize=2
// vim: ts=2:sw=2:et:ft=javascript
