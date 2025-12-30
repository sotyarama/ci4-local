<header class="topbar">
    <div>
        <?php
        $hour = (int) date('G');
        $minute = (int) date('i');
        $minutes = $hour * 60 + $minute; // minutes since midnight

        // Ranges provided:
        // Pagi : 00:01 - 10:59
        // Siang : 11:00 - 14:59
        // Sore : 15:00 - 17:59
        // Petang : 18:00 - 18:59
        // Malam : 19:00 - 00:00 (and 00:00)

        if ($minutes >= 1 && $minutes <= (10 * 60 + 59)) {
            $greeting = 'Selamat Pagi';
        } elseif ($minutes >= (11 * 60) && $minutes <= (14 * 60 + 59)) {
            $greeting = 'Selamat Siang';
        } elseif ($minutes >= (15 * 60) && $minutes <= (17 * 60 + 59)) {
            $greeting = 'Selamat Sore';
        } elseif ($minutes >= (18 * 60) && $minutes <= (18 * 60 + 59)) {
            $greeting = 'Selamat Petang';
        } else {
            $greeting = 'Selamat Malam';
        }
        ?>

        <div class="topbar-greeting">
            <div id="topbar-greeting-text" class="greeting-text"><?= esc($greeting); ?></div>
            <div class="greeting-datetime">
                <span id="topbar-date"><?= date('d M Y'); ?></span>
                <span id="topbar-time"><?= date('H:i:s'); ?></span>
            </div>
        </div>

        <script>
            (function() {
                function updateDateTime() {
                    var now = new Date();
                    var dateStr = now.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    var timeStr = now.toLocaleTimeString('id-ID');
                    var d = document.getElementById('topbar-date');
                    var t = document.getElementById('topbar-time');
                    if (d) d.textContent = dateStr;
                    if (t) t.textContent = timeStr;
                    // update greeting according to same ranges
                    var g = document.getElementById('topbar-greeting-text');
                    if (g) {
                        var minutes = now.getHours() * 60 + now.getMinutes();
                        var greeting = 'Selamat Malam';
                        if (minutes >= 1 && minutes <= (10 * 60 + 59)) {
                            greeting = 'Selamat Pagi';
                        } else if (minutes >= (11 * 60) && minutes <= (14 * 60 + 59)) {
                            greeting = 'Selamat Siang';
                        } else if (minutes >= (15 * 60) && minutes <= (17 * 60 + 59)) {
                            greeting = 'Selamat Sore';
                        } else if (minutes >= (18 * 60) && minutes <= (18 * 60 + 59)) {
                            greeting = 'Selamat Petang';
                        } else {
                            greeting = 'Selamat Malam';
                        }
                        g.textContent = greeting;
                    }
                }
                updateDateTime();
                setInterval(updateDateTime, 1000);
            })();
        </script>
    </div>

    <div class="topbar-right">
        <a href="<?= site_url('branding'); ?>" class="theme-toggle-btn topbar-pill" style="text-decoration:none;">
            Branding
        </a>
        <a href="<?= site_url('how-to-use'); ?>" class="theme-toggle-btn topbar-pill" style="text-decoration:none;">
            How to Use
        </a>
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
