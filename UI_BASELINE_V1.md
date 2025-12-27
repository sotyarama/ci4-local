# 🧊 UI BASELINE v1 — Temu Rasa POS

**Status**: BASELINE (subject to controlled tuning)  
**Scope**: Layout · Typography · Color · Components  
**Freeze Type**: Structural & System Freeze  
**Tuning Policy**: Minor numeric tuning allowed (see Section 6)

---

## 1. Purpose

UI Baseline v1 is defined to:

-   Ensure visual consistency
-   Prevent UI technical debt
-   Provide an objective reference during UI reviews
-   Clearly separate design errors from intentional tuning

This baseline is not the final UI, but the official foundation.

---

## 2. Layout Baseline (Structural Freeze)

### Layout Contract

| Area         | Rule                                                    |
| ------------ | ------------------------------------------------------- |
| Header       | Fixed height; contains brand, page title, and user menu |
| Sidebar      | Fixed width; final menu structure; vertical scroll      |
| Main Content | Consistent global padding; no per-page hardcoding       |
| Footer       | Fixed height; non-interactive information               |

**Rules**

-   No new layout structures without a formal redesign
-   No structural changes during the tuning phase

---

## 3. Typography Baseline

### 3.1 Font Family

-   Single font family across the entire application
-   Font family must not be changed after baseline declaration

---

### 3.2 Font Size System (Hierarchy Freeze)

| Token  | Usage              |
| ------ | ------------------ |
| fs-xs  | Helper text, hints |
| fs-sm  | Caption, metadata  |
| fs-md  | Default body text  |
| fs-lg  | Emphasized text    |
| fs-xl  | Section titles     |
| fs-2xl | Page titles        |

**Rules**

-   Token hierarchy is frozen
-   Pixel values may be tuned ±1–2px

---

### 3.3 Line Height

| Token      | Usage          |
| ---------- | -------------- |
| lh-tight   | Headings       |
| lh-normal  | Body text      |
| lh-relaxed | Long-form text |

---

### 3.4 Font Weight

| Weight | Usage                |
| ------ | -------------------- |
| 400    | Body text            |
| 500    | Buttons, labels      |
| 600    | Titles, card headers |

No additional font weights are permitted.

---

## 4. Color System Baseline

### 4.1 Semantic Color Roles

Colors are defined by function, not decoration.

-   Primary
-   Secondary
-   Accent
-   Background
-   Surface
-   Border
-   Text Primary
-   Text Muted
-   Success
-   Warning
-   Danger

**Rules**

-   Semantic roles are frozen
-   Color shades may be tuned (lighter/darker)

---

### 4.2 Color Usage Rules

-   No hardcoded colors in components
-   All colors must be referenced via tokens
-   Colors represent state and meaning, not decoration

---

## 5. Component Baseline

### 5.1 Buttons

-   Variants: Primary, Secondary, Danger
-   Consistent size and padding
-   Clear disabled and loading states

---

### 5.2 Cards

-   Consistent border radius
-   Limited shadow levels
-   Consistent internal padding

---

### 5.3 Tables

-   Consistent row height
-   Defined header styling
-   Hover state required
-   Empty state required

---

### 5.4 Forms

-   Consistent input height
-   Clear error states
-   Disabled state must be visually distinct

**Rule**

-   No new component variants without functional justification

---

## 6. Tuning Policy (Explicit)

### Allowed Adjustments

-   Font size (±1–2px)
-   Line height
-   Button and card padding
-   Color shade adjustments
-   Minor border-radius changes

---

### Not Allowed

-   Changing font family
-   Adding new font size tokens
-   Adding new color roles
-   Adding new component variants without definition
-   Modifying layout structure

**Principle**: Tune, do not expand.

---

## 7. Review & Lock Plan

| Phase          | Status                    |
| -------------- | ------------------------- |
| UI Baseline v1 | Current                   |
| Tuning Window  | After real usage          |
| UI System v1.0 | After tuning is completed |

---

## 8. Declaration

UI Baseline v1 is established as the official UI system foundation.  
Changes after this baseline are allowed only as controlled tuning, not structural modification.

---

## 9. Definition of Done (Baseline)

-   [ ] Layout contract defined
-   [ ] Single font family applied
-   [ ] Font sizes use tokens
-   [ ] Semantic color system in place
-   [ ] Components follow baseline rules
-   [ ] No uncontrolled hardcoded styles

---

### Closing Statement

UI Baseline v1 is not about perfection,  
but about having a clear standard to improve from.
