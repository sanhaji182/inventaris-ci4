<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanModel;
use App\Models\UnitModel;

class PenjualanController extends BaseController
{
    public function index()
    {
        $penjualan = new PenjualanModel();
        $cari      = (string) $this->request->getGet('cari');

        return view('penjualan/index', [
            'title'     => 'Riwayat Penjualan',
            'penjualan' => $penjualan->listing($cari ?: null),
            'cari'      => $cari,
        ]);
    }

    public function form()
    {
        $penjualan = new PenjualanModel();
        $barang    = new BarangModel();

        // Hanya barang yang punya stok tersedia
        $barangList = $barang->db->table('barang')
            ->select('barang.*, COUNT(unit.id) AS stok_tersedia')
            ->join('unit', 'unit.barang_id = barang.id AND unit.status = "tersedia"', 'inner')
            ->groupBy('barang.id')
            ->orderBy('barang.nama')
            ->get()->getResultArray();

        return view('penjualan/form', [
            'title'      => 'Kasir · Penjualan Baru',
            'nextNo'     => $penjualan->kodeBerikutnya(),
            'barangList' => $barangList,
            'metode'     => PenjualanModel::METODE,
        ]);
    }

    public function detail(int $id)
    {
        $penjualan = new PenjualanModel();
        $trx       = $penjualan->detail($id);

        if ($trx === null) {
            return redirect()->to('/penjualan')->with('error', 'Transaksi penjualan tidak ditemukan.');
        }

        return view('penjualan/detail', [
            'title' => 'Detail Penjualan · ' . $trx['no'],
            'trx'   => $trx,
        ]);
    }

    public function save()
    {
        $db        = \Config\Database::connect();
        $penjualan = new PenjualanModel();
        $unit      = new UnitModel();

        $no        = trim((string) $this->request->getPost('no')) ?: $penjualan->kodeBerikutnya();
        $tanggal   = (string) $this->request->getPost('tanggal') ?: date('Y-m-d');
        $metode    = (string) $this->request->getPost('metode') ?: 'cash';
        $dibayar   = (float) str_replace(['.', ','], ['', '.'], (string) $this->request->getPost('dibayar'));
        $catatan   = trim((string) $this->request->getPost('catatan')) ?: null;

        // Array unit_id[] yang dipilih + custom harga_jual[] per unit
        $unitIds    = (array) $this->request->getPost('unit_id');
        $hargaJuals = (array) $this->request->getPost('harga_jual');

        if (empty($unitIds)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal 1 unit untuk dijual.');
        }

        $db->transStart();

        // Kumpulkan data unit terpilih & validasi statusnya masih 'tersedia'
        $items = [];
        $total = 0.0;
        foreach ($unitIds as $idx => $uId) {
            $uId = (int) $uId;
            if (! $uId) continue;

            $uRow = $unit->where('id', $uId)->where('status', 'tersedia')->first();
            if ($uRow === null) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', "Unit ID {$uId} sudah tidak tersedia.");
            }

            $hJual = (float) str_replace(['.', ','], ['', '.'], (string) ($hargaJuals[$idx] ?? 0));
            $hBeli = (float) $uRow['harga_beli'];
            $laba  = $hJual - $hBeli;
            $persen = $hJual > 0 ? ($laba / $hJual) * 100 : 0.0;

            $items[] = [
                'unit_id'     => $uId,
                'barang_id'   => (int) $uRow['barang_id'],
                'harga_jual'  => $hJual,
                'harga_beli'  => $hBeli,
                'laba'        => $laba,
                'laba_persen' => $persen,
            ];
            $total += $hJual;
        }

        if (empty($items)) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Tidak ada unit valid untuk dijual.');
        }

        if ($dibayar < $total && $metode === 'cash') {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Pembayaran tunai kurang dari total belanja.');
        }

        $kembalian = max(0.0, $dibayar - $total);

        $pId = $penjualan->insert([
            'no'         => $no,
            'user_id'    => (int) session('user_id'),
            'tanggal'    => $tanggal,
            'total'      => $total,
            'dibayar'    => $dibayar,
            'kembalian'  => $kembalian,
            'metode'     => $metode,
            'catatan'    => $catatan,
            'created_at' => date('Y-m-d H:i:s'),
        ], true);

        foreach ($items as $it) {
            $db->table('penjualan_unit')->insert([
                'penjualan_id' => $pId,
                'unit_id'      => $it['unit_id'],
                'barang_id'    => $it['barang_id'],
                'harga_jual'   => $it['harga_jual'],
                'harga_beli'   => $it['harga_beli'],
                'laba'         => $it['laba'],
                'laba_persen'  => $it['laba_persen'],
            ]);

            // Tandai unit jadi terjual
            $unit->update($it['unit_id'], [
                'status'  => 'terjual',
                'catatan' => "Terjual lewat {$no}",
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses transaksi penjualan.');
        }

        return redirect()->to('/penjualan/detail/' . $pId)->with('sukses', "Penjualan {$no} berhasil.");
    }

    public function batal(int $id)
    {
        $db        = \Config\Database::connect();
        $penjualan = new PenjualanModel();

        $trx = $penjualan->detail($id);
        if ($trx === null) {
            return redirect()->to('/penjualan')->with('error', 'Penjualan tidak ditemukan.');
        }

        $db->transStart();

        // Kembalikan status unit jadi 'tersedia'
        foreach ($trx['items'] as $it) {
            $db->table('unit')->update([
                'status'  => 'tersedia',
                'catatan' => 'Batal penjualan ' . $trx['no'],
            ], ['id' => $it['unit_id']]);
        }

        $db->table('penjualan_unit')->delete(['penjualan_id' => $id]);
        $penjualan->delete($id);

        $db->transComplete();

        return redirect()->to('/penjualan')->with('sukses', "Transaksi {$trx['no']} berhasil dibatalkan dan unit dikembalikan ke stok.");
    }
}
