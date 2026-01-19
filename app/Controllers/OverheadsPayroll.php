<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PayrollModel;
use App\Services\AuditLogService;

class OverheadsPayroll extends BaseController
{
    protected UserModel $userModel;
    protected PayrollModel $payrollModel;
    protected AuditLogService $auditService;

    public function __construct()
    {
        $this->userModel    = new UserModel();
        $this->payrollModel = new PayrollModel();
        $this->auditService = new AuditLogService();
    }

    public function index()
    {
        $this->ensureOwner();

        $filterStaff = (int) ($this->request->getGet('staff_id') ?? 0);
        $filterPeriod= $this->request->getGet('period') ?: '';

        $staff = $this->userModel
            ->select('users.id, users.username, users.full_name, users.email')
            ->join('roles r', 'r.id = users.role_id', 'left')
            ->where('users.active', 1)
            ->where('users.deleted_at', null)
            ->where('LOWER(r.name)', 'staff')
            ->orderBy('users.id', 'ASC')
            ->findAll();

        $payrollBuilder = $this->payrollModel
            ->withUser()
            ->orderBy('period_month', 'DESC')
            ->orderBy('staff_name', 'ASC');

        if ($filterStaff > 0) {
            $payrollBuilder->where('payrolls.user_id', $filterStaff);
        }
        if ($filterPeriod !== '') {
            $payrollBuilder->where('period_month', $filterPeriod);
        }

        $payrolls = $payrollBuilder->findAll();

        return view('overheads/payroll', [
            'title'       => 'Overhead Payroll',
            'subtitle'    => 'Payroll bulanan per staff (owner only).',
            'staff'       => $staff,
            'payrolls'    => $payrolls,
            'filterStaff' => $filterStaff,
            'filterPeriod'=> $filterPeriod,
        ]);
    }

    public function create()
    {
        $this->ensureOwner();

        $staff = $this->getStaffList();

        return view('overheads/payroll_form', [
            'title'    => 'Tambah Payroll Staff',
            'subtitle' => 'Catat payroll bulanan untuk staff.',
            'staff'    => $staff,
            'payroll'  => null,
            'action'   => site_url('overheads/payroll/store'),
            'method'   => 'post',
        ]);
    }

    public function store()
    {
        $this->ensureOwner();
        $data = $this->validatePayload();
        if ($data === null) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal.');
        }

        $this->payrollModel->insert($data);
        $insertId = $this->payrollModel->getInsertID();

        // Get staff name for logging
        $staff = $this->userModel->find($data['user_id']);
        $staffName = $staff['full_name'] ?? $staff['username'] ?? 'Unknown';

        // Log payroll creation
        $this->auditService->log('payroll', 'create', $insertId, array_merge($data, ['staff_name' => $staffName]), 'Payroll created: ' . $staffName . ' - ' . $data['period_month']);

        return redirect()->to(site_url('overheads/payroll'))
            ->with('message', 'Payroll berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $this->ensureOwner();

        $payroll = $this->payrollModel->find($id);
        if (! $payroll) {
            return redirect()->to(site_url('overheads/payroll'))->with('error', 'Data payroll tidak ditemukan.');
        }

        $staff = $this->getStaffList();

        return view('overheads/payroll_form', [
            'title'    => 'Edit Payroll Staff',
            'subtitle' => 'Perbarui payroll bulanan untuk staff.',
            'staff'    => $staff,
            'payroll'  => $payroll,
            'action'   => site_url('overheads/payroll/update/' . $id),
            'method'   => 'post',
        ]);
    }

    public function update(int $id)
    {
        $this->ensureOwner();

        $exists = $this->payrollModel->find($id);
        if (! $exists) {
            return redirect()->to(site_url('overheads/payroll'))->with('error', 'Data payroll tidak ditemukan.');
        }

        $data = $this->validatePayload();
        if ($data === null) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal.');
        }

        $this->payrollModel->update($id, $data);

        // Get staff name for logging
        $staff = $this->userModel->find($data['user_id']);
        $staffName = $staff['full_name'] ?? $staff['username'] ?? 'Unknown';

        // Log payroll update
        $this->auditService->log('payroll', 'update', $id, [
            'before' => $exists,
            'after'  => array_merge($data, ['staff_name' => $staffName]),
        ], 'Payroll updated: ' . $staffName . ' - ' . $data['period_month']);

        return redirect()->to(site_url('overheads/payroll'))
            ->with('message', 'Payroll berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->ensureOwner();

        $payroll = $this->payrollModel->find($id);
        if ($payroll) {
            // Get staff name for logging
            $staff = $this->userModel->find($payroll['user_id'] ?? 0);
            $staffName = $staff['full_name'] ?? $staff['username'] ?? 'Unknown';

            // Log payroll deletion (capture before delete)
            $this->auditService->log('payroll', 'delete', $id, array_merge($payroll, ['staff_name' => $staffName]), 'Payroll deleted: ' . $staffName . ' - ' . ($payroll['period_month'] ?? ''));
        }

        $this->payrollModel->delete($id);
        return redirect()->to(site_url('overheads/payroll'))
            ->with('message', 'Payroll dihapus.');
    }

    private function validatePayload(): ?array
    {
        $rules = [
            'user_id'      => 'required|is_natural_no_zero',
            'period_month' => 'required|min_length[7]|max_length[7]',
            'pay_date'     => 'permit_empty|valid_date',
            'amount'       => 'required|numeric',
            'notes'        => 'permit_empty|string',
        ];

        if (! $this->validate($rules)) {
            return null;
        }

        $userId = (int) $this->request->getPost('user_id');
        if (! $this->isStaff($userId)) {
            return null;
        }

        return [
            'user_id'      => $userId,
            'period_month' => $this->request->getPost('period_month'),
            'pay_date'     => $this->request->getPost('pay_date') ?: null,
            'amount'       => (float) $this->request->getPost('amount'),
            'notes'        => $this->request->getPost('notes') ?: null,
        ];
    }

    private function getStaffList(): array
    {
        return $this->userModel
            ->select('users.id, users.full_name, users.username')
            ->join('roles r', 'r.id = users.role_id', 'left')
            ->where('users.active', 1)
            ->where('users.deleted_at', null)
            ->where('LOWER(r.name)', 'staff')
            ->orderBy('users.id', 'ASC')
            ->findAll();
    }

    private function isStaff(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }
        $row = $this->userModel
            ->select('users.id')
            ->join('roles r', 'r.id = users.role_id', 'left')
            ->where('users.id', $userId)
            ->where('users.active', 1)
            ->where('users.deleted_at', null)
            ->where('LOWER(r.name)', 'staff')
            ->first();
        return (bool) $row;
    }

    private function ensureOwner(): void
    {
        $role = strtolower((string) (session('role') ?? session('role_name') ?? ''));
        if ($role !== 'owner') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}
