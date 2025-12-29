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
