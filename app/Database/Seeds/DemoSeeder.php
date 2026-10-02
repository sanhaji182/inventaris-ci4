<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seed data demo yang saling konsisten:
 *  - pembelian -> unit (harga_beli) -> penjualan_unit (harga_beli dicopy)
 *  - laba per unit = harga_jual - harga_beli saat transaksi
 *  - utang + pembayaran menghasilkan status lunas/tidak
 */
class DemoSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        $db->transStrict(false);
        $db->transStart();

        $db->table('pembayaran')->emptyTable();
        $db->table('utang')->emptyTable();
        $db->table('penjualan_unit')->emptyTable();
        $db->table('penjualan')->emptyTable();
        $db->table('pembelian_unit')->emptyTable();
        $db->table('pembelian')->emptyTable();
        $db->table('unit')->emptyTable();
        $db->table('barang')->emptyTable();
        $db->table('supplier')->emptyTable();
        $db->table('kategori')->emptyTable();
        $db->table('users')->emptyTable();

        $now = date('Y-m-d H:i:s');

        // ---------- Users ----------
        $users = [];
        foreach ([
            ['admin',   'admin123',   'Administrator', 'admin'],
            ['pembeli', 'pembeli123', 'Kasir Toko',    'pembeli'],
            ['staf',    'staf123',    'Staf Gudang',   'staf'],
        ] as [$u, $p, $n, $r]) {
            $users[$u] = $db->table('users')->insert([
                'username'      => $u,
                'password_hash' => password_hash($p, PASSWORD_DEFAULT),
                'nama'          => $n,
                'role'          => $r,
                'status'        => 'aktif',
                'created_at'    => $now,
            ]); $users[$u] = $db->insertID();
        }

        // ---------- Kategori ----------
        $kategori = [];
        foreach ([
            ['HP',    'Handphone'],
            ['LAPTOP', 'Laptop & Notebook'],
            ['TAB',   'Tablet'],
            ['AUDIO', 'Audio & Headset'],
            ['WATCH', 'Smartwatch & Aksesori'],
            ['KAMERA', 'Kamera'],
        ] as $i => [$kode, $nama]) {
            $kategori[$kode] = $db->table('kategori')->insert([
                'kode'       => $kode,
                'nama'       => $nama,
                'keterangan' => null,
            ]); $kategori[$kode] = $db->insertID();
        }

        // ---------- Supplier ----------
        $supplier = [];
        foreach ([
            ['SUP-001', 'Gadget Murah Jaya',   'Jl.cyber No. 12, Jakarta', '0812-1111-2222'],
            ['SUP-002', 'Tekno Second Online', 'Tokopedia: teknosecond88',  '0813-3333-4444'],
            ['SUP-003', 'Distributor Android', 'Jl.Orchard No. 5, Jakarta', '0815-5555-6666'],
        ] as [$kode, $nama, $alamat, $telp]) {
            $supplier[$kode] = $db->table('supplier')->insert([
                'kode' => $kode, 'nama' => $nama, 'alamat' => $alamat,
                'telepon' => $telp, 'catatan' => null,
            ]); $supplier[$kode] = $db->insertID();
        }

        // ---------- Barang (katalog) ----------
        // [kode, nama, kategori, merek, spek, harga_jual, stok_min]
        $barangDef = [
            ['B0001', 'Samsung Galaxy A15',   'HP',     'Samsung', 'RAM 6/128GB, AMOLED 90Hz, Baterai 5000mAh', 2750000, 3],
            ['B0002', 'Xiaomi Redmi Note 13', 'HP',     'Xiaomi',  'RAM 8/256GB, AMOLED 120Hz, Baterai 5000mAh', 2850000, 3],
            ['B0003', 'iPhone 12 64GB',       'HP',     'Apple',   'RAM 4/64GB, Camera 12MP, Baterai 2814mAh',       5200000, 2],
            ['B0004', 'Infinix Hot 40i',      'HP',     'Infinix', 'RAM 8/128GB, Layar 90Hz, Baterai 5000mAh',      1950000, 4],
            ['B0005', 'Realme C55',           'HP',     'Realme',  'RAM 6/128GB, Kamera 64MP, Baterai 5000mAh',      2050000, 4],
            ['B0006', 'ASUS Zenbook 14',      'LAPTOP', 'ASUS',    'i5-1235U, RAM 16GB, SSD 512GB, OLED 14"',       9450000, 1],
            ['B0007', 'Lenovo IdeaPad Slim 3', 'LAPTOP', 'Lenovo', 'Ryzen 5 7530U, RAM 16GB, SSD 512GB',          7850000, 1],
            ['B0008', 'Samsung Galaxy Tab A9', 'TAB',   'Samsung', 'RAM 4/128GB, Layar 11", Speaker Dolby',       2150000, 2],
            ['B0009', 'Xiaomi Pad 6',         'TAB',     'Xiaomi',  'RAM 8/256GB, Layar 11", 144Hz',             3250000, 2],
            ['B0010', 'JBL Tune 510BT',       'AUDIO',   'JBL',     'Bluetooth 5.0, Baterai 40 Jam, Multipoint',    650000,  4],
            ['B0011', 'TWS Sony WF-C500',     'AUDIO',   'Sony',    'Bluetooth 5.2, ANC, Baterai 24 Jam',          1150000, 4],
            ['B0012', 'Apple EarPods 2',      'AUDIO',   'Apple',   'Bluetooth 5.0,ANC, Lightning',              1450000, 3],
            ['B0013', 'Apple Watch SE 2',     'WATCH',   'Apple',   'GPS, Retina, Baterai 18 Jam, 44mm',          2850000, 2],
            ['B0014', 'Xiaomi Smart Band 8',  'WATCH',   'Xiaomi',  'AMOLED 1.62", Baterai 16 Hari',                 425000,  6],
            ['B0015', 'Amazfit GTS 4 Mini',   'WATCH',   'Amazfit', 'AMOLED, GPS, Baterai 10 Hari',                1250000, 3],
            ['B0016', 'Canon EOS M200',       'KAMERA',  'Canon',   'Sensor APS-C 24MP, Video 4K, Kit 18-45mm',     9850000, 1],
        ];
        $barang = [];
        foreach ($barangDef as [$kode, $nama, $k, $merek, $spek, $jual, $min]) {
            $barang[$kode] = $db->table('barang')->insert([
                'kode' => $kode, 'nama' => $nama, 'kategori_id' => $kategori[$k],
                'merek' => $merek, 'spek' => $spek, 'harga_jual' => $jual,
                'stok_min' => $min, 'foto' => null, 'created_at' => $now,
            ]); $barang[$kode] = $db->insertID();
        }

        // ---------- Helper buat unit ----------
        $unitSeq = 0;
        $unit = function (string $barangKode, int $hargaBeli, string $kondisi, string $status, string $tanggal, ?string $imei, string $lokasi = 'Rak A') use ($db, $barang, $supplier, $now, &$unitSeq) {
            $unitSeq++;
            $kode = 'U' . str_pad((string) $unitSeq, 4, '0', STR_PAD_LEFT);

            return $db->table('unit')->insert([
                'kode' => $kode, 'barang_id' => $barang[$barangKode], 'imei' => $imei,
                'kondisi' => $kondisi, 'harga_beli' => $hargaBeli,
                'supplier_id' => $supplier['SUP-001'], 'pembelian_id' => null,
                'tanggal_masuk' => $tanggal, 'status' => $status,
                'lokasi' => $lokasi, 'catatan' => null, 'created_at' => $now,
            ]); return $db->insertID();
        };

        // ---------- Pembelian ----------
        // [no, tanggal, supplier, sumber, metode, items[(barangKode, hargaBeli, kondisi, imei)]]
        $pembelianDef = [
            ['PBL-' . date('Ymd') . '-001', date('Y-m-d', strtotime('-120 days')), 'SUP-001', 'Tokopedia', 'termin', [
                ['B0001', 2450000, 'mulus', '352094051234567'],
                ['B0002', 2550000, 'mulus', '356938112223334'],
                ['B0010',  480000, 'mulus', '694550012345'],
                ['B0014',  310000, 'mulus', 'XIAOMI-SB8-0001'],
                ['B0014',  310000, 'mulus', 'XIAOMI-SB8-0002'],
            ]],
            ['PBL-' . date('Ymd') . '-002', date('Y-m-d', strtotime('-95 days')), 'SUP-002', 'Shopee', 'tunai', [
                ['B0004', 1750000, 'mulus', '99441122667788'],
                ['B0005', 1820000, 'mulus', '88023144556677'],
                ['B0011',  890000, 'mulus', 'SNY-WF500-011'],
                ['B0012', 1250000, 'mulus', 'AP-EP2-2201'],
            ]],
            ['PBL-' . date('Ymd') . '-003', date('Y-m-d', strtotime('-70 days')), 'SUP-003', 'Offline', 'termin', [
                ['B0003', 4750000, 'mulus', '366312099887766'],
                ['B0003', 4700000, 'mulus', '366312099887767'],
                ['B0006', 8900000, 'mulus', 'ASUS-ZB14-2201'],
                ['B0013', 2550000, 'mulus', 'AW-SE2-4411'],
                ['B0013', 2550000, 'mulus', 'AW-SE2-4412'],
            ]],
            ['PBL-' . date('Ymd') . '-004', date('Y-m-d', strtotime('-45 days')), 'SUP-001', 'Tokopedia', 'transfer', [
                ['B0007', 7350000, 'mulus', 'LNV-IPS3-0099'],
                ['B0008', 1950000, 'mulus', 'SM-TABA9-1122'],
                ['B0009', 2980000, 'mulus', null],
                ['B0010',  475000, 'mulus', '694550033399'],
                ['B0011',  870000, 'mulus', 'SNY-WF500-022'],
                ['B0015', 1090000, 'mulus', 'AMZ-GTS4-003'],
            ]],
            ['PBL-' . date('Ymd') . '-005', date('Y-m-d', strtotime('-20 days')), 'SUP-002', 'Shopee', 'transfer', [
                ['B0001', 2480000, 'mulus', '352094051239999'],
                ['B0004', 1780000, 'mulus', '99441122998877'],
                ['B0005', 1840000, 'mulus', '88023144112266'],
                ['B0012', 1260000, 'mulus', 'AP-EP2-3390'],
                ['B0012', 1260000, 'mulus', 'AP-EP2-3391'],
            ]],
            ['PBL-' . date('Ymd') . '-006', date('Y-m-d', strtotime('-8 days')), 'SUP-003', 'Offline', 'termin', [
                ['B0002', 2560000, 'mulus', '35693811999888'],
                ['B0002', 2560000, 'mulus', '35693811999889'],
                ['B0008', 1980000, 'mulus', 'SM-TABA9-4455'],
                ['B0014',  315000, 'mulus', 'XIAOMI-SB8-0003'],
                ['B0014',  315000, 'mulus', 'XIAOMI-SB8-0004'],
                ['B0014',  315000, 'mulus', 'XIAOMI-SB8-0005'],
            ]],
            ['PBL-' . date('Ymd') . '-007', date('Y-m-d', strtotime('-3 days')), 'SUP-001', 'Tokopedia', 'transfer', [
                ['B0003', 4780000, 'mulus', '366312099887799'],
                ['B0016', 9350000, 'mulus', null],
            ]],
            ['PBL-' . date('Ymd') . '-008', date('Y-m-d', strtotime('-25 days')), 'SUP-003', 'Offline', 'termin', [
                ['B0005', 1860000, 'mulus', '88023144335544'],
                ['B0010',  482000, 'mulus', '694550077788'],
                ['B0015', 1100000, 'mulus', 'AMZ-GTS4-021'],
            ]],
            ['PBL-' . date('Ymd') . '-009', date('Y-m-d', strtotime('-2 days')), 'SUP-003', 'Offline', 'termin', [
                ['B0005', 1860000, 'mulus', '88023144335555'],
                ['B0010',  482000, 'mulus', '694550077799'],
            ]],
        ];

        $pembelian = [];
        $unitOfPembelian = [];

        foreach ($pembelianDef as $idx => [$no, $tgl, $sup, $sumber, $metode, $items]) {
            $total = array_sum(array_map(static fn ($i) => $i[1], $items));

            $pid = $db->table('pembelian')->insert([
                'no' => $no, 'supplier_id' => $supplier[$sup], 'user_id' => $users['admin'],
                'tanggal' => $tgl, 'sumber' => $sumber,
                'url' => in_array($sumber, ['Tokopedia', 'Shopee'], true) ? 'https://' . strtolower($sumber) . '.com/orders/demo' : null,
                'total' => $total, 'uang_muka' => 0, 'sisa' => $total,
                'metode' => $metode,
                'jatuh_tempo' => $metode === 'termin' ? date('Y-m-d', strtotime($tgl . ' +30 days')) : null,
                'catatan' => null, 'created_at' => $now,
            ]); $pid = $db->insertID();

            $pembelian[$no] = ['id' => $pid, 'total' => $total, 'metode' => $metode, 'tgl' => $tgl];

            foreach ($items as [$bKode, $hBeli, $kondisi, $imei]) {
                $uid = $unit($bKode, $hBeli, $kondisi, 'tersedia', $tgl, $imei);
                $db->table('unit')->update(['pembelian_id' => $pid], ['id' => $uid]);
                $db->table('pembelian_unit')->insert([
                    'pembelian_id' => $pid, 'unit_id' => $uid, 'harga_beli' => $hBeli,
                ]);
                $unitOfPembelian[] = ['unit_id' => $uid, 'barang_id' => $barang[$bKode], 'harga_beli' => $hBeli, 'tgl' => $tgl];
            }
        }

        // ---------- Utang hanya dari pembelian termin ----------
        // Three termin purchases, each with a different payment scenario so the
        // dashboard + aging buckets all have real data:
        //   #1 -> partially paid, overdue (>60 hari)  bucket merah
        //   #2 -> fully paid                          lunas
        //   #3 -> partially paid, jatuh tempo hari ini  bucket hijau
        $terminPlan = [
            1 => ['bayarPersen' => 50, 'jatuhTempo' => '+30 days', 'keterangan' => 'Termin 30 hari - lewat jatuh tempo 90 hari, separuh menunggak'],
            2 => ['bayarPersen' => 60, 'jatuhTempo' => '+30 days', 'keterangan' => 'Termin 30 hari - lewat jatuh tempo 40 hari, sisa 40% menunggak'],
            3 => ['bayarPersen' => 40, 'jatuhTempo' => '+14 days', 'keterangan' => 'Termin 14 hari - lewat jatuh tempo 11 hari'],
            4 => ['bayarPersen' => 100, 'jatuhTempo' => '+30 days', 'keterangan' => 'Termin 30 hari - lunas dibayar penuh'],
            5 => ['bayarPersen' => 30, 'jatuhTempo' => '+30 days', 'keterangan' => 'Termin 30 hari - baru 30% dibayar'],
        ];

        $noU = 0;
        foreach ($pembelian as $no => $p) {
            if ($p['metode'] !== 'termin') {
                // Non-termin purchases are fully paid at the point of sale.
                $db->table('pembelian')->update(['uang_muka' => $p['total'], 'sisa' => 0], ['id' => $p['id']]);
                continue;
            }
            if (! isset($terminPlan[$noU + 1])) {
                // Termin purchase beyond the plan: leave fully unpaid (sisa = total).
                continue;
            }

            $noU++;
            $plan      = $terminPlan[$noU];
            $kode      = 'UTG-' . date('Ymd', strtotime($p['tgl'])) . '-' . str_pad((string) $noU, 3, '0', STR_PAD_LEFT);
            $jatuhTempo = date('Y-m-d', strtotime($p['tgl'] . ' ' . $plan['jatuhTempo']));
            $terbayar   = round($p['total'] * ($plan['bayarPersen'] / 100), 2);
            $lunas      = $terbayar >= $p['total'];

            $supId = $db->table('pembelian')->select('supplier_id')->where('id', $p['id'])->get()->getRowArray();
            $supId = $supId['supplier_id'] ?? null;

            $utangId = $db->table('utang')->insert([
                'kode'         => $kode,
                'pembelian_id' => $p['id'],
                'supplier_id'  => $supId,
                'tanggal'      => $p['tgl'],
                'jatuh_tempo'  => $jatuhTempo,
                'nominal'      => $p['total'],
                'terbayar'     => $terbayar,
                'status'       => $lunas ? 'lunas' : 'belum',
                'catatan'      => $plan['keterangan'],
                'created_at'   => $now,
            ]); $utangId = $db->insertID();

            // Payment history: one initial payment at purchase, one later installment
            // for the still-open invoices so the cicilan list is realistic.
            $riwayat   = [[$p['tgl'], $terbayar]];
            $totalBayar = $terbayar;
            if (! $lunas) {
                $sisa   = $p['total'] - $terbayar;
                $bayar2 = in_array($noU, [1, 2, 3], true) ? round($sisa * 0.5, 2) : 0.0;
                if ($bayar2 > 0) {
                    $riwayat[] = [date('Y-m-d', strtotime($p['tgl'] . ' +14 days')), $bayar2];
                    $totalBayar += $bayar2;
                    $db->table('utang')->update(['terbayar' => $totalBayar], ['id' => $utangId]);
                }
            }

            // Ripple total pembayaran (termasuk cicilan) ke pembelian.
            $db->table('pembelian')->update([
                'uang_muka' => $totalBayar,
                'sisa'      => max(0, $p['total'] - $totalBayar),
            ], ['id' => $p['id']]);

            foreach ($riwayat as $i => [$tgl, $nominal]) {
                if ($nominal <= 0) {
                    continue;
                }
                $db->table('pembayaran')->insert([
                    'utang_id'   => $utangId,
                    'tanggal'    => $tgl,
                    'nominal'    => $nominal,
                    'metode'     => 'transfer',
                    'bukti'      => 'bukti-' . strtolower($kode) . '-' . ($i + 1) . '.jpg',
                    'catatan'    => $i === 0 ? 'Pembayaran saat pembelian' : 'Cicilan ke-2',
                    'created_at' => $now,
                ]);
            }
        }

        // ---------- Penjualan ----------
        // Unit dipilih dari unitOfPembelian (FIFO-ish: unit terlama lebih dulu).
        $pool = $unitOfPembelian;
        $noJ = 0;

        $jual = function (array $unitRows, string $tgl, string $metode, string $user, int $diskonPersen = 0, ?string $catatan = null) use (&$pool, &$noJ, $db, $users, $barang, $now) {
            $noJ++;
            $no = 'PJL-' . date('Ymd', strtotime($tgl)) . '-' . str_pad((string) $noJ, 3, '0', STR_PAD_LEFT);

            $subtotal = 0;
            $items = [];

            foreach ($unitRows as $u) {
                $hJual = (float) $db->table('barang')->select('harga_jual')->where('id', $u['barang_id'])->get()->getRow('harga_jual');
                $hBeli = (float) $u['harga_beli'];
                $laba  = $hJual - $hBeli;
                $subtotal += $hJual;
                $items[] = ['unit' => $u, 'harga_jual' => $hJual, 'harga_beli' => $hBeli, 'laba' => $laba, 'persen' => $hJual > 0 ? ($laba / $hJual) * 100 : 0];
            }

            $diskon = $subtotal * ($diskonPersen / 100);
            $total  = $subtotal - $diskon;
            $laba   = array_sum(array_column($items, 'laba')) - $diskon;

            $dibayar = $metode === 'cash' ? $total : ($total > 0 ? round($total * 0.5, 2) : 0);
            $kembalian = $dibayar - $total;

            $pid = $db->table('penjualan')->insert([
                'no' => $no, 'user_id' => $users[$user], 'tanggal' => $tgl,
                'total' => $total, 'dibayar' => $dibayar, 'kembalian' => $kembalian,
                'metode' => $metode, 'catatan' => $catatan, 'created_at' => $now,
            ]); $pid = $db->insertID();

            foreach ($items as $it) {
                $db->table('penjualan_unit')->insert([
                    'penjualan_id' => $pid, 'unit_id' => $it['unit']['unit_id'],
                    'barang_id' => $it['unit']['barang_id'],
                    'harga_jual' => $it['harga_jual'], 'harga_beli' => $it['harga_beli'],
                    'laba' => $it['laba'], 'laba_persen' => $it['persen'],
                ]);
                $db->table('unit')->update(['status' => 'terjual', 'catatan' => 'Terjual pada ' . $no], ['id' => $it['unit']['unit_id']]);
            }

            return ['id' => $pid, 'no' => $no, 'total' => $total, 'laba' => $laba, 'tgl' => $tgl];
        };

        // Ambil unit berdasarkan (barang, tanggal) untuk simulates FIFO
        $pick = function (string $barangKode, int $n) use (&$pool, $barang) {
            $out = [];
            foreach ($pool as $i => $p) {
                if ($p['barang_id'] === $barang[$barangKode]) {
                    $out[] = $p;
                    unset($pool[$i]);
                    if (count($out) >= $n) {
                        break;
                    }
                }
            }
            $pool = array_values($pool);

            return $out;
        };

        // Bulan lalu: transaksi rutin
        $jual($pick('B0001', 1), date('Y-m-d', strtotime('-100 days')), 'cash', 'pembeli');
        $jual($pick('B0002', 1), date('Y-m-d', strtotime('-92 days')), 'transfer', 'pembeli');
        $jual($pick('B0010', 1), date('Y-m-d', strtotime('-88 days')), 'qris', 'pembeli');
        $jual($pick('B0014', 1), date('Y-m-d', strtotime('-80 days')), 'cash', 'pembeli', 10, 'Promo RAMadlan');
        $jual($pick('B0004', 1), date('Y-m-d', strtotime('-70 days')), 'cash', 'pembeli');
        $jual($pick('B0005', 1), date('Y-m-d', strtotime('-65 days')), 'qris', 'pembeli');
        $jual($pick('B0011', 1), date('Y-m-d', strtotime('-60 days')), 'transfer', 'pembeli');
        $jual($pick('B0012', 1), date('Y-m-d', strtotime('-55 days')), 'cash', 'pembeli');
        $jual($pick('B0003', 1), date('Y-m-d', strtotime('-50 days')), 'transfer', 'pembeli');
        $jual($pick('B0006', 1), date('Y-m-d', strtotime('-45 days')), 'cash', 'pembeli');
        $jual($pick('B0013', 1), date('Y-m-d', strtotime('-40 days')), 'qris', 'pembeli');
        $jual($pick('B0013', 1), date('Y-m-d', strtotime('-38 days')), 'cash', 'pembeli');

        // 30 hari keunder
        $jual($pick('B0007', 1), date('Y-m-d', strtotime('-32 days')), 'transfer', 'pembeli');
        $jual($pick('B0008', 1), date('Y-m-d', strtotime('-28 days')), 'cash', 'pembeli');
        $jual($pick('B0009', 1), date('Y-m-d', strtotime('-25 days')), 'cash', 'pembeli', 5);
        $jual($pick('B0010', 1), date('Y-m-d', strtotime('-22 days')), 'qris', 'pembeli');
        $jual($pick('B0011', 1), date('Y-m-d', strtotime('-18 days')), 'transfer', 'pembeli');
        $jual($pick('B0015', 1), date('Y-m-d', strtotime('-14 days')), 'cash', 'pembeli');

        // 7 hari keunder
        $jual($pick('B0001', 1), date('Y-m-d', strtotime('-6 days')), 'qris', 'pembeli');
        $jual($pick('B0004', 1), date('Y-m-d', strtotime('-5 days')), 'cash', 'pembeli');
        $jual($pick('B0005', 1), date('Y-m-d', strtotime('-4 days')), 'transfer', 'pembeli');
        $jual($pick('B0012', 1), date('Y-m-d', strtotime('-3 days')), 'cash', 'pembeli');
        $jual($pick('B0012', 1), date('Y-m-d', strtotime('-2 days')), 'cash', 'pembeli', 15, 'Flash sale');

        // Hari ini / kemarin
        $jual($pick('B0002', 1), date('Y-m-d', strtotime('-1 day')), 'transfer', 'pembeli');
        $jual($pick('B0008', 1), date('Y-m-d', strtotime('-1 day')), 'cash', 'pembeli');
        $jual($pick('B0002', 1), date('Y-m-d'), 'cash', 'pembeli');
        $jual($pick('B0014', 1), date('Y-m-d'), 'qris', 'pembeli');

        // Sisa unit di rak: sebagian ditandai rusak supaya laporan stok tidak
        // terlihat sempurna dan ada kasus data yang perlu ditangani.
        $rusak = $db->table('unit')->where('status', 'tersedia')->orderBy('id', 'RANDOM')->limit(2)->get()->getResultArray();
        foreach ($rusak as $r) {
            $db->table('unit')->update([
                'status'  => 'rusak',
                'catatan' => 'Retak layar saat QC, tidak dijual',
            ], ['id' => $r['id']]);
        }

        $db->transComplete();

        echo "\n=== SEED SELESAI ===\n";
        echo "Users     : " . $db->table('users')->countAllResults() . "\n";
        echo "Kategori  : " . $db->table('kategori')->countAllResults() . "\n";
        echo "Supplier  : " . $db->table('supplier')->countAllResults() . "\n";
        echo "Barang    : " . $db->table('barang')->countAllResults() . "\n";
        echo "Unit      : " . $db->table('unit')->countAllResults() . "\n";
        echo "Pembelian : " . $db->table('pembelian')->countAllResults() . "\n";
        echo "Penjualan : " . $db->table('penjualan')->countAllResults() . "\n";
        echo "Utang     : " . $db->table('utang')->countAllResults() . "\n";
        echo "Bayar     : " . $db->table('pembayaran')->countAllResults() . "\n";
    }
}
