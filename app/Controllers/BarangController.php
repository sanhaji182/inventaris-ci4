<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\RiwayatStokModel;

class BarangController extends BaseController
{
    protected BarangModel $barangModel;
    protected KategoriModel $kategoriModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $cari = trim((string) $this->request->getGet('cari'));
        $kategoriId = (int) $this->request->getGet('kategori');

        $builder = $this->barangModel->db->table('barang')
            ->select('barang.*, kategori.nama AS kategori_nama')
            ->join('kategori', 'kategori.id = barang.kategori_id', 'left');

        if ($cari !== '') {
            $builder->groupStart()
                ->like('barang.nama_barang', $cari)
                ->orLike('barang.kode_barang', $cari)
                ->orLike('barang.sumber_toko', $cari)
                ->orLike('barang.minus_kondisi', $cari)
                ->groupEnd();
        }

        if ($kategoriId > 0) {
            $builder->where('barang.kategori_id', $kategoriId);
        }

        $barang = $builder->orderBy('barang.id', 'DESC')->get()->getResultArray();
        $kategori = $this->kategoriModel->orderBy('nama', 'ASC')->findAll();

        return view('barang/index', [
            'title'      => 'Katalog Inventaris Barang',
            'barang'     => $barang,
            'kategori'   => $kategori,
            'cari'       => $cari,
            'kategoriId' => $kategoriId,
        ]);
    }

    public function form(?int $id = null)
    {
        $barang = null;
        if ($id) {
            $barang = $this->barangModel->find($id);
            if (! $barang) {
                return redirect()->to('/barang')->with('error', 'Barang tidak ditemukan.');
            }
        }

        $kodeOtomatis = $barang ? $barang['kode_barang'] : $this->barangModel->kodeBerikutnya();
        $kategori = $this->kategoriModel->orderBy('nama', 'ASC')->findAll();

        return view('barang/form', [
            'title'        => $barang ? 'Edit Data Barang' : 'Tambah Barang Baru',
            'barang'       => $barang,
            'kodeOtomatis' => $kodeOtomatis,
            'kategori'     => $kategori,
        ]);
    }

    public function save()
    {
        $id = (int) $this->request->getPost('id');
        $isNew = ($id === 0);

        $rules = [
            'nama_barang' => 'required|min_length[3]|max_length[150]',
            'kategori_id' => 'required|is_not_unique[kategori.id]',
            'harga_beli'  => 'required|numeric|greater_than_equal_to[0]',
            'harga_jual'  => 'required|numeric|greater_than_equal_to[0]',
            'stok'        => 'required|integer|greater_than_equal_to[0]',
            'satuan'      => 'required|max_length[30]',
        ];

        if ($isNew) {
            $rules['kode_barang'] = 'required|is_unique[barang.kode_barang]';
        } else {
            $rules['kode_barang'] = "required|is_unique[barang.kode_barang,id,{$id}]";
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode_barang'    => trim((string) $this->request->getPost('kode_barang')),
            'nama_barang'    => trim((string) $this->request->getPost('nama_barang')),
            'kategori_id'    => (int) $this->request->getPost('kategori_id'),
            'stok'           => (int) $this->request->getPost('stok'),
            'satuan'         => trim((string) $this->request->getPost('satuan')),
            'harga_beli'     => (float) $this->request->getPost('harga_beli'),
            'harga_jual'     => (float) $this->request->getPost('harga_jual'),
            'link_pembelian' => trim((string) $this->request->getPost('link_pembelian')),
            'sumber_toko'    => trim((string) $this->request->getPost('sumber_toko')),
            'minus_kondisi'  => trim((string) $this->request->getPost('minus_kondisi')),
            'catatan'        => trim((string) $this->request->getPost('catatan')),
        ];

        if ($isNew) {
            $newId = $this->barangModel->insert($data);
            // Catat ke riwayat stok awal
            if ($data['stok'] > 0) {
                $riwayatModel = new RiwayatStokModel();
                $riwayatModel->insert([
                    'barang_id'       => $newId,
                    'user_id'         => (int) session()->get('user_id'),
                    'jenis'           => 'masuk',
                    'jumlah'          => $data['stok'],
                    'harga_transaksi' => $data['harga_beli'],
                    'total_laba'      => 0,
                    'keterangan'      => 'Saldo stok awal barang baru',
                    'tanggal'         => date('Y-m-d'),
                    'created_at'      => date('Y-m-d H:i:s'),
                ]);
            }
            return redirect()->to('/barang')->with('sukses', 'Barang baru berhasil ditambahkan.');
        }

        $this->barangModel->update($id, $data);
        return redirect()->to('/barang')->with('sukses', 'Data barang berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $barang = $this->barangModel->find($id);
        if (! $barang) {
            return redirect()->to('/barang')->with('error', 'Barang tidak ditemukan.');
        }

        $this->barangModel->delete($id);
        return redirect()->to('/barang')->with('sukses', 'Barang berhasil dihapus.');
    }
}
