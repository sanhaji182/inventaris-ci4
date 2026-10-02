<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\RiwayatStokModel;

class StokController extends BaseController
{
    protected BarangModel $barangModel;
    protected RiwayatStokModel $riwayatModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->riwayatModel = new RiwayatStokModel();
    }

    public function index()
    {
        $riwayat = $this->riwayatModel->getRiwayatLengkap(100);
        $barangList = $this->barangModel->orderBy('nama_barang', 'ASC')->findAll();

        return view('stok/index', [
            'title'      => 'Pencatatan Keluar/Masuk Stok & Margin Laba',
            'riwayat'    => $riwayat,
            'barangList' => $barangList,
        ]);
    }

    public function proses()
    {
        $barangId = (int) $this->request->getPost('barang_id');
        $jenis = (string) $this->request->getPost('jenis'); // masuk / keluar / penyesuaian
        $jumlah = (int) $this->request->getPost('jumlah');
        $hargaTransaksi = (float) $this->request->getPost('harga_transaksi');
        $keterangan = trim((string) $this->request->getPost('keterangan'));
        $tanggal = (string) $this->request->getPost('tanggal') ?: date('Y-m-d');

        $barang = $this->barangModel->find($barangId);
        if (! $barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        if ($jumlah <= 0) {
            return redirect()->back()->with('error', 'Jumlah harus lebih besar dari 0.');
        }

        $totalLaba = 0.00;
        $stokLama = (int) $barang['stok'];

        if ($jenis === 'keluar') {
            if ($stokLama < $jumlah) {
                return redirect()->back()->with('error', "Stok barang tidak mencukupi (sisa {$stokLama} unit).");
            }
            $stokBaru = $stokLama - $jumlah;

            // Hitung laba riil: (harga jual transaksi - modal beli barang) * qty
            $hargaJualSatuan = ($hargaTransaksi > 0) ? $hargaTransaksi : (float) $barang['harga_jual'];
            $modalSatuan = (float) $barang['harga_beli'];
            $totalLaba = ($hargaJualSatuan - $modalSatuan) * $jumlah;
            $hargaTransaksi = $hargaJualSatuan;

        } elseif ($jenis === 'masuk') {
            $stokBaru = $stokLama + $jumlah;
            if ($hargaTransaksi <= 0) {
                $hargaTransaksi = (float) $barang['harga_beli'];
            }
        } else {
            // penyesuaian stok langsung ke nominal tertentu
            $stokBaru = $jumlah;
            $jumlah = abs($stokBaru - $stokLama);
        }

        // 1. Simpan riwayat
        $this->riwayatModel->insert([
            'barang_id'       => $barangId,
            'user_id'         => (int) session()->get('user_id'),
            'jenis'           => $jenis,
            'jumlah'          => $jumlah,
            'harga_transaksi' => $hargaTransaksi,
            'total_laba'      => $totalLaba,
            'keterangan'      => $keterangan,
            'tanggal'         => $tanggal,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        // 2. Update stok barang
        $this->barangModel->update($barangId, ['stok' => $stokBaru]);

        $pesan = "Transaksi stok berhasil dicatat. Stok {$barang['nama_barang']} kini {$stokBaru} unit.";
        if ($totalLaba > 0) {
            $pesan .= ' Laba tercatat: Rp ' . number_format($totalLaba, 0, ',', '.');
        }

        return redirect()->to('/stok')->with('sukses', $pesan);
    }
}
