// --------------------------------------------------------------
// mapRouting.js
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

import { coords2Dm } from './coords.js';
import { showToast, downloadFile } from './helpers.js';
import { generateTrackGPX } from './gpx.js';
import { MapButtonControl } from './mapHelpers.js';


// -----------------------------------------------------------------------------------------------------------------------------
// ---------- Routing Mode ----------
// -----------------------------------------------------------------------------------------------------------------------------
//
// Markers are placed by mouseclick on the map. As soon as more than one marker is on the map, a route can and will be
// calculated by calling a web service at brouter.de. Existing markers can be dragged to new positions in which case a
// new route is calculated. Sometime on may need to bridge a short distance where no path on the map exists, letting brouter
// calculate a route might take an enormous detour, it might not even find a route. In this situation a marker can be placed
// with the CTRL key pressed, which indicates that the previous marker and the new marker should be connected with a
// straight line (visualized by a dotted line). All markers can be dragged those which connect to predecessor and potentially
// successor with straight lines too.
//
// In order to implement this we introduce track segments, represented in the object trackSegments[] each segment is
// an entry in the array of segments. The points (marker's coordinates) in a segment are sent to brouter.de to calculate
// the route. Now when CTRL is pressed and a straight line should be drawn, this is represented by creating a new segment
// and place the marker into that new segment. Subsequent markers go into the new segement. And so on. Now after each
// placement of a new marker or drag operation on an existing one all existing routes are removed from the map, also
// all "connecting lines" between segments and then all routes in all segments and all interconnecting straight lines
// are recalculated. This design point is primarily chosen for simplicity in the implementation, not for performance or
// minimizing the number of calls to brouter. It could be improved as not all routes and lines need to be recalculated after
// each operation. In many cases only the current segment needs to be recalculated and redrawn. A route will always be
// recalculated from the start to the end, no matter how many points are in the segment, this is kind of a brute force
// method but it yields deterministic results without involving complicate programming. A more elaborate approach would be
// to call the router always with exactly two point and let it do its work. More efficient strategies can always be implemented
// later. Since routing a track is a rarely used task, and since routes typically don't consist of more than 30 to 100 
// points it is probably not worth optimizing it.

let state = {};
let markers     = [];
let trackSegments = [];

let routingControl;

export function init(mapState) {
  state = mapState;
  const map = state.mapRoot;

  // --- Create GPX button (action button - always white) ---
  const gpxBtn = document.createElement('button');
  gpxBtn.type = 'button';
  gpxBtn.className = 'map-btn off';
  gpxBtn.innerText = 'GPX';
  gpxBtn.title = 'Download GPX';
  gpxBtn.style.display = 'none';
  gpxBtn.addEventListener('click', e => {
    e.preventDefault();
    e.stopPropagation();
    downloadGPX();
  });

  // --- Create Length display ---
  const lengthDisplay = document.createElement('div');
  lengthDisplay.className = 'length-display';
  lengthDisplay.style.display = 'none';
  lengthDisplay.innerText = '0.00 km';

  // --- Create MapButtonControl row ---
  const controls = new MapButtonControl({
    position: 'topleft',
    rows: [
      {
        id: 'routing',
        buttons: [
          {
            id: 'routing-toggle',
            icon: '⇧',
            title: 'Routing Mode',
            onClick: (_map, ctrl) => {
              if (state.smSelectionModeEnabled) {
                showToast('Please turn off selection mode before activating routing mode');
                return;
              }

              state.rmRoutingModeEnabled = !state.rmRoutingModeEnabled;
              ctrl.setActive('routing-toggle', state.rmRoutingModeEnabled);

              if (state.rmRoutingModeEnabled) {
                updateLengthDisplay(0);
                ctrl.show('routing-gpx');
                ctrl.show('routing-length');
                map.getContainer().style.cursor = 'pointer';
                map.on('click', addMarker);
              } else {
                clearRouting();
                updateLengthDisplay(0);
                ctrl.hide('routing-gpx');
                ctrl.hide('routing-length');
                map.getContainer().style.cursor = '';
                map.off('click', addMarker);
              }
            }
          },

          // GPX button as custom element
          {
            id: 'routing-gpx',
            element: gpxBtn,
            hidden: true,
            toggleVisible: true,
          },

          // Length display as custom element
          {
            id: 'routing-length',
            element: lengthDisplay,
            hidden: true,
          }
        ]
      }
    ]
  });

  // --- Add control to map ---
  controls.addTo(map);

  // --- Helper to update length display dynamically ---
  controls.updateLength = (value) => {
    lengthDisplay.innerText = `${value.toFixed(2)} km`;
  };
}

// ---------------------------------------
// Clear markers, routes, lines, and trackSegments
// Essentially clear al residue from working in
// routing mode enabled.

