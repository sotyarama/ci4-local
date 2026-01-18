# UI Migration Inventory

**Generated:** 2026-01-11  
**Purpose:** Track UI component migration to standardized tr-\* pattern

---

## Changed (Working Tree)

Files that have been modified or are new in the current working tree.

### app/Views/

#### master/

- [ ] app/Views/master/recipes_form.php — Modified
- [ ] app/Views/master/

#### transactions/

- [ ] app/Views/transactions/sales_form.php — Modified

---

## All Views & JS (Scope)

Complete inventory of all view templates and JavaScript files for migration planning.

### app/Views/

#### Root Level

- [ ] app/Views/brand_presentation.php — Existing
- [ ] app/Views/dashboard.php — Existing
- [ ] app/Views/welcome_message.php — Existing

#### app/

- [ ] app/Views/app/playground.php — Existing

#### audit_logs/

- [ ] app/Views/audit_logs/audit_logs_index.php — Existing

#### auth/

- [ ] app/Views/auth/forgot.php — Existing
- [ ] app/Views/auth/login.php — Existing
- [ ] app/Views/auth/reset.php — Existing

#### errors/cli/

- [ ] app/Views/errors/cli/error_404.php — Existing
- [ ] app/Views/errors/cli/error_exception.php — Existing
- [ ] app/Views/errors/cli/production.php — Existing

#### errors/html/

- [ ] app/Views/errors/html/error_400.php — Existing
- [ ] app/Views/errors/html/error_404.php — Existing
- [ ] app/Views/errors/html/error_exception.php — Existing
- [ ] app/Views/errors/html/production.php — Existing

#### guides/

- [ ] app/Views/guides/branding.php — Existing
- [ ] app/Views/guides/branding_content.php — Existing
- [ ] app/Views/guides/how_to_use.php — Existing

#### inventory/

- [ ] app/Views/inventory/stock_adjustments.php — Existing
- [ ] app/Views/inventory/stock_card.php — Existing
- [ ] app/Views/inventory/stock_movements_index.php — Existing
- [ ] app/Views/inventory/stock_opname.php — Existing

#### layouts/

- [ ] app/Views/layouts/app_shell.php — Existing
- [ ] app/Views/layouts/auth.php — Existing
- [ ] app/Views/layouts/main.php — Existing
- [ ] app/Views/layouts/print.php — Existing

#### layouts/partials/

- [ ] app/Views/layouts/partials/flash_toast.php — Existing
- [ ] app/Views/layouts/partials/footer.php — Existing
- [ ] app/Views/layouts/partials/head.php — Existing
- [ ] app/Views/layouts/partials/scripts.php — Existing
- [ ] app/Views/layouts/partials/sidebar.php — Existing
- [ ] app/Views/layouts/partials/topbar.php — Existing

#### master/

- [ ] app/Views/master/customers_form.php — Existing
- [ ] app/Views/master/customers_index.php — Existing
- [ ] app/Views/master/menu_categories_form.php — Existing
- [ ] app/Views/master/menu_categories_index.php — Existing
- [ ] app/Views/master/menu_options_index.php — Existing
- [ ] app/Views/master/products_form.php — Existing
- [ ] app/Views/master/products_index.php — Existing
- [ ] app/Views/master/raw_materials_form.php — Existing
- [ ] app/Views/master/raw_materials_index.php — Existing
- [ ] app/Views/master/recipes_form.php — Existing
- [ ] app/Views/master/recipes_index.php — Existing
- [ ] app/Views/master/suppliers_form.php — Existing
- [ ] app/Views/master/suppliers_index.php — Existing
- [ ] app/Views/master/units_form.php — Existing
- [ ] app/Views/master/units_index.php — Existing

#### overheads/

- [ ] app/Views/overheads/overheads_form.php — Existing
- [ ] app/Views/overheads/overheads_index.php — Existing
- [ ] app/Views/overheads/payroll.php — Existing
- [ ] app/Views/overheads/payroll_form.php — Existing

#### overhead_categories/

