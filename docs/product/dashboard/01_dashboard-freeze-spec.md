# 🔐 Dashboard Freeze Spec — Temu Rasa POS

**Product:** Dashboard POS (Temu Rasa)  
**Audience:** Owner & Staff  
**Status:** FINAL / LOCKED  
**Function:** Single Source of Truth for Dashboard Product Decisions

---

## 1. Purpose & Positioning

Dashboard digunakan untuk tiga tujuan sekaligus:

1. Monitoring harian operasional
2. Evaluasi performa bisnis
3. Decision support cepat

Dashboard **bukan**:

-   laporan periodik statis
-   alat audit kinerja individu

---

## 2. User & Psychology Contract

### Target User

-   Owner
-   Staff operasional

### Prinsip Psikologis

-   Tidak menghakimi
-   Tidak menekan
-   Tidak memicu defensif
-   Mengundang perhatian & aksi sadar

---

## 3. Time Contract (LOCKED)

### Single Source of Truth

-   Satu dashboard = satu **active date range**
-   Semua KPI, alert, dan insight **wajib** mengikuti range aktif
-   Preset (Today / 7D / MTD) hanya shortcut picker

❌ Tidak diperbolehkan:

-   KPI dengan logika periode berbeda
-   Referensi “hari ini”
-   Perbandingan periode otomatis

---

## 4. Information Hierarchy (LOCKED)

### Level 1 — Ringkasan Performa

_Gambaran hasil bisnis pada periode yang sedang dipilih._

**Card:**

-   Penjualan
-   Transaksi
-   Avg Ticket
-   Item Terjual
-   Margin / Profit

---

### Level 2 — Perlu Perhatian

_Beberapa hal yang sebaiknya dicek agar operasional tetap lancar._

**Card (NOW):**

-   Stok mendekati minimum
-   Margin ekstrem (transaksi)

---

### Level 3 — Aktivitas Terbaru

_Transaksi yang baru saja terjadi dalam periode aktif._

**Card:**

-   Transaksi terbaru

---

### Level 4 — Insight Periode Ini

_Pola dan kecenderungan yang menonjol dari periode ini._

**Card (NOW):**

-   Top Menu / Menu Terlaris

**Card (LATER):**

-   Peak Hour
-   Peak Day
-   Menu bermargin ekstrem

---

## 5. KPI Contract (LOCKED)

### Headline KPI

-   Semua headline KPI bersifat **NOW**
-   Selalu mengikuti active date range
-   Tidak ada KPI statis

### Margin / Profit

-   1 KPI tunggal
-   Menampilkan:
    -   Rp (makna utama)
    -   % (konteks efisiensi)
-   Bukan KPI performa individu

---

## 6. Alert Semantic Contract (LOCKED)

### Prinsip Alert

-   Alert = permintaan perhatian
-   Bukan judgement
-   Bukan alarm bahaya

### Margin Ekstrem — Transaksi (NOW)

_Transaksi yang hasil marginnya tidak wajar dan perlu dicek._

-   Insidental
-   Taktis
-   Untuk verifikasi transaksi

### Margin Ekstrem — Menu (LATER)

_Menu yang secara konsisten menghasilkan margin tidak wajar dalam periode aktif._

-   Struktural
-   Strategis
-   Untuk keputusan bisnis

---

## 7. Card-Level Micro Copy Contract

### Aturan Umum

-   1 kalimat
-   Bahasa manusia
-   Netral
-   Tidak teknis
-   Tidak menghakimi
-   Implisit tunduk ke active date range

### Micro Copy Final

-   **Penjualan:** Total nilai penjualan dalam periode aktif.
-   **Transaksi:** Jumlah transaksi yang tercatat pada periode ini.
-   **Avg Ticket:** Rata-rata nilai penjualan per transaksi.
-   **Item Terjual:** Total item yang terjual dalam periode aktif.
-   **Margin / Profit:** Selisih pendapatan dan biaya dari penjualan pada periode ini.
-   **Stok Minimum:** Item yang jumlahnya hampir mencapai batas minimum.
-   **Margin Ekstrem:** Transaksi dengan hasil margin yang tidak wajar dan perlu dicek.
-   **Transaksi Terbaru:** Daftar transaksi yang baru saja tercatat pada periode aktif.
-   **Top Menu:** Menu yang paling sering dibeli pada periode ini.

---

## 8. Explicitly Dropped (FINAL)

Dashboard **tidak menampilkan**:

-   Pembelian bahan
-   Biaya operasional
-   Beban bulanan
-   KPI statis (Today / MTD)
-   Performance score
-   Target vs actual

---

## 9. UI & Implementation Note

-   Dokumen ini tidak mengatur:
    -   warna
    -   layout
    -   chart
    -   komponen UI
-   Semua keputusan UI **wajib tunduk** ke spec ini
-   Jika terjadi konflik: **UI yang salah, bukan spec**

---

## 10. Freeze Rule

-   Spec ini **tidak boleh diubah**
    kecuali ada perubahan:
    -   tujuan produk
    -   user utama
-   Perubahan UI **tidak dianggap perubahan spec**

---

**Dashboard Spec Status:** 🔒 FROZEN  
**Ready for UI / Layout Phase**
