// --------------------------------------------------------------
// mapSelect.js
//
// © 2025 hxdimpf Research
//
// Licensed under the MIT License.
//
// You may use, copy, modify, and distribute this software
// under the terms of the MIT License.
//
// https://opensource.org/licenses/MIT
// --------------------------------------------------------------

import { getIcon } from './mapIcons.js';
import { showToast, downloadFile } from './helpers.js';
import { generateWaypointGPX } from './gpx.js';
import { MapButtonControl } from './mapHelpers.js';

let state = {};
let drawnItems;
let controls;

// Draw handlers
let drawRectangle;
let drawCircle;
let editHandler;
let deleteHandler;

// Track active draw mode
let activeDrawMode = null;

// Store original layer states for cancel/revert
let originalLayerStates = new Map();

// Store layers removed during delete mode for potential restore
let deletedLayers = [];

// -------------------------------------
// Actions Dropdown
// -------------------------------------

function createActionsDropdown() {
  const wrapper = document.createElement('div');
  wrapper.className = 'map-dropdown';

  // Trigger button (action button - always white)
  const trigger = document.createElement('button');
  trigger.type = 'button';
  trigger.className = 'map-btn off';
  trigger.innerText = '⋯';
  trigger.title = 'Actions';

  // Dropdown menu
  const menu = document.createElement('div');
  menu.className = 'map-dropdown-menu';

  // Action items - easily extensible
  const actions = [
    { label: 'GPX', title: 'Download selected caches as GPX waypoints', handler: () => downloadGPX() },
    { label: 'TXT', title: 'Download selected cache codes as text file', handler: () => downloadTXT() }
  ];

  actions.forEach(action => {
    const item = document.createElement('button');
    item.type = 'button';
    item.className = 'map-dropdown-item';
    item.innerText = action.label;
    item.title = action.title;
    item.addEventListener('click', e => {
      e.preventDefault();
      e.stopPropagation();
      menu.classList.remove('show');
      action.handler();
    });
    menu.appendChild(item);
  });

  // Toggle menu on trigger click
  trigger.addEventListener('click', e => {
    e.preventDefault();
    e.stopPropagation();
    menu.classList.toggle('show');
  });

  // Close menu when clicking outside
  document.addEventListener('click', e => {
    if (!wrapper.contains(e.target)) {
      menu.classList.remove('show');
    }
  });

  wrapper.appendChild(trigger);
  wrapper.appendChild(menu);

  return wrapper;
}

// -------------------------------------
// Initialize selection mode controls
// -------------------------------------

