# CSS & JavaScript Quality Audit Report

**Audit Date:** 2026-01-19  
**Scope:** CSS, JS files, and inline scripts in views  
**Context:** Post UI migration (P0–P2 complete and frozen)  
**Source of Truth:** [docs/ui-migration.md](ui-migration.md)

---

## 1. Executive Summary

### Overall Health: ✅ GOOD (with minor improvements needed)

The codebase is in **maintainable condition** after the tr-\* UI migration. CSS architecture is well-organized with clear layering. JavaScript uses primarily ID-based selectors, avoiding fragile styling-class dependencies. Legacy classes exist only as documented aliases in CSS, not as runtime dependencies.

### Top 5 Issues

| #   | Issue                                          | Severity | Location                                                         |
| --- | ---------------------------------------------- | -------- | ---------------------------------------------------------------- |
| 1   | **65 `!important` declarations**               | Medium   | Mostly in ui-baseline.css, modal.css, forms.css                  |
| 2   | **Legacy CSS aliases still present**           | Low      | buttons.css, tables.css, badges.css (documented, intentional)    |
| 3   | **4 inline scripts use `.btn-*` hook classes** | Low      | recipes_form, sales_form, sales_index, overhead_categories_index |
| 4   | **app.js uses `.card table` selector**         | Low      | app.js:212 (table scroll wrapper)                                |
| 5   | **POS touchscreen has 70+ DOM queries**        | Low      | touchscreen.php (complexity, not a bug)                          |

### Safe to Proceed Statement

✅ **Safe to proceed with feature development; defer P3–P4 migration until views are finalized.**

The codebase has no critical CSS/JS regressions. Legacy class references in CSS are clearly marked as aliases and do not indicate broken functionality. Inline scripts use proper ID/data-attribute patterns with limited, documented class hooks.

### Remaining Risks

1. `!important` usage may cause specificity wars in future changes
2. Legacy `.btn`/`.table` CSS aliases should eventually be removed (after full migration)
3. POS touchscreen complexity warrants dedicated regression testing

---

## 2. CSS Audit

### 2.1 File Inventory

| File                              | Size    | Role                              |
| --------------------------------- | ------- | --------------------------------- |
| **00-theme/theme-temurasa.css**   | 3.0 KB  | Brand tokens (colors, fonts)      |
| **10-tokens/ui-tokens.css**       | 2.7 KB  | Design tokens (spacing, radii)    |
| **20-base/ui-baseline.css**       | 3.3 KB  | Resets, typography, form defaults |
| **30-layout/layout.css**          | 4.3 KB  | Grid, content areas               |
| **30-layout/shell.css**           | 2.6 KB  | App shell structure               |
| **30-layout/topbar.css**          | 1.3 KB  | Top navigation bar                |
| **30-layout/footer.css**          | 0.9 KB  | Footer styling                    |
| **40-sections/sidebar-nav.css**   | 6.0 KB  | Sidebar navigation                |
| **50-components/buttons.css**     | 5.7 KB  | tr-btn system + legacy aliases    |
| **50-components/tables.css**      | 3.6 KB  | tr-table system + legacy aliases  |
| **50-components/forms.css**       | 4.9 KB  | tr-control + form layouts         |
| **50-components/cards.css**       | 3.5 KB  | Card components                   |
| **50-components/badges.css**      | 3.3 KB  | Badges and pills                  |
| **50-components/modal.css**       | 7.5 KB  | Modal dialogs                     |
| **50-components/alerts.css**      | 0.9 KB  | Alert messages                    |
| **60-pages/branding.css**         | 51.7 KB | Branding guide (largest)          |
| **60-pages/pos-touch.css**        | 17.6 KB | POS touchscreen                   |
| **60-pages/dashboard.css**        | 5.6 KB  | Dashboard page                    |
| **60-pages/auth.css**             | 2.8 KB  | Login/auth pages                  |
| **99-legacy/temurasa-reveal.css** | 5.0 KB  | Presentation mode                 |

**Total:** 21 CSS files, well-organized by layer (tokens → base → layout → sections → components → pages → legacy)

### 2.2 Hotspots

#### Largest Files (potential complexity)

1. **branding.css** (51.7 KB) — Branding guide; self-contained, not a risk
2. **pos-touch.css** (17.6 KB) — POS-specific; properly scoped to `.pos-touch`
3. **modal.css** (7.5 KB) — Modal system; high `!important` usage

