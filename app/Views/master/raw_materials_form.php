<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Raw Materials - Form
 * - Fokus: minim inline style, pakai class dari theme-temurasa.css
 * - Create vs Edit:
 *   - Create: initial_stock & initial_cost
 *   - Edit:   current_stock + last cost (readonly info)
 */
$errors = session('errors') ?? [];
$isEdit = ! empty($material);
$hasVariants = (int) old('has_variants', $material['has_variants'] ?? 0);
$brandName = (string) old('brand_name', $material['brand_name'] ?? '');
$qtyPrecision = (int) old('qty_precision', $material['qty_precision'] ?? 0);
if ($qtyPrecision < 0) {
    $qtyPrecision = 0;
}
if ($qtyPrecision > 3) {
    $qtyPrecision = 3;
}
$precisionStep = $qtyPrecision > 0 ? '0.' . str_repeat('0', $qtyPrecision - 1) . '1' : '1';
?>

<div class="card">
    <h2 class="page-title"><?= esc($title ?? 'Form Bahan Baku'); ?></h2>
    <p class="page-subtitle"><?= esc($subtitle ?? ''); ?></p>

    <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="alert-list">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= esc($formAction ?? '#'); ?>" method="post" class="form">
        <?= csrf_field(); ?>

        <div class="form-grid">
            <div class="form-field">
                <label class="form-label">Memiliki Varian?</label>
                <div class="form-check" style="margin-top:6px;">
                    <label class="form-check__label" style="margin-right:12px;">
                        <input
                            class="form-check__input"
                            type="radio"
                            name="has_variants"
                            value="0"
                            <?= $hasVariants === 0 ? 'checked' : ''; ?>>
                        Tidak
                    </label>
                    <label class="form-check__label">
                        <input
                            class="form-check__input"
                            type="radio"
                            name="has_variants"
                            value="1"
                            <?= $hasVariants === 1 ? 'checked' : ''; ?>>
                        Ya
                    </label>
                </div>
            </div>

            <div class="form-field">
                <label class="form-label">Nama Bahan</label>
                <input
                    class="form-input"
                    type="text"
                    name="name"
                    value="<?= esc(old('name', $material['name'] ?? '')); ?>"
                    required>
            </div>

            <div class="form-field">
                <label class="form-label">Satuan</label>
                <select class="form-input" name="unit_id" required>
                    <option value="">-- pilih satuan --</option>

                    <?php foreach (($units ?? []) as $u): ?>
                        <?php
                        $uid   = (string) ($u['id'] ?? '');
                        $uname = (string) ($u['name'] ?? '');
                        $short = (string) ($u['short_name'] ?? '');
                        $selected = ((string) old('unit_id', $material['unit_id'] ?? '') === $uid);
                        ?>
                        <option value="<?= esc($uid); ?>" <?= $selected ? 'selected' : ''; ?>>
                            <?= esc($uname); ?><?= $short !== '' ? ' (' . esc($short) . ')' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-field">
                <label class="form-label">Presisi Qty</label>
                <select class="form-input" name="qty_precision" id="qty-precision-input">
                    <option value="0" <?= $qtyPrecision === 0 ? 'selected' : ''; ?>>0 (tanpa desimal)</option>
                    <option value="2" <?= $qtyPrecision === 2 ? 'selected' : ''; ?>>2 digit</option>
                    <option value="3" <?= $qtyPrecision === 3 ? 'selected' : ''; ?>>3 digit</option>
                </select>
                <div class="form-note">Gunakan 0 untuk pcs, 2/3 untuk bahan seperti gula/minyak.</div>
            </div>

            <div class="form-field">
                <label class="form-label">Min. Stok</label>
                <input
                    class="form-input"
                    type="number"
                    step="<?= esc($precisionStep); ?>"
                    name="min_stock"
                    id="min-stock-input"
                    value="<?= esc(old('min_stock', $material['min_stock'] ?? '0')); ?>">
            </div>

            <?php if (! $isEdit): ?>
                <div class="form-field">
                    <label class="form-label">Stok Awal (opsional)</label>
                    <input
                        class="form-input"
                        type="number"
                        step="<?= esc($precisionStep); ?>"
                        name="initial_stock"
                        id="initial-stock-input"
                        <?= $hasVariants === 1 ? 'readonly' : ''; ?>
                        value="<?= esc(old('initial_stock', '0')); ?>">
                </div>

                <div class="form-field">
                    <label class="form-label">Harga per Satuan (opsional)</label>
                    <input
                        class="form-input"
                        type="number"
                        step="1"
                        name="initial_cost"
                        id="initial-cost-input"
                        <?= $hasVariants === 1 ? 'readonly' : ''; ?>
                        value="<?= esc(old('initial_cost', '0')); ?>">
                </div>
            <?php else: ?>
                <div class="form-field">
                    <label class="form-label">Stok Saat Ini</label>
                    <input
                        class="form-input"
                        type="number"
                        step="<?= esc($precisionStep); ?>"
                        name="current_stock"
                        id="current-stock-input"
                        <?= $hasVariants === 1 ? 'readonly' : ''; ?>
                        value="<?= esc(old('current_stock', $material['current_stock'] ?? '0')); ?>">
                </div>

                <div class="form-field">
                    <label class="form-label">Last Cost (readonly untuk info)</label>
                    <input
                        class="form-input"
                        type="number"
                        step="1"
                        value="<?= esc($material['cost_last'] ?? '0'); ?>"
                        readonly
                        aria-readonly="true">
                    <div class="form-note">
                        Last Cost & Avg Cost akan berubah otomatis dari modul Pembelian / Stock Movement.
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-field" id="brand-parent-field">
                <label class="form-label">Brand (opsional)</label>
                <input
                    class="form-input"
                    type="text"
                    name="brand_name"
                    value="<?= esc($brandName); ?>">
            </div>
        </div>

        <?php
            $variantRows = old('variants');
            if (! is_array($variantRows)) {
                $variantRows = $variants ?? [];
            }
            $variantRows = array_values($variantRows);
            for ($i = 0; $i < 3; $i++) {
                $variantRows[] = [
                    'id' => '',
                    'brand_name' => '',
                    'variant_name' => '',
                    'sku_code' => '',
                    'current_stock' => '',
                    'min_stock' => '',
                    'is_active' => 1,
                ];
            }
        ?>

        <div class="form-section" style="margin-top:16px;" id="variant-section">
            <h3 class="page-subtitle" style="margin-bottom:8px;">Varian / Brand</h3>
            <p class="form-note" style="margin-bottom:8px;">
                Isi varian bila bahan punya pilihan brand. Baris kosong akan diabaikan.
            </p>

            <table class="table">
                <thead>
                    <tr>
                        <th class="table__th">Brand</th>
                        <th class="table__th">Nama Varian</th>
                        <th class="table__th">SKU (opsional)</th>
                        <th class="table__th">Stok</th>
                        <th class="table__th">Min. Stok</th>
                        <th class="table__th table__th--center">Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($variantRows as $idx => $row): ?>
                        <?php
                            $rowId = (string) ($row['id'] ?? '');
                            $rowBrand = (string) ($row['brand_name'] ?? '');
                            $rowName = (string) ($row['variant_name'] ?? '');
                            $rowSku = (string) ($row['sku_code'] ?? '');
                            $rowStock = (string) ($row['current_stock'] ?? '');
                            $rowMin = (string) ($row['min_stock'] ?? '');
                            $rowActive = ! empty($row['is_active']);
                        ?>
                        <tr>
                            <td class="table__td">
                                <input
                                    class="form-input"
                                    type="text"
                                    name="variants[<?= $idx; ?>][brand_name]"
                                    value="<?= esc($rowBrand); ?>">
                            </td>
                            <td class="table__td">
                                <input type="hidden" name="variants[<?= $idx; ?>][id]" value="<?= esc($rowId); ?>">
                                <input
                                    class="form-input"
                                    type="text"
                                    name="variants[<?= $idx; ?>][variant_name]"
                                    value="<?= esc($rowName); ?>">
                            </td>
                            <td class="table__td">
                                <input
                                    class="form-input"
                                    type="text"
                                    name="variants[<?= $idx; ?>][sku_code]"
                                    value="<?= esc($rowSku); ?>">
                            </td>
                            <td class="table__td">
                                <input
                                    class="form-input"
                                    type="number"
                                    step="<?= esc($precisionStep); ?>"
                                    data-precision-input="1"
                                    name="variants[<?= $idx; ?>][current_stock]"
                                    value="<?= esc($rowStock); ?>">
                            </td>
                            <td class="table__td">
                                <input
                                    class="form-input"
                                    type="number"
                                    step="<?= esc($precisionStep); ?>"
                                    data-precision-input="1"
                                    name="variants[<?= $idx; ?>][min_stock]"
                                    value="<?= esc($rowMin); ?>">
                            </td>
                            <td class="table__td table__td--center">
                                <input
                                    type="checkbox"
                                    name="variants[<?= $idx; ?>][is_active]"
                                    value="1"
                                    <?= $rowActive ? 'checked' : ''; ?>>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="button" class="btn btn-secondary btn-sm" id="add-variant-row">
                + Tambah Baris Varian
            </button>
        </div>

        <div class="form-check">
            <label class="form-check__label">
                <input
                    class="form-check__input"
                    type="checkbox"
                    name="is_active"
                    value="1"
                    <?= (old('is_active', $material['is_active'] ?? 1) ? 'checked' : ''); ?>>
                Aktif
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('master/raw-materials'); ?>" class="btn btn-secondary">Batal</a>
        </div>

    <?php if (! $isEdit): ?>
        <div class="form-note">
            Stok & harga akan diperbarui otomatis dari modul Pembelian dan Penyesuaian Stok.
        </div>
    <?php endif; ?>
    </form>
