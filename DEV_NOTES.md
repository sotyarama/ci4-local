# 📘 DEV_NOTES.md  
POS Café System — Development Notes  
Author: GS  
Last updated: 2025-12-08 (Night Session)

---

# 📌 Repository Overview

Project ini adalah sistem POS lengkap untuk café kecil menggunakan CodeIgniter 4.  
Tujuan utama:

- POS penjualan  
- Manajemen stok (IN/OUT)  
- Perhitungan HPP (recipe-based)  
- Master data menu, bahan baku, supplier  
- Pembelian & average costing  
- Laporan keuangan dasar (future)  
- Audit log (future)  
- POS touchscreen UI (future skin)

---

# 🧱 Commit History Summary

## 1) Initial Setup (579c5ce — 2025-12-06)
- Fresh CI4 project
- Struktur dasar & konfigurasi

## 2) POS App Core (3b3a527 — 2025-12-06)
- Auth system (login + filter)
- Dashboard layout
- CRUD Products
- Migrations for:
  - roles, users
  - menu categories, menus
  - raw materials, units
- Seeds for initial data

## 3) Purchasing System (32e2a4c — 2025-12-07)
- CRUD Raw Materials & Suppliers
- Purchases module
- Stock IN movement
- Average cost auto-update (cost_last & cost_avg)
- Basic invoice/notes support

## 4) Recipes + HPP Module (2025-12-08 Afternoon)
- CRUD Recipes (header)
- CRUD Recipe Items (detail)
- HPP calculation per menu  
- Display HPP per menu in recipe list
- Recipe-based cost engine prepared

## 5) Sales Backend Logic v1 (2025-12-08 Night)
**Major milestone — core POS logic functional**

- `sales` + `sale_items` migrations created via CLI  
- Base models created (`SaleModel`, `SaleItemModel`)
- SalesController:
  - Create & store transactions
  - Subtotal calculation
  - Insert sale_items
  - Calculate **HPP snapshot** per item (based on recipe)
  - Sum **total_cost** for transaction
  - Save revenue + cost into sales table
  - Recipe-based **stock OUT** deduction
  - Generate stock_movements:
    - `movement_type = OUT`
    - `ref_type = sale`
    - `ref_id = sale_id`

- Sales detail page completed:
  - Tampilkan per-item:
    - qty  
    - price  
    - subtotal  
    - hpp per porsi  
    - total hpp  
    - margin per item  
  - Ringkasan transaksi:
    - total revenue  
    - total HPP  
    - gross margin (nominal)  
    - gross margin (%)  

Sales module **now works end-to-end**:
Menu → Recipe → Purchase → Sale → HPP → Margin → Stok OUT.

---

# 🧩 Current Modules Status

| Module               | Status        | Notes                                           |
|----------------------|--------------|--------------------------------------------------|
| Auth / Login         | ✔️ Complete   | Stable                                           |
| Dashboard            | ✔️ Complete   | Base layout                                      |
| Master Products      | ✔️ Complete   | CRUD                                             |
| Master Units         | ✔️ Complete   |                                                  |
| Raw Materials        | ✔️ Complete   | CRUD + costing                                   |
| Suppliers            | ✔️ Complete   |                                                  |
| Purchases            | ✔️ Mostly     | Stock IN + costing OK                            |
| Recipes              | ✔️ Phase 1    | CRUD + HPP computation                           |
| Sales                | 🟢 **Backend v1 Complete** | HPP + stock OUT + detail page             |
| Sales UI Skin        | ⏳ Planned    | New UI coming after backend stabilizes           |
| Stock Movements      | ✔️ Partial    | IN + OUT working, audit view not yet built       |
| Reports              | ⛔ Not started| Planned                                          |
| Audit Logs           | ⛔ Not started| Planned                                          |

---

# 🚀 NEXT TODOs (Short-Term — Priority)

## 🔥 Sales Backend Logic v2
- [ ] Validasi stok tidak cukup saat penjualan
- [ ] Menampilkan warning jika menu tanpa resep / bahan tidak lengkap
- [ ] Tambah field `unit_cost_snapshot` (opsional)
- [ ] Tambah tampilan daftar stock movements per item

## 📊 Sales Detail (Enhancement)
- [ ] Warna margin merah untuk minus
- [ ] Breakdown bahan baku yang dipakai per item (optional)
- [ ] Export PDF (optional, future)

## 📦 Stok & Movement
- [ ] Buat halaman "Riwayat Stok" (IN/OUT)
- [ ] Filter per bahan baku

---

# 🍳 NEXT TODOs (Medium-Term)

## Recipes & HPP
- [ ] Checklist bahan habis
- [ ] Sub-recipe support
- [ ] Waste factor ringan

## POS UI (Phase 2 — Modern Touchscreen Skin)
*(Dikerjakan setelah backend final)*  
- [ ] Grid menu item  
- [ ] Quick order buttons  
- [ ] Auto-calc pembulatan kembalian  
- [ ] Shortcut function keys  
- [ ] Simpan draft order / hold  

---

# 🛒 NEXT TODOs (Long-Term)

## Reports
- [ ] Laporan penjualan harian/bulanan
- [ ] Laporan margin per menu
- [ ] Laporan pembelian
- [ ] Laporan stok & selisih

## Audit Logs
- [ ] Log perubahan resep
- [ ] Log perubahan harga menu
- [ ] Log stok adjustment manual

---

# 🧪 Testing Guideline

Setiap fitur harus lulus tes berikut:

- [ ] Validasi form
- [ ] CRUD lengkap
- [ ] Hitungan HPP akurat
- [ ] Stok konsisten (raw_materials.current_stock)
- [ ] stock_movements IN/OUT sesuai
- [ ] Sale → Recipe → Stock OUT tidak error
- [ ] Migrasi bisa fresh install tanpa error

---

# 🌱 Development Rules

- Satu commit = satu fitur kecil  
- Tidak edit migration lama setelah dipush  
- UI boleh sederhana → backend harus solid  
- Skin POS modern dilakukan setelah backend stabil  
- Semua logika costing harus audit-friendly

---

# 📝 Notes

Sales backend logic sudah lengkap versi 1 — milestone besar.  
Step berikutnya: validasi stok, laporan, dan UI POS modern.

Dokumen ini menjaga kesinambungan meski ada jeda development 1–2 hari.

