<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;

class BarangController extends BaseController
{
    public function index()
    {
        $barang   = new BarangModel();
        $kategori = new KategoriModel();

        $cari = (string) $this->request->getGet('cari');
        $kat  = (int) $this->request->getGet('kategori');

        return view('barang/index', [
            'title'    => 'Katalog Barang',
            'barang'   => $barang->withStok($cari ?: null, $kat ?: null),
            'kategori' => $kategori->findAll(),
            'cari'     => $cari,
            'katPilih' => $kat,
        ]);
    }

    public function form(int $id = 0)
    {
        $barang   = new BarangModel();
        $kategori = new KategoriModel();

        $row = $id > 0 ? $barang->find($id) : null;

        return view('barang/form', [
            'title'    => $id > 0 ? 'Edit Barang' : 'Tambah Barang',
            'barang'   => $row,
            'kategori' => $kategori->findAll(),
            'nextKode' => $row ? $row['kode'] : $barang->kodeBerikutnya(),
        ]);
    }

    public function save()
    {
        $barang = new BarangModel();
        $id     = (int) $this->request->getPost('id');

        $data = [
            'kode'        => strtoupper(trim((string) $this->request->getPost('kode'))),
            'nama'        => trim((string) $this->request->getPost('nama')),
            'kategori_id' => (int) $this->request->getPost('kategori_id'),
            'merek'       => trim((string) $this->request->getPost('merek')) ?: null,
            'spek'        => trim((string) $this->request->getPost('spek')) ?: null,
            'harga_jual'  => (float) str_replace(['.', ','], ['', '.'], (string) $this->request->getPost('harga_jual')),
            'stok_min'    => (int) $this->request->getPost('stok_min'),
        ];

        if ($data['kode'] === '' || $data['nama'] === '' || ! $data['kategori_id']) {
            return redirect()->back()->withInput()->with('error', 'Kode, nama, dan kategori wajib diisi.');
        }

        // Upload foto opsional
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && ! $foto->hasMoved()) {
            $namaBaru = $foto->getRandomName();
            $foto->move(ROOTPATH . 'public/uploads/barang', $namaBaru);
            $data['foto'] = $namaBaru;
        }

        if ($id > 0) {
            $barang->update($id, $data);
            $msg = 'Barang berhasil diperbarui.';
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $barang->insert($data);
            $msg = 'Barang berhasil ditambahkan ke katalog.';
        }

        return redirect()->to('/barang')->with('sukses', $msg);
    }

    public function delete(int $id)
    {
        $barang = new BarangModel();

        // Validasi: tidak boleh hapus bila ada unit fisik yang pernah terdaftar
        $unitCount = $barang->db->table('unit')->where('barang_id', $id)->countAllResults();
        if ($unitCount > 0) {
            return redirect()->to('/barang')->with('error', "Barang tidak bisa dihapus: masih tercatat {$unitCount} unit fisik.");
        }

        $barang->delete($id);

        return redirect()->to('/barang')->with('sukses', 'Barang berhasil dihapus.');
    }

    /** Riwayat unit fisik dari barang ini. */
    public function riwayat(int $id)
    {
        $barang = (new BarangModel())->findWithDetail($id);
        if ($barang === null) {
            return redirect()->to('/barang')->with('error', 'Barang tidak ditemukan.');
        }

        $units = (new BarangModel())->db->table('unit')
            ->select('unit.*, supplier.nama AS supplier_nama, penjualan.no AS penjualan_no,
                      penjualan_unit.harga_jual AS jual_aktual, penjualan_unit.laba')
            ->join('supplier', 'supplier.id = unit.supplier_id', 'left')
            ->join('penjualan_unit', 'penjualan_unit.unit_id = unit.id', 'left')
            ->join('penjualan', 'penjualan.id = penjualan_unit.penjualan_id', 'left')
            ->where('unit.barang_id', $id)
            ->orderBy('unit.tanggal_masuk', 'DESC')
            ->get()->getResultArray();

        return view('barang/riwayat', [
            'title'  => 'Riwayat Unit · ' . $barang['nama'],
            'barang' => $barang,
            'units'  => $units,
        ]);
    }
}
