<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->orderBy('id', 'ASC')->findAll();

        return view('user/index', [
            'title' => 'Manajemen Hak Akses Pengguna',
            'users' => $users,
        ]);
    }

    public function save()
    {
        $id = (int) $this->request->getPost('id');
        $nama = trim((string) $this->request->getPost('nama'));
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $role = (string) $this->request->getPost('role');

        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'role' => 'required|in_list[admin,pengelola]',
        ];

        if ($id === 0) {
            $rules['username'] = 'required|is_unique[users.username]|min_length[3]';
            $rules['password'] = 'required|min_length[6]';
        } else {
            $rules['username'] = "required|is_unique[users.username,id,{$id}]|min_length[3]";
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama'     => $nama,
            'username' => $username,
            'role'     => $role,
        ];

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if ($id > 0) {
            $this->userModel->update($id, $data);
            return redirect()->to('/user')->with('sukses', 'Data pengguna berhasil diperbarui.');
        }

        $this->userModel->insert($data);
        return redirect()->to('/user')->with('sukses', 'Pengguna baru berhasil ditambahkan.');
    }

    public function delete(int $id)
    {
        if ($id === (int) session()->get('user_id')) {
            return redirect()->to('/user')->with('error', 'Tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $this->userModel->delete($id);
        return redirect()->to('/user')->with('sukses', 'Pengguna berhasil dihapus.');
    }
}
