# DEV_NOTES.md
POS Cafe System — Development Notes  
Author: GS  
Last updated: 2025-12-09 (Night)

---

# Repository Overview

Project POS lengkap untuk cafe kecil menggunakan **CodeIgniter 4**. Fokus: backend rapi, logika stok & costing akurat; UI modern menyusul di fase berikutnya.

Tujuan utama:
- POS penjualan  
- Manajemen stok (IN/OUT)  
- Perhitungan HPP (recipe-based)  
- Master data menu, bahan baku, supplier  
- Pembelian & average costing  
- Laporan dasar (penjualan harian)  
- Audit log (planned)  
- POS touchscreen UI (planned skin)

---

# Commit / Milestone Summary

## 1) Initial Setup (579c5ce — 2025-12-06)
- Fresh CI4 project, struktur dasar & konfigurasi awal

## 2) POS App Core (3b3a527 — 2025-12-06)
- Auth (login + filter), dashboard layout
- CRUD Products (menu)
- Migrations: roles, users, menu_categories, menus, raw_materials, units
- Seeds: roles, users, menu_categories, menus, units

## 3) Purchasing System (32e2a4c — 2025-12-07)
- CRUD Raw Materials & Suppliers
- Purchases module (header + items)
- Stock IN: update `raw_materials.current_stock`, `cost_last`, `cost_avg` (average costing)
- Stock movements: insert IN untuk pembelian

## 4) Recipes + HPP Module (2025-12-08 Afternoon)
- CRUD Recipes + Recipe Items
- HPP per menu: pakai `raw_materials.cost_avg`, tampil per yield
- Recipe-based cost engine siap dipakai Sales

## 5) Sales Backend Logic v1 (2025-12-08 Night)
**Core POS logic fungsional**
- Migrations: `sales`, `sale_items` (hpp_snapshot)
- Controller `Transactions\Sales`: simpan header/detail, subtotal, total_amount, total_cost, HPP snapshot, stock OUT berdasar resep (scaling + waste_pct), validasi stok, rollback jika kurang.
- Sales detail view: margin per item & ringkasan (revenue, HPP, margin nominal & %).
- End-to-end: Menu → Recipe → Purchase → Sales → HPP → Margin → Stock OUT.

## 6) Stock Movement List + Daily Sales Report (2025-12-08 Late Night)
- Inventory\StockMovements: riwayat IN/OUT + filter bahan, date range, ref_type/id.
- Reports\SalesSummary::daily: ringkasan per hari + grand total.
- Sidebar update (master, transaksi, inventory, laporan).

## 7) Stabilization & Fixes (2025-12-09 Morning)
- Sales: blok transaksi jika menu tanpa resep/detail (tidak ada fallback HPP 0); error DB dialihkan ke flash+log (tidak dd).
- Stock movements: model tidak menulis `updated_at`; `created_at` diisi manual sesuai kolom.
- Sidebar: link pembelian → `/purchases`.

## 8) Summaries, Stock Card & Demo Seed (2025-12-09 Afternoon)
- Sales Index: summary harian (total penjualan, margin nominal & %) di header.
- Inventory: Kartu Stok per bahan dengan filter tanggal, opening balance, saldo berjalan, delta vs current_stock; link di sidebar.
- Seeders baru & guard anti-duplikat:
  - SuppliersSeeder, RawMaterialsSeeder, PurchasesDemoSeeder (2 PO + movement IN + update stock/cost), RecipesDemoSeeder (resep untuk menu SKU), UsersSeeder (owner) tetap.
  - DatabaseSeeder memanggil seluruh seeders; aman rerun karena guard.

---

# Current Modules Status

| Module               | Status              | Notes                                                          |
|----------------------|---------------------|----------------------------------------------------------------|
| Auth / Login         | Complete            | Stable                                                         |
| Dashboard            | Complete            | Base layout + summary placeholder                              |
| Master Products      | Complete            | CRUD menu                                                      |
| Master Categories    | Complete            | Dipakai oleh menu                                              |
| Master Units         | Complete            |                                                                |
| Raw Materials        | Complete            | CRUD + costing (stock + cost_last + cost_avg)                  |
| Suppliers            | Complete            |                                                                |
| Purchases            | Complete (v1)       | Stock IN + average costing + movement IN                       |
| Recipes              | Phase 1             | CRUD + HPP computation                                         |
| Sales                | Backend v1 Complete | HPP snapshot, total_cost, margin, stock OUT, validation; blok menu tanpa resep/detail |
| Stock Movements      | List Implemented    | IN/OUT list + filter                                           |
| Stock Card           | Implemented         | Per bahan, filter tanggal + opening balance, saldo berjalan    |
| Reports - Sales Daily| Implemented (v1)    | Ringkasan penjualan harian + grand total                       |
| Overhead             | Basic               | Tabel overhead + kategori master; input & list dengan filter tanggal |
| Audit Logs           | Not started         | Planned                                                        |
| POS UI Skin          | Planned (Phase 2)   | Touchscreen-friendly, setelah backend stabil                   |