export function init(mapState) {
  state = mapState;
  const map = state.mapRoot;

  // Expose binding function so map.js can call it when new markers are added
  state.bindMarkersForSelection = bindMarkersForSelection;

  // --- Drawn shapes layer ---
  drawnItems = new L.FeatureGroup();
  map.addLayer(drawnItems);

  // --- Create draw handlers (not L.Control.Draw) ---
  drawRectangle = new L.Draw.Rectangle(map, {
    shapeOptions: { color: '#3388ff', weight: 2 }
  });

  drawCircle = new L.Draw.Circle(map, {
    shapeOptions: { color: '#3388ff', weight: 2 }
  });

  // --- Handle draw events ---
  map.on('draw:created', e => {
    drawnItems.addLayer(e.layer);
    activeDrawMode = null;
    updateDrawButtonStates();
  });

  map.on('draw:drawstart', () => {
    // Visual feedback handled by button states
  });

  map.on('draw:drawstop', () => {
    activeDrawMode = null;
    updateDrawButtonStates();
  });

  // Note: draw:edited and draw:deleted events are handled by saveEditDelete()
  // No automatic disable - user must click Save or Cancel

  // --- Update shape-dependent button states ---
  drawnItems.on('layeradd layerremove', () => {
    updateShapeButtonStates();
    // Exit edit/delete mode if no shapes remain.
    // Deferred: layerremove fires synchronously inside Leaflet.Draw's _removeLayer(),
    // which still needs _deletedLayers alive to complete its work.
    if (drawnItems.getLayers().length === 0) {
      setTimeout(() => exitEditDeleteModeIfNoShapes(), 0);
    }
  });

  // --- Create count display element ---
  const countDisplay = document.createElement('div');
  countDisplay.className = 'map-info-display';
  countDisplay.id = 'select-count-display';
  countDisplay.innerText = '# Caches: 0';

  // --- Create Actions dropdown element ---
  const actionsDropdown = createActionsDropdown();

  // --- Create MapButtonControl with 3 rows ---
  controls = new MapButtonControl({
    position: 'topleft',
    rows: [
      {
        id: 'select-row1',
        buttons: [
          {
            id: 'select-toggle',
            icon: '⛶',
            title: 'Toggle Selection Mode',
            onClick: (_map, ctrl) => toggleSelectionMode(ctrl)
          },
          {
            id: 'select-actions',
            element: actionsDropdown,
            hidden: true,
            toggleVisible: true
          },
          {
            id: 'select-all',
            icon: '▣',
            title: 'Select All Visible',
            hidden: true,
            toggleVisible: true,
            onClick: () => smSelectAllVisibleMarkers()
          },
          {
            id: 'select-none',
            icon: '□',
            title: 'Deselect All',
            hidden: true,
            toggleVisible: true,
            onClick: () => smDeselectAllMarkers()
          },
          {
            id: 'select-invert',
            icon: '⇆',
            title: 'Invert Selection',
            hidden: true,
            toggleVisible: true,
            onClick: () => smInvertVisibleSelection()
          },
        ]
      },
      {
        id: 'select-row2',
        buttons: [
          {
            id: 'draw-rect',
            icon: '▢',
            title: 'Draw Rectangle',
            hidden: true,
            toggleVisible: true,
            onClick: () => toggleDrawRectangle()
          },
          {
            id: 'draw-circle',
            icon: '◯',
            title: 'Draw Circle',
            hidden: true,
            toggleVisible: true,
            onClick: () => toggleDrawCircle()
          },
          {
            id: 'select-add-shape',
            icon: '+',
            title: 'Add caches in shape to selection',
            hidden: true,
            toggleVisible: true,
            onClick: () => smSelectMarkersInAllShapes()
          },
          {
            id: 'select-remove-shape',
            icon: '−',
            title: 'Remove caches in shape from selection',
            hidden: true,
            toggleVisible: true,
            onClick: () => smDeselectMarkersInAllShapes()
          },
          {
            id: 'select-clear-shape',
            icon: '×',
            title: 'Clear all shapes',
            hidden: true,
            toggleVisible: true,
            onClick: () => clearAllShapes()
          },
        ]
      },
      {
        id: 'select-row3',
        buttons: [
          {
            id: 'edit-layers',
            icon: '✎',
            title: 'Edit Layers',
            hidden: true,
            toggleVisible: true,
            onClick: () => toggleEditMode()
          },
          {
            id: 'delete-layers',
            icon: '🗑',
            title: 'Delete Layers',
            hidden: true,
            toggleVisible: true,
            onClick: () => toggleDeleteMode()
          },
          {
            id: 'select-count',
            element: countDisplay,
            hidden: true,
            toggleVisible: true
          },
          {
            id: 'edit-save',
            icon: '✓',
            title: 'Save Changes',
            hidden: true,
            onClick: () => saveEditDelete()
          },
          {
            id: 'edit-cancel',
            icon: '✗',
            title: 'Cancel Changes',
            hidden: true,
            onClick: () => cancelEditDelete()
          },
          {
            id: 'delete-all',
            icon: '⌫',
            title: 'Delete All Shapes',
            hidden: true,
            onClick: () => deleteAllShapes()
          }
        ]
      }
    ]
  });

  controls.addTo(map);

  // Initialize ALL button states before they're ever shown
  // This ensures buttons have correct classes from the start
  initializeAllButtonStates();
}

// -------------------------------------
// Initialize all button states
// Called once after controls are created
// -------------------------------------

