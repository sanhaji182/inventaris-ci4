<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table         = 'kategori';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kode', 'nama', 'keterangan'];

    public function withJumlahBarang(): array
    {
        return $this->select('kategori.*, COUNT(barang.id) AS jumlah_barang')
            ->join('barang', 'barang.kategori_id = kategori.id', 'left')
            ->groupBy('kategori.id')
            ->orderBy('kategori.nama', 'ASC')
            ->findAll();
    }
}
