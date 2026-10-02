<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitModel extends Model
{
    protected $table         = 'unit';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'kode', 'barang_id', 'imei', 'kondisi', 'harga_beli', 'supplier_id',
        'pembelian_id', 'tanggal_masuk', 'status', 'lokasi', 'catatan', 'created_at',
    ];

    protected $useTimestamps = false;

    public const STATUS = ['tersedia', 'terjual', 'rusak', 'hilang'];

    /**
     * Daftar unit + data barang & supplier.
     *
     * @param array<string,mixed> $filter
     */
    public function listing(array $filter = []): array
    {
        $b = $this->db->table('unit')
            ->select('unit.*, barang.nama AS barang_nama, barang.kode AS barang_kode,
                      kategori.nama AS kategori_nama, supplier.nama AS supplier_nama,
                      penjualan.no AS penjualan_no')
            ->join('barang', 'barang.id = unit.barang_id', 'left')
            ->join('kategori', 'kategori.id = barang.kategori_id', 'left')
            ->join('supplier', 'supplier.id = unit.supplier_id', 'left')
            ->join('penjualan_unit', 'penjualan_unit.unit_id = unit.id', 'left')
            ->join('penjualan', 'penjualan.id = penjualan_unit.penjualan_id', 'left');

        if (! empty($filter['status'])) {
            $b->where('unit.status', $filter['status']);
        }

        if (! empty($filter['barang_id'])) {
            $b->where('unit.barang_id', (int) $filter['barang_id']);
        }

        if (! empty($filter['kategori_id'])) {
            $b->where('barang.kategori_id', (int) $filter['kategori_id']);
        }

        if (! empty($filter['cari'])) {
            $cari = $filter['cari'];
            $b->groupStart()
                ->like('unit.kode', $cari)
                ->orLike('unit.imei', $cari)
                ->orLike('barang.nama', $cari)
                ->groupEnd();
        }

        return $b->orderBy('unit.tanggal_masuk', 'DESC')
            ->orderBy('unit.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /** Unit yang masih bisa dijual (status tersedia). */
    public function unitTersedia(int $barangId): array
    {
        return $this->db->table('unit')
            ->select('unit.id, unit.kode, unit.imei, unit.kondisi, unit.harga_beli, unit.lokasi')
            ->where('unit.barang_id', $barangId)
            ->where('unit.status', 'tersedia')
            ->orderBy('unit.harga_beli', 'ASC')
            ->get()
            ->getResultArray();
    }

    /** Kode berikutnya: U0001, U0002, ... */
    public function kodeBerikutnya(): string
    {
        $last = $this->db->table('unit')
            ->select('kode')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $next = 1;
        if ($last && preg_match('/^U(\d+)$/', $last['kode'], $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return 'U' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
