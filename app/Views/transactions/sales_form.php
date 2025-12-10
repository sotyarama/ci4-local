<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Input Penjualan</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
                Catat transaksi penjualan harian. Beberapa item menu per nota.
            </p>
        </div>
        <a href="<?= site_url('transactions/sales'); ?>"
           style="font-size:11px; padding:6px 10px; border-radius:999px; background:#111827; color:#e5e7eb; text-decoration:none;">
            ← Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:#3f1f1f; border:1px solid #b91c1c; color:#fecaca; font-size:12px;">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <?php $errors = session('errors') ?? []; ?>

    <?php if (! empty($errors)): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:#3f1f1f; border:1px solid #b91c1c; color:#fecaca; font-size:12px;">
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
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-bottom:16px;">
            <div>
                <label style="font-size:11px; color:#9ca3af; display:block; margin-bottom:4px;">
                    Tanggal Transaksi
                </label>
                <input type="date"
                       name="sale_date"
                       required
                       value="<?= old('sale_date', $today ?? date('Y-m-d')); ?>"
                       style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
            </div>

            <div>
                <label style="font-size:11px; color:#9ca3af; display:block; margin-bottom:4px;">
                    No. Invoice (opsional)
                </label>
                <input type="text"
                       name="invoice_no"
                       value="<?= old('invoice_no'); ?>"
                       placeholder="mis: POS-2025-001"
                       style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
            </div>

            <div>
                <label style="font-size:11px; color:#9ca3af; display:block; margin-bottom:4px;">
                    Nama Customer (opsional)
                </label>
                <input type="text"
                       name="customer_name"
                       value="<?= old('customer_name'); ?>"
                       placeholder="boleh dikosongkan"
                       style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label style="font-size:11px; color:#9ca3af; display:block; margin-bottom:4px;">
                Catatan (opsional)
            </label>
            <textarea name="notes"
                      rows="2"
                      style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb; resize:vertical;"><?= old('notes'); ?></textarea>
        </div>

        <!-- DETAIL ITEMS -->
        <div style="margin-bottom:8px; display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:12px; color:#9ca3af;">
                Detail item penjualan
            </div>
            <button type="button"
                    id="btn-add-row"
                    style="font-size:12px; padding:4px 10px; border-radius:999px; border:none; background:#22c55e; color:#022c22; cursor:pointer;">
                + Tambah Item
            </button>
        </div>

        <div style="overflow-x:auto; margin-bottom:10px;">
            <table style="width:100%; border-collapse:collapse; font-size:12px;" id="items-table">
                <thead>
                <tr>
                    <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:left; min-width:160px;">Menu</th>
                    <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right; min-width:60px;">Qty</th>
                    <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right; min-width:90px;">Harga</th>
                    <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right; min-width:90px;">Subtotal</th>
                    <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:center; width:60px;">Aksi</th>
                </tr>
                </thead>
                <tbody id="items-body">
                <!-- akan diisi via JS (1 row default) -->
                </tbody>
            </table>
        </div>

        <div style="display:flex; justify-content:flex-end; margin-top:8px; margin-bottom:16px;">
            <div style="text-align:right;">
                <div style="font-size:12px; color:#9ca3af;">Total</div>
                <div id="grand-total-display" style="font-size:16px; font-weight:600; color:#e5e7eb;">
                    Rp 0
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px;">
            <button type="submit"
                    style="font-size:13px; padding:8px 16px; border-radius:999px; border:none; background:#22c55e; color:#022c22; cursor:pointer;">
                Simpan Penjualan
            </button>
        </div>

    </form>

</div>

<script>
    (function() {
        let rowIndex = 0;

        const menus = <?= json_encode(array_map(function($m) {
            return [
                'id'    => $m['id'],
                'name'  => $m['name'],
                'price' => (float) ($m['price'] ?? 0),
            ];
        }, $menus ?? [])); ?>;

        const itemsBody   = document.getElementById('items-body');
        const btnAddRow   = document.getElementById('btn-add-row');
        const grandTotalEl = document.getElementById('grand-total-display');

        function formatRupiah(num) {
            num = Number(num) || 0;
            return 'Rp ' + num.toLocaleString('id-ID', {maximumFractionDigits: 0});
        }

        function recalcRow(row) {
            const qtyInput   = row.querySelector('.item-qty');
            const priceInput = row.querySelector('.item-price');
            const subCell    = row.querySelector('.item-subtotal');

            const qty   = parseFloat(qtyInput.value.replace(',', '.')) || 0;
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
            grandTotalEl.textContent = formatRupiah(total);
        }

        function createRow(defaultMenuId = '', defaultQty = 1, defaultPrice = '') {
            const tr = document.createElement('tr');
            tr.dataset.subtotal = '0';

            const selectedMenu = menus.find(m => m.id === defaultMenuId) || null;

            const priceVal = (defaultPrice !== '' ? defaultPrice :
                             (selectedMenu ? selectedMenu.price : 0));

            tr.innerHTML = `
                <td style="padding:4px 6px; border-bottom:1px solid #1f2937;">
                    <select name="items[${rowIndex}][menu_id]"
                            class="item-menu"
                            style="width:100%; padding:4px 6px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
                        <option value="">-- pilih menu --</option>
                        ${menus.map(m => `
                            <option value="${m.id}" data-price="${m.price}">
                                ${m.name}
                            </option>
                        `).join('')}
                    </select>
                </td>
                <td style="padding:4px 6px; border-bottom:1px solid #1f2937; text-align:right;">
                    <input type="number"
                           step="0.01"
                           min="0"
                           name="items[${rowIndex}][qty]"
                           class="item-qty"
                           value="${defaultQty}"
                           style="width:80px; text-align:right; padding:4px 6px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
                </td>
                <td style="padding:4px 6px; border-bottom:1px solid #1f2937; text-align:right;">
                    <input type="number"
                           step="1"
                           min="0"
                           name="items[${rowIndex}][price]"
                           class="item-price"
                           value="${priceVal}"
                           style="width:100px; text-align:right; padding:4px 6px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
                </td>
                <td style="padding:4px 6px; border-bottom:1px solid #1f2937; text-align:right;"
                    class="item-subtotal">
                    Rp 0
                </td>
                <td style="padding:4px 6px; border-bottom:1px solid #1f2937; text-align:center;">
                    <button type="button"
                            class="btn-remove-row"
                            style="font-size:11px; padding:2px 8px; border-radius:999px; border:1px solid #4b5563; background:#111827; color:#e5e7eb; cursor:pointer;">
                        Hapus
                    </button>
                </td>
            `;

            itemsBody.appendChild(tr);
            rowIndex++;

            const menuSelect = tr.querySelector('.item-menu');
            const qtyInput   = tr.querySelector('.item-qty');
            const priceInput = tr.querySelector('.item-price');
            const removeBtn  = tr.querySelector('.btn-remove-row');

            if (defaultMenuId) {
                menuSelect.value = defaultMenuId;
            }

            menuSelect.addEventListener('change', function() {
                const opt = menuSelect.options[menuSelect.selectedIndex];
                const defPrice = parseFloat(opt.getAttribute('data-price') || '0');
                // Set harga sesuai menu yang dipilih; user tetap bisa override setelahnya.
                if (! Number.isNaN(defPrice)) {
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
    })();
</script>

<?= $this->endSection() ?>
