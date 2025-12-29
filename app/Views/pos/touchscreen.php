<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="pos-touch">
    <div class="card pos-card">
        <div class="pos-header pos-header-sticky">
            <div>
                <h2 class="pos-title"><?= esc($title); ?></h2>
                <p class="pos-subtitle"><?= esc($subtitle); ?></p>
            </div>
            <div>
                <span class="pill pos-pill"><?= esc($today); ?></span>
            </div>
        </div>

        <?php if (session()->getFlashdata('errors')): ?>
            <?php $errs = (array) session()->getFlashdata('errors'); ?>
            <div class="pos-alert">
                <ul style="margin:0; padding-left:16px;">
                    <?php foreach ($errs as $e): ?>
                        <li><?= $e; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="pos-form" method="post" action="<?= site_url('transactions/sales/store'); ?>" class="pos-grid">
            <?= csrf_field(); ?>
            <input type="hidden" name="sale_date" value="<?= esc($today); ?>">
            <input type="hidden" name="invoice_no" value="">

            <!-- LEFT: MENU -->
            <div id="menu-list" class="pos-menu-list">
                <?php foreach ($menusByCategory as $cat => $menus): ?>
                    <details open class="pos-cat">
                        <summary class="pos-cat-summary">
                            <span><?= esc($cat); ?></span>
                            <span class="menu-toggle-symbol pos-cat-symbol">-</span>
                        </summary>

                        <div class="pos-cat-grid">
                            <?php foreach ($menus as $m): ?>
                                <button type="button"
                                    class="menu-card pos-menu-card"
                                    data-id="<?= $m['id']; ?>"
                                    data-name="<?= esc($m['name']); ?>"
                                    data-price="<?= (float) $m['price']; ?>">
                                    <div class="pos-menu-name"><?= esc($m['name']); ?></div>
                                    <div class="pos-menu-price">Rp <?= number_format((float) $m['price'], 0, ',', '.'); ?></div>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>

            <!-- RIGHT: CART + PAYMENT -->
            <div class="pos-side">

                <!-- TOP: Customer (tetap) -->
                <div class="pos-side-top">
                    <div>
                        <label class="tr-label">Customer</label>
                        <?php
                        $defaultId = (int) ($defaultCustomerId ?? 0);
                        $selectedId = (int) old('customer_id', $defaultId);
                        $selectedName = 'Tamu';
                        foreach (($customers ?? []) as $cust) {
                            if ((int) ($cust['id'] ?? 0) === $selectedId) {
                                $selectedName = (string) ($cust['name'] ?? 'Tamu');
                                break;
                            }
                        }
                        ?>
                        <div class="pos-customer-row">
                            <input type="text"
                                id="customer-display"
                                value="<?= esc($selectedName); ?>"
                                readonly
                                class="tr-control"
                                style="flex:1;">
                            <button type="button"
                                id="customer-open"
                                title="Pilih Customer"
                                class="tr-icon-btn pos-icon-btn">👤</button>
                        </div>
                        <input type="hidden" name="customer_id" id="customer-id" value="<?= esc((string) $selectedId); ?>" required>
                    </div>
                </div>

                <!-- CARD: Keranjang + Payment -->
                <div class="tr-card tr-card--outlined pos-cart-card">

                    <!-- HEADER: Keranjang -->
                    <div class="tr-card__header">
                        <div>
                            <div class="tr-card__title">Keranjang</div>
                            <div class="tr-card__subtitle">Tap menu untuk tambah qty</div>
                        </div>

                        <div class="tr-card__actions">
                            <button type="button" id="clear-cart" class="tr-btn tr-btn--secondary tr-btn--sm">Kosongkan</button>
                        </div>
                    </div>

                    <!-- BODY: Scrollable Cart -->
                    <div class="tr-card__body pos-cart-body">
                        <div id="cart-list" class="pos-cart-list">
                            <div id="cart-empty" class="pos-empty">Keranjang kosong</div>
                        </div>
                    </div>

                    <!-- FOOTER: Totals + Payment + Actions -->
                    <div class="tr-card__footer pos-cart-footer">

                        <div class="pos-totals">
                            <div class="pos-row">
                                <span>Total Item</span>
                                <span id="total-items">0</span>
                            </div>
                            <div class="pos-row pos-row-strong">
                                <span>Total Bayar</span>
                                <span id="total-amount">Rp 0</span>
                            </div>
                        </div>

                        <div class="tr-divider"></div>

                        <!-- PAYMENT (tetap) -->
                        <div class="pos-pay" id="pos-pay">
                            <div class="pos-pay-method">
                                <label class="tr-label">Metode</label>
                                <select name="payment_method" id="payment-method" required class="tr-control">
                                    <?php $method = old('payment_method', 'cash'); ?>
                                    <option value="cash" <?= $method === 'cash' ? 'selected' : ''; ?>>Cash</option>
                                    <option value="qris" <?= $method === 'qris' ? 'selected' : ''; ?>>QRIS</option>
                                </select>
                                <div class="pos-pay-hint" id="pay-hint"></div>
                            </div>

                            <div class="pos-pay-amounts">
                                <div>
                                    <label class="tr-label">Jumlah Bayar</label>
                                    <div class="pos-money" id="money-paid">
                                        <span class="pos-money-prefix">Rp</span>
                                        <input type="number"
                                            min="0"
                                            step="1"
                                            name="amount_paid"
                                            id="amount-paid"
                                            required
                                            value="<?= esc(old('amount_paid', '')); ?>"
                                            placeholder="0"
                                            class="tr-control pos-money-input">
                                    </div>
                                </div>

                                <div>
                                    <label class="tr-label">Kembalian</label>
                                    <div class="pos-money is-readonly" id="money-change">
                                        <span class="pos-money-prefix">Rp</span>
                                        <input type="text"
                                            id="change-display"
                                            value="0"
                                            readonly
                                            class="tr-control pos-money-input">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tr-actions pos-actions">
                            <button type="submit" class="tr-btn tr-btn--primary tr-btn--block">
                                <span class="tr-btn__label">Simpan Transaksi</span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- OPTIONS MODAL -->
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

    <!-- CUSTOMER MODAL -->
    <div id="customer-modal" class="pos-modal" style="display:none; position:fixed; inset:0; background:rgba(20,20,20,0.4); align-items:center; justify-content:center; z-index:70;">
        <div class="pos-modal-dialog" style="background:#fff; border-radius:14px; width:min(560px, 94vw); max-height:86vh; overflow:hidden; padding:16px; box-shadow:0 14px 30px rgba(0,0,0,0.18);">
            <div class="pos-modal-head" style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:10px;">
                <div>
                    <div class="pos-modal-title" style="font-weight:900; font-size:18px; line-height:1.2;">Pilih Customer</div>
                    <div class="pos-modal-sub" style="font-size:12px; color:var(--tr-muted-text); margin-top:3px;">Cari dan pilih customer</div>
                </div>
                <button type="button" id="customer-close" class="pos-modal-x"
                    style="width:36px; height:36px; border-radius:12px; border:1px solid var(--tr-border); background:#fff; cursor:pointer;">X</button>
            </div>

            <div class="pos-modal-search" style="margin-bottom:10px;">
                <input type="text"
                    id="customer-filter"
                    placeholder="Cari nama / telepon / email..."
                    class="tr-control"
                    style="width:100%;">
            </div>

            <div id="customer-recent-wrap" style="display:none; margin-bottom:10px;">
                <div style="font-size:12px; font-weight:900; margin:0 0 6px; color:var(--tr-text);">Recent</div>
                <div id="customer-recent" style="display:flex; flex-direction:column; gap:6px;"></div>
            </div>

            <div id="customer-list" class="pos-modal-list" style="max-height:56vh; overflow:auto; display:flex; flex-direction:column; gap:8px;">
                <?php foreach (($customers ?? []) as $cust): ?>
                    <?php
                    $cid = (int) ($cust['id'] ?? 0);
                    $cname = (string) ($cust['name'] ?? '');
                    $cphone = (string) ($cust['phone'] ?? '');
                    $cemail = (string) ($cust['email'] ?? '');
                    $search = strtolower(trim($cname . ' ' . $cphone . ' ' . $cemail));
                    ?>
                    <button type="button"
                        class="customer-item"
                        data-id="<?= $cid; ?>"
                        data-name="<?= esc($cname); ?>"
                        data-search="<?= esc($search, 'attr'); ?>"
                        style="text-align:left; border:1px solid var(--tr-border); border-radius:12px; background:#fff; cursor:pointer;">
                        <div style="font-weight:900; padding:10px 12px 2px;"><?= esc($cname !== '' ? $cname : '-'); ?></div>
                        <?php if ($cphone !== '' || $cemail !== ''): ?>
                            <div style="font-size:11px; color:var(--tr-muted-text); padding:0 12px 10px;">
                                <?= esc(trim($cphone . ($cphone !== '' && $cemail !== '' ? ' • ' : '') . $cemail)); ?>
                            </div>
                        <?php else: ?>
                            <div style="padding:0 12px 10px;"></div>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div id="customer-empty" class="pos-modal-empty" style="display:none; margin-top:10px; font-size:12px; color:var(--tr-muted-text); text-align:center;">
                Tidak ada hasil.
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

            const paymentMethodEl = document.getElementById('payment-method');
            const amountPaidEl = document.getElementById('amount-paid');
            const changeDisplayEl = document.getElementById('change-display');

            const customerDisplayEl = document.getElementById('customer-display');
            const customerIdEl = document.getElementById('customer-id');
            const customerModal = document.getElementById('customer-modal');
            const customerOpen = document.getElementById('customer-open');
            const customerClose = document.getElementById('customer-close');
            const customerFilter = document.getElementById('customer-filter');
            const customerList = document.getElementById('customer-list');
            const customerEmpty = document.getElementById('customer-empty');

            const recentWrap = document.getElementById('customer-recent-wrap');
            const recentBox = document.getElementById('customer-recent');
            const recentCustomerIds = []; // newest first, max 3

            let lastTotal = 0;

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
            let lastFocusEl = null;

            /* =========================================================
               Helpers
               ========================================================= */
            function numberFormat(val) {
                return (val || 0).toLocaleString('id-ID');
            }

            function updatePaymentInfo() {
                if (!paymentMethodEl || !amountPaidEl || !changeDisplayEl) return;

                const payWrap = document.getElementById('pos-pay');
                const hintEl = document.getElementById('pay-hint');
                const moneyPaid = document.getElementById('money-paid');
                const moneyChange = document.getElementById('money-change');

                const method = (paymentMethodEl.value || 'cash').toLowerCase();
                if (payWrap) payWrap.classList.toggle('is-qris', method === 'qris');

                if (method === 'qris') {
                    amountPaidEl.value = String(Math.round(lastTotal));
                    amountPaidEl.readOnly = true;

                    changeDisplayEl.value = '0';

                    if (moneyPaid) moneyPaid.classList.add('is-readonly');
                    if (moneyChange) moneyChange.classList.add('is-readonly');

                    if (hintEl) hintEl.textContent = 'QRIS: nominal otomatis mengikuti total dan terkunci.';
                } else {
                    amountPaidEl.readOnly = false;

                    if (moneyPaid) moneyPaid.classList.remove('is-readonly');
                    if (moneyChange) moneyChange.classList.add('is-readonly');

                    if (hintEl) hintEl.textContent = 'Cash: isi nominal bayar, kembalian otomatis dihitung.';
                }

                const paid = parseFloat(String(amountPaidEl.value).replace(',', '.')) || 0;
                const change = Math.max(0, paid - lastTotal);
                changeDisplayEl.value = numberFormat(change);
            }

            /* =========================================================
               Cart
               ========================================================= */
            function renderCart() {
                cartList.innerHTML = '';

                if (cart.length === 0) {
                    cartList.appendChild(cartEmpty);
                    cartEmpty.style.display = 'block';
                    totalItemsEl.textContent = '0';
                    totalAmountEl.textContent = 'Rp 0';
                    lastTotal = 0;
                    updatePaymentInfo();
                    return;
                }
                cartEmpty.style.display = 'none';

                let totalItems = 0;
                let totalAmount = 0;

                cart.forEach((item, idx) => {
                    totalItems += item.qty;
                    totalAmount += item.qty * item.unitPrice;

                    const row = document.createElement('div');
                    row.className = 'pos-cart-item' + (idx % 2 === 0 ? ' is-even' : '');

                    const left = document.createElement('div');
                    left.className = 'pos-cart-left';

                    const title = document.createElement('div');
                    title.className = 'pos-cart-name';
                    title.textContent = item.name;

                    const priceLine = document.createElement('div');
                    priceLine.className = 'pos-cart-price';
                    priceLine.textContent = 'Rp ' + numberFormat(item.unitPrice);

                    left.appendChild(title);
                    left.appendChild(priceLine);

                    if (item.options.length > 0) {
                        const groupMap = {};
                        item.options.forEach(opt => {
                            if (!groupMap[opt.groupName]) groupMap[opt.groupName] = [];
                            groupMap[opt.groupName].push(opt.optionName);
                        });

                        Object.keys(groupMap).forEach(groupName => {
                            const line = document.createElement('div');
                            line.className = 'pos-cart-meta';
                            line.textContent = groupName.toUpperCase() + ': ' + groupMap[groupName].join(', ');
                            left.appendChild(line);
                        });
                    }

                    const noteWrap = document.createElement('div');
                    noteWrap.className = 'pos-cart-note';

                    const noteLabel = document.createElement('div');
                    noteLabel.className = 'pos-cart-note-label';
                    noteLabel.textContent = 'Catatan';

                    const noteInput = document.createElement('textarea');
                    noteInput.rows = 2;
                    noteInput.placeholder = 'Catatan khusus (opsional)';
                    noteInput.value = item.note || '';

                    noteInput.addEventListener('input', () => {
                        item.note = noteInput.value;
                        item.lineKey = buildLineKey(item.menuId, item.options, item.note);
                    });

                    noteWrap.appendChild(noteLabel);
                    noteWrap.appendChild(noteInput);
                    left.appendChild(noteWrap);

                    const right = document.createElement('div');
                    right.className = 'pos-cart-right';

                    const minus = document.createElement('button');
                    minus.type = 'button';
                    minus.className = 'tr-icon-btn pos-mini-btn';
                    minus.textContent = '-';
                    minus.onclick = () => changeQty(item.lineId, -1);

                    const qty = document.createElement('div');
                    qty.className = 'pos-qty';
                    qty.textContent = item.qty;

                    const plus = document.createElement('button');
                    plus.type = 'button';
                    plus.className = 'tr-icon-btn pos-mini-btn';
                    plus.textContent = '+';
                    plus.onclick = () => changeQty(item.lineId, 1);

                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'tr-icon-btn pos-mini-btn is-danger';
                    remove.textContent = '🗑';
                    remove.title = 'Hapus item';
                    remove.onclick = () => removeItem(item.lineId);

                    right.append(minus, qty, plus, remove);
                    row.append(left, right);
                    cartList.appendChild(row);
                });

                totalItemsEl.textContent = totalItems.toString();
                totalAmountEl.textContent = 'Rp ' + numberFormat(totalAmount);
                lastTotal = totalAmount;
                updatePaymentInfo();
            }

            function changeQty(lineId, delta) {
                const idx = cart.findIndex(item => item.lineId === lineId);
                if (idx === -1) return;
                cart[idx].qty += delta;
                if (cart[idx].qty <= 0) cart.splice(idx, 1);
                renderCart();
            }

            function removeItem(lineId) {
                const idx = cart.findIndex(item => item.lineId === lineId);
                if (idx !== -1) cart.splice(idx, 1);
                renderCart();
            }

            function buildLineKey(menuId, selections, note) {
                if (!selections || selections.length === 0) {
                    const noteKey = (note || '').trim().toLowerCase();
                    return menuId + '|no-options|' + noteKey;
                }
                const parts = selections.map(opt => `${opt.optionId}:${opt.qtySelected || 1}`).sort();
                const noteKey = (note || '').trim().toLowerCase();
                return menuId + '|' + parts.join(',') + '|' + noteKey;
            }

            function addItemToCart(menu, selections) {
                const optionDelta = selections.reduce((sum, opt) => sum + (opt.priceDelta || 0), 0);
                const unitPrice = menu.price + optionDelta;
                const lineKey = buildLineKey(menu.id, selections, '');
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
                        note: '',
                    });
                }
                renderCart();
            }

            /* =========================================================
               Options Modal
               ========================================================= */
            function closeModal() {
                modal.style.display = 'none';
                modalBody.innerHTML = '';
                modalError.style.display = 'none';
                pendingMenu = null;
                pendingGroups = [];
            }

            function buildGroupHint(group) {
                const min = parseInt(group.min_select || 0, 10);
                const max = parseInt(group.max_select || 0, 10);
                const required = parseInt(group.is_required || 0, 10) === 1;

                const status = required ? 'Wajib' : 'Opsional';
                const rules = [];
                if (min > 0) rules.push('Min ' + min);
                if (max > 0) rules.push('Maks ' + max);

                return rules.length ? status + '\n' + rules.join(' • ') : status;
            }

            function enforceGroupMax(groupId, max, changedEl) {
                const maxInt = parseInt(max || 0, 10);
                if (maxInt <= 0) return;
                const checked = document.querySelectorAll('input[data-group-id="' + groupId + '"]:checked');
                if (checked.length > maxInt) changedEl.checked = false;
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
                            if (e.target && e.target.tagName === 'INPUT') return;
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

            /* =========================================================
               CUSTOMER MODAL (FIX: badges never overlap)
               ========================================================= */
            function ensureBadgeStructure(btn) {
                if (!btn) return;

                // already normalized?
                if (btn.querySelector('.customer-left') && btn.querySelector('.customer-badges')) return;

                const left = document.createElement('div');
                left.className = 'customer-left';

                // move all existing children into left
                Array.from(btn.childNodes).forEach(node => left.appendChild(node));

                const badges = document.createElement('div');
                badges.className = 'customer-badges';

                btn.innerHTML = '';
                btn.appendChild(left);
                btn.appendChild(badges);
            }

            function normalizeCustomerItems() {
                if (!customerList) return;
                customerList.querySelectorAll('.customer-item').forEach(btn => ensureBadgeStructure(btn));
            }

            function setBadges(btn, {
                pinned = false,
                selected = false
            } = {}) {
                if (!btn) return;
                ensureBadgeStructure(btn);

                const badges = btn.querySelector('.customer-badges');
                if (!badges) return;

                // clear old
                badges.innerHTML = '';

                if (selected) {
                    const s = document.createElement('span');
                    s.className = 'customer-badge is-selected';
                    s.textContent = 'Dipilih';
                    badges.appendChild(s);
                }
                if (pinned) {
                    const p = document.createElement('span');
                    p.className = 'customer-badge is-pin';
                    p.textContent = 'PIN';
                    badges.appendChild(p);
                }
            }

            function markSelectedCustomerInModal() {
                if (!customerList || !customerIdEl) return;

                const selectedId = String(customerIdEl.value || '');

                customerList.querySelectorAll('.customer-item').forEach(btn => {
                    btn.classList.remove('is-selected');
                    const pinned = btn.classList.contains('is-pinned');
                    setBadges(btn, {
                        pinned,
                        selected: false
                    });
                });

                if (!selectedId) return;

                const selectedBtn = customerList.querySelector('.customer-item[data-id="' + selectedId + '"]');
                if (!selectedBtn) return;

                selectedBtn.classList.add('is-selected');

                const pinned = selectedBtn.classList.contains('is-pinned');
                setBadges(selectedBtn, {
                    pinned,
                    selected: true
                });
            }

            function pinGuestCustomerIfExists() {
                if (!customerList) return;

                // cari customer bernama "Tamu" (case-insensitive)
                const guestBtn = Array.from(customerList.querySelectorAll('.customer-item'))
                    .find(b => ((b.getAttribute('data-name') || '').trim().toLowerCase() === 'tamu'));

                if (!guestBtn) return;

                // pindah ke paling atas list (silent)
                customerList.prepend(guestBtn);
            }

            function pushRecentCustomer(id) {
                const sid = String(id || '');
                if (!sid) return;

                const idx = recentCustomerIds.indexOf(sid);
                if (idx !== -1) recentCustomerIds.splice(idx, 1);
                recentCustomerIds.unshift(sid);
                while (recentCustomerIds.length > 3) recentCustomerIds.pop();
            }

            function cloneCustomerButton(originalBtn) {
                const btn = originalBtn.cloneNode(true);

                // ensure no duplicated badges
                btn.classList.remove('is-selected');
                btn.querySelectorAll('.customer-selected-badge,.customer-pin-badge,.customer-badge').forEach(n => n.remove());

                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id') || '';
                    const name = btn.getAttribute('data-name') || '';
                    if (customerIdEl) customerIdEl.value = id;
                    if (customerDisplayEl) customerDisplayEl.value = name || 'Tamu';
                    pushRecentCustomer(id);
                    renderRecentCustomers();
                    closeCustomerModal();
                });

                return btn;
            }

            function renderRecentCustomers() {
                if (!recentWrap || !recentBox || !customerList) return;

                recentBox.innerHTML = '';
                let visible = 0;

                recentCustomerIds.forEach(id => {
                    const original = customerList.querySelector('.customer-item[data-id="' + id + '"]');
                    if (!original) return;
                    recentBox.appendChild(cloneCustomerButton(original));
                    visible += 1;
                });

                recentWrap.style.display = visible > 0 ? '' : 'none';
            }

            function selectCustomerById(id) {
                if (!customerList || !customerIdEl || !customerDisplayEl) return;

                const btn = customerList.querySelector('.customer-item[data-id="' + String(id) + '"]');
                if (!btn) return;

                const cid = btn.getAttribute('data-id') || '';
                const name = btn.getAttribute('data-name') || '';

                customerIdEl.value = cid;
                customerDisplayEl.value = name || 'Tamu';

                pushRecentCustomer(cid);
                renderRecentCustomers();
                markSelectedCustomerInModal();
                closeCustomerModal();
            }

            function filterCustomerList() {
                if (!customerFilter || !customerList) return;

                const q = customerFilter.value.toLowerCase().trim();
                let visible = 0;

                customerList.querySelectorAll('.customer-item').forEach(btn => {
                    const hay = (btn.dataset.search || '').toLowerCase();
                    const show = q === '' || hay.includes(q);
                    btn.style.display = show ? '' : 'none';
                    if (show) visible += 1;
                });

                if (customerEmpty) customerEmpty.style.display = visible === 0 ? '' : 'none';
            }

            function openCustomerModal() {
                lastFocusEl = document.activeElement;
                if (!customerModal) return;

                customerModal.style.display = 'flex';

                normalizeCustomerItems(); // <--- IMPORTANT: normalize first
                pinGuestCustomerIfExists(); // <--- then pin (badge into badges container)
                renderRecentCustomers();
                markSelectedCustomerInModal();

                if (customerFilter) {
                    customerFilter.value = '';
                    filterCustomerList();
                    customerFilter.focus();
                }
            }

            function closeCustomerModal() {
                if (!customerModal) return;
                customerModal.style.display = 'none';
                if (lastFocusEl && typeof lastFocusEl.focus === 'function') lastFocusEl.focus();
            }

            /* =========================================================
               Bindings
               ========================================================= */
            document.querySelectorAll('.menu-card').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.dataset.id, 10);
                    const name = this.dataset.name;
                    const price = parseFloat(this.dataset.price);
                    openOptionsModal({
                        id,
                        name,
                        price
                    });
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
                const paid = parseFloat(String(amountPaidEl?.value || '0').replace(',', '.')) || 0;
                if (paid < lastTotal) {
                    e.preventDefault();
                    alert('Pembayaran kurang dari total.');
                    return;
                }

                form.querySelectorAll('.cart-hidden').forEach(el => el.remove());

                let idx = 0;
                cart.forEach(item => {
                    const base = 'items[' + idx + ']';
                    addHidden(base + '[menu_id]', item.menuId);
                    addHidden(base + '[qty]', item.qty);
                    addHidden(base + '[price]', item.unitPrice);
                    addHidden(base + '[note]', item.note || '');
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
                if (!pendingMenu) {
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

            if (paymentMethodEl) paymentMethodEl.addEventListener('change', updatePaymentInfo);
            if (amountPaidEl) amountPaidEl.addEventListener('input', updatePaymentInfo);

            if (customerOpen) customerOpen.addEventListener('click', openCustomerModal);
            if (customerClose) customerClose.addEventListener('click', closeCustomerModal);

            if (customerModal) {
                customerModal.addEventListener('click', function(e) {
                    if (e.target === customerModal) closeCustomerModal();
                });
            }
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) closeModal();
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key !== 'Escape') return;

                if (customerModal && customerModal.style.display === 'flex') {
                    closeCustomerModal();
                    return;
                }
                if (modal && modal.style.display === 'flex') {
                    closeModal();
                    return;
                }
            });

            if (customerFilter) customerFilter.addEventListener('input', filterCustomerList);

            if (customerFilter) {
                customerFilter.addEventListener('keydown', function(e) {
                    if (e.key !== 'Enter') return;
                    e.preventDefault();

                    const first = customerList ? [...customerList.querySelectorAll('.customer-item')]
                        .find(btn => btn.style.display !== 'none') : null;

                    if (first) {
                        const id = first.getAttribute('data-id') || '';
                        selectCustomerById(id);
                    }
                });
            }

            if (customerList) {
                customerList.addEventListener('click', function(e) {
                    const btn = e.target.closest('.customer-item');
                    if (!btn) return;
                    const id = btn.getAttribute('data-id') || '';
                    selectCustomerById(id);
                });
            }

            function syncMenuToggle(detailsEl) {
                const symbol = detailsEl.querySelector('.menu-toggle-symbol');
                if (!symbol) return;
                symbol.textContent = detailsEl.open ? '-' : '+';
            }

            document.querySelectorAll('#menu-list details').forEach(detailsEl => {
                syncMenuToggle(detailsEl);
                detailsEl.addEventListener('toggle', function() {
                    syncMenuToggle(detailsEl);
                });
            });

            renderCart();
        })();
    </script>
</div>

<?= $this->endSection() ?>
