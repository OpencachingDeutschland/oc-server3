
import { getTrackById } from './mapApi.js';

let state = {};

const getById = id => document.getElementById(id);

//-------------------------
// renderTrackIds()
//
// Render an array of GPX tracks on the map based on their ids in the mongodb
// This function operates cummulative which means, tracks that are already on
// the map aren't processed again.

const processedTrackIds = new Set(); // cache those we already processed

export async function renderTrackIds(ids) {
  getById('tracksButton').classList.add('blink-gold');
  for (const id of ids) {
    try {
      if (processedTrackIds.has(id)) {
        continue; // ... as this one is already on the map
      } else {
        const data = await getTrackById(id);
        if (data) {
          renderTrack(data.gpx, id, data.description);
          processedTrackIds.add(id); // Update the cache
        }
      }
      
      await new Promise(resolve => setTimeout(resolve, 100)); // 100ms delay
      
    } catch (error) {
      console.log(`Error processing track ${id}:`, error);
    }
  }
  getById('tracksButton').classList.remove('blink-gold');
  getById('tracksButton').style.backgroundColor = 'green';
}

//-------------------------
// renderTrack()
//
// Render a single track based on a given XML string.
//
// Design considerations:
// - We want the line to turn red on mouseover.
// - We want to keep it red on mouseout.
// - We want the line to turn red on clicking the line and simultaneously open the popup assigned to the Start marker
//   (as it does when the Start marker is clicked).
// - The only way to turn all lines back to blue is to click anywhere outside of any tracks on the map.
//

let polylineClicked = false;

async function renderTrack(gpx, trackId, description) {
  let tooltipContent;
  let popupContent;
  let startMarkerPopup = null;
  const diameter = 20;
  const radius = diameter / 2;
  const svg = `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${diameter} ${diameter}" width="${diameter}" height="${diameter}">
      <circle cx="${radius}" cy="${radius}" r="${radius}" fill="green" />
      <circle cx="${radius}" cy="${radius}" r="${radius/2}" fill="yellow" />
    </svg>`;
  const gpxLayer = new L.GPX(gpx, {
    gpx_options: { joinTrackSegments: false },
    markers: {
      startIcon: `data:image/svg+xml;base64,${btoa(svg)}`,
      endIcon:   `data:image/svg+xml;base64,${btoa('<svg xmlns="http://www.w3.org/2000/svg" width="1" height="1"></svg>')}`
    },
    marker_options: {
      iconSize:   [diameter, diameter], // Size of the icon
      iconAnchor: [radius, radius] // Anchor the icon at its center
    },
    async: true
  });

  //-------------------------
  // addline
  //
  gpxLayer.on('addline', function(e) {
    const polyline = e.line;

    polyline.on('mouseover', function() {
      this.setStyle({ color: 'red' });
    });

    polyline.on('mouseout', function() {
      //this.setStyle({ color: 'blue' }); // just leave it on for now
    });

    polyline.on('click', function(e) {
      polylineClicked = true;
      if (startMarkerPopup) {
        startMarkerPopup.setLatLng(e.latlng).openOn(state.mapRoot);
      }
      this.setStyle({ color: 'red' });
    });
  });

  //-------------------------
  // addpoint
  //
  gpxLayer.on('addpoint', function(e) {
    const { point_type, point } = e;

    if (point_type === 'start'|| point_type === 'end') {
      let distanceInKm = (gpxLayer.get_distance() / 1000).toFixed(2);
      let duration     = gpxLayer.get_duration_string(gpxLayer.get_total_time());
      tooltipContent = `Tour: ${trackId}<br>${distanceInKm} km, ${duration}`;
      popupContent   = `View Tour: <a href="logging?fn=${trackId}">${trackId}</a><br>${distanceInKm} km, ${duration}`;
      if (description)
        popupContent += ` <a href="#" title="${description}" class="pcn-tooltip">description</a>`;

      point.bindTooltip(tooltipContent);
      point.bindPopup(popupContent);

      if (point._popup) {
        startMarkerPopup = point._popup;
      }

      point.on('click', function () {
        gpxLayer.setStyle({ color: 'red' }); // Ensure this does not interfere
      });

      point.on('popupclose', function() {
        //gpxLayer.setStyle({ color: 'blue' }); // this works, but we want to keep it red on popup close
      });
    }
  });

  //-------------------------
  // error
  //
  gpxLayer.on('error', function(e) {
    console.err('Error: ' + e.err);
  });

  gpxLayer.addTo(state.mapRoot);
}

//-------------------------
// Reset all GPX track colors to blue

export function resetTrackColors() {
  if (polylineClicked) return;

  polylineClicked = false;
  state.mapRoot.eachLayer(function(layer) {
    if (L.GPX && layer instanceof L.GPX) {
      layer.setStyle({ color : 'blue'});
    }
  });
}

export function init(mapState){
  state = mapState;
}
