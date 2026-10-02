<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Nonaktifkan foreign key checks sementara saat reset
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $db->table('riwayat_stok')->emptyTable();
        $db->table('barang')->emptyTable();
        $db->table('kategori')->emptyTable();
        $db->table('users')->emptyTable();
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        $now = date('Y-m-d H:i:s');

        // 1. Akun Pengguna: Admin & Pengelola (Standar BNSP)
        $users = [
            [
                'nama'       => 'Administrator',
                'username'   => 'admin',
                'password'   => password_hash('admin123', PASSWORD_BCRYPT),
                'role'       => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama'       => 'Pengelola Gudang',
                'username'   => 'pengelola',
                'password'   => password_hash('pengelola123', PASSWORD_BCRYPT),
                'role'       => 'pengelola',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        $db->table('users')->insertBatch($users);
        $adminId = 1;
        $pengelolaId = 2;

        // 2. Kategori Barang
        $kategori = [
            ['nama' => 'Smartphone & Tablet', 'keterangan' => 'Gawai genggam, HP Android & iOS', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Laptop & Komputer', 'keterangan' => 'Ultrabook, laptop kerja dan gaming', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Audio & TWS', 'keterangan' => 'Earphone nirkabel, headphone, speaker', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Aksesoris & Wearable', 'keterangan' => 'Smartwatch, charger, kabel data', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Kamera & Fotografi', 'keterangan' => 'Kamera mirrorless dan lensa', 'created_at' => $now, 'updated_at' => $now],
        ];
        $db->table('kategori')->insertBatch($kategori);

        // 3. Barang Inventaris dengan Modal Beli, Jual, Link Pembelian & Minus/Catatan
        $barangData = [
            [
                'kode_barang'    => 'BRG-001',
                'nama_barang'    => 'iPhone 13 128GB Midnight (Bekas)',
                'kategori_id'    => 1,
                'stok'           => 4,
                'satuan'         => 'Unit',
                'harga_beli'     => 7800000.00,
                'harga_jual'     => 8950000.00,
                'link_pembelian' => 'https://tokopedia.link/iphone13-second-exibox',
                'sumber_toko'    => 'iStore Mangga Dua (Tokopedia)',
                'minus_kondisi'  => 'Battery Health 86%, ada baret halus pemakaian di bezel kanan.',
                'catatan'        => 'Garansi personal toko 1 bulan, fullset original box.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'kode_barang'    => 'BRG-002',
                'nama_barang'    => 'Samsung Galaxy S23 FE 128GB Mint',
                'kategori_id'    => 1,
                'stok'           => 6,
                'satuan'         => 'Unit',
                'harga_beli'     => 6200000.00,
                'harga_jual'     => 7100000.00,
                'link_pembelian' => 'https://shopee.co.id/samsung-official-s23fe',
                'sumber_toko'    => 'Samsung Official Store Shopee',
                'minus_kondisi'  => 'Mulus 99% like new, plastik tepi masih nempel.',
                'catatan'        => 'Barang clearance sale event 9.9.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'kode_barang'    => 'BRG-003',
                'nama_barang'    => 'MacBook Air M1 2020 8/256GB Space Grey',
                'kategori_id'    => 2,
                'stok'           => 2,
                'satuan'         => 'Unit',
                'harga_beli'     => 8500000.00,
                'harga_jual'     => 9900000.00,
                'link_pembelian' => 'https://tokopedia.com/applecenter-jkt',
                'sumber_toko'    => 'AppleCenter Jakarta',
                'minus_kondisi'  => 'Cycle count 140, rubber list layar ada sedikit aus di pojok atas.',
                'catatan'        => 'Keyboard US layout, charger original 30W include.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'kode_barang'    => 'BRG-004',
                'nama_barang'    => 'Sony WH-1000XM4 Noise Canceling Black',
                'kategori_id'    => 3,
                'stok'           => 5,
                'satuan'         => 'Unit',
                'harga_beli'     => 2900000.00,
                'harga_jual'     => 3550000.00,
                'link_pembelian' => 'https://tokopedia.link/sony-audio-official',
                'sumber_toko'    => 'Sony Audio Mall',
                'minus_kondisi'  => 'Kondisi fisik 95%, earpad busa bersih no crack.',
                'catatan'        => 'Pouch case kulit dan kabel jack 3.5mm lengkap.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'kode_barang'    => 'BRG-005',
                'nama_barang'    => 'Xiaomi Redmi Note 13 8/256GB Midnight Black',
                'kategori_id'    => 1,
                'stok'           => 8,
                'satuan'         => 'Unit',
                'harga_beli'     => 2100000.00,
                'harga_jual'     => 2450000.00,
                'link_pembelian' => 'https://shopee.co.id/xiaomi-authorized-partner',
                'sumber_toko'    => 'Grosir Ponsel Roxy Mas',
                'minus_kondisi'  => 'Baru segel box (BNIB).',
                'catatan'        => 'Garansi resmi Xiaomi Indonesia 15 bulan.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'kode_barang'    => 'BRG-006',
                'nama_barang'    => 'Apple Watch SE 2 40mm Starlight GPS',
                'kategori_id'    => 4,
                'stok'           => 3,
                'satuan'         => 'Unit',
                'harga_beli'     => 3100000.00,
                'harga_jual'     => 3750000.00,
                'link_pembelian' => 'https://tokopedia.link/istyle-apple-store',
                'sumber_toko'    => 'iBox Central Park',
                'minus_kondisi'  => 'Battery Health 92%, strap sport band original ada sedikit pudar.',
                'catatan'        => 'Kabel magnetic charger include.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'kode_barang'    => 'BRG-007',
                'nama_barang'    => 'Lensa Sony FE 50mm f/1.8 Prime (Sel50F18F)',
                'kategori_id'    => 5,
                'stok'           => 2,
                'satuan'         => 'Unit',
                'harga_beli'     => 2200000.00,
                'harga_jual'     => 2700000.00,
                'link_pembelian' => 'https://tokopedia.link/bursa-kamera-profesional',
                'sumber_toko'    => 'Bursa Kamera Profesional Jkt',
                'minus_kondisi'  => 'Optik bening bebas jamur/fog, autofokus responsif.',
                'catatan'        => 'Lens hood dan front/rear cap komplit.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'kode_barang'    => 'BRG-008',
                'nama_barang'    => 'Logitech MX Master 3S Wireless Mouse',
                'kategori_id'    => 4,
                'stok'           => 7,
                'satuan'         => 'Unit',
                'harga_beli'     => 1250000.00,
                'harga_jual'     => 1550000.00,
                'link_pembelian' => 'https://shopee.co.id/logitech-g-official',
                'sumber_toko'    => 'Distributor Komputer Harco',
                'minus_kondisi'  => 'Baru segel pabrik (BNIB).',
                'catatan'        => 'Sensor 8000 DPI quiet click, garansi 1 tahun.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ];
        $db->table('barang')->insertBatch($barangData);

        // 4. Riwayat Masuk / Keluar Stok dengan kalkulasi laba riil
        $riwayatData = [
            [
                'barang_id'       => 1,
                'user_id'         => $adminId,
                'jenis'           => 'masuk',
                'jumlah'          => 5,
                'harga_transaksi' => 7800000.00,
                'total_laba'      => 0.00,
                'keterangan'      => 'Kulak awal batch 1',
                'tanggal'         => date('Y-m-d', strtotime('-15 days')),
                'created_at'      => $now,
            ],
            [
                'barang_id'       => 1,
                'user_id'         => $pengelolaId,
                'jenis'           => 'keluar',
                'jumlah'          => 1,
                'harga_transaksi' => 8950000.00,
                'total_laba'      => 1150000.00, // (8.950.000 - 7.800.000) * 1
                'keterangan'      => 'Penjualan ke pelanggan offline (Mas Budi)',
                'tanggal'         => date('Y-m-d', strtotime('-5 days')),
                'created_at'      => $now,
            ],
            [
                'barang_id'       => 2,
                'user_id'         => $adminId,
                'jenis'           => 'masuk',
                'jumlah'          => 8,
                'harga_transaksi' => 6200000.00,
                'total_laba'      => 0.00,
                'keterangan'      => 'Stok flash sale promo',
                'tanggal'         => date('Y-m-d', strtotime('-10 days')),
                'created_at'      => $now,
            ],
            [
                'barang_id'       => 2,
                'user_id'         => $pengelolaId,
                'jenis'           => 'keluar',
                'jumlah'          => 2,
                'harga_transaksi' => 7100000.00,
                'total_laba'      => 1800000.00, // (7.100.000 - 6.200.000) * 2
                'keterangan'      => 'Penjualan kirim via Gojek instan',
                'tanggal'         => date('Y-m-d', strtotime('-2 days')),
                'created_at'      => $now,
            ],
            [
                'barang_id'       => 4,
                'user_id'         => $pengelolaId,
                'jenis'           => 'keluar',
                'jumlah'          => 1,
                'harga_transaksi' => 3550000.00,
                'total_laba'      => 650000.00, // (3.550.000 - 2.900.000) * 1
                'keterangan'      => 'Penjualan unit headphone Sony',
                'tanggal'         => date('Y-m-d', strtotime('-1 days')),
                'created_at'      => $now,
            ],
        ];
        $db->table('riwayat_stok')->insertBatch($riwayatData);
    }
}
