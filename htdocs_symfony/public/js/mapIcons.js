// --------------------------------------------------------------
// mapIcons.js
//
// Consolidated icon generation for maps and tables.
// Contains cache type definitions, badge definitions, and all icon generators.
//
// © 2025 hxdimpf Research
// Licensed under the MIT License.
// --------------------------------------------------------------

// --------------------------------------------------------------------------
// Cache type definitions: id -> { name, col (fill color), text (label) }
// Icon paths are merged from iconPaths.js (auto-generated from cgeo drawables)
// --------------------------------------------------------------------------

import { iconPaths } from './iconPaths.js';

export const UNKNOWN_TYPE_ID = 0;

export const cacheTypes = {
  [UNKNOWN_TYPE_ID] : { name: "Unknown",             col: "silver"      , text: "??" },
  // OC type IDs 1-10
  1    : { name: "Unknown",             col: "silver"      , text: "??" },
  2    : { name: "Traditional",         col: "green"       , text: "T"  },
  3    : { name: "Multi",               col: "orange"      , text: "M"  },
  4    : { name: "Virtual",             col: "darkBlue"    , text: "V"  },
  5    : { name: "Webcam",              col: "blue"        , text: "W"  },
  6    : { name: "Event",               col: "darkRed"     , text: "EV" },
  7    : { name: "Quiz",                col: "darkBlue"    , text: "Q"  },
  8    : { name: "Math/Physics",        col: "darkBlue"    , text: "MP" },
  9    : { name: "Moving",              col: "green"       , text: "MV" },
  10   : { name: "Drive-in",            col: "darkGreen"   , text: "DI" },
};

// --------------------------------------------------------------------------
// Waypoint type definitions: OC `coordinates.subtype` (1-5) → legacy PNG.
// Mirrors the legacy `coordinates_type` table and renders the same icons
// the opencaching.de platform shows on map2.php (icon set 2 / caches2).
// --------------------------------------------------------------------------

export const waypointTypes = {
  1 : { name: "Parking",                  png: "wp_parking.png"   },
  2 : { name: "Stage or reference point", png: "wp_reference.png" },
  3 : { name: "Path",                     png: "wp_path.png"      },
  4 : { name: "Final",                    png: "wp_final.png"     },
  5 : { name: "Point of interest",        png: "wp_poi.png"       },
};

// Merge icon path data into cache types.
// GC-conflicting IDs have been removed from iconPaths so no guard is needed.
// Type 5 (Webcam) has no direct iconPaths entry — it reuses the camera-lens
// icon stored under ID 11.
for (const [id, icon] of Object.entries(iconPaths)) {
  if (cacheTypes[id]) cacheTypes[id].icon = icon;
}
if (iconPaths[11]) cacheTypes[5].icon = iconPaths[11]; // OC Webcam uses camera-lens icon

/**
 * Get cache type definition with fallback to unknown type.
 */
export function getCacheType(typeId) {
  return cacheTypes[typeId] ?? cacheTypes[UNKNOWN_TYPE_ID];
}

// Cached Leaflet icons per waypoint subtype.
const waypointIcons = {};

/**
 * Leaflet icon for a child waypoint (parking, stage, final, path, POI).
 * Source PNGs are 32x32 (legacy caches2 icon set); rendered ~1.3x for visibility.
 * Anchor is scaled from legacy (13, 24).
 */
export function getWaypointIcon(subtype) {
  if (typeof L === 'undefined') return null;
  const wt = waypointTypes[subtype];
  if (!wt) return null;
  if (waypointIcons[subtype]) return waypointIcons[subtype];
  waypointIcons[subtype] = L.icon({
    iconUrl:       `/images/waypoints/${wt.png}`,
    iconSize:      [42, 42],
    iconAnchor:    [17, 31],
    popupAnchor:   [0, -31],
    tooltipAnchor: [-17, -16],
  });
  return waypointIcons[subtype];
}

// --------------------------------------------------------------------------
// Badge definitions for status indicators
// Position: ur=upper-right, ul=upper-left, br=bottom-right, bl=bottom-left
// --------------------------------------------------------------------------

