/*
 * Front-end entry point.
 *
 * Naming convention:
 *   - One central app.js loaded as a module from base.html.twig.
 *   - The current page is communicated via `<body data-page="...">`.
 *   - The value names a module under public/js/pages/<page>.js, e.g.
 *       data-page="tabulator"     -> public/js/pages/tabulator.js
 *       data-page="map3/map3"     -> public/js/pages/map3/map3.js
 *   - Each page module exports `init()`. app.js calls it inside its
 *     DOMContentLoaded handler so init() runs in the same lifecycle phase
 *     regardless of which page is loaded.
 *   - Generic functionality lives under public/js/modules/ as pure ES
 *     modules and is imported by page modules.
 */

document.addEventListener('DOMContentLoaded', async () => {
    const page = document.body.dataset.page;
    if (!page) {
        console.log('[App] No page module to load.');
        return;
    }

    console.log(`[App] Loading page module: ${page}`);
    try {
        const mod = await import(`./pages/${page}.js`);
        if (typeof mod.init === 'function') {
            mod.init();
        } else {
            console.warn(`[App] Page module "${page}" has no exported init() function.`);
        }
    } catch (err) {
        console.error(`[App] Failed to load page module: ${page}`, err);
    }
});
