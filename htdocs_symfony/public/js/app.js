/*
 * Front-end entry point — modelled after gcxm.js.
 *
 * Page selection:
 *   - `<body data-page="...">` names a module under public/js/pages/<page>.js
 *   - `<body data-map-js="true">` triggers Leaflet asset loading and a
 *     side-effect import of public/js/pages/map/map.js (the verbatim gcxm
 *     map module which initializes the live map on import).
 *
 * Globals such as `uniCacheWP`, `lat`, `lon`, `defaultZoom`, `enabledPlatforms`
 * must be set by the page template BEFORE this module loads so the map module
 * sees them. Each page module then exports `init()` which app.js calls after
 * the map (if any) is ready.
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
        loadCss('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
        loadCss('https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css'),
        loadCss('https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css'),
        loadCss('https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css'),
    ]);

    await loadJs('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
    await Promise.all([
        loadJs('https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js'),
        loadJs('https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js'),
    ]);

    // Side-effect import: the verbatim gcxm map.js initializes the map at top level.
    await import('./pages/map/map.js');
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
        const mod = await import(`./pages/${page}.js`);
        if (typeof mod.init === 'function') await mod.init();
    } catch (err) {
        console.log(`[App] Failed to load page module: ${page}`, err);
    }
});
