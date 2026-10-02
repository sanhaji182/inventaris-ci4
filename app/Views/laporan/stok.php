<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Laporan Posisi Stok</h5>
        <small class="text-muted">Rekap unit fisik & nilai modal per produk di katalog</small>
    </div>
    <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i> Cetak</button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th class="text-center">Tersedia</th>
                    <th class="text-center">Terjual</th>
                    <th class="text-center">Stok Min</th>
                    <th class="text-end">Nilai Modal Stok</th>
                    <th class="text-end">Potensi Omzet</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $totTersedia = 0;
                $totTerjual = 0;
                $totNilai = 0;
                $totOmzet = 0;
                foreach ($barang as $b):
                    $totTersedia += (int) $b['stok_tersedia'];
                    $totTerjual  += (int) $b['stok_terjual'];
                    $totNilai    += (float) $b['nilai_stok'];
                    $potensi      = (int) $b['stok_tersedia'] * (float) $b['harga_jual'];
                    $totOmzet    += $potensi;
            ?>
                <tr>
                    <td><code><?= esc($b['kode']) ?></code></td>
                    <td class="fw-semibold"><?= esc($b['nama']) ?></td>
                    <td><span class="badge badge-soft-secondary"><?= esc($b['kategori_nama']) ?></span></td>
                    <td class="text-center">
                        <span class="badge <?= (int) $b['stok_tersedia'] <= (int) $b['stok_min'] ? 'badge-soft-warning' : 'badge-soft-success' ?>">
                            <?= (int) $b['stok_tersedia'] ?>
                        </span>
                    </td>
                    <td class="text-center text-muted"><?= (int) $b['stok_terjual'] ?></td>
                    <td class="text-center text-muted"><?= (int) $b['stok_min'] ?></td>
                    <td class="text-end"><?= rupiah($b['nilai_stok']) ?></td>
                    <td class="text-end fw-semibold"><?= rupiah($potensi) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="3" class="text-end">Total:</th>
                    <th class="text-center"><?= $totTersedia ?></th>
                    <th class="text-center"><?= $totTerjual ?></th>
                    <th></th>
                    <th class="text-end"><?= rupiah($totNilai) ?></th>
                    <th class="text-end text-primary"><?= rupiah($totOmzet) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
