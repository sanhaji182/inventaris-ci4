<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Laporan Laba/Rugi Penjualan</h5>
        <small class="text-muted">Perhitungan laba riil per unit fisik (harga jual aktual - harga beli)</small>
    </div>
    <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i> Cetak Laporan</button>
</div>

<div class="card mb-3 p-3">
    <form method="GET" action="/laporan/laba-rugi" class="row g-2 align-items-center">
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Dari Tanggal</label>
            <input type="date" name="mulai" class="form-control form-control-sm" value="<?= esc($mulai) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Sampai Tanggal</label>
            <input type="date" name="selesai" class="form-control form-control-sm" value="<?= esc($selesai) ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-filter me-1"></i> Terapkan Periode</button>
        </div>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Total Penjualan</small>
            <div class="fw-bold fs-5"><?= rupiah($totalJual) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Total Modal (HPP)</small>
            <div class="fw-bold fs-5 text-muted"><?= rupiah($totalBeli) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Laba Bersih Kotor</small>
            <div class="fw-bold fs-5 <?= $totalLaba >= 0 ? 'text-success' : 'text-danger' ?>">
                <?= rupiah($totalLaba) ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Rata-rata Margin</small>
            <div class="fw-bold fs-5 text-primary"><?= round($margin, 1) ?>%</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>No Nota</th>
                    <th>Barang</th>
                    <th>Unit / IMEI</th>
                    <th class="text-end">Harga Beli</th>
                    <th class="text-end">Harga Jual</th>
                    <th class="text-end">Laba</th>
                    <th class="text-end">Margin</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada transaksi penjualan pada periode ini.</td></tr>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= esc($r['tanggal']) ?></td>
                    <td><a href="/penjualan/detail/<?= (int) $r['penjualan_no'] ?>"><code><?= esc($r['penjualan_no']) ?></code></a></td>
                    <td class="fw-semibold"><?= esc($r['barang_nama']) ?></td>
                    <td><code><?= esc($r['unit_kode']) ?></code> <small class="text-muted"><?= esc($r['imei'] ?? '-') ?></small></td>
                    <td class="text-end text-muted"><?= rupiah($r['harga_beli']) ?></td>
                    <td class="text-end fw-semibold"><?= rupiah($r['harga_jual']) ?></td>
                    <td class="text-end fw-semibold <?= (float) $r['laba'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= rupiah($r['laba']) ?>
                    </td>
                    <td class="text-end small"><?= round((float) $r['laba_persen'], 1) ?>%</td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="4" class="text-end">Total Periode:</th>
                    <th class="text-end"><?= rupiah($totalBeli) ?></th>
                    <th class="text-end"><?= rupiah($totalJual) ?></th>
                    <th class="text-end text-success"><?= rupiah($totalLaba) ?></th>
                    <th class="text-end"><?= round($margin, 1) ?>%</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
