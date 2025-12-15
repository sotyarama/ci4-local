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

<script>
    (function() {
        const cart = new Map();
        const cartList = document.getElementById('cart-list');
        const cartEmpty = document.getElementById('cart-empty');
        const totalItemsEl = document.getElementById('total-items');
        const totalAmountEl = document.getElementById('total-amount');
        const form = document.getElementById('pos-form');

        function renderCart() {
            cartList.innerHTML = '';
            if (cart.size === 0) {
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
                totalAmount += item.qty * item.price;

                const row = document.createElement('div');
                row.style.display = 'grid';
                row.style.gridTemplateColumns = '1fr auto';
                row.style.alignItems = 'center';
                row.style.padding = '6px 4px';
                row.style.borderBottom = '1px solid var(--tr-border)';

                const left = document.createElement('div');
                left.innerHTML = '<div style="font-weight:700;">' + item.name + '</div>' +
                    '<div style="font-size:11px; color:var(--tr-muted-text);">Rp ' + numberFormat(item.price) + '</div>';

                const right = document.createElement('div');
                right.style.display = 'flex';
                right.style.alignItems = 'center';
                right.style.gap = '6px';

                const minus = document.createElement('button');
                minus.type = 'button';
                minus.textContent = '-';
                minus.style.cssText = 'width:26px; height:26px; border-radius:6px; border:1px solid var(--tr-border); background:#fff; cursor:pointer;';
                minus.onclick = () => changeQty(item.id, -1);

                const qty = document.createElement('div');
                qty.textContent = item.qty;
                qty.style.minWidth = '24px';
                qty.style.textAlign = 'center';
                qty.style.fontWeight = '700';

                const plus = document.createElement('button');
                plus.type = 'button';
                plus.textContent = '+';
                plus.style.cssText = 'width:26px; height:26px; border-radius:6px; border:1px solid var(--tr-border); background:#fff; cursor:pointer;';
                plus.onclick = () => changeQty(item.id, 1);

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.textContent = '×';
                remove.style.cssText = 'width:26px; height:26px; border-radius:6px; border:1px solid var(--tr-border); background:#fff; cursor:pointer; color:var(--tr-accent-brown);';
                remove.onclick = () => removeItem(item.id);

                right.append(minus, qty, plus, remove);
                row.append(left, right);
                cartList.appendChild(row);
            });

            totalItemsEl.textContent = totalItems.toString();
            totalAmountEl.textContent = 'Rp ' + numberFormat(totalAmount);
        }

        function changeQty(id, delta) {
            if (!cart.has(id)) return;
            const item = cart.get(id);
            item.qty += delta;
            if (item.qty <= 0) {
                cart.delete(id);
            } else {
                cart.set(id, item);
            }
            renderCart();
        }

        function removeItem(id) {
            cart.delete(id);
            renderCart();
        }

        function numberFormat(val) {
            return val.toLocaleString('id-ID');
        }

        document.querySelectorAll('.menu-card').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.dataset.id, 10);
                const name = this.dataset.name;
                const price = parseFloat(this.dataset.price);
                if (!cart.has(id)) {
                    cart.set(id, { id, name, price, qty: 0 });
                }
                const item = cart.get(id);
                item.qty += 1;
                cart.set(id, item);
                renderCart();
            });
        });

        document.getElementById('clear-cart').addEventListener('click', function() {
            cart.clear();
            renderCart();
        });

        form.addEventListener('submit', function(e) {
            if (cart.size === 0) {
                e.preventDefault();
                alert('Keranjang masih kosong.');
                return;
            }
            // buang hidden items lama
            form.querySelectorAll('.cart-hidden').forEach(el => el.remove());
            let idx = 0;
            cart.forEach(item => {
                const base = 'items[' + idx + ']';
                addHidden(base + '[menu_id]', item.id);
                addHidden(base + '[qty]', item.qty);
                addHidden(base + '[price]', item.price);
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

        renderCart();
    })();
</script>

<?= $this->endSection() ?>
