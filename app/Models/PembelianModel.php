<?php

namespace App\Models;

use CodeIgniter\Model;

class PembelianModel extends Model
{
    protected $table         = 'pembelian';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'no', 'supplier_id', 'user_id', 'tanggal', 'sumber', 'url',
        'total', 'uang_muka', 'sisa', 'metode', 'jatuh_tempo', 'catatan', 'created_at',
    ];

    protected $useTimestamps = false;

    public const SUMBER = ['Tokopedia', 'Shopee', 'Lazada', 'Offline', 'Lainnya'];
    public const METODE = ['tunai', 'transfer', 'kartu', 'termin'];

    public function listing(?string $cari = null): array
    {
        $b = $this->db->table('pembelian')
            ->select('pembelian.*, supplier.nama AS supplier_nama, users.nama AS user_nama,
                      COUNT(pembelian_unit.id) AS jumlah_unit')
            ->join('supplier', 'supplier.id = pembelian.supplier_id', 'left')
            ->join('users', 'users.id = pembelian.user_id', 'left')
            ->join('pembelian_unit', 'pembelian_unit.pembelian_id = pembelian.id', 'left')
            ->groupBy('pembelian.id')
            ->orderBy('pembelian.tanggal', 'DESC')
            ->orderBy('pembelian.id', 'DESC');

        if ($cari !== null && $cari !== '') {
            $b->groupStart()
                ->like('pembelian.no', $cari)
                ->orLike('supplier.nama', $cari)
                ->groupEnd();
        }

        return $b->get()->getResultArray();
    }

    public function detail(int $id): ?array
    {
        $trx = $this->db->table('pembelian')
            ->select('pembelian.*, supplier.nama AS supplier_nama, supplier.kode AS supplier_kode, users.nama AS user_nama')
            ->join('supplier', 'supplier.id = pembelian.supplier_id', 'left')
            ->join('users', 'users.id = pembelian.user_id', 'left')
            ->where('pembelian.id', $id)
            ->get()
            ->getRowArray();

        if ($trx === null) {
            return null;
        }

        $trx['items'] = $this->db->table('pembelian_unit')
            ->select('pembelian_unit.*, unit.kode AS unit_kode, unit.imei, unit.kondisi, barang.nama AS barang_nama, barang.kode AS barang_kode')
            ->join('unit', 'unit.id = pembelian_unit.unit_id', 'left')
            ->join('barang', 'barang.id = unit.barang_id', 'left')
            ->where('pembelian_unit.pembelian_id', $id)
            ->get()
            ->getResultArray();

        $trx['utang'] = $this->db->table('utang')
            ->where('pembelian_id', $id)
            ->get()
            ->getRowArray();

        return $trx;
    }

    public function kodeBerikutnya(): string
    {
        $prefix = 'PBL-' . date('Ymd') . '-';
        $last = $this->db->table('pembelian')
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
