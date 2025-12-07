# 📘 **DEV_NOTES.md (Updated: 2025-12-07 Evening)**

# POS Café System — Development Notes

*Author: GS*
*Project: CodeIgniter 4 Local Development*
*Last updated: 2025-12-07 23:00*

---

## 📌 **Repository Overview**

Sistem POS Café berbasis **CodeIgniter 4** dengan fokus pada:

* Manajemen produk & resep (BOM)
* Manajemen stok & bahan baku
* Pembelian bahan baku
* HPP otomatis
* POS Sales (future)
* Laporan & audit log (future)

---

# 🧱 **Commit History Summary**

### **1. Initial Setup (579c5ce, 2025-12-06)**

* Install CI4 fresh
* Struktur default
* No POS modules yet

---

### **2. Initial POS App (3b3a527, 2025-12-06)**

**Modules Added**

* Login & Logout
* Auth Filter
* Dashboard layout ready
* CRUD Master Products

**Database**

* Roles, Users
* Menu Categories, Menus
* Units, Raw Materials
  **Seeds**: Roles, Users, Menu Categories, Menus, Units

---

### **3. Master Raw Materials + Suppliers + Purchases (32e2a4c, 2025-12-07)**

**Modules Added**

* Master Raw Materials (full CRUD)
* Master Suppliers (full CRUD)
* Purchases module (header + detail items)

**Database**

* Suppliers
* Purchases
* Purchase Items

**Logic**

* Average cost update
* Current stock update
* Stock movement recorded

---

### **4. Recipes Module + HPP System (WORK IN PROGRESS, not committed yet)**

**Today’s progress (Dec 7 evening):**

* Create Recipe (header + multiple items)
* Edit Recipe (header + items)
* Recipe Model: `calculateHppForMenu()`
* HPP panel added to **Edit Recipe** page
* HPP column added to **List Recipes**
* Error handling on missing cost / missing recipe fixed
* Foreign key disabled temporarily (future improvement noted)

---

# 🧩 **Current Modules Status**

| Module               | Status        | Notes                                  |
| -------------------- | ------------- | -------------------------------------- |
| Login / Auth         | ✔️ Done       | Stable                                 |
| Dashboard            | ✔️ Done       | UI base ready                          |
| Master Products      | ✔️ Done       | CRUD OK                                |
| Master Units         | ✔️ Done       | Used by raw materials                  |
| Master Raw Materials | ✔️ Done       | CRUD + validation                      |
| Master Suppliers     | ✔️ Done       | CRUD                                   |
| Purchases            | ✔️ Done       | Auto stock + avg cost + stock movement |
| Stock Movement       | ✔️ Done       | Auto-insert on purchase                |
| Recipes (CRUD)       | ✔️ Done       | Header + multi-items                   |
| HPP Calculation      | ✔️ Done       | CostAvg, waste%, yield supported       |
| HPP UI (Edit Recipe) | ✔️ Done       | Green info panel                       |
| HPP in Recipe List   | ✔️ Done       | With unit display                      |
| POS Sales            | ⛔ Not started | Next milestone                         |
| Overhead             | ⛔ Not started | Future                                 |
| Reports              | ⛔ Not started | Future                                 |
| Audit Logs           | ⛔ Not started | Future                                 |
| Email Notifications  | ⛔ Optional    | Future                                 |

---

# 🚧 **NEXT TODOs (Short-Term — TOMORROW)**

### ⭐ **POS Sales Module**

This will be the next big module.

To build POS Sales:

1. **UI POS sederhana**

   * Pilih menu
   * Qty
   * Hitung total
   * Save to DB (sales + sale_items)

2. **Stock deduction**

   * Deduct bahan baku berdasarkan recipe items
   * Insert stock_movements: OUT

3. **Food cost**

   * Rekam HPP yang dipakai saat transaksi (snapshot)

4. **Profit summary dasar**

   * Revenue — Cost

---

# 🍳 **NEXT TODOs (Medium-Term)**

### **Stock Adjustment Module**

* Untuk koreksi manual
* Wajib masuk kartu stok

### **Overhead Module**

* Catat biaya listrik, air, gas, internet
* Integrasi ke laporan Profit/Loss

### **Reports (Batch 1)**

* Laporan Pembelian
* Laporan Penjualan
* Laporan Stok (Kartu Stok)
* Laporan HPP Summary per Menu

---

# 🛒 **NEXT TODOs (Long-Term)**

### **Audit Log System**

* Perubahan harga menu
* Perubahan resep
* Manual stock adjustment
* Track user who edited

### **Settings Module**

* Owner email
* Auto email toggle
* Default currency (IDR fixed)

---

# ⚠️ **Technical Debt / Cleanup Needed Later**

### **Database / Migration Cleanup**

* RecipeItems currently without FK → add FK via new migration
* Ingredient waste_pct not yet validated
* HPP rounding rules not standardized yet

### **Performance**

* Recipes::index() melakukan HPP calculation N+1 — acceptable small scale
  → bisa dioptimasi batch query later

### **UI/UX**

* Dynamic row in Recipes form needs polishing
* Consistency spacing/padding across modules

### **Security**

* Add CSRF on AJAX (future)
* Harden input validation

---

# 🧪 **Testing Guideline**

Checklist setiap modul selesai:

* [ ] Validation OK
* [ ] Flashdata errors tampil
* [ ] CRUD lengkap
* [ ] Migration works from fresh DB
* [ ] Stock movement correct
* [ ] HPP calculation correct
* [ ] No undefined index / null errors

---

# 🌱 **Development Rules**

* 1 fitur = 1 commit
* Migration tidak di-edit setelah push (buat migration baru)
* Jangan commit `.env`
* Kalau modul besar → buat feature branch

---

# 🔚 **Notes**

Dokumen ini diperbarui setiap selesai 1 sesi coding agar tidak kehilangan konteks.

Besok rencana utama:
✔ Lanjut ke **POS Sales Module**
✔ Integrasi Recipes + Stock OUT
✔ Buat UI kasir sederhana

---
