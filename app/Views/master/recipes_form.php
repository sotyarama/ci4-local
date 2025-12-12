<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <h2 style="margin-top:0; font-size:18px;">
        <?= $mode === 'edit' ? 'Edit Resep Menu' : 'Tambah Resep Menu'; ?>
    </h2>
    <p style="margin:0 0 16px; font-size:12px; color:var(--tr-muted-text);">
        Definisikan komposisi bahan baku sebagai dasar perhitungan HPP.
    </p>

        <?php if (($mode === 'edit') && isset($hpp) && $hpp !== null): ?>
        <?php
            $yieldQty  = (float) ($hpp['recipe']['yield_qty'] ?? 1);
            $yieldUnit = $hpp['recipe']['yield_unit'] ?? 'porsi';
            $totalCost = (float) ($hpp['total_cost'] ?? 0);
            $hppPer    = (float) ($hpp['hpp_per_yield'] ?? 0);
        ?>
        <div style="margin:0 0 16px; padding:10px 12px; border-radius:8px; background:rgba(122,154,108,0.14); border:1px solid var(--tr-primary); color:var(--tr-secondary-green); font-size:12px;">
            <div style="font-weight:600; margin-bottom:4px;">Ringkasan HPP (Perkiraan)</div>
            <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                <span>Total biaya 1 resep (<?= number_format($yieldQty, 3, ',', '.'); ?> <?= esc($yieldUnit); ?>):</span>
                <span><strong>Rp <?= number_format($totalCost, 0, ',', '.'); ?></strong></span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span>HPP per <?= esc($yieldUnit); ?>:</span>
                <span><strong>Rp <?= number_format($hppPer, 0, ',', '.'); ?></strong></span>
            </div>
            <div style="margin-top:4px; font-size:11px; color:var(--tr-secondary-green);">
                *Menggunakan <code>cost_avg</code> terakhir dari masing-masing bahan baku dan faktor waste %.
            </div>
        </div>
    <?php endif; ?>

    <?php
    $errors = session('errors') ?? [];
    $recipeNames = [];
    foreach ($recipes ?? [] as $r) {
        $recipeNames[$r['id']] = $r['menu_name'] ?? ('Resep #' . $r['id']);
    }
    ?>

    <?php if (! empty($errors)): ?>
        <div style="margin-bottom:12px; padding:8px; border-radius:6px; background:var(--tr-secondary-beige); color:var(--tr-accent-brown); font-size:12px; border:1px solid var(--tr-accent-brown);">
            <ul style="margin:0; padding-left:18px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post"
          action="<?= $mode === 'edit'
              ? site_url('master/recipes/update/' . $recipe['id'])
              : site_url('master/recipes/store'); ?>">

        <?= csrf_field(); ?>

        <!-- Menu -->
        <div style="margin-bottom:12px;">
            <label style="display:block; font-size:12px; margin-bottom:4px;">Menu</label>

            <?php if ($mode === 'edit'): ?>
                <input type="text"
                       value="<?= esc($menus[0]['name'] ?? ''); ?>"
                       readonly
                       style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-border); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
                <input type="hidden" name="menu_id" value="<?= esc($recipe['menu_id']); ?>">
            <?php else: ?>
                <select name="menu_id"
                        required
                        style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
                    <option value="">-- pilih menu --</option>
                    <?php foreach ($menus as $m): ?>
                        <?php
                            // pakai old('menu_id') supaya kalau form error, pilihan tetap keingat
                            $selected = (string) old('menu_id') === (string) $m['id'] ? 'selected' : '';
                        ?>
                        <option value="<?= $m['id']; ?>" <?= $selected; ?>>
                            <?= esc($m['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <!-- Yield -->
        <div style="display:flex; gap:8px; margin-bottom:12px;">
            <div style="flex:1;">
                <label style="display:block; font-size:12px; margin-bottom:4px;">Yield (jumlah hasil)</label>
                <input type="number"
                       step="0.001"
                       name="yield_qty"
                       value="<?= old('yield_qty', $recipe['yield_qty'] ?? 1); ?>"
                       required
                       style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
            </div>
            <div style="width:120px;">
                <label style="display:block; font-size:12px; margin-bottom:4px;">Satuan</label>
                <input type="text"
                       name="yield_unit"
                       value="<?= old('yield_unit', $recipe['yield_unit'] ?? 'porsi'); ?>"
                       style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
            </div>
        </div>

        <!-- Notes -->
        <div style="margin-bottom:12px;">
            <label style="display:block; font-size:12px; margin-bottom:4px;">Catatan (opsional)</label>
            <textarea name="notes"
                      rows="2"
                      style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);"><?= old('notes', $recipe['notes'] ?? ''); ?></textarea>
        </div>

        <hr style="border-color:var(--tr-border); margin:14px 0;">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <h3 style="margin:0; font-size:14px;">Komposisi Bahan / Sub-Resep</h3>
            <button type="button" id="btn-add-ingredient"
                    style="font-size:12px; padding:4px 10px; border-radius:999px; border:1px solid var(--tr-border); background:var(--tr-border); color:var(--tr-text); cursor:pointer;">
                + Tambah baris
            </button>
        </div>
        <p style="margin:0 0 10px; font-size:11px; color:var(--tr-muted-text);">
            Isi bahan baku yang digunakan untuk 1 resep (yield di atas).
        </p>

        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:10px;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Bahan / Sub-Resep</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Qty</th>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Satuan / Info</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Waste %</th>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Catatan</th>
                    <th style="text-align:center; padding:6px 8px; border-bottom:1px solid var(--tr-border); width:70px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="recipe-items-body">
                <?php
                $oldItems = old('items');
                if ($oldItems !== null) {
                    // pakai data old input (misal setelah error)
                    $rows = $oldItems;
                } elseif (! empty($items)) {
                    $rows = $items;
                } else {
                    // default 5 baris kosong
                    $rows = array_fill(0, 5, [
                        'item_type'       => 'raw',
                        'raw_material_id' => '',
                        'child_recipe_id' => '',
                        'qty'             => '',
                        'waste_pct'       => '',
                        'note'            => '',
                        'material_name'   => '',
                        'unit_short'      => '',
                    ]);
                }
                ?>

                <?php foreach ($rows as $idx => $row): ?>
                    <?php
                        $type         = $row['item_type'] ?? 'raw';
                        $rawSelected  = $row['raw_material_id'] ?? '';
                        $childSelected= $row['child_recipe_id'] ?? '';
                        $unit         = $row['unit_short'] ?? '';

                        if ($type === 'recipe' && ! empty($childSelected)) {
                            $unit = 'Sub: ' . ($recipeNames[(int) $childSelected] ?? ('Resep #' . $childSelected));
                        }
                    ?>
                    <tr>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <div style="display:flex; flex-direction:column; gap:4px;">
                                <select name="items[<?= $idx; ?>][item_type]"
                                        class="item-type"
                                        style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
                                    <option value="raw" <?= $type === 'raw' ? 'selected' : ''; ?>>Bahan baku</option>
                                    <option value="recipe" <?= $type === 'recipe' ? 'selected' : ''; ?>>Sub-resep</option>
                                </select>
                                <select name="items[<?= $idx; ?>][raw_material_id]"
                                        class="select-raw"
                                        style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text); <?= $type === 'recipe' ? 'display:none;' : ''; ?>">
                                    <option value="">-- pilih bahan --</option>
                                    <?php foreach ($materials as $m): ?>
                                        <option value="<?= $m['id']; ?>"
                                            <?= (int)($rawSelected ?? 0) === (int)$m['id'] ? 'selected' : ''; ?>>
                                            <?= esc($m['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="items[<?= $idx; ?>][child_recipe_id]"
                                        class="select-recipe"
                                        style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text); <?= $type === 'recipe' ? '' : 'display:none;'; ?>">
                                    <option value="">-- pilih sub-resep --</option>
                                    <?php foreach (($recipes ?? []) as $r): ?>
                                        <option value="<?= $r['id']; ?>"
                                            <?= (int)($childSelected ?? 0) === (int)$r['id'] ? 'selected' : ''; ?>>
                                            <?= esc($r['menu_name'] ?? ('Resep #' . $r['id'])); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                            <input type="number"
                                   name="items[<?= $idx; ?>][qty]"
                                   step="0.001"
                                   value="<?= esc($row['qty'] ?? ''); ?>"
                                   style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text); text-align:right;">
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <span class="unit-label" style="font-size:11px; color:var(--tr-muted-text);">
                                <?= esc($unit); ?>
                            </span>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                            <input type="number"
                                   name="items[<?= $idx; ?>][waste_pct]"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   value="<?= esc($row['waste_pct'] ?? '0'); ?>"
                                   style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text); text-align:right;">
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <input type="text"
                                   name="items[<?= $idx; ?>][note]"
                                   value="<?= esc($row['note'] ?? ''); ?>"
                                   style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                            <button type="button"
                                    class="btn-remove-row"
                                    style="font-size:11px; padding:4px 8px; border-radius:8px; border:1px solid var(--tr-muted-text); background:var(--tr-border); color:var(--tr-text); cursor:pointer;">
                                Hapus
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="font-size:11px; color:var(--tr-muted-text); margin-top:4px;">
            Waste % dibatasi 0 - 100 agar perhitungan stok dan HPP tetap wajar.
        </div>

        <div id="hpp-live" style="margin-top:12px; padding:10px 12px; border-radius:10px; border:1px dashed var(--tr-border); background:var(--tr-bg); font-size:12px; color:var(--tr-text);">
            <div style="font-weight:700; margin-bottom:4px;">HPP Live (berdasar cost_avg bahan baku)</div>
            <div style="display:flex; justify-content:space-between;">
                <span>Total biaya 1 resep (raw only):</span>
                <span id="hpp-live-total">Rp 0</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span>HPP per yield:</span>
                <span id="hpp-live-per">Rp 0</span>
            </div>
            <div style="margin-top:4px; font-size:11px; color:var(--tr-muted-text);">
                Sub-resep belum dihitung di sini (hanya bahan baku).
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:10px;">
            <a href="<?= site_url('master/recipes'); ?>"
               style="font-size:12px; padding:6px 12px; border-radius:999px; border:1px solid var(--tr-muted-text); background:var(--tr-bg); color:var(--tr-text); text-decoration:none;">
                Batal
            </a>
            <button type="submit"
                    style="font-size:12px; padding:6px 14px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; cursor:pointer;">
                <?= $mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Resep'; ?>
            </button>
        </div>

    </form>
