# B2 – Global UI Elements

## Deskripsi

Tahap **B2 – Global UI Elements** bertujuan untuk menstandarkan seluruh komponen UI yang digunakan lintas halaman agar tampilan aplikasi **Temu Rasa POS** konsisten, rapi, dan mudah dikembangkan.

B2 dibangun di atas hasil **B1 (Layout & Typography)** dan menjadi fondasi sebelum masuk ke tahap **B3 (Page-Specific UX & Flow)**.

---

## Tujuan

-   Menyatukan gaya visual dan interaksi UI
-   Menghilangkan styling ad-hoc per halaman
-   Mempercepat pengembangan fitur baru
-   Menjaga konsistensi UX antara POS Touch dan halaman non-POS

---

## Scope Pekerjaan

### 1. Buttons

Standarisasi tombol global:

-   Variant: `primary`, `secondary`, `danger`, `ghost`
-   State: `default`, `hover`, `active`, `disabled`, `loading`
-   Size: `sm`, `md`, `lg`
-   Dukungan ikon + teks

### 2. Cards

Komponen struktur konten:

-   `.card`
-   `.card-header`
-   `.card-body`
-   `.card-footer`
-   Variant: `flat`, `outlined`, `interactive`

### 3. Form Elements

Standarisasi input:

-   Text input, textarea, select
-   Checkbox & radio (custom style)
-   Helper text & error state
-   Disabled & readonly state

### 4. Tables

Tabel non-DataTables:

-   Header styling
-   Row hover
-   Empty state
-   Alignment teks & numerik

### 5. Badges & Status

Indikator status:

-   `success`, `warning`, `danger`, `info`
-   Digunakan untuk status transaksi, stok, role user

### 6. Alerts & Toast

Feedback sistem:

-   Alert inline (info / success / warning / error)
-   Toast global (reusable)

### 7. Modal

Pola modal global:

-   Header, body, footer
-   Scroll behavior konsisten
-   Size: `sm`, `md`, `lg`

---

## Struktur File CSS

Direkomendasikan untuk modular & scalable:

```
css/
 ├─ theme-temurasa.css   // design tokens & color system
 ├─ layout.css           // layout global (sidebar, topbar, footer)
 ├─ ui/
 │   ├─ buttons.css
 │   ├─ cards.css
 │   ├─ forms.css
 │   ├─ tables.css
 │   ├─ badges.css
 │   ├─ alerts.css
 │   └─ modal.css
```

`main.php` hanya perlu memuat file CSS ini sekali untuk seluruh halaman.

---

## Urutan Implementasi

1. Buttons
2. Cards
3. Forms
4. Tables & Badges
5. Alerts & Modal

---

## Definition of Done (DoD)

-   Tidak ada inline style untuk komponen global
-   Seluruh halaman existing tetap aman (no regression)
-   POS Touch & halaman lain terasa satu sistem
-   Siap lanjut ke tahap **B3 – Page-Specific Polish & UX Flow**

---

## Catatan

-   B2 **tidak** mengubah logic atau behavior bisnis
-   Fokus murni pada konsistensi UI & UX
-   Semua komponen harus reusable dan documented
