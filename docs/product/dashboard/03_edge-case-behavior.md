# 🧪 Dashboard Edge-Case Behavior Spec — Temu Rasa POS

**Product:** Dashboard POS (Temu Rasa)  
**Dependency:**

-   01_dashboard-freeze-spec.md
-   02_transition-checklist.md

**Purpose:**  
Menentukan perilaku dashboard pada kondisi data tidak ideal  
tanpa melanggar makna, psikologi, dan time contract.

---

## 1. Prinsip Umum (WAJIB)

-   Dashboard **tidak boleh panik** saat data tidak ideal
-   Edge case **bukan error**
-   Dashboard harus tetap:
    -   jujur
    -   tenang
    -   bisa dibaca

❌ Tidak boleh:

-   menyembunyikan card tanpa alasan
-   mengganti makna KPI
-   menampilkan pesan teknis

---

## 2. Empty Data (Tidak Ada Data)

### Kondisi

-   Tidak ada transaksi dalam active date range

### Perilaku

-   KPI tetap ditampilkan
-   Nilai ditampilkan sebagai **0** atau **—** (bukan error)
-   Tidak ada alert palsu

### Makna ke User

> “Dalam periode ini belum ada aktivitas.”

❌ Tidak boleh:

-   auto-ganti periode
-   menampilkan “N/A” teknis
-   menyarankan aksi

---

## 3. Zero Value (Angka = 0)

### Contoh

-   Penjualan = 0
-   Transaksi = 0
-   Margin = 0

### Perilaku

-   Angka **0 adalah valid**
-   Tetap ditampilkan sebagai fakta
-   Tidak diberi penekanan visual negatif

### Makna ke User

> “Ini hasil yang terjadi, bukan kesalahan.”

---

## 4. Partial Data (Data Tidak Lengkap)

### Contoh

-   Transaksi ada, tapi cost belum lengkap
-   Margin belum bisa dihitung sempurna

### Perilaku

-   KPI tetap ditampilkan
-   Jika perlu konteks:
    -   gunakan penanda netral (mis. “sementara”)
-   Tidak menghitung asumsi tersembunyi

❌ Tidak boleh:

-   menebak angka
-   mengisi dengan rata-rata
-   menyembunyikan card

---

## 5. Extreme Single Transaction

### Kondisi

-   Satu transaksi sangat besar / sangat kecil
-   Margin ekstrem muncul karena 1 kejadian

### Perilaku

-   Margin ekstrem **boleh muncul**
-   Tetap diperlakukan sebagai:
    -   insidental
    -   bukan pola

### Makna ke User

> “Ada transaksi yang menyimpang, perlu dicek.”

❌ Tidak boleh:

-   menyimpulkan masalah bisnis
-   memicu insight struktural

---

## 6. Very Short Date Range

### Contoh

-   Range hanya beberapa jam
-   Range hanya 1 transaksi

### Perilaku

-   Dashboard tetap bekerja normal
-   Tidak ada peringatan “data terlalu sedikit”
-   Insight tetap muncul apa adanya

### Makna ke User

> “Ini memang hasil dari periode yang kamu pilih.”

---

## 7. Alert Suppression Rules

### Margin Ekstrem

-   Tidak muncul jika:
    -   tidak ada transaksi
    -   margin tidak bisa dihitung
-   Tidak diulang berlebihan

### Stok Minimum

-   Tidak muncul jika:
    -   stok tidak dikelola
    -   item belum punya threshold

❌ Tidak boleh:

-   alert kosong
-   alert default tanpa basis data

---

## 8. Consistency with Time Contract

Dalam semua edge case:

-   Active date range **tidak pernah diubah otomatis**
-   Preset tidak berubah perilaku
-   Tidak ada fallback ke “hari ini”

---

## 9. Non-Goals (EXPLICIT)

Dokumen ini **tidak mengatur**:

-   UI empty state design
-   wording final tooltip
-   warna / icon
-   error handling teknis backend

---

## 10. Guiding Principle (Ringkas)

> **Dashboard tidak bertugas membuat data terlihat bagus.**  
> **Dashboard bertugas membuat data terlihat jujur dan bisa dipahami.**

---

**Edge-Case Spec Status:** ACTIVE  
**Next Document:**  
`04_ui-guardrail-rules.md`
