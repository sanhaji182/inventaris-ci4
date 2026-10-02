<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\SupplierModel;
use App\Models\UnitModel;

class UnitController extends BaseController
{
    public function index()
    {
        $unit     = new UnitModel();
        $barang   = new BarangModel();
        $kategori = new KategoriModel();

        $filter = [
            'status'      => (string) $this->request->getGet('status'),
            'kategori_id' => (int) $this->request->getGet('kategori'),
            'barang_id'   => (int) $this->request->getGet('barang'),
            'cari'        => (string) $this->request->getGet('cari'),
        ];

        return view('unit/index', [
            'title'    => 'Unit Stok Fisik',
            'units'    => $unit->listing($filter),
            'kategori' => $kategori->findAll(),
            'barang'   => $barang->findAll(),
            'filter'   => $filter,
        ]);
    }

    public function form(int $id = 0)
    {
        $unit     = new UnitModel();
        $barang   = new BarangModel();
        $supplier = new SupplierModel();

        $row = $id > 0 ? $unit->find($id) : null;

        return view('unit/form', [
            'title'    => $id > 0 ? 'Edit Unit' : 'Tambah Unit Manual',
            'unit'     => $row,
            'barang'   => $barang->findAll(),
            'supplier' => $supplier->findAll(),
            'nextKode' => $row ? $row['kode'] : $unit->kodeBerikutnya(),
        ]);
    }

    public function save()
    {
        $unit = new UnitModel();
        $id   = (int) $this->request->getPost('id');

        $data = [
            'kode'          => strtoupper(trim((string) $this->request->getPost('kode'))),
            'barang_id'     => (int) $this->request->getPost('barang_id'),
            'imei'          => trim((string) $this->request->getPost('imei')) ?: null,
            'kondisi'       => (string) $this->request->getPost('kondisi') ?: 'mulus',
            'harga_beli'    => (float) str_replace(['.', ','], ['', '.'], (string) $this->request->getPost('harga_beli')),
            'supplier_id'   => (int) $this->request->getPost('supplier_id') ?: null,
            'tanggal_masuk' => (string) $this->request->getPost('tanggal_masuk') ?: date('Y-m-d'),
            'status'        => (string) $this->request->getPost('status') ?: 'tersedia',
            'lokasi'        => trim((string) $this->request->getPost('lokasi')) ?: null,
            'catatan'       => trim((string) $this->request->getPost('catatan')) ?: null,
        ];

        if (! $data['barang_id'] || $data['kode'] === '') {
            return redirect()->back()->withInput()->with('error', 'Kode dan barang wajib diisi.');
        }

        if ($id > 0) {
            $unit->update($id, $data);
            $msg = 'Unit berhasil diperbarui.';
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $unit->insert($data);
            $msg = 'Unit berhasil ditambahkan.';
        }

        return redirect()->to('/unit')->with('sukses', $msg);
    }

    public function tersediaJson(int $barangId)
    {
        $unit = new UnitModel();
        return $this->response->setJSON($unit->unitTersedia($barangId));
    }

    public function ubahStatus(int $id)
    {
        $unit   = new UnitModel();
        $status = (string) $this->request->getPost('status');

        if (! in_array($status, UnitModel::STATUS, true)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $unit->update($id, [
            'status'  => $status,
            'catatan' => trim((string) $this->request->getPost('catatan')) ?: null,
        ]);

        return redirect()->back()->with('sukses', "Status unit diubah menjadi {$status}.");
    }

    public function delete(int $id)
    {
        $unit = new UnitModel();

        // Unit terjual tidak boleh dihapus agar integritas laporan laba terjaga
        $u = $unit->find($id);
        if ($u && $u['status'] === 'terjual') {
            return redirect()->to('/unit')->with('error', 'Unit sudah terjual, tidak boleh dihapus demi integritas laporan laba/rugi.');
        }

        $unit->delete($id);

        return redirect()->to('/unit')->with('sukses', 'Unit berhasil dihapus.');
    }
}