function initializeAllButtonStates() {
  // Toggle buttons start in OFF state (white)
  const toggleButtonIds = ['draw-rect', 'draw-circle', 'edit-layers', 'delete-layers'];
  toggleButtonIds.forEach(id => {
    const btn = controls.getElement(id);
    if (btn) {
      btn.classList.remove('on');
      btn.classList.add('off');
    }
  });

  // Action buttons are always white (off)
  const actionButtonIds = ['select-all', 'select-none', 'select-invert'];
  actionButtonIds.forEach(id => {
    const btn = controls.getElement(id);
    if (btn) {
      btn.classList.remove('on');
      btn.classList.add('off');
    }
  });

  // Shape-dependent buttons start disabled (no shapes yet)
  updateShapeButtonStates();
}

// -------------------------------------
// Toggle Selection Mode
// -------------------------------------

function toggleSelectionMode(ctrl) {
  if (state.rmRoutingModeEnabled) {
    return showToast('Please turn off routing mode before activating selection mode');
  }

  state.smSelectionModeEnabled = !state.smSelectionModeEnabled;
  ctrl.setActive('select-toggle', state.smSelectionModeEnabled);

  // All buttons that need visibility toggling (show/hide only)
  const allButtonIds = [
    'select-actions', 'select-all', 'select-none', 'select-invert',
    'select-add-shape', 'select-remove-shape', 'select-clear-shape', 'select-count',
    'draw-rect', 'draw-circle',
    'edit-layers', 'delete-layers'
  ];

  // Show/hide all buttons based on selection mode
  allButtonIds.forEach(id => {
    if (state.smSelectionModeEnabled) {
      ctrl.show(id);
    } else {
      ctrl.hide(id);
    }
  });

  if (state.smSelectionModeEnabled) {
    enableSelectMode();
    state.mapRoot.getContainer().style.cursor = 'pointer';
  } else {
    disableSelectMode();
    state.mapRoot.getContainer().style.cursor = '';
    drawnItems.clearLayers();
    disableAllDrawModes();
    disableEditDeleteModes();
    // Reset all button states to initial (off) for next enable
    initializeAllButtonStates();
  }

  updateShapeButtonStates();
}

// -------------------------------------
// Draw mode toggles
// -------------------------------------

function toggleDrawRectangle() {
  disableEditDeleteModes();

  if (activeDrawMode === 'rectangle') {
    drawRectangle.disable();
    activeDrawMode = null;
  } else {
    if (activeDrawMode === 'circle') drawCircle.disable();
    drawRectangle.enable();
    activeDrawMode = 'rectangle';
  }
  updateDrawButtonStates();
}

function toggleDrawCircle() {
  disableEditDeleteModes();

  if (activeDrawMode === 'circle') {
    drawCircle.disable();
    activeDrawMode = null;
  } else {
    if (activeDrawMode === 'rectangle') drawRectangle.disable();
    drawCircle.enable();
    activeDrawMode = 'circle';
  }
  updateDrawButtonStates();
}

function toggleEditMode() {
  // Cannot enable if no shapes exist
  if (drawnItems.getLayers().length === 0) {
    showToast('No shapes to edit');
    return;
  }

  disableAllDrawModes();

  if (editHandler && editHandler.enabled()) {
    // Turn off edit mode
    editHandler.disable();
    editHandler = null;
    setButtonState('edit-layers', false);
    hideSaveCancelButtons();
  } else {
    // Turn off delete mode first if active
    if (deleteHandler) {
      deleteHandler.disable();
      deleteHandler = null;
      setButtonState('delete-layers', false);
    }
    // Turn on edit mode
    originalLayerStates.clear();
    drawnItems.eachLayer(layer => {
      if (layer instanceof L.Circle) {
        originalLayerStates.set(layer._leaflet_id, {
          type: 'circle',
          latlng: L.latLng(layer.getLatLng().lat, layer.getLatLng().lng),
          radius: layer.getRadius()
        });
      } else if (layer instanceof L.Rectangle) {
        originalLayerStates.set(layer._leaflet_id, {
          type: 'rectangle',
          bounds: L.latLngBounds(layer.getBounds().getSouthWest(), layer.getBounds().getNorthEast())
        });
      }
    });
    editHandler = new L.EditToolbar.Edit(state.mapRoot, { featureGroup: drawnItems });
    editHandler.enable();
    setButtonState('edit-layers', true);
    showSaveCancelButtons();
  }
}

