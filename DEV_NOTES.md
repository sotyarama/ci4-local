# 📘 DEV_NOTES.md  
POS Café System — Development Notes  
Author: GS  
Last updated: 2025-12-08

---

# 📌 Repository Overview

Project ini adalah sistem POS lengkap untuk café kecil, dibangun dengan CodeIgniter 4.  
Fokus sistem:

- POS penjualan  
- Manajemen stok & bahan baku  
- Perhitungan HPP & food cost  
- Pembelian & supplier  
- Overhead operasional  
- Laporan penjualan / stok / profit  
- Audit log (planned)  

---

# 🧱 Commit History Summary

## 1. Initial Setup (579c5ce — 2025-12-06)
- Install CI4 default project
- Setup folder, configs, structure

## 2. Initial POS App (3b3a527 — 2025-12-06)
- Login & Auth Filter
- Dashboard layout
- CRUD Master Products
- Migrations:
  - roles, users, menu categories, menus, units, raw materials

## 3. Suppliers + Purchases Module (32e2a4c — 2025-12-07)
- CRUD Raw Materials (complete)
- CRUD Suppliers (complete)
- Purchases module (initial)
- Migrations for suppliers, purchases, purchase_items
- Stock movement (IN) working
- Average cost & last cost logic working

## 4. Recipes Module + HPP (2025-12-08)
- Migrations:
  - recipes
  - recipe_items
- CRUD Recipes (header)
- CRUD Recipe Items (detail)
- HPP calculation for each recipe
- Display HPP in recipe list
- Prepared backend for future sales→HPP linking

---

# 🧩 Current Modules Status

| Module               | Status        | Notes                                          |
|----------------------|--------------|------------------------------------------------|
| Login / Auth         | ✔️ Complete   | Stable                                         |
| Dashboard            | ✔️ Complete   | Layout OK                                      |
| Master Products      | ✔️ Complete   | CRUD                                           |
| Master Units         | ✔️ Complete   |                                                |
| Master Raw Materials | ✔️ Complete   | CRUD + cost avg/last                           |
| Master Suppliers     | ✔️ Complete   |                                                |
| Purchases            | ✔️ Mostly     | Stock IN + cost logic OK                       |
| Recipes              | ✔️ Phase 1    | CRUD + HPP per menu                            |
| Sales                | 🟡 In Progress| Form complete; backend logic next              |
| Stock Movement       | ✔️ Partial    | IN working; OUT pending                        |
| POS UI Skin Modern   | ⏳ Planned    | Akan dibuat setelah backend sales stabil       |
| Reports              | ⛔ Not started|                                                |
| Audit Logs           | ⛔ Not started|                                                |

---

# 🚀 NEXT TODOs (Short Term)

## 🔥 **Sales Backend (priority next session)**
- [ ] Kurangi stok otomatis berdasarkan resep
- [ ] Catat stock_movements type OUT
- [ ] Validasi stok tidak cukup
- [ ] Hitung HPP per sale item
- [ ] Simpan total_cost ke sales
- [ ] Tampilkan HPP / margin di sales detail

---

# 🍳 NEXT TODOs (Medium-Term)

## Recipes & HPP
- [ ] Checklist bahan habis
- [ ] Pilih sub-recipe (future)
- [ ] Waste factor per menu

## POS UI (Phase 2 — Modern Skin)
- [ ] Mode touchscreen
- [ ] Card-based item UI
- [ ] Quick order buttons
- [ ] Auto-calc change payment
- [ ] Hold bill / resume bill
*(Catatan: skin ini akan dikerjakan setelah logic backend selesai.)*

---

# 🛒 NEXT TODOs (Long-Term)

## POS Sales
- [ ] Penjualan multi-item cepat
- [ ] Draft order / hold
- [ ] Cetak struk sederhana (optional)

## Reports
- [ ] Laporan Penjualan harian/bulanan
- [ ] Laporan Pembelian
- [ ] Laporan Stok
- [ ] Laporan Laba Rugi per menu atau per periode

## Audit Log
- [ ] Log perubahan harga menu
- [ ] Log perubahan resep
- [ ] Log stok adjustment
- [ ] Log user activity

---

# 🧪 Testing Guideline

Setiap modul harus dilakukan test:
- [ ] Validasi input
- [ ] CRUD lengkap
- [ ] Migrasi fresh install bisa jalan
- [ ] Stock IN/OUT konsisten
- [ ] HPP akurat vs data bahan baku
- [ ] Error handling sesuai standar

---

# 🌱 Development Rules

- 1 commit = 1 fitur kecil  
- Jangan edit migration lama → buat migration baru  
- Jangan commit .env  
- Gunakan feature branch jika modul besar  
- UI boleh sederhana dulu → backend harus kuat  
- Skin POS modern akan dilakukan setelah semua logic dasar stabil  

---

# 📝 Notes

Dokumen ini memastikan kesinambungan development saat jeda pekerjaan 1–2 hari.  
Jika ada perubahan arsitektur besar, tambahkan CHANGELOG.

