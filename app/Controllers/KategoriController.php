<?php

namespace App\Controllers;

use App\Models\KategoriModel;

class KategoriController extends BaseController
{
    public function index()
    {
        $kategori = new KategoriModel();

        return view('kategori/index', [
            'title'    => 'Kategori Barang',
            'kategori' => $kategori->withJumlahBarang(),
        ]);
    }

    public function save()
    {
        $kategori = new KategoriModel();
        $id       = (int) $this->request->getPost('id');

        $data = [
            'kode'       => strtoupper(trim((string) $this->request->getPost('kode'))),
            'nama'       => trim((string) $this->request->getPost('nama')),
            'keterangan' => trim((string) $this->request->getPost('keterangan')) ?: null,
        ];

        if ($data['kode'] === '' || $data['nama'] === '') {
            return redirect()->back()->withInput()->with('error', 'Kode dan nama kategori wajib diisi.');
        }

        if ($id > 0) {
            $kategori->update($id, $data);
            $msg = 'Kategori berhasil diperbarui.';
        } else {
            $kategori->insert($data);
            $msg = 'Kategori baru berhasil ditambahkan.';
        }

        return redirect()->to('/kategori')->with('sukses', $msg);
    }

    public function delete(int $id)
    {
        $kategori = new KategoriModel();

        // Cegah hapus bila masih dipakai di barang
        $terpakai = $kategori->db->table('barang')->where('kategori_id', $id)->countAllResults();
        if ($terpakai > 0) {
            return redirect()->to('/kategori')->with('error', "Kategori tidak bisa dihapus: masih digunakan oleh {$terpakai} barang.");
        }

        $kategori->delete($id);

        return redirect()->to('/kategori')->with('sukses', 'Kategori berhasil dihapus.');
    }
}