function toggleDeleteMode() {
  // Cannot enable if no shapes exist
  if (drawnItems.getLayers().length === 0) {
    showToast('No shapes to delete');
    return;
  }

  disableAllDrawModes();

  if (deleteHandler && deleteHandler.enabled()) {
    // Turn off delete mode
    deleteHandler.disable();
    deleteHandler = null;
    setButtonState('delete-layers', false);
    hideSaveCancelButtons();
  } else {
    // Turn off edit mode first if active
    if (editHandler) {
      editHandler.disable();
      editHandler = null;
      setButtonState('edit-layers', false);
    }
    // Turn on delete mode
    deletedLayers = [];
    drawnItems.eachLayer(layer => deletedLayers.push(layer));
    deleteHandler = new L.EditToolbar.Delete(state.mapRoot, { featureGroup: drawnItems });
    deleteHandler.enable();
    setButtonState('delete-layers', true);
    showSaveCancelButtons(true);
  }
}

// Helper to set a single button's on/off state
function setButtonState(id, isOn) {
  const btn = controls.getElement(id);
  if (btn) {
    btn.classList.toggle('on', isOn);
    btn.classList.toggle('off', !isOn);
  }
}

function showSaveCancelButtons(showDeleteAll = false) {
  controls.show('edit-save');
  controls.show('edit-cancel');
  if (showDeleteAll) {
    controls.show('delete-all');
  }
}

function hideSaveCancelButtons() {
  // Only hide if neither edit nor delete mode is active
  if (!editHandler && !deleteHandler) {
    controls.hide('edit-save');
    controls.hide('edit-cancel');
    controls.hide('delete-all');
  }
}

function deleteAllShapes() {
  // Clear all layers and exit delete mode
  drawnItems.clearLayers();
  deletedLayers = [];

  if (deleteHandler) {
    deleteHandler.disable();
    deleteHandler = null;
  }
  setButtonState('delete-layers', false);

  controls.hide('edit-save');
  controls.hide('edit-cancel');
  controls.hide('delete-all');
}

function saveEditDelete() {
  if (editHandler) {
    editHandler.save();
    editHandler.disable();
    editHandler = null;
    originalLayerStates.clear();
    setButtonState('edit-layers', false);
  }
  if (deleteHandler) {
    deleteHandler.save();
    deleteHandler.disable();
    deleteHandler = null;
    deletedLayers = []; // Clear - deletions are confirmed
    setButtonState('delete-layers', false);
  }
  controls.hide('edit-save');
  controls.hide('edit-cancel');
}

function cancelEditDelete() {
  if (editHandler) {
    // Disable first to exit edit mode
    editHandler.disable();
    editHandler = null;

    // Restore original layer states
    drawnItems.eachLayer(layer => {
      const original = originalLayerStates.get(layer._leaflet_id);
      if (original) {
        if (original.type === 'circle') {
          layer.setLatLng(original.latlng);
          layer.setRadius(original.radius);
        } else if (original.type === 'rectangle') {
          layer.setBounds(original.bounds);
        }
      }
    });
    originalLayerStates.clear();
    setButtonState('edit-layers', false);
  }
  if (deleteHandler) {
    // Disable the handler
    deleteHandler.disable();
    deleteHandler = null;

    // Restore any layers that were removed during delete mode
    deletedLayers.forEach(layer => {
      if (!drawnItems.hasLayer(layer)) {
        drawnItems.addLayer(layer);
      }
    });
    deletedLayers = [];
    setButtonState('delete-layers', false);
  }
  controls.hide('edit-save');
  controls.hide('edit-cancel');
}

