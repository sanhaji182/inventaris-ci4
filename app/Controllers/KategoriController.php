<?php

namespace App\Controllers;

use App\Models\KategoriModel;

class KategoriController extends BaseController
{
    protected KategoriModel $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $kategori = $this->kategoriModel->orderBy('id', 'DESC')->findAll();

        return view('kategori/index', [
            'title'    => 'Kategori Barang',
            'kategori' => $kategori,
        ]);
    }

    public function save()
    {
        $id = (int) $this->request->getPost('id');
        $nama = trim((string) $this->request->getPost('nama'));
        $keterangan = trim((string) $this->request->getPost('keterangan'));

        $rules = [
            'nama' => 'required|min_length[2]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = ['nama' => $nama, 'keterangan' => $keterangan];

        if ($id > 0) {
            $this->kategoriModel->update($id, $data);
            return redirect()->to('/kategori')->with('sukses', 'Kategori berhasil diperbarui.');
        }

        $this->kategoriModel->insert($data);
        return redirect()->to('/kategori')->with('sukses', 'Kategori baru berhasil ditambahkan.');
    }

    public function delete(int $id)
    {
        // Cek apakah dipakai barang
        $db = \Config\Database::connect();
        $dipakai = $db->table('barang')->where('kategori_id', $id)->countAllResults();
        if ($dipakai > 0) {
            return redirect()->to('/kategori')->with('error', "Kategori tidak dapat dihapus karena masih digunakan oleh {$dipakai} barang.");
        }

        $this->kategoriModel->delete($id);
        return redirect()->to('/kategori')->with('sukses', 'Kategori berhasil dihapus.');
    }
}
