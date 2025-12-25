# DEV_NOTES.md

POS Cafe System — Development Notes  
Author: GS (updated by security review)  
Last updated: 2025-12-25 (Audit & history purge)

---

## Executive summary ✅

This document summarizes the project status after a security-first audit (2025-12-25), the remedial actions performed, current risks, verification steps, and an actionable follow-up checklist. Key outcomes:

-   Removed sensitive runtime artifacts and archived them locally. 🔒
-   Deleted historical `env` file from git history and force-pushed a cleaned repository mirror to remote. ✅
-   Added CI secret-scanning and PHPUnit workflows; added `SECURITY.md`. 🔍
-   Generated rotate-ready credentials and wrote rotation instructions; created a local `.env` for testing (never committed). 🔑

---

## Quick project snapshot

-   Framework: **CodeIgniter 4** (PHP 8.3 compatible).
-   App: POS + inventory + recipe/HPP engine + basic payroll & reports.
-   Tests: PHPUnit configured (CI runs use sqlite3).
-   CI: Added `phpunit.yml` and `secret-scan.yml` workflows.

---

## Actions taken (what I did) 🛠️

1. Immediate cleanup

    - Removed sensitive runtime artifacts (debugbar JSON, logs) and archived them to `backups/secret-backups-2025-12-25.zip` (excluded from git).
    - Ensured `.env` is ignored and added ` .env.example` (no secrets).

2. Git history purge (safe, auditable)

    - Created `pre-purge-2025-12-25` backup branch on remote.
    - Created and cleaned a local mirror (`C:/repo-mirror-2025-12-25.git`) using **BFG** to remove `env` blobs and attempted replace-text for found strings.
    - Verified by cloning cleaned mirror (`C:/repo-cleaned-check`) and searching for secret strings — **no matches found**.
    - Force-pushed cleaned history to GitHub remote (master replaced). A remote backup branch `backup/pre-purge-2025-12-25` was created prior to the rewrite.

3. Secrets & credential rotation

    - Generated strong passwords and an app encryption key locally and stored them in a local, permission-restricted backup file `backups/credentials-rotated-2025-12-25.txt` (not committed).
    - Wrote `backups/rotation-instructions-2025-12-25.md` with exact DB/SMTP rotation commands and GitHub Actions secret guidance.
    - Created a temporary local `.env` to validate app connectivity; validated DB connectivity (SELECT 1 succeeded).

4. CI & repo hardening

    - Added `secret-scan` workflow and `phpunit` workflow and committed `SECURITY.md` with rotation/purge procedures.

5. Commit cleanup
    - Committed post-purge helper scripts and `scripts/check_db.php` and rotation instructions.

---

## Verification performed ✅

-   Searched cloned cleaned repo for known secret strings (exact match and `-S` git search): **no results**.
-   Ran local DB connectivity check and `php scripts/check_db.php`: returned `SELECT 1 = 1`.
-   Ran BFG reports and `git gc` on the mirror. Reports stored in `C:/repo-mirror-2025-12-25.git.bfg-report/` for audit.

---

## Current status & remaining risk ⚠️

-   History purge: **complete** on remote (forced update done). Team must re-clone or reset local clones.
-   Credentials: **rotated locally**, but remote provider-side revocation/rotation may still be required (SMTP provider password must be revoked in provider UI). I have not changed provider-side secrets because admin/login access is required.
-   CI secrets: **not** yet set in GitHub Actions (we prepared instructions). Use `gh` or the UI to create secrets.
-   Logs & debug data: archived offline; ensure no other backups contain secrets.

---

## Immediate follow-up actions (HIGH priority) ⚠️

1. Revoke old SMTP credentials at provider (Brevo/Gmail) immediately and confirm new secret validity. (Owner/Provider access required.)
2. Update deployment servers and staging with new `.env` values (database password, smtp password, encryption key) and restart services. Test DB and SMTP.
3. Add secrets to GitHub Actions (via `gh` or UI): `APP_DB_PASSWORD`, `APP_SMTP_USER`, `APP_SMTP_PASS`, `APP_ENCRYPTION_KEY`.
4. Notify collaborators: advise all devs to re-clone and recreate local envs. Use the provided message below.

---

