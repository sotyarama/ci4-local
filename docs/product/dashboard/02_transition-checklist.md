# 🧭 Dashboard Transition Checklist — Product → UI → Implementation

**Product:** Dashboard POS (Temu Rasa)  
**Dependency:** 01_dashboard-freeze-spec.md  
**Purpose:** Guardrail agar desain & implementasi tidak melanggar keputusan produk

---

## 0. Golden Rule (WAJIB)

-   Freeze Spec adalah **otoritas tertinggi**
-   Checklist ini **tidak boleh** mengubah:
    -   hierarchy
    -   KPI logic
    -   alert semantics
    -   time contract
-   Jika ada konflik:
    👉 **Desain / implementasi yang salah, bukan spec**

---

## 1. Pre-UI Sanity Check (SEBELUM DESAIN)

Pastikan semua poin ini **YA** sebelum mulai layout:

-   [ ] Semua KPI mengikuti **single active date range**
-   [ ] Tidak ada KPI statis (Today / MTD / dll)
-   [ ] Semua card yang ditampilkan ada di Freeze Spec
-   [ ] Tidak ada card yang termasuk daftar DROPPED
-   [ ] Margin diperlakukan sebagai **1 KPI (Rp + %)**

Jika ada “ide tambahan”:
➡️ parkir, **jangan masuk UI**

---

## 2. Hierarchy Preservation Check

Saat menyusun layout, pastikan:

### Level 1 — Ringkasan Performa

-   [ ] Terlihat pertama tanpa scrolling berat
-   [ ] Tidak tertutup / tersaingi alert
-   [ ] Urutan baca tetap logis (hasil → kualitas)

### Level 2 — Perlu Perhatian

-   [ ] Terlihat jelas tapi **tidak dominan**
-   [ ] Tidak tampil sebagai alarm bahaya
-   [ ] Nada visual ≠ merah darurat

### Level 3 — Aktivitas Terbaru

-   [ ] Mudah di-skip tanpa kehilangan makna
-   [ ] Tidak mengganggu headline KPI

### Level 4 — Insight Periode Ini

-   [ ] Diletakkan setelah user paham kondisi
-   [ ] Tidak “memaksa dibaca”

---

## 3. Copy & Semantics Guardrail

Untuk **SEMUA CARD**:

-   [ ] Judul card sesuai Freeze Spec
-   [ ] Micro copy 1 kalimat terpasang / tersedia
-   [ ] Tidak ada kata:
    -   hari ini
    -   target
    -   performa individu
    -   gagal / buruk / salah
-   [ ] Tidak ada copy yang mengandung judgement

---

## 4. Margin & Alert Specific Check

### Margin / Profit

-   [ ] Ditampilkan sebagai **1 card**
-   [ ] Rp = makna utama
-   [ ] % = konteks
-   [ ] Tidak diperlakukan sebagai score / rating

### Margin Ekstrem

-   [ ] Diposisikan sebagai **alert**, bukan KPI
-   [ ] Copy netral (“perlu dicek”)
-   [ ] Tidak dipakai untuk evaluasi staff

---

## 5. Interaction & Behavior Check

Saat user berinteraksi:

-   [ ] Mengganti date range → **semua angka berubah**
-   [ ] Tidak ada card yang “tetap”
-   [ ] Preset (Today / 7D / MTD) hanya shortcut
-   [ ] Tidak ada auto-comparison antar periode

---

## 6. Empty & Edge State Awareness (BELUM DETAIL)

Catatan untuk fase berikutnya:

-   Data kosong
-   Angka 0
-   Periode sangat pendek
-   Transaksi ekstrem tunggal

➡️ Akan dibahas di dokumen terpisah:
`03_edge-case-behavior.md`

---

## 7. Final Gate — Before Merge

Sebelum UI / feature di-merge:

-   [ ] Freeze Spec sudah dibaca ulang
-   [ ] Checklist ini sudah dilewati
-   [ ] Tidak ada “interpretasi bebas”
-   [ ] Tidak ada UI-driven decision

Jika ragu:
➡️ **Kembali ke Freeze Spec**

---

**Checklist Status:** ACTIVE  
**Role:** Guardrail, bukan blocker  
**Next Phase:** UI / Layout Exploration
