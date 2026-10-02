<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\PembelianModel;
use App\Models\SupplierModel;
use App\Models\UnitModel;
use App\Models\UtangModel;

class PembelianController extends BaseController
{
    public function index()
    {
        $pembelian = new PembelianModel();
        $cari      = (string) $this->request->getGet('cari');

        return view('pembelian/index', [
            'title'     => 'Riwayat Pembelian',
            'pembelian' => $pembelian->listing($cari ?: null),
            'cari'      => $cari,
        ]);
    }

    public function form(int $id = 0)
    {
        $pembelian = new PembelianModel();
        $supplier  = new SupplierModel();
        $barang    = new BarangModel();

        $row = $id > 0 ? $pembelian->detail($id) : null;

        return view('pembelian/form', [
            'title'     => $id > 0 ? 'Edit Pembelian' : 'Input Pembelian Baru',
            'pembelian' => $row,
            'supplier'  => $supplier->findAll(),
            'barang'    => $barang->findAll(),
            'nextNo'    => $row ? $row['no'] : $pembelian->kodeBerikutnya(),
            'sumber'    => PembelianModel::SUMBER,
            'metode'    => PembelianModel::METODE,
        ]);
    }

    public function detail(int $id)
    {
        $pembelian = new PembelianModel();
        $trx       = $pembelian->detail($id);

        if ($trx === null) {
            return redirect()->to('/pembelian')->with('error', 'Transaksi pembelian tidak ditemukan.');
        }

        return view('pembelian/detail', [
            'title' => 'Detail Pembelian · ' . $trx['no'],
            'trx'   => $trx,
        ]);
    }