---

# Role & Access Control (Target)

- Owner (Admin): akses penuh, boleh edit harga menu, resep, user & settings, lihat semua laporan termasuk P/L.
- Staff: boleh input penjualan, pembelian bahan baku, overhead (kecuali gaji), stock adjustment; boleh lihat master; boleh edit resep (butuh audit log). Tidak boleh edit user, harga menu, setting sistem.
- Auditor: read-only (tanpa ubah data), boleh lihat semua laporan dan audit log.

---

# NEXT TODOs (Short-Term — Backend Fokus)

## 1) Sales & HPP Enhancements
- [x] Blok transaksi jika menu belum punya resep/detail (no fallback HPP 0)
- [x] Summary harian di Sales Index (total penjualan & margin hari ini)
- [x] Validasi tambahan untuk override harga jual per transaksi
- [ ] Dukungan void/retur penjualan (rollback stok & margin) atau catatan eksplisit belum didukung

## 2) Inventory & Stock Monitoring
- [x] Kartu Stok per Bahan: filter tanggal, saldo berjalan, opening balance
- [x] Kolom saldo akhir di list movement (opsional, atau cukup di kartu stok)
- [ ] Guard waste_pct resep (batas wajar, mis: 0-100) dan precision stok (hindari selisih float besar)

## 3) Reports
- [x] Laporan Penjualan per Menu:
  - Periode
  - Qty per menu
  - Omzet per menu
  - HPP total per menu
  - Margin per menu
- [x] Export CSV laporan penjualan (harian & per menu)
- [ ] Pagination/limit sederhana untuk laporan jika data besar

## 4) Overhead
- [ ] Edit/nonaktif kategori overhead + tampilkan kategori nonaktif di filter (overhead lama tetap bisa difilter)

## 5) Access Control
- [ ] Terapkan role Owner/Staff/Auditor pada route/controller: Staff dibatasi dari edit user, harga menu, settings; Auditor read-only (blok POST/PUT/DELETE)
- [ ] Audit log untuk edit resep/menu (harga & bahan) sebagai bagian pengamanan Staff

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
- [ ] CRUD berfungsi end-to-end
- [ ] HPP per menu sesuai ekspektasi (cek manual)
- [ ] Stok: pembelian → bertambah (IN); penjualan → berkurang (OUT)
- [ ] `stock_movements`: IN untuk purchases, OUT untuk sales, qty sesuai perhitungan recipe
- [ ] Laporan harian: `total_amount` dan `total_cost` cocok dengan sum data sales
- [ ] Migration: dari DB kosong → `php spark migrate` sukses tanpa error
- [ ] Seeder demo: `php spark db:seed DatabaseSeeder` terisi tanpa duplikat

---

# Development Rules

- 1 commit = 1 fitur / blok perubahan yang jelas  
- Tidak mengedit migration lama setelah di-push → gunakan migration baru untuk perubahan struktur  
- Backend (logic & data) diprioritaskan sebelum UI/skin  
- Perubahan costing & stock harus traceable (stock_movements, receipts, dsb.)

---

# Tooling & Formatting

- Prettier (`.prettierrc`): indent 4 spasi, single quotes, trailing comma `es5`, `semi: true`, `printWidth: 120`, `endOfLine: lf`.
- VS Code (`.vscode/setting.json`): `tabSize: 4`, `insertSpaces: true`, `detectIndentation: false`, format-on-save aktif + trim trailing whitespace + final newline; default formatter Prettier, PHP pakai Intelephense, JS/TS/JSON/HTML pakai Prettier.

---

# Notes

- Alur utama (Purchase → Stock IN → Recipe → Sales → Stock OUT → Laporan Harian) sudah berfungsi.  
- Observability diperkuat: kartu stok per bahan, summary harian di Sales.  
- Seed demo siap dipakai untuk testing cepat (supplier, bahan, pembelian IN, resep, user owner).  
- Dokumen ini untuk menjaga konteks jika ada jeda/pindah device.
