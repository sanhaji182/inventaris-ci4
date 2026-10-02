<?php

namespace App\Controllers;

use App\Models\SupplierModel;

class SupplierController extends BaseController
{
    public function index()
    {
        $sup = new SupplierModel();

        return view('supplier/index', [
            'title'    => 'Daftar Supplier',
            'supplier' => $sup->withUtang(),
        ]);
    }

    public function form(int $id = 0)
    {
        $sup = new SupplierModel();
        $row = $id > 0 ? $sup->find($id) : null;

        return view('supplier/form', [
            'title'    => $id > 0 ? 'Edit Supplier' : 'Tambah Supplier',
            'supplier' => $row,
        ]);
    }

    public function save()
    {
        $sup = new SupplierModel();
        $id  = (int) $this->request->getPost('id');

        $data = [
            'kode'    => strtoupper(trim((string) $this->request->getPost('kode'))),
            'nama'    => trim((string) $this->request->getPost('nama')),
            'alamat'  => trim((string) $this->request->getPost('alamat')) ?: null,
            'telepon' => trim((string) $this->request->getPost('telepon')) ?: null,
            'catatan' => trim((string) $this->request->getPost('catatan')) ?: null,
        ];

        if ($data['nama'] === '') {
            return redirect()->back()->withInput()->with('error', 'Nama supplier wajib diisi.');
        }

        if ($id > 0) {
            $sup->update($id, $data);
            $msg = 'Supplier berhasil diperbarui.';
        } else {
            if ($data['kode'] === '') {
                $last = $sup->select('kode')->orderBy('id', 'DESC')->first();
                $seq  = $last ? ((int) preg_replace('/\D/', '', $last['kode'])) + 1 : 1;
                $data['kode'] = 'SUP-' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
            }
            $sup->insert($data);
            $msg = 'Supplier berhasil ditambahkan.';
        }

        return redirect()->to('/supplier')->with('sukses', $msg);
    }

    public function delete(int $id)
    {
        $sup = new SupplierModel();

        // Cegah hapus bila ada riwayat pembelian atau utang
        $adaTrx = $sup->db->table('pembelian')->where('supplier_id', $id)->countAllResults();
        if ($adaTrx > 0) {
            return redirect()->to('/supplier')->with('error', 'Supplier tidak bisa dihapus karena memiliki riwayat pembelian.');
        }

        $sup->delete($id);

        return redirect()->to('/supplier')->with('sukses', 'Supplier berhasil dihapus.');
    }
}