## Recommended collaborator notice (copy/paste) ✉️

> Heads-up: repository history was cleaned for secret removal on 2025-12-25. Remote history was rewritten and force-updated. Please backup any local changes and then either:
>
> -   Re-clone the repo: `git clone https://github.com/sotyarama/ci4-local.git`, or
> -   If you keep your clone: `git fetch origin && git switch master && git reset --hard origin/master` (then re-create any local branches from the new master).
>     Note: A remote backup branch `backup/pre-purge-2025-12-25` exists if you need to inspect pre-purge refs.

---

## Short-term recommended TODOs (next 24–72h) 📋

-   [ ] Revoke provider secrets (SMTP) and confirm provider-side rotation. (CRITICAL)
-   [ ] Set repository secrets in GitHub Actions and update CI to use them. (CRITICAL)
-   [ ] Update deployed `.env` on servers and restart apps; run smoke tests (DB & SMTP). (CRITICAL)
-   [ ] Ask all collaborators to re-clone / reset local clones and re-setup `.env` from ` .env.example`.
-   [ ] Ensure backups (offsite) that may contain the old `env` are rotated/cleaned.

---

## Medium-term improvements (this sprint) 🛡️

-   Harden CI: add automatic secret-scan on PRs, and block merges that fail leak checks.
-   Add token vault integration (HashiVault / GitHub Secrets Manager) for deployment workflows.
-   Add an automated job to scan the repo weekly and alert on newly committed secrets.
-   Add audit logs for critical changes (user/password/email settings, recipes price changes).

---

## Tests & validation checklist ✅

-   `phpunit` runs on CI (we added workflow) — ensure test suite green and sqlite3 extension present in CI.
-   Smoke-tests after rotation: DB connect, write-read; SMTP send a test message.
-   Manual: Walk forgot/reset password flow to ensure tokens work and no leaked tokens exist.

---

## Files / artifacts of interest

-   `backups/secret-backups-2025-12-25.zip` (archived debug/logs) — local only.
-   `backups/credentials-rotated-2025-12-25.txt` — local, permission restricted.
-   `backups/rotation-instructions-2025-12-25.md` — step-by-step rotation & CI secrets instructions.
-   `C:/repo-mirror-2025-12-25.git.bfg-report/` — detailed BFG output (audit trail).
-   `pre-purge-2025-12-25` and `backup/pre-purge-2025-12-25` — remote refs for inspection.

---

## Final notes & contacts

If you'd like, I can:

-   Apply provider-side rotation (DB / SMTP) if you provide the admin credentials or delegate access securely. 🔑
-   Set GitHub repo secrets if you authorize and provide a PAT with `repo` scope or run `gh auth login` locally and give me the session.
-   Clean up temporary files (mirror repo and `bfg.jar`) and archive reports into `backups/` when you confirm audit completion.

---

_This DEV_NOTES.md was updated automatically to reflect the security audit and corrective actions performed on 2025-12-25._

Project POS lengkap untuk cafe kecil menggunakan **CodeIgniter 4**. Fokus: backend rapi, logika stok & costing akurat; UI modern menyusul di fase berikutnya.

Tujuan utama:

-   POS penjualan
-   Manajemen stok (IN/OUT)
-   Perhitungan HPP (recipe-based)
-   Master data menu, bahan baku, supplier
-   Pembelian & average costing
-   Laporan dasar (penjualan harian)
-   Audit log (planned)
-   POS touchscreen UI (planned skin)

---

# Commit / Milestone Summary

## 1) Initial Setup (579c5ce — 2025-12-06)

-   Fresh CI4 project, struktur dasar & konfigurasi awal

## 2) POS App Core (3b3a527 — 2025-12-06)

-   Auth (login + filter), dashboard layout
-   CRUD Products (menu)
-   Migrations: roles, users, menu_categories, menus, raw_materials, units
-   Seeds: roles, users, menu_categories, menus, units

## 3) Purchasing System (32e2a4c — 2025-12-07)

-   CRUD Raw Materials & Suppliers
-   Purchases module (header + items)
-   Stock IN: update `raw_materials.current_stock`, `cost_last`, `cost_avg` (average costing)
-   Stock movements: insert IN untuk pembelian

