# PROJECT_WORKFLOW_EVIDENCE.md

**Generated:** 2026-01-19  
**Purpose:** Evidence backing claims in `PROJECT_WORKFLOW.md`

---

## A. Repository Root

```
git rev-parse --show-toplevel
→ C:/ci4-local
```

**Top-level contents (verified):**

```
.git, .github, .vscode, app, branding_slides, docs, node_modules, public,
screenshots, scripts, tests, vendor, writable
.env, .env.example, .gitignore, .prettierrc, .stylelintignore, .stylelintrc.json,
B2 - Global UI Elements.md, composer.json, composer.lock, DEPLOYMENT_WORKFLOW.md,
DEV_NOTES.md, LICENSE, package.json, phpunit.xml.dist, README.md, SECURITY.md,
spark, UI_BASELINE_V1.md
```

---

## B. App Directory Structure

```
app/Config, app/Controllers, app/Database, app/Filters, app/Helpers,
app/Language, app/Libraries, app/Models, app/Services, app/ThirdParty, app/Views
app/Controllers/App, app/Controllers/Auth, app/Controllers/Inventory,
app/Controllers/Master, app/Controllers/Pos, app/Controllers/Reports,
app/Controllers/Transactions
app/Database/Migrations, app/Database/Seeds
app/Views/app, app/Views/audit_logs, app/Views/auth, app/Views/errors,
app/Views/guides, app/Views/inventory, app/Views/layouts, app/Views/master,
app/Views/overheads, app/Views/overhead_categories, app/Views/partials,
app/Views/pos, app/Views/reports, app/Views/transactions, app/Views/users
```

---

## C. Public Directory Structure

```
public/assets, public/css, public/images, public/js
public/assets/css, public/assets/js
public/assets/css/app
public/assets/js/datatables, public/assets/js/qz-tray
public/css/00-theme, public/css/10-tokens, public/css/20-base, public/css/30-layout,
public/css/40-sections, public/css/50-components, public/css/60-pages, public/css/99-legacy
```

---

## D. CSS Files (Verified Paths)

```
public\css\00-theme\theme-temurasa.css
public\css\10-tokens\ui-tokens.css
public\css\20-base\ui-baseline.css
public\css\30-layout\footer.css
public\css\30-layout\layout.css
public\css\30-layout\shell.css
public\css\30-layout\topbar.css
public\css\40-sections\sidebar-nav.css
public\css\50-components\alerts.css
public\css\50-components\badges.css
public\css\50-components\buttons.css
public\css\50-components\cards.css
public\css\50-components\forms.css
public\css\50-components\modal.css
public\css\50-components\tables.css
public\css\60-pages\auth.css
public\css\60-pages\branding.css
public\css\60-pages\dashboard.css
public\css\60-pages\pos-touch.css
public\css\99-legacy\temurasa-reveal.css
public\css\main.css
```

**CSS Loading (verified in `app/Views/layouts/partials/head.php`):**

- Main layout loads: `base_url('css/main.css')`
- `public/css/main.css` uses `@import` to load layered CSS

---

## E. JS Files (Verified Paths)

```
public\assets\js\datatables\menu.js
public\assets\js\datatables\raw_materials.js
public\assets\js\datatables\stock_movements.js
public\assets\js\qz-tray\qz-tray.js
public\assets\js\qz-tray\qz-tray-trial.js
public\js\app.js
public\js\branding.js
public\js\sidebar-collapse-state.js
public\js\sidebar-toggle.js
public\js\theme-toggle.js
public\js\tr-daterange.js
```

---

## F. Branch Reality

```
git branch -vv (local branches):
  backup/deploy-nas-before-8731366  → local only
  backup/pre-purge-2025-12-25       → local, ahead/behind origin/master
* deploy/nas                        → tracks origin/deploy/nas
  master                            → tracks origin/master (ahead 33)

git remote -v:
  origin  https://github.com/sotyarama/ci4-local.git (fetch/push)

git branch -a | Select-String "deploy|master|backup":
  remotes/origin/HEAD → origin/master
  remotes/origin/deploy/nas
  remotes/origin/master
```

