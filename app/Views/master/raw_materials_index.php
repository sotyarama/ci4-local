<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Raw Materials - Index
 * - Fokus: minim inline style, konsisten dengan theme-temurasa.css
 * - Tetap pakai App.setupFilter (app.js)
 */
$fmtQty3  = static fn($v): string => number_format((float) ($v ?? 0), 3, ',', '.');
$fmtMoney = static fn($v): string => number_format((float) ($v ?? 0), 0, ',', '.');
?>

<div class="card">

    <div class="page-head">
        <div>
            <h2 class="page-title">Master Bahan Baku</h2>
            <p class="page-subtitle">Daftar bahan baku untuk resep dan pengelolaan stok.</p>
        </div>

        <a href="<?= site_url('master/raw-materials/create'); ?>" class="btn btn-primary btn-sm">
            + Tambah Bahan
        </a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('message')); ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($materials)): ?>
        <p class="empty-state">Belum ada data bahan baku. Silakan tambahkan data baru.</p>
    <?php else: ?>

        <div class="table-tools">
            <div class="table-tools__hint">Filter nama/satuan/status:</div>
            <input
                type="text"
                id="rm-filter"
                class="table-tools__search"
                placeholder="Cari bahan baku...">
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th class="table__th">Nama Bahan</th>
                    <th class="table__th">Satuan</th>
                    <th class="table__th table__th--right">Stok Saat Ini</th>
                    <th class="table__th table__th--right">Min Stok</th>
                    <th class="table__th table__th--right">Last Cost</th>
                    <th class="table__th table__th--right">Avg Cost</th>
                    <th class="table__th table__th--center">Status</th>
                    <th class="table__th table__th--center">Aksi</th>
                </tr>
            </thead>

            <tbody id="rm-table-body">
                <?php foreach ($materials as $m): ?>
                    <?php
                    $id       = (int) ($m['id'] ?? 0);
                    $name     = (string) ($m['name'] ?? '');
                    $unit     = (string) ($m['unit_short'] ?? $m['unit_name'] ?? '');
                    $isActive = ! empty($m['is_active']);
                    $status   = $isActive ? 'aktif' : 'nonaktif';

                    $currentStock = (float) ($m['current_stock'] ?? 0);
                    $minStock     = (float) ($m['min_stock'] ?? 0);
                    $isLow        = ($minStock > 0) && ($currentStock < $minStock);

                    $costLast = (float) ($m['cost_last'] ?? 0);
                    $costAvg  = (float) ($m['cost_avg'] ?? 0);
                    ?>
                    <tr
                        data-name="<?= esc(strtolower($name)); ?>"
                        data-unit="<?= esc(strtolower($unit)); ?>"
                        data-status="<?= esc($status); ?>">

                        <td class="table__td"><?= esc($name !== '' ? $name : '-'); ?></td>
                        <td class="table__td"><?= esc($unit !== '' ? $unit : '-'); ?></td>

                        <td class="table__td table__td--right">
                            <?= $fmtQty3($currentStock); ?>

                            <?php if ($isLow): ?>
                                <span class="badge badge--low">Low</span>
                            <?php endif; ?>
                        </td>

                        <td class="table__td table__td--right"><?= $fmtQty3($minStock); ?></td>

                        <td class="table__td table__td--right">Rp <?= $fmtMoney($costLast); ?></td>
                        <td class="table__td table__td--right">Rp <?= $fmtMoney($costAvg); ?></td>

                        <td class="table__td table__td--center">
                            <?php if ($isActive): ?>
                                <span class="badge badge--active">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge--inactive">Nonaktif</span>
                            <?php endif; ?>
                        </td>

                        <td class="table__td table__td--center">
                            <div class="row-actions">
                                <a href="<?= site_url('master/raw-materials/edit/' . $id); ?>" class="btn btn-primary btn-sm">
                                    Edit
                                </a>

                                <form
                                    action="<?= site_url('master/raw-materials/delete/' . $id); ?>"
                                    method="post"
                                    class="inline"
                                    onsubmit="return confirm('Yakin ingin menghapus bahan ini?');">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr id="rm-noresult" style="display:none;">
                    <td colspan="8" class="table__td table__td--center muted">Tidak ada hasil.</td>
                </tr>
            </tbody>
        </table>

        <div class="footnote">
            Data ini akan digunakan pada modul Resep, Pembelian, dan Stock Movement.
        </div>

    <?php endif; ?>

</div>

<script>
    (function() {
        function initFilter() {
            if (!window.App || !App.setupFilter) return setTimeout(initFilter, 50);

            App.setupFilter({
                input: '#rm-filter',
                rows: document.querySelectorAll('#rm-table-body tr:not(#rm-noresult)'),
                noResult: '#rm-noresult',
                fields: ['name', 'unit', 'status'],
                debounce: 200
            });
        }

        document.addEventListener('DOMContentLoaded', initFilter);
    })();
</script>

<?= $this->endSection() ?>