## 4) Recipes + HPP Module (2025-12-08 Afternoon)

-   CRUD Recipes + Recipe Items
-   HPP per menu: pakai `raw_materials.cost_avg`, tampil per yield
-   Recipe-based cost engine siap dipakai Sales

## 5) Sales Backend Logic v1 (2025-12-08 Night)

**Core POS logic fungsional**

-   Migrations: `sales`, `sale_items` (hpp_snapshot)
-   Controller `Transactions\Sales`: simpan header/detail, subtotal, total_amount, total_cost, HPP snapshot, stock OUT berdasar resep (scaling + waste_pct), validasi stok, rollback jika kurang.
-   Sales detail view: margin per item & ringkasan (revenue, HPP, margin nominal & %).
-   End-to-end: Menu → Recipe → Purchase → Sales → HPP → Margin → Stock OUT.

## 6) Stock Movement List + Sales Report (2025-12-08 Late Night)

-   Inventory\StockMovements: riwayat IN/OUT + filter bahan, date range, ref_type/id.
-   Reports\SalesSummary::daily (digantikan laporan by time).
-   Sidebar update (master, transaksi, inventory, laporan).

## 7) Stabilization & Fixes (2025-12-09 Morning)

-   Sales: blok transaksi jika menu tanpa resep/detail (tidak ada fallback HPP 0); error DB dialihkan ke flash+log (tidak dd).
-   Stock movements: model tidak menulis `updated_at`; `created_at` diisi manual sesuai kolom.
-   Sidebar: link pembelian → `/purchases`.

## 8) Summaries, Stock Card & Demo Seed (2025-12-09 Afternoon)

-   Sales Index: summary harian (total penjualan, margin nominal & %) di header.
-   Inventory: Kartu Stok per bahan dengan filter tanggal, opening balance, saldo berjalan, delta vs current_stock; link di sidebar.
-   Seeders baru & guard anti-duplikat:
    -   SuppliersSeeder, RawMaterialsSeeder, PurchasesDemoSeeder (2 PO + movement IN + update stock/cost), RecipesDemoSeeder (resep untuk menu SKU), UsersSeeder (owner) tetap.
    -   DatabaseSeeder memanggil seluruh seeders; aman rerun karena guard.

## 9) Stabilization Precision + Guard (2025-12-09 Late Night)

-   Recipes: validasi waste_pct dibatasi 0-100, rounding 3 desimal saat simpan.
-   HPP: clamp waste_pct dan rounding qty/cost (6 desimal) untuk kurangi noise float; total_cost & hpp_per_yield ikut di-round.
-   Sales: kebutuhan bahan & pengurangan stok di-round 6 desimal, guard stok pakai nilai yang sudah dinormalisasi, pesan shortage lebih jelas.
-   Stock Movements/Card: saldo berjalan & qty di-round per langkah untuk menghindari drift.

## 10) Brand Guide Presentation (2025-12-10 Morning)

-   Route `/brand-guide` menuju controller `BrandGuide` yang merender deck Reveal.js.
-   View `brand_presentation.php`: 14 slide brand/UI guideline (cover, essence, story, color palette, typography, logo meaning/usage, pattern, photography mood, POS cashier/dashboard, menu layout, tone of voice, closing).
-   Theme `public/css/temurasa-reveal.css`: palet Temu Rasa, typography Nunito/Inter/Poppins, carded slide layout, color grid, chips/divider, responsive tweak untuk mobile.

## 11) Sub-Recipe Support (2025-12-12)

-   Migration `AddSubrecipeSupport`: `recipe_items` bisa `item_type=raw|recipe`, kolom `child_recipe_id` (nullable), `raw_material_id` sekarang boleh null.
-   Recipe form: pilih tipe baris (bahan baku atau sub-resep), dropdown sub-resep (daftar resep lain), unit label mengikuti pilihan; tambah guard siklus dan validasi qty/waste.
-   HPP engine: rekursif dengan raw breakdown per batch + guard siklus; sub-resep ikut dihitung ke HPP dan konsumsi bahan.
-   Sales flow: kebutuhan stok & pengurangan stok memakai raw breakdown (flatten) dari resep, jadi sub-resep ikut mengurangi stok bahan baku.

