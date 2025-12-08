# 📘 DEV_NOTES.md  
POS Café System — Development Notes  
Author: GS  
Last updated: 2025-12-08 (Late Night)

---

# 📌 Repository Overview

Project ini adalah sistem POS lengkap untuk café kecil menggunakan **CodeIgniter 4**.  
Fokus utama: backend rapih, logika stok & costing akurat, UI menyusul (skin modern direncanakan di fase berikutnya).

Tujuan utama:

- POS penjualan  
- Manajemen stok (IN/OUT)  
- Perhitungan HPP (recipe-based)  
- Master data menu, bahan baku, supplier  
- Pembelian & average costing  
- Laporan keuangan dasar (minimal: penjualan harian)  
- Audit log (future)  
- POS touchscreen UI (future skin)

---

# 🧱 Commit History Summary

## 1) Initial Setup (579c5ce — 2025-12-06)
- Fresh CI4 project
- Struktur dasar & konfigurasi awal

## 2) POS App Core (3b3a527 — 2025-12-06)
- Auth system (login + filter)
- Dashboard layout (layout/main.php)
- CRUD Products (menu)
- Migrations:
  - roles, users
  - menu_categories, menus
  - raw_materials, units
- Seeds:
  - roles, users, menu_categories, menus, units

## 3) Purchasing System (32e2a4c — 2025-12-07)
- CRUD Raw Materials & Suppliers
- Purchases module:
  - header + items
  - basic forms & list
- Stock IN logic:
  - update `raw_materials.current_stock`
  - update `cost_last` & `cost_avg` (average costing)
- Stock movements:
  - insert IN movement untuk pembelian

## 4) Recipes + HPP Module (2025-12-08 Afternoon)
- CRUD Recipes (header: menu_id, yield_qty, yield_unit, notes)
- CRUD Recipe Items (detail: raw_material_id, qty, waste_pct, note)
- HPP calculation per menu:
  - hitung total cost bahan (pakai `raw_materials.cost_avg`)
  - hitung HPP per yield (per porsi / cup)
- HPP ditampilkan di list resep
- Recipe-based cost engine siap dipakai Sales

## 5) Sales Backend Logic v1 (2025-12-08 Night)
**Major milestone — core POS logic functional**

- Migrations:
  - `sales` (header)
  - `sale_items` (detail, include `hpp_snapshot`)
- Models:
  - `SaleModel`
  - `SaleItemModel`
- Sales controller (`Transactions\Sales`):
  - Create & store transaksi penjualan
  - Hitung subtotal per item & total transaksi
  - Ambil HPP per menu dari `RecipeModel::calculateHppForMenu`
  - Simpan `hpp_snapshot` per item
  - Hitung `total_cost` transaksi (sum HPP semua item)
  - Simpan `total_amount` dan `total_cost` ke tabel `sales`
  - Lakukan **stock OUT** berdasarkan resep:
    - scaling by `qty / yield_qty`
    - perhitungkan `waste_pct`
    - update `raw_materials.current_stock`
    - insert `stock_movements` (movement_type = OUT, ref_type = sale, ref_id = sale_id)
  - Validasi stok:
    - jika stok tidak cukup, transaksi dibatalkan (rollback) & tampil pesan error

- Sales detail view:
  - Per item:
    - qty
    - price
    - subtotal
    - HPP per porsi (snapshot)
    - total HPP item
    - margin per item
  - Ringkasan transaksi:
    - total revenue
    - total HPP
    - gross margin (nominal)
    - gross margin (%)
    - highlight margin minus (merah)

Sales module **now works end-to-end**:  
Menu → Recipe → Purchase → Sales → HPP → Margin → Stok OUT.

## 6) Stock Movement List + Daily Sales Report (2025-12-08 Late Night)
**Fokus: monitoring & reporting backend**

- `StockMovementModel`:
  - helper `withMaterial()` join ke `raw_materials` & `units`

- Inventory: Riwayat Stok
  - Controller: `Inventory\StockMovements`
  - View: `inventory/stock_movements_index.php`
  - Route: `inventory/stock-movements`
  - Fitur:
    - Tabel IN/OUT lengkap per bahan
    - Join nama bahan & satuan
    - Filter:
      - per bahan baku (`raw_material_id`)
      - date range (`date_from`, `date_to`)
    - Tampilkan referensi `ref_type` + `ref_id`
    - Label badge hijau (IN) & merah (OUT)

- Reports: Laporan Penjualan Harian
  - Controller: `Reports\SalesSummary::daily`
  - View: `reports/sales_daily.php`
  - Route: `reports/sales/daily`
  - Fitur:
    - Group by `sale_date`
    - Hitung:
      - total_sales (SUM total_amount)
      - total_cost (SUM total_cost)
      - margin per hari (nominal)
      - margin % per hari
    - Grand total di bawah tabel:
      - total_sales
      - total_cost
      - total_margin
      - margin %
    - Filter date range (from/to)

- Layout / Sidebar (layout/main.php)
  - Update link:
    - Master:
      - `master/products`
      - `master/categories`
      - `master/raw-materials`
      - `master/suppliers`
      - `master/recipes`
    - Transaksi:
      - `transactions/purchases`
      - `transactions/sales`
    - Inventory:
      - `inventory/stock-movements`
    - Laporan:
      - `reports/sales/daily`

