# 📘 **DEV_NOTES.md**

# POS Café System — Development Notes

*Author: GS*
*Project: CodeIgniter 4 Local Development*
*Last updated: {{today}}*

---

## 📌 **Repository Overview**

Project ini adalah sistem POS sederhana untuk kebutuhan café kecil, dibangun menggunakan **CodeIgniter 4 (CI4)** dan **PHP 8.3**, dengan struktur modular untuk Master Data, Transaksi, dan Laporan.

Tujuan utama:

* POS penjualan
* Manajemen stok + bahan baku
* Perhitungan HPP dan food cost
* Pembelian bahan baku
* Overhead operasional
* Audit log & notifikasi
* Laporan-laporan dasar

---

# 🧱 **Commit History Summary**

### **1. Initial Setup (579c5ce, 2025-12-06)**

* Fresh install CodeIgniter 4
* Struktur default CI4
* Belum ada modul POS

---

### **2. Initial POS App (3b3a527, 2025-12-06)**

**Modules added:**

* Login & Logout
* Authentication filter
* Dashboard (layout utama)
* CRUD Master Products

**Database work:**

* Roles
* Users
* Menu Categories
* Menus
* Units
* Raw Materials

**Seeds:**

* Roles, Users, Menu Categories, Menus, Units

---

### **3. Suppliers + Purchases Module (32e2a4c, 2025-12-07)**

**Modules added:**

* Master Raw Materials CRUD (finalized)
* Master Suppliers CRUD (finalized)
* Purchases module (initial create)

**Database work:**

* Purchases table
* Purchase items table
* Suppliers table

Views & controllers untuk form dan list pembelian sudah siap, tetapi cooking logic belum diselesaikan (avg cost, stock movement, dll.)

---

# 🧩 **Current Modules Status**

| Module               | Status        | Notes                             |
| -------------------- | ------------- | --------------------------------- |
| Login / Auth         | ✔️ Complete   | Stable                            |
| Dashboard            | ✔️ Complete   | Layout ready                      |
| Master Products      | ✔️ Complete   | CRUD OK                           |
| Master Units         | ✔️ Done       | Used in other modules             |
| Master Raw Materials | ✔️ Complete   | CRUD + validation                 |
| Master Suppliers     | ✔️ Complete   | CRUD + validation                 |
| Purchases            | 🟡 Partial    | Needs backend logic (stock, cost) |
| Stock Movement       | ⛔ Not started | Planned                           |
| Recipes / HPP        | ⛔ Not started | Planned                           |
| POS Sales            | ⛔ Not started | Planned                           |
| Overhead             | ⛔ Not started | Planned                           |
| Reports              | ⛔ Not started | Planned                           |
| Audit Logs           | ⛔ Not started | Planned                           |
| Email Notifications  | ⛔ Optional    | Not started                       |

---

# 🚧 **NEXT TODOs (Short-Term)**

### **Purchases Module (Finish Core Logic)**

* [ ] Tambah `stock_movements` migration
* [ ] Update stok bahan baku otomatis ketika pembelian dibuat
* [ ] Hitung:

  * [ ] `cost_last`
  * [ ] `cost_avg`
* [ ] Tambah field:

  * subtotal per item
  * total pembelian
* [ ] Tambah view untuk riwayat pembelian lengkap

---

# 🍳 **NEXT TODOs (Medium-Term)**

### **Recipes & HPP Calculation**

* [ ] CRUD Resep (recipe header + recipe items)
* [ ] Isi resep otomatis dikurangi dari stok saat penjualan
* [ ] Hitung HPP per menu
* [ ] Format laporan food cost

---

# 🛒 **NEXT TODOs (Long-Term)**

### **POS Sales & Daily Operations**

* [ ] POS kasir
* [ ] Penjualan → reduce stock by recipe
* [ ] Invoice / struk sederhana

### **Reports**

* [ ] Laporan Penjualan
* [ ] Laporan Pembelian
* [ ] Laporan Stok
* [ ] Laporan Laba Rugi sederhana

### **Audit Logs**

* [ ] Tracking perubahan:

  * harga menu
  * resep
  * stok adjustment
* [ ] Multi-user awareness

### **System Settings**

* [ ] Owner email
* [ ] Auto email toggle
* [ ] Default currency (fixed: IDR)

---

# 🧪 **Testing Guideline**

Checklist setiap modul selesai:

* [ ] Form validation berfungsi
* [ ] Error handling standar (flashdata)
* [ ] CRUD lengkap (Add, Edit, Delete, Restore optional)
* [ ] SQL constraints tidak error
* [ ] Migration berjalan dari fresh install
* [ ] Routes bersih dan terstruktur

---

# 🌱 **Development Rules**

* 1 commit per fitur kecil → mudah tracking
* Migration tidak diubah setelah dipush → kalau mau update, buat migration baru
* Jangan commit .env (gunakan env template)
* Gunakan `feature branches` jika modul besar

---

# 🔚 **Notes**

Ini adalah project jangka panjang (beberapa minggu–bulan).
Dokumen ini menjaga konteks untuk menghindari miscommunication dan mempermudah lanjutan development setelah jeda 1–2 hari.

Jika terjadi perubahan struktur besar, tambahkan **CHANGELOG** di bawah section commit.

---

Siap pakai ya.

Kalau kamu ingin:

* versi markdown yang lebih fancy (emoji lebih banyak, warna, tabel status),
* versi PDF,
* atau mau aku tambahkan diagram ERD / flow architecture,

tinggal bilang saja.