function disableAllDrawModes() {
  if (activeDrawMode === 'rectangle') drawRectangle.disable();
  if (activeDrawMode === 'circle') drawCircle.disable();
  activeDrawMode = null;
  updateDrawButtonStates();
}

function disableEditDeleteModes() {
  if (editHandler) {
    editHandler.disable();
    editHandler = null;
    setButtonState('edit-layers', false);
  }
  if (deleteHandler) {
    deleteHandler.disable();
    deleteHandler = null;
    setButtonState('delete-layers', false);
  }
  hideSaveCancelButtons();
}

// Called when last shape is removed - exit any active edit/delete mode
function exitEditDeleteModeIfNoShapes() {
  if (editHandler) {
    editHandler.disable();
    editHandler = null;
    setButtonState('edit-layers', false);
    originalLayerStates.clear();
  }
  if (deleteHandler) {
    deleteHandler.disable();
    deleteHandler = null;
    setButtonState('delete-layers', false);
    deletedLayers = [];
  }
  controls.hide('edit-save');
  controls.hide('edit-cancel');
  controls.hide('delete-all');
}

function updateDrawButtonStates() {
  const rectBtn = controls.getElement('draw-rect');
  const circleBtn = controls.getElement('draw-circle');

  if (rectBtn) {
    rectBtn.classList.toggle('on', activeDrawMode === 'rectangle');
    rectBtn.classList.toggle('off', activeDrawMode !== 'rectangle');
  }
  if (circleBtn) {
    circleBtn.classList.toggle('on', activeDrawMode === 'circle');
    circleBtn.classList.toggle('off', activeDrawMode !== 'circle');
  }
}

function updateShapeButtonStates() {
  const hasShapes = drawnItems.getLayers().length > 0;

  ['select-add-shape', 'select-remove-shape', 'select-clear-shape'].forEach(id => {
    const btn = controls.getElement(id);
    if (btn) {
      btn.classList.toggle('disabled', !hasShapes);
      // When enabled, button should be white (off), not blue
      btn.classList.toggle('off', hasShapes);
      btn.classList.remove('on'); // Never blue for these action buttons
      btn.style.pointerEvents = hasShapes ? 'auto' : 'none';
    }
  });
}

function clearAllShapes() {
  disableEditDeleteModes();
  drawnItems.clearLayers();
}

// -------------------------------------
// GPX Download
// -------------------------------------

function downloadGPX() {
  const selectedMarkers = getAllMarkers().filter(m => m.options.isSelected);
  if (selectedMarkers.length === 0) return showToast('No caches selected');

  // Extract cache data from markers for GPX generation.
  // marker.options contains all GPX-relevant fields (name, geocacheType,
  // geocacheSize, foundDate, pcn, correctedCoordinates, etc.) stored by createMarker().
  const caches = selectedMarkers.map(marker => {
    const { lat, lng } = marker.getLatLng();
    return {
      ...marker.options,
      lat,
      lon: lng,
    };
  });

  const gpxContent = generateWaypointGPX(caches);
  if (!gpxContent) return showToast('No valid caches to export');

  downloadFile(gpxContent, 'selected_caches.gpx', 'application/gpx+xml');
}

// -------------------------------------
// TXT Download
// -------------------------------------

function downloadTXT() {
  const txtContent = smGenerateTXT();
  if (!txtContent.length) return showToast('No caches selected');

  downloadFile(txtContent.join('\n'), 'selected_caches.txt', 'text/plain');
}

function smGenerateTXT() {
  const selectedMarkers = getAllMarkers().filter(m => m.options.isSelected);

  if (selectedMarkers.length === 0) return '';

  const codes = [];
  selectedMarkers.forEach(marker => {
    const code = marker.options.referenceCode || 'Unknown';
    codes.push(code);
  });

  return codes;
}

// -------------------------------------
// Selection Mode Logic
// -------------------------------------

function enableSelectMode() {
  bindMarkersForSelection();
}