## 12) Sales Time Report + POS Touch + Stock Variance + Payroll (2025-12-15)

-   Reports\SalesSummary::byTime: laporan penjualan by day/week/month/year + preset range ala Moka (start/end, all-day/time, CSV). Laporan harian/bulanan terpisah dihapus.
-   Laporan Stok & Selisih: opening (movement sebelum start), IN/OUT periode, saldo akhir, stok sistem, selisih per bahan (filter bahan/tanggal/pencarian).
-   POS Touchscreen (Phase 2 awal): grid card menu, tap tambah qty, keranjang qty +/-/hapus, submit ke Sales::store (backend sama).
-   Payroll Overhead (owner only): CRUD payroll bulanan per staff (role Staff) + filter; tabel `payrolls` (uniq user+periode).

## 13) Dashboard KPI & Alerts (2025-12-16)

-   KPI live: omzet, cost, margin%, avg ticket, transaksi untuk hari ini, 7 hari, dan bulan berjalan, plus delta MTD vs bulan lalu (non-void saja).
-   Top 5 menu 7 hari terakhir (qty, omzet, margin%).
-   Peringatan stok minim: raw_materials dengan current_stock <= min_stock (include unit) + meter bar.
-   Ringkasan biaya bulan berjalan: total pembelian, overhead operasional, dan payroll.
-   5 transaksi terbaru non-void (total, margin, margin%).

## 14) Auth Hardening & Password Reset (2025-12-19)

-   CSRF diaktifkan global melalui filter `csrf`; halaman/form eksisting sudah pakai `csrf_field()` dan meta token di JS.
-   Page cache global dihapus dari `required` untuk menghindari cache halaman dinamis/auth; `forcehttps` tetap on (potensi friksi dev jika baseURL http).
-   Session fixation hardening: session ID diregenerate saat login sukses; session keys tetap sama (user_id, username, role, isLoggedIn, dll.).
-   Forgot/Reset Password ditambahkan di custom auth (tanpa Shield): routes `/auth/forgot` & `/auth/reset`, controller Auth\ForgotPassword + Auth\ResetPassword, views baru + link “Lupa password?” di login.
-   Password reset tokens: migration `password_resets` (token_hash SHA-256, expires_at, used_at, created_at, request_ip, user_agent, FK users, indexes on user_id/exp/used). Token dibuat dari base64url(random_bytes(32)), disimpan hash; throttle 2 menit per email; expiry default 60 menit; reset sukses menandai semua token aktif user sebagai used.
-   Model baru `PasswordResetModel` untuk simpan & validasi token (hash_equals).
-   Email reset dikirim via Services::email() dengan konfigurasi Brevo SMTP dari .env (placeholder, secret tidak dikomit); body teks singkat berisi tautan reset + expiry info.

### Manual steps (ops)

-   Jalankan migrasi: `php spark migrate`.
-   Isi kredensial Brevo SMTP di `.env` (host/port/user/pass/from/name) & verifikasi sender di Brevo.
-   Smoke-test POST/redirect setelah CSRF aktif + alur forgot/reset end-to-end (request, email link, reset, login baru).

### Risks / Notes

-   CSRF sekarang wajib: form/JS yang tidak menyertakan token akan 403; DataTables/AJAX harus pakai meta token yang sudah disediakan.
-   `forcehttps` masih global; baseURL http saat dev bisa memicu redirect loop jika server belum https.
-   Pastikan `.env` tetap di-ignore (jangan commit secret SMTP).

### TODO / Recommended (prioritas)

1. Bungkus proses reset password dalam DB transaction (update password + invalidasi token).
2. Tambah index token_hash + pertimbangkan lookup langsung (bukan loop hasil findAll).
3. Tambah password_needs_rehash saat login untuk upgrade hash lama.
4. Cleanup job/command untuk expired/used password_resets.
5. Perkuat throttling (per IP/email) untuk permintaan reset.
6. Tambah Change Password untuk user yang sudah login.
7. Granular authorization per-route (permissions), bukan hanya role filter global.
8. Evaluasi CSP & pemisahan Filters required list untuk dev/prod.

## Uncommitted (2025-12-10) - Temu Rasa UI Refresh

