<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Models\MenuOptionGroupModel;
use App\Models\MenuOptionModel;
use App\Models\RawMaterialVariantModel;
use App\Services\AuditLogService;

class MenuOptions extends BaseController
{
    protected MenuModel $menuModel;
    protected MenuOptionGroupModel $groupModel;
    protected MenuOptionModel $optionModel;
    protected RawMaterialVariantModel $variantModel;
    protected AuditLogService $auditService;

    public function __construct()
    {
        $this->menuModel    = new MenuModel();
        $this->groupModel   = new MenuOptionGroupModel();
        $this->optionModel  = new MenuOptionModel();
        $this->variantModel = new RawMaterialVariantModel();
        $this->auditService = new AuditLogService();
    }

    public function index()
    {
        $menuId = (int) ($this->request->getGet('menu_id') ?? old('menu_id'));

        $menus = $this->menuModel
            ->orderBy('name', 'ASC')
            ->findAll();

        $groups = [];
        $optionsByGroup = [];

        if ($menuId > 0) {
            $groups = $this->groupModel
                ->where('menu_id', $menuId)
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            $groupIds = array_map(static fn($row) => (int) $row['id'], $groups);
            if (! empty($groupIds)) {
                $options = $this->optionModel
                    ->whereIn('group_id', $groupIds)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();

                foreach ($options as $opt) {
                    $gid = (int) ($opt['group_id'] ?? 0);
                    if (! isset($optionsByGroup[$gid])) {
                        $optionsByGroup[$gid] = [];
                    }
                    $optionsByGroup[$gid][] = $opt;
                }
            }
        }

        $variants = $this->variantModel
            ->select('raw_material_variants.id, raw_material_variants.variant_name, raw_materials.name AS raw_material_name, brands.name AS brand_name')
            ->join('raw_materials', 'raw_materials.id = raw_material_variants.raw_material_id', 'left')
            ->join('brands', 'brands.id = raw_material_variants.brand_id', 'left')
            ->where('raw_material_variants.is_active', 1)
            ->where('raw_materials.is_active', 1)
            ->orderBy('raw_materials.name', 'ASC')
            ->orderBy('brands.name', 'ASC')
            ->orderBy('raw_material_variants.variant_name', 'ASC')
            ->findAll();

        return view('master/menu_options_index', [
            'title'          => 'Menu Options',
            'subtitle'       => 'Konfigurasi group dan opsi menu',
            'menus'          => $menus,
            'menuId'         => $menuId,
            'groups'         => $groups,
            'optionsByGroup' => $optionsByGroup,
            'variants'       => $variants,
        ]);
    }

