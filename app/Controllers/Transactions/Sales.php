<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\MenuModel;
use App\Models\RecipeModel;
use App\Models\RawMaterialModel;
use App\Models\StockMovementModel;
use App\Models\MenuOptionGroupModel;
use App\Models\MenuOptionModel;
use App\Models\OrderItemOptionModel;
use App\Models\CustomerModel;
use App\Services\StockConsumptionService;
use App\Services\AuditLogService;

class Sales extends BaseController
{
    protected SaleModel $saleModel;
    protected SaleItemModel $saleItemModel;
    protected MenuModel $menuModel;
    protected RecipeModel $recipeModel;
    protected RawMaterialModel $rawModel;
    protected StockMovementModel $movementModel;
    protected MenuOptionGroupModel $optionGroupModel;
    protected MenuOptionModel $optionModel;
    protected OrderItemOptionModel $orderItemOptionModel;
    protected StockConsumptionService $stockService;
    protected CustomerModel $customerModel;
    protected AuditLogService $auditService;

    public function __construct()
    {
        $this->saleModel     = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->menuModel     = new MenuModel();
        $this->recipeModel   = new RecipeModel();
        $this->rawModel      = new RawMaterialModel();
        $this->movementModel = new StockMovementModel();
        $this->optionGroupModel = new MenuOptionGroupModel();
        $this->optionModel = new MenuOptionModel();
        $this->orderItemOptionModel = new OrderItemOptionModel();
        $this->stockService = new StockConsumptionService();
        $this->customerModel = new CustomerModel();
        $this->auditService  = new AuditLogService();
    }

    /**
     * List penjualan (riwayat).
     */
    public function index()
    {
        $today = date('Y-m-d');

        $todayAgg = $this->saleModel
            ->selectSum('total_amount', 'total_sales')
            ->selectSum('total_cost', 'total_cost')
            ->where('status', 'completed')
            ->where('sale_date', $today)
            ->first();

        $totalSalesToday = (float) ($todayAgg['total_sales'] ?? 0);
        $totalCostToday  = (float) ($todayAgg['total_cost'] ?? 0);
        $marginToday     = $totalSalesToday - $totalCostToday;
        $marginPctToday  = $totalSalesToday > 0 ? ($marginToday / $totalSalesToday * 100) : 0;

        $sales = $this->saleModel
            ->orderBy('sale_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [
            'title'           => 'Penjualan',
            'subtitle'        => 'Riwayat transaksi penjualan',
            'sales'           => $sales,
            'todaySales'      => $totalSalesToday,
            'todayCost'       => $totalCostToday,
            'todayMargin'     => $marginToday,
            'todayMarginPct'  => $marginPctToday,
            'todayDate'       => $today,
        ];

        return view('transactions/sales_index', $data);
    }

    /**
     * Form input penjualan (POS minimalis).
     */
    public function create()
    {
        $menus = $this->menuModel
            ->orderBy('name', 'ASC')
            ->findAll();

        $today = date('Y-m-d');
        $customers = $this->customerModel->getActiveForDropdown();
        $defaultCustomer = $this->customerModel->getDefaultCustomer();
        $defaultId = (int) ($defaultCustomer['id'] ?? 0);
        if ($defaultId > 0) {
            $exists = false;
            foreach ($customers as $cust) {
                if ((int) ($cust['id'] ?? 0) === $defaultId) {
                    $exists = true;
                    break;
                }
            }
            if (! $exists) {
                $customers[] = $defaultCustomer;
            }
        }

        $data = [
            'title'    => 'Input Penjualan',
            'subtitle' => 'Pencatatan transaksi penjualan harian',
            'menus'    => $menus,
            'today'    => $today,
            'customers' => $customers,
            'defaultCustomerId' => $defaultId,
        ];

        return view('transactions/sales_form', $data);
    }

    /**
     * Simpan transaksi penjualan:
     * - sales (header)
     * - sale_items (detail)
     * - HPP snapshot per menu (hpp_snapshot)
     * - Stock OUT dari raw_materials + stock_movements
     */

