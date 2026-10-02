<?php

namespace App\Controllers;

use App\Models\UtangModel;

class UtangController extends BaseController
{
    public function index()
    {
        $utang  = new UtangModel();
        $status = (string) $this->request->getGet('status');

        return view('utang/index', [
            'title'  => 'Daftar Utang Usaha',
            'utang'  => $utang->listing($status ?: null),
            'status' => $status,
            'aging'  => $utang->aging(),
        ]);
    }

    public function detail(int $id)
    {
        $utang = new UtangModel();
        $row   = $utang->detail($id);

        if ($row === null) {
            return redirect()->to('/utang')->with('error', 'Data utang tidak ditemukan.');
        }

        return view('utang/detail', [
            'title' => 'Detail Utang · ' . $row['kode'],
            'utang' => $row,
        ]);
    }

    public function bayar(int $id)
    {
        $db    = \Config\Database::connect();
        $utang = new UtangModel();

        $row = $utang->find($id);
        if ($row === null) {
            return redirect()->to('/utang')->with('error', 'Data utang tidak ditemukan.');
        }

        $nominal = (float) str_replace(['.', ','], ['', '.'], (string) $this->request->getPost('nominal'));
        $metode  = (string) $this->request->getPost('metode') ?: 'transfer';
        $tanggal = (string) $this->request->getPost('tanggal') ?: date('Y-m-d');
        $catatan = trim((string) $this->request->getPost('catatan')) ?: null;

        $sisa = (float) $row['nominal'] - (float) $row['terbayar'];
        if ($nominal <= 0 || $nominal > $sisa) {
            return redirect()->back()->with('error', "Nominal bayar harus antara 1 s/d " . rupiah($sisa));
        }

        $bukti = $this->request->getFile('bukti');
        $namaBukti = null;
        if ($bukti && $bukti->isValid() && ! $bukti->hasMoved()) {
            $namaBukti = $bukti->getRandomName();
            $bukti->move(ROOTPATH . 'public/uploads/bukti', $namaBukti);
        }

        $db->transStart();

        $db->table('pembayaran')->insert([
            'utang_id'   => $id,
            'tanggal'    => $tanggal,
            'nominal'    => $nominal,
            'metode'     => $metode,
            'bukti'      => $namaBukti,
            'catatan'    => $catatan,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $terbayarBaru = (float) $row['terbayar'] + $nominal;
        $lunas        = $terbayarBaru >= (float) $row['nominal'];

        $utang->update($id, [
            'terbayar' => $terbayarBaru,
            'status'   => $lunas ? 'lunas' : 'belum',
        ]);

        // Ripple ke pembelian terkait
        if (! empty($row['pembelian_id'])) {
            $db->table('pembelian')->update([
                'uang_muka' => $terbayarBaru,
                'sisa'      => max(0.0, (float) $row['nominal'] - $terbayarBaru),
            ], ['id' => $row['pembelian_id']]);
        }

        $db->transComplete();

        return redirect()->to('/utang/detail/' . $id)->with('sukses', 'Pembayaran berhasil dicatat.');
    }
}
