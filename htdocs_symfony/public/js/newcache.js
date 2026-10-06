import { coords2Dm } from './coords.js';

export function init() {
    const attribsField = document.getElementById('cache_attribs');
    const typeSelect   = document.getElementById('cacheType');
    const sizeSelect   = document.getElementById('cacheSize');

    const selected = new Set(
        (attribsField.value || '').split(';').filter(Boolean).map(Number)
    );

    function saveAttribs() {
        attribsField.value = [...selected].join(';');
    }

    document.getElementById('attrPicker')?.addEventListener('click', (e) => {
        const img = e.target.closest('img.attr-icon');
        if (!img) return;
        const id = parseInt(img.dataset.id, 10);
        if (selected.has(id)) {
            selected.delete(id);
            img.src = img.dataset.iconOff;
        } else {
            selected.add(id);
            img.src = img.dataset.iconOn;
        }
        saveAttribs();
    });

    const diffSelect = document.querySelector('select[name="difficulty"]');
    const terrSelect = document.querySelector('select[name="terrain"]');

    function syncSize() {
        const type = parseInt(typeSelect.value, 10);
        if (type === 4 || type === 5 || type === 6) {
            sizeSelect.value    = '7';
            sizeSelect.disabled = true;
        } else {
            sizeSelect.disabled = false;
        }
        if (type === 6) {
            if (diffSelect) { diffSelect.value = '2'; diffSelect.disabled = true; }
            if (terrSelect) { terrSelect.value = '2'; terrSelect.disabled = true; }
        } else {
            if (diffSelect) diffSelect.disabled = false;
            if (terrSelect) terrSelect.disabled = false;
        }
    }

    typeSelect?.addEventListener('change', syncSize);
    syncSize();

    // Country auto-fill via Nominatim reverse geocoding
    const coordsInput    = document.querySelector('input[name="coords"]');
    const countrySelect  = document.querySelector('select[name="country"]');

    function parseCoordsInput(raw) {
        raw = raw.trim();
        if (!raw) return null;
        // Decimal: "51.12345, 9.456"
        const commaMatch = raw.match(/^(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)$/);
        if (commaMatch) {
            return { lat: parseFloat(commaMatch[1]), lon: parseFloat(commaMatch[2]) };
        }
        // DM: "N51 02.345 E009 43.210"
        const dmMatch = raw.match(/^([NS])\s*(\d+)\s+(\d{2})\.(\d{1,3})\s+([EW])\s*(\d+)\s+(\d{2})\.(\d{1,3})$/);
        if (dmMatch) {
            const latSign = dmMatch[1] === 'S' ? -1 : 1;
            const latDeg  = parseInt(dmMatch[2], 10);
            const latMin  = parseInt(dmMatch[3], 10) + parseInt(dmMatch[4], 10) / Math.pow(10, dmMatch[4].length);
            const lat = latSign * (latDeg + latMin / 60);

            const lonSign = dmMatch[5] === 'W' ? -1 : 1;
            const lonDeg  = parseInt(dmMatch[6], 10);
            const lonMin  = parseInt(dmMatch[7], 10) + parseInt(dmMatch[8], 10) / Math.pow(10, dmMatch[8].length);
            const lon = lonSign * (lonDeg + lonMin / 60);

            return { lat, lon };
        }
        return null;
    }

    async function lookupCountry(lat, lon) {
        if (!countrySelect) return;
        try {
            const res = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=3`,
                { headers: { 'Accept-Language': 'en' } }
            );
            if (!res.ok) return;
            const data = await res.json();
            const code = (data.address?.country_code || '').toUpperCase();
            if (code && countrySelect.querySelector(`option[value="${code}"]`)) {
                countrySelect.value = code;
            }
        } catch (_) { /* network errors are silent */ }
    }

    let debounceTimer;
    function onCoordsChange() {
        const c = parseCoordsInput(coordsInput?.value || '');
        if (!c || c.lat === 0 || c.lon === 0) return;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => lookupCountry(c.lat, c.lon), 800);
    }

    coordsInput?.addEventListener('input', onCoordsChange);

    // Pre-fill on load if coords already set (e.g. from map click or edit prefill)
    const initial = parseCoordsInput(coordsInput?.value || '');
    if (initial) {
        lookupCountry(initial.lat, initial.lon);
    }

    // ── Additional waypoints ──
    const wptContainer = document.getElementById('waypointsContainer');
    const wptJsonField = document.getElementById('waypoints_json');
    const addWptBtn     = document.getElementById('addWaypointBtn');
    const rowTemplate   = document.getElementById('wptRowTemplate');

    function serializeWaypoints() {
        const rows = wptContainer.querySelectorAll('.wpt-row');
        const data = [];
        rows.forEach(row => {
            const type  = row.querySelector('.wpt-type')?.value || '';
            const coords = row.querySelector('.wpt-coords')?.value || '';
            const desc  = row.querySelector('.wpt-desc')?.value || '';
            if (type || coords || desc) {
                data.push({ type: parseInt(type, 10) || 0, coords, desc });
            }
        });
        wptJsonField.value = JSON.stringify(data);
    }

    function addWaypointRow(type = '', coords = '', desc = '') {
        if (!wptContainer || !rowTemplate) return;
        const clone = rowTemplate.content.cloneNode(true);
        const row = clone.querySelector('.wpt-row');

        row.querySelector('.wpt-type').value = type;
        row.querySelector('.wpt-coords').value = coords;
        row.querySelector('.wpt-desc').value = desc;

        row.querySelector('.btn-remove').addEventListener('click', () => {
            row.remove();
            serializeWaypoints();
        });
        row.querySelector('.wpt-type')?.addEventListener('change', serializeWaypoints);
        row.querySelector('.wpt-coords')?.addEventListener('input', serializeWaypoints);
        row.querySelector('.wpt-desc')?.addEventListener('input', serializeWaypoints);

        wptContainer.appendChild(row);
        serializeWaypoints();
    }

    addWptBtn?.addEventListener('click', () => addWaypointRow());

    // Load existing waypoints from the hidden field (edit prefill)
    try {
        const existing = JSON.parse(wptJsonField?.value || '[]');
        if (Array.isArray(existing)) {
            existing.forEach(w => addWaypointRow(
                String(w.type || ''), w.coords || '', w.desc || ''
            ));
        }
    } catch (e) { /* ignore */ }

    // ── Map Picker Modal ──
    const backdrop   = document.getElementById('mapPickerBackdrop');
    const dialog     = document.getElementById('mapPickerDialog');
    const pickerMap  = document.getElementById('pickerMap');
    const coordLabel = document.getElementById('mapPickerCoords');
    const saveBtn    = document.getElementById('mapPickerSave');
    const cancelBtn  = document.getElementById('mapPickerCancel');
    const closeBtn   = document.getElementById('mapPickerClose');

    let pickerLeafletMap = null;
    let pickerMarker     = null;
    let pickerTarget     = null; // the <input> we're picking for

    function showPicker() { if (backdrop) backdrop.style.display = ''; if (dialog) dialog.style.display = 'flex'; }
    function hidePicker() { if (backdrop) backdrop.style.display = 'none'; if (dialog) dialog.style.display = 'none'; }

    async function ensureLeaflet() {
        if (window.L) return;
        // Load CSS
        if (![...document.styleSheets].some(s => s.href && s.href.includes('leaflet.css'))) {
            await new Promise((resolve, reject) => {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = '/vendor/leaflet/leaflet.css';
                link.onload = resolve;
                link.onerror = reject;
                document.head.appendChild(link);
            });
        }
        // Load JS
        await new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = '/vendor/leaflet/leaflet.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    function initPickerMap() {
        if (pickerLeafletMap) return;
        const L = window.L;
        if (!L) return;
        pickerLeafletMap = L.map(pickerMap, { zoomControl: true }).setView([51.16, 10.45], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(pickerLeafletMap);

        pickerMarker = L.marker([51.16, 10.45], { draggable: true }).addTo(pickerLeafletMap);
        pickerMarker.on('dragend', updateCoordLabel);
        pickerLeafletMap.on('click', e => {
            pickerMarker.setLatLng(e.latlng);
            updateCoordLabel();
        });
    }

    function updateCoordLabel() {
        if (!pickerMarker || !coordLabel) return;
        const ll = pickerMarker.getLatLng();
        coordLabel.textContent = coords2Dm(ll.lat, ll.lng);
    }

    async function openPicker(inputEl) {
        pickerTarget = inputEl;
        showPicker();
        await ensureLeaflet();
        // Delay map init + resize until visible so Leaflet calculates size correctly
        setTimeout(() => {
            initPickerMap();
            if (!pickerLeafletMap) return;
            pickerLeafletMap.invalidateSize();

            // Parse current input value for initial position
            const raw = inputEl.value.trim();
            let lat = 51.16, lon = 10.45;
            if (raw) {
                const c = parseCoordsInput(raw);
                if (c) { lat = c.lat; lon = c.lon; }
            }
            pickerMarker.setLatLng([lat, lon]);
            pickerLeafletMap.setView([lat, lon], 14);
            updateCoordLabel();
        }, 100);
    }

    function savePicker() {
        if (pickerTarget && pickerMarker) {
            const ll = pickerMarker.getLatLng();
            pickerTarget.value = coords2Dm(ll.lat, ll.lng);
            pickerTarget.dispatchEvent(new Event('input', { bubbles: true }));
        }
        hidePicker();
    }

    // Close buttons
    [cancelBtn, closeBtn, backdrop].forEach(el => el?.addEventListener('click', hidePicker));
    saveBtn?.addEventListener('click', savePicker);

    // Click on any map-pin button — find the adjacent input and open picker
    document.addEventListener('click', e => {
        const btn = e.target.closest('.map-pin-btn');
        if (!btn) return;
        // Find the text input in the same .coords-input-wrap
        const input = btn.closest('.coords-input-wrap')?.querySelector('input[type="text"]');
        if (input) openPicker(input);
    });
}
