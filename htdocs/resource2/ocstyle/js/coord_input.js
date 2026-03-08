/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/
/**
 * Unified coordinate input field — parser + UI logic (ESM module).
 *
 * Adds a single text input above the legacy 6-field coordinate form.
 * On input/paste the parser populates hidden lat/lon fields for POST.
 * A toggle link switches between unified and 6-field modes.
 */
import { coords2Dm, pad } from '../../misc/shared/coords.js';

// ── conversion: lat/lon ↔ 6-field values ────────────────────────

/** Convert parsed 6-component result to decimal lat/lon. */
function toLatLon(r) {
    var lat = r.latDeg + r.latMin / 60;
    if (r.latHem === 'S') lat = -lat;
    var lon = r.lonDeg + r.lonMin / 60;
    if (r.lonHem === 'W') lon = -lon;
    return { latitude: lat, longitude: lon };
}

/** Convert decimal lat/lon to 6-field values for legacy fields. */
function toFields(lat, lon) {
    var latHem = lat >= 0 ? 'N' : 'S';
    var lonHem = lon >= 0 ? 'E' : 'W';
    lat = Math.abs(lat);
    lon = Math.abs(lon);

    var latDeg = Math.trunc(lat);
    var lonDeg = Math.trunc(lon);

    var latDegFrac = Number((lat - latDeg).toFixed(14));
    var lonDegFrac = Number((lon - lonDeg).toFixed(14));

    var latMin = 60 * latDegFrac;
    var lonMin = 60 * lonDegFrac;

    var latMinInt = Math.trunc(latMin);
    var lonMinInt = Math.trunc(lonMin);

    var latMinFrac = Number((latMin - latMinInt).toFixed(3));
    var lonMinFrac = Number((lonMin - lonMinInt).toFixed(3));

    if (latMinFrac === 1) {
        latMinFrac = 0; latMinInt++;
        if (latMinInt === 60) { latMinInt = 0; latDeg++; }
    }
    if (lonMinFrac === 1) {
        lonMinFrac = 0; lonMinInt++;
        if (lonMinInt === 60) { lonMinInt = 0; lonDeg++; }
    }

    return {
        latHem: latHem, latDeg: latDeg,
        latMin: pad(latMinInt, 2) + '.' + pad(1000 * latMinFrac, 3),
        lonHem: lonHem, lonDeg: lonDeg,
        lonMin: pad(lonMinInt, 2) + '.' + pad(1000 * lonMinFrac, 3)
    };
}

// ── parser ──────────────────────────────────────────────────────

