<header class="topbar">
    <div>
        <div class="topbar-title"><?= esc($title ?? ''); ?></div>
        <?php if (! empty($subtitle)): ?>
            <div class="topbar-subtitle"><?= esc($subtitle); ?></div>
        <?php endif; ?>
    </div>

    <div class="topbar-right">
        <span class="topbar-pill" style="margin-right: 8px;">
            <?= esc(session('full_name') ?? session('username') ?? ''); ?>
        </span>

        <button type="button" id="themeToggle" class="theme-toggle-btn" aria-label="Toggle theme" style="margin-right: 8px;">
            🌙
        </button>

        <a href="<?= site_url('logout'); ?>" style="font-size:12px; text-decoration:none; color:inherit;">
            Logout
        </a>
    </div>
</header>