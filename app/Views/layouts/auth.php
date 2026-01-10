<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Login'); ?></title>

    <?php $assetVer = time(); ?>
    <link rel="stylesheet" href="<?= base_url('css/00-theme/theme-temurasa.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/20-base/ui-baseline.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/60-pages/auth.css') ?>">

</head>

<body class="tr-auth">
    <?= $this->renderSection('content') ?>
</body>

</html>