function clearRouting() {
  markers.forEach(marker => {
    state.mapRoot.removeLayer(marker);
  });
  markers = [];
  clearRoutesAndLines();
  trackSegments = [];
}

// ---------------------------------------
// Iterate all segments and clear all routes and
// all interconnecting lines, and also remove
// all coordinates stored for calculating a
// GPX.

function clearRoutesAndLines() {
  for (let segment of trackSegments) {
    segment.routeLayer?.clearLayers();
    segment.lineLayer?.clearLayers();
    segment.routeCoordinates = [];
  }
}

// ---------------------------------------
// Function to add marker on map click

const addMarker = async (event) => {
  if (!state.rmRoutingModeEnabled) return;

  event.originalEvent.stopPropagation();

  const marker = L.marker(event.latlng, { draggable: true }).addTo(state.mapRoot);
  markers.push(marker);
  const markerIndex = markers.indexOf(marker);
  const isCtrlPressed = event.originalEvent.ctrlKey || event.originalEvent.metaKey;

  // --- Function to generate popup content ---
  const getPopupContent = () => {
    const { lat, lng } = marker.getLatLng();
    const coordsText = coords2Dm(lat, lng);
    const escaped = coordsText.replace(/'/g, "\\'");
    return `<div style="display:flex;align-items:center;gap:6px">
      <input type="text" readonly value="${coordsText}"
        style="min-width:160px;width:auto;border:none;outline:none;background:none;font-size:12px;font-family:monospace;cursor:text" />
      <span title="Copy to clipboard" style="cursor:pointer;font-size:13px;user-select:none"
        onclick="this.textContent='✓';setTimeout(()=>{this.textContent='⧉'},1500);navigator.clipboard?.writeText('${escaped}')">⧉</span>
    </div>`;
  };

  // Track popup open/close state
  let popupOpen = false;

  // Click on marker toggles popup
  marker.on('click', () => {
    if (popupOpen) {
      marker.closePopup();
      popupOpen = false;
    } else {
      // Optional: close all other marker popups
      markers.forEach(m => { if (m !== marker) m.closePopup(); });
      marker.bindPopup(getPopupContent(), { closeOnClick: false }).openPopup();
      popupOpen = true;
    }
  });

  // Drag handler updates routes and popup content if open
  marker.on('dragend', async () => {
    await updateRoutes(markerIndex, true, isCtrlPressed);
    if (popupOpen) {
      marker.setPopupContent(getPopupContent());
    }
  });

  await updateRoutes(markerIndex, false, isCtrlPressed);
};

// ---------------------------------------
// Function to update routes

async function updateRoutes(markerIndex, isDraggedMarker, isCtrlPressed) {
  if (markers.length < 1) return;

  const currentCoords = markers[markerIndex].getLatLng();

  if (trackSegments.length === 0) {
    const newSegment = {
      points: [{ latlng: currentCoords, markerIndex}],
      routeLayer: L.layerGroup().addTo(state.mapRoot),
      routeLength: 0,
      lineLayer: L.layerGroup().addTo(state.mapRoot),
      lineLength: 0,
      routeCoordinates: [], // to generate a GPX
    };
    trackSegments.push(newSegment);
    return; // Exit the function after adding the first segment
  }

  // being here means we have more than one marker. All we need to do is
  // figuring out where to add that marker. If we're dragging, we just need to
  // modify an existing marker's point coordinates. If CTRL is pressed we just
  // need to open up a new segment.

  let affectedSegmentIndex;
  if (isDraggedMarker) {
    affectedSegmentIndex = trackSegments.findIndex(segment => segment.points.some(point => point.markerIndex === markerIndex));
  } else {
    affectedSegmentIndex = trackSegments.length - 1;
    if (isCtrlPressed) {
      const newSegment = {
        points: [{ latlng: currentCoords, markerIndex}],
        routeLayer: L.layerGroup().addTo(state.mapRoot),
        routeLength: 0,
        lineLayer: L.layerGroup().addTo(state.mapRoot),
        lineLength: 0,
        routeCoordinates: [],
      };
      trackSegments.push(newSegment);
      affectedSegmentIndex++;
    }
  }

  const currentSegment     = trackSegments[affectedSegmentIndex];
  const existingPointIndex = currentSegment.points.findIndex(point => point.markerIndex === markerIndex);

  if (existingPointIndex !== -1) {
    currentSegment.points[existingPointIndex].latlng = currentCoords;
  } else {
    currentSegment.points.push({ latlng: currentCoords, markerIndex, isCtrlPressed });
  }
  createRoutes();
}

// ---------------------------------------

async function createRoutes() {
  clearRoutesAndLines(); // Clear existing routes and lines
  let totalLength = 0;     // Initialize total length

  for (let i = 0; i < trackSegments.length; i++) {
    const segment = trackSegments[i];

    if (segment.points.length > 1) {
      const segmentLength = await rmAddRoute(i); // Assuming rmAddRoute returns the route length
      totalLength += segmentLength;
    }

    if (i < trackSegments.length - 1) {
      const nextSegment = trackSegments[i + 1];

      if (segment.points.length && nextSegment.points.length) {
        const lastPointCurrent = segment.points[segment.points.length - 1];
        const firstPointNext = nextSegment.points[0];

        L.polyline([lastPointCurrent.latlng, firstPointNext.latlng], { dashArray: '5, 5', color: 'darkGreen', weight: 3 }).addTo(segment.lineLayer);

        const {lat, lng} = lastPointCurrent.latlng;
        const connectingPoint = await fetchElevation(lat, lng);
        if (connectingPoint) {
          segment.routeCoordinates.push(connectingPoint);
        } else {
          segment.routeCoordinates.push({ lat, lng, ele: 300 }); // elevation of 300m is arbitrarily chosen
        }

        totalLength += lastPointCurrent.latlng.distanceTo(firstPointNext.latlng);
      }
    }
  }

  updateLengthDisplay(totalLength);
}

// ----------------------------------------------------------------------------------
// Helper function to fetch elevation data
// We just use brouter and call it with two identical points and asking for
// a route, which will obviously be a zero route or a "point route" and
// for that point we do get the elevation and that is all we want. Ok,
// this is a brute force method, perhaps there would be an API endpoint
// accepting a single point and returning the elevation.

async function fetchElevation(lat, lng) {
  try {
    const response = await fetch(`https://brouter.de/brouter?lonlats=${lng},${lat}|${lng},${lat}&profile=hiking-mountain&alternativeidx=0&format=geojson`);
    const rmRouteData = await response.json();
    return {
      lat: rmRouteData.features[0].geometry.coordinates[0][1],
      lng: rmRouteData.features[0].geometry.coordinates[0][0],
      ele: rmRouteData.features[0].geometry.coordinates[0][2]
    };
  } catch (error) {
    console.log("Failed to fetch elevation data:", error);
    return null;
  }
}

// ----------------------------------------------------------------------------------
// Function to add a route between a list of coordinates.

async function rmAddRoute(affectedSegmentIndex) {
  const currentSegment = trackSegments[affectedSegmentIndex];
  currentSegment.routeCoordinates = [];
  currentSegment.routeLength = 0; // Reset segment length for the affected segment
  const coords = currentSegment.points.map(point => `${point.latlng.lng},${point.latlng.lat}`).join('|'); // Adjust to use latlng

  const response = await fetch(`https://brouter.de/brouter?lonlats=${coords}&profile=hiking-mountain&alternativeidx=0&format=geojson`);

  if (!response.ok) return console.log("Error fetching route:", response.statusText);

  const rmRouteData = await response.json();

  if (rmRouteData.features.length > 0) {
    const rmRoute = L.geoJSON(rmRouteData, {
      style: { color: 'darkGreen', weight: 3 }
    }).addTo(currentSegment.routeLayer);

    rmRouteData.features.forEach(feature => {
      currentSegment.routeLength += parseInt(feature.properties['track-length'] || 0, 10);
      if (feature.geometry && feature.geometry.type === 'LineString') {
        feature.geometry.coordinates.forEach(coord => {
          const lat = coord[1];
          const lng = coord[0];
          const ele = coord.length > 2 ? coord[2] : undefined; // Use undefined if elevation is not provided

          currentSegment.routeCoordinates.push({ lat: lat, lng: lng, ele: ele });
        });
      }
    });
  }
  return currentSegment.routeLength;
}

// ----------------------------------------------------------------------------------
// Function to update the route length display

function updateLengthDisplay(length) {
  const lengthDisplayElement = document.querySelector('.length-display');

  if (typeof length === 'number' && !isNaN(length)) {
    lengthDisplayElement.innerText = `Total Length: ${(length/1000).toFixed(2)} km`;
  } else {
    console.log("Invalid length value:", length); // Log invalid length
    lengthDisplayElement.innerText = `Total Length: N/A`; // Handle invalid length
  }
}

// ----------------------------------------------------------------------------------
// Function to download GPX with the route track

function downloadGPX() {
  const gpxData = generateTrackGPX(trackSegments, 'opencaching.de Track');
  if (!gpxData) return;
  downloadFile(gpxData, 'track.gpx', 'application/gpx+xml');
}

// code: language=javascript insertSpaces=true tabSize=2
// vim: ts=2:sw=2:et:ft=javascript
