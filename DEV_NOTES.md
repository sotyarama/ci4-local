# DEV_NOTES.md
POS Cafe System — Development Notes  
Author: GS  
Last updated: 2025-12-09 (Morning)

---

# Repository Overview

Project ini adalah sistem POS lengkap untuk cafe kecil menggunakan **CodeIgniter 4**.  
Fokus utama: backend rapi, logika stok & costing akurat; UI modern direncanakan di fase berikutnya.

Tujuan utama:

- POS penjualan  
- Manajemen stok (IN/OUT)  
- Perhitungan HPP (recipe-based)  
- Master data menu, bahan baku, supplier  
- Pembelian & average costing  
- Laporan keuangan dasar (minimal: penjualan harian)  
- Audit log (planned)  
- POS touchscreen UI (planned skin)

---

# Commit / Milestone Summary

## 1) Initial Setup (579c5ce — 2025-12-06)
- Fresh CI4 project, struktur dasar & konfigurasi awal

## 2) POS App Core (3b3a527 — 2025-12-06)
- Auth system (login + filter)
- Dashboard layout (layout/main.php)
- CRUD Products (menu)
- Migrations: roles, users, menu_categories, menus, raw_materials, units
- Seeds: roles, users, menu_categories, menus, units

## 3) Purchasing System (32e2a4c — 2025-12-07)
- CRUD Raw Materials & Suppliers
- Purchases module: header + items, basic forms & list
- Stock IN logic: update `raw_materials.current_stock`, `cost_last`, `cost_avg` (average costing)
- Stock movements: insert IN movement untuk pembelian

## 4) Recipes + HPP Module (2025-12-08 Afternoon)
- CRUD Recipes (header: menu_id, yield_qty, yield_unit, notes)
- CRUD Recipe Items (detail: raw_material_id, qty, waste_pct, note)
- HPP per menu:
  - total cost bahan (pakai `raw_materials.cost_avg`)
  - HPP per yield (per porsi / cup)
- HPP ditampilkan di list resep
- Recipe-based cost engine siap dipakai Sales

## 5) Sales Backend Logic v1 (2025-12-08 Night)
**Major milestone — core POS logic functional**

- Migrations: `sales` (header), `sale_items` (detail, include `hpp_snapshot`)
- Models: `SaleModel`, `SaleItemModel`
- Controller `Transactions\Sales`:
  - Create & store transaksi penjualan
  - Hitung subtotal per item & total transaksi
  - Ambil HPP per menu dari `RecipeModel::calculateHppForMenu`
  - Simpan `hpp_snapshot` per item
  - Hitung `total_cost` transaksi (sum HPP semua item)
  - Simpan `total_amount` dan `total_cost` ke tabel `sales`
  - Stock OUT berdasarkan resep:
    - scaling by `qty / yield_qty`
    - perhitungkan `waste_pct`
    - update `raw_materials.current_stock`
    - insert `stock_movements` (movement_type = OUT, ref_type = sale, ref_id = sale_id)
  - Validasi stok:
    - jika stok tidak cukup, transaksi dibatalkan (rollback) & pesan error
- Sales detail view:
  - Per item: qty, price, subtotal, HPP per porsi (snapshot), total HPP item, margin per item
  - Ringkasan transaksi: total revenue, total HPP, gross margin (nominal & %), highlight margin minus

Sales module now works end-to-end: Menu → Recipe → Purchase → Sales → HPP → Margin → Stock OUT.

## 6) Stock Movement List + Daily Sales Report (2025-12-08 Late Night)
**Fokus: monitoring & reporting backend**

- `StockMovementModel`: helper `withMaterial()` join ke `raw_materials` & `units`
- Inventory: Riwayat Stok
  - Controller: `Inventory\StockMovements`
  - View: `inventory/stock_movements_index.php`
  - Route: `inventory/stock-movements`
  - Fitur: tabel IN/OUT per bahan, filter bahan & date range, ref_type + ref_id, badge IN/OUT
- Reports: Laporan Penjualan Harian
  - Controller: `Reports\SalesSummary::daily`
  - View: `reports/sales_daily.php`
  - Route: `reports/sales/daily`
  - Fitur: group by sale_date; total_sales, total_cost, margin nominal & %; grand total; filter date range
- Layout / Sidebar (layout/main.php)
  - Link Master: `master/products`, `master/categories`, `master/raw-materials`, `master/suppliers`, `master/recipes`
  - Transaksi: `transactions/sales`, pembelian diarahkan ke `/purchases`
  - Inventory: `inventory/stock-movements`
  - Laporan: `reports/sales/daily`

## 7) Stabilization & Fixes (2025-12-09 Morning)
- Sales: transaksi dibatalkan jika menu belum punya resep/detail (tidak ada fallback HPP 0); error DB dialihkan ke flash + logging (tidak `dd`).
- Stock movements: model tidak menulis `updated_at`; `created_at` diisi manual oleh Sales/Purchases sesuai kolom yang tersedia.
- Layout: link Pembelian diperbaiki ke route yang benar (`/purchases`).