    public function store()
    {
        $rules = [
            'sale_date' => 'required',
            'customer_id' => 'required|integer|greater_than[0]',
            'payment_method' => 'required|in_list[cash,qris]',
            'amount_paid' => 'permit_empty|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $itemsInput = $this->request->getPost('items') ?? [];
        $items      = [];
        $menuIds    = [];

        // Bersihkan baris kosong
        foreach ($itemsInput as $row) {
            $menuId = (int) ($row['menu_id'] ?? 0);
            $qty    = (float) ($row['qty'] ?? 0);
            $price  = (float) ($row['price'] ?? 0);
            $optionsInput = $row['options'] ?? [];
            $itemNote = trim((string) ($row['note'] ?? ''));

            if ($menuId > 0 && $qty > 0 && $price >= 0) {
                $items[] = [
                    'menu_id'       => $menuId,
                    'qty'           => $qty,
                    'price'         => $price,
                    'options_input' => is_array($optionsInput) ? $optionsInput : [],
                    'item_note'     => $itemNote !== '' ? $itemNote : null,
                ];
                $menuIds[$menuId] = true;
            }
        }

        if (empty($items)) {
            return redirect()->back()
                ->with('errors', ['items' => 'Minimal satu baris item penjualan harus diisi.'])
                ->withInput();
        }

        $customerId = (int) ($this->request->getPost('customer_id') ?? 0);
        $customer = $customerId > 0 ? $this->customerModel->find($customerId) : null;
        if (! $customer || (int) ($customer['is_active'] ?? 0) !== 1) {
            $customer = $this->customerModel->getDefaultCustomer();
        }
        $customerId = (int) ($customer['id'] ?? 0);
        $customerName = (string) ($customer['name'] ?? 'Tamu');

        $menus = [];
        if (! empty($menuIds)) {
            $menus = $this->menuModel
                ->whereIn('id', array_keys($menuIds))
                ->findAll();
        }

        $menuMap = [];
        foreach ($menus as $menu) {
            $menuMap[(int) $menu['id']] = $menu;
        }

        $groupsByMenu = [];
        $groupIds     = [];
        if (! empty($menuIds)) {
            $groups = $this->optionGroupModel
                ->whereIn('menu_id', array_keys($menuIds))
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            foreach ($groups as $group) {
                $menuId  = (int) ($group['menu_id'] ?? 0);
                $groupId = (int) ($group['id'] ?? 0);
                if ($menuId <= 0 || $groupId <= 0) {
                    continue;
                }
                if (! isset($groupsByMenu[$menuId])) {
                    $groupsByMenu[$menuId] = [];
                }
                $groupsByMenu[$menuId][$groupId] = $group;
                $groupIds[$groupId] = true;
            }
        }

        $optionById = [];
        if (! empty($groupIds)) {
            $options = $this->optionModel
                ->whereIn('group_id', array_keys($groupIds))
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            foreach ($options as $opt) {
                $optionId = (int) ($opt['id'] ?? 0);
                if ($optionId <= 0) {
                    continue;
                }
                $optionById[$optionId] = $opt;
            }
        }

        $errors     = [];
        $variantIds = [];

        foreach ($items as &$item) {
            $menuId = $item['menu_id'];
            $menuData = $menuMap[$menuId] ?? null;
            if (! $menuData) {
                $errors[] = 'Menu #' . $menuId . ' tidak ditemukan.';
                continue;
            }

            $item['menu_name']  = $menuData['name'] ?? 'Menu';
            $item['base_price'] = (float) ($menuData['price'] ?? 0);

            $groupMap = $groupsByMenu[$menuId] ?? [];
            $selectedOptions = [];
            $groupCounts = [];

            foreach ($item['options_input'] as $optRow) {
                $optId = (int) ($optRow['option_id'] ?? 0);
                $qtySelected = (float) ($optRow['qty_selected'] ?? 1);
                if ($optId <= 0) {
                    continue;
                }

                $opt = $optionById[$optId] ?? null;
                if (! $opt) {
                    $errors[] = 'Opsi tidak valid untuk <b>' . $item['menu_name'] . '</b>.';
                    continue;
                }

                $groupId = (int) ($opt['group_id'] ?? 0);
                if (! isset($groupMap[$groupId])) {
                    $errors[] = 'Opsi tidak sesuai menu untuk <b>' . $item['menu_name'] . '</b>.';
                    continue;
                }

                if ($qtySelected <= 0) {
                    $qtySelected = 1;
                }

                if (! isset($selectedOptions[$optId])) {
                    $selectedOptions[$optId] = [
                        'option_id'    => $optId,
                        'qty_selected' => $qtySelected,
                    ];
                } else {
                    $selectedOptions[$optId]['qty_selected'] += $qtySelected;
                }

                $groupCounts[$groupId] = ($groupCounts[$groupId] ?? 0) + 1;

                $variantId = (int) ($opt['variant_id'] ?? 0);
                if ($variantId > 0) {
                    $variantIds[$variantId] = true;
                }
            }

            foreach ($groupMap as $groupId => $group) {
                $min = (int) ($group['min_select'] ?? 0);
                $max = (int) ($group['max_select'] ?? 0);
                if ($min <= 0 && (int) ($group['is_required'] ?? 0) === 1) {
                    $min = 1;
                }
                $selectedCount = (int) ($groupCounts[$groupId] ?? 0);
                if ($min > 0 && $selectedCount < $min) {
                    $errors[] = 'Grup opsi <b>' . ($group['name'] ?? '-') . '</b> untuk <b>' .
                        $item['menu_name'] . '</b> wajib dipilih minimal ' . $min . '.';
                }
                if ($max > 0 && $selectedCount > $max) {
                    $errors[] = 'Grup opsi <b>' . ($group['name'] ?? '-') . '</b> untuk <b>' .
                        $item['menu_name'] . '</b> melebihi batas maksimal ' . $max . '.';
                }
            }

            $item['options'] = array_values($selectedOptions);
        }
        unset($item);

        if (! empty($errors)) {
            return redirect()->back()
                ->with('errors', $errors)
                ->withInput();
        }

        $db = \Config\Database::connect();
        $variantMap = [];
        if (! empty($variantIds)) {
            $variantRows = $db->table('raw_material_variants rmv')
                ->select('rmv.id, rmv.raw_material_id, rm.name AS raw_material_name, rm.cost_avg AS raw_material_cost, rm.current_stock AS raw_current_stock, rm.has_variants, rm.qty_precision AS qty_precision, rmv.current_stock, rmv.variant_name')
                ->join('raw_materials rm', 'rm.id = rmv.raw_material_id', 'left')
                ->whereIn('rmv.id', array_keys($variantIds))
                ->get()
                ->getResultArray();

            foreach ($variantRows as $row) {
                $variantId = (int) ($row['id'] ?? 0);
                if ($variantId <= 0) {
                    continue;
                }
                $variantMap[$variantId] = $row;
            }
        }

        foreach ($items as &$item) {
            $optionDelta = 0.0;
            $optionCostPerUnit = 0.0;
            $optionVariantNeeds = [];

            foreach ($item['options'] as &$sel) {
                $opt = $optionById[$sel['option_id']] ?? null;
                if (! $opt) {
                    $errors[] = 'Opsi tidak valid untuk <b>' . $item['menu_name'] . '</b>.';
                    continue;
                }

                $qtySelected = (float) ($sel['qty_selected'] ?? 1);
                $priceDelta  = (float) ($opt['price_delta'] ?? 0);
                $qtyMultiplier = (float) ($opt['qty_multiplier'] ?? 1);
                $variantId   = (int) ($opt['variant_id'] ?? 0);

                $sel['price_delta'] = $priceDelta;
                $sel['option_name'] = $opt['name'] ?? 'Option';
                $sel['qty_multiplier'] = $qtyMultiplier;
                $sel['variant_id'] = $variantId;

                $optionDelta += $priceDelta * $qtySelected;

                if ($variantId <= 0 || ! isset($variantMap[$variantId])) {
                    $errors[] = 'Variant bahan baku untuk opsi <b>' . $sel['option_name'] . '</b> belum tersedia.';
                    continue;
                }

                $rawId = (int) ($variantMap[$variantId]['raw_material_id'] ?? 0);
                $rawCost = (float) ($variantMap[$variantId]['raw_material_cost'] ?? 0);

                $optionCostPerUnit += $rawCost * $qtySelected * $qtyMultiplier;
                $needQty = $this->roundQty($item['qty'] * $qtySelected * $qtyMultiplier);

                if ($variantId > 0) {
                    if (! isset($optionVariantNeeds[$variantId])) {
                        $optionVariantNeeds[$variantId] = 0.0;
                    }
                    $optionVariantNeeds[$variantId] = $this->roundQty($optionVariantNeeds[$variantId] + $needQty);
                }
            }
            unset($sel);

            $item['option_price_delta'] = $this->roundQty($optionDelta);
            $item['option_cost_per_unit'] = $this->roundQty($optionCostPerUnit);
            $item['option_variant_needs'] = $optionVariantNeeds;
            $item['subtotal'] = $item['qty'] * $item['price'];
        }
        unset($item);

        if (! empty($errors)) {
            return redirect()->back()
                ->with('errors', $errors)
                ->withInput();
        }

        // Validasi override harga jual per item
        $priceErrors = [];
        foreach ($items as $row) {
            $minPrice = (float) ($row['base_price'] ?? 0) + (float) ($row['option_price_delta'] ?? 0);
            if ($row['price'] <= 0) {
                $priceErrors[] = 'Harga jual untuk <b>' . $row['menu_name'] . '</b> harus lebih dari 0.';
                continue;
            }

            if ($minPrice > 0 && $row['price'] < $minPrice) {
                $priceErrors[] = 'Harga jual untuk <b>' . $row['menu_name'] .
                    '</b> lebih rendah dari harga minimal (Rp ' . number_format($minPrice, 0, ',', '.') . ').';
            }
        }

        if (! empty($priceErrors)) {
            return redirect()->back()
                ->with('errors', $priceErrors)
                ->withInput();
        }

        // ============================================================
        // STEP 1: VALIDASI RESEP & HITUNG KEBUTUHAN BAHAN BAKU
        // ============================================================

        $errors        = [];
        $rawNeeds      = [];
        $variantNeeds  = [];
        $hppCache      = [];

        foreach ($items as $row) {
            $menuId  = $row['menu_id'];
            $qtySale = $row['qty'];

            $hppData = $this->recipeModel->calculateHppForMenu($menuId);
            if (! $hppData) {
                $errors[] = 'Menu <b>' . $row['menu_name'] . '</b> belum memiliki resep. Transaksi dibatalkan.';
                continue;
            }

            $recipe       = $hppData['recipe'] ?? [];
            $rawBreakdown = $hppData['raw_breakdown'] ?? [];

            if (empty($rawBreakdown) && empty($row['option_variant_needs'] ?? [])) {
                $errors[] = 'Menu <b>' . $row['menu_name'] . '</b> belum memiliki bahan baku atau opsi varian. Transaksi dibatalkan.';
                continue;
            }

            $hppCache[$menuId] = $hppData;

            $yieldQty = (float) ($recipe['yield_qty'] ?? 1);
            if ($yieldQty <= 0) {
                $yieldQty = 1;
            }

            $factor = $qtySale / $yieldQty;

            foreach ($rawBreakdown as $rawId => $qtyPerBatch) {
                $needQty = $this->roundQty($qtyPerBatch * $factor);

                if (! isset($rawNeeds[$rawId])) {
                    $rawNeeds[$rawId] = 0;
                }

                $rawNeeds[$rawId] = $this->roundQty($rawNeeds[$rawId] + $needQty);
            }

            foreach (($row['option_variant_needs'] ?? []) as $variantId => $needQty) {
                if (! isset($variantNeeds[$variantId])) {
                    $variantNeeds[$variantId] = 0;
                }
                $variantNeeds[$variantId] = $this->roundQty($variantNeeds[$variantId] + $needQty);
            }
        }

        if (! empty($errors)) {
            return redirect()->back()
                ->with('errors', $errors)
                ->withInput();
        }

        // ============================================================
        // STEP 2: VALIDASI STOK BAHAN BAKU
        // ============================================================

        $shortages = [];
        $variantShortages = [];

        if (! empty($variantNeeds)) {
            foreach ($variantNeeds as $variantId => $neededQty) {
                $variant = $variantMap[$variantId] ?? null;
                if (! $variant) {
                    continue;
                }
                if ((int) ($variant['has_variants'] ?? 0) === 0) {
                    $rawId = (int) ($variant['raw_material_id'] ?? 0);
                    if ($rawId > 0) {
                        $rawNeeds[$rawId] = $this->roundQty(($rawNeeds[$rawId] ?? 0) + $neededQty);
                    }
                    unset($variantNeeds[$variantId]);
                }
            }
        }

        $rawMap = [];
        if (! empty($rawNeeds)) {
            $rows = $this->rawModel
                ->whereIn('id', array_keys($rawNeeds))
                ->findAll();

            foreach ($rows as $row) {
                $rawMap[(int) $row['id']] = $row;
            }
        }

        foreach ($rawNeeds as $rawId => $neededQty) {
            $rm = $rawMap[$rawId] ?? null;
            if (! $rm) {
                continue;
            }

            $neededQty    = $this->roundQty($neededQty);
            $currentStock = $this->roundQty((float) ($rm['current_stock'] ?? 0));

            if ((int) ($rm['has_variants'] ?? 0) === 1) {
                $errors[] = 'Bahan <b>' . ($rm['name'] ?? '-') .
                    '</b> memiliki varian. Pilih varian dari opsi menu agar stok terpotong.';
                continue;
            }

            if ($currentStock < $neededQty) {
                $shortages[] = [
                    'name'   => $rm['name'],
                    'needed' => $neededQty,
                    'stock'  => $currentStock,
                    'precision' => (int) ($rm['qty_precision'] ?? 0),
                ];
            }
        }

        foreach ($variantNeeds as $variantId => $neededQty) {
            $variant = $variantMap[$variantId] ?? null;
            if (! $variant) {
                continue;
            }
            $neededQty = $this->roundQty($neededQty);
            $currentStock = $this->roundQty((float) ($variant['current_stock'] ?? 0));
            if ($currentStock < $neededQty) {
                $variantShortages[] = [
                    'name' => ($variant['raw_material_name'] ?? '-') . ' - ' . ($variant['variant_name'] ?? ''),
                    'needed' => $neededQty,
                    'stock' => $currentStock,
                    'precision' => (int) ($variant['qty_precision'] ?? 0),
                ];
            }
        }

        if (! empty($shortages) || ! empty($variantShortages) || ! empty($errors)) {
            foreach ($shortages as $s) {
                $precision = (int) ($s['precision'] ?? 0);
                if ($precision < 0) {
                    $precision = 0;
                }
                if ($precision > 3) {
                    $precision = 3;
                }
                $needed = number_format($s['needed'], $precision, ',', '.');
                $stock  = number_format($s['stock'], $precision, ',', '.');
                $errors[] = 'Stok tidak mencukupi untuk <b>' . $s['name'] . '</b>: butuh ' . $needed . ', stok hanya ' . $stock;
            }

            foreach ($variantShortages as $s) {
                $precision = (int) ($s['precision'] ?? 0);
                if ($precision < 0) {
                    $precision = 0;
                }
                if ($precision > 3) {
                    $precision = 3;
                }
                $needed = number_format($s['needed'], $precision, ',', '.');
                $stock  = number_format($s['stock'], $precision, ',', '.');
                $errors[] = 'Stok varian tidak mencukupi untuk <b>' . $s['name'] . '</b>: butuh ' . $needed . ', stok hanya ' . $stock;
            }

            return redirect()->back()
                ->with('errors', $errors)
                ->withInput();
        }

        // ============================================================
        // STEP 3: SIMPAN TRANSAKSI + HITUNG TOTAL COST
        // ============================================================

        $db->transStart();

        $totalAmount = array_sum(array_column($items, 'subtotal'));
        $totalCost   = 0.0;

        $paymentMethod = (string) ($this->request->getPost('payment_method') ?? 'cash');
        $amountPaid = (float) ($this->request->getPost('amount_paid') ?? 0);
        if ($paymentMethod === 'qris') {
            $amountPaid = $totalAmount;
        }
        $changeAmount = $amountPaid - $totalAmount;

        if ($amountPaid < $totalAmount) {
            return redirect()->back()
                ->with('errors', ['Pembayaran kurang dari total.'])
                ->withInput();
        }

        // Ensure created_at/updated_at are populated (Asia/Jakarta timezone).
        if (class_exists('\\CodeIgniter\\I18n\\Time')) {
            $now = \CodeIgniter\I18n\Time::now('Asia/Jakarta')->toDateTimeString();
        } else {
            // Fallback to server time; app is expected to use Asia/Jakarta.
            $now = date('Y-m-d H:i:s');
        }

        $headerData = [
            'sale_date'     => $this->request->getPost('sale_date'),
            'invoice_no'    => $this->request->getPost('invoice_no') ?: null,
            'customer_id'   => $customerId > 0 ? $customerId : null,
            'customer_name' => $customerName !== '' ? $customerName : null,
            'payment_method' => $paymentMethod,
            'amount_paid'   => $amountPaid,
            'change_amount' => $changeAmount > 0 ? $changeAmount : 0,
            'kitchen_status' => 'open',
            'total_amount'  => $totalAmount,
            'total_cost'    => 0,
            'notes'         => $this->request->getPost('notes') ?: null,
            'status'        => 'completed',
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        $db->table('sales')->insert($headerData);
        $saleId = (int) $db->insertID();

        foreach ($items as $item) {
            $menuId = $item['menu_id'];
            $qty    = $item['qty'];

            $hppData = $hppCache[$menuId] ?? $this->recipeModel->calculateHppForMenu($menuId);
            $hppPerPortion = (float) ($hppData['hpp_per_yield'] ?? 0);
            $optionCostPerUnit = (float) ($item['option_cost_per_unit'] ?? 0);
            $hppSnapshot = $this->roundQty($hppPerPortion + $optionCostPerUnit);

            $lineCost  = $hppSnapshot * $qty;
            $totalCost += $lineCost;

            $db->table('sale_items')->insert([
                'sale_id'      => $saleId,
                'menu_id'      => $menuId,
                'qty'          => $qty,
                'price'        => $item['price'],
                'subtotal'     => $item['subtotal'],
                'hpp_snapshot' => $hppSnapshot,
                'item_note'    => $item['item_note'] ?? null,
            ]);

            $saleItemId = (int) $db->insertID();

            foreach ($item['options'] as $optSel) {
                $opt = $optionById[$optSel['option_id']] ?? null;
                if (! $opt) {
                    continue;
                }

                $db->table('sale_item_options')->insert([
                    'sale_item_id'        => $saleItemId,
                    'option_id'           => $optSel['option_id'],
                    'qty_selected'        => $optSel['qty_selected'] ?? 1,
                    'option_name_snapshot' => $opt['name'] ?? 'Option',
                    'price_delta_snapshot' => $opt['price_delta'] ?? 0,
                    'variant_id_snapshot' => $opt['variant_id'] ?? null,
                ]);
            }
        }

        $consume = $this->stockService->consumeForOrder($saleId, $db);
        if (! $consume['ok']) {
            $db->transRollback();

            return redirect()->back()
                ->with('errors', $consume['errors'] ?? ['Gagal memproses konsumsi stok.'])
                ->withInput();
        }

        $totalCost = round($totalCost, 6);
        $db->table('sales')->where('id', $saleId)->update([
            'total_cost' => $totalCost,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            $err = $db->error();

            log_message('error', 'Gagal simpan transaksi penjualan: {error}', [
                'error' => $err['message'] ?? 'unknown DB error',
            ]);

            return redirect()->back()
                ->with('errors', [
                    'Terjadi kesalahan saat menyimpan transaksi.',
                    'Detail: ' . ($err['message'] ?? 'unknown DB error'),
                ])
                ->withInput();
        }

        // Log sale creation
        $this->auditService->log('sale', 'create', $saleId, [
            'sale_date'      => $headerData['sale_date'],
            'invoice_no'     => $headerData['invoice_no'],
            'customer_id'    => $customerId,
            'customer_name'  => $customerName,
            'payment_method' => $paymentMethod,
            'total_amount'   => $totalAmount,
            'total_cost'     => $totalCost,
            'items_count'    => count($items),
        ], 'Sale created: ' . ($headerData['invoice_no'] ?? '#' . $saleId));

        return redirect()->to(site_url('transactions/sales'))
            ->with('message', 'Transaksi penjualan berhasil disimpan.');
    }

    /**
     * Detail 1 transaksi penjualan.
     */
    public function detail(int $id)
    {
        $sale = $this->saleModel->find($id);
        if (! $sale) {
            return redirect()->to(site_url('transactions/sales'))
                ->with('error', 'Data penjualan tidak ditemukan.');
        }

        $items = $this->saleItemModel
            ->withMenu()
            ->where('sale_id', $id)
            ->findAll();

        $data = [
            'title'   => 'Detail Penjualan',
            'subtitle' => 'Rincian transaksi',
            'sale'    => $sale,
            'items'   => $items,
        ];

        return view('transactions/sales_detail', $data);
    }

    /**
     * Kitchen ticket untuk 1 transaksi.
     */
    public function kitchenTicket(int $id)
    {
        $sale = $this->saleModel->find($id);
        if (! $sale) {
            return redirect()->to(site_url('transactions/sales'))
                ->with('error', 'Data penjualan tidak ditemukan.');
        }

        $items = $this->saleItemModel
            ->withMenu()
            ->where('sale_id', $id)
            ->findAll();

        $optionsByItem = [];
        if (! empty($items)) {
            $itemIds = array_map(static fn($row) => (int) $row['id'], $items);
            $rows = \Config\Database::connect()
                ->table('sale_item_options sio')
                ->select('sio.sale_item_id, sio.option_name_snapshot, mog.name AS group_name, mog.show_on_kitchen_ticket')
                ->join('menu_options mo', 'mo.id = sio.option_id', 'left')
                ->join('menu_option_groups mog', 'mog.id = mo.group_id', 'left')
                ->whereIn('sio.sale_item_id', $itemIds)
                ->orderBy('mog.sort_order', 'ASC')
                ->orderBy('mo.sort_order', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                if ((int) ($row['show_on_kitchen_ticket'] ?? 1) !== 1) {
                    continue;
                }
                $itemId = (int) ($row['sale_item_id'] ?? 0);
                if ($itemId <= 0) {
                    continue;
                }
                $groupName = $row['group_name'] ?? 'Option';
                $optionName = $row['option_name_snapshot'] ?? '';
                if (! isset($optionsByItem[$itemId])) {
                    $optionsByItem[$itemId] = [];
                }
                if (! isset($optionsByItem[$itemId][$groupName])) {
                    $optionsByItem[$itemId][$groupName] = [];
                }
                $optionsByItem[$itemId][$groupName][] = $optionName;
            }
        }

        return view('transactions/kitchen_ticket', [
            'title'   => 'Kitchen Ticket',
            'subtitle' => 'Ringkasan pesanan untuk dapur',
            'sale'    => $sale,
            'items'   => $items,
            'optionsByItem' => $optionsByItem,
        ]);
    }

    /**
     * Void / batalkan penjualan:
     * - tandai status void + simpan alasan
     * - rollback stok berdasarkan movement OUT yang terkait sale ini
     */
    public function void(int $id)
    {
        $sale = $this->saleModel->find($id);
        if (! $sale) {
            return redirect()->to(site_url('transactions/sales'))
                ->with('error', 'Data penjualan tidak ditemukan.');
        }

        if (($sale['status'] ?? 'completed') === 'void') {
            return redirect()->to(site_url('transactions/sales/detail/' . $id))
                ->with('error', 'Transaksi sudah void.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Ambil movement OUT yang terkait sale ini agar rollback akurat
        $movements = $this->movementModel
            ->where('ref_type', 'sale')
            ->where('ref_id', $id)
            ->where('movement_type', 'OUT')
            ->findAll();

        $rollbackMap = [];
        $rollbackVariantMap = [];
        foreach ($movements as $mv) {
            $rawId = (int) ($mv['raw_material_id'] ?? 0);
            if ($rawId <= 0) {
                continue;
            }
            $variantId = (int) ($mv['raw_material_variant_id'] ?? 0);
            $qty = $this->roundQty((float) ($mv['qty'] ?? 0));
            if ($variantId > 0) {
                if (! isset($rollbackVariantMap[$variantId])) {
                    $rollbackVariantMap[$variantId] = 0.0;
                }
                $rollbackVariantMap[$variantId] = $this->roundQty($rollbackVariantMap[$variantId] + $qty);
            } else {
                if (! isset($rollbackMap[$rawId])) {
                    $rollbackMap[$rawId] = 0.0;
                }
                $rollbackMap[$rawId] = $this->roundQty($rollbackMap[$rawId] + $qty);
            }
        }

        // Kembalikan stok dan catat movement IN
        foreach ($rollbackVariantMap as $variantId => $qty) {
            $variant = $db
                ->table('raw_material_variants')
                ->where('id', $variantId)
                ->get()
                ->getRowArray();
            if (! $variant) {
                continue;
            }

            $rawId = (int) ($variant['raw_material_id'] ?? 0);
            $currentStock = $this->roundQty((float) ($variant['current_stock'] ?? 0));
            $newStock     = $this->roundQty($currentStock + $qty);

            $db
                ->table('raw_material_variants')
                ->where('id', $variantId)
                ->update(['current_stock' => $newStock]);

            if ($rawId > 0) {
                $total = $db
                    ->table('raw_material_variants')
                    ->selectSum('current_stock', 'total_stock')
                    ->where('raw_material_id', $rawId)
                    ->get()
                    ->getRowArray();
                $stock = (float) ($total['total_stock'] ?? 0);
                $db->table('raw_materials')->where('id', $rawId)->update(['current_stock' => $stock]);
            }

            $db->table('stock_movements')->insert([
                'raw_material_id' => $rawId,
                'raw_material_variant_id' => $variantId,
                'movement_type'   => 'IN',
                'qty'             => $qty,
                'ref_type'        => 'sale_void',
                'ref_id'          => $id,
                'note'            => 'Void penjualan #' . $id,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);
        }

        foreach ($rollbackMap as $rawId => $qty) {
            $material = $db->table('raw_materials')->where('id', $rawId)->get()->getRowArray();
            if (! $material) {
                continue;
            }

            $currentStock = $this->roundQty((float) ($material['current_stock'] ?? 0));
            $newStock     = $this->roundQty($currentStock + $qty);

            $db->table('raw_materials')->where('id', $rawId)->update(['current_stock' => $newStock]);

            $db->table('stock_movements')->insert([
                'raw_material_id' => $rawId,
                'raw_material_variant_id' => null,
                'movement_type'   => 'IN',
                'qty'             => $qty,
                'ref_type'        => 'sale_void',
                'ref_id'          => $id,
                'note'            => 'Void penjualan #' . $id,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);
        }

        // Tandai sale void
        $db->table('sales')->where('id', $id)->update([
            'status'      => 'void',
            'void_reason' => $this->request->getPost('void_reason') ?: null,
            'voided_at'   => date('Y-m-d H:i:s'),
            'kitchen_status' => 'done',
            'kitchen_done_at' => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()
                ->with('error', 'Gagal memproses void. Silakan coba lagi.')
                ->withInput();
        }

        // Log sale void
        $this->auditService->log('sale', 'void', $id, [
            'sale_date'     => $sale['sale_date'] ?? null,
            'invoice_no'    => $sale['invoice_no'] ?? null,
            'customer_name' => $sale['customer_name'] ?? null,
            'total_amount'  => $sale['total_amount'] ?? 0,
            'void_reason'   => $this->request->getPost('void_reason') ?: null,
        ], 'Sale voided: ' . ($sale['invoice_no'] ?? '#' . $id));

        return redirect()->to(site_url('transactions/sales/detail/' . $id))
            ->with('message', 'Transaksi berhasil di-void dan stok sudah dikembalikan.');
    }

    /**
     * Batasi waste % supaya tidak negatif / di atas 100.
     */
    private function clampWastePct(float $value): float
    {
        if ($value < 0) {
            return 0.0;
        }
        if ($value > 100) {
            return 100.0;
        }

        return $value;
    }

    /**
     * Normalisasi qty supaya tidak ada noise floating point berlebih.
     */
    private function roundQty(float $value): float
    {
        return round($value, 6);
    }

    /**
     * Kitchen queue (pesanan dapur).
     */
    public function kitchenQueue()
    {
        $filter = strtolower((string) ($this->request->getGet('status') ?? 'open'));
        if (! in_array($filter, ['open', 'done', 'all'], true)) {
            $filter = 'open';
        }

        $builder = $this->saleModel
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC');

        // Hanya transaksi completed untuk dapur.
        $builder->where('status', 'completed');

        if ($filter === 'open') {
            $builder->where('kitchen_status', 'open');
        } elseif ($filter === 'done') {
            $builder->where('kitchen_status', 'done');
        }

        $sales = $builder->findAll();

        return view('transactions/kitchen_queue', [
            'title' => 'Kitchen Queue',
            'subtitle' => 'Daftar pesanan yang perlu disiapkan dapur.',
            'sales' => $sales,
            'filter' => $filter,
        ]);
    }

    /**
     * Tandai kitchen ticket selesai.
     */
    public function kitchenDone(int $id)
    {
        $sale = $this->saleModel->find($id);
        if (! $sale) {
            return redirect()->back()
                ->with('error', 'Data penjualan tidak ditemukan.');
        }

        if (($sale['status'] ?? 'completed') !== 'completed') {
            return redirect()->back()
                ->with('error', 'Transaksi bukan status completed.');
        }

        if (($sale['kitchen_status'] ?? 'open') === 'done') {
            return redirect()->back()
                ->with('message', 'Ticket dapur sudah selesai.');
        }

        $this->saleModel->update($id, [
            'kitchen_status' => 'done',
            'kitchen_done_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()
            ->with('message', 'Ticket dapur ditandai selesai.');
    }
}