</div>

<script>
    (function() {
        const materials = <?= json_encode(array_map(static function($m) {
            return [
                'id' => (int) $m['id'],
                'name' => $m['name'],
                'unit' => $m['unit_short'] ?? '',
                'cost' => (float) ($m['cost_avg'] ?? 0),
            ];
        }, $materials ?? [])); ?>;

        const recipes = <?= json_encode(array_map(static function($r) {
            return [
                'id' => (int) $r['id'],
                'name' => $r['menu_name'] ?? ('Resep #' . $r['id']),
                'yield' => $r['yield_qty'] ?? null,
                'unit' => $r['yield_unit'] ?? 'porsi',
            ];
        }, $recipes ?? [])); ?>;

        const tbody = document.getElementById('recipe-items-body');
        const btnAdd = document.getElementById('btn-add-ingredient');

        function buildRawOptions(selected) {
            return '<option value="">-- pilih bahan --</option>' + materials.map(function(m) {
                const sel = String(selected || '') === String(m.id) ? ' selected' : '';
                return '<option value="' + m.id + '"' + sel + '>' + m.name + (m.unit ? ' (' + m.unit + ')' : '') + '</option>';
            }).join('');
        }

        function buildRecipeOptions(selected) {
            return '<option value="">-- pilih sub-resep --</option>' + recipes.map(function(r) {
                const sel = String(selected || '') === String(r.id) ? ' selected' : '';
                return '<option value="' + r.id + '"' + sel + '>' + r.name + '</option>';
            }).join('');
        }

        function findMaterial(id) {
            return materials.find(function(m) { return String(m.id) === String(id); });
        }

        function findRecipe(id) {
            return recipes.find(function(r) { return String(r.id) === String(id); });
        }

        function createRow(idx) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                    <div style="display:flex; flex-direction:column; gap:4px;">
                        <select name="items[${idx}][item_type]"
                                class="item-type"
                                style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
                            <option value="raw" selected>Bahan baku</option>
                            <option value="recipe">Sub-resep</option>
                        </select>
                        <select name="items[${idx}][raw_material_id]"
                                class="select-raw"
                                style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
                            ${buildRawOptions()}
                        </select>
                        <select name="items[${idx}][child_recipe_id]"
                                class="select-recipe"
                                style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text); display:none;">
                            ${buildRecipeOptions()}
                        </select>
                    </div>
                </td>
                <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                    <input type="number"
                           name="items[${idx}][qty]"
                           step="0.001"
                           value=""
                           style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text); text-align:right;">
                </td>
                <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                    <span style="font-size:11px; color:var(--tr-muted-text);" class="unit-label"></span>
                </td>
                <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                    <input type="number"
                           name="items[${idx}][waste_pct]"
                           step="0.01"
                           min="0"
                           max="100"
                           value="0"
                           style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text); text-align:right;">
                </td>
                <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                    <input type="text"
                           name="items[${idx}][note]"
                           value=""
                           style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
                </td>
                <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                    <button type="button"
                            class="btn-remove-row"
                            style="font-size:11px; padding:4px 8px; border-radius:8px; border:1px solid var(--tr-muted-text); background:var(--tr-border); color:var(--tr-text); cursor:pointer;">
                        Hapus
                    </button>
                </td>
            `;

            wireRow(tr);

            return tr;
        }

        function wireRow(tr) {
            const typeSelect   = tr.querySelector('.item-type');
            const rawSelect    = tr.querySelector('.select-raw');
            const recipeSelect = tr.querySelector('.select-recipe');
            const unitLabel    = tr.querySelector('.unit-label');

            function sync() {
                const type = typeSelect.value;
                rawSelect.style.display    = type === 'raw' ? 'block' : 'none';
                recipeSelect.style.display = type === 'recipe' ? 'block' : 'none';

                if (type === 'raw') {
                    const mat = findMaterial(rawSelect.value);
                    unitLabel.textContent = mat && mat.unit ? mat.unit : '';
                } else {
                    const rec = findRecipe(recipeSelect.value);
                    unitLabel.textContent = rec ? ('Sub: ' + rec.name) : '';
                }
            }

            typeSelect.addEventListener('change', sync);
            rawSelect.addEventListener('change', sync);
            recipeSelect.addEventListener('change', sync);
            sync();

            const removeBtn = tr.querySelector('.btn-remove-row');
            removeBtn.addEventListener('click', function() {
                if (tbody.children.length > 1) {
                    tr.remove();
                } else {
                    tr.querySelectorAll('input, select').forEach(function(el) { el.value = ''; });
                    unitLabel.textContent = '';
                    typeSelect.value = 'raw';
                    rawSelect.style.display = 'block';
                    recipeSelect.style.display = 'none';
                }
            });
        }

        // Wire existing rows rendered from PHP
        document.querySelectorAll('#recipe-items-body tr').forEach(function(tr) {
            wireRow(tr);
        });

        btnAdd.addEventListener('click', function() {
            const idx = tbody.children.length;
            const newRow = createRow(idx);
            tbody.appendChild(newRow);
        });

        function formatCurrency(n) {
            if (isNaN(n)) return 'Rp 0';
            return 'Rp ' + Number(n || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
        }

        function clampWaste(v) {
            if (isNaN(v)) return 0;
            return Math.min(100, Math.max(0, v));
        }

        function computeHppLive() {
            const yieldInput = document.querySelector('input[name="yield_qty"]');
            let yieldQty = parseFloat(yieldInput?.value || '1');
            if (!(yieldQty > 0)) yieldQty = 1;

            let total = 0;
            tbody.querySelectorAll('tr').forEach(function(tr) {
                const type = tr.querySelector('.item-type')?.value || 'raw';
                if (type !== 'raw') return; // skip sub-recipe untuk live calc
                const rawId = tr.querySelector('.select-raw')?.value || '';
                const qty = parseFloat(tr.querySelector('input[name*="[qty]"]')?.value || '0');
                const waste = clampWaste(parseFloat(tr.querySelector('input[name*="[waste_pct]"]')?.value || '0'));
                if (!rawId || !(qty > 0)) return;
                const mat = findMaterial(rawId);
                const cost = mat ? parseFloat(mat.cost || 0) : 0;
                const effectiveQty = qty * (1 + waste / 100);
                total += effectiveQty * cost;
            });

            const per = total / yieldQty;
            const totalEl = document.getElementById('hpp-live-total');
            const perEl = document.getElementById('hpp-live-per');
            if (totalEl) totalEl.textContent = formatCurrency(total);
            if (perEl) perEl.textContent = formatCurrency(per);
        }

        document.addEventListener('input', function(e) {
            if (e.target.closest('#recipe-items-body') || e.target.name === 'yield_qty') {
                computeHppLive();
            }
        });

        document.addEventListener('DOMContentLoaded', computeHppLive);
    })();
</script>

<?= $this->endSection() ?>
