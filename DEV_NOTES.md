# 📘 **DEV_NOTES.md**

**POS Café System — Development Notes**
**Author:** GS
**Last updated:** 2025-12-08 (Night Session)

---

# 📌 **Repository Overview**

Project ini adalah sistem POS lengkap untuk café kecil berbasis **CodeIgniter 4**, mencakup:

* POS penjualan
* Manajemen stok IN/OUT
* Perhitungan HPP berbasis resep
* Master data menu, bahan baku, supplier
* Pembelian + average costing
* Modul laporan (planned)
* Audit log (planned)
* Modern POS touchscreen UI (planned skin)

---

# 🧱 **Commit History Summary**

## **1) Initial Setup — 579c5ce (2025-12-06)**

* Fresh CI4 project
* Struktur dasar + konfigurasi environment

## **2) POS App Core — 3b3a527 (2025-12-06)**

* Sistem login & authentication filter
* Dashboard layout
* CRUD Products
* Migrations:

  * roles, users
  * menu_categories, menus
  * raw_materials, units
* Seed data awal

## **3) Purchasing System — 32e2a4c (2025-12-07)**

* CRUD Raw Materials
* CRUD Suppliers
* Module Purchases (pembelian)
* Stock IN movement
* Auto average costing (cost_last & cost_avg)
* Basic invoice & notes

## **4) Recipes + HPP Module — (2025-12-08 Afternoon)**

* CRUD Recipes (header)
* CRUD Recipe Items (detail)
* HPP calculation engine
* Menampilkan HPP per menu pada recipe list
* Menyiapkan fondasi perhitungan costing

## **5) Sales Backend Logic v1 — (2025-12-08 Night)**

**🔥 Milestone besar — core POS logic sudah fungsional end-to-end**

Implementasi utama:

* Migrations untuk `sales` & `sale_items`
* Model lengkap (`SaleModel`, `SaleItemModel`)
* Sales Controller:

  * Input penjualan
  * Insert header + detail sale_items
  * Hitung subtotal
  * Hitung **HPP snapshot** per item
  * Hitung **total_cost** transaksi
  * Hitung margin (revenue – cost)
  * Kurangi stok bahan baku berdasarkan recipe
  * Buat stock_movements OUT
* Sales detail page:

  * qty, price, subtotal
  * HPP per porsi
  * total hpp
  * margin per item
  * Ringkasan transaksi:

    * total revenue
    * total HPP
    * gross margin nominal & %

**Sales module is now fully operational.**
Alur: Menu → Resep → Pembelian → Penjualan → HPP → Margin → Stok OUT

---

# 🧩 **Current Modules Status**

| Module          | Status                     | Notes                     |
| --------------- | -------------------------- | ------------------------- |
| Auth / Login    | ✔️ Complete                | Stable                    |
| Dashboard       | ✔️ Complete                | Base layout               |
| Master Products | ✔️ Complete                | CRUD                      |
| Master Units    | ✔️ Complete                | –                         |
| Raw Materials   | ✔️ Complete                | CRUD + costing            |
| Suppliers       | ✔️ Complete                | –                         |
| Purchases       | ✔️ Mostly Done             | Stock IN + costing stable |
| Recipes         | ✔️ Phase 1 Complete        | CRUD + HPP                |
| **Sales**       | 🟢 **Backend v1 Complete** | Full logic working        |
| Sales UI Skin   | ⏳ Planned                  | Modern touchscreen POS    |
| Stock Movements | ✔️ Partial                 | IN/OUT ready, UI not done |
| Reports         | ⛔ Planned                  | –                         |
| Audit Logs      | ⛔ Planned                  | –                         |

---

# 🚀 **NEXT TODOs (Short-Term — Priority)**

## **🔥 Sales Backend Logic v2**

* [ ] Validasi stok tidak mencukupi saat transaksi
* [ ] Warning: menu tanpa resep atau resep tidak lengkap
* [ ] Tambah field opsional `unit_cost_snapshot`
* [ ] Halaman daftar stock movements per item

## **📊 Sales Detail Enhancements**

* [ ] Margin warna merah jika negative
* [ ] Breakdown bahan baku per item (optional)
* [ ] Export PDF (future)

## **📦 Stok & Movements**

* [ ] Halaman “Riwayat Stok” (list IN/OUT)
* [ ] Filter per bahan baku

---

# 🍳 **NEXT TODOs (Medium-Term)**

## **Recipes & HPP**

* [ ] Checklist bahan habis
* [ ] Sub-recipe / nested recipes
* [ ] Waste factor lanjutan

## **POS UI (Phase 2 — Touchscreen Skin)**

*(Dikerjakan setelah backend stabil)*

* [ ] Grid menu style ShopeeFood/GoFood
* [ ] Quick order buttons
* [ ] Auto-calc kembalian
* [ ] Shortcut function keys
* [ ] Save draft / hold order

---

# 🛒 **NEXT TODOs (Long-Term)**

## **Reports**

* [ ] Penjualan harian/bulanan
* [ ] Margin per menu
* [ ] Pembelian
* [ ] Stok dan selisih

## **Audit Logs**

* [ ] Perubahan resep
* [ ] Perubahan harga menu
* [ ] Stok adjustment manual

---

# 🧪 **Testing Guideline**

Setiap fitur harus memenuhi checklist:

* [ ] Validasi form berjalan
* [ ] CRUD lengkap
* [ ] Perhitungan HPP akurat
* [ ] Stok konsisten
* [ ] stock_movements IN/OUT benar
* [ ] Penjualan → HPP → Stock OUT tidak error
* [ ] Migration bisa fresh install tanpa error

---

# 🌱 **Development Rules**

* 1 commit = 1 fitur kecil
* Migration TIDAK diubah setelah dipush
* Backend harus solid dulu, UI bisa menyusul
* Skin POS modern dibuat setelah backend final
* Semua costing harus audit-friendly

---

# 📝 **Notes**

Sales Backend Logic versi 1 **telah selesai dan stabil**.
Tahap berikutnya: validasi, laporan, dan UI POS touchscreen.

Dokumen ini memastikan kelanjutan development tetap konsisten meskipun ada jeda beberapa hari.
