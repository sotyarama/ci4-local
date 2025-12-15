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

    <?php if (session()->getFlashdata('error')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:var(--tr-secondary-beige); border:1px solid var(--tr-accent-brown); color:var(--tr-accent-brown); font-size:12px;">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= esc($action); ?>" style="display:grid; gap:12px; max-width:520px;">
        <?= csrf_field(); ?>
        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="user_id" style="margin-bottom:4px; color:var(--tr-muted-text);">Staff</label>
            <select name="user_id" id="user_id"
                    style="padding:8px 10px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color:var(--tr-text); font-size:13px;">
                <option value="">Pilih staff</option>
                <?php foreach ($staff as $s): ?>
                    <option value="<?= $s['id']; ?>" <?= old('user_id', $payroll['user_id'] ?? '') == $s['id'] ? 'selected' : ''; ?>>
                        <?= esc($s['full_name'] ?? $s['username']); ?> (<?= esc($s['username']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; gap:10px;">
            <div style="flex:1; display:flex; flex-direction:column; font-size:12px;">
                <label for="period_month" style="margin-bottom:4px; color:var(--tr-muted-text);">Periode (YYYY-MM)</label>
                <input type="text" name="period_month" id="period_month" placeholder="2025-12"
                       value="<?= old('period_month', $payroll['period_month'] ?? ''); ?>"
                       style="padding:8px 10px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color:var(--tr-text); font-size:13px;">
            </div>
            <div style="flex:1; display:flex; flex-direction:column; font-size:12px;">
                <label for="pay_date" style="margin-bottom:4px; color:var(--tr-muted-text);">Tanggal Bayar</label>
                <input type="date" name="pay_date" id="pay_date"
                       value="<?= old('pay_date', $payroll['pay_date'] ?? ''); ?>"
                       style="padding:8px 10px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color:var(--tr-text); font-size:13px;">
            </div>
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="amount" style="margin-bottom:4px; color:var(--tr-muted-text);">Nominal</label>
            <input type="number" name="amount" id="amount" step="0.01" min="0"
                   value="<?= old('amount', $payroll['amount'] ?? ''); ?>"
                   style="padding:8px 10px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color:var(--tr-text); font-size:13px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="notes" style="margin-bottom:4px; color:var(--tr-muted-text);">Catatan</label>
            <textarea name="notes" id="notes" rows="3"
                      style="padding:8px 10px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color:var(--tr-text); font-size:13px;"><?= old('notes', $payroll['notes'] ?? ''); ?></textarea>
        </div>

        <div style="display:flex; gap:10px; align-items:center; margin-top:4px;">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('overheads/payroll'); ?>" class="btn btn-secondary" style="text-decoration:none;">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
