/* Minimal sidebar collapsed-state toggler
   - toggles .sidebar-collapsed on .layout
   - persists to localStorage key: tr.sidebarCollapsed
   - additive only, does not alter existing section-collapse logic
*/
(function () {
    var STORAGE_KEY = 'tr.sidebarCollapsed';

    function readStored() {
        try {
            return localStorage.getItem(STORAGE_KEY) === '1';
        } catch (e) {
            return false;
        }
    }

    function writeStored(v) {
        try { localStorage.setItem(STORAGE_KEY, v ? '1' : '0'); } catch (e) {}
    }

    function setCollapsed(collapsed, save) {
        var root = document.querySelector('.layout');
        if (!root) return;
        root.classList.toggle('sidebar-collapsed', !!collapsed);
        var btn = document.getElementById('sidebar-toggle-btn');
        // aria-expanded should be true when sidebar is expanded (not collapsed)
        if (btn) btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        if (save) writeStored(collapsed);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // initialize from storage
        setCollapsed(readStored(), false);

        var toggle = document.getElementById('sidebar-toggle-btn');
        if (!toggle) return;
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            var isCollapsed = document.querySelector('.layout')?.classList.contains('sidebar-collapsed');
            setCollapsed(!isCollapsed, true);
        });
    });
})();
