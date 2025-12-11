<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <h1 style="margin: 0 0 8px; font-size: 20px;">Dashboard</h1>
    <p style="margin: 0 0 16px; font-size: 13px; color:var(--tr-muted-text);">
        Selamat datang di sistem POS café (local development).
    </p>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px,1fr)); gap:12px;">
        <div style="background:var(--tr-bg); border-radius:12px; padding:12px 14px; border:1px solid var(--tr-border);">
            <div style="font-size:11px; text-transform:uppercase; color:var(--tr-muted-text); letter-spacing:.08em;">
                Transaksi hari ini
            </div>
            <div style="margin-top:4px; font-size:18px;">0</div>
        </div>
        <div style="background:var(--tr-bg); border-radius:12px; padding:12px 14px; border:1px solid var(--tr-border);">
            <div style="font-size:11px; text-transform:uppercase; color:var(--tr-muted-text); letter-spacing:.08em;">
                Item terjual
            </div>
            <div style="margin-top:4px; font-size:18px;">0</div>
        </div>
        <div style="background:var(--tr-bg); border-radius:12px; padding:12px 14px; border:1px solid var(--tr-border);">
            <div style="font-size:11px; text-transform:uppercase; color:var(--tr-muted-text); letter-spacing:.08em;">
                Total omzet
            </div>
            <div style="margin-top:4px; font-size:18px;">Rp 0</div>
        </div>
    </div>

    <div style="margin-top:16px; font-size:11px; color:var(--tr-muted-text);">
        Angka di atas masih dummy. Nanti akan diisi dari database:
        penjualan, stok, HPP, dan overhead.
    </div>
</div>

<?= $this->endSection() ?>


