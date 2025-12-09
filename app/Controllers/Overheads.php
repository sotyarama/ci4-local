<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OverheadModel;
use App\Models\OverheadCategoryModel;

class Overheads extends BaseController
{
    protected OverheadModel $overheadModel;
    protected OverheadCategoryModel $categoryModel;

    public function __construct()
    {
        $this->overheadModel = new OverheadModel();
        $this->categoryModel = new OverheadCategoryModel();
    }

    public function index()
    {
        $dateFrom = $this->request->getGet('date_from') ?: null;
        $dateTo   = $this->request->getGet('date_to') ?: null;
        $categoryFilter = $this->request->getGet('category_id') ?: null;

        $builder = $this->overheadModel
            ->select('overheads.*, oc.name AS category_name')
            ->join('overhead_categories oc', 'oc.id = overheads.category_id', 'left')
            ->orderBy('trans_date', 'DESC')
            ->orderBy('overheads.id', 'DESC');

        if ($dateFrom) {
            $builder->where('trans_date >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('trans_date <=', $dateTo);
        }
        if ($categoryFilter) {
            $builder->where('overheads.category_id', $categoryFilter);
        }

        $rows = $builder->findAll();

        $total = array_sum(array_column($rows, 'amount'));

        $categories = $this->categoryModel
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'    => 'Biaya Overhead',
            'subtitle' => 'Catatan biaya operasional harian/bulanan',
            'rows'     => $rows,
            'total'    => $total,
            'dateFrom' => $dateFrom,
            'dateTo'   => $dateTo,
            'categories' => $categories,
            'filterCategory' => $categoryFilter,
        ];

        return view('overheads/overheads_index', $data);
    }

    public function create()
    {
        $categories = $this->categoryModel
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'    => 'Tambah Biaya Overhead',
            'subtitle' => 'Catat biaya operasional',
            'categories' => $categories,
        ];

        return view('overheads/overheads_form', $data);
    }

    public function store()
    {
        $rules = [
            'trans_date'  => 'required',
            'amount'      => 'required|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $this->overheadModel->insert([
            'trans_date'  => $this->request->getPost('trans_date'),
            'category'    => $this->request->getPost('category') ?: null,
            'description' => $this->request->getPost('description') ?: null,
            'category_id' => $this->request->getPost('category_id') ?: null,
            'category'    => $this->resolveCategoryName($this->request->getPost('category_id')),
            'amount'      => (float) $this->request->getPost('amount'),
        ]);

        return redirect()->to(site_url('overheads'))
            ->with('message', 'Biaya overhead berhasil disimpan.');
    }

    private function resolveCategoryName($categoryId): ?string
    {
        $id = (int) $categoryId;
        if ($id <= 0) {
            return null;
        }

        $cat = $this->categoryModel->find($id);
        return $cat['name'] ?? null;
    }
}
