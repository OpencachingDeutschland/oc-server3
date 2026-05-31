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
    '/search.php': '/search',
    '/livemap.php': '/livemap',
    '/newcache.php': '/newcache',
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

      // Special handling for viewcache.php?wp=OC123AB → /cache/OC123AB
      if (path === '/viewcache.php' && params.has('wp')) {
        newPath = '/cache/' + params.get('wp');
      }

      // Redirect to try-opencaching.ddev.site (where Symfony new UI lives)
      const newDomain = 'https://try-opencaching.ddev.site';
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
