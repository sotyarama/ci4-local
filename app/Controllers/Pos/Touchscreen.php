<?php

namespace App\Controllers\Pos;

use App\Controllers\BaseController;
use App\Models\MenuModel;

class Touchscreen extends BaseController
{
    protected MenuModel $menuModel;

    public function __construct()
    {
        $this->menuModel = new MenuModel();
    }

    public function index()
    {
        $menus = $this->menuModel
            ->withCategory()
            ->where('menus.is_active', 1)
            ->orderBy('menu_categories.name', 'ASC')
            ->orderBy('menus.name', 'ASC')
            ->findAll();

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
            'today'    => $today,
        ]);
    }
}