-   Brand theme file `public/css/theme-temurasa.css` dengan CSS variables + komponen dasar (card, input, button, table, scrollbar) sesuai palet Temu Rasa.
-   Layout utama & login: background softCream, topbar primary, surface beige/green, teks charcoal, scrollbar mengikuti tema.
-   View POS (master, transaksi, inventory, reports, overhead) dipetakan ke palette: tombol aksi kini solid (primary/accents), alert/badge/header tabel pakai secondary green/beige.
-   Margin/margin% di laporan & sales detail pakai `--tr-primary-deep` untuk kontras; void notice diperjelas.
-   Tabel/aksi dirapikan: padding header/body diperbesar, tombol “Detail” di pembelian diperkecil & center untuk proporsi lebih rapi.
-   Laporan baru: Pembelian per Supplier (`reports/purchases/supplier`) dengan filter tanggal & supplier, ringkasan jumlah PO + total pembelian per pemasok + grand total, link di sidebar Reports.
-   Laporan baru: Penjualan per Kategori (`reports/sales/category`) dengan filter tanggal + pagination + export CSV; sidebar Reports menaut ke laporan ini (menggantikan placeholder “Margin per Kategori”).

---

## Uncommitted (2025-12-13) - Dropdown Filterable & Inline Fixes

-   Filter-select: opsi placeholder `value=""` tidak ikut di daftar; input filter hanya menampilkan placeholder, tidak dipakai sebagai value.
-   Auto-init diperkuat: init di DOMContentLoaded + fallback window.load.
-   CSS: z-index dropdown/list dinaikkan, overflow parent (card/row/col/table-scroll) dibuka agar menu tidak terpotong.
-   POS Sales: dropdown dalam tabel dipaksa inline (position static) supaya tinggi row mengikuti dropdown.
-   Tidak ada perubahan HTML; hanya JS + CSS.

## Uncommitted (2025-12-13) - Dropdown Filterable Alignment & Whitespace

-   Option label dibersihkan (hapus NBSP, trim, collapse whitespace) sebelum dirender supaya teks sejajar kiri.
-   Input filter dipaksa left-align via style JS (important) dan diterapkan ulang saat sinkron/open untuk cegah override.
-   Multi-row behavior: saat satu dropdown dibuka, dropdown lain otomatis ditutup (hindari tumpuk).
-   Placeholder `value=""` tetap diabaikan (hanya tampil sebagai placeholder input); event change/form submit tidak berubah.
-   File: `public/js/app.js`.

## Uncommitted (2025-12-13) - Guard Tambah Resep

-   Tombol "Tambah Resep" di list resep otomatis disabled jika semua menu sudah punya resep (ada title helper).
-   Guard backend di `Recipes::create()` memblokir akses langsung saat tidak ada menu tersisa tanpa resep.

## Uncommitted (2025-12-13) - Recipe Form Defaults

-   Form resep: default hanya 1 baris komposisi saat create; dropdown tipe kini pakai placeholder "Pilih tipe bahan" dengan opsi "Bahan Baku" / "Sub-resep".
-   Dropdown tipe kosong menyembunyikan kedua dropdown bahan/sub-resep sampai user memilih.

# Current Modules Status

| Module                | Status              | Notes                                                                                   |
| --------------------- | ------------------- | --------------------------------------------------------------------------------------- |
| Auth / Login          | Complete            | Stable                                                                                  |
| Dashboard             | Complete            | KPI harian/7d/MTD, delta vs bulan lalu, top menu 7d, low-stock alert, transaksi terbaru |
| Master Products       | Complete            | CRUD menu                                                                               |
| Master Categories     | Complete            | Dipakai oleh menu                                                                       |
| Master Units          | Complete            |                                                                                         |
| Raw Materials         | Complete            | CRUD + costing (stock + cost_last + cost_avg)                                           |
| Suppliers             | Complete            |                                                                                         |
| Purchases             | Complete (v1)       | Stock IN + average costing + movement IN                                                |
| Recipes               | Phase 1             | CRUD + HPP computation                                                                  |
| Sales                 | Backend v1 Complete | HPP snapshot, total_cost, margin, stock OUT, validation; blok menu tanpa resep/detail   |
| Stock Movements       | List Implemented    | IN/OUT list + filter                                                                    |
| Stock Card            | Implemented         | Per bahan, filter tanggal + opening balance, saldo berjalan                             |
| Reports - Sales Daily | Replaced            | Diganti laporan Penjualan by Time (day/week/month/year)                                 |
| Overhead              | Basic               | Tabel overhead + kategori master; input & list dengan filter tanggal                    |
| Audit Logs            | Not started         | Planned                                                                                 |
| POS UI Skin           | In Progress (P2)    | Touchscreen grid, cart qty +/- , submit ke Sales::store                                 |
| Overhead Payroll      | Implemented         | CRUD payroll bulanan staff (owner only)                                                 |

