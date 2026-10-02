<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (! $session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
        }

        // Cek argument role jika dispesifikasikan (misal role:admin)
        if (! empty($arguments)) {
            $userRole = $session->get('user_role');
            if (! in_array($userRole, $arguments, true)) {
                return redirect()->to('/dashboard')->with('error', 'Akses ditolak: menu ini khusus pengguna berwenang.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
