<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventarisTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'auto_increment' => true],
            'username'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'nama'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'role'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'staf'],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'aktif'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('users', true);

        // ---------- Master data ----------
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'auto_increment' => true],
            'kode'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'keterangan' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('kategori', true);

        $this->forge->addField([
            'id'      => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'auto_increment' => true],
            'kode'    => ['type' => 'VARCHAR', 'constraint' => 20],
            'nama'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'alamat'  => ['type' => 'TEXT', 'null' => true],
            'telepon' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'catatan' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('supplier', true);

        // barang = katalog/tipe. Stok TIDAK disimpan di sini (lihat tabel unit).
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'auto_increment' => true],
            'kode'        => ['type' => 'VARCHAR', 'constraint' => 20],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'kategori_id' => ['type' => 'INT', 'constraint' => true, 'unsigned' => true],
            'merek'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'spek'        => ['type' => 'TEXT', 'null' => true],
            'harga_jual'  => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'stok_min'    => ['type' => 'INT', 'constraint' => true, 'default' => 0],
            'foto'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->addKey('kategori_id');
        $this->forge->createTable('barang', true);

        // unit = device fisik yang dilacak (barang second)
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'auto_increment' => true],
            'kode'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'barang_id'     => ['type' => 'INT', 'constraint' => true, 'unsigned' => true],
            'imei'          => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'kondisi'       => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'mulus'],
            'harga_beli'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'supplier_id'   => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'null' => true],
            'pembelian_id'  => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'null' => true],
            'tanggal_masuk' => ['type' => 'DATE', 'null' => true],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 15, 'default' => 'tersedia'],
            'lokasi'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'catatan'       => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->addKey('barang_id');
        $this->forge->addKey('status');
        $this->forge->createTable('unit', true);

        // ---------- Pembelian ----------
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'auto_increment' => true],
            'no'          => ['type' => 'VARCHAR', 'constraint' => 30],
            'supplier_id' => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'null' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => true, 'unsigned' => true],
            'tanggal'     => ['type' => 'DATE'],
            'sumber'      => ['type' => 'VARCHAR', 'constraint' => 25],
            'url'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'total'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'uang_muka'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'sisa'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'metode'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'jatuh_tempo' => ['type' => 'DATE', 'null' => true],
            'catatan'     => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('no');
        $this->forge->createTable('pembelian', true);

        // unit yang masuk lewat pembelian tertentu (riwayat batch)
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'auto_increment' => true],
            'pembelian_id'  => ['type' => 'INT', 'constraint' => true, 'unsigned' => true],
            'unit_id'       => ['type' => 'INT', 'constraint' => true, 'unsigned' => true],
            'harga_beli'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['pembelian_id', 'unit_id']);
        $this->forge->createTable('pembelian_unit', true);

        // ---------- Penjualan ----------
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'auto_increment' => true],
            'no'         => ['type' => 'VARCHAR', 'constraint' => 30],
            'user_id'    => ['type' => 'INT', 'constraint' => true, 'unsigned' => true],
            'tanggal'    => ['type' => 'DATE'],
            'total'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'dibayar'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'kembalian'  => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'metode'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'cash'],
            'catatan'    => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('no');
        $this->forge->createTable('penjualan', true);

        // harga_beli di-copy saat sold → laba historis tidak berubah retroactive
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'auto_increment' => true],
            'penjualan_id'  => ['type' => 'INT', 'constraint' => true, 'unsigned' => true],
            'unit_id'       => ['type' => 'INT', 'constraint' => true, 'unsigned' => true],
            'barang_id'     => ['type' => 'INT', 'constraint' => true, 'unsigned' => true],
            'harga_jual'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'harga_beli'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'laba'          => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'laba_persen'   => ['type' => 'DECIMAL', 'constraint' => '8,2', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['penjualan_id', 'unit_id']);
        $this->forge->createTable('penjualan_unit', true);

        // ---------- Utang (hanya sisi pembelian; penjualan cash/COD/TF) ----------
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'auto_increment' => true],
            'kode'          => ['type' => 'VARCHAR', 'constraint' => 30],
            'pembelian_id'  => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'null' => true],
            'supplier_id'   => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'null' => true],
            'tanggal'       => ['type' => 'DATE'],
            'jatuh_tempo'   => ['type' => 'DATE', 'null' => true],
            'nominal'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'terbayar'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 15, 'default' => 'belum'],
            'catatan'       => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('utang', true);

        $this->forge->addField([
            'id'       => ['type' => 'INT', 'constraint' => true, 'unsigned' => true, 'auto_increment' => true],
            'utang_id' => ['type' => 'INT', 'constraint' => true, 'unsigned' => true],
            'tanggal'  => ['type' => 'DATE'],
            'nominal'  => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'metode'   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'bukti'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'catatan'  => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('utang_id');
        $this->forge->createTable('pembayaran', true);
    }

    public function down()
    {
        $this->forge->dropTable('pembayaran', true);
        $this->forge->dropTable('utang', true);
        $this->forge->dropTable('penjualan_unit', true);
        $this->forge->dropTable('penjualan', true);
        $this->forge->dropTable('pembelian_unit', true);
        $this->forge->dropTable('pembelian', true);
        $this->forge->dropTable('unit', true);
        $this->forge->dropTable('barang', true);
        $this->forge->dropTable('supplier', true);
        $this->forge->dropTable('kategori', true);
        $this->forge->dropTable('users', true);
    }
}
