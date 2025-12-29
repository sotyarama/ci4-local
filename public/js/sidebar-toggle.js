(function () {
    // Collapse/expand all sidebar sections
    function setCollapsed(collapsed) {
        document.querySelectorAll('.nav-section-title').forEach(function (el) {
            if (collapsed) {
                el.classList.add('collapsed');
                var group = el.nextElementSibling;
                if (group && group.classList.contains('nav-group')) group.style.display = 'none';
            } else {
                el.classList.remove('collapsed');
                var group = el.nextElementSibling;
                if (group && group.classList.contains('nav-group')) group.style.display = '';
            }
        });
    }

    function toggleAll() {
        var anyOpen = Array.from(document.querySelectorAll('.nav-section-title')).some(function (el) {
            return !el.classList.contains('collapsed');
        });
        setCollapsed(anyOpen);
        // update button icon + aria-label
        var btn = document.getElementById('sidebar-collapse-all-btn');
        if (btn) {
            var icon = btn.querySelector('.collapse-icon');
            if (icon) icon.textContent = anyOpen ? '+' : '−';
            btn.setAttribute('aria-label', anyOpen ? 'Expand all' : 'Collapse all');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Fallback: attach per-section toggle handlers if not already present
        document.querySelectorAll('.nav-section-title.collapsible').forEach(function (title) {
            // avoid adding duplicate listeners
            if (title.dataset.sidebarToggleAttached) return;
            title.dataset.sidebarToggleAttached = '1';
            title.setAttribute('role', 'button');
            title.setAttribute('tabindex', '0');
            title.addEventListener('click', function () {
                var isCollapsed = title.classList.contains('collapsed');
                if (isCollapsed) {
                    title.classList.remove('collapsed');
                    var grp = title.nextElementSibling;
                    if (grp && grp.classList.contains('nav-group')) grp.style.display = '';
                    title.setAttribute('aria-expanded', 'true');
                    if (grp) grp.setAttribute('aria-hidden', 'false');
                } else {
                    title.classList.add('collapsed');
                    var grp2 = title.nextElementSibling;
                    if (grp2 && grp2.classList.contains('nav-group')) grp2.style.display = 'none';
                    title.setAttribute('aria-expanded', 'false');
                    if (grp2) grp2.setAttribute('aria-hidden', 'true');
                }
            });
            title.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    title.click();
                }
            });
        });
        var btn = document.getElementById('sidebar-collapse-all-btn');
        if (!btn) return;
        // Set initial icon based on current state
        var anyOpenInit = Array.from(document.querySelectorAll('.nav-section-title')).some(function (el) {
            return !el.classList.contains('collapsed');
        });
        var iconInit = btn.querySelector('.collapse-icon');
        if (iconInit) iconInit.textContent = anyOpenInit ? '−' : '+';
        btn.setAttribute('aria-label', anyOpenInit ? 'Collapse all' : 'Expand all');

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            toggleAll();
        });
    });
})();

// Delegate: ensure clicks on any .nav-section-title toggle the section (robust if elements are dynamic)
document.addEventListener('click', function (ev) {
    var title = ev.target.closest && ev.target.closest('.nav-section-title.collapsible');
    if (!title) return;
    // ignore clicks on the collapse-all button
    if (title.id === 'sidebar-collapse-all-btn' || title.closest('#sidebar-collapse-all-btn')) return;
    // toggle this section
    var isCollapsed = title.classList.contains('collapsed');
    var grp = title.nextElementSibling;
    if (isCollapsed) {
        title.classList.remove('collapsed');
        title.setAttribute('aria-expanded', 'true');
        if (grp && grp.classList.contains('nav-group')) grp.style.display = '';
        if (grp) grp.setAttribute('aria-hidden', 'false');
    } else {
        title.classList.add('collapsed');
        title.setAttribute('aria-expanded', 'false');
        if (grp && grp.classList.contains('nav-group')) grp.style.display = 'none';
        if (grp) grp.setAttribute('aria-hidden', 'true');
    }
});
