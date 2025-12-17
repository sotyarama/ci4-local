<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Recipes - Form (Refactored)
 * - Create & Edit mode
 * - Fokus: readability & konsistensi UI
 */

$errors = session('errors') ?? [];
$isEdit = ($mode === 'edit');

/**
 * Mapping recipe_id => menu_name (label sub-resep)
 */
$recipeNames = [];
foreach (($recipes ?? []) as $r) {
    $recipeNames[$r['id']] = $r['menu_name'] ?? ('Resep #' . $r['id']);
}

/**
 * Rows source:
 * - old('items') kalau submit error
 * - items dari DB kalau edit
 * - default 1 baris kosong
 */
$oldItems = old('items');
if ($oldItems !== null) {
    $rows = $oldItems;
} elseif (! empty($items)) {
    $rows = $items;
} else {
    $rows = [[
        'item_type'       => 'raw',   // default baris pertama bahan baku
        'raw_material_id' => '',
        'child_recipe_id' => '',
        'qty'             => '',
        'waste_pct'       => '0',
        'note'            => '',
        'unit_short'      => '',
    ]];
}

/**
 * Map unit bahan baku (untuk label unit server-side)
 */
$materialUnitMap = [];
foreach (($materials ?? []) as $m) {
    $materialUnitMap[(int) $m['id']] = (string) ($m['unit_short'] ?? '');
}

/**
 * Map nama resep (untuk label sub-resep server-side)
 */
$recipeNameMap = [];
foreach (($recipes ?? []) as $r) {
    $recipeNameMap[(int) $r['id']] = (string) ($r['menu_name'] ?? ('Resep #' . ($r['id'] ?? '')));
}
?>

