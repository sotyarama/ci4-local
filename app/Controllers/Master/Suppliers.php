<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\SupplierModel;

class Suppliers extends BaseController
{
    protected SupplierModel $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
    }

    public function index()
    {
        $suppliers = $this->supplierModel
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'     => 'Master Supplier',
            'subtitle'  => 'Daftar supplier bahan baku',
            'suppliers' => $suppliers,
        ];

        return view('master/suppliers_index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Tambah Supplier',
            'subtitle'   => 'Daftarkan supplier baru',
            'supplier'   => null,
            'formAction' => site_url('master/suppliers/store'),
        ];

        return view('master/suppliers_form', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'phone' => 'permit_empty|max_length[50]',
            'address' => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'phone'     => $this->request->getPost('phone') ?: null,
            'address'   => $this->request->getPost('address') ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->supplierModel->insert($data);

        return redirect()->to(site_url('master/suppliers'))
            ->with('message', 'Supplier berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $supplier = $this->supplierModel->find($id);
        if (! $supplier) {
            return redirect()->to(site_url('master/suppliers'))
                ->with('error', 'Supplier tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Supplier',
            'subtitle'   => 'Ubah data supplier',
            'supplier'   => $supplier,
            'formAction' => site_url('master/suppliers/update/' . $id),
        ];

        return view('master/suppliers_form', $data);
    }

    public function update(int $id)
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'phone' => 'permit_empty|max_length[50]',
            'address' => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $supplier = $this->supplierModel->find($id);
        if (! $supplier) {
            return redirect()->to(site_url('master/suppliers'))
                ->with('error', 'Supplier tidak ditemukan.');
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'phone'     => $this->request->getPost('phone') ?: null,
            'address'   => $this->request->getPost('address') ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->supplierModel->update($id, $data);

        return redirect()->to(site_url('master/suppliers'))
            ->with('message', 'Supplier berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $supplier = $this->supplierModel->find($id);
        if (! $supplier) {
            return redirect()->to(site_url('master/suppliers'))
                ->with('error', 'Supplier tidak ditemukan.');
        }

        $this->supplierModel->delete($id);

        return redirect()->to(site_url('master/suppliers'))
            ->with('message', 'Supplier berhasil dihapus.');
    }
}
