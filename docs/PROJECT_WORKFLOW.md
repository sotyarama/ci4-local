# PROJECT_WORKFLOW.md — Single Source of Truth

Any AI or human action that ignores this document is considered non-compliant by default.

**Repository:** Temu Rasa POS (CodeIgniter 4)  
**Status:** AUTHORITATIVE  
**Created:** 2026-01-19  
**Last Verified:** 2026-01-19  
**Scope:** All work on this repository by humans or AI tools  
**Evidence:** See `docs/PROJECT_WORKFLOW_EVIDENCE.md` for verification data

---

## Table of Contents

1. [Purpose & Scope](#1-purpose--scope)
2. [Mandatory Pre-Execution Reading](#2-mandatory-pre-execution-reading)
3. [Repository Reality Map](#3-repository-reality-map)
4. [Change Categories & Required Procedures](#4-change-categories--required-procedures)
5. [Safety Rules](#5-safety-rules)
6. [Commit & Push Discipline](#6-commit--push-discipline)
7. [AI Usage Contract](#7-ai-usage-contract)
8. [Exception Handling](#8-exception-handling)
9. [Document Registry & Roles](#9-document-registry--roles)
10. [Gap & Risk Analysis](#10-gap--risk-analysis)
11. [AI Prompt Header Template](#11-ai-prompt-header-template-must)

---

## 1. Purpose & Scope

### 1.1 What This Document Governs

This document defines the **mandatory procedure** for all modifications to this repository, including but not limited to:

- Adding new features or modules
- Modifying UI, CSS, JavaScript, or inline scripts
- Introducing libraries or dependencies
- Touching database schema, data, or migrations
- Committing, pushing, or preparing releases
- Writing prompts for AI tools (Copilot, ChatGPT, Codex, etc.)

### 1.2 Who/What MUST Obey This Document

- **Humans:** All developers, operators, and maintainers
- **AI Tools:** All AI assistants, code generators, and automated systems
- **Scripts:** All automated tooling that modifies code

### 1.3 Authority Hierarchy

When documents conflict, this is the precedence order:

1. `docs/PROJECT_WORKFLOW.md` (this document) — governance procedures
2. `docs/product/dashboard/*.md` — dashboard product decisions (LOCKED)
3. `docs/ui-migration.md` — UI migration rules (AUTHORITATIVE)
4. `docs/js-hooks.md` — JavaScript hook contracts (AUTHORITATIVE)
5. `UI_BASELINE_V1.md` — UI design system baseline (STRUCTURAL FREEZE)
6. `DEV_NOTES.md` — historical context and feature logs
7. `DEPLOYMENT_WORKFLOW.md` — deployment procedures

---

## 2. Mandatory Pre-Execution Reading

### 2.1 Before ANY Code Change

You MUST read and understand:

| Document                              | Purpose                                | When Required          |
| ------------------------------------- | -------------------------------------- | ---------------------- |
| This document (`PROJECT_WORKFLOW.md`) | Governance rules                       | ALWAYS                 |
| `DEV_NOTES.md`                        | Current module status, feature history | ALWAYS                 |
| `docs/js-hooks.md`                    | JS selector contracts                  | Any view/JS/CSS change |
| `docs/ui-migration.md`                | UI class system rules                  | Any UI/CSS change      |

### 2.2 Before UI/CSS Changes

You MUST ALSO read:

| Document                                | Purpose                         |
| --------------------------------------- | ------------------------------- |
| `UI_BASELINE_V1.md`                     | Design system constraints       |
| `B2 - Global UI Elements.md`            | Component standardization scope |
| `docs/UI_MIGRATION_AUDIT_2026-01-19.md` | Current migration status        |
| `docs/UI_CSS_JS_AUDIT_2026-01-19.md`    | CSS/JS health status            |

### 2.3 Before Dashboard Changes

You MUST ALSO read the FULL dashboard spec chain:

1. `docs/product/dashboard/01_dashboard-freeze-spec.md` — LOCKED product decisions
2. `docs/product/dashboard/02_transition-checklist.md` — implementation guardrails
3. `docs/product/dashboard/03_edge-case-behavior.md` — edge case handling
4. `docs/product/dashboard/04_ui-guardrail-rules.md` — UI constraints
5. `docs/product/dashboard/05_component-responsibility-map.md` — architecture
6. `docs/product/dashboard/06_layout-skeleton-notes.md` — layout contract

### 2.4 Before Database Changes

You MUST understand:

- Current migration file naming: `YYYY-MM-DD-HHMMSS_Description.php`
- Migration files are in `app/Database/Migrations/`
- Seeder files are in `app/Database/Seeds/`
- **NEVER** edit an existing migration after it has been committed

### 2.5 Before Deployment

You MUST read:

- `DEPLOYMENT_WORKFLOW.md` — NAS deployment procedure
- `SECURITY.md` — secret handling requirements

---

## 3. Repository Reality Map

### 3.1 Framework & Technology Stack

| Aspect           | Reality                                                                                         |
| ---------------- | ----------------------------------------------------------------------------------------------- |
| Framework        | CodeIgniter 4 (PHP 8.1+)                                                                        |
| Database         | MySQL (InnoDB)                                                                                  |
| Frontend         | Server-rendered PHP views + vanilla JS                                                          |
| CSS Architecture | Layered CSS (tokens → base → layout → components → pages)                                       |
| Testing          | PHPUnit (configured in `phpunit.xml.dist`)                                                      |
| CI/CD            | GitHub Actions: `secret-scan.yml`, `phpunit.yml`, `env-format-check.yml`, `deploy-reminder.yml` |
| Deployment       | Manual git pull to ASUSTOR NAS                                                                  |

### 3.2 Directory Responsibilities (As Observed)

| Directory                  | Actual Role                                                                 |
| -------------------------- | --------------------------------------------------------------------------- |
| `app/Controllers/`         | Request handlers, organized by domain (Master, Transactions, Reports, etc.) |
| `app/Models/`              | Database models (CI4 Model pattern)                                         |
| `app/Views/`               | PHP view templates, organized by domain                                     |
| `app/Database/Migrations/` | Schema changes (timestamped, sequential)                                    |
| `app/Database/Seeds/`      | Demo/test data seeders                                                      |
| `app/Services/`            | Business logic services (e.g., `StockConsumptionService`)                   |
| `app/Filters/`             | HTTP filters (auth, role-based access)                                      |
| `app/Config/`              | Configuration files                                                         |
| `public/css/`              | Layered CSS via `main.css` imports (00-theme → 99-legacy)                   |
| `public/js/`               | Core JS files (app.js, theme-toggle.js, etc.)                               |
| `public/assets/js/`        | Domain-specific JS (datatables/, qz-tray/)                                  |
| `docs/`                    | Documentation                                                               |
| `scripts/`                 | Utility scripts (Python, PHP, shell)                                        |
| `tests/`                   | PHPUnit test files                                                          |
| `writable/`                | Runtime files (logs, cache, sessions)                                       |
| `vendor/`                  | Composer dependencies (DO NOT EDIT)                                         |

### 3.3 Module Status

**Authoritative Source:** `DEV_NOTES.md` section "Current Modules Status"

Before working on any module, verify its current status in `DEV_NOTES.md`.
The table below is a snapshot; always consult the authoritative source.

| Module               | Verify In                                                                       |
| -------------------- | ------------------------------------------------------------------------------- |
| All modules          | `DEV_NOTES.md` → "Current Modules Status" table                                 |
| High-risk (JS-heavy) | `pos/touchscreen.php`, `transactions/sales_form.php`, `master/recipes_form.php` |

### 3.4 UI Migration Status

**Authoritative Source:** `docs/ui-migration.md` → "Migration Priority & Execution Order"

Do NOT rely on file counts here. Always verify current status in `docs/ui-migration.md`.

| Phase | Status Check                                                                       |
| ----- | ---------------------------------------------------------------------------------- |
| P0-P2 | Marked as complete in `docs/ui-migration.md`; DO NOT MODIFY without bug fix reason |
| P3-P5 | Check `docs/ui-migration.md` for current status before any UI work                 |

### 3.5 Areas by Risk Profile

#### Stable / Frozen

- Dashboard product spec (`docs/product/dashboard/*.md`)
- UI Baseline (`UI_BASELINE_V1.md`)
- Layout skeleton contract
- P0-P2 migrated views (DO NOT MODIFY without bug fix reason)

#### Active / Evolving

- P3-P5 views (pending migration)
- Reports system
- Master data index pages

#### Fragile / High-Risk

- `pos/touchscreen.php` — complex inline JS, many DOM queries
- `transactions/sales_form.php` — dynamic rows, JS hooks
- `master/recipes_form.php` — sub-recipe logic, JS hooks
- Stock consumption service — critical business logic

#### Intentionally Isolated

- POS touchscreen CSS (`60-pages/pos-touch.css`) — scoped to `.pos-touch`
- PDF views (`reports/pdf/*.php`) — may need legacy classes for DOMPDF
- Branding/presentation (`branding.css`, `temurasa-reveal.css`)
- Vendor code (`vendor/`) — NEVER EDIT

---

## 4. Change Categories & Required Procedures

### 4.1 Feature / Module Addition

#### REQUIRED STEPS

1. **Read** `DEV_NOTES.md` to understand current module status
2. **Check** if feature overlaps with existing modules
3. **Create** migration files for any DB changes (new file, never edit existing)
4. **Follow** existing patterns for Controllers, Models, Views
5. **Update** `DEV_NOTES.md` with new feature documentation
6. **Add** routes to `app/Config/Routes.php`
7. **Update** sidebar in `app/Views/layouts/partials/sidebar.php` if needed
8. **Test** end-to-end before committing

#### MUST NOT

- Assume module structure — verify in codebase first
- Create controllers without corresponding routes
- Add features without documenting in `DEV_NOTES.md`

### 4.2 UI / CSS / JS Changes

#### REQUIRED STEPS

1. **Read** `docs/js-hooks.md` — identify protected selectors
2. **Read** `docs/ui-migration.md` — understand class system
3. **Check** migration status of target file(s)
4. **Preserve** all JS hook classes (see Section 5.1)
5. **Use** `tr-*` classes for new UI elements
6. **Work** on ONE file per task
7. **Test** all JS behaviors after changes

#### MUST NOT

- Rename or remove JS hook selectors
- Add legacy classes (`.btn`, `.table`, `.badge`, `.pill`)
- Modify P0-P2 migrated files without explicit bug fix reason
- Mix UI changes with logic changes in same commit

### 4.3 Inline Script Changes

#### REQUIRED STEPS

1. **Read** `docs/js-hooks.md` for the target view
2. **Identify** all ID, class, and data-attribute selectors used
3. **Preserve** DOM traversal patterns (`.closest()`, `.querySelector()`)
4. **Test** all interactive behaviors after changes

#### MUST NOT

- Change selector names without updating both HTML and JS
- Add deep DOM traversal chains (`.parent().parent().parent()`)
- Reference legacy UI classes for JS targeting

### 4.4 Library / Dependency Introduction

#### REQUIRED STEPS

1. **Justify** why existing tools cannot accomplish the task
2. **Check** PHP version compatibility (requires PHP 8.1+)
3. **Add** via Composer: `composer require <package>`
4. **Update** `composer.json` and `composer.lock` together
5. **Document** in `DEV_NOTES.md` why dependency was added
6. **Test** on clean install: `composer install`

#### MUST NOT

- Manually edit `vendor/` directory
- Add dependencies without documenting reason
- Introduce frontend build tools without team consensus

### 4.5 Database Migration / Seed Changes

#### REQUIRED STEPS

1. **Create** new migration file with timestamp: `YYYY-MM-DD-HHMMSS_Description.php`
2. **Use** InnoDB engine for all tables
3. **Add** proper foreign keys with appropriate ON DELETE behavior
4. **Test** migration: `php spark migrate`
5. **Test** rollback: `php spark migrate:rollback`
6. **Update** seeders if demo data affected
7. **Document** schema changes in `DEV_NOTES.md`

#### MUST NOT

- Edit existing migration files after commit
- Use raw SQL without migration wrapper
- Delete or rename migration files
- Add migrations that cannot be rolled back

### 4.6 Refactoring vs Non-Refactor Changes

#### Refactoring (structure change, same behavior)

- MUST be in separate commit from feature changes
- MUST NOT change observable behavior
- MUST run existing tests after refactor

#### Non-Refactor (behavior change)

- MUST be clearly documented in commit message
- MUST update tests if behavior changed
- MUST update `DEV_NOTES.md` if user-facing

### 4.7 Documentation-Only Changes

#### REQUIRED STEPS

1. **Use** clear, specific language
2. **Mark** status clearly (LOCKED, ACTIVE, DRAFT)
3. **Include** date stamps for auditable changes
4. **Cross-reference** related documents

#### MUST NOT

- Remove content without archiving/justification
- Change LOCKED documents without explicit exception approval

---

## 5. Safety Rules

### 5.1 JS Hook Preservation (CRITICAL)

**Authoritative Source:** `docs/js-hooks.md`

The selectors listed below are **JavaScript contracts** and MUST NOT be renamed or removed.
Always verify current hooks in `docs/js-hooks.md` before modifying any view.

#### Key hooks from `docs/js-hooks.md`:

**sales_form.php:**

- IDs: `#items-body`, `#btn-add-row`, `#grand-total-display`, `#payment-method`, `#amount-paid`, `#change-display`
- Classes: `.item-menu`, `.item-qty`, `.item-price`, `.item-subtotal`, `.btn-remove-row`

**recipes_form.php:**

- IDs: `#recipe-items-body`, `#btn-add-ingredient`
- Classes: `.item-type`, `.select-raw`, `.select-recipe`, `.unit-label`, `.btn-remove-row`

**menu_options_index.php:**

- IDs: `#group-container`, `#add-group`, `#group-template`, `#option-row-template`
- Data-attrs: `[data-group-index]`
- Classes: `.add-option`, `.remove-option`, `.remove-group`

**touchscreen.php:**

- IDs: `#cart-list`, `#cart-empty`, `#total-items`, `#total-amount`, `#pos-form`, `#payment-method`, `#amount-paid`, `#change-display`, `#customer-*`, `#options-modal`, `#options-*`
- Classes: `.customer-item`
- Data-attrs: `data-id`, `data-name`, `data-price`, `data-group-id`, `data-option-id`

**sales_index.php:**

- IDs: `#void-modal`, `#void-form`, `#void-reason`, `#void-close`, `#void-cancel`
- Classes: `.btn-void`
- Data-attrs: `data-url`

**overhead_categories_index.php:**

- IDs: `#oc-filter`, `#oc-table-body`, `#oc-noresult`
- Classes: `.btn-toggle-oc`, `.oc-status`
- Data-attrs: `data-id`, `data-active`

### 5.2 UI Migration Constraints

#### Class System Rules

| Class Type           | Status        | Action                 |
| -------------------- | ------------- | ---------------------- |
| `tr-btn`, `tr-btn-*` | Target system | USE for new code       |
| `tr-control`         | Target system | USE for inputs/selects |
| `tr-table`           | Target system | USE for tables         |
| `.btn`, `.btn-*`     | Legacy        | DO NOT add new         |
| `.table`             | Legacy        | DO NOT add new         |
| `.badge`, `.pill`    | Legacy        | DO NOT add new         |
| `.form-control`      | Legacy        | DO NOT add new         |

#### Dual-class Exception

Dual-class (e.g., `class="table tr-table"`) is allowed ONLY when:

- JS currently depends on the legacy class, AND
- The JS cannot be updated in the same task

### 5.3 Scope Isolation Rules

| Area            | Isolation Method                               |
| --------------- | ---------------------------------------------- |
| POS Touchscreen | All styles scoped to `.pos-touch`              |
| PDF Views       | May use inline styles for DOMPDF compatibility |
| Branding Guide  | Self-contained in `60-pages/branding.css`      |

### 5.4 "One Change Unit" Rules

Each commit MUST contain only ONE of:

- Feature addition
- Bug fix
- Refactor
- Documentation update
- UI migration (single file)

MUST NOT mix:

- Backend logic + UI changes
- Multiple unrelated bug fixes
- Feature + refactor

### 5.5 Forbidden Without Explicit Instruction

- Modifying LOCKED dashboard spec documents
- Changing UI Baseline structural rules
- Editing migration files after commit
- Removing JS hook selectors
- Adding new legacy CSS classes
- Auto-fixing P0-P2 migrated views
- Modifying vendor directory

---

## 6. Commit & Push Discipline

### 6.1 When to Commit

- After completing ONE logical unit of work
- After all tests pass
- After verifying no lint errors
- Before switching to unrelated task

### 6.2 Commit Message Format

```
<type>(<scope>): <description>

[optional body]
[optional footer]
```

#### Types (from observed history):

- `feat` — new feature
- `fix` — bug fix
- `ui` — UI/CSS changes
- `ui(tr)` — UI migration to tr-\* system
- `docs` — documentation
- `refactor` — code restructure without behavior change
- `chore` — maintenance tasks
- `test` — test additions/modifications

#### Examples from repository:

```
ui: migrate sales_form.php to tr-* system (P0)
feat(theme): add light/dark mode toggle using css variables
fix: guard waste_pct recipe (limit 0-100) + rounding
docs: add authoritative UI migration plan
```

### 6.3 Commit Grouping Rules

**Same commit:**

- Related changes to achieve one feature
- Model + Controller + View for same feature
- CSS + JS for same UI component (if tightly coupled)

**Separate commits:**

- Backend logic vs UI styling
- Different files in UI migration (one file per commit)
- Refactor vs feature addition
- Bug fix vs new feature

### 6.4 Pre-Push Validation

Before `git push`:

1. **Run** tests: `vendor\bin\phpunit` (Windows) or `./phpunit` (Unix)
2. **Check** for debug code: no `dd()`, `var_dump()`, `console.log()` in commits
3. **Verify** `.env` is not staged
4. **Review** `git diff --cached` for unintended changes
5. **Confirm** commit messages follow format

### 6.5 Branch Strategy

**Verify current branches:** `git branch -a`

| Branch (Verified 2026-01-19) | Purpose                            |
| ---------------------------- | ---------------------------------- |
| `master`                     | Development branch, stable commits |
| `deploy/nas`                 | Production deployment branch       |

> Other branches may exist (backup, feature). Run `git branch -a` to verify.

#### Workflow (from `DEPLOYMENT_WORKFLOW.md`):

1. Work on `master`
2. Merge/promote to `deploy/nas` for release
3. Pull `deploy/nas` on NAS server

---

## 7. AI Usage Contract

### 7.1 Mandatory Reading for AI

Before executing ANY task, AI tools MUST:

1. **Read** this document (`PROJECT_WORKFLOW.md`)
2. **Read** `DEV_NOTES.md` for context
3. **Read** relevant domain documents (see Section 2)
4. **Search** codebase for existing patterns before proposing new ones

### 7.2 Prohibited Assumptions

AI tools MUST NOT assume:

- Framework conventions (verify in codebase)
- File locations (search first)
- Database schema (check migrations)
- UI class system (read `ui-migration.md`)
- JS hook names (read `js-hooks.md`)
- That any structure is "standard" or "best practice"

### 7.3 Scope Boundaries

AI tools MUST:

- Work on ONE file at a time for UI changes
- Confirm scope before making changes
- Ask for clarification if task is ambiguous
- Stop if task would violate safety rules

AI tools MUST NOT:

- Make changes outside explicit task scope
- "Improve" code not mentioned in request
- Add features not explicitly requested
- Auto-fix issues not in the current task

### 7.4 Evidence-First Behavior

AI tools MUST:

- Search codebase before proposing solutions
- Reference actual file paths and line numbers
- Quote existing code patterns when relevant
- Identify discrepancies with documentation

AI tools MUST NOT:

- Propose "ideal" solutions without codebase evidence
- Assume file structure from framework documentation
- Generate code without verifying existing patterns

### 7.5 Output Requirements

AI-generated changes MUST:

- Follow existing code style (4-space indent, single quotes)
- Match existing naming conventions
- Include only requested changes
- Be testable independently

AI responses MUST:

- List documents consulted
- Note any conflicts found
- Identify risks or concerns
- Suggest verification steps

---

## 8. Exception Handling

### 8.1 When Rules May Be Broken

Rules may be broken ONLY when:

1. **Emergency fix** — production is broken
2. **Security patch** — vulnerability discovered
3. **Explicit approval** — documented decision to deviate

### 8.2 Exception Documentation Requirements

Any exception MUST be documented with:

- **What rule was broken**
- **Why it was necessary**
- **Who approved** (if applicable)
- **Recovery plan** — how to return to compliance

### 8.3 Exception Format

Add to commit message or `DEV_NOTES.md`:

```
EXCEPTION: <rule violated>
REASON: <justification>
APPROVED: <name/role> (if applicable)
RECOVERY: <plan to fix>
DATE: YYYY-MM-DD
```

### 8.4 Recovery Procedure

After emergency exception:

1. **Document** exception within 24 hours
2. **Create** follow-up task to return to compliance
3. **Review** if rule needs amendment
4. **Update** this document if rule is problematic

---

## 9. Document Registry & Roles

### 9.1 Markdown Files in Repository (20 verified)

**Verify:** `Get-ChildItem -Path . -Recurse -Filter "*.md" | Select-Object FullName`

| File                                                        | Role                               | Status                      |
| ----------------------------------------------------------- | ---------------------------------- | --------------------------- |
| `README.md`                                                 | Project overview, setup            | Active                      |
| `DEV_NOTES.md`                                              | Development history, module status | Active (frequently updated) |
| `DEPLOYMENT_WORKFLOW.md`                                    | NAS deployment procedure           | Active                      |
| `SECURITY.md`                                               | Secret handling procedures         | Active                      |
| `UI_BASELINE_V1.md`                                         | UI design system foundation        | STRUCTURAL FREEZE           |
| `B2 - Global UI Elements.md`                                | Component standardization          | Active                      |
| `tests/README.md`                                           | PHPUnit test guidance              | Active                      |
| `scripts/README_playwright.md`                              | Visual regression helpers          | Active                      |
| `docs/ENV_FORMAT_CHECK.md`                                  | .env validation                    | Active                      |
| `docs/js-hooks.md`                                          | JavaScript selector contracts      | AUTHORITATIVE               |
| `docs/ui-migration.md`                                      | UI migration rules & status        | AUTHORITATIVE               |
| `docs/UI_MIGRATION_AUDIT_2026-01-19.md`                     | Migration compliance audit         | Audit (dated)               |
| `docs/UI_CSS_JS_AUDIT_2026-01-19.md`                        | CSS/JS quality audit               | Audit (dated)               |
| `docs/product/dashboard/01_dashboard-freeze-spec.md`        | Dashboard product decisions        | FINAL / LOCKED              |
| `docs/product/dashboard/02_transition-checklist.md`         | Dashboard implementation guardrail | Active                      |
| `docs/product/dashboard/03_edge-case-behavior.md`           | Dashboard edge cases               | Active                      |
| `docs/product/dashboard/04_ui-guardrail-rules.md`           | Dashboard UI constraints           | Active                      |
| `docs/product/dashboard/05_component-responsibility-map.md` | Dashboard architecture             | Active                      |
| `docs/product/dashboard/06_layout-skeleton-notes.md`        | Dashboard layout contract          | LOCKED                      |

### 9.2 Document Conflicts Identified

| Conflict        | Resolution                                   |
| --------------- | -------------------------------------------- |
| None identified | Documents are complementary, not conflicting |

### 9.3 Documentation Gaps Identified

| Gap                     | Risk   | Recommendation                             |
| ----------------------- | ------ | ------------------------------------------ |
| No CONTRIBUTING.md      | Low    | Consider adding for external contributors  |
| No CHANGELOG.md         | Medium | `DEV_NOTES.md` serves this role informally |
| No API documentation    | Low    | Backend-focused app, not API-first         |
| No test coverage report | Medium | Consider adding to CI                      |

---

## 10. Gap & Risk Analysis

### 10.1 Current State Summary

#### Governance: GOOD

- Clear documentation hierarchy exists
- UI migration rules are well-defined
- Dashboard product spec is locked
- JS hooks are documented

#### Codebase: STABLE

- P0-P2 views migrated to tr-\* system
- CSS architecture is layered and organized
- No critical JS/CSS regressions detected
- Legacy aliases are documented and intentional

#### Process: PARTIALLY DEFINED

- Commit conventions observed but not formally documented
- No formal code review process documented
- Testing exists but coverage unknown

### 10.2 Known Risks

| Risk                                          | Severity  | Mitigation                                      |
| --------------------------------------------- | --------- | ----------------------------------------------- |
| P3-P5 views still have legacy classes         | Low       | Defer until views finalized                     |
| POS touchscreen complexity (many DOM queries) | Medium    | Dedicated regression testing                    |
| No automated UI regression testing            | Medium    | Playwright scripts exist, not CI-integrated     |
| Migration files could be edited accidentally  | Medium    | This document prohibits it                      |
| `!important` usage in CSS                     | Low       | Audit shows most are justified (resets, modals) |
| `.env` format errors break app                | Low       | CI check exists (`env-format-check.yml`)        |
| Secrets in history (historical)               | Mitigated | See `DEV_NOTES.md` for purge documentation      |

### 10.3 Areas Not Yet Governed

| Area                   | Current State            | Recommendation                 |
| ---------------------- | ------------------------ | ------------------------------ |
| Code review process    | Not documented           | Optional: Add PR checklist     |
| Performance monitoring | Not documented           | Optional: Add APM guidance     |
| Backup procedures      | Partial (deployment doc) | Optional: Formalize            |
| Error monitoring       | Not documented           | Optional: Add logging guidance |

### 10.4 Optional / Future Improvements

These are NOT required but may improve workflow:

1. **Automated UI Regression** — Integrate Playwright into CI
2. **Test Coverage Reporting** — Add PHPUnit coverage to CI
3. **PR Template** — Create `.github/PULL_REQUEST_TEMPLATE.md`
4. **CHANGELOG.md** — Formalize version history
5. **Pre-commit Hooks** — Automate format/lint checks
6. **Extract POS Inline JS** — Move queries to external file (verify count first)

---

## 11. AI Prompt Header Template (MUST)

Copy and paste this header at the start of any AI prompt for this repository:

````markdown
## MANDATORY PRE-READ (DO FIRST)

Before executing ANY task on this repository:

1. Read `docs/PROJECT_WORKFLOW.md` (governance rules)
2. Read `DEV_NOTES.md` (current module status, recent changes)
3. If touching UI/CSS/JS: also read `docs/js-hooks.md` and `docs/ui-migration.md`
4. If touching dashboard: also read `docs/product/dashboard/*.md`

## VERIFY BEFORE CHANGING

Run these commands to verify current state:

```powershell
# Verify file exists before editing
Test-Path "<file-path>"

# Verify CSS/JS locations (do not assume)
Get-ChildItem -Path public -Recurse -Filter "*.css" | Select-Object FullName
Get-ChildItem -Path public -Recurse -Filter "*.js" | Select-Object FullName

# Verify migration naming convention
Get-ChildItem -Path "app/Database/Migrations" -Filter "*.php" | Select-Object -Last 5 Name

# Verify branches
git branch -a
```
````

## RULES

- DO NOT assume file paths—search first
- DO NOT assume framework conventions—verify in codebase
- DO NOT modify P0-P2 migrated views without explicit bug fix reason
- DO NOT edit existing migration files
- DO NOT rename JS hook selectors (see `docs/js-hooks.md`)
- Cite file paths and line numbers when referencing code
- Work on ONE file per task for UI changes

````

---

## Appendix A: Quick Reference Commands

### Development

```powershell
# Run migrations
php spark migrate

# Rollback last migration
php spark migrate:rollback

# Seed demo data
php spark db:seed DatabaseSeeder

# Clear cache
php spark cache:clear

# Run tests (Windows)
vendor\bin\phpunit

# Run tests (Unix)
./phpunit
````

### Git Workflow

```powershell
# Check status
git status

# Stage specific files
git add <file>

# Commit with message
git commit -m "type(scope): description"

# Push to master
git push origin master

# Promote to deployment
git checkout deploy/nas
git merge --no-ff master
git push origin deploy/nas
git checkout master
```

### Validation

```powershell
# Check .env format
php scripts/check_env_format.php

# Verify CSS file locations (do not assume paths)
Get-ChildItem -Path public -Recurse -Filter "*.css" | ForEach-Object { $_.FullName -replace [regex]::Escape((Get-Location).Path + '\'),'' }

# Verify JS file locations
Get-ChildItem -Path public -Recurse -Filter "*.js" | ForEach-Object { $_.FullName -replace [regex]::Escape((Get-Location).Path + '\'),'' }

# Verify migration naming convention
Get-ChildItem -Path "app/Database/Migrations" -Filter "*.php" | Sort-Object Name | Select-Object -Last 5 Name

# Search for legacy classes in views
Select-String -Path "app/Views/**/*.php" -Pattern '\.(btn|table|badge|pill)[^-]'
```

---

## Appendix B: Document Update Log

| Date       | Change                                                        | Author   |
| ---------- | ------------------------------------------------------------- | -------- |
| 2026-01-19 | Initial creation from repository audit                        | AI Audit |
| 2026-01-19 | Evidence-based validation; add `PROJECT_WORKFLOW_EVIDENCE.md` | AI Audit |

---

**END OF DOCUMENT**

This document MUST be read before any work on this repository.
When in doubt, follow the rules. When rules conflict, consult the authority hierarchy.
