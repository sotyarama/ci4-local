<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\AuditLogService;

class Login extends BaseController
{
    protected AuditLogService $auditService;

    public function __construct()
    {
        $this->auditService = new AuditLogService();
    }

    public function index()
    {
        // Kalau sudah login, langsung ke dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        return view('auth/login', [
            'title'    => 'Login',
            'subtitle' => 'Silakan masuk ke sistem POS',
        ]);
    }

    public function attempt()
    {
        $session = session();
        $request = $this->request;

        $username = trim((string) $request->getPost('username'));
        $password = (string) $request->getPost('password');
        $ip       = $request->getIPAddress();

        if ($username === '' || $password === '') {
            return redirect()->back()
                ->with('error', 'Username dan password wajib diisi.')
                ->withInput();
        }

        $userModel = new UserModel();
        $user      = $userModel->findByUsername($username);

        if (! $user) {
            // Log failed login (unknown user)
            $this->auditService->logAuth('login_fail', null, [
                'username' => $username,
                'reason'   => 'user_not_found',
                'ip'       => $ip,
            ], 'Login failed: user not found');

            return redirect()->back()
                ->with('error', 'Username atau password salah.')
                ->withInput();
        }

        if (! password_verify($password, $user['password_hash'])) {
            // Log failed login (wrong password)
            $this->auditService->logAuth('login_fail', (int) $user['id'], [
                'username' => $username,
                'reason'   => 'wrong_password',
                'ip'       => $ip,
            ], 'Login failed: wrong password');

            return redirect()->back()
                ->with('error', 'Username atau password salah.')
                ->withInput();
        }

        $roleName = $this->getRoleName((int) ($user['role_id'] ?? 0));

        // Regenerate session ID to mitigate fixation before storing identity
        $session->regenerate(true);

        // Simpan ke session
        $session->set([
            'user_id'     => $user['id'],
            'username'    => $user['username'],
            'full_name'   => $user['full_name'],
            'role_id'     => $user['role_id'],
            'role'        => $roleName,
            'role_name'   => $roleName,
            'isLoggedIn'  => true,
        ]);

        // Log successful login
        $this->auditService->logAuth('login_success', (int) $user['id'], [
            'username' => $user['username'],
            'role'     => $roleName,
            'ip'       => $ip,
        ], 'Login successful');

        return redirect()->to('/'); // ke dashboard
    }

    private function getRoleName(int $roleId): string
    {
        if ($roleId <= 0) {
            return '';
        }

        $db = \Config\Database::connect();
        $row = $db->table('roles')->select('name')->where('id', $roleId)->get()->getRowArray();

        return strtolower((string) ($row['name'] ?? ''));
    }
}
