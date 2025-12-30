<?= $this->extend('guides/pdf/layout') ?>

<?= $this->section('content') ?>
<?= view('guides/branding_content', [
    'showExport' => false,
    'logoSrc' => $logoSrc ?? null,
]) ?>
<?= $this->endSection() ?>
