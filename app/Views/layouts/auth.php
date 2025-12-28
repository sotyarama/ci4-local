<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Login'); ?></title>

    <?php $assetVer = time(); ?>
    <link rel="stylesheet" href="<?= base_url('css/theme-temurasa.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/ui-baseline.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/auth.css') . '?v=' . $assetVer; ?>">
</head>

<body class="tr-auth">
    <?= $this->renderSection('content') ?>
</body>

</html>