**Verified branches:**

- `master` (local + remote)
- `deploy/nas` (local + remote)
- `backup/pre-purge-2025-12-25` (local only; documented in DEV_NOTES.md)

---

## G. Recent Commits

```
git log --oneline -10:
b216515 docs: add inline script guards and JS hook registry
3f11995 docs(audit): add CSS/JS audit report and refine safe-to-proceed wording
9a80b25 chore(ui): freeze tr-* migration (P0–P2 complete, audit validated)
b4bacd4 ui(tr): migrate master/menu_options_index.php to tr-* classes
22f372f ui(tr): migrate master/menu_categories_form.php to tr-* classes
557f4ed ui(tr): migrate master/units_form.php to tr-* classes
286ac95 ui(tr): migrate master/suppliers_form.php to tr-* classes
92a95ac ui(tr): migrate master/raw_materials_form.php to tr-* classes
bf8653d ui(tr): migrate master/products_form.php to tr-* classes
6251213 ui(tr): migrate master/customers_form.php to tr-* classes
```

---

## H. Migration File Naming Convention

**Earliest migrations:**

```
2025-12-06-065542_CreateRolesTable.php
2025-12-06-065542_CreateUsersTable.php
2025-12-06-065543_CreateMenuCategoriesTable.php
...
```

**Latest migrations:**

```
2025-12-27-114000_AddItemNoteToSaleItems.php
2025-12-27-113000_AddKitchenStatusToSales.php
2025-12-27-112000_AddPaymentFieldsToSales.php
...
```

**Observed pattern:** `YYYY-MM-DD-HHMMSS_PascalCaseDescription.php`

---

## I. CI/CD Workflows (Verified)

```
.github/workflows/:
  deploy-reminder.yml     (642 bytes)
  env-format-check.yml    (483 bytes)
  phpunit.yml             (1016 bytes)
  secret-scan.yml         (423 bytes)
```

---

## J. Markdown Files Inventory (20 total)

```
B2 - Global UI Elements.md
DEPLOYMENT_WORKFLOW.md
DEV_NOTES.md
README.md
SECURITY.md
UI_BASELINE_V1.md
docs/ENV_FORMAT_CHECK.md
docs/PROJECT_WORKFLOW.md
docs/UI_CSS_JS_AUDIT_2026-01-19.md
docs/UI_MIGRATION_AUDIT_2026-01-19.md
docs/js-hooks.md
docs/ui-migration.md
docs/product/dashboard/01_dashboard-freeze-spec.md
docs/product/dashboard/02_transition-checklist.md
docs/product/dashboard/03_edge-case-behavior.md
docs/product/dashboard/04_ui-guardrail-rules.md
docs/product/dashboard/05_component-responsibility-map.md
docs/product/dashboard/06_layout-skeleton-notes.md
scripts/README_playwright.md
tests/README.md
```

---

## K. Historical Claims Verification

**"BFG purge completed 2025-12-25":**

- DEV_NOTES.md references this date multiple times (lines 5, 11, 14, 15, 25, 33-41, etc.)
- Local branch `pre-purge-2025-12-25` and `backup/pre-purge-2025-12-25` exist
- This claim is supported by documentation and branch evidence

---

## L. Verification Commands (for future audits)

```powershell
# Verify CSS file locations
Get-ChildItem -Path public -Recurse -Filter "*.css" | Select-Object FullName

# Verify JS file locations
Get-ChildItem -Path public -Recurse -Filter "*.js" | Select-Object FullName

# Verify branches
git branch -a

# Verify migration naming
Get-ChildItem -Path "app/Database/Migrations" -Filter "*.php" | Select-Object Name

# Verify workflows
Get-ChildItem -Path ".github/workflows" -Filter "*.yml" | Select-Object Name
```

---

_Evidence collected 2026-01-19_