#### Duplication Risk

No significant duplication detected. Component styles are centralized.

### 2.3 Specificity Risks

#### `!important` Usage (65 occurrences)

| File            | Count | Context                       |
| --------------- | ----- | ----------------------------- |
| ui-baseline.css | 13    | Resets, accessibility states  |
| sidebar-nav.css | 3     | Collapsed state overrides     |
| forms.css       | 4     | Input state overrides         |
| modal.css       | 12    | Modal positioning (necessary) |
| pos-touch.css   | 20+   | POS layout isolation          |
| branding.css    | ~10   | Print/presentation modes      |

**Assessment:** Most `!important` usage is justified (resets, modals, print). Consider refactoring forms.css and sidebar-nav.css if conflicts arise.

#### Overly Broad Selectors

Found in `ui-baseline.css` (appropriate for resets):

- `body { }` — font defaults
- `textarea { }` — form resets
- `label { }` — label styling
- `a { }`, `a:hover { }` — link styling

Found in `sidebar-nav.css`:

- `.sidebar-title * { }` — forced inheritance (line 19)

**Risk:** Low. Baseline selectors are scoped appropriately.

### 2.4 Legacy Dependencies in CSS

#### Documented Legacy Aliases (Intentional)

| Class           | File        | Line | Status                           |
| --------------- | ----------- | ---- | -------------------------------- |
| `.btn`          | buttons.css | 15   | LEGACY ALIAS (documented header) |
| `.btn:active`   | buttons.css | 46   | Alias mapping                    |
| `.btn:focus`    | buttons.css | 52   | Alias mapping                    |
| `.btn:disabled` | buttons.css | 65   | Alias mapping                    |
| `.table`        | tables.css  | 21   | LEGACY ALIAS (documented header) |
| `.table th`     | tables.css  | 30   | Alias mapping                    |
| `.table td`     | tables.css  | 40   | Alias mapping                    |
| `.badge`        | badges.css  | 13   | LEGACY ALIAS (documented header) |
| `.pill`         | badges.css  | 47   | LEGACY ALIAS                     |
| `.card`         | cards.css   | 2    | Active class (not legacy)        |

**Assessment:** All legacy aliases are clearly documented in file headers. They exist for backwards compatibility during migration. **No undocumented legacy dependencies.**

### 2.5 tr-\* Coverage

#### Button Variants (buttons.css)

- ✅ `.tr-btn` — base button
- ✅ `.tr-btn-primary`, `.tr-btn-secondary`, `.tr-btn-danger`, `.tr-btn-ghost`
- ✅ `.tr-btn-sm`, `.tr-btn-lg` — size variants
- ✅ `.tr-btn-block` — full-width
- ✅ `.tr-btn-icon` — icon-only buttons
- ✅ `.is-loading`, `.is-disabled` — state classes

#### Table Variants (tables.css)

- ✅ `.tr-table` — base table
- ✅ `.tr-table-wrap` — scroll container
- ✅ `.tr-table-compact` — compact padding
- ✅ `.tr-table-striped` — zebra striping
- ✅ `.tr-table-empty` — empty state
- ⚠️ Missing: `.tr-table-hover` (hover defined on base `.tr-table` already)

#### Form Controls (forms.css)

- ✅ `.tr-control` — input/select/textarea
- ✅ `.tr-control-sm` — small variant
- ✅ `.form-group`, `.form-row` — layout helpers

### 2.6 POS Isolation

**Confirmed:** POS styles are properly scoped:

```css
/* pos-touch.css scoping */
.pos-touch { ... }
.content:has(.pos-touch) { ... }
.pos-touch .card { ... }
.pos-touch .tr-card { ... }
```

All POS selectors use `.pos-touch` parent or `pos-*` prefixed classes. No global leakage detected.

---

## 3. JS Audit (External Files)

### 3.1 File Inventory

