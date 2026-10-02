<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $session   = session();
        $username  = (string) $this->request->getPost('username');
        $password  = (string) $this->request->getPost('password');

        if ($username === '' || $password === '') {
            return redirect()->back()->with('error', 'Username dan password wajib diisi.');
        }

        $user = (new UserModel())->attempt($username, $password);

        if ($user === null) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username atau password salah.');
        }

        $session->regenerate();
        $session->set([
            'logged_in' => true,
            'user_id'   => (int) $user['id'],
            'username'  => $user['username'],
            'nama'      => $user['nama'],
            'role'      => $user['role'],
        ]);

        $target = $session->get('redirect_after_login') ?? '/dashboard';
        $session->remove('redirect_after_login');

        return redirect()->to($target)->with('sukses', 'Selamat datang, ' . $user['nama'] . '!');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login')->with('sukses', 'Anda telah keluar.');
    }
}
