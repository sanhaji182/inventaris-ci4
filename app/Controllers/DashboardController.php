<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\RiwayatStokModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Ringkasan Aset & Stok
        $totalBarang = $db->table('barang')->countAllResults();
        $totalStok = (int) ($db->table('barang')->selectSum('stok')->get()->getRow()->stok ?? 0);

        // Valuasi total modal (aset) & potensi omzet jika terjual semua
        $valuasi = $db->table('barang')
            ->select('SUM(stok * harga_beli) AS total_modal, SUM(stok * harga_jual) AS potensi_omzet')
            ->get()
            ->getRow();
        $totalModal = (float) ($valuasi->total_modal ?? 0);
        $potensiOmzet = (float) ($valuasi->potensi_omzet ?? 0);
        $potensiLaba = $potensiOmzet - $totalModal;

        // 2. Realisasi Keuntungan dari Barang Keluar/Terjual
        $realisasiLaba = (float) ($db->table('riwayat_stok')
            ->where('jenis', 'keluar')
            ->selectSum('total_laba')
            ->get()
            ->getRow()->total_laba ?? 0);

        $totalItemTerjual = (int) ($db->table('riwayat_stok')
            ->where('jenis', 'keluar')
            ->selectSum('jumlah')
            ->get()
            ->getRow()->jumlah ?? 0);

        // 3. Stok Menipis (<= 2 unit)
        $stokMenipis = $db->table('barang')
            ->select('barang.*, kategori.nama AS kategori_nama')
            ->join('kategori', 'kategori.id = barang.kategori_id', 'left')
            ->where('stok <=', 2)
            ->orderBy('stok', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // 4. Riwayat Transaksi Terakhir
        $riwayatModel = new RiwayatStokModel();
        $riwayatTerakhir = $riwayatModel->getRiwayatLengkap(6);

        $data = [
            'title'            => 'Dashboard Inventaris',
            'totalBarang'      => $totalBarang,
            'totalStok'        => $totalStok,
            'totalModal'       => $totalModal,
            'potensiOmzet'     => $potensiOmzet,
            'potensiLaba'      => $potensiLaba,
            'realisasiLaba'    => $realisasiLaba,
            'totalItemTerjual' => $totalItemTerjual,
            'stokMenipis'      => $stokMenipis,
            'riwayatTerakhir'  => $riwayatTerakhir,
        ];

        return view('dashboard/index', $data);
    }
}