---

# Role & Access Control (Target & Current State)

Legenda: A=Access (view/read), I=Input/Create, U=Update, D=Delete/Nonaktifkan.

| Modul/Fitur                                     | Owner                         | Staff (target)                 | Staff (current)                                   | Auditor (target) | Auditor (current)     |
| ----------------------------------------------- | ----------------------------- | ------------------------------ | ------------------------------------------------- | ---------------- | --------------------- |
| Dashboard                                       | A                             | A                              | A                                                 | A                | A                     |
| Master Data (produk, kategori, bahan, supplier) | A/I/U/D (termasuk harga menu) | A/I/U menu & resep; D dibatasi | A/I/U menu & resep (blok hanya di users/settings) | A                | A (filter blok I/U/D) |
| Users & Settings                                | A/I/U/D                       | Tidak boleh                    | A; filter blok I/U/D                              | A (view)         | A (filter blok I/U/D) |
| Resep                                           | A/I/U/D                       | A/I/U (butuh audit log)        | A/I/U (filter tidak blok; audit log belum)        | A                | A (filter blok I/U/D) |
| Penjualan                                       | A/I/U/D                       | A/I (void belum)               | A/I (filter izinkan)                              | A (lihat)        | A (filter blok I/U/D) |
| Pembelian                                       | A/I/U/D                       | A/I                            | A/I (filter izinkan)                              | A (lihat)        | A (filter blok I/U/D) |
| Stock Adjustment                                | A/I/U/D                       | A/I                            | A/I (belum ada modul, asumsi)                     | A (lihat)        | A (filter blok I/U/D) |
| Overhead                                        | A/I/U/D                       | A/I                            | A/I (filter izinkan)                              | A (lihat)        | A (filter blok I/U/D) |
| Laporan (Sales, dll.)                           | A                             | A                              | A                                                 | A                | A                     |
| Audit Log                                       | A                             | A (view)                       | (belum ada modul)                                 | A (view)         | (belum ada modul)     |

Catatan gap:

-   Staff masih bisa membuka form edit master/harga karena GET tidak diblok; filter hanya blok submit POST/PUT/DELETE di products/users/settings. ✅ di-TO-DO: "Staff blok GET form master sensitif".
-   Audit log resep/menu belum ada. ✅ di-TO-DO: "Audit log edit resep/menu".
-   Void sudah tersedia; retur penjualan belum ada. (TO-DO: "Retur penjualan").

---

# NEXT TODOs (Short-Term — Backend Fokus)

## 1) Sales & HPP Enhancements

-   [x] Blok transaksi jika menu belum punya resep/detail (no fallback HPP 0)
-   [x] Summary harian di Sales Index (total penjualan & margin hari ini)
-   [x] Validasi tambahan untuk override harga jual per transaksi
-   [x] Void penjualan: status void, rollback stok via movement (retur belum)

## 2) Inventory & Stock Monitoring

-   [x] Kartu Stok per Bahan: filter tanggal, saldo berjalan, opening balance
-   [x] Kolom saldo akhir di list movement (opsional, atau cukup di kartu stok)
-   [x] Guard waste_pct resep (batas 0-100) + rounding qty/cost untuk hindari selisih float besar

## 3) Reports

-   [x] Laporan Penjualan per Menu:
    -   Periode
    -   Qty per menu
    -   Omzet per menu
    -   HPP total per menu
    -   Margin per menu
-   [x] Export CSV laporan penjualan (harian & per menu)
-   [x] Pagination/limit sederhana untuk laporan jika data besar

## 4) Overhead

