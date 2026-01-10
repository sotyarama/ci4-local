<?= $this->extend('layouts/app_shell') ?>

<?= $this->section('sidebar') ?>
<div style="padding:16px;">
    <div class="app-logo">
        <!-- logo / brand -->
    </div>

    <nav class="app-nav">
        <a class="app-navlink is-active" href="#">Dashboard</a>
        <a class="app-navlink" href="#">Sales</a>
        <a class="app-navlink" href="#">Transactions</a>
        <a class="app-navlink" href="#">Inventory</a>
        <!-- ...banyak item, nav akan scroll -->
    </nav>
</div>
<?= $this->endSection() ?>

<?= $this->section('topbar') ?>
<div style="padding:16px;">
    <strong>Topbar</strong> — App Shell Playground
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div style="padding:16px;">
    <strong>Content</strong> (scroll di sini)
    <div style="margin-top:12px;">
        <?php for ($i = 1; $i <= 60; $i++): ?>
            <div>Row <?= $i ?></div>
        <?php endfor; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('footer') ?>
<div style="padding:16px;">
    <strong>Footer</strong>
</div>
<?= $this->endSection() ?>