---

# 🧩 Current Modules Status

| Module               | Status                      | Notes                                                   |
|----------------------|-----------------------------|---------------------------------------------------------|
| Auth / Login         | ✔️ Complete                 | Stable                                                  |
| Dashboard            | ✔️ Complete                 | Base layout + summary placeholder                      |
| Master Products      | ✔️ Complete                 | CRUD menu                                               |
| Master Categories    | ✔️ Complete                 | Dipakai oleh menu                                       |
| Master Units         | ✔️ Complete                 |                                                         |
| Raw Materials        | ✔️ Complete                 | CRUD + costing (stock + cost_last + cost_avg)          |
| Suppliers            | ✔️ Complete                 |                                                         |
| Purchases            | ✔️ Complete (v1)            | Stock IN + average costing + movement IN               |
| Recipes              | ✔️ Phase 1                  | CRUD + HPP computation                                 |
| Sales                | 🟢 Backend v1 Complete      | HPP snapshot, total_cost, margin, stock OUT, validation|
| Stock Movements      | ✔️ List Implemented         | IN/OUT list + filter; belum ada detail per material    |
| Reports - Sales Daily| 🟢 Implemented (v1)         | Ringkasan penjualan harian + grand total               |
| Overhead             | ⛔ Not started              | Planned                                                 |
| Audit Logs           | ⛔ Not started              | Planned                                                 |
| POS UI Skin          | ⏳ Planned (Phase 2)        | Touchscreen-friendly, setelah backend stabil           |

---

# 🚀 NEXT TODOs (Short-Term — Backend Fokus)

## 1) Sales & HPP Enhancements
- [ ] Perbaiki messaging jika menu belum punya resep (fallback: dianggap HPP 0, diberi warning)
- [ ] Tambah opsi untuk override harga jual per transaksi (sudah ada, tapi perlu validasi tambahan)
- [ ] Tambah summary kecil di Sales Index:
  - total penjualan hari ini
  - total margin hari ini

## 2) Inventory & Stock Monitoring
- [ ] Tambah halaman “Kartu Stok per Bahan”:
  - Input: pilih 1 bahan → tampil kronologi IN/OUT + saldo berjalan
- [ ] Tambah kolom saldo akhir di list movement (opsional, atau di halaman kartu stok saja)

## 3) Reports
- [ ] Tambah “Laporan Penjualan per Menu”:
  - periode tertentu
  - qty per menu
  - omzet per menu
  - HPP total per menu
  - margin per menu

---

# 🍳 NEXT TODOs (Medium-Term)

## Recipes & Food Cost
- [ ] Support sub-recipe (misalnya syrup / base dibuat dulu, lalu dipakai menu lain)
- [ ] Tambah indikator bahan hampir habis (min_stock)
- [ ] Tambah warning di dashboard untuk bahan di bawah min_stock

## POS UI (Phase 2 — Modern Touchscreen Skin)
*(Dikerjakan setelah backend final & stabil)*

- [ ] Grid menu item (card besar)
- [ ] Quick add qty (tap berkali-kali → qty naik)
- [ ] Shortcut pembayaran dan pembulatan kembalian
- [ ] Simpan draft order / meja / hold

---

# 🛒 NEXT TODOs (Long-Term)

## Reports
- [ ] Laporan penjualan bulanan + grafik sederhana
- [ ] Laporan margin per kategori menu
- [ ] Laporan pembelian per supplier
- [ ] Laporan stok & selisih (dibanding stok fisik)

## Audit Logs
- [ ] Log perubahan:
  - harga menu
  - resep
  - stok adjustment manual
- [ ] Viewer untuk audit log (filter by user, tanggal, entitas)

---

# 🧪 Testing Guideline

Setiap fitur / modul baru minimal cek:

- [ ] Validasi form jalan (required, numeric, dsb.)
- [ ] CRUD berfungsi dari awal sampai akhir
- [ ] HPP per menu sesuai ekspektasi (cek pakai kalkulator manual)
- [ ] Stok:
  - pembelian → bertambah (IN)
  - penjualan → berkurang (OUT)
- [ ] `stock_movements`:
  - IN untuk purchases
  - OUT untuk sales
  - nilai qty sesuai perhitungan recipe
- [ ] Laporan harian:
  - total_amount dan total_cost cocok dengan sum data sales
- [ ] Migration:
  - dari DB kosong → `php spark migrate` sukses tanpa error

---

# 🌱 Development Rules

- 1 commit = 1 fitur / blok perubahan yang jelas  
- Tidak mengedit migration lama setelah di-push → gunakan migration baru untuk perubahan struktur  
- Backend (logic & data) diprioritaskan sebelum UI/skin  
- Setiap perubahan costing & stock harus bisa ditelusuri (traceable lewat stock_movements, receipts, dsb.)

---

# 📝 Notes

- Backend untuk alur utama (Purchase → Stock IN → Recipe → Sales → Stock OUT → Laporan Harian) sudah berfungsi.  
- Fase berikutnya: perkuat observability (kartu stok, laporan per menu) sebelum masuk ke fase UI/UX besar.  
- Dokumen ini dipakai untuk menjaga konteks development, terutama jika ada jeda 1–2 hari atau pindah device.
