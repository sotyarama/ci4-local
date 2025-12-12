# DEV_NOTES.md
POS Cafe System — Development Notes  
Author: GS  
Last updated: 2025-12-10 (Morning)

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

## 9) Stabilization Precision + Guard (2025-12-09 Late Night)
- Recipes: validasi waste_pct dibatasi 0-100, rounding 3 desimal saat simpan.
- HPP: clamp waste_pct dan rounding qty/cost (6 desimal) untuk kurangi noise float; total_cost & hpp_per_yield ikut di-round.
- Sales: kebutuhan bahan & pengurangan stok di-round 6 desimal, guard stok pakai nilai yang sudah dinormalisasi, pesan shortage lebih jelas.
- Stock Movements/Card: saldo berjalan & qty di-round per langkah untuk menghindari drift.

## 10) Brand Guide Presentation (2025-12-10 Morning)
- Route `/brand-guide` menuju controller `BrandGuide` yang merender deck Reveal.js.
- View `brand_presentation.php`: 14 slide brand/UI guideline (cover, essence, story, color palette, typography, logo meaning/usage, pattern, photography mood, POS cashier/dashboard, menu layout, tone of voice, closing).
- Theme `public/css/temurasa-reveal.css`: palet Temu Rasa, typography Nunito/Inter/Poppins, carded slide layout, color grid, chips/divider, responsive tweak untuk mobile.

## Uncommitted (2025-12-10) - Temu Rasa UI Refresh
- Brand theme file `public/css/theme-temurasa.css` dengan CSS variables + komponen dasar (card, input, button, table, scrollbar) sesuai palet Temu Rasa.
- Layout utama & login: background softCream, topbar primary, surface beige/green, teks charcoal, scrollbar mengikuti tema.
- View POS (master, transaksi, inventory, reports, overhead) dipetakan ke palette: tombol aksi kini solid (primary/accents), alert/badge/header tabel pakai secondary green/beige.
- Margin/margin% di laporan & sales detail pakai `--tr-primary-deep` untuk kontras; void notice diperjelas.
- Tabel/aksi dirapikan: padding header/body diperbesar, tombol “Detail” di pembelian diperkecil & center untuk proporsi lebih rapi.
- Laporan baru: Pembelian per Supplier (`reports/purchases/supplier`) dengan filter tanggal & supplier, ringkasan jumlah PO + total pembelian per pemasok + grand total, link di sidebar Reports.
- Laporan baru: Penjualan per Kategori (`reports/sales/category`) dengan filter tanggal + pagination + export CSV; sidebar Reports menaut ke laporan ini (menggantikan placeholder “Margin per Kategori”).

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

# Role & Access Control (Target & Current State)

Legenda: A=Access (view/read), I=Input/Create, U=Update, D=Delete/Nonaktifkan.

| Modul/Fitur           | Owner         | Staff (target)                          | Staff (current)                                      | Auditor (target)   | Auditor (current)            |
|-----------------------|---------------|-----------------------------------------|-------------------------------------------------------|--------------------|------------------------------|
| Dashboard             | A             | A                                       | A                                                     | A                  | A                            |
| Master Data (produk, kategori, bahan, supplier) | A/I/U/D (termasuk harga menu) | A/I/U menu & resep; D dibatasi                       | A/I/U menu & resep (blok hanya di users/settings)      | A                  | A (filter blok I/U/D)        |
| Users & Settings      | A/I/U/D       | Tidak boleh                             | A; filter blok I/U/D                                  | A (view)           | A (filter blok I/U/D)        |
| Resep                 | A/I/U/D       | A/I/U (butuh audit log)                 | A/I/U (filter tidak blok; audit log belum)           | A                  | A (filter blok I/U/D)        |
| Penjualan             | A/I/U/D       | A/I (void belum)                        | A/I (filter izinkan)                                  | A (lihat)          | A (filter blok I/U/D)        |
| Pembelian             | A/I/U/D       | A/I                                     | A/I (filter izinkan)                                  | A (lihat)          | A (filter blok I/U/D)        |
| Stock Adjustment      | A/I/U/D       | A/I                                     | A/I (belum ada modul, asumsi)                         | A (lihat)          | A (filter blok I/U/D)        |
| Overhead              | A/I/U/D       | A/I                                     | A/I (filter izinkan)                                  | A (lihat)          | A (filter blok I/U/D)        |
| Laporan (Sales, dll.) | A             | A                                       | A                                                     | A                  | A                            |
| Audit Log             | A             | A (view)                                | (belum ada modul)                                     | A (view)           | (belum ada modul)            |

Catatan gap:
- Staff masih bisa membuka form edit master/harga karena GET tidak diblok; filter hanya blok submit POST/PUT/DELETE di products/users/settings. ✅ di-TO-DO: "Staff blok GET form master sensitif".
- Audit log resep/menu belum ada. ✅ di-TO-DO: "Audit log edit resep/menu".
- Void sudah tersedia; retur penjualan belum ada. (TO-DO: "Retur penjualan").

---

# NEXT TODOs (Short-Term — Backend Fokus)

## 1) Sales & HPP Enhancements
- [x] Blok transaksi jika menu belum punya resep/detail (no fallback HPP 0)
- [x] Summary harian di Sales Index (total penjualan & margin hari ini)
- [x] Validasi tambahan untuk override harga jual per transaksi
- [x] Void penjualan: status void, rollback stok via movement (retur belum)

## 2) Inventory & Stock Monitoring
- [x] Kartu Stok per Bahan: filter tanggal, saldo berjalan, opening balance
- [x] Kolom saldo akhir di list movement (opsional, atau cukup di kartu stok)
- [x] Guard waste_pct resep (batas 0-100) + rounding qty/cost untuk hindari selisih float besar

## 3) Reports
- [x] Laporan Penjualan per Menu:
  - Periode
  - Qty per menu
  - Omzet per menu
  - HPP total per menu
  - Margin per menu
- [x] Export CSV laporan penjualan (harian & per menu)
- [x] Pagination/limit sederhana untuk laporan jika data besar

## 4) Overhead
- [x] Edit/nonaktif kategori overhead + tampilkan kategori nonaktif di filter (overhead lama tetap bisa difilter)

## 5) Access Control
- [x] Terapkan role Owner/Staff/Auditor pada route/controller: Staff dibatasi dari edit user & settings; Auditor read-only (blok POST/PUT/DELETE)
- [x] Audit log untuk edit resep/menu (harga & bahan) sebagai bagian pengamanan Staff
- [x] Staff: blokir akses GET ke form create/edit user/setting (produk dibiarkan, user/setting dibatasi); sidebar/link nonaktif untuk area sensitif
- [x] Void penjualan tersedia; retur belum (rollback stok/margin via movement)
- [x] Lanjutan UI/UX: scrollbar custom sesuai tema, sidebar collapsible + fixed header/footer, tabel auto-scroll wrapper (done)
  - Catatan progres: Auditor read-only via RoleFilter; Staff diblok untuk GET/POST area users/settings (belum ada modul user/setting, guard berbasis path).

---

# NEXT TODOs (Medium-Term)

## Recipes & Food Cost
- [ ] Support sub-recipe (mis: syrup/base dipakai menu lain)
- [x] Indikator bahan hampir habis (min_stock) di master bahan + kartu stok
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
- [x] Laporan margin per kategori menu
- [x] Laporan pembelian per supplier
- [ ] Laporan stok & selisih (banding stok fisik)

## Audit Logs
- [x] Log perubahan: harga menu, resep (payload JSON)
- [x] Viewer audit log (filter entity, tanggal) sederhana

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
