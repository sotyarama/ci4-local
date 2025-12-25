<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\RoleModel;
use App\Models\UserModel;

class Users extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;
    protected AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->roleModel     = new RoleModel();
        $this->auditLogModel = new AuditLogModel();
    }

    public function index()
    {
        $users = $this->userModel
            ->select('users.*, roles.name AS role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->orderBy('users.id', 'ASC')
            ->findAll();

        return view('users/users_index', [
            'title'    => 'User Management',
            'subtitle' => 'Kelola akun pengguna',
            'users'    => $users,
        ]);
    }

    public function create()
    {
        return view('users/users_form', [
            'title'      => 'Tambah User',
            'subtitle'   => 'Buat akun pengguna baru',
            'roles'      => $this->roleModel->getForDropdown(),
            'user'       => null,
            'formAction' => site_url('users/store'),
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->rulesForCreate())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $roleId = (int) $this->request->getPost('role_id');
        if (! $this->roleExists($roleId)) {
            return redirect()->back()
                ->with('errors', ['role_id' => 'Role tidak valid.'])
                ->withInput();
        }

        $payload = $this->payloadForCreate();

        $this->userModel->insert($payload);
        $newId = (int) $this->userModel->getInsertID();

        $this->logUserChange($newId, 'create', $payload);

        return redirect()->to(site_url('users'))
            ->with('message', 'User berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(site_url('users'))
                ->with('error', 'User tidak ditemukan.');
        }

        $roleLabel = $this->getRoleLabel((int) ($user['role_id'] ?? 0));

        return view('users/users_form', [
            'title'      => 'Edit User',
            'subtitle'   => 'Ubah data akun pengguna',
            'user'       => $user,
            'roleLabel'  => $roleLabel,
            'formAction' => site_url('users/update/' . $id),
        ]);
    }

    public function update(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(site_url('users'))
                ->with('error', 'User tidak ditemukan.');
        }

        if (! $this->validate($this->rulesForUpdate($id))) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $payload = $this->payloadForUpdate();

        $this->userModel->update($id, $payload);

        $this->logUserChange($id, 'update', $payload);

        return redirect()->to(site_url('users'))
            ->with('message', 'User berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(site_url('users'))
                ->with('error', 'User tidak ditemukan.');
        }

        $currentUserId = (int) (session('user_id') ?? 0);
        if ($currentUserId > 0 && $currentUserId === $id) {
            return redirect()->to(site_url('users'))
                ->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $snapshot = [
            'username'  => $user['username'] ?? null,
            'full_name' => $user['full_name'] ?? null,
            'email'     => $user['email'] ?? null,
            'role_id'   => $user['role_id'] ?? null,
            'active'    => $user['active'] ?? null,
        ];

        $this->userModel->delete($id);

        $this->logUserChange($id, 'delete', $snapshot, false);

        return redirect()->to(site_url('users'))
            ->with('message', 'User berhasil dihapus.');
    }

    private function rulesForCreate(): array
    {
        return [
            'username'         => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'full_name'        => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|max_length[100]',
            'role_id'          => 'required|is_natural_no_zero',
        ];
    }

    private function rulesForUpdate(int $id): array
    {
        return [
            'username'         => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,' . $id . ']',
            'full_name'        => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|max_length[100]',
        ];
    }

    private function payloadForCreate(): array
    {
        $username = trim((string) $this->request->getPost('username'));
        $fullName = trim((string) $this->request->getPost('full_name'));
        $email    = trim((string) $this->request->getPost('email'));

        $payload = [
            'username'  => $username,
            'full_name' => $fullName,
            'email'     => ($email !== '') ? $email : null,
            'role_id'   => (int) $this->request->getPost('role_id'),
            'active'    => 1,
        ];

        return $payload;
    }

    private function payloadForUpdate(): array
    {
        $username = trim((string) $this->request->getPost('username'));
        $fullName = trim((string) $this->request->getPost('full_name'));
        $email    = trim((string) $this->request->getPost('email'));

        return [
            'username'  => $username,
            'full_name' => $fullName,
            'email'     => ($email !== '') ? $email : null,
        ];
    }

    private function roleExists(int $roleId): bool
    {
        if ($roleId <= 0) {
            return false;
        }

        return (bool) $this->roleModel->find($roleId);
    }

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

    private function getRoleLabel(int $roleId): string
    {
        if ($roleId <= 0) {
            return '-';
        }

        $role = $this->roleModel->find($roleId);
        if (! $role) {
            return '-';
        }

        $label = (string) ($role['name'] ?? '');
        $label = $label !== '' ? ucfirst($label) : '-';
        $desc  = (string) ($role['description'] ?? '');

        if ($desc !== '') {
            $label .= ' - ' . $desc;
        }

        return $label;
    }
}
