<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table         = 'barang';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kode', 'nama', 'kategori_id', 'merek', 'spek', 'harga_jual', 'stok_min', 'foto', 'created_at'];

    protected $useTimestamps = false;

    /**
     * Katalog barang + agregat stok fisik dari tabel `unit`.
     *
     * Stok sengaja TIDAK disimpan di tabel `barang`: satu unit = satu device fisik
     * (ditelusuri lewat IMEI / kondisi), jadi sumber kebenaran jumlah unit adalah
     * `COUNT(unit.id)` yang dikelompokkan per barang.
     */
    public function withStok(?string $cari = null, ?int $kategoriId = null): array
    {
        $b = $this->db->table('barang')
            ->select('barang.*, kategori.nama AS kategori_nama, kategori.kode AS kategori_kode,
                      COALESCE(SUM(CASE WHEN unit.status = "tersedia" THEN 1 ELSE 0 END), 0) AS stok_tersedia,
                      COALESCE(SUM(CASE WHEN unit.status = "terjual"   THEN 1 ELSE 0 END), 0) AS stok_terjual,
                      COUNT(unit.id) AS total_unit,
                      COALESCE(SUM(CASE WHEN unit.status <> "rusak" THEN unit.harga_beli ELSE 0 END), 0) AS nilai_stok')
            ->join('kategori', 'kategori.id = barang.kategori_id', 'left')
            ->join('unit', 'unit.barang_id = barang.id', 'left')
            ->groupBy('barang.id')
            ->orderBy('barang.nama', 'ASC');

        if ($cari !== null && $cari !== '') {
            $b->groupStart()
                ->like('barang.kode', $cari)
                ->orLike('barang.nama', $cari)
                ->orLike('barang.merek', $cari)
                ->groupEnd();
        }

        if ($kategoriId) {
            $b->where('barang.kategori_id', $kategoriId);
        }

        return $b->get()->getResultArray();
    }

    public function findWithDetail(int $id): ?array
    {
        return $this->db->table('barang')
            ->select('barang.*, kategori.nama AS kategori_nama')
            ->join('kategori', 'kategori.id = barang.kategori_id', 'left')
            ->where('barang.id', $id)
            ->get()
            ->getRowArray();
    }

    /** Kode berikutnya: B0001, B0002, ... */
    public function kodeBerikutnya(): string
    {
        $last = $this->db->table('barang')
            ->select('kode')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $next = 1;
        if ($last && preg_match('/^B(\d+)$/', $last['kode'], $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return 'B' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