| File                              | Size     | Role                                              |
| --------------------------------- | -------- | ------------------------------------------------- |
| **app.js**                        | 8.5 KB   | Core utilities (CSRF, toast, filter, scroll wrap) |
| **branding.js**                   | 9.8 KB   | Branding page interactivity                       |
| **sidebar-toggle.js**             | 4.7 KB   | Sidebar collapse behavior                         |
| **sidebar-collapse-state.js**     | 1.6 KB   | Persist sidebar state                             |
| **theme-toggle.js**               | 1.1 KB   | Dark/light mode                                   |
| **tr-daterange.js**               | 10.3 KB  | Date range picker                                 |
| **datatables/menu.js**            | 0.5 KB   | Products DataTable                                |
| **datatables/raw_materials.js**   | 5.0 KB   | Raw materials DataTable                           |
| **datatables/stock_movements.js** | 0.5 KB   | Stock movements DataTable                         |
| **qz-tray/qz-tray.js**            | 141.9 KB | QZ Tray printing (vendor)                         |
| **qz-tray/qz-tray-trial.js**      | 1.5 KB   | QZ Tray trial config                              |

### 3.2 Selector Strategy

#### ID-based (Preferred) ✅

Most scripts use `getElementById` or ID selectors:

- `$('#menuTable')` — DataTables
- `document.getElementById('cart-list')` — POS
- `document.getElementById('items-body')` — forms

#### Data-attribute (Good) ✅

- `[data-target="..."]` — sidebar sections
- `[data-group-index]` — menu options
- `[data-hex]` — branding colors

#### Class-based (Limited, Acceptable)

- `.tr-color-card`, `.tr-color-swatch` — branding.js (tr-\* classes)
- `.nav-section-title` — sidebar-toggle.js (layout class)
- `.card table` — app.js (wrapper utility)
- `.preset-btn` — tr-daterange.js (component class)

### 3.3 Legacy Selector Check

| File   | Line | Selector             | Risk                                                               |
| ------ | ---- | -------------------- | ------------------------------------------------------------------ |
| app.js | 213  | `.table-scroll-wrap` | ⚠️ Uses `.table` in comment context, but selector is wrapper class |

**Result:** ✅ No JS files use legacy `.btn`, `.table`, `.badge`, `.pill`, or `.form-control` as runtime selectors.

### 3.4 DOM Traversal Patterns

| File                 | Pattern                                      | Context                         | Risk |
| -------------------- | -------------------------------------------- | ------------------------------- | ---- |
| app.js:213           | `tbl.closest('.table-scroll-wrap')`          | Skip already-wrapped tables     | Low  |
| sidebar-toggle.js:81 | `ev.target.closest('.nav-section-title')`    | Event delegation                | Low  |
| sidebar-toggle.js:84 | `title.closest('#sidebar-collapse-all-btn')` | Ignore global toggle            | Low  |
| raw_materials.js:110 | `$btn.closest('tr')`                         | Row context for expand/collapse | Low  |

**Assessment:** Traversal patterns are shallow (1-2 levels) and target semantic elements (`tr`, layout classes). No fragile deep chains.

### 3.5 DataTables Modules

All DataTables modules use ID-based targeting:

| Module             | Table Selector         | Filter Selector    |
| ------------------ | ---------------------- | ------------------ |
| menu.js            | `#menuTable`           | `#products-filter` |
| raw_materials.js   | `#rawMaterialsTable`   | `#rm-filter`       |
| stock_movements.js | `#stockMovementsTable` | `#mov-filter`      |

✅ **No `.table` class dependency in DataTables modules.**

### 3.6 Global Scripts Safety

**app.js exports:**

```javascript
window.App = {
    fetchJSON, // AJAX helper with CSRF
    toast, // Toast notifications
    csrfName, // CSRF token name
    csrfToken, // CSRF token getter
    setupFilter, // Live filter utility
};
```

**Side effects:**

- Auto-wraps `.card table` elements in `.table-scroll-wrap`
- Sets up sidebar section toggles

**Risk:** The `.card table` selector is broad but intentionally catches all tables in cards. Consider making this opt-in via a data attribute in future.

---

## 4. Inline Script Audit

### 4.1 Files with `<script>` Blocks

