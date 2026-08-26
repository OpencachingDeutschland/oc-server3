// loadAsset.js — dynamic vendor CSS/JS loading.
//
// Both helpers are idempotent: a URL is only inserted into <head> once,
// and the same Promise is returned on every subsequent call.
// Page modules use these in their init() to pull in vendor stylesheets
// or UMD scripts (e.g. Leaflet) that are only needed on that page.

const inflight = new Map();

export function loadCss(url) {
    if (inflight.has(url)) return inflight.get(url);
    const p = new Promise((resolve, reject) => {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = url;
        link.onload = () => resolve(link);
        link.onerror = () => reject(new Error(`Failed to load CSS: ${url}`));
        document.head.appendChild(link);
    });
    inflight.set(url, p);
    return p;
}

export function loadScript(url, { crossorigin } = {}) {
    if (inflight.has(url)) return inflight.get(url);
    const p = new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = url;
        if (crossorigin) script.crossOrigin = crossorigin;
        script.onload = () => resolve(script);
        script.onerror = () => reject(new Error(`Failed to load script: ${url}`));
        document.head.appendChild(script);
    });
    inflight.set(url, p);
    return p;
}
