<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanModel;
use App\Models\UtangModel;

class LaporanController extends BaseController
{
    public function index()
    {
        return $this->labaRugi();
    }

    public function labaRugi()
    {
        $penjualan = new PenjualanModel();

        $mulai   = (string) $this->request->getGet('mulai') ?: date('Y-m-01');
        $selesai = (string) $this->request->getGet('selesai') ?: date('Y-m-d');

        $rows = $penjualan->db->table('penjualan_unit')
            ->select('penjualan.tanggal, penjualan.no AS penjualan_no, barang.kode AS barang_kode,
                      barang.nama AS barang_nama, unit.kode AS unit_kode, unit.imei,
                      penjualan_unit.harga_jual, penjualan_unit.harga_beli,
                      penjualan_unit.laba, penjualan_unit.laba_persen')
            ->join('penjualan', 'penjualan.id = penjualan_unit.penjualan_id')
            ->join('barang', 'barang.id = penjualan_unit.barang_id')
            ->join('unit', 'unit.id = penjualan_unit.unit_id')
            ->where('penjualan.tanggal >=', $mulai)
            ->where('penjualan.tanggal <=', $selesai)
            ->orderBy('penjualan.tanggal', 'DESC')
            ->orderBy('penjualan.id', 'DESC')
            ->get()->getResultArray();

        $totalJual = 0.0;
        $totalBeli = 0.0;
        $totalLaba = 0.0;

        foreach ($rows as $r) {
            $totalJual += (float) $r['harga_jual'];
            $totalBeli += (float) $r['harga_beli'];
            $totalLaba += (float) $r['laba'];
        }

        return view('laporan/laba_rugi', [
            'title'     => 'Laporan Laba/Rugi Penjualan',
            'rows'      => $rows,
            'mulai'     => $mulai,
            'selesai'   => $selesai,
            'totalJual' => $totalJual,
            'totalBeli' => $totalBeli,
            'totalLaba' => $totalLaba,
            'margin'    => $totalJual > 0 ? ($totalLaba / $totalJual) * 100 : 0.0,
        ]);
    }

    public function utang()
    {
        $utang = new UtangModel();

        return view('laporan/utang', [
            'title' => 'Laporan Utang Usaha & Aging',
            'utang' => $utang->listing(),
            'aging' => $utang->aging(),
        ]);
    }

    public function stok()
    {
        $barang = new BarangModel();

        return view('laporan/stok', [
            'title'  => 'Laporan Posisi Stok',
            'barang' => $barang->withStok(),
        ]);
    }
}