---

# Current Modules Status

| Module               | Status                | Notes                                                    |
|----------------------|-----------------------|----------------------------------------------------------|
| Auth / Login         | Complete              | Stable                                                   |
| Dashboard            | Complete              | Base layout + summary placeholder                        |
| Master Products      | Complete              | CRUD menu                                                |
| Master Categories    | Complete              | Dipakai oleh menu                                        |
| Master Units         | Complete              |                                                          |
| Raw Materials        | Complete              | CRUD + costing (stock + cost_last + cost_avg)            |
| Suppliers            | Complete              |                                                          |
| Purchases            | Complete (v1)         | Stock IN + average costing + movement IN                 |
| Recipes              | Phase 1               | CRUD + HPP computation                                   |
| Sales                | Backend v1 Complete   | HPP snapshot, total_cost, margin, stock OUT, validation; blok menu tanpa resep/detail |
| Stock Movements      | List Implemented      | IN/OUT list + filter; belum ada detail per material      |
| Reports - Sales Daily| Implemented (v1)      | Ringkasan penjualan harian + grand total                 |
| Overhead             | Not started           | Planned                                                  |
| Audit Logs           | Not started           | Planned                                                  |
| POS UI Skin          | Planned (Phase 2)     | Touchscreen-friendly, setelah backend stabil             |

---

# NEXT TODOs (Short-Term — Backend Fokus)

## 1) Sales & HPP Enhancements
- [x] Blok transaksi jika menu belum punya resep/detail; tidak ada fallback HPP 0
- [ ] Validasi tambahan untuk override harga jual per transaksi
- [ ] Summary kecil di Sales Index: total penjualan & margin hari ini

## 2) Inventory & Stock Monitoring
- [ ] Halaman "Kartu Stok per Bahan": pilih bahan → kronologi IN/OUT + saldo berjalan
- [ ] Kolom saldo akhir di list movement (opsional, atau hanya di kartu stok)

## 3) Reports
- [ ] Laporan Penjualan per Menu:
  - Periode tertentu
  - Qty per menu
  - Omzet per menu
  - HPP total per menu
  - Margin per menu

---

# NEXT TODOs (Medium-Term)

## Recipes & Food Cost
- [ ] Support sub-recipe (mis: syrup/base dipakai menu lain)
- [ ] Indikator bahan hampir habis (min_stock)
- [ ] Warning di dashboard untuk bahan di bawah min_stock

## POS UI (Phase 2 — Modern Touchscreen Skin)
*(Dikerjakan setelah backend final & stabil)*

- [ ] Grid menu item (card besar)
- [ ] Quick add qty (tap berkali-kali → qty naik)
- [ ] Shortcut pembayaran dan pembulatan kembalian
- [ ] Simpan draft order / meja / hold

---

# NEXT TODOs (Long-Term)

## Reports
- [ ] Laporan penjualan bulanan + grafik sederhana
- [ ] Laporan margin per kategori menu
- [ ] Laporan pembelian per supplier
- [ ] Laporan stok & selisih (banding stok fisik)

## Audit Logs
- [ ] Log perubahan: harga menu, resep, stok adjustment manual
- [ ] Viewer audit log (filter by user, tanggal, entitas)

---

# Testing Guideline

Setiap fitur/modul baru minimal cek:

- [ ] Validasi form jalan (required, numeric, dsb.)
- [ ] CRUD berfungsi dari awal sampai akhir
- [ ] HPP per menu sesuai ekspektasi (cek manual)
- [ ] Stok: pembelian → bertambah (IN); penjualan → berkurang (OUT)
- [ ] `stock_movements`: IN untuk purchases, OUT untuk sales, qty sesuai perhitungan recipe
- [ ] Laporan harian: `total_amount` dan `total_cost` cocok dengan sum data sales
- [ ] Migration: dari DB kosong → `php spark migrate` sukses tanpa error

---

# Development Rules

- 1 commit = 1 fitur / blok perubahan yang jelas  
- Tidak mengedit migration lama setelah di-push → gunakan migration baru untuk perubahan struktur  
- Backend (logic & data) diprioritaskan sebelum UI/skin  
- Setiap perubahan costing & stock harus traceable (stock_movements, receipts, dsb.)

---

# Notes

- Backend untuk alur utama (Purchase → Stock IN → Recipe → Sales → Stock OUT → Laporan Harian) sudah berfungsi.  
- Fase berikutnya: perkuat observability (kartu stok, laporan per menu) sebelum UI/UX besar.  
- Dokumen ini untuk menjaga konteks development, terutama jika ada jeda 1–2 hari atau pindah device.
