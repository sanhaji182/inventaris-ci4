<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Batasi akses rute ke peran tertentu.
 *
 * Pemakaian pada definisi rute:
 *   $routes->get('user', 'UserController::index', ['filter' => 'role:admin']);
 *   $routes->group('...', ..., ['filter' => 'role:admin,pembeli']);
 *
 * Nilai yang diizinkan dibaca dari kolom `role` pada tabel `users`.
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (empty($arguments)) {
            return null;
        }

        $raw     = is_array($arguments) ? reset($arguments) : $arguments;
        $allowed = array_map('trim', explode(',', (string) $raw));
        $role    = (string) session('role');

        if (! in_array($role, $allowed, true)) {
            return redirect()->to('/dashboard')
                ->with('error', 'Akses ditolak: modul ini khusus untuk ' . implode(' / ', $allowed) . '.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
