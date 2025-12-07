<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\RawMaterialModel;
use App\Models\UnitModel;

class RawMaterials extends BaseController
{
    protected RawMaterialModel $rawModel;
    protected UnitModel $unitModel;

    public function __construct()
    {
        $this->rawModel  = new RawMaterialModel();
        $this->unitModel = new UnitModel();
    }

    public function index()
    {
        $materials = $this->rawModel
            ->withUnit()
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'      => 'Master Bahan Baku',
            'subtitle'   => 'Daftar bahan baku untuk resep & stok',
            'materials'  => $materials,
        ];

        return view('master/raw_materials_index', $data);
    }

    public function create()
    {
        $units = $this->unitModel->getForDropdown();

        $data = [
            'title'      => 'Tambah Bahan Baku',
            'subtitle'   => 'Daftarkan bahan baku baru',
            'units'      => $units,
            'material'   => null,
            'formAction' => site_url('master/raw-materials/store'),
        ];

        return view('master/raw_materials_form', $data);
    }

    public function store()
    {
        $rules = [
            'name'    => 'required|min_length[3]',
            'unit_id' => 'required|integer',
            'min_stock' => 'permit_empty|numeric',
            'initial_stock' => 'permit_empty|numeric',
            'initial_cost'  => 'permit_empty|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $minStock     = (float) ($this->request->getPost('min_stock') ?: 0);
        $initialStock = (float) ($this->request->getPost('initial_stock') ?: 0);
        $initialCost  = (float) ($this->request->getPost('initial_cost') ?: 0);

        $data = [
            'name'          => $this->request->getPost('name'),
            'unit_id'       => (int) $this->request->getPost('unit_id'),
            'current_stock' => $initialStock,
            'min_stock'     => $minStock,
            'cost_last'     => $initialCost,
            'cost_avg'      => $initialStock > 0 ? $initialCost : 0,
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->rawModel->insert($data);

        return redirect()->to(site_url('master/raw-materials'))
            ->with('message', 'Bahan baku berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $material = $this->rawModel->find($id);
        if (! $material) {
            return redirect()->to(site_url('master/raw-materials'))
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        $units = $this->unitModel->getForDropdown();

        $data = [
            'title'      => 'Edit Bahan Baku',
            'subtitle'   => 'Ubah data bahan baku',
            'units'      => $units,
            'material'   => $material,
            'formAction' => site_url('master/raw-materials/update/' . $id),
        ];

        return view('master/raw_materials_form', $data);
    }

    public function update(int $id)
    {
        $rules = [
            'name'    => 'required|min_length[3]',
            'unit_id' => 'required|integer',
            'min_stock' => 'permit_empty|numeric',
            'current_stock' => 'permit_empty|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $material = $this->rawModel->find($id);
        if (! $material) {
            return redirect()->to(site_url('master/raw-materials'))
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        $data = [
            'name'          => $this->request->getPost('name'),
            'unit_id'       => (int) $this->request->getPost('unit_id'),
            'min_stock'     => (float) ($this->request->getPost('min_stock') ?: 0),
            'current_stock' => (float) ($this->request->getPost('current_stock') ?? $material['current_stock']),
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->rawModel->update($id, $data);

        return redirect()->to(site_url('master/raw-materials'))
            ->with('message', 'Bahan baku berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $material = $this->rawModel->find($id);
        if (! $material) {
            return redirect()->to(site_url('master/raw-materials'))
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        $this->rawModel->delete($id);

        return redirect()->to(site_url('master/raw-materials'))
            ->with('message', 'Bahan baku berhasil dihapus.');
    }
}
