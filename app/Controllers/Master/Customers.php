<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SaleModel;
use App\Services\AuditLogService;

class Customers extends BaseController
{
    protected CustomerModel $customerModel;
    protected SaleModel $saleModel;
    protected AuditLogService $auditService;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->saleModel     = new SaleModel();
        $this->auditService  = new AuditLogService();
    }

    /**
     * List semua customer.
     */
    public function index()
    {
        $customers = $this->customerModel
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('master/customers_index', [
            'title'     => 'Master Customer',
            'subtitle'  => 'Daftar customer untuk transaksi penjualan',
            'customers' => $customers,
        ]);
    }

    /**
     * Form create customer.
     */
    public function create()
    {
        return view('master/customers_form', [
            'title'      => 'Tambah Customer',
            'subtitle'   => 'Buat data customer baru',
            'customer'   => null,
            'formAction' => site_url('master/customers/store'),
        ]);
    }

    /**
     * Simpan customer baru.
     */
    public function store()
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $payload = $this->payloadFromRequest();
        $this->customerModel->insert($payload);

        $insertId = $this->customerModel->getInsertID();

        // Log customer creation
        $this->auditService->log('customer', 'create', $insertId, $payload, 'Customer created: ' . $payload['name']);

        return redirect()->to(site_url('master/customers'))
            ->with('message', 'Customer berhasil ditambahkan.');
    }

    /**
     * Form edit customer.
     */
    public function edit(int $id)
    {
        $customer = $this->customerModel->find($id);
        if (! $customer) {
            return redirect()->to(site_url('master/customers'))
                ->with('error', 'Customer tidak ditemukan.');
        }

        return view('master/customers_form', [
            'title'      => 'Edit Customer',
            'subtitle'   => 'Ubah data customer',
            'customer'   => $customer,
            'formAction' => site_url('master/customers/update/' . $id),
        ]);
    }

    /**
     * Update customer.
     */
    public function update(int $id)
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $customer = $this->customerModel->find($id);
        if (! $customer) {
            return redirect()->to(site_url('master/customers'))
                ->with('error', 'Customer tidak ditemukan.');
        }

        $oldData = $customer;
        $payload = $this->payloadFromRequest();
        $this->customerModel->update($id, $payload);

        // Log customer update with before/after
        $this->auditService->log('customer', 'update', $id, [
            'before' => $oldData,
            'after'  => $payload,
        ], 'Customer updated: ' . $payload['name']);

        return redirect()->to(site_url('master/customers'))
            ->with('message', 'Customer berhasil diperbarui.');
    }

    /**
     * Delete customer.
     */
    public function delete(int $id)
    {
        $customer = $this->customerModel->find($id);
        if (! $customer) {
            return redirect()->to(site_url('master/customers'))
                ->with('error', 'Customer tidak ditemukan.');
        }

        if (strtolower((string) ($customer['name'] ?? '')) === 'tamu') {
            return redirect()->to(site_url('master/customers'))
                ->with('error', 'Customer default "Tamu" tidak boleh dihapus.');
        }

        $usageCount = $this->saleModel
            ->where('customer_id', $id)
            ->countAllResults();
        if ($usageCount > 0) {
            return redirect()->to(site_url('master/customers'))
                ->with('error', 'Customer ini sudah dipakai pada transaksi dan tidak bisa dihapus.');
        }

        // Log customer deletion (capture before delete)
        $this->auditService->log('customer', 'delete', $id, $customer, 'Customer deleted: ' . ($customer['name'] ?? ''));

        $this->customerModel->delete($id);

        return redirect()->to(site_url('master/customers'))
            ->with('message', 'Customer berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'name'  => 'required|min_length[2]|max_length[120]',
            'phone' => 'permit_empty|min_length[6]|max_length[30]',
            'email' => 'permit_empty|valid_email|max_length[150]',
        ];
    }

    private function payloadFromRequest(): array
    {
        return [
            'name'      => trim((string) $this->request->getPost('name')),
            'phone'     => trim((string) $this->request->getPost('phone')) ?: null,
            'email'     => trim((string) $this->request->getPost('email')) ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }
}