| File                                                  | JS Complexity         | Primary Selectors                  |
| ----------------------------------------------------- | --------------------- | ---------------------------------- |
| **pos/touchscreen.php**                               | High (70+ queries)    | IDs only                           |
| **transactions/sales_form.php**                       | Medium (22 queries)   | IDs + `.item-*`, `.btn-remove-row` |
| **master/recipes_form.php**                           | Medium (20 queries)   | IDs + `.item-*`, `.btn-remove-row` |
| **master/raw_materials_form.php**                     | Medium (18 queries)   | IDs + data-attrs                   |
| **transactions/sales_index.php**                      | Low (12 queries)      | IDs + `.btn-void`                  |
| **master/menu_options_index.php**                     | Low (10 queries)      | IDs + `[data-group-index]`         |
| **overhead_categories/overhead_categories_index.php** | Low (9 queries)       | `.btn-toggle-oc`                   |
| All other files                                       | Minimal (2-5 queries) | IDs only                           |

### 4.2 Legacy Selector Usage in Inline Scripts

| File                          | Line | Selector          | Context                 | Risk          |
| ----------------------------- | ---- | ----------------- | ----------------------- | ------------- |
| recipes_form.php              | 431  | `.btn-remove-row` | Event delegation target | Low (JS hook) |
| sales_form.php                | 313  | `.btn-remove-row` | Query for event binding | Low (JS hook) |
| sales_index.php               | 251  | `.btn-void`       | Event delegation        | Low (JS hook) |
| overhead_categories_index.php | 119  | `.btn-toggle-oc`  | Toggle buttons          | Low (JS hook) |

**Assessment:** These are **JS hook classes** (documented in ui-migration.md as exceptions). They are not legacy UI classes—they exist specifically for JavaScript targeting. ✅ Compliant with migration rules.

### 4.3 DOM Traversal in Inline Scripts

| File                   | Line | Pattern                                | Purpose                 | Risk |
| ---------------------- | ---- | -------------------------------------- | ----------------------- | ---- |
| menu_options_index.php | 423  | `target.closest('[data-group-index]')` | Find parent group       | Low  |
| menu_options_index.php | 431  | `target.closest('tr')`                 | Find row                | Low  |
| recipes_form.php       | 417  | `e.target.closest('tr')`               | Find row for input sync | Low  |
| recipes_form.php       | 431  | `e.target.closest('.btn-remove-row')`  | Find remove button      | Low  |
| recipes_form.php       | 434  | `btn.closest('tr')`                    | Find row to remove      | Low  |
| pos/touchscreen.php    | 978  | `e.target.closest('.customer-item')`   | Find customer item      | Low  |

**Assessment:** All traversals are shallow (1 level to `tr` or data-attributed parent). No deep `.parent().parent().parent()` chains.

### 4.4 JS Hook Contracts (Classes/IDs/Data-attrs)

#### POS Touchscreen (touchscreen.php)

- **IDs:** `cart-list`, `cart-empty`, `total-items`, `total-amount`, `pos-form`, `payment-method`, `amount-paid`, `change-display`, `customer-*`, `options-modal`, `options-*`
- **Classes:** `.customer-item`, `.pos-mini-btn`, `.pos-icon-btn`
- **Data-attrs:** `data-menu-id`, `data-price`, `data-name`, `data-options`

#### Sales Form (sales_form.php)

- **IDs:** `items-body`, `btn-add-row`, `grand-total-display`, `payment-method`, `amount-paid`, `change-display`
- **Classes:** `.item-menu`, `.item-qty`, `.item-price`, `.item-subtotal`, `.btn-remove-row`

#### Recipes Form (recipes_form.php)

- **IDs:** `recipe-items-body`, `btn-add-ingredient`
- **Classes:** `.item-type`, `.select-raw`, `.select-recipe`, `.unit-label`, `.btn-remove-row`

#### Menu Options (menu_options_index.php)

- **IDs:** `group-container`, `add-group`, `group-template`, `option-row-template`
- **Data-attrs:** `[data-group-index]`
- **Classes:** `.add-option`, `.remove-option`, `.remove-group`

### 4.5 Inline Script Compliance

✅ **All inline scripts comply with migration rules:**

- Primary targeting via IDs
- Class selectors are documented JS hooks (`.btn-remove-row`, `.item-*`)
- No dependency on legacy UI classes (`.btn`, `.table`, `.badge`, `.pill`, `.form-control`)

---

## 5. Concrete Recommendations

### Do Now (Low Effort, High Impact)

