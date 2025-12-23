<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\PasswordResetModel;
use App\Models\UserModel;

class ResetPassword extends BaseController
{
    private UserModel $userModel;
    private PasswordResetModel $resetModel;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->resetModel = new PasswordResetModel();
    }

    public function index()
    {
        $token = (string) $this->request->getGet('token');
        $email = strtolower(trim((string) $this->request->getGet('email')));

        if ($token === '' || $email === '') {
            return redirect()->to(site_url('auth/forgot'))
                ->with('error', 'Tautan reset tidak valid atau sudah kedaluwarsa.');
        }

        $user = $this->getActiveUserByEmail($email);
        if (! $user) {
            return redirect()->to(site_url('auth/forgot'))
                ->with('error', 'Tautan reset tidak valid atau sudah kedaluwarsa.');
        }

        $reset = $this->resetModel->findValidReset((int) $user['id'], $token);
        if (! $reset) {
            return redirect()->to(site_url('auth/forgot'))
                ->with('error', 'Tautan reset tidak valid atau sudah kedaluwarsa.');
        }

        return view('auth/reset', [
            'title'    => 'Reset Password',
            'subtitle' => 'Masukkan password baru',
            'email'    => $email,
            'token'    => $token,
        ]);
    }

    public function update()
    {
        $token            = (string) $this->request->getPost('token');
        $email            = strtolower(trim((string) $this->request->getPost('email')));
        $password         = (string) $this->request->getPost('password');
        $passwordConfirm  = (string) $this->request->getPost('password_confirm');

        if ($password === '' || $password !== $passwordConfirm) {
            return redirect()->back()
                ->with('error', 'Password tidak cocok atau kosong.')
                ->withInput();
        }

        if (strlen($password) < 8) {
            return redirect()->back()
                ->with('error', 'Password minimal 8 karakter.')
                ->withInput();
        }

        $user = $this->getActiveUserByEmail($email);
        if (! $user) {
            return redirect()->to(site_url('auth/forgot'))
                ->with('error', 'Tautan reset tidak valid atau sudah kedaluwarsa.');
        }

        $reset = $this->resetModel->findValidReset((int) $user['id'], $token);
        if (! $reset) {
            return redirect()->to(site_url('auth/forgot'))
                ->with('error', 'Tautan reset tidak valid atau sudah kedaluwarsa.');
        }

        $userId = (int) $user['id'];
        $now    = date('Y-m-d H:i:s');

        // Update password
        $this->userModel->update($userId, [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        // Invalidate all pending tokens for this user (including current)
        $this->resetModel->where('user_id', $userId)
            ->where('used_at', null)
            ->set('used_at', $now)
            ->update();

        return redirect()->to(site_url('login'))
            ->with('message', 'Password berhasil diperbarui. Silakan login dengan password baru.');
    }

    private function getActiveUserByEmail(string $email): ?array
    {
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $this->userModel
            ->where('email', $email)
            ->where('active', 1)
            ->first();
    }
}