    public function save()
    {
        $db        = \Config\Database::connect();
        $pembelian = new PembelianModel();
        $unit      = new UnitModel();
        $utang     = new UtangModel();

        $id          = (int) $this->request->getPost('id');
        $no          = trim((string) $this->request->getPost('no')) ?: $pembelian->kodeBerikutnya();
        $supplierId  = (int) $this->request->getPost('supplier_id') ?: null;
        $tanggal     = (string) $this->request->getPost('tanggal') ?: date('Y-m-d');
        $sumber      = (string) $this->request->getPost('sumber') ?: 'Offline';
        $url         = trim((string) $this->request->getPost('url')) ?: null;
        $metode      = (string) $this->request->getPost('metode') ?: 'tunai';
        $jatuhTempo  = $metode === 'termin' ? ((string) $this->request->getPost('jatuh_tempo') ?: null) : null;
        $uangMuka    = (float) str_replace(['.', ','], ['', '.'], (string) $this->request->getPost('uang_muka'));
        $catatan     = trim((string) $this->request->getPost('catatan')) ?: null;

        // Items array: barang_id[], imei[], kondisi[], harga_beli[]
        $itemsBarang   = (array) $this->request->getPost('item_barang_id');
        $itemsImei     = (array) $this->request->getPost('item_imei');
        $itemsKondisi  = (array) $this->request->getPost('item_kondisi');
        $itemsHarga    = (array) $this->request->getPost('item_harga_beli');

        if (empty($itemsBarang)) {
            return redirect()->back()->withInput()->with('error', 'Minimal 1 unit barang harus diinput.');
        }

        $total = 0.0;
        $cleanItems = [];
        foreach ($itemsBarang as $i => $bId) {
            $bId = (int) $bId;
            if (! $bId) continue;
            $hBeli = (float) str_replace(['.', ','], ['', '.'], (string) ($itemsHarga[$i] ?? 0));
            $cleanItems[] = [
                'barang_id'  => $bId,
                'imei'       => trim((string) ($itemsImei[$i] ?? '')) ?: null,
                'kondisi'    => (string) ($itemsKondisi[$i] ?? 'mulus'),
                'harga_beli' => $hBeli,
            ];
            $total += $hBeli;
        }

        if (empty($cleanItems)) {
            return redirect()->back()->withInput()->with('error', 'Data unit tidak valid.');
        }

        if ($metode !== 'termin') {
            $uangMuka = $total;
            $sisa     = 0.0;
        } else {
            $sisa = max(0.0, $total - $uangMuka);
        }

        $db->transStart();

        $pembelianData = [
            'no'          => $no,
            'supplier_id' => $supplierId,
            'user_id'     => (int) session('user_id'),
            'tanggal'     => $tanggal,
            'sumber'      => $sumber,
            'url'         => $url,
            'total'       => $total,
            'uang_muka'   => $uangMuka,
            'sisa'        => $sisa,
            'metode'      => $metode,
            'jatuh_tempo' => $jatuhTempo,
            'catatan'     => $catatan,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $pId = $pembelian->insert($pembelianData, true);

        // Generate unit fisik per baris item
        foreach ($cleanItems as $it) {
            $uKode = $unit->kodeBerikutnya();
            $uId   = $unit->insert([
                'kode'          => $uKode,
                'barang_id'     => $it['barang_id'],
                'imei'          => $it['imei'],
                'kondisi'       => $it['kondisi'],
                'harga_beli'    => $it['harga_beli'],
                'supplier_id'   => $supplierId,
                'pembelian_id'  => $pId,
                'tanggal_masuk' => $tanggal,
                'status'        => 'tersedia',
                'lokasi'        => 'Gudang Utama',
                'catatan'       => "Masuk lewat {$no}",
                'created_at'    => date('Y-m-d H:i:s'),
            ], true);

            $db->table('pembelian_unit')->insert([
                'pembelian_id' => $pId,
                'unit_id'      => $uId,
                'harga_beli'   => $it['harga_beli'],
            ]);
        }

        // Catat utang otomatis bila metode termin & ada sisa
        if ($metode === 'termin' && $sisa > 0) {
            $uKode = $utang->kodeBerikutnya();
            $utangId = $utang->insert([
                'kode'         => $uKode,
                'pembelian_id' => $pId,
                'supplier_id'  => $supplierId,
                'tanggal'      => $tanggal,
                'jatuh_tempo'  => $jatuhTempo,
                'nominal'      => $total,
                'terbayar'     => $uangMuka,
                'status'       => $uangMuka >= $total ? 'lunas' : 'belum',
                'catatan'      => "Utang dari pembelian {$no}",
                'created_at'   => date('Y-m-d H:i:s'),
            ], true);

            // Bila ada DP, catat pembayaran pertama
            if ($uangMuka > 0) {
                $db->table('pembayaran')->insert([
                    'utang_id'   => $utangId,
                    'tanggal'    => $tanggal,
                    'nominal'    => $uangMuka,
                    'metode'     => 'transfer',
                    'bukti'      => null,
                    'catatan'    => 'Uang muka saat pembelian',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi pembelian.');
        }

        return redirect()->to('/pembelian')->with('sukses', "Pembelian {$no} berhasil disimpan.");
    }

    public function delete(int $id)
    {
        $db        = \Config\Database::connect();
        $pembelian = new PembelianModel();

        // Validasi: tidak boleh hapus bila ada unit dari pembelian ini yang sudah TERJUAL
        $terjual = $db->table('pembelian_unit')
            ->join('unit', 'unit.id = pembelian_unit.unit_id')
            ->where('pembelian_unit.pembelian_id', $id)
            ->where('unit.status', 'terjual')
            ->countAllResults();

        if ($terjual > 0) {
            return redirect()->to('/pembelian')
                ->with('error', "Pembelian tidak bisa dibatalkan: {$terjual} unit sudah terjual.");
        }

        $db->transStart();
        // Hapus unit fisik yang belum terjual
        $units = $db->table('pembelian_unit')->where('pembelian_id', $id)->get()->getResultArray();
        foreach ($units as $u) {
            $db->table('unit')->delete(['id' => $u['unit_id']]);
        }
        $db->table('pembelian_unit')->delete(['pembelian_id' => $id]);

        // Hapus pembayaran & utang terkait bila ada
        $utang = $db->table('utang')->where('pembelian_id', $id)->get()->getRowArray();
        if ($utang) {
            $db->table('pembayaran')->delete(['utang_id' => $utang['id']]);
            $db->table('utang')->delete(['id' => $utang['id']]);
        }

        $pembelian->delete($id);
        $db->transComplete();

        return redirect()->to('/pembelian')->with('sukses', 'Transaksi pembelian berhasil dibatalkan.');
    }
}
