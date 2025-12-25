<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card" style="padding:14px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;"><?= esc($title); ?></h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                <?= esc($subtitle); ?>
            </p>
        </div>
        <div>
            <span class="pill" style="padding:6px 10px;"><?= esc($today); ?></span>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <?php $errs = (array) session()->getFlashdata('errors'); ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:var(--tr-secondary-beige); border:1px solid var(--tr-accent-brown); color:var(--tr-accent-brown); font-size:12px;">
            <ul style="margin:0; padding-left:16px;">
                <?php foreach ($errs as $e): ?>
                    <li><?= $e; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form id="pos-form" method="post" action="<?= site_url('transactions/sales/store'); ?>" style="display:grid; grid-template-columns:2fr 1fr; gap:14px;">
        <?= csrf_field(); ?>
        <input type="hidden" name="sale_date" value="<?= esc($today); ?>">
        <input type="hidden" name="invoice_no" value="">
        <input type="hidden" name="customer_name" value="">

        <div>
            <?php foreach ($menusByCategory as $cat => $menus): ?>
                <div style="margin-bottom:10px;">
                    <div style="font-weight:700; font-size:13px; margin:6px 0; color:var(--tr-primary-deep);"><?= esc($cat); ?></div>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:10px;">
                        <?php foreach ($menus as $m): ?>
                            <button type="button"
                                    class="menu-card"
                                    data-id="<?= $m['id']; ?>"
                                    data-name="<?= esc($m['name']); ?>"
                                    data-price="<?= (float) $m['price']; ?>"
                                    style="border:1px solid var(--tr-border); background:#fff; border-radius:12px; padding:12px; text-align:left; box-shadow:0 6px 16px rgba(0,0,0,0.06); cursor:pointer;">
                                <div style="font-weight:700; font-size:14px; color:var(--tr-text); margin-bottom:6px;"><?= esc($m['name']); ?></div>
                                <div style="font-size:12px; color:var(--tr-muted-text);">Rp <?= number_format((float) $m['price'], 0, ',', '.'); ?></div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="border:1px solid var(--tr-border); border-radius:12px; background:var(--tr-surface); padding:12px; display:flex; flex-direction:column; gap:10px; min-height:400px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <div style="font-weight:700; font-size:14px;">Keranjang</div>
                    <div style="font-size:12px; color:var(--tr-muted-text);">Tap menu untuk tambah qty</div>
                </div>
                <button type="button" id="clear-cart" class="btn btn-secondary" style="padding:6px 10px; border-radius:8px; font-size:12px;">Kosongkan</button>
            </div>

            <div id="cart-list" style="flex:1; overflow:auto; border:1px dashed var(--tr-border); border-radius:8px; padding:8px; background:#fff;">
                <div id="cart-empty" style="text-align:center; color:var(--tr-muted-text); font-size:12px;">Keranjang kosong</div>
            </div>

            <div style="border-top:1px solid var(--tr-border); padding-top:8px;">
                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:4px;">
                    <span>Total Item</span>
                    <span id="total-items">0</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:14px; font-weight:700;">
                    <span>Total Bayar</span>
                    <span id="total-amount">Rp 0</span>
                </div>
            </div>

            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Simpan Transaksi</button>
            </div>
        </div>
    </form>
</div>

<div id="options-modal" style="display:none; position:fixed; inset:0; background:rgba(20,20,20,0.4); align-items:center; justify-content:center; z-index:60;">
    <div style="background:#fff; border-radius:14px; width:min(520px, 92vw); max-height:86vh; overflow:auto; padding:16px; box-shadow:0 14px 30px rgba(0,0,0,0.18);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <div>
                <div id="options-title" style="font-weight:700; font-size:16px;">Pilih Opsi</div>
                <div style="font-size:12px; color:var(--tr-muted-text);">Pilih sesuai kebutuhan</div>
            </div>
            <button type="button" id="options-close" style="border:none; background:transparent; font-size:18px; cursor:pointer; color:var(--tr-muted-text);">x</button>
        </div>
        <div id="options-body" style="display:flex; flex-direction:column; gap:12px;"></div>
        <div id="options-error" style="display:none; margin-top:10px; padding:8px 10px; border-radius:8px; background:var(--tr-secondary-beige); border:1px solid var(--tr-accent-brown); color:var(--tr-accent-brown); font-size:12px;"></div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Total per item</div>
            <div id="options-total" style="font-weight:700;">Rp 0</div>
        </div>
        <div style="display:flex; gap:8px; margin-top:12px;">
            <button type="button" id="options-cancel" class="btn btn-secondary" style="flex:1;">Batal</button>
            <button type="button" id="options-confirm" class="btn btn-primary" style="flex:1;">Tambah</button>
        </div>
    </div>