- [ ] app/Views/overhead_categories/overhead_categories_form.php — Existing
- [ ] app/Views/overhead_categories/overhead_categories_index.php — Existing

#### partials/

- [ ] app/Views/partials/date_range_picker.php — Existing

#### pos/

- [ ] app/Views/pos/touchscreen.php — Existing

#### reports/

- [ ] app/Views/reports/purchases_material.php — Existing
- [ ] app/Views/reports/purchases_supplier.php — Existing
- [ ] app/Views/reports/sales_category.php — Existing
- [ ] app/Views/reports/sales_customer.php — Existing
- [ ] app/Views/reports/sales_customer_detail.php — Existing
- [ ] app/Views/reports/sales_menu.php — Existing
- [ ] app/Views/reports/sales_time.php — Existing
- [ ] app/Views/reports/stock_variance.php — Existing

#### reports/pdf/

- [ ] app/Views/reports/pdf/layout.php — Existing
- [ ] app/Views/reports/pdf/sales_category.php — Existing
- [ ] app/Views/reports/pdf/sales_customer.php — Existing
- [ ] app/Views/reports/pdf/sales_menu.php — Existing
- [ ] app/Views/reports/pdf/sales_time.php — Existing

#### transactions/

- [ ] app/Views/transactions/kitchen_queue.php — Existing
- [ ] app/Views/transactions/kitchen_ticket.php — Existing
- [ ] app/Views/transactions/purchases_detail.php — Existing
- [ ] app/Views/transactions/purchases_form.php — Existing
- [ ] app/Views/transactions/purchases_index.php — Existing
- [ ] app/Views/transactions/sales_detail.php — Existing
- [ ] app/Views/transactions/sales_form.php — Existing
- [ ] app/Views/transactions/sales_index.php — Existing

#### users/

- [ ] app/Views/users/users_form.php — Existing
- [ ] app/Views/users/users_index.php — Existing

### public/js/

- [ ] public/js/app.js — Existing
- [ ] public/js/branding.js — Existing
- [ ] public/js/sidebar-collapse-state.js — Existing
- [ ] public/js/sidebar-toggle.js — Existing
- [ ] public/js/theme-toggle.js — Existing
- [ ] public/js/tr-daterange.js — Existing

### public/assets/js/

#### datatables/

- [ ] public/assets/js/datatables/menu.js — Existing
- [ ] public/assets/js/datatables/raw_materials.js — Existing
- [ ] public/assets/js/datatables/stock_movements.js — Existing

#### qz-tray/

- [ ] public/assets/js/qz-tray/qz-tray-trial.js — Existing
- [ ] public/assets/js/qz-tray/qz-tray.js — Existing

---

## Commands Used

### PowerShell (Windows)

```powershell
# Get git status
git status --porcelain

# List all PHP view files
Get-ChildItem -Path "app/Views" -Recurse -Filter "*.php" | Select-Object -ExpandProperty FullName | ForEach-Object { $_.Replace((Get-Location).Path + '\', '').Replace('\', '/') }

# List all JS files in public/js
Get-ChildItem -Path "public/js" -Recurse -Filter "*.js" -ErrorAction SilentlyContinue | Select-Object -ExpandProperty FullName | ForEach-Object { $_.Replace((Get-Location).Path + '\', '').Replace('\', '/') }

# List all JS files in public/assets/js
Get-ChildItem -Path "public/assets/js" -Recurse -Filter "*.js" -ErrorAction SilentlyContinue | Select-Object -ExpandProperty FullName | ForEach-Object { $_.Replace((Get-Location).Path + '\', '').Replace('\', '/') }

# Check for assets/js directory
Test-Path "assets/js"
```

### Bash (Linux/Mac Alternative)

```bash
# Get git status
git status --porcelain

# List all PHP view files
find app/Views -type f -name "*.php" | sed 's|^./||'

# List all JS files
find public/js -type f -name "*.js" 2>/dev/null | sed 's|^./||'
find public/assets/js -type f -name "*.js" 2>/dev/null | sed 's|^./||'
find assets/js -type f -name "*.js" 2>/dev/null | sed 's|^./||'
```

