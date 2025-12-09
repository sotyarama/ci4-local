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
            return $this->forbidden();
        }

        $method = strtolower($request->getMethod());

        // Auditor: read-only
        if ($role === 'auditor' && $method !== 'get') {
            return $this->forbidden();
        }

        // Staff: blokir update area sensitif (user, settings, harga menu/master products)
        if ($role === 'staff' && $method !== 'get') {
            $path = strtolower($request->uri->getPath());
            $blockedPrefixes = [
                'master/products', // termasuk create/update harga menu
                'users',
                'settings',
            ];

            foreach ($blockedPrefixes as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    return $this->forbidden();
                }
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }

    private function forbidden()
    {
        return Services::response()
            ->setStatusCode(403)
            ->setBody('Forbidden');
    }
}
