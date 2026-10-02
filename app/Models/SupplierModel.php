<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table         = 'supplier';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kode', 'nama', 'alamat', 'telepon', 'catatan'];

    /** Supplier beserta total utang yang masih outstanding. */
    public function withUtang(): array
    {
        return $this->select('supplier.*, COALESCE(SUM(utang.nominal - utang.terbayar), 0) AS utang_tersisa, COUNT(DISTINCT utang.id) AS jumlah_utang')
            ->join('utang', 'utang.supplier_id = supplier.id', 'left')
            ->groupBy('supplier.id')
            ->orderBy('nama', 'ASC')
            ->findAll();
    }
}