function disableSelectMode() {
  smDeselectAllMarkers();
  bindMarkersForNormalClick();
}

// -------------------------------------
// Marker Binding
// -------------------------------------

function getAllMarkers() {
  // All registries are Map objects, use .values()
  return [
    ...(state.staticFoundRegistry?.values() || []),
    ...(state.staticUnfoundRegistry?.values() || []),
    ...(state.liveRegistry?.values() || [])
  ];
}

function getAllVisibleMarkers() {
  return getAllMarkers().filter(m =>
    state.mapRoot.hasLayer(m)
  );
}

function bindMarkersForSelection() {
  getAllVisibleMarkers().forEach(marker => bindSelectionClick(marker));
}

function bindSelectionClick(marker) {
  marker.off('click');
  marker.on('click', (e) => {
    L.DomEvent.stopPropagation(e);
    marker.options.isSelected = !marker.options.isSelected;
    marker.setIcon(getIcon(marker.options));
    smUpdateCountDisplay();
  });
}

function bindMarkersForNormalClick() {
  getAllMarkers().forEach(marker => bindNormalClick(marker));
}

function bindNormalClick(marker) {
  marker.off('click');
  marker.on('click', e => state.handleMarkerClick(e.target, state.stageMarkers));
}

// -------------------------------------
// Count Display
// -------------------------------------

function smUpdateCountDisplay() {
  const selectedMarkers = getAllMarkers().filter(m => m.options.isSelected);
  const el = document.getElementById('select-count-display');
  if (el) el.innerText = `# Caches: ${selectedMarkers.length}`;
}

// -------------------------------------
// Batch Selection Operations
// -------------------------------------

function smSelectAllVisibleMarkers() {
  const bounds = state.mapRoot.getBounds();

  getAllVisibleMarkers().forEach(m => {
    if (bounds.contains(m.getLatLng()) && !m.options.isSelected) {
      m.options.isSelected = true;
      m.setIcon(getIcon(m.options));
    }
  });

  smUpdateCountDisplay();
}

function smDeselectAllMarkers() {
  getAllMarkers().forEach(m => {
    if (m.options.isSelected) {
      m.options.isSelected = false;
      m.setIcon(getIcon(m.options));
    }
  });
  smUpdateCountDisplay();
}

function smInvertVisibleSelection() {
  const bounds = state.mapRoot.getBounds();
  getAllVisibleMarkers().forEach(m => {
    if (bounds.contains(m.getLatLng())) {
      m.options.isSelected = !m.options.isSelected;
      m.setIcon(getIcon(m.options));
    }
  });
  smUpdateCountDisplay();
}

// -------------------------------------
// Shape-based Selection
// -------------------------------------

function smSelectMarkersInAllShapes() {
  drawnItems.eachLayer(layer => smSelectMarkersInShape(layer));
}

function smDeselectMarkersInAllShapes() {
  drawnItems.eachLayer(layer => smDeselectMarkersInShape(layer));
}

function smSelectMarkersInShape(shape) {
  getAllVisibleMarkers().forEach(m => {
    if (isMarkerInShape(m, shape) && !m.options.isSelected) {
      m.options.isSelected = true;
      m.setIcon(getIcon(m.options));
    }
  });
  smUpdateCountDisplay();
}

function smDeselectMarkersInShape(shape) {
  getAllVisibleMarkers().forEach(m => {
    if (isMarkerInShape(m, shape) && m.options.isSelected) {
      m.options.isSelected = false;
      m.setIcon(getIcon(m.options));
    }
  });
  smUpdateCountDisplay();
}

function isMarkerInShape(marker, shape) {
  const latlng = marker.getLatLng();

  // Circle has a different containment check
  if (shape instanceof L.Circle) {
    const center = shape.getLatLng();
    const radius = shape.getRadius();
    return center.distanceTo(latlng) <= radius;
  }

  // Rectangle and other shapes use bounds
  return shape.getBounds().contains(latlng);
}

// code: language=javascript insertSpaces=true tabSize=2
// vim: ts=2:sw=2:et:ft=javascript
