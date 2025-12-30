<?= $this->extend('layouts/print') ?>
<?= $this->section('content') ?>

<style>
@media print {
    h2.tr-section-title {
        break-before: page;
        page-break-before: always;
        break-after: avoid;
        page-break-after: avoid;
    }
    h2.tr-section-title:first-of-type {
        break-before: auto;
        page-break-before: auto;
    }
    section.tr-section {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
</style>

<?= view('guides/branding_content', [
    'showExport' => false,
    'logoSrc' => $logoSrc ?? null,
]) ?>

<?= $this->endSection() ?>
