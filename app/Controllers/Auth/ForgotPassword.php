<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\PasswordResetModel;
use App\Models\UserModel;
use App\Services\AuditLogService;
use Config\Services;

class ForgotPassword extends BaseController
{
    private UserModel $userModel;
    private PasswordResetModel $resetModel;
    private AuditLogService $auditService;
    private int $expiryMinutes = 60;
    private int $throttleSeconds = 120;

    public function __construct()
    {
        $this->userModel    = new UserModel();
        $this->resetModel   = new PasswordResetModel();
        $this->auditService = new AuditLogService();
    }

    public function index()
    {
        return view('auth/forgot', [
            'title'    => 'Lupa Password',
            'subtitle' => 'Kirim tautan reset ke email',
        ]);
    }

    public function send()
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $ip    = $this->request->getIPAddress();
        $ua    = $this->request->getUserAgent()->getAgentString();

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()
                ->with('error', 'Silakan masukkan email yang valid.')
                ->withInput();
        }

        $user = $this->userModel
            ->where('email', $email)
            ->where('active', 1)
            ->first();

        $shouldCreate = true;
        if ($user) {
            $lastReset = $this->resetModel
                ->where('user_id', $user['id'])
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($lastReset && strtotime((string) $lastReset['created_at']) > (time() - $this->throttleSeconds)) {
                $shouldCreate = false;

                // Log throttled request
                $this->auditService->logAuth('forgot_throttled', (int) $user['id'], [
                    'email' => $email,
                    'ip'    => $ip,
                ], 'Password reset request throttled');
            }
        } else {
            $shouldCreate = false; // user tidak ada -> tetap balikan sukses generic

            // Log unknown email attempt (for security monitoring)
            $this->auditService->logAuth('forgot_unknown', null, [
                'email' => $email,
                'ip'    => $ip,
            ], 'Password reset requested for unknown email');
        }

        $resetLink = null;
        if ($shouldCreate && $user) {
            $rawToken   = $this->generateToken();
            $expiresAt  = date('Y-m-d H:i:s', time() + ($this->expiryMinutes * 60));
            $this->resetModel->createTokenForUser((int) $user['id'], $rawToken, $expiresAt, $ip, $ua);

            $resetLink = site_url('auth/reset?token=' . urlencode($rawToken) . '&email=' . urlencode($email));
            $emailSent = $this->sendEmail($email, $resetLink, $this->expiryMinutes);

            // Log reset request + email status
            $this->auditService->logAuth($emailSent ? 'forgot_email_sent' : 'forgot_email_fail', (int) $user['id'], [
                'email' => $email,
                'ip'    => $ip,
            ], $emailSent ? 'Password reset email sent' : 'Password reset email failed to send');
        }

        // Selalu tampilkan pesan sukses generic (tidak bocorkan keberadaan email)
        return redirect()->back()
            ->with('message', 'Jika email terdaftar, tautan reset telah dikirim.');
    }

    private function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    private function sendEmail(string $toEmail, string $link, int $expiresMinutes): bool
    {
        $config = config('Email');
        $email  = Services::email();

        $fromEmail = $config->fromEmail ?: 'no-reply@example.com';
        $fromName  = $config->fromName ?: 'POS System';
        $email->setFrom($fromEmail, $fromName);

        $email->setTo($toEmail);
        $email->setSubject('Reset Password POS');

        $body = "Anda menerima email ini karena ada permintaan reset password.\n\n";
        $body .= "Klik tautan berikut untuk mengatur ulang password (berlaku {$expiresMinutes} menit):\n";
        $body .= $link . "\n\n";
        $body .= "Jika Anda tidak meminta reset, abaikan email ini.";

        $email->setMessage($body);

        try {
            $email->send();
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mengirim email reset password: {message}', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
