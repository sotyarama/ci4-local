<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/branding.css'); ?>">

<!-- PDF export intentionally disabled for Branding/How-To modules — web-only content. -->
<div class="tr-branding-actions">
    <div class="tr-branding-actions-inner">
        <div id="branding-slide-nav" class="tr-slide-nav-host"></div>
        <!-- actions intentionally omitted: no PDF export -->
    </div>
</div>

<?= view('guides/branding_content', [
    'showExport' => false,
]) ?>

<script src="<?= base_url('js/branding.js?v=1cc2d2e'); ?>"></script>

<?= $this->endSection() ?>
