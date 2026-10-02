<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table         = 'penjualan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['no', 'user_id', 'tanggal', 'total', 'dibayar', 'kembalian', 'metode', 'catatan', 'created_at'];

    protected $useTimestamps = false;

    public const METODE = ['cash', 'transfer', 'qris'];

    public function listing(?string $cari = null): array
    {
        $b = $this->db->table('penjualan')
            ->select('penjualan.*, users.nama AS user_nama, COUNT(penjualan_unit.id) AS jumlah_unit,
                      COALESCE(SUM(penjualan_unit.laba), 0) AS total_laba')
            ->join('users', 'users.id = penjualan.user_id', 'left')
            ->join('penjualan_unit', 'penjualan_unit.penjualan_id = penjualan.id', 'left')
            ->groupBy('penjualan.id')
            ->orderBy('penjualan.tanggal', 'DESC')
            ->orderBy('penjualan.id', 'DESC');

        if ($cari !== null && $cari !== '') {
            $b->groupStart()
                ->like('penjualan.no', $cari)
                ->orLike('penjualan.catatan', $cari)
                ->groupEnd();
        }

        return $b->get()->getResultArray();
    }

    public function detail(int $id): ?array
    {
        $trx = $this->db->table('penjualan')
            ->select('penjualan.*, users.nama AS user_nama')
            ->join('users', 'users.id = penjualan.user_id', 'left')
            ->where('penjualan.id', $id)
            ->get()
            ->getRowArray();

        if ($trx === null) {
            return null;
        }

        $trx['items'] = $this->db->table('penjualan_unit')
            ->select('penjualan_unit.*, unit.kode AS unit_kode, unit.imei, unit.kondisi,
                      barang.nama AS barang_nama, barang.kode AS barang_kode, kategori.nama AS kategori_nama')
            ->join('unit', 'unit.id = penjualan_unit.unit_id', 'left')
            ->join('barang', 'barang.id = penjualan_unit.barang_id', 'left')
            ->join('kategori', 'kategori.id = barang.kategori_id', 'left')
            ->where('penjualan_unit.penjualan_id', $id)
            ->get()
            ->getResultArray();

        return $trx;
    }

    public function kodeBerikutnya(): string
    {
        $prefix = 'PJL-' . date('Ymd') . '-';
        $last = $this->db->table('penjualan')
            ->select('no')
            ->like('no', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $next = 1;
        if ($last && preg_match('/-(\d+)$/', $last['no'], $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