</div>

<script>
    (function() {
        const cart = [];
        let lineCounter = 0;
        const cartList = document.getElementById('cart-list');
        const cartEmpty = document.getElementById('cart-empty');
        const totalItemsEl = document.getElementById('total-items');
        const totalAmountEl = document.getElementById('total-amount');
        const form = document.getElementById('pos-form');
        const menuOptions = <?= json_encode($menuOptions ?? []); ?>;

        const modal = document.getElementById('options-modal');
        const modalTitle = document.getElementById('options-title');
        const modalBody = document.getElementById('options-body');
        const modalError = document.getElementById('options-error');
        const modalTotal = document.getElementById('options-total');
        const modalCancel = document.getElementById('options-cancel');
        const modalConfirm = document.getElementById('options-confirm');
        const modalClose = document.getElementById('options-close');

        let pendingMenu = null;
        let pendingGroups = [];

        function renderCart() {
            cartList.innerHTML = '';
            if (cart.length === 0) {
                cartList.appendChild(cartEmpty);
                cartEmpty.style.display = 'block';
                totalItemsEl.textContent = '0';
                totalAmountEl.textContent = 'Rp 0';
                return;
            }
            cartEmpty.style.display = 'none';

            let totalItems = 0;
            let totalAmount = 0;

            cart.forEach((item) => {
                totalItems += item.qty;
                totalAmount += item.qty * item.unitPrice;

                const row = document.createElement('div');
                row.style.display = 'grid';
                row.style.gridTemplateColumns = '1fr auto';
                row.style.alignItems = 'center';
                row.style.padding = '6px 4px';
                row.style.borderBottom = '1px solid var(--tr-border)';

                const left = document.createElement('div');
                const title = document.createElement('div');
                title.style.fontWeight = '700';
                title.textContent = item.name;

                const priceLine = document.createElement('div');
                priceLine.style.fontSize = '11px';
                priceLine.style.color = 'var(--tr-muted-text)';
                priceLine.textContent = 'Rp ' + numberFormat(item.unitPrice);

                left.appendChild(title);
                left.appendChild(priceLine);

                if (item.options.length > 0) {
                    const groupMap = {};
                    item.options.forEach(opt => {
                        if (! groupMap[opt.groupName]) {
                            groupMap[opt.groupName] = [];
                        }
                        groupMap[opt.groupName].push(opt.optionName);
                    });

                    Object.keys(groupMap).forEach(groupName => {
                        const line = document.createElement('div');
                        line.style.fontSize = '11px';
                        line.style.color = 'var(--tr-muted-text)';
                        line.textContent = groupName.toUpperCase() + ': ' + groupMap[groupName].join(', ');
                        left.appendChild(line);
                    });
                }

                const right = document.createElement('div');
                right.style.display = 'flex';
                right.style.alignItems = 'center';
                right.style.gap = '6px';

                const minus = document.createElement('button');
                minus.type = 'button';
                minus.textContent = '-';
                minus.style.cssText = 'width:26px; height:26px; border-radius:6px; border:1px solid var(--tr-border); background:#fff; cursor:pointer;';
                minus.onclick = () => changeQty(item.lineId, -1);

                const qty = document.createElement('div');
                qty.textContent = item.qty;
                qty.style.minWidth = '24px';
                qty.style.textAlign = 'center';
                qty.style.fontWeight = '700';

                const plus = document.createElement('button');
                plus.type = 'button';
                plus.textContent = '+';
                plus.style.cssText = 'width:26px; height:26px; border-radius:6px; border:1px solid var(--tr-border); background:#fff; cursor:pointer;';
                plus.onclick = () => changeQty(item.lineId, 1);

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.textContent = 'x';
                remove.style.cssText = 'width:26px; height:26px; border-radius:6px; border:1px solid var(--tr-border); background:#fff; cursor:pointer; color:var(--tr-accent-brown);';
                remove.onclick = () => removeItem(item.lineId);

                right.append(minus, qty, plus, remove);
                row.append(left, right);
                cartList.appendChild(row);
            });

            totalItemsEl.textContent = totalItems.toString();
            totalAmountEl.textContent = 'Rp ' + numberFormat(totalAmount);
        }

        function changeQty(lineId, delta) {
            const idx = cart.findIndex(item => item.lineId === lineId);
            if (idx === -1) return;
            cart[idx].qty += delta;
            if (cart[idx].qty <= 0) {
                cart.splice(idx, 1);
            }
            renderCart();
        }

        function removeItem(lineId) {
            const idx = cart.findIndex(item => item.lineId === lineId);
            if (idx !== -1) {
                cart.splice(idx, 1);
            }
            renderCart();
        }

        function numberFormat(val) {
            return val.toLocaleString('id-ID');
        }

        function openOptionsModal(menu) {
            pendingMenu = menu;
            pendingGroups = menuOptions[menu.id] || [];

            if (pendingGroups.length === 0) {
                addItemToCart(menu, []);
                return;
            }

            modalTitle.textContent = 'Opsi - ' + menu.name;
            modalBody.innerHTML = '';
            modalError.style.display = 'none';

            pendingGroups.forEach(group => {
                const groupWrap = document.createElement('div');
                groupWrap.style.border = '1px solid var(--tr-border)';
                groupWrap.style.borderRadius = '10px';
                groupWrap.style.padding = '10px';
                groupWrap.dataset.groupId = group.id;
                groupWrap.dataset.min = group.min_select || 0;
                groupWrap.dataset.max = group.max_select || 0;
                groupWrap.dataset.required = group.is_required || 0;

                const header = document.createElement('div');
                header.style.fontWeight = '700';
                header.style.fontSize = '13px';
                header.textContent = group.name;

                const hint = document.createElement('div');
                hint.style.fontSize = '11px';
                hint.style.color = 'var(--tr-muted-text)';
                hint.textContent = buildGroupHint(group);

                const list = document.createElement('div');
                list.style.display = 'flex';
                list.style.flexDirection = 'column';
                list.style.gap = '6px';
                list.style.marginTop = '8px';

                (group.options || []).forEach(opt => {
                    const row = document.createElement('div');
                    row.style.display = 'flex';
                    row.style.alignItems = 'center';
                    row.style.justifyContent = 'space-between';
                    row.style.gap = '10px';
                    row.style.padding = '6px 8px';
                    row.style.border = '1px solid var(--tr-border)';
                    row.style.borderRadius = '8px';
                    row.style.cursor = 'pointer';

                    const left = document.createElement('div');
                    left.style.display = 'flex';
                    left.style.alignItems = 'center';
                    left.style.gap = '6px';

                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.dataset.groupId = group.id;
                    checkbox.dataset.groupName = group.name;
                    checkbox.dataset.optionId = opt.id;
                    checkbox.dataset.optionName = opt.name;
                    checkbox.dataset.priceDelta = opt.price_delta || 0;

                    checkbox.addEventListener('change', () => {
                        enforceGroupMax(group.id, group.max_select || 0, checkbox);
                        updateModalTotal();
                    });

                    const text = document.createElement('span');
                    text.textContent = opt.name;

                    left.appendChild(checkbox);
                    left.appendChild(text);

                    const price = document.createElement('div');
                    price.style.textAlign = 'right';
                    price.style.fontSize = '12px';
                    price.style.color = 'var(--tr-muted-text)';
                    const delta = parseFloat(opt.price_delta || 0);
                    price.textContent = delta > 0 ? '+ Rp ' + numberFormat(delta) : 'Rp 0';

                    row.appendChild(left);
                    row.appendChild(price);
                    row.addEventListener('click', (e) => {
                        if (e.target && e.target.tagName === 'INPUT') {
                            return;
                        }
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change'));
                    });

                    list.appendChild(row);
                });

                groupWrap.appendChild(header);
                groupWrap.appendChild(hint);
                groupWrap.appendChild(list);
                modalBody.appendChild(groupWrap);
            });

            updateModalTotal();
            modal.style.display = 'flex';
        }

        function buildGroupHint(group) {
            const min = parseInt(group.min_select || 0, 10);
            const max = parseInt(group.max_select || 0, 10);
            const required = parseInt(group.is_required || 0, 10) === 1;
            let hint = required ? 'Wajib' : 'Opsional';
            if (max > 0) {
                hint += ' - Maks ' + max;
            }
            if (min > 0) {
                hint += ' - Min ' + min;
            }
            return hint;
        }

        function enforceGroupMax(groupId, max, changedEl) {
            const maxInt = parseInt(max || 0, 10);
            if (maxInt <= 0) return;
            const checked = document.querySelectorAll('input[data-group-id="' + groupId + '"]:checked');
            if (checked.length > maxInt) {
                changedEl.checked = false;
            }
        }

        function updateModalTotal() {
            let total = pendingMenu ? pendingMenu.price : 0;
            document.querySelectorAll('#options-body input[data-option-id]:checked').forEach(input => {
                total += parseFloat(input.dataset.priceDelta || '0');
            });
            modalTotal.textContent = 'Rp ' + numberFormat(total);
        }

        function validateSelections() {
            const errors = [];
            pendingGroups.forEach(group => {
                const min = parseInt(group.min_select || 0, 10);
                const max = parseInt(group.max_select || 0, 10);
                const required = parseInt(group.is_required || 0, 10) === 1;
                const minRequired = min > 0 ? min : (required ? 1 : 0);
                const selectedCount = document.querySelectorAll('input[data-group-id="' + group.id + '"]:checked').length;
                if (minRequired > 0 && selectedCount < minRequired) {
                    errors.push('Grup "' + group.name + '" harus dipilih minimal ' + minRequired + ' opsi.');
                }
                if (max > 0 && selectedCount > max) {
                    errors.push('Grup "' + group.name + '" melebihi batas maksimal ' + max + ' opsi.');
                }
            });
            return errors;
        }

        function buildLineKey(menuId, selections) {
            if (!selections || selections.length === 0) {
                return menuId + '|no-options';
            }
            const parts = selections
                .map(opt => `${opt.optionId}:${opt.qtySelected || 1}`)
                .sort();
            return menuId + '|' + parts.join(',');
        }

        function addItemToCart(menu, selections) {
            const optionDelta = selections.reduce((sum, opt) => sum + (opt.priceDelta || 0), 0);
            const unitPrice = menu.price + optionDelta;
            const lineKey = buildLineKey(menu.id, selections);
            const existing = cart.find(item => item.lineKey === lineKey);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({
                    lineId: ++lineCounter,
                    lineKey: lineKey,
                    menuId: menu.id,
                    name: menu.name,
                    qty: 1,
                    unitPrice: unitPrice,
                    options: selections,
                });
            }
            renderCart();
        }

        function closeModal() {
            modal.style.display = 'none';
            modalBody.innerHTML = '';
            modalError.style.display = 'none';
            pendingMenu = null;
            pendingGroups = [];
        }

        document.querySelectorAll('.menu-card').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.dataset.id, 10);
                const name = this.dataset.name;
                const price = parseFloat(this.dataset.price);
                openOptionsModal({ id, name, price });
            });
        });

        document.getElementById('clear-cart').addEventListener('click', function() {
            cart.length = 0;
            renderCart();
        });

        form.addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Keranjang masih kosong.');
                return;
            }
            // buang hidden items lama
            form.querySelectorAll('.cart-hidden').forEach(el => el.remove());
            let idx = 0;
            cart.forEach(item => {
                const base = 'items[' + idx + ']';
                addHidden(base + '[menu_id]', item.menuId);
                addHidden(base + '[qty]', item.qty);
                addHidden(base + '[price]', item.unitPrice);
                if (item.options.length > 0) {
                    item.options.forEach((opt, optIndex) => {
                        const optBase = base + '[options][' + optIndex + ']';
                        addHidden(optBase + '[option_id]', opt.optionId);
                        addHidden(optBase + '[qty_selected]', opt.qtySelected || 1);
                    });
                }
                idx++;
            });
        });

        function addHidden(name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            input.className = 'cart-hidden';
            form.appendChild(input);
        }

        modalCancel.addEventListener('click', closeModal);
        modalClose.addEventListener('click', closeModal);
        modalConfirm.addEventListener('click', function() {
            if (! pendingMenu) {
                closeModal();
                return;
            }
            const errors = validateSelections();
            if (errors.length > 0) {
                modalError.style.display = 'block';
                modalError.innerHTML = '<ul style="margin:0; padding-left:16px;"><li>' + errors.join('</li><li>') + '</li></ul>';
                return;
            }

            const selections = [];
            document.querySelectorAll('#options-body input[data-option-id]:checked').forEach(input => {
                selections.push({
                    optionId: parseInt(input.dataset.optionId, 10),
                    optionName: input.dataset.optionName,
                    groupName: input.dataset.groupName,
                    priceDelta: parseFloat(input.dataset.priceDelta || '0'),
                    qtySelected: 1,
                });
            });

            addItemToCart(pendingMenu, selections);
            closeModal();
        });

        renderCart();
    })();
</script>

<?= $this->endSection() ?>
