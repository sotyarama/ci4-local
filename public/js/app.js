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
    el.style.top = '16px';
    el.style.right = '16px';
    el.style.display = 'flex';
    el.style.flexDirection = 'column';
    el.style.gap = '8px';
    el.style.zIndex = '9999';
    document.addEventListener('DOMContentLoaded', () => {
      document.body.appendChild(el);
    });
    return el;
  })();

  function toast(message, type = 'info') {
    const el = document.createElement('div');
    el.textContent = message;
    el.style.padding = '10px 12px';
    el.style.borderRadius = '8px';
    el.style.fontSize = '12px';
    el.style.color = '#111';
    el.style.background = type === 'error' ? 'var(--tr-secondary-beige, #f2e8da)' : 'rgba(122,154,108,0.16)';
    el.style.border = type === 'error' ? '1px solid var(--tr-accent-brown, #a27c55)' : '1px solid var(--tr-primary, #7A9A6C)';

    toastContainer.appendChild(el);
    setTimeout(() => {
      el.style.opacity = '0';
      el.style.transition = 'opacity 0.3s';
      setTimeout(() => el.remove(), 300);
    }, 2200);
  }

  window.App = {
    fetchJSON,
    toast,
    csrfName,
    csrfToken,
  };
})();
