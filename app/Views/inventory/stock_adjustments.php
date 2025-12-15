<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;"><?= esc($title); ?></h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                <?= esc($subtitle); ?>
            </p>
        </div>
        <span class="pill">Planned</span>
    </div>

    <div style="font-size:13px; line-height:1.5; color:var(--tr-text);">
        <p style="margin:0 0 8px;">
            Placeholder module penyesuaian stok manual. Rencana fitur:
        </p>
        <ul style="margin:0 0 10px 18px; padding:0; list-style:disc;">
            <li>Input bahan, qty selisih (+/-), alasan, dan lampiran referensi.</li>
            <li>Generate movement `ADJ` yang memperbarui `raw_materials.current_stock`.</li>
            <li>Optional: HPP penyesuaian pakai `cost_avg` terakhir.</li>
            <li>Audit log penyesuaian termasuk user & timestamp.</li>
        </ul>
        <p style="margin:0; color:var(--tr-muted-text);">
            UI/aksi belum dibuat; stub ini untuk memastikan rute & menu hidup.
        </p>
    </div>
</div>

<?= $this->endSection() ?>
