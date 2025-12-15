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
            Stub halaman opname. Draft scope berikut akan diisi di tahap implementasi:
        </p>
        <ul style="margin:0 0 10px 18px; padding:0; list-style:disc;">
            <li>Entry hasil hitung fisik per bahan per tanggal opname.</li>
            <li>Hitung selisih vs stok sistem + flag shortage/excess.</li>
            <li>Opsional auto-create penyesuaian stok berdasarkan selisih.</li>
            <li>Download/print daftar opname untuk verifikasi fisik.</li>
        </ul>
        <p style="margin:0; color:var(--tr-muted-text);">
            Rute & tampilan sudah aktif sebagai placeholder, belum ada form/aksi.
        </p>
    </div>
</div>

<?= $this->endSection() ?>
