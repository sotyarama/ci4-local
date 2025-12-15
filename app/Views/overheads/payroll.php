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

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">
            <div style="display:flex; flex-direction:column; font-size:12px;">
                <label for="filter_staff" style="margin-bottom:2px; color:var(--tr-muted-text);">Staff</label>
                <select id="filter_staff" onchange="location.href='<?= current_url(); ?>?staff_id='+this.value+'&period=<?= esc($filterPeriod); ?>'" style="min-width:160px; padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color:var(--tr-text); font-size:12px;">
                    <option value="0">Semua staff</option>
                    <?php foreach ($staff as $s): ?>
                        <option value="<?= $s['id']; ?>" <?= ((int)$filterStaff === (int)$s['id']) ? 'selected' : ''; ?>>
                            <?= esc($s['full_name'] ?? $s['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex; flex-direction:column; font-size:12px;">
                <label for="filter_period" style="margin-bottom:2px; color:var(--tr-muted-text);">Periode (YYYY-MM)</label>
                <input type="text" id="filter_period" value="<?= esc($filterPeriod); ?>"
                       onkeydown="if(event.key==='Enter'){location.href='<?= current_url(); ?>?staff_id=<?= esc($filterStaff); ?>&period='+encodeURIComponent(this.value);}"
                       placeholder="2025-12"
                       style="padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color:var(--tr-text); font-size:12px; min-width:120px;">
            </div>
            <a href="<?= current_url(); ?>?staff_id=<?= $filterStaff; ?>&period=" style="font-size:12px; color:var(--tr-muted-text); text-decoration:none; margin-bottom:4px;">Reset</a>
        </div>
        <a href="<?= site_url('overheads/payroll/create'); ?>" class="btn btn-primary">+ Tambah Payroll</a>
    </div>

    <div class="table-scroll-wrap">
        <table>
            <thead>
                <tr>
                    <th style="min-width:160px;">Staff</th>
                    <th style="min-width:90px;">Periode</th>
                    <th style="min-width:110px;">Tanggal Bayar</th>
                    <th style="min-width:120px;">Nominal</th>
                    <th style="min-width:200px;">Catatan</th>
                    <th style="min-width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payrolls)): ?>
                    <tr><td colspan="6" style="text-align:center; padding:10px; color:var(--tr-muted-text);">Belum ada data payroll.</td></tr>
                <?php else: ?>
                    <?php foreach ($payrolls as $row): ?>
                        <tr>
                            <td><?= esc($row['staff_name'] ?? ''); ?> <span class="muted">(<?= esc($row['staff_username'] ?? ''); ?>)</span></td>
                            <td><?= esc($row['period_month'] ?? ''); ?></td>
                            <td><?= esc($row['pay_date'] ?? '-'); ?></td>
                            <td>Rp <?= number_format((float) ($row['amount'] ?? 0), 0, ',', '.'); ?></td>
                            <td><?= esc($row['notes'] ?? '-'); ?></td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a href="<?= site_url('overheads/payroll/edit/' . $row['id']); ?>" class="btn btn-secondary" style="padding:6px 10px; border-radius:8px; font-size:12px;">Edit</a>
                                    <form method="post" action="<?= site_url('overheads/payroll/delete/' . $row['id']); ?>" onsubmit="return confirm('Hapus payroll ini?');">
                                        <?= csrf_field(); ?>
                                        <button type="submit" class="btn btn-secondary" style="padding:6px 10px; border-radius:8px; font-size:12px; background:var(--tr-accent-brown); border-color:var(--tr-accent-brown); color:#fff;">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p style="margin-top:10px; font-size:12px; color:var(--tr-muted-text);">
        Payroll hanya dapat diinput oleh Owner. Data staff diambil dari tabel users (role Staff).
    </p>
</div>

<?= $this->endSection() ?>
