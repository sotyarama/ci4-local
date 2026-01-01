// Copy HEX on click (optional, harmless).
(function () {
  var cards = document.querySelectorAll('.tr-color-card');
  if (!cards.length) return;

  var hint = document.getElementById('hexHint');
  var val = document.getElementById('hexValue');
  var timer = null;

  function showHint(hex) {
    if (!hint || !val) return;
    val.textContent = hex;
    hint.style.display = 'block';
    clearTimeout(timer);
    timer = setTimeout(function () { hint.style.display = 'none'; }, 1200);
  }

  cards.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var hex = btn.getAttribute('data-hex') || '';
      if (!hex) return;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(hex).then(function () { showHint(hex); });
      } else {
        // Fallback.
        var tmp = document.createElement('textarea');
        tmp.value = hex;
        document.body.appendChild(tmp);
        tmp.select();
        try { document.execCommand('copy'); } catch (e) {}
        document.body.removeChild(tmp);
        showHint(hex);
      }
    });
  });
})();

// Branding slides (desktop only).
(function () {
  if (!window.matchMedia || !window.matchMedia('(min-width: 980px)').matches) {
    return;
  }

  var container = document.querySelector('.tr-branding-container');
  if (!container) return;

  var children = Array.prototype.slice.call(container.children);
  if (!children.length) return;

  var slides = [];
  var current = document.createElement('div');
  current.className = 'tr-slide';
  slides.push(current);

  children.forEach(function (node) {
    if (node.matches && node.matches('section.tr-section')) {
      var mode = node.getAttribute('data-slide') || '';
      if (mode !== 'continue' && current.childNodes.length) {
        current = document.createElement('div');
        current.className = 'tr-slide';
        slides.push(current);
      }
    }

    current.appendChild(node);
  });

  container.innerHTML = '';
  container.classList.add('tr-slide-mode');

  var nav = document.createElement('div');
  nav.className = 'tr-slide-nav';

  var btnPrev = document.createElement('button');
  btnPrev.type = 'button';
  btnPrev.className = 'tr-btn tr-btn-outline';
  btnPrev.textContent = 'Prev';

  var indicator = document.createElement('div');
  indicator.className = 'tr-slide-indicator';

  var btnNext = document.createElement('button');
  btnNext.type = 'button';
  btnNext.className = 'tr-btn tr-btn-outline';
  btnNext.textContent = 'Next';

  nav.appendChild(btnPrev);
  nav.appendChild(indicator);
  nav.appendChild(btnNext);

  var navHost = document.getElementById('branding-slide-nav');
  if (navHost) {
    navHost.appendChild(nav);
  } else {
    container.appendChild(nav);
  }
  slides.forEach(function (slide) {
    container.appendChild(slide);
  });

  var index = 0;

  function setActive(next) {
    if (next < 0 || next >= slides.length) return;
    slides[index].classList.remove('is-active');
    index = next;
    slides[index].classList.add('is-active');
    indicator.textContent = 'Slide ' + (index + 1) + ' / ' + slides.length;
  }

  slides[index].classList.add('is-active');
  indicator.textContent = 'Slide 1 / ' + slides.length;

  btnPrev.addEventListener('click', function () {
    setActive(index - 1);
  });

  btnNext.addEventListener('click', function () {
    setActive(index + 1);
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'ArrowRight' || event.key === 'PageDown') {
      setActive(index + 1);
    }
    if (event.key === 'ArrowLeft' || event.key === 'PageUp') {
      setActive(index - 1);
    }
  });
})();

(function () {
  var page = document.querySelector('.tr-branding-page');
  var actions = document.querySelector('.tr-branding-actions-inner');
  if (!page || !actions) return;

  function syncWidth() {
    var rect = page.getBoundingClientRect();
    var parentRect = actions.parentElement
      ? actions.parentElement.getBoundingClientRect()
      : { left: 0 };
    actions.style.width = rect.width + 'px';
    actions.style.marginLeft = (rect.left - parentRect.left) + 'px';
    actions.style.marginRight = '0';
  }

  if (window.ResizeObserver) {
    var observer = new ResizeObserver(syncWidth);
    observer.observe(page);
  } else {
    window.addEventListener('resize', syncWidth);
  }

  window.addEventListener('load', syncWidth);
  syncWidth();
})();

(function () {
  var box = document.querySelector('.tr-clearspace-box');
  if (!box) return;

  var inner = box.querySelector('.tr-clearspace-inner');
  var svg = box.querySelector('.tr-clearspace-label svg');
  if (!inner || !svg) return;

  var circleSelectors = ['#path17', '#path15'];

  function updateInset() {
    var maxHeight = 0;
    circleSelectors.forEach(function (selector) {
      var node = svg.querySelector(selector);
      if (!node || !node.getBoundingClientRect) return;
      var rect = node.getBoundingClientRect();
      if (rect.height > maxHeight) {
        maxHeight = rect.height;
      }
    });

    if (maxHeight > 0) {
      var insetValue = maxHeight.toFixed(1) + 'px';
      inner.style.inset = insetValue;
      box.style.setProperty('--tr-clearspace-inset', insetValue);
    }
  }

  function scheduleUpdate() {
    window.requestAnimationFrame(updateInset);
  }

  if (window.ResizeObserver) {
    var observer = new ResizeObserver(scheduleUpdate);
    observer.observe(box);
  } else {
    window.addEventListener('resize', scheduleUpdate);
  }

  window.addEventListener('load', scheduleUpdate);
  scheduleUpdate();
})();
