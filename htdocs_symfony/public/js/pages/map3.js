import { loadCss, loadScript } from '../lib/loadAsset.js';

export async function init() {
    await Promise.all([
        loadCss('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
        loadCss('https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css'),
        loadCss('https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css'),
        loadCss('https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css'),
    ]);

    await loadScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
    await Promise.all([
        loadScript('https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js'),
        loadScript('https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js'),
    ]);

    const { boot } = await import('./map3/boot.js');
    await boot();
}