export const badgeTypes = [
  { key: 'isPartial',   pos: 'ur', col: 'blue',      text: 'P',  textCol: 'white' },
  { key: 'isGuessable', pos: 'ur', col: 'lime',      text: 'G',  textCol: 'black' },
  { key: 'isOwned',     pos: 'ur', col: 'crimson',   text: 'O',  textCol: 'white' },
  { key: 'isDNF',       pos: 'ur', col: 'lightBlue', text: 'D',  textCol: 'black' },
  { key: 'isFound',     pos: 'ur', col: 'gold',      text: 'F',  textCol: 'black' },
  { key: 'isOcOnly',    pos: 'ul', col: 'royalblue', text: 'OC', textCol: 'white' },
  { key: 'isCached',    pos: 'ul', col: 'white',     text: 'S',  textCol: 'black' },
  { key: 'hasFav',      pos: 'ul', col: 'crimson',   text: '♥',  textCol: 'white' },
  { key: 'hasCC',       pos: 'br', col: 'green',     text: '✓',  textCol: 'white' },
  { key: 'hasPCN',      pos: 'bl', col: 'crimson',   text: 'CN', textCol: 'white' },
];

// --------------------------------------------------------------------------
// Icon caches
// --------------------------------------------------------------------------

const tableIconCache = {};

//-------------------------
// --- icons

const gpsIconSvg = `
<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
  <circle cx="15" cy="15" r="14" fill="#4285f4" opacity="0.15"/>
  <circle cx="15" cy="15" r="7" fill="white"/>
  <circle cx="15" cy="15" r="5.5" fill="#4285f4"/>
</svg>
`;

// Leaflet-dependent objects — only created when Leaflet is available (map pages)
export const gpsIcon = typeof L !== 'undefined' ? L.icon({
  iconUrl: 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(gpsIconSvg),
  iconSize: [30, 30],
  iconAnchor: [15, 15],
  tooltipAnchor: [15, 0],
}) : null;

const liveIcon = typeof L !== 'undefined' ? L.Icon.extend({
  options: {
    iconAnchor:    [ 18,  39],
    popupAnchor:   [  0, -40],
    tooltipAnchor: [-18, -20]
  }
}) : null;

// --------------------------------------------------------------------------
// Icon path renderer — renders cgeo VectorDrawable paths as nested SVG
// --------------------------------------------------------------------------

/**
 * Render icon paths as a nested <svg> element with viewBox scaling.
 * @param {Object} icon - Icon definition with { paths, vb? }
 * @param {number} x - Top-left x position
 * @param {number} y - Top-left y position
 * @param {number} size - Width and height of the target area
 * @returns {string} SVG markup
 */
function renderIconPaths(icon, x, y, size) {
  const vb = icon.vb || 36;
  const zoom = icon.z || 1.4;
  const inset = vb * (1 - 1 / zoom) / 2;
  const vbSize = vb / zoom;
  let svg = `<svg x="${x}" y="${y}" width="${size}" height="${size}" viewBox="${inset} ${inset} ${vbSize} ${vbSize}">`;
  for (const p of icon.paths) {
    if (p.f) {
      svg += `<path d="${p.d}" fill="white"/>`;
    } else {
      svg += `<path d="${p.d}" fill="none" stroke="white" stroke-width="${p.s}"${p.cap ? ` stroke-linecap="${p.cap}"` : ''}/>`;
    }
  }
  svg += '</svg>';
  return svg;
}

// --------------------------------------------------------------------------
// Table Icon Generator (non-map icons without pins)
// Used for Tabulator tables, explore page, etc.
// --------------------------------------------------------------------------

/**
 * Build a hash string for table icon cache lookup
 */
function buildTableIconHash(typeId, state, size) {
  let hash = `tbl_${typeId}_${size}`;
  if (state.isDisabled) hash += '_dis';
  if (state.isArchived) hash += '_arch';
  for (const badge of badgeTypes) {
    if (state[badge.key]) hash += `_${badge.key}`;
  }
  return hash;
}

/**
 * Generate an SVG icon for tables (no pin pointer).
 * @param {number} typeId - Cache type ID
 * @param {Object} state - State flags: { isFound, isDNF, isDisabled, isArchived, ... }
 * @param {number} size - Icon size in pixels (default 24)
 * @returns {string} SVG markup
 */
