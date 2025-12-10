<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $role    = strtolower((string) ($session->get('role') ?? $session->get('role_name') ?? ''));

        if (! $role) {
            return redirect()->to(site_url('login'));
        }

        $allowedRoles = $arguments ?? [];
        if (! empty($allowedRoles) && ! in_array($role, $allowedRoles, true)) {
            return $this->forbidden($request, 'Akses ditolak untuk role ini.');
        }

        $method = strtolower($request->getMethod());

        // Auditor: read-only
        if ($role === 'auditor' && $method !== 'get') {
            return $this->forbidden($request, 'Auditor bersifat read-only.');
        }

        // Staff: blokir update area sensitif (user, settings)
        if ($role === 'staff') {
            $path = strtolower($request->uri->getPath());
            $blockedPrefixes = [
                'users',
                'settings',
            ];

            foreach ($blockedPrefixes as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    if ($method === 'get') {
                        return $this->forbidden($request, 'Akses halaman sensitif dibatasi untuk Staff.');
                    }

                    return $this->forbidden($request, 'Aksi ini dibatasi untuk Staff.');
                }
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }

    private function forbidden(RequestInterface $request, string $message)
    {
        $session = session();
        $session->setFlashdata('error', $message);

        $referer = $request->getServer('HTTP_REFERER');
        $target  = $referer ?: site_url('/');

        return redirect()->to($target);
    }
}
