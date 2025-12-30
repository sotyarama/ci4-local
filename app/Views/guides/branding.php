<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/branding.css'); ?>">

<?php
$pdfQuery = http_build_query(['export' => 'pdf']);
$pdfUrl = current_url() . ($pdfQuery ? '?' . $pdfQuery : '');
$printQuery = http_build_query(['export' => 'print']);
$printUrl = current_url() . ($printQuery ? '?' . $printQuery : '');
?>

<?= view('guides/branding_content', [
    'showExport' => true,
    'pdfUrl' => $pdfUrl,
    'printUrl' => $printUrl,
]) ?>

<script src="<?= base_url('js/branding.js'); ?>"></script>

<?= $this->endSection() ?>