---

## Summary

- **Total View Files:** 78
- **Total JS Files:** 11
- **Changed Files:** 2 views (recipes_form.php, sales_form.php)
- **Migration Target:** All files to use tr-\* CSS classes and standardized patterns

## Notes

- CSS files were modified but not included in this inventory (focused on Views & JS only)
- No files in `assets/js/` directory (does not exist)
- Changed files are actively being migrated to the new UI pattern

# UI Migration Inventory

**Generated:** 2026-01-11  
**Purpose:** Track UI component migration to standardized tr-\* pattern

---

## Changed (Working Tree)

Files that have been modified or are new in the current working tree.

### app/Views/

#### master/

- [ ] app/Views/master/recipes_form.php — Modified
- [ ] app/Views/master/

#### transactions/

- [ ] app/Views/transactions/sales_form.php — Modified

---

## All Views & JS (Scope)

Complete inventory of all view templates and JavaScript files for migration planning.

### app/Views/

#### Root Level

- [ ] app/Views/brand_presentation.php — Existing
- [ ] app/Views/dashboard.php — Existing
- [ ] app/Views/welcome_message.php — Existing

#### app/

- [ ] app/Views/app/playground.php — Existing

#### audit_logs/

- [ ] app/Views/audit_logs/audit_logs_index.php — Existing

#### auth/

- [ ] app/Views/auth/forgot.php — Existing
- [ ] app/Views/auth/login.php — Existing
- [ ] app/Views/auth/reset.php — Existing

#### errors/cli/

- [ ] app/Views/errors/cli/error_404.php — Existing
- [ ] app/Views/errors/cli/error_exception.php — Existing
- [ ] app/Views/errors/cli/production.php — Existing

#### errors/html/

- [ ] app/Views/errors/html/error_400.php — Existing
- [ ] app/Views/errors/html/error_404.php — Existing
- [ ] app/Views/errors/html/error_exception.php — Existing
- [ ] app/Views/errors/html/production.php — Existing

#### guides/

- [ ] app/Views/guides/branding.php — Existing
- [ ] app/Views/guides/branding_content.php — Existing
- [ ] app/Views/guides/how_to_use.php — Existing

#### inventory/

- [ ] app/Views/inventory/stock_adjustments.php — Existing
- [ ] app/Views/inventory/stock_card.php — Existing
- [ ] app/Views/inventory/stock_movements_index.php — Existing
- [ ] app/Views/inventory/stock_opname.php — Existing

#### layouts/

- [ ] app/Views/layouts/app_shell.php — Existing
- [ ] app/Views/layouts/auth.php — Existing
- [ ] app/Views/layouts/main.php — Existing
- [ ] app/Views/layouts/print.php — Existing

#### layouts/partials/

- [ ] app/Views/layouts/partials/flash_toast.php — Existing
- [ ] app/Views/layouts/partials/footer.php — Existing
- [ ] app/Views/layouts/partials/head.php — Existing
- [ ] app/Views/layouts/partials/scripts.php — Existing
- [ ] app/Views/layouts/partials/sidebar.php — Existing
- [ ] app/Views/layouts/partials/topbar.php — Existing

#### master/

- [ ] app/Views/master/customers_form.php — Existing
- [ ] app/Views/master/customers_index.php — Existing
- [ ] app/Views/master/menu_categories_form.php — Existing
- [ ] app/Views/master/menu_categories_index.php — Existing
- [ ] app/Views/master/menu_options_index.php — Existing
- [ ] app/Views/master/products_form.php — Existing
- [ ] app/Views/master/products_index.php — Existing
- [ ] app/Views/master/raw_materials_form.php — Existing
- [ ] app/Views/master/raw_materials_index.php — Existing
- [ ] app/Views/master/recipes_form.php — Existing
- [ ] app/Views/master/recipes_index.php — Existing
- [ ] app/Views/master/suppliers_form.php — Existing
- [ ] app/Views/master/suppliers_index.php — Existing
- [ ] app/Views/master/units_form.php — Existing
- [ ] app/Views/master/units_index.php — Existing

