<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php $errors = session('errors') ?? []; ?>

<div class="card">
    <h2 style="margin:0 0 8px; font-size:18px;">Tambah Pembelian</h2>
    <p style="margin:0 0 16px; font-size:13px; color:var(--tr-muted-text);">
        Input pembelian bahan baku. Minimal satu baris item diisi.
    </p>

    <?php if (!empty($errors)): ?>
        <div style="background:var(--tr-accent-brown); border-radius:8px; padding:8px 10px; border:1px solid var(--tr-accent-brown); font-size:12px; color:var(--tr-secondary-beige); margin-bottom:12px;">
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin:4px 0 0 16px; padding:0;">
                <?php foreach ($errors as $e): ?>
                    <li><?= esc($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('purchases/store'); ?>" method="post">
        <?= csrf_field(); ?>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px; margin-bottom:16px;">
            <div>
                <label style="font-size:12px; display:block; margin-bottom:4px;">Supplier</label>
                <select name="supplier_id"
                        style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:13px;"
                        required>
                    <option value="">-- pilih supplier --</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id']; ?>"
                            <?= (string) old('supplier_id') === (string) $s['id'] ? 'selected' : ''; ?>>
                            <?= esc($s['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label style="font-size:12px; display:block; margin-bottom:4px;">Tanggal</label>
                <input type="date"
                       name="purchase_date"
                       value="<?= esc(old('purchase_date', date('Y-m-d'))); ?>"
                       style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:13px;"
                       required>
            </div>

            <div>
                <label style="font-size:12px; display:block; margin-bottom:4px;">No. Invoice (opsional)</label>
                <input type="text"
                       name="invoice_no"
                       value="<?= esc(old('invoice_no', '')); ?>"
                       style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:13px;">
            </div>
        </div>

        <div style="margin-bottom:12px;">
            <label style="font-size:12px; display:block; margin-bottom:4px;">Catatan (opsional)</label>
            <textarea name="notes"
                      rows="2"
                      style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:13px;"><?= esc(old('notes', '')); ?></textarea>
        </div>

        <h3 style="margin:0 0 8px; font-size:14px;">Detail Item</h3>
        <p style="margin:0 0 8px; font-size:11px; color:var(--tr-muted-text);">
            Isi baris yang diperlukan saja, baris kosong akan diabaikan.
        </p>

        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Bahan</th>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Brand</th>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Varian</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Qty</th>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Satuan</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Harga / Satuan</th>
            </tr>
            </thead>
            <tbody>
            <?php for ($i = 0; $i < 5; $i++): ?>
                <?php
                $oldItems = old('items', []);
                $row = $oldItems[$i] ?? ['raw_material_id' => '', 'raw_material_variant_id' => '', 'qty' => '', 'unit_cost' => ''];
                ?>
                <tr>
                    <td style="padding:4px 8px; border-bottom:1px solid var(--tr-border);">
                        <select name="items[<?= $i; ?>][raw_material_id]"
                                class="item-raw"
                                style="width:100%; padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                            <option value="">-- pilih bahan --</option>
                            <?php foreach ($materials as $m): ?>
                                <option value="<?= $m['id']; ?>"
                                    data-unit="<?= esc($m['unit_short'] ?? ''); ?>"
                                    data-has-variants="<?= esc($m['has_variants'] ?? 0); ?>"
                                    <?= (string) ($row['raw_material_id'] ?? '') === (string) $m['id'] ? 'selected' : ''; ?>>
                                    <?= esc($m['name']); ?> (<?= esc($m['unit_short'] ?? ''); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td style="padding:4px 8px; border-bottom:1px solid var(--tr-border);">
                        <select class="item-brand"
                                style="width:100%; padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                            <option value="">-- pilih brand --</option>
                            <?php foreach (($brands ?? []) as $b): ?>
                                <option value="<?= $b['id']; ?>"
                                    data-raw="<?= esc($b['raw_material_id'] ?? ''); ?>">
                                    <?= esc($b['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td style="padding:4px 8px; border-bottom:1px solid var(--tr-border);">
                        <select name="items[<?= $i; ?>][raw_material_variant_id]"
                                class="item-variant"
                                style="width:100%; padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                            <option value="">-- pilih varian --</option>
                            <?php foreach (($variants ?? []) as $v): ?>
                                <option value="<?= $v['id']; ?>"
                                    data-raw="<?= esc($v['raw_material_id'] ?? ''); ?>"
                                    data-brand="<?= esc($v['brand_id'] ?? ''); ?>"
                                    <?= (string) ($row['raw_material_variant_id'] ?? '') === (string) $v['id'] ? 'selected' : ''; ?>>
                                    <?= esc(trim(($v['brand_name'] ?? '') . ' - ' . ($v['variant_name'] ?? ''), ' -')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td style="padding:4px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <input type="number"
                               step="0.001"
                               min="0"
                               name="items[<?= $i; ?>][qty]"
                               value="<?= esc($row['qty'] ?? ''); ?>"
                               style="width:100%; padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                    </td>
                    <td class="item-unit" style="padding:4px 8px; border-bottom:1px solid var(--tr-border); font-size:11px; color:var(--tr-muted-text);">
                        <!-- hanya info satuan dari dropdown -->
                        &mdash;
                    </td>
                    <td style="padding:4px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <input type="number"
                               step="1"
                               min="0"
                               name="items[<?= $i; ?>][unit_cost]"
                               value="<?= esc($row['unit_cost'] ?? ''); ?>"
                               style="width:100%; padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                    </td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>

        <div style="margin-top:16px; display:flex; gap:8px;">
            <button type="submit"
                    style="padding:8px 14px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; font-size:13px; cursor:pointer;">
                Simpan Pembelian
            </button>
            <a href="<?= site_url('purchases'); ?>"
               style="padding:8px 14px; border-radius:999px; border:1px solid var(--tr-muted-text); font-size:13px; color:var(--tr-text); text-decoration:none;">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    (function() {
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const rawSelect = row.querySelector('.item-raw');
            const brandSelect = row.querySelector('.item-brand');
            const variantSelect = row.querySelector('.item-variant');
            const unitCell = row.querySelector('.item-unit');

            if (!rawSelect || !brandSelect || !variantSelect || !unitCell) return;

            const brandOptions = Array.from(brandSelect.querySelectorAll('option[data-raw]'));
            const variantOptions = Array.from(variantSelect.querySelectorAll('option[data-raw]'));

            function filterBrandOptions(rawId) {
                let validSelection = false;
                brandOptions.forEach(opt => {
                    const match = rawId !== '' && opt.dataset.raw === rawId;
                    opt.style.display = match ? '' : 'none';
                    if (match && opt.value === brandSelect.value) {
                        validSelection = true;
                    }
                });
                if (!validSelection) {
                    brandSelect.value = '';
                }
                brandSelect.disabled = rawId === '';
            }

            function syncBrandFromVariant() {
                const selected = variantSelect.options[variantSelect.selectedIndex];
                if (!selected || !selected.dataset) {
                    brandSelect.value = '';
                    return;
                }
                const brandId = selected.dataset.brand || '';
                if (brandId !== '') {
                    const candidate = brandSelect.querySelector(`option[value="${brandId}"]`);
                    brandSelect.value = candidate ? brandId : '';
                } else {
                    brandSelect.value = '';
                }
            }

            function filterVariantOptions(rawId, brandId) {
                let validSelection = false;
                variantOptions.forEach(opt => {
                    const matchRaw = rawId !== '' && opt.dataset.raw === rawId;
                    const optBrand = opt.dataset.brand || '';
                    const matchBrand = brandId === '' || optBrand === brandId;
                    const match = matchRaw && matchBrand;
                    opt.style.display = match ? '' : 'none';
                    if (match && opt.value === variantSelect.value) {
                        validSelection = true;
                    }
                });
                if (!validSelection) {
                    variantSelect.value = '';
                }
                variantSelect.disabled = rawId === '';
            }

            function refreshRow() {
                const rawId = rawSelect.value;
                const unitText = rawSelect.options[rawSelect.selectedIndex]?.dataset.unit || '';
                const hasVariants = rawSelect.options[rawSelect.selectedIndex]?.dataset.hasVariants === '1';
                unitCell.textContent = unitText !== '' ? unitText : '-';

                filterBrandOptions(rawId);
                if (variantSelect.value) {
                    syncBrandFromVariant();
                }
                filterVariantOptions(rawId, brandSelect.value);

                if (!hasVariants) {
                    brandSelect.value = '';
                    variantSelect.value = '';
                    brandSelect.disabled = true;
                    variantSelect.disabled = true;
                }
            }

            rawSelect.addEventListener('change', refreshRow);
            brandSelect.addEventListener('change', () => {
                filterVariantOptions(rawSelect.value, brandSelect.value);
            });
            variantSelect.addEventListener('change', () => {
                syncBrandFromVariant();
                filterVariantOptions(rawSelect.value, brandSelect.value);
            });

            refreshRow();
        });
    })();
</script>

<?= $this->endSection() ?>


