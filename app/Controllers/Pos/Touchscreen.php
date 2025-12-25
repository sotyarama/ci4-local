<?php

namespace App\Controllers\Pos;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Models\MenuOptionGroupModel;
use App\Models\MenuOptionModel;

class Touchscreen extends BaseController
{
    protected MenuModel $menuModel;
    protected MenuOptionGroupModel $optionGroupModel;
    protected MenuOptionModel $optionModel;

    public function __construct()
    {
        $this->menuModel = new MenuModel();
        $this->optionGroupModel = new MenuOptionGroupModel();
        $this->optionModel = new MenuOptionModel();
    }

    public function index()
    {
        $menus = $this->menuModel
            ->withCategory()
            ->where('menus.is_active', 1)
            ->orderBy('menu_categories.name', 'ASC')
            ->orderBy('menus.name', 'ASC')
            ->findAll();

        $menuIds = array_map(static fn($row) => (int) $row['id'], $menus);
        $menuOptions = [];

        if (! empty($menuIds)) {
            $groups = $this->optionGroupModel
                ->whereIn('menu_id', $menuIds)
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            $groupIds = array_map(static fn($row) => (int) $row['id'], $groups);

            $options = [];
            if (! empty($groupIds)) {
                $options = $this->optionModel
                    ->whereIn('group_id', $groupIds)
                    ->where('is_active', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
            }

            $optionsByGroup = [];
            foreach ($options as $opt) {
                $groupId = (int) ($opt['group_id'] ?? 0);
                if (! isset($optionsByGroup[$groupId])) {
                    $optionsByGroup[$groupId] = [];
                }
                $optionsByGroup[$groupId][] = [
                    'id'            => (int) ($opt['id'] ?? 0),
                    'name'          => $opt['name'] ?? '',
                    'price_delta'   => (float) ($opt['price_delta'] ?? 0),
                    'qty_multiplier'=> (float) ($opt['qty_multiplier'] ?? 1),
                ];
            }

            foreach ($groups as $group) {
                $menuId = (int) ($group['menu_id'] ?? 0);
                $groupId = (int) ($group['id'] ?? 0);
                if (! isset($menuOptions[$menuId])) {
                    $menuOptions[$menuId] = [];
                }
                $menuOptions[$menuId][] = [
                    'id'                     => $groupId,
                    'name'                   => $group['name'] ?? '',
                    'is_required'            => (int) ($group['is_required'] ?? 0),
                    'min_select'             => (int) ($group['min_select'] ?? 0),
                    'max_select'             => (int) ($group['max_select'] ?? 0),
                    'show_on_kitchen_ticket' => (int) ($group['show_on_kitchen_ticket'] ?? 1),
                    'options'                => $optionsByGroup[$groupId] ?? [],
                ];
            }
        }

        $grouped = [];
        foreach ($menus as $m) {
            $cat = $m['category_name'] ?? 'Tanpa Kategori';
            if (! isset($grouped[$cat])) {
                $grouped[$cat] = [];
            }
            $grouped[$cat][] = $m;
        }

        $today = date('Y-m-d');

        return view('pos/touchscreen', [
            'title'    => 'POS Touchscreen',
            'subtitle' => 'Pilih menu, tap untuk tambah qty, lalu simpan.',
            'menusByCategory' => $grouped,
            'menuOptions' => $menuOptions,
            'today'    => $today,
        ]);
    }
}