#### overheads/

- [ ] app/Views/overheads/overheads_form.php — Existing
- [ ] app/Views/overheads/overheads_index.php — Existing
- [ ] app/Views/overheads/payroll.php — Existing
- [ ] app/Views/overheads/payroll_form.php — Existing

#### overhead_categories/

- [ ] app/Views/overhead_categories/overhead_categories_form.php — Existing
- [ ] app/Views/overhead_categories/overhead_categories_index.php — Existing

#### partials/

- [ ] app/Views/partials/date_range_picker.php — Existing

#### pos/

- [ ] app/Views/pos/touchscreen.php — Existing

#### reports/

- [ ] app/Views/reports/purchases_material.php — Existing
- [ ] app/Views/reports/purchases_supplier.php — Existing
- [ ] app/Views/reports/sales_category.php — Existing
- [ ] app/Views/reports/sales_customer.php — Existing
- [ ] app/Views/reports/sales_customer_detail.php — Existing
- [ ] app/Views/reports/sales_menu.php — Existing
- [ ] app/Views/reports/sales_time.php — Existing
- [ ] app/Views/reports/stock_variance.php — Existing

#### reports/pdf/

- [ ] app/Views/reports/pdf/layout.php — Existing
- [ ] app/Views/reports/pdf/sales_category.php — Existing
- [ ] app/Views/reports/pdf/sales_customer.php — Existing
- [ ] app/Views/reports/pdf/sales_menu.php — Existing
- [ ] app/Views/reports/pdf/sales_time.php — Existing

#### transactions/

- [ ] app/Views/transactions/kitchen_queue.php — Existing
- [ ] app/Views/transactions/kitchen_ticket.php — Existing
- [ ] app/Views/transactions/purchases_detail.php — Existing
- [ ] app/Views/transactions/purchases_form.php — Existing
- [ ] app/Views/transactions/purchases_index.php — Existing
- [ ] app/Views/transactions/sales_detail.php — Existing
- [ ] app/Views/transactions/sales_form.php — Existing
- [ ] app/Views/transactions/sales_index.php — Existing

#### users/

- [ ] app/Views/users/users_form.php — Existing
- [ ] app/Views/users/users_index.php — Existing

### public/js/

- [ ] public/js/app.js — Existing
- [ ] public/js/branding.js — Existing
- [ ] public/js/sidebar-collapse-state.js — Existing
- [ ] public/js/sidebar-toggle.js — Existing
- [ ] public/js/theme-toggle.js — Existing
- [ ] public/js/tr-daterange.js — Existing

### public/assets/js/

#### datatables/

- [ ] public/assets/js/datatables/menu.js — Existing
- [ ] public/assets/js/datatables/raw_materials.js — Existing
- [ ] public/assets/js/datatables/stock_movements.js — Existing

#### qz-tray/

- [ ] public/assets/js/qz-tray/qz-tray-trial.js — Existing
- [ ] public/assets/js/qz-tray/qz-tray.js — Existing

---

## Commands Used

### PowerShell (Windows)

```powershell
# Get git status
git status --porcelain

# List all PHP view files
Get-ChildItem -Path "app/Views" -Recurse -Filter "*.php" | Select-Object -ExpandProperty FullName | ForEach-Object { $_.Replace((Get-Location).Path + '\', '').Replace('\', '/') }

# List all JS files in public/js
Get-ChildItem -Path "public/js" -Recurse -Filter "*.js" -ErrorAction SilentlyContinue | Select-Object -ExpandProperty FullName | ForEach-Object { $_.Replace((Get-Location).Path + '\', '').Replace('\', '/') }

# List all JS files in public/assets/js
Get-ChildItem -Path "public/assets/js" -Recurse -Filter "*.js" -ErrorAction SilentlyContinue | Select-Object -ExpandProperty FullName | ForEach-Object { $_.Replace((Get-Location).Path + '\', '').Replace('\', '/') }

# Check for assets/js directory
Test-Path "assets/js"
```

