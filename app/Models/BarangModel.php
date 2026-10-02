<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table            = 'barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'stok',
        'satuan',
        'harga_beli',
        'harga_jual',
        'link_pembelian',
        'sumber_toko',
        'minus_kondisi',
        'catatan',
    ];
    protected $useTimestamps    = true;

    public function getWithKategori(?int $id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('barang.*, kategori.nama AS kategori_nama')
            ->join('kategori', 'kategori.id = barang.kategori_id', 'left');

        if ($id !== null) {
            return $builder->where('barang.id', $id)->get()->getRowArray();
        }

        return $builder->orderBy('barang.id', 'DESC')->get()->getResultArray();
    }

    public function kodeBerikutnya(): string
    {
        $last = $this->db->table($this->table)
            ->select('kode_barang')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (! $last) {
            return 'BRG-001';
        }

        if (preg_match('/BRG-(\d+)/', $last['kode_barang'], $m)) {
            $num = (int) $m[1] + 1;
            return sprintf('BRG-%03d', $num);
        }

        return 'BRG-' . str_pad((string) (time() % 1000), 3, '0', STR_PAD_LEFT);
    }
}
