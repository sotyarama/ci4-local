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
        <span class="pill">Help</span>
    </div>

    <div style="font-size:13px; line-height:1.6; color:var(--tr-text);">
        <p style="margin:0 0 12px;">
            Panduan ringkas ini merangkum cara menggunakan aplikasi berdasarkan struktur menu saat ini.
        </p>

        <h3 style="margin:0 0 6px; font-size:14px;">Quick Start (Harian)</h3>
        <ol style="margin:0 0 12px 18px; padding:0; list-style:decimal;">
            <li>Login dengan akun masing-masing agar menu sesuai role muncul.</li>
            <li>Cek ringkasan performa di Dashboard sebelum buka kasir.</li>
            <li>Pastikan data Master sudah lengkap (produk, bahan baku, kategori, resep).</li>
            <li>Catat pembelian bahan masuk pada menu Pembelian Bahan.</li>
            <li>Gunakan POS Penjualan untuk transaksi dan pantau Kitchen Queue.</li>
            <li>Tinjau laporan penjualan dan stok untuk evaluasi.</li>
        </ol>

        <h3 style="margin:0 0 6px; font-size:14px;">Panduan Menu</h3>

        <div style="margin:0 0 10px;">
            <strong>Main</strong>
            <ul style="margin:4px 0 0 18px; padding:0; list-style:disc;">
                <li><strong>Dashboard</strong> - ringkasan KPI dan info operasional hari ini.</li>
                <li><strong>Main Sales UI</strong> - tampilan POS touchscreen (preview/stub).</li>
            </ul>
        </div>

        <div style="margin:0 0 10px;">
            <strong>Master</strong>
            <ul style="margin:4px 0 0 18px; padding:0; list-style:disc;">
                <li><strong>Menu / Produk</strong> - data produk dan harga jual.</li>
                <li><strong>Menu Options</strong> - opsi tambahan untuk menu.</li>
                <li><strong>Kategori Menu</strong> - grouping menu agar rapi di POS.</li>
                <li><strong>Bahan Baku</strong> - stok bahan dan satuan.</li>
                <li><strong>Supplier</strong> - daftar pemasok bahan.</li>
                <li><strong>Customer</strong> - data pelanggan (opsional).</li>
                <li><strong>Resep</strong> - komposisi bahan per menu.</li>
                <li><strong>Audit Log</strong> - jejak aktivitas utama.</li>
            </ul>
        </div>

        <div style="margin:0 0 10px;">
            <strong>Transaksi</strong>
            <ul style="margin:4px 0 0 18px; padding:0; list-style:disc;">
                <li><strong>Pembelian Bahan</strong> - catat pembelian dan stok masuk.</li>
                <li><strong>POS Penjualan</strong> - transaksi kasir, invoice, dan detail penjualan.</li>
                <li><strong>Kitchen Queue</strong> - pantau antrian pesanan dapur.</li>
            </ul>
        </div>

        <div style="margin:0 0 10px;">
            <strong>Inventory</strong>
            <ul style="margin:4px 0 0 18px; padding:0; list-style:disc;">
                <li><strong>Riwayat Stok (IN/OUT)</strong> - histori pergerakan stok.</li>
                <li><strong>Kartu Stok per Bahan</strong> - detail stok per item bahan.</li>
                <li><strong>Stock Adjustment</strong> - placeholder penyesuaian stok manual.</li>
                <li><strong>Stock & Selisih Fisik</strong> - placeholder stock opname.</li>
            </ul>
        </div>

        <div style="margin:0 0 10px;">
            <strong>Laporan</strong>
            <ul style="margin:4px 0 0 18px; padding:0; list-style:disc;">
                <li><strong>Penjualan by Time</strong> - tren penjualan per waktu.</li>
                <li><strong>Penjualan per Menu</strong> - performa tiap menu.</li>
                <li><strong>Penjualan per Kategori</strong> - ringkasan per kategori.</li>
                <li><strong>Penjualan per Customer</strong> - riwayat penjualan pelanggan.</li>
                <li><strong>Pembelian per Supplier</strong> - ringkasan pembelian pemasok.</li>
                <li><strong>Pembelian per Bahan</strong> - pembelian per item bahan.</li>
                <li><strong>Stok & Selisih</strong> - laporan variansi stok.</li>
            </ul>
        </div>

        <div style="margin:0 0 10px;">
            <strong>Overhead</strong>
            <ul style="margin:4px 0 0 18px; padding:0; list-style:disc;">
                <li><strong>Biaya Overhead</strong> - catatan biaya operasional.</li>
                <li><strong>Kategori Overhead</strong> - kelompok biaya.</li>
                <li><strong>Overhead (Payroll)</strong> - biaya payroll.</li>
            </ul>
        </div>

        <div style="margin:0 0 10px;">
            <strong>Settings</strong>
            <ul style="margin:4px 0 0 18px; padding:0; list-style:disc;">
                <li><strong>User Management</strong> - kelola akun (owner/auditor).</li>
            </ul>
        </div>

        <h3 style="margin:0 0 6px; font-size:14px;">Catatan</h3>
        <ul style="margin:0 0 10px 18px; padding:0; list-style:disc;">
            <li>Menu bisa tersembunyi sesuai role. Jika tidak terlihat, hubungi owner/admin.</li>
            <li>Halaman bertanda placeholder belum memiliki form/aksi final.</li>
            <li>Gunakan Logout di topbar setelah selesai bekerja.</li>
        </ul>

        <p style="margin:0; color:var(--tr-muted-text);">
            Konten ini bersifat read-only dan bisa diperbarui sesuai SOP terbaru.
        </p>
    </div>
</div>

<?= $this->endSection() ?>