/** Replace unicode symbols with ASCII equivalents and collapse whitespace. */
function normalize(str) {
    return str
        .replace(/[\u00B0\u00BA]/g, '\u00B0')
        .replace(/[\u2032\u2019\u0060\u00B4]/g, "'")
        .replace(/[\u2033\u201D]/g, '"')
        .replace(/,/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

/**
 * Parse a coordinate string into its 6 components.
 * Returns { latHem, latDeg, latMin, lonHem, lonDeg, lonMin } or null.
 */
function parseCoordinates(raw) {
    var s = normalize(raw);
    if (s === '') return null;

    var result;

    result = parseWithHemispheres(s);
    if (result) return validate(result);

    result = parseDecimalDegrees(s);
    if (result) return validate(result);

    return null;
}

function parseWithHemispheres(s) {
    var hems = [];
    var re = /[NSEW]/gi;
    var m;
    while ((m = re.exec(s)) !== null) {
        hems.push({ letter: m[0].toUpperCase(), index: m.index });
    }
    if (hems.length !== 2) return null;

    var ns = null, ew = null;
    for (var i = 0; i < 2; i++) {
        if (hems[i].letter === 'N' || hems[i].letter === 'S') {
            if (ns !== null) return null;
            ns = i;
        } else {
            if (ew !== null) return null;
            ew = i;
        }
    }
    if (ns === null || ew === null) return null;

    var parts = splitByHemispheres(s, hems, ns, ew);
    if (!parts) return null;

    var lat = parseNumberPart(parts.lat);
    var lon = parseNumberPart(parts.lon);
    if (!lat || !lon) return null;

    return {
        latHem: hems[ns].letter, latDeg: lat.deg, latMin: lat.min,
        lonHem: hems[ew].letter, lonDeg: lon.deg, lonMin: lon.min
    };
}

function splitByHemispheres(s, hems, nsIdx, ewIdx) {
    var first = hems[0], second = hems[1];
    var firstIsNS = (nsIdx === 0);

    var chars = s.split('');
    chars[first.index] = '|';
    chars[second.index] = '|';
    var segments = chars.join('').split('|');
    var numberParts = [];
    for (var i = 0; i < segments.length; i++) {
        var trimmed = segments[i].trim();
        if (trimmed !== '') numberParts.push(trimmed);
    }

    if (numberParts.length === 2) {
        if (firstIsNS) {
            return { lat: numberParts[0], lon: numberParts[1] };
        } else {
            return { lat: numberParts[1], lon: numberParts[0] };
        }
    }
    return null;
}

function parseNumberPart(s) {
    s = s.trim();

    // DMS: deg° min' sec" (require ' between min and sec)
    var dms = s.match(/^(\d{1,3})\s*°?\s*(\d{1,2})\s*['′]\s*(\d{1,2}(?:\.\d+)?)\s*["″]?\s*$/);
    if (dms) {
        return { deg: parseInt(dms[1], 10), min: parseInt(dms[2], 10) + parseFloat(dms[3]) / 60 };
    }

    // Decimal minutes: deg° min.mmm' (require ° or space between deg and min)
    var dm = s.match(/^(\d{1,3})\s*[°\s]\s*(\d{1,2}(?:\.\d+)?)\s*['′]?\s*$/);
    if (dm) {
        return { deg: parseInt(dm[1], 10), min: parseFloat(dm[2]) };
    }

    // Decimal degrees: deg.ddddd
    var dd = s.match(/^(\d{1,3}(?:\.\d+)?)\s*°?\s*$/);
    if (dd) {
        var total = parseFloat(dd[1]);
        var d = Math.floor(total);
        return { deg: d, min: (total - d) * 60 };
    }

    return null;
}

function parseDecimalDegrees(s) {
    var m = s.match(/^([+-]?\d{1,3}(?:\.\d+)?)\s+([+-]?\d{1,3}(?:\.\d+)?)$/);
    if (!m) return null;

    var lat = parseFloat(m[1]);
    var lon = parseFloat(m[2]);

    return {
        latHem: lat >= 0 ? 'N' : 'S', latDeg: Math.floor(Math.abs(lat)), latMin: (Math.abs(lat) - Math.floor(Math.abs(lat))) * 60,
        lonHem: lon >= 0 ? 'E' : 'W', lonDeg: Math.floor(Math.abs(lon)), lonMin: (Math.abs(lon) - Math.floor(Math.abs(lon))) * 60
    };
}

function validate(r) {
    if (r.latDeg < 0 || r.latDeg > 90) return null;
    if (r.lonDeg < 0 || r.lonDeg > 180) return null;
    if (r.latMin < 0 || r.latMin >= 60) return null;
    if (r.lonMin < 0 || r.lonMin >= 60) return null;
    if (r.latDeg === 90 && r.latMin > 0) return null;
    if (r.lonDeg === 180 && r.lonMin > 0) return null;
    return r;
}

// ── DOM wiring ──────────────────────────────────────────────────

function init() {
    var unified   = document.getElementById('coord_unified');
    var feedback  = document.getElementById('coord_feedback');
    var toggle    = document.getElementById('coord_toggle');
    var detail    = document.getElementById('coord_detail');
    var singleSec = document.getElementById('coord_single');
    var hiddenLat = document.getElementById('coord_lat');
    var hiddenLon = document.getElementById('coord_lon');

    if (!unified || !feedback || !toggle || !detail || !singleSec || !hiddenLat || !hiddenLon) return;

    var latHemEl = document.getElementsByName('lat_hem')[0];
    var latDegEl = document.getElementsByName('lat_deg')[0];
    var latMinEl = document.getElementsByName('lat_min')[0];
    var lonHemEl = document.getElementsByName('lon_hem')[0];
    var lonDegEl = document.getElementsByName('lon_deg')[0];
    var lonMinEl = document.getElementsByName('lon_min')[0];

    if (!latHemEl || !latDegEl || !latMinEl || !lonHemEl || !lonDegEl || !lonMinEl) return;

    var showing6 = false;
    var label6 = toggle.getAttribute('data-label-6') || '6 fields';
    var label1 = toggle.getAttribute('data-label-1') || '1 field';
    var labelParseError = toggle.getAttribute('data-label-parse-error') || 'Could not parse coordinates';

    /** Populate 6 legacy fields from lat/lon. */
    function populateFields(lat, lon) {
        var f = toFields(lat, lon);
        latHemEl.value = f.latHem;
        latDegEl.value = f.latDeg;
        latMinEl.value = f.latMin;
        lonHemEl.value = f.lonHem;
        lonDegEl.value = f.lonDeg;
        lonMinEl.value = f.lonMin;
    }

    /** Compute lat/lon from 6 legacy fields. */
    function latLonFrom6Fields() {
        var hem1 = latHemEl.value === 'N' ? 1 : -1;
        var hem2 = lonHemEl.value === 'E' ? 1 : -1;
        var lat = ((parseFloat(latDegEl.value) || 0) + (parseFloat(latMinEl.value) || 0) / 60) * hem1;
        var lon = ((parseFloat(lonDegEl.value) || 0) + (parseFloat(lonMinEl.value) || 0) / 60) * hem2;
        return { latitude: lat, longitude: lon };
    }

    function clearFields() {
        latDegEl.value = '';
        latMinEl.value = '';
        lonDegEl.value = '';
        lonMinEl.value = '';
    }

    /** Handle input in the unified field. */
    function onUnifiedInput() {
        var val = unified.value.trim();
        if (val === '') {
            feedback.textContent = '';
            feedback.style.color = '';
            hiddenLat.value = '';
            hiddenLon.value = '';
            clearFields();
            return;
        }
        var r = parseCoordinates(val);
        if (r) {
            var ll = toLatLon(r);
            hiddenLat.value = ll.latitude;
            hiddenLon.value = ll.longitude;
            populateFields(ll.latitude, ll.longitude);
            feedback.textContent = '\u2713 ' + coords2Dm(ll.latitude, ll.longitude);
            feedback.style.color = '#060';
        } else {
            feedback.textContent = '\u2717 ' + labelParseError;
            feedback.style.color = '#c00';
            hiddenLat.value = '';
            hiddenLon.value = '';
            clearFields();
        }
    }

    /** Switch between unified and 6-field modes. */
    function toggleMode(e) {
        if (e) e.preventDefault();
        showing6 = !showing6;
        try { localStorage.setItem('oc_coord_6fields', showing6 ? '1' : '0'); } catch (ex) {}
        if (showing6) {
            singleSec.style.display = 'none';
            detail.style.display = '';
            toggle.textContent = label1 + ' \u25B4';
        } else {
            detail.style.display = 'none';
            singleSec.style.display = '';
            toggle.textContent = label6 + ' \u25BE';
            var ll = latLonFrom6Fields();
            hiddenLat.value = ll.latitude;
            hiddenLon.value = ll.longitude;
            if (ll.latitude !== 0 || ll.longitude !== 0) {
                unified.value = coords2Dm(ll.latitude, ll.longitude);
                feedback.textContent = '\u2713 ' + coords2Dm(ll.latitude, ll.longitude);
                feedback.style.color = '#060';
            } else {
                unified.value = '';
                feedback.textContent = '';
                feedback.style.color = '';
            }
        }
    }

    // Wire events
    unified.addEventListener('input', onUnifiedInput);
    toggle.addEventListener('click', toggleMode);

    // Initial state: show unified, hide 6-field, reveal toggle
    singleSec.style.display = '';
    detail.style.display = 'none';
    toggle.style.display = '';
    toggle.textContent = label6 + ' \u25BE';

    // Restore user's preferred mode
    try {
        if (localStorage.getItem('oc_coord_6fields') === '1') {
            toggleMode(null);
        }
    } catch (ex) {}

    // Populate unified field from stored lat/lon
    var storedLat = parseFloat(hiddenLat.value);
    var storedLon = parseFloat(hiddenLon.value);
    if (!isNaN(storedLat) && !isNaN(storedLon) && (storedLat !== 0 || storedLon !== 0)) {
        unified.value = coords2Dm(storedLat, storedLon);
        populateFields(storedLat, storedLon);
        feedback.textContent = '\u2713 ' + coords2Dm(storedLat, storedLon);
        feedback.style.color = '#060';
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
