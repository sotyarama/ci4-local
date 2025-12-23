(function () {
  const csrfName = document.querySelector('meta[name="csrf-name"]')?.content || 'csrf_token';
  const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

  function encodeForm(data) {
    const params = new URLSearchParams();
    Object.entries(data || {}).forEach(([k, v]) => {
      params.append(k, v);
    });
    // CSRF
    params.set(csrfName, csrfToken());
    return params;
  }

  async function fetchJSON(url, options = {}) {
    const opts = Object.assign(
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
      options || {}
    );

    if (opts.body && typeof opts.body === 'object' && !(opts.body instanceof URLSearchParams)) {
      opts.body = encodeForm(opts.body);
    }

    const res = await fetch(url, opts);
    const text = await res.text();
    let json;
    try {
      json = JSON.parse(text);
    } catch (e) {
      throw new Error(text || 'Invalid JSON response');
    }

    if (!res.ok || (json && json.status === 'error')) {
      const msg = (json && json.message) || `Request failed (${res.status})`;
      const err = new Error(msg);
      err.response = json;
      throw err;
    }
    return json;
  }

  // Simple toast
  const toastContainer = (() => {
    const el = document.createElement('div');
    el.id = 'app-toast-container';
    el.style.position = 'fixed';
    el.style.top = '18px';
    el.style.right = '18px';
    el.style.display = 'flex';
    el.style.flexDirection = 'column';
    el.style.gap = '10px';
    el.style.zIndex = '9999';
    document.addEventListener('DOMContentLoaded', () => {
      document.body.appendChild(el);
    });
    return el;
  })();

  function toast(message, type = 'info') {
    const el = document.createElement('div');
    el.textContent = message;
    el.style.padding = '12px 14px';
    el.style.borderRadius = '12px';
    el.style.fontSize = '13px';
    el.style.lineHeight = '1.5';
    el.style.minWidth = '260px';
    el.style.maxWidth = '360px';
    el.style.boxShadow = '0 10px 28px rgba(0,0,0,0.12)';
    el.style.display = 'flex';
    el.style.alignItems = 'center';
    el.style.gap = '10px';
    el.style.fontWeight = '600';

    if (type === 'error') {
      el.style.background = '#fbeae3';
      el.style.border = '1px solid #d26f46';
      el.style.color = '#8b3d1f';
    } else {
      el.style.background = '#e6f1e7';
      el.style.border = '1px solid #7a9a6c';
      el.style.color = '#2f3a2f';
    }

    toastContainer.appendChild(el);
    setTimeout(() => {
      el.style.opacity = '0';
      el.style.transition = 'opacity 0.3s';
      setTimeout(() => el.remove(), 300);
    }, 2200);
  }

  function setupFilter(opts) {
    const input = typeof opts.input === 'string' ? document.querySelector(opts.input) : opts.input;
    const rows = typeof opts.rows === 'string' ? document.querySelectorAll(opts.rows) : opts.rows;
    const nores = opts.noResult ? (typeof opts.noResult === 'string' ? document.querySelector(opts.noResult) : opts.noResult) : null;
    const fields = opts.fields || [];
    if (!input || !rows) return;
    let timer;
    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(() => {
        const q = (input.value || '').toLowerCase().trim();
        let shown = 0;
        rows.forEach(function (tr) {
          const data = fields.map(f => (tr.dataset[f] || '').toLowerCase()).join(' ');
          const match = !q || data.includes(q);
          tr.style.display = match ? '' : 'none';
          if (match) shown++;
        });
        if (nores) nores.style.display = shown === 0 ? '' : 'none';
      }, opts.debounce || 200);
    });
  }

    // ======================================================
  // Sidebar collapse state (dipindah dari layout)
  // ======================================================
  (function initSidebarCollapse() {
    const stateKey = 'sidebarCollapseState';
    const saved = localStorage.getItem(stateKey);
    let collapseState = {};

    if (saved) {
      try { collapseState = JSON.parse(saved); } catch (e) { collapseState = {}; }
    }

    function saveState() {
      localStorage.setItem(stateKey, JSON.stringify(collapseState));
    }

    function toggleSection(section, force) {
      const group = document.getElementById('nav-' + section);
      const title = document.querySelector('.nav-section-title[data-target="' + section + '"]');
      if (!group || !title) return;

      const shouldCollapse = force !== undefined ? force : !(collapseState[section] === true);

      if (shouldCollapse) {
        group.style.display = 'none';
        title.classList.add('collapsed');
        collapseState[section] = true;
      } else {
        group.style.display = '';
        title.classList.remove('collapsed');
        collapseState[section] = false;
      }
      saveState();
    }

    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.nav-section-title.collapsible').forEach(function (title) {
        const target = title.getAttribute('data-target');
        if (collapseState[target] === true) {
          toggleSection(target, true);
        }
        title.addEventListener('click', function () {
          toggleSection(target);
        });
      });
    });
  })();

  // ======================================================
  // Auto wrap table di dalam .card (scrollable)
  // ======================================================
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.card table').forEach(function (tbl) {
      if (tbl.closest('.table-scroll-wrap')) return;

      const wrap = document.createElement('div');
      wrap.className = 'table-scroll-wrap';
      tbl.parentNode.insertBefore(wrap, tbl);
      wrap.appendChild(tbl);
    });
  });


  window.App = {
    fetchJSON,
    toast,
    csrfName,
    csrfToken,
    setupFilter,
  };
})();
