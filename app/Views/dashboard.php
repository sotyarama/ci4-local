<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <h1 style="margin: 0 0 8px; font-size: 20px;">Dashboard</h1>
    <p style="margin: 0 0 16px; font-size: 13px; color:#9ca3af;">
        Selamat datang di sistem POS café (local development).
    </p>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px,1fr)); gap:12px;">
        <div style="background:#020617; border-radius:12px; padding:12px 14px; border:1px solid #1f2937;">
            <div style="font-size:11px; text-transform:uppercase; color:#9ca3af; letter-spacing:.08em;">
                Transaksi hari ini
            </div>
            <div style="margin-top:4px; font-size:18px;">0</div>
        </div>
        <div style="background:#020617; border-radius:12px; padding:12px 14px; border:1px solid #1f2937;">
            <div style="font-size:11px; text-transform:uppercase; color:#9ca3af; letter-spacing:.08em;">
                Item terjual
            </div>
            <div style="margin-top:4px; font-size:18px;">0</div>
        </div>
        <div style="background:#020617; border-radius:12px; padding:12px 14px; border:1px solid #1f2937;">
            <div style="font-size:11px; text-transform:uppercase; color:#9ca3af; letter-spacing:.08em;">
                Total omzet
            </div>
            <div style="margin-top:4px; font-size:18px;">Rp 0</div>
        </div>
    </div>

    <div style="margin-top:16px; font-size:11px; color:#6b7280;">
        Angka di atas masih dummy. Nanti akan diisi dari database:
        penjualan, stok, HPP, dan overhead.
    </div>
</div>

<?= $this->endSection() ?>
