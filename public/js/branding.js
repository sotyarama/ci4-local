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