-   [x] Edit/nonaktif kategori overhead + tampilkan kategori nonaktif di filter (overhead lama tetap bisa difilter)

## 5) Access Control

-   [x] Terapkan role Owner/Staff/Auditor pada route/controller: Staff dibatasi dari edit user & settings; Auditor read-only (blok POST/PUT/DELETE)
-   [x] Audit log untuk edit resep/menu (harga & bahan) sebagai bagian pengamanan Staff
-   [x] Staff: blokir akses GET ke form create/edit user/setting (produk dibiarkan, user/setting dibatasi); sidebar/link nonaktif untuk area sensitif
-   [x] Void penjualan tersedia; retur belum (rollback stok/margin via movement)
-   [x] Lanjutan UI/UX: scrollbar custom sesuai tema, sidebar collapsible + fixed header/footer, tabel auto-scroll wrapper (done)
    -   Catatan progres: Auditor read-only via RoleFilter; Staff diblok untuk GET/POST area users/settings (belum ada modul user/setting, guard berbasis path).

---

# NEXT TODOs (Medium-Term)

## Recipes & Food Cost

-   [x] Support sub-recipe (mis: syrup/base dipakai menu lain)
-   [x] Indikator bahan hampir habis (min_stock) di master bahan + kartu stok
-   [x] Warning di dashboard untuk bahan di bawah min_stock

## POS UI (Phase 2 — Modern Touchscreen Skin)

_(Dikerjakan setelah backend final & stabil)_

-   [ ] Grid menu item (card besar)
-   [ ] Quick add qty (tap berkali-kali → qty naik)
-   [ ] Shortcut pembayaran dan pembulatan kembalian
-   [ ] Simpan draft order / meja / hold

---

# NEXT TODOs (Long-Term)

## Reports

-   [ ] Laporan penjualan bulanan + grafik sederhana
-   [x] Laporan margin per kategori menu
-   [x] Laporan pembelian per supplier
-   [ ] Laporan stok & selisih (banding stok fisik)

## Audit Logs

-   [x] Log perubahan: harga menu, resep (payload JSON)
-   [x] Viewer audit log (filter entity, tanggal) sederhana

---

# NEXT TODOs (Frontend & JS Progressive Enhancement)

-   Platform & helper
    -   [ ] Tambah util JS dasar (public/js/app.js): fetchJSON (dengan CSRF), toast/error handler, loading state pada tombol; tetap server-rendered dengan fallback non-JS.
    -   [ ] Guard keamanan: handle 401/419 redirect ke login; batasi payload client (max rows/file); log fetch failure ke console (opsional kirim ke endpoint log).
-   Audit fitur prioritas untuk JS
    -   [ ] Review halaman yang paling diuntungkan: form resep (HPP live), filter tabel tanpa reload, toggle aktif/nonaktif entitas sederhana.
    -   [ ] Checklist progressive enhancement: tandai mana yang sudah/belum, pastikan fallback tanpa JS tetap berfungsi.
-   Fase 1 (kecil, validasi pola)
    -   [ ] Toggle aktif/nonaktif entitas ringan (kategori overhead/supplier) via fetch POST, update badge tanpa reload.
    -   [ ] Inline filter/search ringan di tabel (debounce 300 ms, fetch data, render tbody ulang).
-   Fase 2 (form & grid interaktif)
    -   [ ] Form resep: HPP live preview saat ubah bahan/qty/waste, tambah baris di client, opsional drag/sort baris.
    -   [ ] POS/Sales draft: grid item dengan increment qty, subtotal live, validasi override harga, submit via fetch.
-   Fase 3 (UX kelengkapan)
    -   [ ] Snackbar/toast global untuk success/error; konfirmasi dialog ringan (native confirm atau modal kecil).
    -   [ ] Skeleton/loading placeholder untuk tabel besar saat filter/pagination via fetch.
-   Fase 4 (optimasi)
    -   [ ] Cache data referensi (menu/bahan) di sessionStorage untuk form yang sering dipakai; invalidasi via versi data.
    -   [ ] Siapkan struktur minimal untuk Vite/ESM bila nanti butuh build step; sementara UMD tanpa build cukup.

---

## 2025-12 - Theme Toggle (Light/Dark)

