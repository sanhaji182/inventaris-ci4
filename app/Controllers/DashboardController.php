<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\PembelianModel;
use App\Models\PenjualanModel;
use App\Models\UnitModel;
use App\Models\UtangModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $unit    = new UnitModel();
        $utang   = new UtangModel();

        // ---- Kartu ringkasan (bulan ini) ----
        $awalBulan  = date('Y-m-01');
        $penjualan  = new PenjualanModel();

        $bulanIni = $penjualan->db->table('penjualan')
            ->select('COUNT(*) AS trx, COALESCE(SUM(total),0) AS omzet')
            ->where('tanggal >=', $awalBulan)
            ->get()->getRowArray();

        $labaBulanIni = $penjualan->db->table('penjualan_unit')
            ->select('COALESCE(SUM(penjualan_unit.laba),0) AS laba')
            ->join('penjualan', 'penjualan.id = penjualan_unit.penjualan_id')
            ->where('penjualan.tanggal >=', $awalBulan)
            ->get()->getRowArray();

        // ---- Nilai stok + alert stok minimum ----
        $stok = $unit->db->table('unit')
            ->select('status, COUNT(*) AS jml, COALESCE(SUM(harga_beli),0) AS nilai')
            ->groupBy('status')->get()->getResultArray();

        $stokMap = [];
        foreach ($stok as $s) {
            $stokMap[$s['status']] = $s;
        }

        $stokAlert = (new BarangModel())->db->table('barang')
            ->select('barang.kode, barang.nama, barang.stok_min,
                      COALESCE(SUM(CASE WHEN unit.status = "tersedia" THEN 1 ELSE 0 END), 0) AS tersedia')
            ->join('unit', 'unit.barang_id = barang.id', 'left')
            ->groupBy('barang.id')
            ->having('tersedia <= barang.stok_min', null, false)
            ->orderBy('barang.nama')
            ->get()->getResultArray();

        // ---- Utang ----
        $utangRingkas = $utang->db->table('utang')
            ->select('COUNT(*) AS jml, COALESCE(SUM(nominal-terbayar),0) AS sisa')
            ->where('status', 'belum')->get()->getRowArray();

        $aging = $utang->aging();

        // ---- Tren 14 hari (omzet & laba) ----
        $trenPenjualan = $penjualan->db->table('penjualan')
            ->select("tanggal, SUM(total) AS omzet")
            ->where('tanggal >=', date('Y-m-d', strtotime('-13 days')))
            ->groupBy('tanggal')->get()->getResultArray();

        $trenLaba = $penjualan->db->table('penjualan_unit')
            ->select('penjualan.tanggal, SUM(penjualan_unit.laba) AS laba')
            ->join('penjualan', 'penjualan.id = penjualan_unit.penjualan_id')
            ->where('penjualan.tanggal >=', date('Y-m-d', strtotime('-13 days')))
            ->groupBy('penjualan.tanggal')->get()->getResultArray();

        $tren = [];
        for ($i = 13; $i >= 0; $i--) {
            $t = date('Y-m-d', strtotime("-{$i} days"));
            $tren[$t] = ['omzet' => 0.0, 'laba' => 0.0];
        }
        foreach ($trenPenjualan as $r) {
            $tren[$r['tanggal']]['omzet'] = (float) $r['omzet'];
        }
        foreach ($trenLaba as $r) {
            $tren[$r['tanggal']]['laba'] = (float) $r['laba'];
        }

        // ---- Transaksi terbaru ----
        $terakhirPenjualan = $penjualan->db->table('penjualan')
            ->select('penjualan.*, users.nama AS user_nama')
            ->join('users', 'users.id = penjualan.user_id', 'left')
            ->orderBy('penjualan.id', 'DESC')->limit(5)->get()->getResultArray();

        $terakhirUtang = $utang->db->table('utang')
            ->select('utang.*, supplier.nama AS supplier_nama,
                      DATEDIFF(CURDATE(), utang.jatuh_tempo) AS hari_telat')
            ->join('supplier', 'supplier.id = utang.supplier_id', 'left')
            ->where('utang.status', 'belum')
            ->orderBy('utang.jatuh_tempo', 'ASC')->limit(5)->get()->getResultArray();

        return view('dashboard/index', [
            'title'          => 'Dashboard',
            'bulanIni'       => $bulanIni,
            'labaBulanIni'   => $labaBulanIni['laba'],
            'stokMap'        => $stokMap,
            'stokAlert'      => $stokAlert,
            'utangRingkas'   => $utangRingkas,
            'aging'          => $aging,
            'tren'           => $tren,
            'terakhirPenjualan' => $terakhirPenjualan,
            'terakhirUtang'     => $terakhirUtang,
        ]);
    }

    /** JSON untuk chart (dipanggil AJAX). */
    public function chartData()
    {
        $penjualan = new PenjualanModel();

        $rows = $penjualan->db->table('penjualan')
            ->select('tanggal, SUM(total) AS omzet')
            ->where('tanggal >=', date('Y-m-d', strtotime('-29 days')))
            ->groupBy('tanggal')->get()->getResultArray();

        return $this->response->setJSON($rows);
    }
}
