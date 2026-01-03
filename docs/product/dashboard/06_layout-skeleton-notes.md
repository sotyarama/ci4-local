# 🧩 Layout Skeleton Notes — Dashboard POS (Temu Rasa)

**Product:** Dashboard POS (Temu Rasa)  
**Dependency:**

-   01_dashboard-freeze-spec.md
-   04_ui-guardrail-rules.md
-   05_component-responsibility-map.md

**Purpose:**  
Mendokumentasikan struktur layout dasar (skeleton) dashboard  
sebelum implementasi UI, tanpa mengatur style detail.

Dokumen ini berfungsi sebagai:

-   pengingat niat desain
-   pencegah layout drifting saat refactor

---

## 1. Global Page Contract

-   Dashboard menggunakan **app-style layout**
-   Browser window **tidak boleh scroll**
-   Elemen berikut **fixed terhadap window**:
    -   sidebar (jika ada)
    -   topbar
    -   footer
-   **Hanya MAIN CONTENT** yang boleh scroll

Tujuan:

-   konsisten dengan aplikasi POS
-   mencegah nested scroll berlebihan

---

## 2. Main Content Scroll Area

Main content adalah:

-   satu container
-   overflow-y: scroll
-   tinggi = sisa viewport setelah topbar & footer

❌ Tidak boleh:

-   window scroll
-   multiple scroll container vertikal

---

## 3. Section Order (VERTICAL — LOCKED)

Urutan section **tidak boleh diubah**:

1. Ringkasan Performa (Headline KPI)
2. Perlu Perhatian (Alerts)
3. Aktivitas Terbaru
4. Insight Periode Ini

Alasan:

-   mengikuti hierarchy kognitif
-   menghindari insight terbaca sebelum kondisi bisnis

### Clarification — Vertical Layout Scope

The term **vertical layout** in this document refers to the **ordering of sections**, not the internal layout of components inside a section.

Each section (e.g. Ringkasan Performa, Perlu Perhatian, Insight, Transaksi Terbaru) must be read in a top-to-bottom order.

Components **within the same section** may use grid or multi-column layouts as long as:

-   the section hierarchy is preserved
-   the section does not visually dominate other sections
-   the reading order between sections remains vertical

---

## 4. Section Behavior Notes

### 4.1 Ringkasan Performa

-   Berbentuk grid card
-   Harus terbaca cepat
-   Tidak terlalu tinggi
-   Tidak mengandung scroll internal

---

### 4.2 Perlu Perhatian

-   List pendek
-   Selalu di bawah KPI
-   Tidak boleh visually overpower KPI

---

### 4.3 Aktivitas Terbaru

-   Berbentuk list
-   Mengikuti alur scroll utama
-   ❌ Tidak punya scroll internal terpisah
-   ❌ Tidak membatasi tinggi sendiri

Alasan:

-   hanya ada satu scroll axis
-   menghindari UX “scroll di dalam scroll”

---

### 4.4 Insight Periode Ini

-   Berbentuk card / grid ringan
-   Mengikuti scroll utama
-   Tidak diposisikan side-by-side dengan Activity

---

## 5. Responsive Intent (NON-NUMERIC)

### Layar sempit

-   Semua section vertikal
-   KPI grid menyesuaikan kolom
-   Tidak ada perubahan urutan

### Layar lebar

-   Tetap vertikal
-   Tidak memecah Activity vs Insight menjadi kolom

Konsistensi lebih penting dari kepadatan.

---

## 6. Density & Rhythm (Guideline)

-   KPI: padat & cepat dibaca
-   Alerts: cukup jarak agar terasa “tenang”
-   Activity: paling padat (operasional)
-   Insight: paling longgar (reflektif)

---

## 7. Skeleton Validation Checklist

Layout dianggap sesuai skeleton jika:

-   [ ] Window tidak scroll
-   [ ] Footer selalu terlihat
-   [ ] Hanya main content yang scroll
-   [ ] Urutan section tidak berubah
-   [ ] Tidak ada nested vertical scroll

---

## 8. Non-Goals

Dokumen ini **tidak mengatur**:

-   warna
-   font
-   ukuran spesifik
-   breakpoint detail
-   animasi

---

## 9. Guiding Sentence

> **Layout yang baik tidak terasa mengarahkan,  
> tapi tidak pernah membingungkan.**

---

**Layout Skeleton Status:** LOCKED  
**Next Phase:** UI Refactor / Layout Implementation
