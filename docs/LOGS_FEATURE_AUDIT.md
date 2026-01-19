# LOGS_FEATURE_AUDIT.md — Automatic Logging Feature Audit

**Repository:** Temu Rasa POS (CodeIgniter 4)  
**Audit Date:** 2026-01-19  
**Status:** READ-ONLY AUDIT — No implementation changes  
**Evidence:** See `docs/LOGS_FEATURE_EVIDENCE.md` for raw data

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Authentication & User Identity Findings](#2-authentication--user-identity-findings)
3. [Existing Audit Log Capability](#3-existing-audit-log-capability)
4. [Central Hook Points Analysis](#4-central-hook-points-analysis)
5. [Modules Coverage Map](#5-modules-coverage-map)
6. [Email/Notification Logging](#6-emailnotification-logging)
7. [Database Feasibility](#7-database-feasibility)
8. [Risks & Unknowns](#8-risks--unknowns)
9. [Recommended Next Steps](#9-recommended-next-steps)

---

## 1. Executive Summary

### What Exists

| Component                   | Status         | Evidence                                    |
| --------------------------- | -------------- | ------------------------------------------- |
| Session-based auth (custom) | ✅ Implemented | `AuthFilter.php`, `Login.php`               |
| User identity in session    | ✅ Available   | `user_id`, `username`, `role`, `isLoggedIn` |
| Audit log table             | ✅ Exists      | `audit_logs` with JSON payload              |
| Partial audit logging       | ⚠️ Scattered   | ~7 controllers have manual logging          |
| CodeIgniter Events          | ⚠️ Minimal     | Only `pre_system` hook for debug toolbar    |
| Filters infrastructure      | ✅ Ready       | `AuthFilter`, `RoleFilter` exist            |
| Service layer               | ⚠️ Limited     | Only 2 services exist                       |

### What's Missing

| Gap                              | Impact                                          | Priority |
| -------------------------------- | ----------------------------------------------- | -------- |
| No centralized logging mechanism | High — logging is copy/paste per controller     | P0       |
| Security events not logged       | High — login/logout/reset not tracked           | P0       |
| Incomplete module coverage       | Medium — Sales, Purchases, Overheads not logged | P1       |
| No "Logs" menu item              | Low — exists as "Audit Log" under Master        | P2       |
| Email send events not logged     | Low — only ForgotPassword sends email           | P2       |

### Key Findings

1. **Authentication is CUSTOM** — No Myth\Auth or external library. Session stores `user_id`, `username`, `role`, `isLoggedIn`.
2. **User ID is RELIABLY available** for all authenticated routes via `session('user_id')`.
3. **Existing audit_logs table** already supports JSON payload and user tracking — can be extended.
4. **Best centralized hook point:** Combination of **Filter (after)** + **Trait/Helper** pattern.

---

## 2. Authentication & User Identity Findings

### 2.1 Session Keys Used

**Location:** [app/Controllers/Auth/Login.php#L53-L61](app/Controllers/Auth/Login.php#L53-L61)

```php
$session->set([
    'user_id'     => $user['id'],
    'username'    => $user['username'],
    'full_name'   => $user['full_name'],
    'role_id'     => $user['role_id'],
    'role'        => $roleName,
    'role_name'   => $roleName,
    'isLoggedIn'  => true,
]);
```

**DEFINITIVE:** Every authenticated request has these session keys available.

### 2.2 How Controllers Retrieve User Today

| Controller         | Pattern Used         | Line       |
| ------------------ | -------------------- | ---------- |
| Users.php          | `session('user_id')` | L133, L229 |
| MenuCategories.php | `session('user_id')` | L203       |
| Products.php       | `session('user_id')` | L211       |
| RawMaterials.php   | `session('user_id')` | L350       |
| Recipes.php        | `session('user_id')` | L468       |
| Suppliers.php      | `session('user_id')` | L189       |
| Units.php          | `session('user_id')` | L176       |

**Pattern:** `$userId = (int) (session('user_id') ?? 0);`

### 2.3 Public/Unauthenticated Endpoints

**Routes without auth filter:** (from [app/Config/Routes.php#L19-L27](app/Config/Routes.php#L19-L27))

| Route                 | Controller                 | Purpose              |
| --------------------- | -------------------------- | -------------------- |
| `GET /login`          | Auth\Login::index          | Login form           |
| `POST /login/attempt` | Auth\Login::attempt        | Login action         |
| `GET /logout`         | Auth\Logout::index         | Logout action        |
| `GET /auth/forgot`    | Auth\ForgotPassword::index | Forgot password form |
| `POST /auth/forgot`   | Auth\ForgotPassword::send  | Send reset email     |
| `GET /auth/reset`     | Auth\ResetPassword::index  | Reset password form  |
| `POST /auth/reset`    | Auth\ResetPassword::update | Update password      |
| `GET /app/playground` | App\Playground::index      | Dev playground       |

**Note:** These routes do NOT have `user_id` in session (except logout which destroys it).

### 2.4 Auth Flow Locations

| Event           | Controller          | Method    | File                                                                                       |
| --------------- | ------------------- | --------- | ------------------------------------------------------------------------------------------ |
| Login attempt   | Auth\Login          | attempt() | [app/Controllers/Auth/Login.php#L23-L65](app/Controllers/Auth/Login.php)                   |
| Logout          | Auth\Logout         | index()   | [app/Controllers/Auth/Logout.php#L10-L13](app/Controllers/Auth/Logout.php)                 |
| Forgot password | Auth\ForgotPassword | send()    | [app/Controllers/Auth/ForgotPassword.php#L32-L73](app/Controllers/Auth/ForgotPassword.php) |
| Reset password  | Auth\ResetPassword  | update()  | [app/Controllers/Auth/ResetPassword.php#L50-L93](app/Controllers/Auth/ResetPassword.php)   |

---

## 3. Existing Audit Log Capability

### 3.1 Database Schema

**Table:** `audit_logs`  
**Location:** [app/Database/Migrations/2025-12-10-120125_CreateAuditLogsTable.php](app/Database/Migrations/2025-12-10-120125_CreateAuditLogsTable.php)

| Column      | Type                        | Purpose                |
| ----------- | --------------------------- | ---------------------- |
| id          | INT UNSIGNED AUTO_INCREMENT | PK                     |
| entity_type | VARCHAR(50)                 | Module/entity name     |
| entity_id   | INT UNSIGNED NULL           | Record ID              |
| action      | VARCHAR(20)                 | create/update/delete   |
| description | TEXT NULL                   | Human-readable summary |
| payload     | JSON (LONGTEXT with CHECK)  | Full snapshot data     |
| user_id     | INT UNSIGNED NULL           | Actor who made change  |
| created_at  | DATETIME                    | Timestamp              |

**Indexes:** entity_type, entity_id, user_id

### 3.2 Model

**Location:** [app/Models/AuditLogModel.php](app/Models/AuditLogModel.php)

```php
protected $allowedFields = [
    'entity_type', 'entity_id', 'action', 'description', 'payload', 'user_id', 'created_at'
];
```

### 3.3 Current Coverage

| Controller                 | Audit Logging | Entity Types Logged |
| -------------------------- | ------------- | ------------------- |
| Users.php                  | ✅ Yes        | `user`              |
| MenuCategories.php         | ✅ Yes        | `menu_category`     |
| Products.php               | ✅ Yes        | `menu`              |
| RawMaterials.php           | ✅ Yes        | `raw_material`      |
| Recipes.php                | ✅ Yes        | `recipe`            |
| Suppliers.php              | ✅ Yes        | `supplier`          |
| Units.php                  | ✅ Yes        | `unit`              |
| **Customers.php**          | ❌ No         | -                   |
| **MenuOptions.php**        | ❌ No         | -                   |
| **Sales.php**              | ❌ No         | -                   |
| **Purchases.php**          | ❌ No         | -                   |
| **Overheads.php**          | ❌ No         | -                   |
| **OverheadsPayroll.php**   | ❌ No         | -                   |
| **OverheadCategories.php** | ❌ No         | -                   |

### 3.4 Existing Viewer

**Location:** [app/Controllers/AuditLogs.php](app/Controllers/AuditLogs.php) + [app/Views/audit_logs/audit_logs_index.php](app/Views/audit_logs/audit_logs_index.php)

- Route: `GET /audit-logs`
- Menu: Exists under "Master" section as "Audit Log"
- Features: Filter by entity_type, date range, text search
- Limitation: Only shows entity_type = menu, recipe in dropdown

---

## 4. Central Hook Points Analysis

### 4.1 Candidate Comparison

| Hook Point          | Pros                                                    | Cons                                                             | Recommendation   |
| ------------------- | ------------------------------------------------------- | ---------------------------------------------------------------- | ---------------- |
| **BaseController**  | Central, all controllers extend it                      | Must override initController(); doesn't know action outcome      | ⭐⭐ Secondary   |
| **Filter (after)**  | Runs after controller; can see response; central config | Can't see internal data (payload); needs request context parsing | ⭐⭐⭐ Primary   |
| **Events**          | Decoupled; can add multiple listeners                   | Requires explicit emit in each action; not auto                  | ⭐⭐ Alternative |
| **Model callbacks** | Auto for Model operations; granular                     | Many models; doesn't cover non-Model actions                     | ⭐ Limited       |
| **Trait/Helper**    | Reusable; explicit call in controller                   | Still requires manual call per action                            | ⭐⭐⭐ Primary   |

### 4.2 Recommended Approach: Hybrid

**Best strategy for this repo:**

1. **AuditLogTrait** or **AuditLogService** — Encapsulate logging logic (create/update/delete with snapshot)
2. **Filter (after)** — For security events (login success/fail, logout, password reset)
3. **Explicit calls in controllers** — Existing pattern works; just needs standardization

**Why not pure Filter-based?**

- Filter can't access internal variables (`$payload`, entity state before/after)
- Controller knows context best

**Why not Model callbacks?**

- Would need to add to every Model
- Doesn't capture non-Model operations (e.g., batch updates via QueryBuilder)
- Already using direct `->insert()` in controllers

### 4.3 Filter Infrastructure Status

**Location:** [app/Config/Filters.php](app/Config/Filters.php)

```php
public array $aliases = [
    // ... standard filters ...
    'auth' => \App\Filters\AuthFilter::class,
    'role' => \App\Filters\RoleFilter::class,
];
```

**Custom filters exist:**

- `AuthFilter` — Checks `isLoggedIn` session
- `RoleFilter` — Checks role permissions

**Gap:** No `after` filter for logging. Can add one.

### 4.4 Events Infrastructure Status

**Location:** [app/Config/Events.php](app/Config/Events.php)

Only `pre_system` hook exists for debug toolbar. **No custom events defined.**

Could add:

- `Events::trigger('user.login', $userId)`
- `Events::trigger('audit.log', $payload)`

---

## 5. Modules Coverage Map

### 5.1 All Controllers (31 total)

**Master Data (8):**
| Controller | Routes | Has Audit | Actions |
|------------|--------|-----------|---------|
| Products | /master/products/_ | ✅ | CRUD |
| MenuCategories | /master/categories/_ | ✅ | CRUD |
| RawMaterials | /master/raw-materials/_ | ✅ | CRUD |
| Recipes | /master/recipes/_ | ✅ | CRUD |
| Suppliers | /master/suppliers/_ | ✅ | CRUD |
| Units | /master/units/_ | ✅ | CRUD |
| Customers | /master/customers/_ | ❌ | CRUD |
| MenuOptions | /master/menu-options/_ | ❌ | Save (bulk) |

**Transactions (2):**
| Controller | Routes | Has Audit | Actions |
|------------|--------|-----------|---------|
| Sales | /transactions/sales/_ | ❌ | CRUD + void + kitchen |
| Purchases | /purchases/_ | ❌ | CRUD |

**Overhead (3):**
| Controller | Routes | Has Audit | Actions |
|------------|--------|-----------|---------|
| Overheads | /overheads/_ | ❌ | CR |
| OverheadCategories | /overhead-categories/_ | ❌ | CRUD + toggle |
| OverheadsPayroll | /overheads/payroll/\* | ❌ | CRUD |

**Inventory (3):**
| Controller | Routes | Has Audit | Actions |
|------------|--------|-----------|---------|
| StockMovements | /inventory/stock-movements | N/A | Read-only |
| StockAdjustments | /inventory/stock-adjustments | N/A | Stub |
| StockOpname | /inventory/stock-opname | N/A | Stub |

**Auth (4):**
| Controller | Routes | Has Audit | Actions |
|------------|--------|-----------|---------|
| Login | /login, /login/attempt | ❌ | Login |
| Logout | /logout | ❌ | Logout |
| ForgotPassword | /auth/forgot | ❌ | Request reset |
| ResetPassword | /auth/reset | ❌ | Reset password |

**Other (4):**
| Controller | Routes | Has Audit | Actions |
|------------|--------|-----------|---------|
| Dashboard | / | N/A | Read-only |
| Users | /users/\* | ✅ | CRUD |
| AuditLogs | /audit-logs | N/A | Read-only |
| POS Touchscreen | /pos/touch | N/A | Read-only |

---

## 6. Email/Notification Logging

### 6.1 Email Configuration

**Location:** [app/Config/Email.php](app/Config/Email.php)

- Protocol: `mail` (default) or SMTP configurable
- Supports SMTP auth via env variables

### 6.2 Where Email is Sent

| Location                                                                        | Purpose             | Logged? |
| ------------------------------------------------------------------------------- | ------------------- | ------- |
| [ForgotPassword.php#L79-L100](app/Controllers/Auth/ForgotPassword.php#L79-L100) | Password reset link | ❌ No   |

**Only one place sends email** — `ForgotPassword::sendEmail()`

**Recommendation:** Add audit log entry when email is sent successfully.

---

## 7. Database Feasibility

### 7.1 Engine & JSON Support

- **Engine:** InnoDB (MariaDB/MySQL)
- **Charset:** utf8mb4
- **JSON:** Already using `JSON` type with `CHECK (json_valid(payload))`

**Evidence from SQL dump:**

```sql
`payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`))
```

### 7.2 Full Snapshot Storage

**Current approach:** `payload` column stores JSON snapshot

**Example from existing data:**

```json
{
    "recipe": { "yield_qty": 1, "yield_unit": "porsi", "notes": null },
    "items": [{ "raw_material_id": 1, "qty": 10, "waste_pct": 2, "note": "" }]
}
```

**Feasibility:** ✅ JSON payload already works well. Can store full entity snapshots.

---

## 8. Risks & Unknowns

### 8.1 Known Risks

| Risk                                    | Severity | Mitigation                                                     |
| --------------------------------------- | -------- | -------------------------------------------------------------- |
| Logging in transactions may slow writes | Low      | JSON encoding is fast; consider async queue for high volume    |
| Sensitive data in payload               | Medium   | Exclude password_hash (already done); review before production |
| Large payloads for complex entities     | Low      | Consider max size limit or compression                         |

### 8.2 Unknowns

| Item                     | Status  | Action Needed                             |
| ------------------------ | ------- | ----------------------------------------- |
| CLI/cron jobs            | UNKNOWN | Verify if any spark commands need logging |
| Webhooks/API endpoints   | UNKNOWN | No evidence of external API; verify       |
| Concurrent user handling | UNKNOWN | Test session isolation under load         |

---

## 9. Recommended Next Steps

### Phase 1: Foundation (No code changes needed for planning)

1. ✅ **DONE:** Audit complete — this document
2. **Define logging scope:** Which entity types and actions to log
3. **Design schema extension:** If needed for security events (login, logout)

### Phase 2: Implementation Plan (Future)

1. **Create AuditLogService or Trait:**
    - Method: `logAction(entityType, entityId, action, payload, description)`
    - Automatically inject `user_id` and `created_at`

2. **Add security event logging:**
    - In `Login::attempt()` — log success/failure
    - In `Logout::index()` — log logout
    - In `ForgotPassword::send()` — log request
    - In `ResetPassword::update()` — log password change

3. **Extend existing controllers:**
    - Add logging to: Customers, MenuOptions, Sales, Purchases, Overheads, OverheadsPayroll, OverheadCategories

4. **Enhance AuditLogs viewer:**
    - Add entity_type dropdown options for new entities
    - Add "Security" category filter
    - Optionally add username display (join with users table)

### Phase 3: Menu/UI

1. **Rename/move menu item:**
    - Current: "Audit Log" under Master
    - Proposed: "Logs" as top-level section or under Settings
    - Sub-items: "Activity Log", "Security Log"

---

## Document Control

| Version | Date       | Author   | Changes       |
| ------- | ---------- | -------- | ------------- |
| 1.0     | 2026-01-19 | AI Audit | Initial audit |

---

_This document is read-only audit output. No code changes were made._
