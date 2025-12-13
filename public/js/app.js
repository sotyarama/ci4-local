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

  // Dropdown dengan filter inline + navigasi keyboard
  const FilterSelect = (() => {
    let activeInstance = null;

    function closeIfOutside(evt) {
      if (activeInstance && !activeInstance.wrapper.contains(evt.target)) {
        activeInstance.close();
      }
    }

    document.addEventListener('click', closeIfOutside);
    document.addEventListener('focusin', closeIfOutside);

    function enhanceSelect(select) {
      if (!select || select.dataset.filterSelect === '1' || select.multiple) return;
      if (select.closest('#debug-bar') || select.closest('.debug-bar')) return;

      select.dataset.filterSelect = '1';

      const wrapper = document.createElement('div');
      wrapper.className = 'filter-select';
      const selectDisplay = getComputedStyle(select).display;
      const inlineWidth = select.style.width;
      const inlineMinWidth = select.style.minWidth;
      const inlineMaxWidth = select.style.maxWidth;
      if (selectDisplay === 'none') wrapper.style.display = 'none';

      // Pindah select ke wrapper supaya event change tetap berjalan.
      select.parentNode.insertBefore(wrapper, select);
      wrapper.appendChild(select);
      if (inlineWidth) wrapper.style.width = inlineWidth;
      if (inlineMinWidth) wrapper.style.minWidth = inlineMinWidth;
      if (inlineMaxWidth) wrapper.style.maxWidth = inlineMaxWidth;

      select.classList.add('filter-select-original');
      select.tabIndex = -1;
      select.setAttribute('aria-hidden', 'true');
      select.style.position = 'absolute';
      select.style.left = '0';
      select.style.top = '0';
      select.style.right = '0';
      select.style.bottom = '0';
      select.style.opacity = '0';
      select.style.pointerEvents = 'none';

      const input = document.createElement('input');
      input.type = 'text';
      if (select.name) {
        input.name = `${select.name}__filter`;
      }
      if (select.id) {
        input.id = `${select.id}__filter`;
      }
      input.autocomplete = 'off';
      input.spellcheck = false;
      input.className = 'filter-select-input';
      input.setAttribute('aria-label', select.getAttribute('aria-label') || select.name || select.id || 'Cari opsi');
      input.setAttribute('role', 'combobox');
      input.setAttribute('aria-expanded', 'false');

      const caret = document.createElement('span');
      caret.className = 'filter-select-caret';
      caret.innerHTML = '&#9662;';

      const list = document.createElement('div');
      list.className = 'filter-select-list';
      list.style.display = 'none';

      wrapper.appendChild(input);
      wrapper.appendChild(caret);
      wrapper.appendChild(list);

      // Ambil label placeholder dari option value="" (kalau ada)
      const placeholder =
        (select.querySelector('option[value=""]')?.textContent || '').trim() ||
        select.getAttribute('placeholder') ||
        'Pilih...';

      // DATA OPSI: abaikan option dengan value kosong (placeholder)
      const optionsData = Array.from(select.options)
        .filter(opt => opt.value !== '') // <- ini penting
        .map(opt => ({
          value: opt.value,
          label: opt.textContent || '',
          disabled: !!opt.disabled,
        }));


      if (!optionsData.length) {
        // Tidak ada opsi, batal enhance agar native select tetap bisa dipakai.
        select.dataset.filterSelect = '';
        wrapper.parentNode.insertBefore(select, wrapper);
        wrapper.remove();
        return;
      }

      let filtered = optionsData.slice();
      let highlightedIdx = filtered.findIndex(opt => opt.value === select.value);
      if (highlightedIdx < 0) highlightedIdx = filtered.length ? 0 : -1;

      function getSelectedOption() {
        return optionsData.find(opt => opt.value === select.value) || null;
      }

      function syncInputLabel() {
      const sel = getSelectedOption();
      if (sel) {
        input.value = sel.label;      // kalau ada pilihan, tampilkan label-nya
      } else {
        input.value = '';             // kalau select.value kosong (placeholder), input dikosongkan
      }
      input.placeholder = placeholder; // teks "-- pilih kategori --" muncul sebagai placeholder
    }


      function renderList() {
        list.innerHTML = '';
        if (!filtered.length) {
          const empty = document.createElement('div');
          empty.className = 'filter-select-empty';
          empty.textContent = 'Tidak ada hasil';
          list.appendChild(empty);
          return;
        }

        filtered.forEach((opt, idx) => {
          const item = document.createElement('div');
          item.className = 'filter-select-option';
          if (opt.disabled) item.classList.add('is-disabled');
          if (opt.value === select.value) item.classList.add('selected');
          if (idx === highlightedIdx) item.classList.add('active');
          item.textContent = opt.label || '(kosong)';
          item.dataset.value = opt.value;
          item.addEventListener('mousedown', e => {
            e.preventDefault();
            if (opt.disabled) return;
            chooseOption(opt);
          });
          list.appendChild(item);
        });

        const activeEl = list.querySelector('.filter-select-option.active');
        if (activeEl) activeEl.scrollIntoView({ block: 'nearest' });
      }

      function syncListSize() {
        list.style.minWidth = `${wrapper.offsetWidth}px`;
      }

      function filterOptions() {
        const q = (input.value || '').toLowerCase().trim();
        filtered = optionsData.filter(opt => {
          if (!q) return true;
          const hay = (opt.label || '') + ' ' + (opt.value || '');
          return hay.toLowerCase().includes(q);
        });

        if (!filtered.length) {
          highlightedIdx = -1;
        } else {
          const selectedIdx = filtered.findIndex(opt => opt.value === select.value);
          highlightedIdx = selectedIdx >= 0 ? selectedIdx : 0;
        }
        renderList();
      }

      function openDropdown() {
        if (wrapper.classList.contains('is-disabled')) return;
        if (activeInstance && activeInstance !== api) {
          activeInstance.close();
        }
        wrapper.classList.add('open');
        input.setAttribute('aria-expanded', 'true');
        activeInstance = api;
        syncListSize();
        list.style.display = 'block';
        filterOptions();
        setTimeout(() => input.focus({ preventScroll: true }), 0);
      }

      function closeDropdown() {
        wrapper.classList.remove('open');
        input.setAttribute('aria-expanded', 'false');
        if (activeInstance === api) activeInstance = null;
        list.style.display = 'none';
        syncInputLabel();
      }

      function chooseOption(opt) {
        select.value = opt.value;
        select.dispatchEvent(new Event('change', { bubbles: true }));
        syncInputLabel();
        closeDropdown();
      }

      function moveHighlight(delta) {
        if (!filtered.length) return;
        if (highlightedIdx < 0) highlightedIdx = 0;
        highlightedIdx = (highlightedIdx + delta + filtered.length) % filtered.length;
        renderList();
      }

      input.addEventListener('focus', () => openDropdown());
      input.addEventListener('click', () => openDropdown());
      caret.addEventListener('click', e => {
        e.preventDefault();
        if (wrapper.classList.contains('open')) closeDropdown();
        else openDropdown();
      });
      wrapper.addEventListener('click', () => {
        if (document.activeElement !== input) {
          input.focus();
        }
        openDropdown();
      });
      input.addEventListener('input', () => {
        openDropdown();
        filterOptions();
      });

      input.addEventListener('keydown', e => {
        if (e.key === 'ArrowDown') {
          openDropdown();
          moveHighlight(1);
          e.preventDefault();
        } else if (e.key === 'ArrowUp') {
          openDropdown();
          moveHighlight(-1);
          e.preventDefault();
        } else if (e.key === 'Enter') {
          if (wrapper.classList.contains('open')) {
            e.preventDefault();
            const opt = filtered[highlightedIdx];
            if (opt && !opt.disabled) chooseOption(opt);
          }
        } else if (e.key === 'Tab') {
          if (wrapper.classList.contains('open')) {
            const opt = filtered[highlightedIdx] || filtered[0];
            if (opt && !opt.disabled) chooseOption(opt);
          }
        } else if (e.key === 'Escape') {
          closeDropdown();
        }
      });

      input.addEventListener('blur', () => {
        setTimeout(() => {
          if (!wrapper.contains(document.activeElement)) closeDropdown();
        }, 80);
      });

      select.addEventListener('change', syncInputLabel);

      const observer = new MutationObserver(() => {
        const disp = getComputedStyle(select).display;
        wrapper.style.display = disp === 'none' ? 'none' : '';
        wrapper.classList.toggle('is-disabled', select.disabled);
        input.disabled = select.disabled;
      });
      observer.observe(select, { attributes: true, attributeFilter: ['style', 'class', 'disabled'] });

      wrapper.classList.toggle('is-disabled', select.disabled);
      input.disabled = select.disabled;
      syncInputLabel();
      renderList();
      syncListSize();

      const api = {
        wrapper,
        close: closeDropdown,
      };
      return api;
    }

    function enhanceAll(root = document) {
      const selects = root.querySelectorAll('select');
      selects.forEach(enhanceSelect);
    }

    function watchNewSelects() {
      const mo = new MutationObserver(mutations => {
        mutations.forEach(m => {
          m.addedNodes.forEach(node => {
            if (node.nodeType !== 1) return;
            if (node.tagName === 'SELECT') {
              enhanceSelect(node);
            } else if (node.querySelectorAll) {
              const found = node.querySelectorAll('select');
              if (found.length) found.forEach(enhanceSelect);
            }
          });
        });
      });
      mo.observe(document.body, { childList: true, subtree: true });
    }

    return {
      enhanceSelect,
      enhanceAll,
      watchNewSelects,
    };
  })();

  function initFilterSelect() {
    try {
      console.log('[FilterSelect] initFilterSelect called');
      FilterSelect.enhanceAll();
      FilterSelect.watchNewSelects();
    } catch (e) {
      console.error('FilterSelect init failed', e);
    }
  }

  // Panggil sekali saat DOM siap
  document.addEventListener('DOMContentLoaded', () => {
    console.log('[FilterSelect] DOMContentLoaded -> initFilterSelect');
    initFilterSelect();
  });

  // (Opsional) jaga-jaga kalau ada elemen yang baru muncul saat window load
  window.addEventListener('load', () => {
    console.log('[FilterSelect] window.load -> initFilterSelect (backup)');
    initFilterSelect();
  });

  window.App = {
    fetchJSON,
    toast,
    csrfName,
    csrfToken,
    setupFilter,
    filterSelect: FilterSelect,
  };
})();
