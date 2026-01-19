# LOGS_FEATURE_EVIDENCE.md — Raw Audit Evidence

**Repository:** Temu Rasa POS (CodeIgniter 4)  
**Audit Date:** 2026-01-19  
**Related Document:** `docs/LOGS_FEATURE_AUDIT.md`

---

## Table of Contents

1. [Evidence Index](#evidence-index)
2. [A. Authentication & User Identity](#a-authentication--user-identity)
3. [B. Existing Audit Log Infrastructure](#b-existing-audit-log-infrastructure)
4. [C. Central Hook Points](#c-central-hook-points)
5. [D. Routes & Modules](#d-routes--modules)
6. [E. Email Infrastructure](#e-email-infrastructure)
7. [F. Database Schema](#f-database-schema)
8. [G. Controller Audit Status](#g-controller-audit-status)

---

## Evidence Index

| Finding                                   | Evidence Section                          |
| ----------------------------------------- | ----------------------------------------- |
| Session stores user_id, username, role    | [A.1](#a1-session-keys-set-on-login)      |
| No Myth\Auth library used                 | [A.3](#a3-search-for-auth-libraries)      |
| AuthFilter checks isLoggedIn              | [A.2](#a2-authfilter-implementation)      |
| audit_logs table exists with JSON payload | [B.1](#b1-audit-logs-migration)           |
| 7 controllers have manual audit logging   | [G.1](#g1-controllers-with-audit-logging) |
| Events.php only has pre_system hook       | [C.2](#c2-eventsphp-content)              |
| BaseController is minimal                 | [C.1](#c1-basecontroller-content)         |
| Public routes without auth filter         | [D.1](#d1-public-routes)                  |
| Email sent only in ForgotPassword         | [E.1](#e1-email-usage-search)             |
| DB uses InnoDB with JSON support          | [F.1](#f1-database-schema-from-sql-dump)  |

---

## A. Authentication & User Identity

### A.1 Session Keys Set on Login

**File:** `app/Controllers/Auth/Login.php`  
**Lines:** 53-61

```php
// Simpan ke session
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

### A.2 AuthFilter Implementation

**File:** `app/Filters/AuthFilter.php`  
**Lines:** 1-26 (complete)

```php
<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return redirect()
                ->to(site_url('login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak digunakan
    }
}
```

### A.3 Search for Auth Libraries

**Command:**

```powershell
Select-String -Path "c:\ci4-local\app\**\*.php" -Pattern "Myth\\Auth|auth\(\)|currentUser\(\)|user\(\)"
```

**Output:**

```
app\Controllers\OverheadsPayroll.php:37:            ->withUser()
app\Models\PayrollModel.php:24:    public function withUser()
```

**Finding:** No Myth\Auth or global auth helper functions. `withUser()` is a Model join method, not auth.

### A.4 Session Usage in Controllers

**Command:**

```powershell
Select-String -Path "c:\ci4-local\app\Controllers\**\*.php" -Pattern "session\('user_id|session\('username"
```

**Output (key matches):**

```
Users.php:133: $currentUserId = (int) (session('user_id') ?? 0);
Users.php:229: $actorId = (int) (session('user_id') ?? 0);
```

### A.5 RoleFilter Implementation

**File:** `app/Filters/RoleFilter.php`  
**Lines:** 14-18

```php
public function before(RequestInterface $request, $arguments = null)
{
    $session = session();
    $role    = strtolower((string) ($session->get('role') ?? $session->get('role_name') ?? ''));

    if (! $role) {
        return redirect()->to(site_url('login'));
    }
    // ... role checking logic
}
```

---

## B. Existing Audit Log Infrastructure

### B.1 Audit Logs Migration

**File:** `app/Database/Migrations/2025-12-10-120125_CreateAuditLogsTable.php`  
**Lines:** 1-64

```php
public function up()
{
    $this->forge->addField([
        'id' => [
            'type'           => 'INT',
            'constraint'     => 11,
            'unsigned'       => true,
            'auto_increment' => true,
        ],
        'entity_type' => [
            'type'       => 'VARCHAR',
            'constraint' => 50,
        ],
        'entity_id' => [
            'type'       => 'INT',
            'constraint' => 11,
            'unsigned'   => true,
            'null'       => true,
        ],
        'action' => [
            'type'       => 'VARCHAR',
            'constraint' => 20,
        ],
        'description' => [
            'type' => 'TEXT',
            'null' => true,
        ],
        'payload' => [
            'type' => 'JSON',
            'null' => true,
        ],
        'user_id' => [
            'type'       => 'INT',
            'constraint' => 11,
            'unsigned'   => true,
            'null'       => true,
        ],
        'created_at' => [
            'type' => 'DATETIME',
            'null' => false,
        ],
    ]);

    $this->forge->addKey('id', true);
    $this->forge->addKey('entity_type');
    $this->forge->addKey('entity_id');
    $this->forge->addKey('user_id');
    $this->forge->createTable('audit_logs');
}
```

### B.2 AuditLogModel

**File:** `app/Models/AuditLogModel.php`  
**Lines:** 1-24 (complete)

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table         = 'audit_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = false;

    protected $allowedFields = [
        'entity_type',
        'entity_id',
        'action',
        'description',
        'payload',
        'user_id',
        'created_at',
    ];
}
```

### B.3 AuditLogs Controller

**File:** `app/Controllers/AuditLogs.php`  
**Lines:** 1-52 (complete)

```php
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

class AuditLogs extends BaseController
{
    protected AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
    }

    public function index()
    {
        $entityType = $this->request->getGet('entity_type') ?: null;
        $dateFrom   = $this->request->getGet('date_from') ?: null;
        $dateTo     = $this->request->getGet('date_to') ?: null;

        $builder = $this->auditLogModel
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC');

        if ($entityType) {
            $builder->where('entity_type', $entityType);
        }
        if ($dateFrom) {
            $builder->where('DATE(created_at) >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('DATE(created_at) <=', $dateTo);
        }

        $logs = $builder->findAll(200);

        $data = [
            'title'       => 'Audit Log',
            'subtitle'    => 'Jejak perubahan menu & resep',
            'logs'        => $logs,
            'entityType'  => $entityType,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
        ];

        return view('audit_logs/audit_logs_index', $data);
    }
}
```

### B.4 Existing Audit Log Data Sample

**File:** `pos_cafe.sql`  
**Lines:** 41-62

```sql
INSERT INTO `audit_logs` (`id`, `entity_type`, `entity_id`, `action`, `description`, `payload`, `user_id`, `created_at`) VALUES
    (1, 'recipe', 1, 'update', 'Recipe update for menu #', '{"recipe":{"yield_qty":1,"yield_unit":"porsi","notes":null},"items":[{"raw_material_id":1,"qty":10,"waste_pct":2,"note":""}]}', 1, '2025-12-10 05:21:04'),
    (3, 'menu', 7, 'create', 'Menu create #7', '{"name":"Kopi Campur","menu_category_id":1,"sku":"COF-MIX","price":8000,"is_active":1}', 1, '2025-12-12 08:37:08'),
    (10, 'user', 5, 'create', 'User create #5', '{"username":"GSG","full_name":"Grangsang Sotyarmadhani","email":"grangsang1991@gmail.com","role_id":1,"active":1}', 1, '2025-12-25 12:30:24'),
    -- ... 19 total rows
```

---

## C. Central Hook Points

### C.1 BaseController Content

**File:** `app/Controllers/BaseController.php`  
**Lines:** 1-56 (complete)

```php
<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // E.g.: $this->session = service('session');
    }
}
```

**Finding:** BaseController is stock CI4 template — no custom initialization.

### C.2 Events.php Content

**File:** `app/Config/Events.php`  
**Lines:** 1-54 (complete)

```php
<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\HotReloader\HotReloader;

Events::on('pre_system', static function (): void {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn ($buffer) => $buffer);
    }

    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        service('toolbar')->respond();
        if (ENVIRONMENT === 'development') {
            service('routes')->get('__hot-reload', static function (): void {
                (new HotReloader())->run();
            });
        }
    }
});
```

**Finding:** Only `pre_system` hook for debug toolbar. No custom events.

### C.3 Filters.php Configuration

**File:** `app/Config/Filters.php`  
**Lines:** 26-39

```php
public array $aliases = [
    'csrf'          => CSRF::class,
    'toolbar'       => DebugToolbar::class,
    'honeypot'      => Honeypot::class,
    'invalidchars'  => InvalidChars::class,
    'secureheaders' => SecureHeaders::class,
    'cors'          => Cors::class,
    'forcehttps'    => ForceHTTPS::class,
    'pagecache'     => PageCache::class,
    'performance'   => PerformanceMetrics::class,
    'auth'          => \App\Filters\AuthFilter::class,
    'role'          => \App\Filters\RoleFilter::class,
];
```

### C.4 Model Callbacks Search

**Command:**

```powershell
Select-String -Path "c:\ci4-local\app\Models\**\*.php" -Pattern "beforeInsert|beforeUpdate|beforeDelete|afterInsert|afterUpdate|afterDelete"
```

**Output:** No matches found.

**Finding:** No Model callbacks currently in use.

---

## D. Routes & Modules

### D.1 Public Routes

**File:** `app/Config/Routes.php`  
**Lines:** 19-27

```php
// Public Routes (Tanpa Auth)
$routes->get('login',         'Auth\Login::index');
$routes->post('login/attempt', 'Auth\Login::attempt');
$routes->get('logout',        'Auth\Logout::index');
$routes->get('auth/forgot',   'Auth\ForgotPassword::index');
$routes->post('auth/forgot',  'Auth\ForgotPassword::send');
$routes->get('auth/reset',    'Auth\ResetPassword::index');
$routes->post('auth/reset',   'Auth\ResetPassword::update');
```

### D.2 Protected Routes Structure

**File:** `app/Config/Routes.php`  
**Lines:** 38-237

```php
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->group('', ['filter' => 'role'], static function ($routes) {
        // Dashboard
        $routes->get('/',          'Dashboard::index');
        $routes->get('/dashboard', 'Dashboard::index');

        // Master Data
        $routes->group('master', ['namespace' => 'App\Controllers\Master'], static function ($routes) {
            // Products, Categories, Raw Materials, Customers, Suppliers, Units
        });

        // Master Recipes
        $routes->group('master/recipes', ...);

        // Transactions - Purchases
        $routes->group('purchases', ...);

        // Transactions - Sales
        $routes->group('transactions', ['filter' => 'auth'], ...);

        // Inventory
        $routes->get('inventory/stock-movements', ...);
        $routes->get('inventory/stock-card', ...);
        $routes->get('inventory/stock-adjustments', ...);
        $routes->get('inventory/stock-opname', ...);

        // Reports
        $routes->get('reports/sales/*', ...);
        $routes->get('reports/purchases/*', ...);
        $routes->get('reports/stock/*', ...);

        // Overheads
        $routes->get('overheads', ...);
        $routes->get('overhead-categories', ...);
        $routes->get('overheads/payroll', ...);

        // Users (Owner/Auditor only)
        $routes->group('users', ['filter' => 'role:owner,auditor'], ...);

        // Audit Logs
        $routes->get('audit-logs', 'AuditLogs::index');

        // POS Touchscreen
        $routes->get('pos/touch', 'Pos\Touchscreen::index');
    });
});
```

### D.3 All Controllers List

**Command:**

```powershell
Get-ChildItem -Recurse "c:\ci4-local\app\Controllers" -Filter "*.php" | Select-Object -ExpandProperty Name
```

**Output:**

```
AuditLogs.php
BaseController.php
BrandGuide.php
Dashboard.php
Guides.php
Home.php
OverheadCategories.php
Overheads.php
OverheadsPayroll.php
Users.php
Playground.php
ForgotPassword.php
Login.php
Logout.php
ResetPassword.php
StockAdjustments.php
StockMovements.php
StockOpname.php
Customers.php
MenuCategories.php
MenuOptions.php
Products.php
RawMaterials.php
Recipes.php
Suppliers.php
Units.php
Touchscreen.php
PurchaseSummary.php
SalesSummary.php
StockSummary.php
Purchases.php
Sales.php
```

---

## E. Email Infrastructure

### E.1 Email Usage Search

**Command:**

```powershell
Select-String -Path "c:\ci4-local\app\**\*.php" -Pattern "Services::email|setTo\(|setSubject\(|->send\("
```

**Key Output:**

```
ForgotPassword.php:83:        $email  = Services::email();
ForgotPassword.php:89:        $email->setTo($toEmail);
ForgotPassword.php:90:        $email->setSubject('Reset Password POS');
ForgotPassword.php:98:            $email->send();
```

### E.2 ForgotPassword sendEmail Method

**File:** `app/Controllers/Auth/ForgotPassword.php`  
**Lines:** 79-100

```php
private function sendEmail(string $toEmail, string $link, int $expiresMinutes): void
{
    $config = config('Email');
    $email  = Services::email();

    $fromEmail = $config->fromEmail ?: 'no-reply@example.com';
    $fromName  = $config->fromName ?: 'POS System';
    $email->setFrom($fromEmail, $fromName);

    $email->setTo($toEmail);
    $email->setSubject('Reset Password POS');

    $body = "Anda menerima email ini karena ada permintaan reset password.\n\n";
    $body .= "Klik tautan berikut untuk mengatur ulang password (berlaku {$expiresMinutes} menit):\n";
    $body .= $link . "\n\n";
    $body .= "Jika Anda tidak meminta reset, abaikan email ini.";

    $email->setMessage($body);

    try {
        $email->send();
    } catch (\Throwable $e) {
        log_message('error', 'Gagal mengirim email reset password: {message}', ['message' => $e->getMessage()]);
    }
}
```

---

## F. Database Schema

### F.1 Database Schema from SQL Dump

**File:** `pos_cafe.sql`  
**Lines:** 23-37

```sql
-- Dumping structure for table pos_cafe.audit_logs
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(11) unsigned DEFAULT NULL,
  `action` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `user_id` int(11) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_type` (`entity_type`),
  KEY `entity_id` (`entity_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**Finding:** JSON type with CHECK constraint, InnoDB engine.

### F.2 All Tables

**Command:**

```powershell
Select-String -Path "c:\ci4-local\pos_cafe.sql" -Pattern "CREATE TABLE"
```

**Output (tables):**

```
audit_logs
brands
customers
menus
menu_categories
menu_options
menu_option_groups
migrations
overheads
overhead_categories
password_resets
payrolls
purchases
purchase_items
raw_materials
raw_material_variants
recipes
recipe_items
roles
sales
sale_items
sale_item_options
stock_movements
suppliers
units
users
```

---

## G. Controller Audit Status

### G.1 Controllers With Audit Logging

**Command:**

```powershell
Select-String -Path "c:\ci4-local\app\Controllers\**\*.php" -Pattern "auditLogModel|AuditLogModel" | Group-Object Filename
```

**Output:**

```
Users.php: 6 matches
MenuCategories.php: 5 matches
Products.php: 5 matches
RawMaterials.php: 7 matches
Recipes.php: 7 matches
Suppliers.php: 5 matches
Units.php: 5 matches
AuditLogs.php: 5 matches (viewer only)
```

### G.2 Sample Audit Log Call Pattern

**File:** `app/Controllers/Users.php`  
**Lines:** 223-239

```php
private function logUserChange(int $userId, string $action, array $payload): void
{
    unset($payload['password_hash']);

    $actorId = (int) (session('user_id') ?? 0);

    $this->auditLogModel->insert([
        'entity_type' => 'user',
        'entity_id'   => $userId,
        'action'      => $action,
        'description' => 'User ' . $action . ' #' . $userId,
        'payload'     => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'user_id'     => $actorId > 0 ? $actorId : null,
        'created_at'  => date('Y-m-d H:i:s'),
    ]);
}
```

### G.3 Controllers WITHOUT Audit Logging

**By exclusion from search results:**

| Controller         | File                       | Reason                           |
| ------------------ | -------------------------- | -------------------------------- |
| Customers          | Master/Customers.php       | Not implemented                  |
| MenuOptions        | Master/MenuOptions.php     | Not implemented                  |
| Sales              | Transactions/Sales.php     | Not implemented                  |
| Purchases          | Transactions/Purchases.php | Not implemented                  |
| Overheads          | Overheads.php              | Not implemented                  |
| OverheadCategories | OverheadCategories.php     | Not implemented                  |
| OverheadsPayroll   | OverheadsPayroll.php       | Not implemented                  |
| Login              | Auth/Login.php             | Security event - not implemented |
| Logout             | Auth/Logout.php            | Security event - not implemented |
| ForgotPassword     | Auth/ForgotPassword.php    | Security event - not implemented |
| ResetPassword      | Auth/ResetPassword.php     | Security event - not implemented |

---

## H. Sidebar Menu Reference

**File:** `app/Views/layouts/partials/sidebar.php`  
**Lines:** 85-88

```php
<?= $navLink('master/recipes', 'Resep', $menuAllowed('master'), $isActive(['master/recipes']), true); ?>
<?= $navLink('audit-logs', 'Audit Log', $menuAllowed('master'), $isActive(['audit-logs']), true); ?>
```

**Finding:** "Audit Log" menu item exists under "Master" section.

---

## I. Services Layer

**Directory:** `app/Services/`

**Contents:**

- `PdfService.php` — PDF generation
- `StockConsumptionService.php` — Stock deduction for sales

**Finding:** Minimal service layer. StockConsumptionService does not have audit logging.

---

## J. Helpers

**Directory:** `app/Helpers/`

**Contents:**

- `tr_daterange_helper.php` — Date range utilities

**Finding:** No auth helper. No audit helper.

---

## K. Common.php

**File:** `app/Common.php`  
**Lines:** 1-16 (complete)

```php
<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own.
 */
```

**Finding:** Empty — no global functions defined.

---

_End of Evidence Document_
