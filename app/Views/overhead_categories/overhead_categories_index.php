<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Kategori Overhead</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Kelola kategori biaya overhead (non gaji).
            </p>
        </div>
        <a href="<?= site_url('overhead-categories/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; text-decoration:none;">
            + Tambah
        </a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:rgba(122,154,108,0.14); border:1px solid var(--tr-primary); color:var(--tr-secondary-green); font-size:12px;">
            <?= session()->getFlashdata('message'); ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:var(--tr-secondary-beige); border:1px solid var(--tr-accent-brown); color:var(--tr-accent-brown); font-size:12px;">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:8px 0 0;">
            Belum ada kategori overhead.
        </p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin:0 0 10px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter nama atau status:</div>
            <input type="text" id="oc-filter" placeholder="Cari kategori..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:180px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Nama</th>
                <th style="text-align:center;padding:6px 8px; border-bottom:1px solid var(--tr-border);">Aktif</th>
                <th style="text-align:center;padding:6px 8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
            </tr>
            </thead>
            <tbody id="oc-table-body">
            <?php foreach ($rows as $row): ?>
                <tr data-name="<?= esc(strtolower($row['name'])); ?>" data-status="<?= (int)($row['is_active'] ?? 0) === 1 ? 'aktif' : 'nonaktif'; ?>">
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <?php $isActive = (int) ($row['is_active'] ?? 0) === 1; ?>
                        <span class="oc-status"
                              data-id="<?= $row['id']; ?>"
                              data-active="<?= $isActive ? '1' : '0'; ?>"
                              style="padding:2px 8px; border-radius:999px; border:1px solid <?= $isActive ? 'var(--tr-primary)' : 'var(--tr-accent-brown)'; ?>; background:<?= $isActive ? 'rgba(122,154,108,0.14)' : 'var(--tr-secondary-beige)'; ?>; color:<?= $isActive ? 'var(--tr-primary)' : 'var(--tr-accent-brown)'; ?>;">
                            <?= $isActive ? 'Aktif' : 'Nonaktif'; ?>
                        </span>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <div style="display:flex; justify-content:center; gap:6px; flex-wrap:wrap;">
                            <a href="<?= site_url('overhead-categories/edit/' . $row['id']); ?>"
                               style="display:inline-block; font-size:11px; padding:6px 12px; border-radius:999px; background:var(--tr-primary); color:#fff; text-decoration:none;">
                                Edit
                            </a>
                            <button type="button"
                                    class="btn-toggle-oc"
                                    data-id="<?= $row['id']; ?>"
                                    data-active="<?= $isActive ? '1' : '0'; ?>"
                                    style="font-size:11px; padding:6px 12px; border-radius:999px; border:1px solid var(--tr-border-soft, #e0d7c8); background:var(--tr-bg, #f4f1ea); color:var(--tr-text, #3a3a3a); cursor:pointer;">
                                <?= $isActive ? 'Nonaktifkan' : 'Aktifkan'; ?>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr id="oc-noresult" style="display:none;">
                <td colspan="3" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    (function() {
        const toggleUrl = "<?= site_url('overhead-categories/toggle'); ?>";
        const badgeColors = {
            active: {
                bg: 'rgba(122,154,108,0.14)',
                border: 'var(--tr-primary)',
                text: 'var(--tr-primary)',
                label: 'Aktif',
                button: 'Nonaktifkan',
            },
            inactive: {
                bg: 'var(--tr-secondary-beige)',
                border: 'var(--tr-accent-brown)',
                text: 'var(--tr-accent-brown)',
                label: 'Nonaktif',
                button: 'Aktifkan',
            },
        };

        function updateBadge(id, isActive) {
            const badge = document.querySelector('.oc-status[data-id="' + id + '"]');
            if (!badge) return;
            const cfg = isActive ? badgeColors.active : badgeColors.inactive;
            badge.dataset.active = isActive ? '1' : '0';
            badge.textContent = cfg.label;
            badge.style.background = cfg.bg;
            badge.style.borderColor = cfg.border;
            badge.style.color = cfg.text;
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-toggle-oc').forEach(function(btn) {
                btn.addEventListener('click', async function() {
                    const id = this.dataset.id;
                    if (!id || !window.App || !App.fetchJSON) return;
                    this.disabled = true;
                    this.textContent = 'Menyimpan...';
                    try {
                        const res = await App.fetchJSON(toggleUrl, { body: { id } });
                        const isActive = (res && res.is_active === 1) || res.is_active === '1';
                        updateBadge(id, isActive);
                        this.dataset.active = isActive ? '1' : '0';
                        this.textContent = isActive ? 'Nonaktifkan' : 'Aktifkan';
                        App.toast('Kategori diperbarui', 'info');
                    } catch (err) {
                        this.textContent = this.dataset.active === '1' ? 'Nonaktifkan' : 'Aktifkan';
                        const msg = err?.message || 'Gagal memperbarui kategori.';
                        App.toast(msg, 'error');
                    } finally {
                        this.disabled = false;
                    }
                });
            });
        });

        (function setupFilter() {
            const input = document.getElementById('oc-filter');
            const tbody = document.getElementById('oc-table-body');
            const nores = document.getElementById('oc-noresult');
            if (!input || !tbody) return;
            let timer;
            input.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    const q = (input.value || '').toLowerCase().trim();
                    let shown = 0;
                    tbody.querySelectorAll('tr').forEach(function(tr) {
                        if (tr.id === 'oc-noresult') return;
                        const name = tr.dataset.name || '';
                        const status = tr.dataset.status || '';
                        const match = !q || name.includes(q) || status.includes(q);
                        tr.style.display = match ? '' : 'none';
                        if (match) shown++;
                    });
                    if (nores) nores.style.display = shown === 0 ? '' : 'none';
                }, 200);
            });
        })();
    })();
</script>

<?= $this->endSection() ?>
