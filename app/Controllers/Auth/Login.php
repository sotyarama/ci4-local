<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Login extends BaseController
{
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

        if ($username === '' || $password === '') {
            return redirect()->back()
                ->with('error', 'Username dan password wajib diisi.')
                ->withInput();
        }

        $userModel = new UserModel();
        $user      = $userModel->findByUsername($username);

        if (! $user) {
            return redirect()->back()
                ->with('error', 'Username atau password salah.')
                ->withInput();
        }

        if (! password_verify($password, $user['password_hash'])) {
            return redirect()->back()
                ->with('error', 'Username atau password salah.')
                ->withInput();
        }

        $roleName = $this->getRoleName((int) ($user['role_id'] ?? 0));

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
