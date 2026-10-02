<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatStokModel extends Model
{
    protected $table            = 'riwayat_stok';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'barang_id',
        'user_id',
        'jenis',
        'jumlah',
        'harga_transaksi',
        'total_laba',
        'keterangan',
        'tanggal',
        'created_at',
    ];
    protected $useTimestamps    = false;

    public function getRiwayatLengkap(int $limit = 50)
    {
        return $this->db->table($this->table)
            ->select('riwayat_stok.*, barang.nama_barang, barang.kode_barang, barang.harga_beli, barang.harga_jual, users.nama AS user_nama')
            ->join('barang', 'barang.id = riwayat_stok.barang_id', 'left')
            ->join('users', 'users.id = riwayat_stok.user_id', 'left')
            ->orderBy('riwayat_stok.id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