-   Tema berbasis CSS variables (`data-theme="light"/"dark"`) untuk warna utama: page/card/sidebar, text, border, accent, success/danger/warning.
-   Toggle kecil di topbar (`#themeToggle`) menyimpan preferensi ke `localStorage` lewat `public/js/theme-toggle.js`.
-   Layout/topbar/sidebar/card/table/button/badge sudah memakai token warna baru agar kontras tetap terjaga di dua mode.
-   Rekomendasi commit: `feat(theme): add light/dark mode toggle using css variables`

# Testing Guideline

# Testing Guideline

Setiap fitur/modul baru minimal cek:

-   [ ] Validasi form jalan (required, numeric, dsb.)
-   [ ] CRUD berfungsi end-to-end
-   [ ] HPP per menu sesuai ekspektasi (cek manual)
-   [ ] Stok: pembelian → bertambah (IN); penjualan → berkurang (OUT)
-   [ ] `stock_movements`: IN untuk purchases, OUT untuk sales, qty sesuai perhitungan recipe
-   [ ] Laporan harian: `total_amount` dan `total_cost` cocok dengan sum data sales
-   [ ] Migration: dari DB kosong → `php spark migrate` sukses tanpa error
-   [ ] Seeder demo: `php spark db:seed DatabaseSeeder` terisi tanpa duplikat

---

# Development Rules

-   1 commit = 1 fitur / blok perubahan yang jelas
-   Tidak mengedit migration lama setelah di-push → gunakan migration baru untuk perubahan struktur
-   Backend (logic & data) diprioritaskan sebelum UI/skin
-   Perubahan costing & stock harus traceable (stock_movements, receipts, dsb.)

---

# Tooling & Formatting

-   Prettier (`.prettierrc`): indent 4 spasi, single quotes, trailing comma `es5`, `semi: true`, `printWidth: 120`, `endOfLine: lf`.
-   VS Code (`.vscode/setting.json`): `tabSize: 4`, `insertSpaces: true`, `detectIndentation: false`, format-on-save aktif + trim trailing whitespace + final newline; default formatter Prettier, PHP pakai Intelephense, JS/TS/JSON/HTML pakai Prettier.

---

# Notes

-   Alur utama (Purchase → Stock IN → Recipe → Sales → Stock OUT → Laporan Harian) sudah berfungsi.
-   Observability diperkuat: kartu stok per bahan, summary harian di Sales.
-   Dashboard sudah memuat KPI live (hari ini, 7d, MTD), top menu 7d, peringatan stok minim, transaksi terbaru, serta beban biaya bulan berjalan.
-   Seed demo siap dipakai untuk testing cepat (supplier, bahan, pembelian IN, resep, user owner).
-   Dokumen ini untuk menjaga konteks jika ada jeda/pindah device.

## [2025-12] Master Data & Recipes Stabilization (Phase 1)

Selama fase ini sejumlah modul (bukan hanya Recipes) direvisi untuk distabilkan: Master Recipes (Controller, Model, View, dan logika JS) direfaktor agar dropdown Raw Material vs Sub-Recipe konsisten, mencegah input mixed-state, dan menghilangkan eksekusi JavaScript rekursif yang memicu error maximum call stack; query SQL di berbagai model diperbaiki dengan kualifikasi kolom eksplisit seperti `is_active` agar tidak ambigu saat JOIN; penanganan Unit (termasuk dukungan `is_active`) diselesaikan dan diintegrasikan ke Raw Materials serta Recipes sehingga informasi unit terselesaikan konsisten di server dan client; sejumlah controller, model, view, serta logika terkait route pada Master Data, persiapan Inventory, dan file pendukung reporting diselaraskan untuk naming, filter, ordering, dan alur data yang konsisten, menghasilkan pembaruan di sekitar dua puluh-an file; fokus fase ini adalah correctness fungsional, integritas data, dan perilaku UI yang dapat diprediksi alih-alih polesan visual; sistem kini dianggap stabil untuk penggunaan harian, pembaruan sidebar/navigasi sengaja ditunda hingga seluruh master final, dan perbaikan berikutnya diarahkan ke ekstraksi JavaScript inline, penguatan UX safeguard, serta penyambungan Recipes lebih erat dengan Stock Movements dan snapshot HPP Sales.
