<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        $user = new UserModel();

        return view('user/index', [
            'title' => 'Manajemen Pengguna',
            'users' => $user->orderBy('role')->orderBy('username')->findAll(),
        ]);
    }

    public function save()
    {
        $user = new UserModel();
        $id   = (int) $this->request->getPost('id');

        $data = [
            'username' => strtolower(trim((string) $this->request->getPost('username'))),
            'nama'     => trim((string) $this->request->getPost('nama')),
            'role'     => (string) $this->request->getPost('role'),
            'status'   => (string) $this->request->getPost('status') ?: 'aktif',
        ];

        $pass = (string) $this->request->getPost('password');
        if ($pass !== '') {
            $data['password_hash'] = password_hash($pass, PASSWORD_DEFAULT);
        }

        if ($data['username'] === '' || $data['nama'] === '') {
            return redirect()->back()->withInput()->with('error', 'Username dan nama wajib diisi.');
        }

        if ($id > 0) {
            $user->update($id, $data);
            $msg = 'Pengguna berhasil diperbarui.';
        } else {
            if ($pass === '') {
                return redirect()->back()->withInput()->with('error', 'Password wajib untuk pengguna baru.');
            }
            $data['created_at'] = date('Y-m-d H:i:s');
            $user->insert($data);
            $msg = 'Pengguna baru berhasil ditambahkan.';
        }

        return redirect()->to('/user')->with('sukses', $msg);
    }

    public function delete(int $id)
    {
        $user = new UserModel();

        // Cegah hapus diri sendiri
        if ($id === (int) session('user_id')) {
            return redirect()->to('/user')->with('error', 'Tidak bisa menghapus akun yang sedang digunakan.');
        }

        $user->delete($id);

        return redirect()->to('/user')->with('sukses', 'Pengguna berhasil dihapus.');
    }
}
