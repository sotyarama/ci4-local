<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h2 style="margin:0; font-size:18px;">Master Supplier</h2>
        <a href="<?= site_url('master/suppliers/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; text-decoration:none;">
            + Tambah Supplier
        </a>
    </div>

    <p style="margin:0 0 16px; font-size:13px; color:var(--tr-muted-text);">
        Daftar supplier bahan baku untuk pembelian.
    </p>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="background:rgba(122,154,108,0.14); border-radius:8px; padding:8px 10px; border:1px solid var(--tr-primary); font-size:12px; color:var(--tr-secondary-green); margin-bottom:12px;">
            <?= esc(session()->getFlashdata('message')); ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:var(--tr-accent-brown); border-radius:8px; padding:8px 10px; border:1px solid var(--tr-accent-brown); font-size:12px; color:var(--tr-secondary-beige); margin-bottom:12px;">
            <?= esc(session()->getFlashdata('error')); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($suppliers)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:0;">
            Belum ada data supplier. Silakan tambahkan data baru.
        </p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter nama/telp/status:</div>
            <input type="text" id="suppliers-filter" placeholder="Cari supplier..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:200px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:8px; border-bottom:1px solid var(--tr-border);">Nama</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid var(--tr-border);">Telepon</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid var(--tr-border);">Alamat</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid var(--tr-border);">Status</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
            </tr>
            </thead>
            <tbody id="suppliers-table-body">
            <?php foreach ($suppliers as $s): ?>
                <?php $isActive = !empty($s['is_active']); ?>
                <tr data-name="<?= esc(strtolower($s['name'])); ?>" data-phone="<?= esc(strtolower($s['phone'] ?? '')); ?>" data-status="<?= $isActive ? 'aktif' : 'nonaktif'; ?>">
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($s['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($s['phone'] ?? ''); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); font-size:11px; color:var(--tr-muted-text);">
                        <?= nl2br(esc($s['address'] ?? '')); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <?php if (!empty($s['is_active'])): ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:rgba(122,154,108,0.14); color:var(--tr-secondary-green); border:1px solid rgba(122,154,108,0.14);">
                                Aktif
                            </span>
                        <?php else: ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:var(--tr-secondary-beige); color:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown);">
                                Nonaktif
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <a href="<?= site_url('master/suppliers/edit/' . $s['id']); ?>"
                           style="font-size:11px; margin-right:6px; color:#fff; text-decoration:none; background:var(--tr-primary); border:1px solid var(--tr-primary); padding:6px 10px; border-radius:999px;">
                            Edit
                        </a>

                        <form action="<?= site_url('master/suppliers/delete/' . $s['id']); ?>"
                              method="post"
                              style="display:inline;"
                              onsubmit="return confirm('Yakin ingin menghapus supplier ini?');">
                            <?= csrf_field(); ?>
                            <button type="submit"
                                    style="font-size:11px; border:1px solid var(--tr-accent-brown); background:var(--tr-accent-brown); color:#fff; cursor:pointer; padding:6px 10px; border-radius:999px;">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr id="suppliers-noresult" style="display:none;">
                <td colspan="5" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    (function() {
        function init() {
            if (!window.App || !App.setupFilter) {
                return setTimeout(init, 50);
            }
            App.setupFilter({
                input: '#suppliers-filter',
                rows: document.querySelectorAll('#suppliers-table-body tr:not(#suppliers-noresult)'),
                noResult: '#suppliers-noresult',
                fields: ['name','phone','status'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>
