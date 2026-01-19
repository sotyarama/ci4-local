<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Services\AuditLogService;

class Logout extends BaseController
{
    public function index()
    {
        $auditService = new AuditLogService();

        // Capture user info before destroying session
        $userId   = session('user_id');
        $username = session('username');
        $ip       = $this->request->getIPAddress();

        // Log logout event (while session still exists)
        if ($userId) {
            $auditService->logAuth('logout', (int) $userId, [
                'username' => $username,
                'ip'       => $ip,
            ], 'User logged out');
        }

        session()->destroy();
        return redirect()->to('/login');
    }
}