function generateTableIconSvg(typeId, state = {}, size = 24) {
  let { col, text } = getCacheType(typeId);

  // Override color for disabled/archived
  if (state.isArchived) col = 'lightPink';
  else if (state.isDisabled) col = 'darkGray';

  const R = size / 2;
  const badgeR = R * 0.4;
  const fontSize = text.length > 1 ? R * 0.7 : R * 0.9;

  // Expand canvas to fit badges
  const padding = badgeR;
  const svgSize = size + padding * 2;
  const cx = svgSize / 2;
  const cy = svgSize / 2;

  let svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${svgSize}" height="${svgSize}" viewBox="0 0 ${svgSize} ${svgSize}">`;

  // Rounded-square icon, scaled by 0.89 for equal perceived size vs circle icons.
  const S = Math.floor(0.89 * R);
  const rx = S * 0.2;
  svg += `<rect x="${cx-S+1}" y="${cy-S+1}" width="${2*(S-1)}" height="${2*(S-1)}" rx="${rx}" fill="black"/>`;
  svg += `<rect x="${cx-S+2}" y="${cy-S+2}" width="${2*(S-2)}" height="${2*(S-2)}" rx="${rx}" fill="white"/>`;
  svg += `<rect x="${cx-S+3}" y="${cy-S+3}" width="${2*(S-3)}" height="${2*(S-3)}" rx="${rx}" fill="${col}"/>`;

  // Type label (icon paths or text fallback)
  const { icon } = getCacheType(typeId);
  if (icon) {
    const iconSize = 2 * (R - 3);
    svg += renderIconPaths(icon, cx - iconSize / 2, cy - iconSize / 2, iconSize);
  } else {
    svg += `<text x="${cx}" y="${cy}" font-size="${fontSize}" font-family="Arial" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="central">${text}</text>`;
  }

  // Badge positions relative to center
  const badgeOffset = R * 0.7;
  const positions = {
    ur: { x: cx + badgeOffset, y: cy - badgeOffset },
    ul: { x: cx - badgeOffset, y: cy - badgeOffset },
    br: { x: cx + badgeOffset, y: cy + badgeOffset },
    bl: { x: cx - badgeOffset, y: cy + badgeOffset },
  };

  // Add badges
  for (const badge of badgeTypes) {
    if (state[badge.key]) {
      const pos = positions[badge.pos];
      svg += `<circle cx="${pos.x}" cy="${pos.y}" r="${badgeR}" fill="white"/>`;
      svg += `<circle cx="${pos.x}" cy="${pos.y}" r="${badgeR * 0.85}" fill="${badge.col}"/>`;
      svg += `<text x="${pos.x}" y="${pos.y}" font-size="${badgeR * 1.2}" font-family="Arial" font-weight="bold" fill="${badge.textCol}" text-anchor="middle" dominant-baseline="central">${badge.text}</text>`;
    }
  }

  svg += '</svg>';
  return svg;
}

/**
 * Generate a data URL for a table icon (cached).
 * This is the primary API for non-map icons.
 * @param {number} typeId - Cache type ID
 * @param {Object} state - State flags
 * @param {number} size - Icon size in pixels (default 24)
 * @returns {string} Data URL
 */
export function generateIconUrl(typeId, state = {}, size = 24) {
  const hash = buildTableIconHash(typeId, state, size);
  if (tableIconCache[hash]) return tableIconCache[hash];

  const svg = generateTableIconSvg(typeId, state, size);
  const url = `data:image/svg+xml,${encodeURIComponent(svg)}`;
  tableIconCache[hash] = url;
  return url;
}

// --------------------------------------------------------------------------
// Map Marker Icon Generator (with pins for Leaflet)
// --------------------------------------------------------------------------

// --------------------------------------------------------------------------
// Calculates a marker icon based on a state variable. At first, based on the
// state vector a hash is created, actually a string where we concatenate the
// state vector elements in a very straight forward way. Once we have the hash
// we do a lookup and see whether we have generatet and used this specific
// icon before and if yes, well then we just turn it over to the caller and
// nothing else. If there is no hit on the lookup we need to create this specific
// icon and all the associated dots (badges) and colors based on state, then
// put it into the lookup table and also handing it over to the caller.

const cacheIcons = {};

