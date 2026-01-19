# JavaScript Hook Registry

This document defines **intentional JavaScript hooks** used in views.
These selectors are JS contracts and **MUST NOT** be renamed without updating scripts.

---

## View: transactions/sales_form.php

### IDs

- `#items-body` — Container tbody for line item rows
- `#btn-add-row` — Button to add new item row
- `#grand-total-display` — Element displaying grand total
- `#payment-method` — Select for payment method
- `#amount-paid` — Input for amount paid (cash transactions)
- `#change-display` — Element displaying calculated change

### Classes (JS hooks)

- `.item-menu` — Menu select in each item row
- `.item-qty` — Quantity input in each item row
- `.item-price` — Price input in each item row
- `.item-subtotal` — Subtotal display cell in each item row
- `.btn-remove-row` — Remove button for each item row

### Data Attributes

None.

---

## View: master/recipes_form.php

### IDs

- `#recipe-items-body` — Container tbody for ingredient rows
- `#btn-add-ingredient` — Button to add new ingredient row

### Classes (JS hooks)

- `.item-type` — Type select (raw/recipe) in each row
- `.select-raw` — Raw material select in each row
- `.select-recipe` — Recipe select in each row
- `.unit-label` — Unit label span in each row
- `.btn-remove-row` — Remove button for each ingredient row

### Data Attributes

None.

---

## View: master/menu_options_index.php

### IDs

- `#group-container` — Container for option groups
- `#add-group` — Button to add new option group
- `#group-template` — Template element for new groups
- `#option-row-template` — Template element for new option rows

### Classes (JS hooks)

- `.add-option` — Button to add option within a group
- `.remove-option` — Button to remove an option row
- `.remove-group` — Button to remove entire group

### Data Attributes

- `[data-group-index]` — Group element identifier for targeting

---

## View: pos/touchscreen.php

### IDs

- `#cart-list` — Container for cart items
- `#cart-empty` — Empty cart placeholder
- `#total-items` — Total items count display
- `#total-amount` — Total amount display
- `#pos-form` — Main POS form element
- `#payment-method` — Payment method select
- `#amount-paid` — Amount paid input
- `#change-display` — Change amount display
- `#customer-display` — Customer name display
- `#customer-id` — Hidden customer ID input
- `#customer-modal` — Customer selection modal
- `#customer-open` — Button to open customer modal
- `#customer-close` — Button to close customer modal
- `#customer-filter` — Customer search input
- `#customer-list` — Customer list container
- `#customer-empty` — Empty customer list placeholder
- `#customer-recent-wrap` — Recent customers wrapper
- `#customer-recent` — Recent customers container
- `#options-modal` — Menu options modal
- `#options-title` — Options modal title
- `#options-body` — Options modal body
- `#options-error` — Options modal error display
- `#options-total` — Options modal total display
- `#options-cancel` — Options modal cancel button
- `#options-confirm` — Options modal confirm button
- `#options-close` — Options modal close button
- `#pos-pay` — Pay button
- `#clear-cart` — Clear cart button
- `#pay-hint` — Payment hint text
- `#money-paid` — Money paid display
- `#money-change` — Money change display

### Classes (JS hooks)

- `.customer-item` — Customer item in selection list

### Data Attributes

- `data-id` — Menu item ID
- `data-name` — Menu item name
- `data-price` — Menu item price
- `data-group-id` — Option group ID
- `data-option-id` — Option ID
- `data-search` — Searchable text for filtering

---

## View: transactions/sales_index.php

### IDs

- `#void-modal` — Void confirmation modal
- `#void-form` — Void form element
- `#void-reason` — Void reason textarea
- `#void-close` — Void modal close button
- `#void-cancel` — Void modal cancel button

### Classes (JS hooks)

- `.btn-void` — Void button on each sale row

### Data Attributes

- `data-url` — Void action URL on .btn-void buttons

---

## View: overhead_categories/overhead_categories_index.php

### IDs

- `#oc-filter` — Filter input for categories
- `#oc-table-body` — Table body container
- `#oc-noresult` — No results placeholder row

### Classes (JS hooks)

- `.btn-toggle-oc` — Toggle active/inactive button
- `.oc-status` — Status badge element

### Data Attributes

- `data-id` — Category ID on toggle buttons and status badges
- `data-active` — Active state (1/0) on toggle buttons and badges

---

## Maintenance Rules

1. **Before renaming any selector**, search for its usage in the corresponding view's `<script>` block
2. **JS hooks are distinct from styling classes** — they exist purely for JavaScript targeting
3. **Coordinate changes** — if a hook must change, update both HTML and JS simultaneously
4. **Test thoroughly** — any hook change requires functional testing of the affected feature
