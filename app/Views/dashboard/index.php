<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Bento Stat Grid -->
<div class="row g-3 mb-4">
    <!-- Total Barang -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="label text-muted small fw-semibold text-uppercase">Total Item Katalog</span>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
            <div class="value fs-4 fw-bold mb-1"><?= (int) $totalBarang ?> <span class="fs-6 fw-normal text-muted">Model</span></div>
            <div class="trend small text-muted">
                Total fisik: <strong><?= (int) $totalStok ?> unit</strong> siap pakai
            </div>
        </div>
    </div>

    <!-- Total Valuasi Modal (Harga Beli) -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="label text-muted small fw-semibold text-uppercase">Total Modal (Harga Beli)</span>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <div class="value fs-4 fw-bold font-monospace mb-1 text-primary"><?= rupiah($totalModal) ?></div>
            <div class="trend small text-muted">
                Nilai aset inventaris saat ini
            </div>
        </div>
    </div>

    <!-- Potensi Omzet & Estimasi Laba -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="label text-muted small fw-semibold text-uppercase">Potensi Untung Stok</span>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
            <div class="value fs-4 fw-bold font-monospace mb-1 text-success"><?= rupiah($potensiLaba) ?></div>
            <div class="trend small text-muted">
                Jika seluruh <?= (int) $totalStok ?> stok habis terjual
            </div>
        </div>
    </div>

    <!-- Realisasi Laba Terkumpul -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="label text-muted small fw-semibold text-uppercase">Untung Bersih Masuk</span>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <div class="value fs-4 fw-bold font-monospace mb-1 text-dark"><?= rupiah($realisasiLaba) ?></div>
            <div class="trend small text-muted">
                Dari <strong><?= (int) $totalItemTerjual ?> unit</strong> barang keluar
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Kolom Kiri: Riwayat Transaksi Keluar-Masuk & Laba -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary fs-5"></i>
                    <span class="fw-bold">Aktivitas Keluar / Masuk Terakhir</span>
                </div>
                <a href="/stok" class="btn btn-sm btn-outline-primary">Catat / Lihat Semua →</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu & Barang</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-end">Harga Transaksi</th>
                            <th class="text-end">Untung / Rugi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($riwayatTerakhir)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat keluar/masuk stok.</td></tr>
                    <?php else: ?>
                        <?php foreach ($riwayatTerakhir as $r): ?>
                        <tr>
                            <td>
                                <div class="fw-bold small"><?= esc($r['nama_barang']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    <span class="font-monospace"><?= esc($r['kode_barang']) ?></span> · <?= esc($r['tanggal']) ?>
                                    <?php if (! empty($r['keterangan'])): ?>
                                        (<?= esc($r['keterangan']) ?>)
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if ($r['jenis'] === 'masuk'): ?>
                                    <span class="badge badge-soft-info"><i class="bi bi-arrow-down-left"></i> Masuk</span>
                                <?php elseif ($r['jenis'] === 'keluar'): ?>
                                    <span class="badge badge-soft-success"><i class="bi bi-arrow-up-right"></i> Keluar</span>
                                <?php else: ?>
                                    <span class="badge badge-soft-secondary">Sesuaikan</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center font-monospace fw-semibold"><?= (int) $r['jumlah'] ?></td>
                            <td class="text-end font-monospace"><?= rupiah($r['harga_transaksi']) ?></td>
                            <td class="text-end font-monospace fw-bold">
                                <?php if ($r['jenis'] === 'keluar'): ?>
                                    <?php if ((float) $r['total_laba'] >= 0): ?>
                                        <span class="text-success">+<?= rupiah($r['total_laba']) ?></span>
                                    <?php else: ?>
                                        <span class="text-danger"><?= rupiah($r['total_laba']) ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Peringatan Stok Menipis -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                    <span class="fw-bold">Perlu Kulak / Stok Menipis</span>
                </div>
            </div>
            <div class="p-3">
                <?php if (empty($stokMenipis)): ?>
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-check2-circle fs-2 text-success d-block mb-1"></i>
                        Semua stok barang dalam kondisi aman.
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($stokMenipis as $sm): ?>
                        <div class="list-group-item px-0 py-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small text-truncate" style="max-width: 180px;"><?= esc($sm['nama_barang']) ?></span>
                                <span class="badge bg-danger rounded-pill"><?= (int) $sm['stok'] ?> unit</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small text-muted">
                                <span>Modal: <?= rupiah($sm['harga_beli']) ?></span>
                                <?php if (! empty($sm['link_pembelian'])): ?>
                                    <a href="<?= esc($sm['link_pembelian']) ?>" target="_blank" class="text-primary text-decoration-none">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Beli Lagi
                                    </a>
                                <?php elseif (! empty($sm['sumber_toko'])): ?>
                                    <span class="text-truncate" style="max-width:120px;"><?= esc($sm['sumber_toko']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mt-3 text-center">
                    <a href="/barang" class="btn btn-sm btn-outline-secondary w-100">Buka Katalog Inventaris</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
