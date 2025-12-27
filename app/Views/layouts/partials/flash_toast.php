<?php if (! empty($flashError)): ?>
    <div id="flash-error"
        style="position:fixed; top:18px; left:50%; transform:translateX(-50%); z-index:9999;
            background:var(--tr-secondary-beige); border:1px solid var(--tr-accent-brown); color:var(--tr-text);
            padding:14px 18px; border-radius:12px; box-shadow:0 16px 40px rgba(58,58,58,0.24);
            font-size:13px; max-width:520px; width:calc(100% - 32px); text-align:center;">
        <?= esc($flashError); ?>
    </div>
    <script>
        setTimeout(function() {
            var el = document.getElementById('flash-error');
            if (el) {
                el.style.transition = 'opacity 0.6s ease';
                el.style.opacity = '0';
                setTimeout(function() {
                    el.remove();
                }, 700);
            }
        }, 4500);
    </script>
<?php endif; ?>