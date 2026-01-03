# 🧱 Component Responsibility Map — Dashboard POS (Temu Rasa)

**Product:** Dashboard POS (Temu Rasa)  
**Dependency:**

-   01_dashboard-freeze-spec.md
-   02_transition-checklist.md
-   03_edge-case-behavior.md
-   04_ui-guardrail-rules.md

**Purpose:**  
Menentukan batas tanggung jawab setiap komponen dashboard  
agar refactor UI tidak mencampur logic, data, dan presentasi.

Dokumen ini menjawab:

> “Komponen ini seharusnya **tahu apa**, dan **tidak tahu apa**?”

---

## 1. Prinsip Umum (WAJIB)

-   Logic bisnis **tidak boleh bocor ke UI**
-   UI **tidak mengambil keputusan data**
-   Setiap card:
    -   tahu **apa yang ditampilkan**
    -   tidak tahu **kenapa angka itu demikian**

---

## 2. Kategori Komponen

Dashboard dibagi ke 3 jenis komponen utama:

1. **Presentational Component**
2. **Data-Aware Component**
3. **Controller / Orchestrator**

---

## 3. Presentational Components (UI MURNI)

### Karakteristik

-   Tidak punya logic bisnis
-   Tidak tahu date range
-   Tidak menghitung angka
-   Hanya menerima data final

### Contoh

-   KPI Card (Penjualan, Transaksi, dll)
-   Alert Card
-   Insight Card
-   Empty / Zero state UI

### Tanggung Jawab

-   Menampilkan nilai
-   Menampilkan judul & micro copy
-   Menampilkan state (normal / empty)

### Tidak Boleh

-   Menghitung margin
-   Menentukan ekstrem / tidak
-   Mengubah data

📌 **Jika angka salah → bukan salah komponen ini**

---

## 4. Data-Aware Components (LOGIC RINGAN)

### Karakteristik

-   Tahu active date range
-   Mengambil data dari helper / service
-   Tidak membuat keputusan bisnis

### Contoh

-   Dashboard KPI Aggregator
-   Alert Data Provider
-   Insight Data Provider

### Tanggung Jawab

-   Mengambil data sesuai range
-   Menyediakan data siap tampil
-   Menentukan empty / zero secara netral

### Tidak Boleh

-   Mengubah definisi KPI
-   Membuat threshold “pintar”
-   Menginterpretasi makna data

📌 **Boleh tahu “apa”, tidak boleh tahu “kenapa”**

---

## 5. Controller / Orchestrator (LOGIC UTAMA)

### Karakteristik

-   Menjadi sumber kebenaran
-   Mengatur active date range
-   Mengorkestrasi semua card

### Contoh

-   Dashboard Controller
-   Dashboard ViewModel / Presenter

### Tanggung Jawab

-   Menentukan periode aktif
-   Memastikan semua data sinkron
-   Menjaga Time Contract

### Tidak Boleh

-   Mengatur UI detail
-   Mengatur layout
-   Mengatur style

---

## 6. Responsibility by Card (RINGKAS)

### 🟢 Headline KPI

-   **Data source:** KPI Aggregator
-   **Logic:** Backend / helper
-   **UI:** Pure presentational

---

### 🟡 Alerts

-   **Data source:** Alert Provider
-   **Logic:** Rule sederhana (bukan judgement)
-   **UI:** Netral, tidak dominan

---

### 🔵 Activity

-   **Data source:** Recent Transaction Provider
-   **Logic:** Urut & filter sesuai range
-   **UI:** List sederhana

---

### 🟦 Insight

-   **Data source:** Insight Provider
-   **Logic:** Agregasi ringan
-   **UI:** Opsional & eksploratif

---

## 7. Anti-Pattern (EXPLICIT)

Komponen **tidak boleh**:

-   menghitung KPI di view
-   menentukan alert di UI
-   membandingkan periode di frontend
-   mengubah data karena UI constraint

Jika terjadi:
➡️ dianggap **pelanggaran arsitektur dashboard**

---

## 8. Refactor Guidance (PRAKTIS)

Saat refactor UI:

-   Jika bingung taruh logic di mana → **taruh di controller / helper**
-   Jika UI mulai punya `if bisnis` → **salah tempat**
-   Jika card mulai “pintar” → **pecah komponen**

---

## 9. Final Responsibility Statement

> **UI bertugas menampilkan.  
> Logic bertugas menentukan.  
> Data bertugas jujur.**

---

**Component Map Status:** ACTIVE  
**Next Phase:** UI Refactor & Layout Implementation
