<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/branding.css'); ?>">

<?php
$pdfQuery = http_build_query(['export' => 'pdf']);
$pdfUrl = current_url() . ($pdfQuery ? '?' . $pdfQuery : '');
?>

<div class="tr-branding-actions">
    <div class="tr-branding-actions-inner">
        <div id="branding-slide-nav" class="tr-slide-nav-host"></div>
        <div class="tr-branding-actions-buttons">
            <a href="<?= esc($pdfUrl); ?>" class="tr-btn tr-btn-outline" style="text-decoration:none;">
                Export PDF
            </a>
        </div>
    </div>
</div>

<?= view('guides/branding_content', [
    'showExport' => false,
]) ?>

<script src="<?= base_url('js/branding.js'); ?>"></script>

<?= $this->endSection() ?>
