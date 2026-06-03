/**
 * OC New UI Toggle — opt-in early access to Symfony rebuild
 *
 * Maps legacy PHP paths to new Symfony UI paths and redirects when enabled.
 * Pages available in new UI: /livemap, /cache/{wp}, /search, /newcache
 * All other pages fall back to legacy.
 */

(function() {
  // Map legacy PHP paths to new Symfony paths
  const LEGACY_TO_SYMFONY = {
    '/map.php': '/livemap',
    '/map3.php': '/livemap', // Old "New map" → Symfony livemap
    '/search.php': '/caches/',
    '/livemap.php': '/livemap',
    '/newcache.php': '/cache/new',
    '/viewcache.php': '/cache', // Special: viewcache.php?wp=... → /cache/...
  };

  // Initialize checkbox state from localStorage
  function initCheckbox() {
    const checkbox = document.getElementById('useNewUIToggle');
    if (!checkbox) return;

    const useNewUI = localStorage.getItem('oc-use-new-ui') === '1';
    checkbox.checked = useNewUI;
  }

  // Toggle handler — save preference and reload
  function handleToggle(event) {
    const useNewUI = event.target.checked;
    localStorage.setItem('oc-use-new-ui', useNewUI ? '1' : '0');
    location.reload();
  }

  // Attach handler when DOM is ready
  function attachHandler() {
    const checkbox = document.getElementById('useNewUIToggle');
    if (checkbox) {
      checkbox.addEventListener('change', handleToggle);
    }
  }

  // Redirect to new UI if enabled and page exists in new UI
  function maybeRedirect() {
    const useNewUI = localStorage.getItem('oc-use-new-ui') === '1';
    console.log('[oc-new-ui-toggle] useNewUI:', useNewUI);
    if (!useNewUI) return;

    // Prevent infinite redirect loops — skip if we already attempted a redirect
    const params = new URLSearchParams(window.location.search);
    if (params.get('from') === 'legacy') {
      console.log('[oc-new-ui-toggle] Already redirected, skipping');
      return;
    }

    const path = window.location.pathname;
    console.log('[oc-new-ui-toggle] legacy path:', path);

    // Check if this legacy page maps to a new UI page
    const symfonyPath = LEGACY_TO_SYMFONY[path];
    console.log('[oc-new-ui-toggle] symfony path:', symfonyPath);

    if (symfonyPath) {
      // Build the new URL
      let newPath = symfonyPath;

      // Resolve cache path: prefer wp=OC code, else extract from page DOM.
      // If neither is available (e.g. non-OC foreign caches), skip redirect.
      if (path === '/viewcache.php' || path === '/viewcache') {
        if (params.has('wp')) {
          newPath = '/cache/' + params.get('wp');
        } else if (params.has('cacheid')) {
          var ocMatch = document.body && document.body.innerText.match(/\bOC[0-9A-F]{4,6}\b/);
          if (ocMatch) {
            newPath = '/cache/' + ocMatch[0];
          } else {
            return; // can't resolve to an OC code, stay on legacy page
          }
        }
      }

      // Determine the Symfony (new UI) domain from a <meta> tag
      // set by the legacy template from server-side configuration.
      // Falls back to same domain if the meta tag is absent.
      var meta = document.querySelector('meta[name="symfony-domain"]');
      var symfonyHost = (meta && meta.content) ? meta.content : window.location.hostname;
      const newDomain = window.location.protocol + '//' + symfonyHost;
      const newParams = new URLSearchParams(window.location.search);
      newParams.set('from', 'legacy');

      const redirectUrl = newDomain + newPath + '?' + newParams.toString();
      console.log('[oc-new-ui-toggle] Redirecting to:', redirectUrl);
      window.location.href = redirectUrl;
    }
  }

  // Run on page load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      initCheckbox();
      attachHandler();
      maybeRedirect();
    });
  } else {
    initCheckbox();
    attachHandler();
    maybeRedirect();
  }
})();
