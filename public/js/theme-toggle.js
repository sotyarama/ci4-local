(function () {
  var root = document.documentElement;

  function applyTheme(mode) {
    var theme = mode === 'dark' ? 'dark' : 'light';
    root.setAttribute('data-theme', theme);
    try {
      localStorage.setItem('theme', theme);
    } catch (e) {
      /* ignore storage errors */
    }

    var btn = document.getElementById('themeToggle');
    if (!btn) return;
    btn.textContent = theme === 'dark' ? '☀️' : '🌙';
    btn.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
  }

  document.addEventListener('DOMContentLoaded', function () {
    var saved = 'light';
    try {
      saved = localStorage.getItem('theme') || root.getAttribute('data-theme') || 'light';
    } catch (e) {
      saved = root.getAttribute('data-theme') || 'light';
    }
    applyTheme(saved);

    var toggle = document.getElementById('themeToggle');
    if (!toggle) return;

    toggle.addEventListener('click', function () {
      var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      applyTheme(next);
    });
  });
})();
