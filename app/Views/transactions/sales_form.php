<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="tr-card">

    <div class="tr-card-header">
        <div>
            <h2 class="tr-card-title">Input Penjualan</h2>
            <p class="tr-card-subtitle">
                Catat transaksi penjualan harian. Beberapa item menu per nota.
            </p>
        </div>
        <a href="<?= site_url('transactions/sales'); ?>" class="tr-btn tr-btn-ghost tr-btn-sm">
            ← Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="tr-alert tr-alert-warning">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <?php $errors = session('errors') ?? []; ?>

    <?php if (! empty($errors)): ?>
        <div class="tr-alert tr-alert-warning">
            <div style="font-weight:600; margin-bottom:4px;">Terjadi kesalahan input:</div>
            <ul style="margin:0 0 0 18px; padding:0;">
                <?php foreach ($errors as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('transactions/sales/store'); ?>" method="post">
        <?= csrf_field() ?>

        <!-- HEADER -->
        <div class="tr-form-grid">
            <div class="tr-form-group">
                <label class="tr-label">
                    Tanggal Transaksi
                </label>
                <input type="date"
                    name="sale_date"
                    required
                    value="<?= old('sale_date', $today ?? date('Y-m-d')); ?>"
                    class="tr-control">
            </div>

            <div class="tr-form-group">
                <label class="tr-label">
                    No. Invoice (opsional)
                </label>
                <input type="text"
                    name="invoice_no"
                    value="<?= old('invoice_no'); ?>"
                    placeholder="mis: POS-2025-001"
                    class="tr-control">
            </div>

            <div class="tr-form-group">
                <label class="tr-label">
                    Customer
                </label>
                <select name="customer_id"
                    required
                    class="tr-control">
                    <?php
                    $defaultId = (int) ($defaultCustomerId ?? 0);
                    $selectedId = (int) old('customer_id', $defaultId);
                    ?>
                    <?php foreach (($customers ?? []) as $cust): ?>
                        <?php $cid = (int) ($cust['id'] ?? 0); ?>
                        <option value="<?= $cid; ?>" <?= $selectedId === $cid ? 'selected' : ''; ?>>
                            <?= esc($cust['name'] ?? '-'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="tr-form-help">
                    Default otomatis ke "Tamu" jika belum memilih.
                </div>
            </div>

            <div class="tr-form-group">
                <label class="tr-label">
                    Metode Pembayaran
                </label>
                <select name="payment_method"
                    id="payment-method"
                    required
                    class="tr-control">
                    <?php $method = old('payment_method', 'cash'); ?>
                    <option value="cash" <?= $method === 'cash' ? 'selected' : ''; ?>>Cash</option>
                    <option value="qris" <?= $method === 'qris' ? 'selected' : ''; ?>>QRIS (manual)</option>
                </select>
            </div>

            <div class="tr-form-group">
                <label class="tr-label">
                    Jumlah Bayar
                </label>
                <input type="number"
                    min="0"
                    step="1"
                    name="amount_paid"
                    id="amount-paid"
                    required
                    value="<?= esc(old('amount_paid', '')); ?>"
                    placeholder="masukkan nominal"
                    class="tr-control">
            </div>

            <div class="tr-form-group">
                <label class="tr-label">
                    Kembalian
                </label>
                <input type="text"
                    id="change-display"
                    value="Rp 0"
                    readonly
                    class="tr-control">
            </div>
        </div>

        <div class="tr-form-group">
            <label class="tr-label">
                Catatan (opsional)
            </label>
            <textarea name="notes"
                rows="2"
                class="tr-control"><?= old('notes'); ?></textarea>
        </div>

        <!-- DETAIL ITEMS -->
        <div class="tr-section-header">
            <div class="tr-section-label">
                Detail item penjualan
            </div>
            <button type="button"
                id="btn-add-row"
                class="tr-btn tr-btn-primary tr-btn-sm">
                + Tambah Item
            </button>
        </div>

        <div class="tr-table-wrapper">
            <table class="tr-table" id="items-table">
                <thead>
                    <tr>
                        <th style="min-width:160px;">Menu</th>
                        <th class="tr-text-right" style="min-width:60px;">Qty</th>
                        <th class="tr-text-right" style="min-width:90px;">Harga</th>
                        <th class="tr-text-right" style="min-width:90px;">Subtotal</th>
                        <th class="tr-text-center" style="width:60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    <!-- akan diisi via JS (1 row default) -->
                </tbody>
            </table>
        </div>

        <div class="tr-total-section">
            <div class="tr-text-right">
                <div class="tr-label">Total</div>
                <div id="grand-total-display" class="tr-total-amount">
                    Rp 0
                </div>
            </div>
        </div>

        <div class="tr-form-actions">
            <button type="submit" class="tr-btn tr-btn-primary">
                Simpan Penjualan
            </button>
        </div>

    </form>

</div>

<script>
    (function() {
        let rowIndex = 0;

        const menus = <?= json_encode(array_map(function ($m) {
                            return [
                                'id'    => $m['id'],
                                'name'  => $m['name'],
                                'price' => (float) ($m['price'] ?? 0),
                            ];
                        }, $menus ?? [])); ?>;

        const itemsBody = document.getElementById('items-body');
        const btnAddRow = document.getElementById('btn-add-row');
        const grandTotalEl = document.getElementById('grand-total-display');
        const paymentMethodEl = document.getElementById('payment-method');
        const amountPaidEl = document.getElementById('amount-paid');
        const changeDisplayEl = document.getElementById('change-display');
        let lastTotal = 0;

        function formatRupiah(num) {
            num = Number(num) || 0;
            return 'Rp ' + num.toLocaleString('id-ID', {
                maximumFractionDigits: 0
            });
        }

        function recalcRow(row) {
            const qtyInput = row.querySelector('.item-qty');
            const priceInput = row.querySelector('.item-price');
            const subCell = row.querySelector('.item-subtotal');

            const qty = parseFloat(qtyInput.value.replace(',', '.')) || 0;
            const price = parseFloat(priceInput.value.replace(',', '.')) || 0;
            const subtotal = qty * price;

            subCell.textContent = formatRupiah(subtotal);
            row.dataset.subtotal = subtotal.toString();

            recalcGrandTotal();
        }

        function recalcGrandTotal() {
            let total = 0;
            const rows = itemsBody.querySelectorAll('tr');
            rows.forEach(tr => {
                total += parseFloat(tr.dataset.subtotal || '0');
            });
            lastTotal = total;
            grandTotalEl.textContent = formatRupiah(total);
            updatePaymentInfo();
        }

        function updatePaymentInfo() {
            if (!paymentMethodEl || !amountPaidEl || !changeDisplayEl) return;
            const method = paymentMethodEl.value || 'cash';
            if (method === 'qris') {
                amountPaidEl.value = String(Math.round(lastTotal));
                amountPaidEl.readOnly = true;
            } else {
                amountPaidEl.readOnly = false;
                if (amountPaidEl.value === '') {
                    amountPaidEl.value = String(Math.round(lastTotal));
                }
            }

            const paid = parseFloat(String(amountPaidEl.value).replace(',', '.')) || 0;
            const change = Math.max(0, paid - lastTotal);
            changeDisplayEl.value = formatRupiah(change);
        }

        function createRow(defaultMenuId = '', defaultQty = 1, defaultPrice = '') {
            const tr = document.createElement('tr');
            tr.dataset.subtotal = '0';

            const selectedMenu = menus.find(m => m.id === defaultMenuId) || null;

            const priceVal = (defaultPrice !== '' ? defaultPrice :
                (selectedMenu ? selectedMenu.price : 0));

            tr.innerHTML = `
                <td>
                    <select name="items[${rowIndex}][menu_id]"
                            class="item-menu tr-control">
                        <option value="">-- pilih menu --</option>
                        ${menus.map(m => `
                            <option value="${m.id}" data-price="${m.price}">
                                ${m.name}
                            </option>
                        `).join('')}
                    </select>
                </td>
                <td class="tr-text-right">
                    <input type="number"
                           step="0.01"
                           min="0"
                           name="items[${rowIndex}][qty]"
                           class="item-qty tr-control"
                           value="${defaultQty}"
                           style="width:80px; text-align:right;">
                </td>
                <td class="tr-text-right">
                    <input type="number"
                           step="1"
                           min="0"
                           name="items[${rowIndex}][price]"
                           class="item-price tr-control"
                           value="${priceVal}"
                           style="width:100px; text-align:right;">
                </td>
                <td class="tr-text-right item-subtotal">
                    Rp 0
                </td>
                <td class="tr-text-center">
                    <button type="button"
                            class="btn-remove-row tr-btn tr-btn-ghost tr-btn-sm">
                        Hapus
                    </button>
                </td>
            `;

            itemsBody.appendChild(tr);
            rowIndex++;

            const menuSelect = tr.querySelector('.item-menu');
            const qtyInput = tr.querySelector('.item-qty');
            const priceInput = tr.querySelector('.item-price');
            const removeBtn = tr.querySelector('.btn-remove-row');

            if (defaultMenuId) {
                menuSelect.value = defaultMenuId;
            }

            menuSelect.addEventListener('change', function() {
                const opt = menuSelect.options[menuSelect.selectedIndex];
                const defPrice = parseFloat(opt.getAttribute('data-price') || '0');
                // Set harga sesuai menu yang dipilih; user tetap bisa override setelahnya.
                if (!Number.isNaN(defPrice)) {
                    priceInput.value = defPrice;
                }
                recalcRow(tr);
            });

            qtyInput.addEventListener('input', function() {
                recalcRow(tr);
            });

            priceInput.addEventListener('input', function() {
                recalcRow(tr);
            });

            removeBtn.addEventListener('click', function() {
                tr.remove();
                recalcGrandTotal();
            });

            recalcRow(tr);

        }

        // Tambah 1 row default saat halaman dibuka
        btnAddRow.addEventListener('click', function() {
            createRow();
        });

        // Auto add 1 row jika belum ada sama sekali
        if (itemsBody.querySelectorAll('tr').length === 0) {
            createRow();
        }

        if (paymentMethodEl) {
            paymentMethodEl.addEventListener('change', updatePaymentInfo);
        }
        if (amountPaidEl) {
            amountPaidEl.addEventListener('input', updatePaymentInfo);
        }
    })();
</script>

<?= $this->endSection() ?>