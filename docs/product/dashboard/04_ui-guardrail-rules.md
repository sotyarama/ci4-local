# 🧱 UI Guardrail Rules — Dashboard POS (Temu Rasa)

**Product:** Dashboard POS (Temu Rasa)  
**Dependency:**

-   01_dashboard-freeze-spec.md
-   02_transition-checklist.md
-   03_edge-case-behavior.md

**Purpose:**  
Menjaga agar eksplorasi UI, layout, dan interaksi  
tidak merusak makna, hierarki, dan psikologi dashboard.

Dokumen ini **bukan**:

-   design system
-   style guide
-   aturan warna / font

---

## 1. Core Principle (WAJIB)

UI **melayani makna**, bukan sebaliknya.

Jika UI membuat user:

-   salah paham prioritas
-   merasa dihakimi
-   panik tanpa alasan

➡️ UI dianggap **melanggar guardrail**.

---

## 2. Hierarchy Protection Rules

### Headline KPI (Ringkasan Performa)

-   ✅ Harus terbaca pertama
-   ✅ Mendapat ruang visual paling stabil
-   ❌ Tidak boleh:
    -   tersaingi alert
    -   terlihat seperti score / target
    -   diberi indikator naik-turun dramatis

---

### Alerts (Perlu Perhatian)

-   ✅ Harus terlihat tanpa mencari
-   ✅ Nada visual netral
-   ❌ Tidak boleh:
    -   lebih dominan dari headline KPI
    -   memakai visual “bahaya / darurat”
    -   auto-expand atau memaksa klik

---

### Activity & Insight

-   ✅ Diposisikan sebagai konteks & pola
-   ❌ Tidak boleh:
    -   tampil sebelum headline KPI
    -   memaksa user membaca
    -   terlihat lebih penting dari hasil bisnis

---

## 3. Visual Semantics Guardrail

UI **harus membedakan secara visual** antara:

| Makna     | Contoh                       |
| --------- | ---------------------------- |
| Hasil     | Penjualan, Margin            |
| Perhatian | Stok minimum, Margin ekstrem |
| Konteks   | Transaksi terbaru            |
| Insight   | Top Menu                     |

❌ Tidak boleh:

-   menyamakan visual alert dengan KPI
-   menyamakan insight dengan headline

---

## 4. Color & Emphasis Rule (NON-SPESIFIK)

-   Emphasis **boleh**, alarm **tidak**
-   Kontras **boleh**, intimidasi **tidak**

❌ Hindari:

-   merah keras untuk alert netral
-   hijau sebagai “nilai baik”
-   visual judgement (baik/buruk)

UI **tidak menilai**, UI **menyampaikan**.

---

## 5. Interaction Guardrails

### Click & Hover

-   Alert:
    -   boleh diklik
    -   tidak boleh auto-trigger
-   Insight:
    -   opsional untuk dieksplor
    -   tidak boleh memaksa interaksi

### Date Range Interaction

-   Mengubah range:
    -   mengubah **semua angka**
-   ❌ Tidak boleh:
    -   ada card yang “tetap”
    -   fallback otomatis ke hari lain

---

## 6. Empty & Edge State Visual Rules

-   Empty ≠ Error
-   Zero ≠ Failure

❌ Tidak boleh:

-   pesan error teknis
-   visual dramatis saat data kosong
-   menyembunyikan card tanpa alasan

UI harus:

-   tenang
-   jujur
-   informatif

---

## 7. Copy & Label Guardrails

-   Judul card **harus konsisten** dengan Freeze Spec
-   Micro copy:
    -   1 kalimat
    -   netral
-   ❌ Tidak boleh ada:
    -   “target”
    -   “pencapaian”
    -   “hari ini”
    -   bahasa performa individu

---

## 8. Forbidden UI Patterns (EXPLICIT)

UI **tidak boleh**:

-   leaderboard staff
-   ranking performa
-   badge “baik / buruk”
-   comparison antar periode otomatis
-   progress bar target

Jika pattern ini muncul:
➡️ dianggap **melanggar spec produk**.

---

## 9. Designer Freedom (WHAT IS ALLOWED)

Designer **bebas**:

-   memilih grid
-   menentukan spacing
-   mengatur responsive layout
-   mengeksplor tipografi
-   menyusun card secara visual

Selama:

-   hierarchy tidak rusak
-   makna tidak berubah
-   psikologi user aman

---

## 10. Final Guardrail Statement

> **UI yang bagus adalah UI yang tidak mengubah cara user memahami data.**

Jika ragu:
➡️ kembali ke Freeze Spec.

---

**UI Guardrail Status:** ACTIVE  
**Scope:** Dashboard UI & Interaction  
**Next Step:** Layout & Component Design