| #   | Recommendation                                                                 | File(s)              | Why                                                               |
| --- | ------------------------------------------------------------------------------ | -------------------- | ----------------------------------------------------------------- |
| 1   | **Add code comment to app.js** explaining `.card table` wrapper is intentional | app.js:212           | Prevents confusion during maintenance                             |
| 2   | **Document JS hook classes** in ui-migration.md                                | docs/ui-migration.md | Centralize contract for `.btn-remove-row`, `.item-*`, `.btn-void` |
| 3   | **Consider data-attr for table scroll**                                        | app.js:212           | Replace `.card table` with `[data-scroll-wrap]` opt-in            |

### Do Later (Medium Effort)

| #   | Recommendation                             | File(s)                          | Why                                                             |
| --- | ------------------------------------------ | -------------------------------- | --------------------------------------------------------------- |
| 4   | **Reduce `!important` in forms.css**       | forms.css                        | 4 occurrences; check if specificity can be improved             |
| 5   | **Reduce `!important` in sidebar-nav.css** | sidebar-nav.css                  | 3 occurrences for collapsed state                               |
| 6   | **Extract POS JS to external file**        | touchscreen.php                  | 70+ queries in inline script; improves cacheability and testing |
| 7   | **Consolidate `.item-*` JS hooks**         | sales_form.php, recipes_form.php | Similar patterns could share a utility module                   |

### Do After Full Migration

| #   | Recommendation                           | File(s)     | Why                                   |
| --- | ---------------------------------------- | ----------- | ------------------------------------- |
| 8   | **Remove legacy `.btn` alias**           | buttons.css | After P5 complete, remove lines 15-89 |
| 9   | **Remove legacy `.table` alias**         | tables.css  | After P5 complete, remove lines 21-53 |
| 10  | **Remove legacy `.badge`/`.pill` alias** | badges.css  | After P5 complete                     |

---

## 6. Quick Regression Checklist

### POS Touchscreen (touchscreen.php)

- [ ] Menu items load and display in grid
- [ ] Add to cart works (qty badge updates)
- [ ] Options modal opens for items with options
- [ ] Customer selection modal works
- [ ] Payment method switch updates UI
- [ ] Amount paid / change calculation works
- [ ] Print receipt flow works (QZ Tray)

### Sales Form (sales_form.php)

- [ ] Add row button creates new item row
- [ ] Menu select populates price
- [ ] Qty/price changes update subtotal
- [ ] Remove row button removes row and recalculates
- [ ] Grand total updates correctly
- [ ] Payment method toggle shows/hides amount fields

### Recipes Form (recipes_form.php)

- [ ] Add ingredient button creates new row
- [ ] Type dropdown switches between raw/recipe selects
- [ ] Unit label updates based on selection
- [ ] Waste % input works
- [ ] Remove row button works
- [ ] Row indices renumber correctly

### Menu Options (menu_options_index.php)

- [ ] Add group button creates new group
- [ ] Add option button (within group) creates option row
- [ ] Remove option button removes row
- [ ] Remove group button removes entire group
- [ ] Form submission saves all groups/options

### DataTables Pages

- [ ] **Products (menu.js):** Table loads, filter works, pagination works
- [ ] **Raw Materials (raw_materials.js):** Table loads, variant expand/collapse works
- [ ] **Stock Movements (stock_movements.js):** Table loads, filter works

---

## Appendix: Scan Commands Used

```powershell
# CSS file inventory
Get-ChildItem -Path "public/css" -Recurse -Filter "*.css" | Sort-Object Length -Descending

# Legacy class scan in CSS
Select-String -Path "public/css/**/*.css" -Pattern '\.(btn|card|table|badge|pill|form-control)[^-]'

# !important count
(Select-String -Path "public/css/**/*.css" -Pattern '!important').Count

# JS legacy selector scan
Select-String -Path "public/js/*.js","public/assets/js/**/*.js" -Pattern "\.(btn|table|badge|pill|form-control)\b"

# Inline script files
Select-String -Path "app/Views/**/*.php" -Pattern '<script\b' | ForEach-Object { $_.Path } | Sort-Object -Unique

# Legacy selectors in views
Select-String -Path "app/Views/**/*.php" -Pattern '\.(btn|table|badge|pill|form-control)\b'

# DOM traversal patterns
Select-String -Path "app/Views/**/*.php" -Pattern 'closest\(|\.parent\(|\.children\('
```

---

_Report generated by CSS/JS audit process on 2026-01-19_
