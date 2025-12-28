<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Cafe POS'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        (function() {
            try {
                var saved = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', saved);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <link rel="stylesheet" href="<?= base_url('css/theme-temurasa.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/ui-baseline.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/layout.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/ui/buttons.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/ui/cards.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/ui/forms.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/ui/tables.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/sidebar-nav.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/pos-touch.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
</head>