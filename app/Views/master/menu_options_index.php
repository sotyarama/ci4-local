<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Menu Options - Index
 * - Konfigurasi group opsi + opsi per menu
 * - Simpan non-destructively (inactive untuk yang dihapus)
 */
$errors = session('errors') ?? [];
$menuName = '';
foreach (($menus ?? []) as $m) {
    if ((int) ($m['id'] ?? 0) === (int) ($menuId ?? 0)) {
        $menuName = (string) ($m['name'] ?? '');
        break;
    }
}

$groupRows = old('groups');
if (! is_array($groupRows)) {
    $groupRows = [];
    foreach (($groups ?? []) as $g) {
        $g['options'] = $optionsByGroup[$g['id']] ?? [];
        $groupRows[] = $g;
    }
}

$variantOptions = '';
foreach (($variants ?? []) as $v) {
    $labelParts = [];
    $rawName = (string) ($v['raw_material_name'] ?? '');
    $brandName = (string) ($v['brand_name'] ?? '');
    $variantName = (string) ($v['variant_name'] ?? '');
    if ($rawName !== '') {
        $labelParts[] = $rawName;
    }
    $detail = trim($brandName . ' ' . $variantName);
    if ($detail !== '') {
        $labelParts[] = $detail;
    }
    $label = implode(' - ', $labelParts);
    $variantOptions .= '<option value="' . esc($v['id']) . '">' . esc($label) . '</option>';
}
?>

