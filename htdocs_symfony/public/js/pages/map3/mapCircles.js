// --------------------------------------------------------------
// mapCircles.js
//
// © 2025 hxdimpf Research
//
// Part of OCmap for opencaching.de. MIT License.
// --------------------------------------------------------------

import { MapButtonControl } from './mapHelpers.js';
import { refreshRoutingCircles } from './mapRouting.js';

let state;                      // global map state
let map;
let controls;                   // reference to MapButtonControl
const circleZoomThreshold = 12; // minimum zoom to show circles
let enabled = false;            // module internal toggle state
let largeRadius = false;        // false = 160m, true = 3200m

// ----------------------------------------------------------------
// Initialization
// ----------------------------------------------------------------
export function init(mapState) {
  state = mapState;
  map = state.mapRoot;
  state.circleRadius = 160;     // default radius

  initControls();
}

// ----------------------------------------------------------------
// Control button setup
// ----------------------------------------------------------------
function initControls() {
  controls = new MapButtonControl({
    position: 'topleft',
    rows: [
      {
        id: 'circles',
        buttons: [
          {
            id: 'circle-toggle',
            icon: '⭕',
            title: 'Toggle 160m circle',
            onClick: (_map, ctrl) => toggleCircles(ctrl)
          },
          {
            id: 'circle-radius',
            icon: '▽',
            title: 'Toggle radius: 160m / 3200m',
            onClick: (_map, ctrl) => toggleRadius(ctrl),
            hidden: true
          }
        ]
      }
    ]
  });

  controls.addTo(map);

  // Update circles on zoom changes
  map.on('zoomend', () => handleCircles());
}

// ----------------------------------------------------------------
// Toggle button logic
// ----------------------------------------------------------------
export function toggleCircles(ctrl) {
  const btnId = 'circle-toggle';
  const btn = ctrl._buttons.get(btnId);
  if (!btn) return;

  enabled = !enabled;

  btn.classList.toggle('on', enabled);
  btn.classList.toggle('off', !enabled);

  // Show/hide radius toggle button
  const radiusBtn = ctrl._buttons.get('circle-radius');
  if (radiusBtn) {
    radiusBtn.style.display = enabled ? 'inline-flex' : 'none';
  }

  handleCircles();
}

// ----------------------------------------------------------------
// Toggle radius between 160m and 3200m
// ----------------------------------------------------------------
function toggleRadius(ctrl) {
  const btn = ctrl._buttons.get('circle-radius');
  if (!btn) return;

  largeRadius = !largeRadius;
  state.circleRadius = largeRadius ? 3200 : 160;

  // Update button icon: △ for large (up), ▽ for small (down)
  btn.innerHTML = largeRadius ? '△' : '▽';
  btn.title = largeRadius ? 'Radius: 3200m (click for 160m)' : 'Radius: 160m (click for 3200m)';

  // Refresh circles with new radius
  forceRefreshCircles();
}

// ----------------------------------------------------------------
// Safe circle refresh
// ----------------------------------------------------------------
function handleCircles() {
  const zoom = map.getZoom();
  const shouldEnable = enabled && zoom >= circleZoomThreshold;

  // Only refresh if the state actually changes
  if (state.circlesEnabled === shouldEnable) return;

  state.circlesEnabled = shouldEnable;

  console.log("mapCircles: circlesEnabled =", state.circlesEnabled);

  // Refresh both static and live markers
  if (typeof state.refreshStaticMarkers === 'function') state.refreshStaticMarkers();
  if (typeof state.refreshLiveMarkers   === 'function') state.refreshLiveMarkers();

  // Refresh routing marker circles
  refreshRoutingCircles();
}

// ----------------------------------------------------------------
// Force refresh circles (when radius changes)
// ----------------------------------------------------------------
function forceRefreshCircles() {
  if (!state.circlesEnabled) return;

  console.log("mapCircles: radius changed to", state.circleRadius);

  // Update radii on existing cached circles (static + live)
  if (typeof state.updateCircleRadii === 'function') state.updateCircleRadii();

  // Refresh routing marker circles (these are recreated, not cached)
  refreshRoutingCircles();
}

