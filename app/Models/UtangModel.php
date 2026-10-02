<?php

namespace App\Models;

use CodeIgniter\Model;

class UtangModel extends Model
{
    protected $table         = 'utang';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kode', 'pembelian_id', 'supplier_id', 'tanggal', 'jatuh_tempo', 'nominal', 'terbayar', 'status', 'catatan', 'created_at'];

    protected $useTimestamps = false;

    public const STATUS = ['belum', 'lunas'];

    /** Utang + sisa + keterlambatan. */
    public function listing(?string $status = null): array
    {
        $b = $this->db->table('utang')
            ->select('utang.*, supplier.nama AS supplier_nama, pembelian.no AS pembelian_no,
                      (utang.nominal - utang.terbayar) AS sisa,
                      DATEDIFF(CURDATE(), utang.jatuh_tempo) AS hari_telat')
            ->join('supplier', 'supplier.id = utang.supplier_id', 'left')
            ->join('pembelian', 'pembelian.id = utang.pembelian_id', 'left')
            ->orderBy('utang.jatuh_tempo', 'ASC')
            ->orderBy('utang.id', 'ASC');

        if ($status === 'belum') {
            $b->where('utang.status', 'belum')
                ->where('utang.nominal > utang.terbayar');
        } elseif ($status === 'lunas') {
            $b->where('utang.status', 'lunas');
        }

        return $b->get()->getResultArray();
    }

    /** Ambang batas kategori umur utang (hari) → label bucket. */
    public const BUCKET = [
        ['label' => 'Belum jatuh tempo', 'min' => -99999, 'max' => 0,  'warna' => 'hijau'],
        ['label' => '1-30 hari',         'min' => 1,    'max' => 30, 'warna' => 'kuning'],
        ['label' => '31-60 hari',        'min' => 31,   'max' => 60, 'warna' => 'jingga'],
        ['label' => '> 60 hari',         'min' => 61,   'max' => 99999, 'warna' => 'merah'],
    ];

    /** Buckets aging untuk tabel + grafik dashboard. */
    public function aging(): array
    {
        $rows = $this->db->table('utang')
            ->select('utang.id, utang.nominal, utang.terbayar, utang.jatuh_tempo,
                      COALESCE(utang.nominal - utang.terbayar, 0) AS sisa,
                      DATEDIFF(CURDATE(), utang.jatuh_tempo) AS hari_telat,
                      supplier.nama AS supplier_nama')
            ->join('supplier', 'supplier.id = utang.supplier_id', 'left')
            ->where('utang.status', 'belum')
            ->where('utang.nominal > utang.terbayar')
            ->get()
            ->getResultArray();

        $out = [];
        foreach (self::BUCKET as $b) {
            $out[] = $b + ['total' => 0.0, 'jumlah' => 0, 'rows' => []];
        }

        foreach ($rows as $r) {
            foreach ($out as $i => $b) {
                if ($r['hari_telat'] >= $b['min'] && $r['hari_telat'] <= $b['max']) {
                    $out[$i]['total'] += (float) $r['sisa'];
                    $out[$i]['jumlah']++;
                    $out[$i]['rows'][] = $r;
                    break;
                }
            }
        }

        return $out;
    }

    public function detail(int $id): ?array
    {
        $utang = $this->db->table('utang')
            ->select('utang.*, supplier.nama AS supplier_nama, pembelian.no AS pembelian_no,
                      (utang.nominal - utang.terbayar) AS sisa,
                      DATEDIFF(CURDATE(), utang.jatuh_tempo) AS hari_telat')
            ->join('supplier', 'supplier.id = utang.supplier_id', 'left')
            ->join('pembelian', 'pembelian.id = utang.pembelian_id', 'left')
            ->where('utang.id', $id)
            ->get()
            ->getRowArray();

        if ($utang === null) {
            return null;
        }

        $utang['pembayaran'] = $this->db->table('pembayaran')
            ->where('utang_id', $id)
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();

        return $utang;
    }

    public function kodeBerikutnya(): string
    {
        $prefix = 'UTG-' . date('Ymd') . '-';
        $last = $this->db->table('utang')
            ->select('kode')
            ->like('kode', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $next = 1;
        if ($last && preg_match('/-(\d+)$/', $last['kode'], $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
