import { Tooltip, Popover } from 'bootstrap';

/**
 * Bootstrap 5 Compatibility helper for legacy data-toggle attributes.
 */
export function initBootstrapCompatibility() {
    document.addEventListener('DOMContentLoaded', () => {
        // Tooltips
        document.querySelectorAll('[data-toggle="tooltip"]').forEach(el => {
            new Tooltip(el);
        });

        // Popovers
        document.querySelectorAll('[data-toggle="popover"]').forEach(el => {
            new Popover(el);
        });

        // Dropdowns, Modals, Collapse, etc. 
        // Bootstrap 5 normally expects data-bs-toggle.
        // We can manually trigger them or just convert the attributes.
        
        const legacyToggles = document.querySelectorAll('[data-toggle]:not([data-bs-toggle])');
        legacyToggles.forEach(el => {
            const toggle = el.getAttribute('data-toggle');
            el.setAttribute('data-bs-toggle', toggle);
            
            // Also handle data-target -> data-bs-target
            if (el.hasAttribute('data-target') && !el.hasAttribute('data-bs-target')) {
                el.setAttribute('data-bs-target', el.getAttribute('data-target'));
            }
            
            // And data-dismiss -> data-bs-dismiss
            if (el.hasAttribute('data-dismiss') && !el.hasAttribute('data-bs-dismiss')) {
                el.setAttribute('data-bs-dismiss', el.getAttribute('data-dismiss'));
            }
        });
    });
}