export function getIcon(u) {

  const R = 15; // some sort of a default radius. Everything is derived from R,
                // however, it may not really work well to modify this value
                // YMMV 

  // --------------------------------------------------------------------------
  // We define the function within a parent scope such that we don't hve to pass
  // "R" as a parameter and to avoid making "R" a global variable.


  function createDots(u) {
    const r = 1.5 * R;
    const c = {
      ur: { x: r *  0.5, y: r * -0.5 },
      ul: { x: r * -0.5, y: r * -0.5 },
      bl: { x: r * -0.5, y: r *  0.5 },
      br: { x: r *  0.5, y: r *  0.5 },
    };

    let dots = '';

    badgeTypes.forEach((badge) => {
      if (u[badge.key]) {
        dots += `    <g transform="translate(${c[badge.pos].x},${c[badge.pos].y}) scale(${0.45})">\n`;
        dots += `      <circle r="${R}" fill="white" />\n`;
        dots += `      <circle r="${0.85 * R}" fill="${badge.col}" />\n`;
        dots += `      <text y="0%" font-size="${R}" font-family="Arial" font-weight="bold" fill="${badge.textCol}" text-anchor="middle" alignment-baseline="middle">${badge.text}</text>\n`;
        dots += `    </g>\n`;
      }
    });
    return dots;
  }

  // --------------------------------------------------------------------------
  //  define a hash to be used to lookup icons which we potentially used before

  const propertyToSuffix = {
    isDisabled  : '_dis',
    isArchived  : '_arch',
    isOwned     : '_o',
    isDNF       : '_d',
    isFound     : '_f',
    isPartial   : '_p',
    isGuessable : '_g',
    hasCC       : '_c',
    hasPCN      : '_n',
    isCached    : '_l',
    isOcOnly    : '_oc',
    isSelected  : '_sel',
  };

  // Support both canonical format (geocacheType.id) and marker options format (type)
  const typeId = u.geocacheType?.id ?? u.type;

  let hash = `${typeId}`;

  for (const property in propertyToSuffix) {
    if (u[property]) {
      hash += propertyToSuffix[property];
    }
  }

  let icon = cacheIcons[hash];
  if (typeof icon !== 'undefined') return icon; // on a match there is nothing left to do

  const ct = getCacheType(typeId);

  let {col, text} = ct;

  // --------------------------------------------------------------------------
  //  create SVG header, define a black outer ring with a smaller white ring
  //  on top of it

  // Increase SVG size if selected to accommodate halo
  const svgWidth = u.isSelected ? 2.8*R : 2.4*R;
  const svgHeight = u.isSelected ? 3.0*R : 2.6*R;
  const offsetX = u.isSelected ? 1.4*R : 1.2*R;
  const offsetY = u.isSelected ? 1.4*R : 1.2*R;

  let svg  = `<svg xmlns="http://www.w3.org/2000/svg" width="${svgWidth}" height="${svgHeight}">\n`;
      svg += `  <g transform="translate(${offsetX},${offsetY})">\n`;

      // Red halo ring behind the icon when selected
      if (u.isSelected) {
        svg += `  <circle fill="none" stroke="red" stroke-width="${0.25*R}" r="${1.35*R}" />\n`;
      }

      svg += createHexagonIcon(R);

  // --------------------------------------------------------------------------
  // create base icon. Color may be overriden by states isDisabled / isArchived

  if (u.isDisabled) col = 'darkGrey';
  if (u.isArchived) col = 'lightPink';

  {
    const S = 0.89 * R;
    const rx = S * 0.15;
    svg += `  <rect x="${-0.80*S}" y="${-0.80*S}" width="${2*0.80*S}" height="${2*0.80*S}" rx="${rx}" fill="${col}" />\n`;
  }

  // Type label (icon paths or text fallback)
  const typeIcon = getCacheType(typeId).icon;
  if (typeIcon) {
    const iconSize = 1.6 * R;
    svg += renderIconPaths(typeIcon, -iconSize / 2, -iconSize / 2, iconSize) + '\n';
  } else {
    svg += `  <text y="1" font-size="${R}" font-family="Arial" fill="white" text-anchor="middle" alignment-baseline="middle" font-weight="bold">${text}</text>\n`;
  }
  
  // --------------------------------------------------------------------------
  // now all the dots based on various state variables

  svg += createDots(u);


  // --------------------------------------------------------------------------
  // done, close it

  svg += '  </g>\n';
  svg += '</svg>\n';

  // Adjust anchor points for selected icons (larger SVG)
  const iconOptions = { iconUrl: `data:image/svg+xml,${encodeURIComponent(svg)}` };
  if (u.isSelected) {
    iconOptions.iconAnchor = [1.4*R, svgHeight];
    iconOptions.popupAnchor = [0, -svgHeight];
    iconOptions.tooltipAnchor = [-1.4*R, -1.2*R];
  }

  icon = new liveIcon(iconOptions);
  cacheIcons[hash] = icon;
  return icon;
}

// -----------------------------------
// createHexagonIcon() — now renders a rounded square (like cgeo OC markers)
//
function createHexagonIcon(R) {
  const S = 0.89 * R;
  const rx = S * 0.2;
  let svg  = `  <rect x="${-S}" y="${-S}" width="${2*S}" height="${2*S}" rx="${rx}" fill="black" />\n`;
      svg += `  <path d="M ${-0.2 * R} ${0.96 * S} L 0 ${1.3 * R} L ${0.2 * R} ${0.96 * S}" fill="black" />\n`;
      svg += `  <path d="M ${-0.1 * R} ${S} L 0 ${1.2 * R} L ${0.1 * R} ${S}" fill="white" />\n`;
      svg += `  <rect x="${-0.95*S}" y="${-0.95*S}" width="${2*0.95*S}" height="${2*0.95*S}" rx="${rx}" fill="white" />\n`;
  return svg;
}

// code: language=javascript insertSpaces=true tabSize=2
// vim: ts=2:sw=2:et:ft=javascript
