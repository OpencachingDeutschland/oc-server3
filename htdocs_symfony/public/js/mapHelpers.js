// --------------------------------------------------------------
// mapHelpers.js
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

/**
 * MapButtonControl
 *
 * A flexible, multi-row Leaflet control for buttons.
 * Each row can contain multiple buttons or custom elements.
 * Supports active, inactive, warning, and disabled states, as well as hiding/showing individual buttons.
 *
 * Key Features:
 * -------------
 * - Multiple rows of buttons with unique IDs.
 * - Each button can have a custom icon, text, title (tooltip), click handler, and visibility state.
 * - Methods to dynamically add rows and buttons.
 * - Methods to set or clear active state for individual buttons.
 * - Access to the underlying DOM element for each button via getElement(id).
 * - Designed to integrate seamlessly with map-related actions (e.g., routing, circle management).
 *
 * CSS Dependencies:
 * -----------------
 * .map-button-control   -> container for all button rows
 * .map-button-row       -> wrapper for each row of buttons
 * .map-btn              -> base button styling
 * .map-btn.on           -> active state
 * .map-btn.off          -> inactive state
 * .map-btn.warn         -> warning state
 * .map-btn.disabled     -> disabled state
 *
 * Notes / Best Practices:
 * -----------------------
 * - Modifying these CSS classes may break layout, visibility, hover effects, or colors.
 * - Tooltips are generated via the 'title' attribute of buttons.
 * - Buttons added dynamically via addButton() or addRow() will automatically integrate with the control.
 * - Avoid directly manipulating DOM elements outside provided methods unless necessary.
 * - Designed to co-exist with other Leaflet controls (zoom, layers, draw toolbar, etc.).
 *
 * Example Usage:
 * --------------
 * const controls = new MapButtonControl({
 *   position: 'topleft',
 *   rows: [
 *     { id: 'routing', buttons: [
 *         { id: 'routing-toggle', icon: '⇧', title: 'Routing Mode', onClick: toggleRouting },
 *         { id: 'routing-gpx', element: gpxBtn, hidden: true },
 *     ]}
 *   ]
 * });
 * controls.addTo(map);
 *
 * @see mapRouting.init()
 */

export class MapButtonControl extends L.Control {
  constructor(options = {}) {
    super(options);

    // rows: { id?, buttons: [ { id, icon?, title?, onClick?, element?, hidden? } ] }
    this.rows = options.rows || [];
    this._buttons = new Map();       // id -> element
    this._rowElements = new Map();   // rowId -> DOM element
  }

  onAdd(map) {
    this._map = map;

    const container = L.DomUtil.create(
      'div',
      'leaflet-control map-button-control'
    );
    this._container = container;

    L.DomEvent.disableClickPropagation(container);
    L.DomEvent.disableScrollPropagation(container);

    // initialize rows
    this.rows.forEach((row, index) => {
      const rowId = row.id ?? index;
      this._createRow(rowId, row.buttons || []);
    });

    return container;
  }

  _createRow(rowId, buttons = []) {
    if (!buttons.length) return;

    // IMPORTANT: no 'leaflet-bar' here – we fully own layout
    const rowEl = L.DomUtil.create(
      'div',
      'map-button-row',
      this._container
    );

    this._rowElements.set(rowId, rowEl);

    buttons.forEach(def => this._createButtonOrElement(rowEl, def));
  }

  _createButtonOrElement(rowEl, def) {
    if (!def || !def.id) return;

    let el;

    if (def.element) {
      // Use the provided custom element
      el = def.element;

      // Initially hidden?
      if (def.hidden) el.style.display = 'none';

      // Optional: allow automatic show/hide when setActive is called
      if (def.toggleVisible) el.dataset.toggleVisible = true;
    } else {
      // Create a standard button (using <button> to avoid scroll jumps from href="#")
      el = L.DomUtil.create('button', 'map-btn', rowEl);
      el.type = 'button';
      el.innerText = def.icon || '';
      el.title = def.title || '';

      if (def.onClick) {
        el.addEventListener('click', e => {
          e.preventDefault();
          e.stopPropagation();
          def.onClick?.(this._map, this);
        });
      }

      // Hide initially if requested
      if (def.hidden) el.style.display = 'none';

      // Optional: allow automatic toggle
      if (def.toggleVisible) el.dataset.toggleVisible = true;
    }

    // Store reference
    this._buttons.set(def.id, el);

    // Attach to DOM
    rowEl.appendChild(el);
  }

  addRow(rowDef) {
    const rowId = rowDef.id ?? this._rowElements.size;
    this._createRow(rowId, rowDef.buttons || []);
  }

  addButton(rowId, btnDef) {
    const rowEl = this._rowElements.get(rowId);
    if (!rowEl) {
      console.warn(`Row "${rowId}" not found`);
      return;
    }
    this._createButtonOrElement(rowEl, btnDef);
  }

  // --------------------------------------------------
  // State handling (visual only)
  // --------------------------------------------------
  setActive(id, active = true) {
    const el = this._buttons.get(id);
    if (!el) return;

    // toggle classes for buttons
    if (el.classList.contains('map-btn')) {
      el.classList.toggle('on', active);
      el.classList.toggle('off', !active);
    }

    // show/hide only if element is marked as toggleable
    if (!el.classList.contains('map-btn') || el.dataset.toggleVisible) {
      el.style.display = active ? 'flex' : 'none';
    }
  }

  clearActive() {
    this._buttons.forEach(el =>
      el.classList.remove('on', 'off')
    );
  }

  // --------------------------------------------------
  // Visibility helpers (explicit, safe)
  // --------------------------------------------------
  show(id) {
    const el = this._buttons.get(id);
    if (el) el.style.display = '';
  }

  hide(id) {
    const el = this._buttons.get(id);
    if (el) el.style.display = 'none';
  }

  // --------------------------------------------------
  // Access
  // --------------------------------------------------
  getElement(id) {
    return this._buttons.get(id);
  }
}


// code: language=javascript insertSpaces=true tabSize=2
// vim: ts=2:sw=2:et:ft=javascript