</div>

<template id="variant-row-template">
    <tr>
        <td class="table__td">
            <input class="form-input" type="text" name="variants[__INDEX__][brand_name]" value="">
        </td>
        <td class="table__td">
            <input type="hidden" name="variants[__INDEX__][id]" value="">
            <input class="form-input" type="text" name="variants[__INDEX__][variant_name]" value="">
        </td>
        <td class="table__td">
            <input class="form-input" type="text" name="variants[__INDEX__][sku_code]" value="">
        </td>
        <td class="table__td">
            <input class="form-input" type="number" step="1" data-precision-input="1" name="variants[__INDEX__][current_stock]" value="">
        </td>
        <td class="table__td">
            <input class="form-input" type="number" step="1" data-precision-input="1" name="variants[__INDEX__][min_stock]" value="">
        </td>
        <td class="table__td table__td--center">
            <input type="checkbox" name="variants[__INDEX__][is_active]" value="1" checked>
        </td>
    </tr>
</template>

<script>
    (function() {
        const hasVariantInputs = document.querySelectorAll('input[name="has_variants"]');
        const variantSection = document.getElementById('variant-section');
        const brandParentField = document.getElementById('brand-parent-field');
        const initialStockInput = document.getElementById('initial-stock-input');
        const initialCostInput = document.getElementById('initial-cost-input');
        const currentStockInput = document.getElementById('current-stock-input');
        const minStockInput = document.getElementById('min-stock-input');
        const precisionInput = document.getElementById('qty-precision-input');
        const addRowBtn = document.getElementById('add-variant-row');
        const tableBody = variantSection ? variantSection.querySelector('tbody') : null;
        const rowTemplate = document.getElementById('variant-row-template');

        function toggleSections() {
            const hasVariants = document.querySelector('input[name="has_variants"]:checked')?.value === '1';

            if (variantSection) {
                variantSection.style.display = hasVariants ? '' : 'none';
            }
            if (brandParentField) {
                brandParentField.style.display = hasVariants ? 'none' : '';
            }

            if (initialStockInput) {
                initialStockInput.readOnly = hasVariants;
            }
            if (initialCostInput) {
                initialCostInput.readOnly = hasVariants;
            }
            if (currentStockInput) {
                currentStockInput.readOnly = hasVariants;
            }
            if (minStockInput) {
                minStockInput.readOnly = false;
            }
        }

        function precisionToStep(precision) {
            if (!precision || precision <= 0) return '1';
            return `0.${'0'.repeat(Math.max(precision - 1, 0))}1`;
        }

        function applyPrecision() {
            const precision = parseInt(precisionInput?.value || '0', 10);
            const step = precisionToStep(isNaN(precision) ? 0 : precision);

            if (minStockInput) {
                minStockInput.step = step;
            }
            if (initialStockInput) {
                initialStockInput.step = step;
            }
            if (currentStockInput) {
                currentStockInput.step = step;
            }
            if (variantSection) {
                variantSection.querySelectorAll('input[data-precision-input="1"]').forEach((input) => {
                    input.step = step;
                });
            }
        }

        function addVariantRow() {
            if (!rowTemplate || !tableBody) return;
            const index = tableBody.querySelectorAll('tr').length;
            const html = rowTemplate.innerHTML.replace(/__INDEX__/g, String(index));
            const wrapper = document.createElement('tbody');
            wrapper.innerHTML = html.trim();
            const newRow = wrapper.querySelector('tr');
            if (newRow) {
                tableBody.appendChild(newRow);
                applyPrecision();
            }
        }

        if (hasVariantInputs.length) {
            hasVariantInputs.forEach(input => {
                input.addEventListener('change', toggleSections);
            });
        }
        if (addRowBtn) {
            addRowBtn.addEventListener('click', addVariantRow);
        }
        if (precisionInput) {
            precisionInput.addEventListener('change', applyPrecision);
        }

        toggleSections();
        applyPrecision();
    })();
</script>

<?= $this->endSection() ?>
