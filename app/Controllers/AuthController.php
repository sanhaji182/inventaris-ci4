<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function doLogin()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password tidak sesuai.');
        }

        session()->set([
            'user_id'   => (int) $user['id'],
            'user_nama' => $user['nama'],
            'user_name' => $user['username'],
            'user_role' => $user['role'],
            'logged_in' => true,
        ]);

        return redirect()->to('/dashboard')->with('sukses', 'Selamat datang kembali, ' . esc($user['nama']) . '!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('sukses', 'Anda telah berhasil keluar.');
    }
}