<div class="tr-card">
    <div class="page-head">
        <div>
            <h2 class="page-title">Menu Options</h2>
            <p class="page-subtitle">Konfigurasi group opsi (modifier) dan add-ons per menu.</p>
        </div>
    </div>

    <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="alert-list">
                <?php foreach ($errors as $err): ?>
                    <li><?= esc($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('message')); ?>
        </div>
    <?php endif; ?>

    <form method="get" action="<?= site_url('master/menu-options'); ?>" class="form">
        <div class="form-grid">
            <div class="form-field">
                <label class="form-label">Pilih Menu</label>
                <select name="menu_id" class="form-input tr-control" required>
                    <option value="">-- pilih menu --</option>
                    <?php foreach (($menus ?? []) as $m): ?>
                        <option value="<?= esc($m['id']); ?>" <?= (string) $menuId === (string) $m['id'] ? 'selected' : ''; ?>>
                            <?= esc($m['name'] ?? '-'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field" style="align-self:end;">
                <button type="submit" class="tr-btn tr-btn-primary tr-btn-sm">Load</button>
            </div>
        </div>
    </form>

    <?php if ((int) ($menuId ?? 0) <= 0): ?>
        <p class="empty-state">Pilih menu terlebih dahulu untuk mengatur opsi.</p>
    <?php else: ?>
        <form method="post" action="<?= site_url('master/menu-options/save'); ?>" class="form" style="margin-top:16px;">
            <?= csrf_field(); ?>
            <input type="hidden" name="menu_id" value="<?= esc($menuId); ?>">

            <div class="form-note" style="margin-bottom:10px;">
                Menu: <strong><?= esc($menuName !== '' ? $menuName : ('#' . $menuId)); ?></strong>
            </div>

            <div id="group-container">
                <?php foreach ($groupRows as $gIdx => $g): ?>
                    <?php
                        $groupId = (int) ($g['id'] ?? 0);
                        $groupName = (string) ($g['name'] ?? '');
                        $isRequired = ! empty($g['is_required']);
                        $minSelect = (string) ($g['min_select'] ?? '0');
                        $maxSelect = (string) ($g['max_select'] ?? '1');
                        $sortOrder = (string) ($g['sort_order'] ?? ($gIdx + 1));
                        $showTicket = ! empty($g['show_on_kitchen_ticket']);
                        $isActive = ! empty($g['is_active']);
                        $options = $g['options'] ?? [];
                        $options = array_values($options);
                        $options[] = [
                            'id' => '',
                            'name' => '',
                            'variant_id' => '',
                            'price_delta' => '',
                            'qty_multiplier' => '',
                            'sort_order' => '',
                            'is_active' => 1,
                        ];
                    ?>
                    <div class="tr-card" data-group-index="<?= $gIdx; ?>">
                        <div class="page-head">
                            <div class="page-title" style="font-size:14px;">Group Opsi</div>
                            <button type="button" class="tr-btn tr-btn-secondary tr-btn-sm remove-group">Hapus Group</button>
                        </div>

                        <div class="form-grid">
                            <input type="hidden" name="groups[<?= $gIdx; ?>][id]" value="<?= esc($groupId); ?>">
                            <div class="form-field">
                                <label class="form-label">Nama Group</label>
                                <input class="form-input tr-control" type="text" name="groups[<?= $gIdx; ?>][name]" value="<?= esc($groupName); ?>">
                            </div>
                            <div class="form-field">
                                <label class="form-label">Min Pilih</label>
                                <input class="form-input tr-control" type="number" name="groups[<?= $gIdx; ?>][min_select]" value="<?= esc($minSelect); ?>">
                            </div>
                            <div class="form-field">
                                <label class="form-label">Max Pilih</label>
                                <input class="form-input tr-control" type="number" name="groups[<?= $gIdx; ?>][max_select]" value="<?= esc($maxSelect); ?>">
                            </div>
                            <div class="form-field">
                                <label class="form-label">Sort Order</label>
                                <input class="form-input tr-control" type="number" name="groups[<?= $gIdx; ?>][sort_order]" value="<?= esc($sortOrder); ?>">
                            </div>
                        </div>

                        <div class="form-check">
                            <label class="form-check__label">
                                <input class="form-check__input" type="checkbox" name="groups[<?= $gIdx; ?>][is_required]" value="1" <?= $isRequired ? 'checked' : ''; ?>>
                                Wajib dipilih
                            </label>
                            <label class="form-check__label">
                                <input class="form-check__input" type="checkbox" name="groups[<?= $gIdx; ?>][show_on_kitchen_ticket]" value="1" <?= $showTicket ? 'checked' : ''; ?>>
                                Tampil di kitchen ticket
                            </label>
                            <label class="form-check__label">
                                <input class="form-check__input" type="checkbox" name="groups[<?= $gIdx; ?>][is_active]" value="1" <?= $isActive ? 'checked' : ''; ?>>
                                Aktif
                            </label>
                        </div>

                        <div class="form-section">
                            <h3 class="page-subtitle">Opsi dalam Group</h3>
                            <table class="tr-table">
                                <thead>
                                    <tr>
                                        <th class="table__th">Nama Opsi</th>
                                        <th class="table__th">Varian</th>
                                        <th class="table__th table__th--right">Harga Tambahan</th>
                                        <th class="table__th table__th--right">Qty Mult</th>
                                        <th class="table__th table__th--right">Sort</th>
                                        <th class="table__th table__th--center">Aktif</th>
                                        <th class="table__th table__th--center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody data-next-index="<?= count($options); ?>">
                                    <?php foreach ($options as $oIdx => $opt): ?>
                                        <?php
                                            $optId = (int) ($opt['id'] ?? 0);
                                            $optName = (string) ($opt['name'] ?? '');
                                            $optVariant = (string) ($opt['variant_id'] ?? '');
                                            $optPrice = (string) ($opt['price_delta'] ?? '');
                                            $optQty = (string) ($opt['qty_multiplier'] ?? 1);
                                            $optSort = (string) ($opt['sort_order'] ?? ($oIdx + 1));
                                            $optActive = ! empty($opt['is_active']);
                                        ?>
                                        <tr>
                                            <td class="table__td">
                                                <input type="hidden" name="groups[<?= $gIdx; ?>][options][<?= $oIdx; ?>][id]" value="<?= esc($optId); ?>">
                                                <input class="form-input tr-control" type="text" name="groups[<?= $gIdx; ?>][options][<?= $oIdx; ?>][name]" value="<?= esc($optName); ?>">
                                            </td>
                                            <td class="table__td">
                                                <select name="groups[<?= $gIdx; ?>][options][<?= $oIdx; ?>][variant_id]" class="form-input tr-control">
                                                    <option value="">-- pilih varian --</option>
                                                    <?php foreach (($variants ?? []) as $v): ?>
                                                        <?php
                                                            $rawName = (string) ($v['raw_material_name'] ?? '');
                                                            $brandName = (string) ($v['brand_name'] ?? '');
                                                            $variantName = (string) ($v['variant_name'] ?? '');
                                                            $labelParts = [];
                                                            if ($rawName !== '') {
                                                                $labelParts[] = $rawName;
                                                            }
                                                            $detail = trim($brandName . ' ' . $variantName);
                                                            if ($detail !== '') {
                                                                $labelParts[] = $detail;
                                                            }
                                                            $label = implode(' - ', $labelParts);
                                                        ?>
                                                        <option value="<?= esc($v['id']); ?>" <?= (string) $optVariant === (string) $v['id'] ? 'selected' : ''; ?>>
                                                            <?= esc($label); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="table__td table__td--right">
                                                <input class="form-input tr-control" type="number" step="1" name="groups[<?= $gIdx; ?>][options][<?= $oIdx; ?>][price_delta]" value="<?= esc($optPrice); ?>">
                                            </td>
                                            <td class="table__td table__td--right">
                                                <input class="form-input tr-control" type="number" step="0.001" name="groups[<?= $gIdx; ?>][options][<?= $oIdx; ?>][qty_multiplier]" value="<?= esc($optQty); ?>">
                                            </td>
                                            <td class="table__td table__td--right">
                                                <input class="form-input tr-control" type="number" name="groups[<?= $gIdx; ?>][options][<?= $oIdx; ?>][sort_order]" value="<?= esc($optSort); ?>">
                                            </td>
                                            <td class="table__td table__td--center">
                                                <input type="checkbox" name="groups[<?= $gIdx; ?>][options][<?= $oIdx; ?>][is_active]" value="1" <?= $optActive ? 'checked' : ''; ?>>
                                            </td>
                                            <td class="table__td table__td--center">
                                                <button type="button" class="tr-btn tr-btn-secondary tr-btn-sm remove-option">Hapus</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <button type="button" class="tr-btn tr-btn-secondary tr-btn-sm add-option">+ Tambah Opsi</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="form-actions">
                <button type="button" class="tr-btn tr-btn-secondary" id="add-group">+ Tambah Group</button>
                <button type="submit" class="tr-btn tr-btn-primary">Simpan</button>
                <a href="<?= site_url('master/menu-options?menu_id=' . $menuId); ?>" class="tr-btn tr-btn-secondary">Reset</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<template id="group-template">
    <div class="tr-card" data-group-index="__GROUP_INDEX__">
        <div class="page-head">
            <div class="page-title" style="font-size:14px;">Group Opsi</div>
            <button type="button" class="tr-btn tr-btn-secondary tr-btn-sm remove-group">Hapus Group</button>
        </div>

        <div class="form-grid">
            <input type="hidden" name="groups[__GROUP_INDEX__][id]" value="">
            <div class="form-field">
                <label class="form-label">Nama Group</label>
                <input class="form-input tr-control" type="text" name="groups[__GROUP_INDEX__][name]" value="">
            </div>
            <div class="form-field">
                <label class="form-label">Min Pilih</label>
                <input class="form-input tr-control" type="number" name="groups[__GROUP_INDEX__][min_select]" value="0">
            </div>
            <div class="form-field">
                <label class="form-label">Max Pilih</label>
                <input class="form-input tr-control" type="number" name="groups[__GROUP_INDEX__][max_select]" value="1">
            </div>
            <div class="form-field">
                <label class="form-label">Sort Order</label>
                <input class="form-input tr-control" type="number" name="groups[__GROUP_INDEX__][sort_order]" value="">
            </div>
        </div>

        <div class="form-check">
            <label class="form-check__label">
                <input class="form-check__input" type="checkbox" name="groups[__GROUP_INDEX__][is_required]" value="1">
                Wajib dipilih
            </label>
            <label class="form-check__label">
                <input class="form-check__input" type="checkbox" name="groups[__GROUP_INDEX__][show_on_kitchen_ticket]" value="1" checked>
                Tampil di kitchen ticket
            </label>
            <label class="form-check__label">
                <input class="form-check__input" type="checkbox" name="groups[__GROUP_INDEX__][is_active]" value="1" checked>
                Aktif
            </label>
        </div>

        <div class="form-section">
            <h3 class="page-subtitle">Opsi dalam Group</h3>
            <table class="tr-table">
                <thead>
                    <tr>
                        <th class="table__th">Nama Opsi</th>
                        <th class="table__th">Varian</th>
                        <th class="table__th table__th--right">Harga Tambahan</th>
                        <th class="table__th table__th--right">Qty Mult</th>
                        <th class="table__th table__th--right">Sort</th>
                        <th class="table__th table__th--center">Aktif</th>
                        <th class="table__th table__th--center">Aksi</th>
                    </tr>
                </thead>
                <tbody data-next-index="1">
                    <tr>
                        <td class="table__td">
                            <input type="hidden" name="groups[__GROUP_INDEX__][options][0][id]" value="">
                            <input class="form-input tr-control" type="text" name="groups[__GROUP_INDEX__][options][0][name]" value="">
                        </td>
                        <td class="table__td">
                            <select name="groups[__GROUP_INDEX__][options][0][variant_id]" class="form-input tr-control">
                                <option value="">-- pilih varian --</option>
                                <?= $variantOptions; ?>
                            </select>
                        </td>
                        <td class="table__td table__td--right">
                            <input class="form-input tr-control" type="number" step="1" name="groups[__GROUP_INDEX__][options][0][price_delta]" value="0">
                        </td>
                        <td class="table__td table__td--right">
                            <input class="form-input tr-control" type="number" step="0.001" name="groups[__GROUP_INDEX__][options][0][qty_multiplier]" value="1">
                        </td>
                        <td class="table__td table__td--right">
                            <input class="form-input tr-control" type="number" name="groups[__GROUP_INDEX__][options][0][sort_order]" value="">
                        </td>
                        <td class="table__td table__td--center">
                            <input type="checkbox" name="groups[__GROUP_INDEX__][options][0][is_active]" value="1" checked>
                        </td>
                        <td class="table__td table__td--center">
                            <button type="button" class="tr-btn tr-btn-secondary tr-btn-sm remove-option">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="tr-btn tr-btn-secondary tr-btn-sm add-option">+ Tambah Opsi</button>
        </div>
    </div>
</template>

<template id="option-row-template">
    <tr>
        <td class="table__td">
            <input type="hidden" name="groups[__GROUP_INDEX__][options][__OPT_INDEX__][id]" value="">
            <input class="form-input tr-control" type="text" name="groups[__GROUP_INDEX__][options][__OPT_INDEX__][name]" value="">
        </td>
        <td class="table__td">
            <select name="groups[__GROUP_INDEX__][options][__OPT_INDEX__][variant_id]" class="form-input tr-control">
                <option value="">-- pilih varian --</option>
                <?= $variantOptions; ?>
            </select>
        </td>
        <td class="table__td table__td--right">
            <input class="form-input tr-control" type="number" step="1" name="groups[__GROUP_INDEX__][options][__OPT_INDEX__][price_delta]" value="0">
        </td>
        <td class="table__td table__td--right">
            <input class="form-input tr-control" type="number" step="0.001" name="groups[__GROUP_INDEX__][options][__OPT_INDEX__][qty_multiplier]" value="1">
        </td>
        <td class="table__td table__td--right">
            <input class="form-input tr-control" type="number" name="groups[__GROUP_INDEX__][options][__OPT_INDEX__][sort_order]" value="">
        </td>
        <td class="table__td table__td--center">
            <input type="checkbox" name="groups[__GROUP_INDEX__][options][__OPT_INDEX__][is_active]" value="1" checked>
        </td>
        <td class="table__td table__td--center">
            <button type="button" class="tr-btn tr-btn-secondary tr-btn-sm remove-option">Hapus</button>
        </td>
    </tr>
</template>

<script>
    (function() {
        const groupContainer = document.getElementById('group-container');
        const addGroupBtn = document.getElementById('add-group');
        const groupTemplate = document.getElementById('group-template');
        const optionTemplate = document.getElementById('option-row-template');

        if (!groupContainer || !addGroupBtn || !groupTemplate || !optionTemplate) {
            return;
        }

        let groupIndex = groupContainer.querySelectorAll('[data-group-index]').length;

        function addGroup() {
            const html = groupTemplate.innerHTML.replace(/__GROUP_INDEX__/g, String(groupIndex));
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const groupEl = wrapper.firstElementChild;
            if (!groupEl) return;
            groupEl.dataset.groupIndex = String(groupIndex);
            groupContainer.appendChild(groupEl);
            groupIndex += 1;
        }

        function addOption(groupEl) {
            const groupIdx = groupEl.dataset.groupIndex;
            const tbody = groupEl.querySelector('tbody');
            if (!tbody) return;
            const nextIndex = parseInt(tbody.dataset.nextIndex || '0', 10);
            const optIndex = Number.isNaN(nextIndex) ? tbody.querySelectorAll('tr').length : nextIndex;
            const html = optionTemplate.innerHTML
                .replace(/__GROUP_INDEX__/g, String(groupIdx))
                .replace(/__OPT_INDEX__/g, String(optIndex));
            const wrapper = document.createElement('tbody');
            wrapper.innerHTML = html.trim();
            const row = wrapper.querySelector('tr');
            if (row) {
                tbody.appendChild(row);
                tbody.dataset.nextIndex = String(optIndex + 1);
            }
        }

        addGroupBtn.addEventListener('click', addGroup);

        groupContainer.addEventListener('click', function(e) {
            const target = e.target;
            if (!target) return;
            if (target.classList.contains('add-option')) {
                const groupEl = target.closest('[data-group-index]');
                if (groupEl) addOption(groupEl);
            }
            if (target.classList.contains('remove-group')) {
                const groupEl = target.closest('[data-group-index]');
                if (groupEl) groupEl.remove();
            }
            if (target.classList.contains('remove-option')) {
                const row = target.closest('tr');
                if (row) row.remove();
            }
        });
    })();
</script>

<?= $this->endSection() ?>