<div class="card">

    <!-- Header -->
    <h2 class="page-title">
        <?= $isEdit ? 'Edit Resep Menu' : 'Tambah Resep Menu'; ?>
    </h2>
    <p class="page-subtitle">
        Definisikan komposisi bahan baku sebagai dasar perhitungan HPP.
    </p>

    <!-- Summary HPP (Edit Mode Only) -->
    <?php if ($isEdit && ! empty($hpp)): ?>
        <?php
        $yieldQty  = (float) ($hpp['recipe']['yield_qty'] ?? 1);
        $yieldUnit = $hpp['recipe']['yield_unit'] ?? 'porsi';
        $totalCost = (float) ($hpp['total_cost'] ?? 0);
        $hppPer    = (float) ($hpp['hpp_per_yield'] ?? 0);
        ?>
        <div class="alert alert-success">
            <div style="font-weight:600; margin-bottom:4px;">Ringkasan HPP (Perkiraan)</div>
            <div class="row-between">
                <span>Total biaya 1 resep (<?= number_format($yieldQty, 3, ',', '.'); ?> <?= esc($yieldUnit); ?>)</span>
                <strong>Rp <?= number_format($totalCost, 0, ',', '.'); ?></strong>
            </div>
            <div class="row-between">
                <span>HPP per <?= esc($yieldUnit); ?></span>
                <strong>Rp <?= number_format($hppPer, 0, ',', '.'); ?></strong>
            </div>
            <div class="form-note">
                *Menggunakan <code>cost_avg</code> bahan baku dan waste %.
            </div>
        </div>
    <?php endif; ?>

    <!-- Error Messages -->
    <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="alert-list">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="post"
        action="<?= $isEdit
                    ? site_url('master/recipes/update/' . $recipe['id'])
                    : site_url('master/recipes/store'); ?>">

        <?= csrf_field(); ?>

        <!-- Menu -->
        <div class="form-field">
            <label class="form-label">Menu</label>

            <?php if ($isEdit): ?>
                <input class="form-input" type="text"
                    value="<?= esc($menus[0]['name'] ?? ''); ?>" readonly>
                <input type="hidden" name="menu_id" value="<?= esc($recipe['menu_id']); ?>">
            <?php else: ?>
                <select name="menu_id" class="form-input" required>
                    <option value="">-- pilih menu --</option>
                    <?php foreach ($menus as $m): ?>
                        <option value="<?= $m['id']; ?>"
                            <?= (string) old('menu_id') === (string) $m['id'] ? 'selected' : ''; ?>>
                            <?= esc($m['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <!-- Yield -->
        <div class="form-grid">
            <div class="form-field">
                <label class="form-label">Yield (jumlah hasil)</label>
                <input class="form-input" type="number" step="0.001"
                    name="yield_qty"
                    value="<?= old('yield_qty', $recipe['yield_qty'] ?? 1); ?>"
                    required>
            </div>
            <div class="form-field">
                <label class="form-label">Satuan</label>
                <input class="form-input" type="text"
                    name="yield_unit"
                    value="<?= old('yield_unit', $recipe['yield_unit'] ?? 'porsi'); ?>">
            </div>
        </div>

        <!-- Notes -->
        <div class="form-field">
            <label class="form-label">Catatan (opsional)</label>
            <textarea class="form-input" rows="2"
                name="notes"><?= old('notes', $recipe['notes'] ?? ''); ?></textarea>
        </div>

        <hr class="divider">

        <!-- Composition -->
        <div class="row-between">
            <h3 class="section-title">Komposisi Bahan / Sub-Resep</h3>
            <button type="button" id="btn-add-ingredient" class="btn btn-secondary btn-sm">
                + Tambah baris
            </button>
        </div>
        <p class="form-note">
            Isi bahan baku yang digunakan untuk 1 resep (sesuai yield).
        </p>

        <table class="table" style="margin-bottom:10px;">
            <thead>
                <tr>
                    <th class="table__th" style="width:160px;">Tipe</th>
                    <th class="table__th">Bahan / Sub-Resep</th>
                    <th class="table__th table__th--right" style="width:140px;">Qty</th>
                    <th class="table__th" style="width:140px;">Satuan / Info</th>
                    <th class="table__th table__th--right" style="width:120px;">Waste %</th>
                    <th class="table__th">Catatan</th>
                    <th class="table__th table__th--center" style="width:90px;">Aksi</th>
                </tr>
            </thead>

            <tbody id="recipe-items-body">
                <?php foreach ($rows as $idx => $row): ?>
                    <?php
                    $type    = (string) ($row['item_type'] ?? 'raw');
                    $rawId   = (string) ($row['raw_material_id'] ?? '');
                    $childId = (string) ($row['child_recipe_id'] ?? '');
                    $qty     = (string) ($row['qty'] ?? '');
                    $waste   = (string) ($row['waste_pct'] ?? '0');
                    $note    = (string) ($row['note'] ?? '');

                    $unitLabel = '';
                    if ($type === 'raw' && $rawId !== '') {
                        $unitLabel = $materialUnitMap[(int)$rawId] ?? '';
                    } elseif ($type === 'recipe' && $childId !== '') {
                        $unitLabel = 'Sub: ' . ($recipeNameMap[(int)$childId] ?? ('Resep #' . $childId));
                    }
                    ?>
                    <tr>
                        <!-- TIPE -->
                        <td class="table__td">
                            <select name="items[<?= $idx; ?>][item_type]" class="form-input item-type">
                                <option value="raw" <?= $type === 'raw' ? 'selected' : ''; ?>>Bahan Baku</option>
                                <option value="recipe" <?= $type === 'recipe' ? 'selected' : ''; ?>>Sub-Resep</option>
                            </select>
                        </td>

                        <!-- ITEM -->
                        <td class="table__td">
                            <select name="items[<?= $idx; ?>][raw_material_id]"
                                class="form-input select-raw"
                                <?= $type === 'raw' ? '' : 'hidden disabled'; ?>
                                style="<?= $type === 'raw' ? '' : 'display:none;'; ?>">
                                <option value="">-- pilih bahan --</option>
                                <?php foreach (($materials ?? []) as $m): ?>
                                    <?php
                                    $mid  = (string) ($m['id'] ?? '');
                                    $unit = (string) ($m['unit_short'] ?? '');
                                    ?>
                                    <option value="<?= esc($mid); ?>"
                                        data-unit="<?= esc($unit); ?>"
                                        <?= $rawId === $mid ? 'selected' : ''; ?>>
                                        <?= esc($m['name'] ?? ''); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select name="items[<?= $idx; ?>][child_recipe_id]"
                                class="form-input select-recipe"
                                <?= $type === 'recipe' ? '' : 'hidden disabled'; ?>
                                style="<?= $type === 'recipe' ? '' : 'display:none;'; ?>">
                                <option value="">-- pilih sub-resep --</option>
                                <?php foreach (($recipes ?? []) as $r): ?>
                                    <?php $rid = (string) ($r['id'] ?? ''); ?>
                                    <option value="<?= esc($rid); ?>"
                                        <?= $childId === $rid ? 'selected' : ''; ?>>
                                        <?= esc($r['menu_name'] ?? ('Resep #' . $rid)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>

                        <!-- QTY -->
                        <td class="table__td table__td--right">
                            <input type="number"
                                step="0.001"
                                name="items[<?= $idx; ?>][qty]"
                                value="<?= esc($qty); ?>"
                                class="form-input"
                                style="text-align:right;">
                        </td>

                        <!-- UNIT/INFO -->
                        <td class="table__td muted">
                            <span class="unit-label"><?= esc($unitLabel); ?></span>
                        </td>

                        <!-- WASTE -->
                        <td class="table__td table__td--right">
                            <input type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                name="items[<?= $idx; ?>][waste_pct]"
                                value="<?= esc($waste); ?>"
                                class="form-input"
                                style="text-align:right;">
                        </td>

                        <!-- NOTE -->
                        <td class="table__td">
                            <input type="text"
                                name="items[<?= $idx; ?>][note]"
                                value="<?= esc($note); ?>"
                                class="form-input">
                        </td>

                        <!-- ACTION -->
                        <td class="table__td table__td--center">
                            <button type="button" class="btn btn-danger btn-sm btn-remove-row">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Live HPP (placeholder UI, perhitungan bisa kamu tambah belakangan) -->
        <div id="hpp-live" class="alert alert-info">
            <div style="font-weight:600;">HPP Live (perkiraan)</div>
            <div class="row-between">
                <span>Total biaya resep</span>
                <span id="hpp-live-total">Rp 0</span>
            </div>
            <div class="row-between">
                <span>HPP per yield</span>
                <span id="hpp-live-per">Rp 0</span>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= site_url('master/recipes'); ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Resep'; ?>
            </button>
        </div>

    </form>
</div>

<script>
    (function() {
        const tbody = document.getElementById('recipe-items-body');
        const btnAdd = document.getElementById('btn-add-ingredient');
        if (!tbody) return;

        function syncRow(tr) {
            const typeSelect = tr.querySelector('.item-type');
            const rawSelect = tr.querySelector('.select-raw');
            const recipeSelect = tr.querySelector('.select-recipe');
            const unitLabel = tr.querySelector('.unit-label');

            if (!typeSelect || !rawSelect || !recipeSelect || !unitLabel) return;

            const type = (typeSelect.value || '').toLowerCase();

            // default: hide both
            rawSelect.style.display = 'none';
            rawSelect.hidden = true;
            rawSelect.disabled = true;

            recipeSelect.style.display = 'none';
            recipeSelect.hidden = true;
            recipeSelect.disabled = true;

            unitLabel.textContent = '';

            if (type === 'raw') {
                recipeSelect.value = '';
                rawSelect.style.display = 'block';
                rawSelect.hidden = false;
                rawSelect.disabled = false;

                const opt = rawSelect.selectedOptions && rawSelect.selectedOptions[0];
                unitLabel.textContent = (opt && opt.dataset && opt.dataset.unit) ? opt.dataset.unit : '';
                return;
            }

            if (type === 'recipe') {
                rawSelect.value = '';
                recipeSelect.style.display = 'block';
                recipeSelect.hidden = false;
                recipeSelect.disabled = false;

                const opt = recipeSelect.selectedOptions && recipeSelect.selectedOptions[0];
                unitLabel.textContent = opt && opt.text ? ('Sub: ' + opt.text) : '';
                return;
            }

            // type kosong (harusnya tidak terjadi karena select hanya raw/recipe)
            rawSelect.value = '';
            recipeSelect.value = '';
        }

        function reindexRows() {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.forEach((tr, idx) => {
                tr.querySelectorAll('input, select, textarea').forEach(el => {
                    const name = el.getAttribute('name');
                    if (!name) return;
                    // items[<num>][field]
                    el.setAttribute('name', name.replace(/items\[\d+\]/, 'items[' + idx + ']'));
                });
            });
        }

        function buildRow() {
            // pakai template row pertama sebagai "blueprint"
            const first = tbody.querySelector('tr');
            if (!first) return null;

            const tr = first.cloneNode(true);

            // reset values
            tr.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.tagName === 'SELECT') {
                    el.selectedIndex = 0;
                } else {
                    el.value = '';
                }
            });

            // default row baru = raw
            const typeSelect = tr.querySelector('.item-type');
            if (typeSelect) typeSelect.value = 'raw';

            const waste = tr.querySelector('input[name*="[waste_pct]"]');
            if (waste) waste.value = '0';

            const unitLabel = tr.querySelector('.unit-label');
            if (unitLabel) unitLabel.textContent = '';

            return tr;
        }

        // Delegation: change handler (pasti kena untuk row baru/lama)
        tbody.addEventListener('change', function(e) {
            const tr = e.target.closest('tr');
            if (!tr) return;

            if (
                e.target.classList.contains('item-type') ||
                e.target.classList.contains('select-raw') ||
                e.target.classList.contains('select-recipe')
            ) {
                syncRow(tr);
            }
        });

        // Delegation: remove row
        tbody.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-remove-row');
            if (!btn) return;

            const tr = btn.closest('tr');
            if (!tr) return;

            const allRows = tbody.querySelectorAll('tr');
            if (allRows.length > 1) {
                tr.remove();
                reindexRows();
                return;
            }

            // kalau tinggal 1 baris: reset saja
            tr.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
                else el.value = '';
            });
            const typeSelect = tr.querySelector('.item-type');
            if (typeSelect) typeSelect.value = 'raw';
            syncRow(tr);
        });

        // Add row
        btnAdd && btnAdd.addEventListener('click', function() {
            const tr = buildRow();
            if (!tr) return;

            tbody.appendChild(tr);
            reindexRows();
            syncRow(tr);
        });

        // Initial sync for existing rows
        tbody.querySelectorAll('tr').forEach(tr => syncRow(tr));
    })();
</script>

<?= $this->endSection() ?>