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
            <h3 style="margin:0; font-size:14px;">Komposisi Bahan</h3>
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
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Bahan Baku</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Qty</th>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Satuan</th>
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
                        'raw_material_id' => '',
                        'qty'             => '',
                        'waste_pct'       => '',
                        'note'            => '',
                        'material_name'   => '',
                        'unit_short'      => '',
                    ]);
                }
                ?>

                <?php foreach ($rows as $idx => $row): ?>
                    <tr>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <select name="items[<?= $idx; ?>][raw_material_id]"
                                    style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
                                <option value="">-- pilih bahan --</option>
                                <?php foreach ($materials as $m): ?>
                                    <?php
                                    $selectedId = $oldItems !== null
                                        ? ($row['raw_material_id'] ?? null)
                                        : ($row['raw_material_id'] ?? $row['raw_material_id'] ?? null);
                                    ?>
                                    <option value="<?= $m['id']; ?>"
                                        <?= (int)($selectedId ?? 0) === (int)$m['id'] ? 'selected' : ''; ?>>
                                        <?= esc($m['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                            <input type="number"
                                   name="items[<?= $idx; ?>][qty]"
                                   step="0.001"
                                   value="<?= esc($row['qty'] ?? ''); ?>"
                                   style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text); text-align:right;">
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <?php
                            $unit = $row['unit_short'] ?? '';
                            ?>
                            <span style="font-size:11px; color:var(--tr-muted-text);">
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
            ];
        }, $materials ?? [])); ?>;

        const tbody = document.getElementById('recipe-items-body');
        const btnAdd = document.getElementById('btn-add-ingredient');

        function buildOptions() {
            return '<option value="">-- pilih bahan --</option>' + materials.map(function(m) {
                return '<option value="' + m.id + '">' + m.name + (m.unit ? ' (' + m.unit + ')' : '') + '</option>';
            }).join('');
        }

        function createRow(idx) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                    <select name="items[${idx}][raw_material_id]"
                            style="width:100%; padding:4px 6px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
                        ${buildOptions()}
                    </select>
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

            const select = tr.querySelector('select');
            const unitLabel = tr.querySelector('.unit-label');
            select.addEventListener('change', function() {
                const selected = materials.find(m => String(m.id) === this.value);
                unitLabel.textContent = selected && selected.unit ? selected.unit : '';
            });

            const removeBtn = tr.querySelector('.btn-remove-row');
            removeBtn.addEventListener('click', function() {
                if (tbody.children.length > 1) {
                    tr.remove();
                } else {
                    // kosongkan saja jika tinggal 1 baris
                    tr.querySelectorAll('input, select').forEach(function(el) { el.value = ''; });
                    unitLabel.textContent = '';
                }
            });

            return tr;
        }

        btnAdd.addEventListener('click', function() {
            const idx = tbody.children.length;
            const newRow = createRow(idx);
            tbody.appendChild(newRow);
        });
    })();
</script>

<?= $this->endSection() ?>

