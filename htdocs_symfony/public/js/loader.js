/*
 * Front-end entry point.
 *
 * Page selection:
 *   - `<body data-page="...">` names a module under public/js/<page>.js
 *   - `<body data-map-js="true">` triggers Leaflet asset loading and a
 *     side-effect import of public/js/map.js, which initializes the live
 *     map on import.
 *
 * Globals such as `uniCacheWP`, `lat`, `lon`, `defaultZoom` must be set by
 * the page template BEFORE this module loads so the map module sees them.
 * Each page module then exports `init()` which loader.js calls
 * after the map (if any) is ready.
 */

async function loadCss(href) {
    if ([...document.styleSheets].some(s => s.href && s.href.endsWith(href))) return;
    return new Promise((resolve, reject) => {
        const link = document.createElement('link');
        link.rel    = 'stylesheet';
        link.href   = href;
        link.onload = resolve;
        link.onerror = reject;
        document.head.appendChild(link);
    });
}

async function loadJs(src) {
    if ([...document.scripts].some(s => s.src === src)) return;
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src    = src;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

async function loadMap() {
    await Promise.all([
        loadCss('/vendor/leaflet/leaflet.css'),
        loadCss('/vendor/leaflet-draw/leaflet.draw.css'),
        loadCss('/vendor/leaflet.markercluster/MarkerCluster.css'),
        loadCss('/vendor/leaflet.markercluster/MarkerCluster.Default.css'),
        loadCss('/css/map.css'),
    ]);

    await loadJs('/vendor/leaflet/leaflet.js');
    await Promise.all([
        loadJs('/vendor/leaflet-draw/leaflet.draw.js'),
        loadJs('/vendor/leaflet.markercluster/leaflet.markercluster.js'),
    ]);

    await import('./map.js');
}

document.addEventListener('DOMContentLoaded', async () => {
    const body  = document.body;
    const page  = body.dataset.page;
    const mapJs = body.dataset.mapJs === 'true';

    if (mapJs) {
        try { await loadMap(); }
        catch (err) { console.log('[App] Map asset/module load failed', err); }
    }

    if (!page) return;

    try {
        const mod = await import(`./${page}.js`);
        if (typeof mod.init === 'function') await mod.init();
    } catch (err) {
        console.log(`[App] Failed to load page module: ${page}`, err);
    }
});