    public function save()
    {
        if (! $this->validate([
            'menu_id' => 'required|integer',
        ])) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $menuId = (int) $this->request->getPost('menu_id');
        $groupsInput = $this->request->getPost('groups') ?? [];
        if (! is_array($groupsInput)) {
            $groupsInput = [];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $existingGroups = $this->groupModel
            ->where('menu_id', $menuId)
            ->findAll();

        $existingGroupMap = [];
        foreach ($existingGroups as $g) {
            $existingGroupMap[(int) $g['id']] = $g;
        }

        $submittedGroupIds = [];
        $errors = [];

        foreach ($groupsInput as $gIndex => $gRow) {
            $groupName = trim((string) ($gRow['name'] ?? ''));
            if ($groupName === '') {
                continue;
            }

            $groupId = (int) ($gRow['id'] ?? 0);
            $isRequired = ! empty($gRow['is_required']) ? 1 : 0;
            $minSelect = (int) ($gRow['min_select'] ?? 0);
            $maxSelect = (int) ($gRow['max_select'] ?? 0);
            $sortOrder = ($gRow['sort_order'] ?? '') !== '' ? (int) $gRow['sort_order'] : ($gIndex + 1);
            $showTicket = ! empty($gRow['show_on_kitchen_ticket']) ? 1 : 0;
            $isActive = ! empty($gRow['is_active']) ? 1 : 0;

            if ($isRequired === 1 && $minSelect <= 0) {
                $minSelect = 1;
            }

            $payload = [
                'menu_id'                => $menuId,
                'name'                   => $groupName,
                'is_required'            => $isRequired,
                'min_select'             => $minSelect,
                'max_select'             => $maxSelect,
                'sort_order'             => $sortOrder,
                'show_on_kitchen_ticket' => $showTicket,
                'is_active'              => $isActive,
            ];

            if ($groupId > 0 && isset($existingGroupMap[$groupId])) {
                $this->groupModel->update($groupId, $payload);
            } else {
                $groupId = (int) $this->groupModel->insert($payload, true);
            }

            if ($groupId <= 0) {
                $errors[] = 'Gagal menyimpan group opsi menu.';
                continue;
            }

            $submittedGroupIds[$groupId] = true;

            $optionsInput = $gRow['options'] ?? [];
            if (! is_array($optionsInput)) {
                $optionsInput = [];
            }

            $existingOptions = $this->optionModel
                ->where('group_id', $groupId)
                ->findAll();

            $existingOptionMap = [];
            foreach ($existingOptions as $opt) {
                $existingOptionMap[(int) $opt['id']] = $opt;
            }

            $submittedOptionIds = [];
            foreach ($optionsInput as $oIndex => $oRow) {
                $optName = trim((string) ($oRow['name'] ?? ''));
                $variantId = (int) ($oRow['variant_id'] ?? 0);

                if ($optName === '' && $variantId <= 0) {
                    continue;
                }

                if ($optName === '' || $variantId <= 0) {
                    $errors[] = 'Opsi pada grup "' . $groupName . '" wajib mengisi nama dan varian.';
                    continue;
                }

                $optId = (int) ($oRow['id'] ?? 0);
                $priceDelta = (float) ($oRow['price_delta'] ?? 0);
                $qtyMultiplier = (float) ($oRow['qty_multiplier'] ?? 1);
                $optSort = ($oRow['sort_order'] ?? '') !== '' ? (int) $oRow['sort_order'] : ($oIndex + 1);
                $optActive = ! empty($oRow['is_active']) ? 1 : 0;

                $optPayload = [
                    'group_id'      => $groupId,
                    'name'          => $optName,
                    'price_delta'   => $priceDelta,
                    'variant_id'    => $variantId,
                    'qty_multiplier'=> $qtyMultiplier,
                    'sort_order'    => $optSort,
                    'is_active'     => $optActive,
                ];

                if ($optId > 0 && isset($existingOptionMap[$optId])) {
                    $this->optionModel->update($optId, $optPayload);
                    $submittedOptionIds[$optId] = true;
                } else {
                    $newId = (int) $this->optionModel->insert($optPayload, true);
                    if ($newId > 0) {
                        $submittedOptionIds[$newId] = true;
                    }
                }
            }

            foreach ($existingOptionMap as $optId => $optRow) {
                if (! isset($submittedOptionIds[$optId])) {
                    $this->optionModel->update($optId, ['is_active' => 0]);
                }
            }
        }

        foreach ($existingGroupMap as $gid => $gRow) {
            if (! isset($submittedGroupIds[$gid])) {
                $this->groupModel->update($gid, ['is_active' => 0]);
                $this->optionModel->where('group_id', $gid)->set(['is_active' => 0])->update();
            }
        }

        if (! empty($errors)) {
            $db->transRollback();
            return redirect()->back()
                ->with('errors', $errors)
                ->withInput();
        }

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()
                ->with('errors', ['Gagal menyimpan konfigurasi opsi menu.'])
                ->withInput();
        }

        // Log menu options bulk save
        $menu = $this->menuModel->find($menuId);
        $menuName = $menu['name'] ?? 'Unknown';
        $this->auditService->log('menu_options', 'bulk_save', $menuId, [
            'menu_id'       => $menuId,
            'menu_name'     => $menuName,
            'groups_count'  => count($submittedGroupIds),
            'groups_input'  => $groupsInput,
        ], 'Menu options saved for: ' . $menuName);

        return redirect()->to(site_url('master/menu-options?menu_id=' . $menuId))
            ->with('message', 'Konfigurasi opsi menu berhasil disimpan.');
    }
}
