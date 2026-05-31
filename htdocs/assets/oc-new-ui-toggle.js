/**
 * OC New UI Toggle — opt-in early access to Symfony rebuild
 *
 * Checks localStorage preference and redirects to new UI pages when enabled.
 * Pages available in new UI: /livemap, /cache/{wp}, /search, /newcache
 * All other pages fall back to legacy.
 */

(function() {
  // Pages that exist in new Symfony UI
  const NEW_UI_PAGES = ['/livemap', '/cache', '/search', '/newcache'];

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
    if (!useNewUI) return;

    // Get current path (e.g., /cache/OC123AB → /cache)
    const path = window.location.pathname;

    // Check if this page exists in new UI
    const pageExists = NEW_UI_PAGES.some(newPage => path.startsWith(newPage));

    if (pageExists) {
      // Redirect to Symfony (same path, but Symfony will handle it)
      // Add ?from=legacy to avoid redirect loops
      const url = new URL(window.location);
      url.searchParams.set('from', 'legacy');
      window.location.href = url.toString();
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