### Bash (Linux/Mac Alternative)

```bash
# Get git status
git status --porcelain

# List all PHP view files
find app/Views -type f -name "*.php" | sed 's|^./||'

# List all JS files
find public/js -type f -name "*.js" 2>/dev/null | sed 's|^./||'
find public/assets/js -type f -name "*.js" 2>/dev/null | sed 's|^./||'
find assets/js -type f -name "*.js" 2>/dev/null | sed 's|^./||'
```

---

## Summary

- **Total View Files:** 78
- **Total JS Files:** 11
- **Changed Files:** 2 views (recipes_form.php, sales_form.php)
- **Migration Target:** All files to use tr-\* CSS classes and standardized patterns

## Notes

- CSS files were modified but not included in this inventory (focused on Views & JS only)
- No files in `assets/js/` directory (does not exist)
- Changed files are actively being migrated to the new UI pattern

---

## Migration Rules (Authoritative)

These rules MUST be followed for all migration tasks.

### General

- The target UI system is **tr-\* classes only**
- `.btn`, `.btn-*`, `.table`, `.badge`, `.pill` are legacy and must be removed gradually
- Dual-class is allowed temporarily **only if JS depends on it**

### JS Safety Rules

- DO NOT rename or remove JS hook classes, including but not limited to:
    - `.btn-remove-row`
    - `.item-menu`, `.item-qty`, `.item-price`
    - `.customer-item`
- JS selectors must continue to work without modification

### Scope Control

- Work on **ONE file per task**
- Do NOT modify unrelated files
- Do NOT refactor JS logic unless explicitly instructed

### Styling Rules

- Prefer class-based styling (`tr-btn`, `tr-control`, `tr-table`)
- Remove inline styles when safe
- Do not introduce new legacy-style classes

---

## Migration Priority & Execution Order

### P0 — Critical (Dynamic / JS-heavy)

These files must be migrated first.

- [x] app/Views/transactions/sales_form.php
- [x] app/Views/master/recipes_form.php
- [x] app/Views/pos/touchscreen.php

### P1 — High Usage (Transactions)

- [x] app/Views/transactions/sales_index.php
- [x] app/Views/transactions/sales_detail.php
- [x] app/Views/transactions/purchases_form.php
- [x] app/Views/transactions/purchases_index.php
- [x] app/Views/transactions/purchases_detail.php
- [x] app/Views/transactions/kitchen_queue.php
- [x] app/Views/transactions/kitchen_ticket.php

### P2 — Master Data (CRUD)

- [x] app/Views/master/customers_form.php
- [x] app/Views/master/products_form.php
- [x] app/Views/master/raw_materials_form.php
- [x] app/Views/master/suppliers_form.php
- [x] app/Views/master/units_form.php
- [ ] app/Views/master/menu_categories_form.php
- [ ] app/Views/master/menu_options_index.php

### P3 — Inventory

- [ ] app/Views/inventory/stock_adjustments.php
- [ ] app/Views/inventory/stock_card.php
- [ ] app/Views/inventory/stock_movements_index.php
- [ ] app/Views/inventory/stock_opname.php

### P4 — Reports & PDF

- [ ] app/Views/reports/\*
- [ ] app/Views/reports/pdf/\*

### P5 — Layouts, Auth, Misc

- [ ] app/Views/layouts/\*
- [ ] app/Views/auth/\*
- [ ] app/Views/users/\*

---

## Definition of Done (Per File)

A file is considered **DONE** when:

- [ ] No `.btn` or `.btn-*` classes remain
- [ ] All buttons use `.tr-btn` variants
- [ ] Inputs/selects/textareas use `.tr-control`
- [ ] Tables use `.tr-table` (dual-class allowed temporarily)
- [ ] All JS behaviors still work correctly
- [ ] No new legacy classes introduced
