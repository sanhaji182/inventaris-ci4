<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Nota Penjualan · <code><?= esc($trx['no']) ?></code></h5>
        <small class="text-muted">Tanggal: <?= esc($trx['tanggal']) ?> · Kasir: <?= esc($trx['user_nama'] ?? '-') ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="/penjualan" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
        <button class="btn btn-sm btn-outline-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i> Cetak Nota</button>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Total Belanja</small>
            <div class="fw-bold fs-5"><?= rupiah($trx['total']) ?></div>
            <small class="text-muted"><?= count($trx['items']) ?> unit</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Metode Pembayaran</small>
            <div class="fw-bold fs-5"><?= strtoupper(esc($trx['metode'])) ?></div>
            <small class="text-muted">Dibayar: <?= rupiah($trx['dibayar']) ?></small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Kembalian</small>
            <div class="fw-bold fs-5 text-primary"><?= rupiah($trx['kembalian']) ?></div>
            <small class="text-muted">Lunas</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Total Laba Kotor</small>
            <?php $totalLaba = array_sum(array_column($trx['items'], 'laba')); ?>
            <div class="fw-bold fs-5 <?= $totalLaba >= 0 ? 'text-success' : 'text-danger' ?>">
                <?= rupiah($totalLaba) ?>
            </div>
            <small class="text-muted"><?= $trx['total'] > 0 ? round(($totalLaba / $trx['total']) * 100, 1) : 0 ?>% margin</small>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-bold">Rincian Unit yang Terjual</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:100px">Kode Unit</th>
                    <th>Barang Katalog</th>
                    <th>IMEI / Serial</th>
                    <th>Kondisi</th>
                    <th class="text-end">Harga Beli</th>
                    <th class="text-end">Harga Jual</th>
                    <th class="text-end">Laba</th>
                    <th class="text-end">Margin</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($trx['items'] as $it): ?>
                <tr>
                    <td><code><?= esc($it['unit_kode']) ?></code></td>
                    <td class="fw-semibold"><?= esc($it['barang_nama']) ?></td>
                    <td><code><?= esc($it['imei'] ?? '-') ?></code></td>
                    <td><span class="badge badge-soft-secondary"><?= esc($it['kondisi']) ?></span></td>
                    <td class="text-end text-muted"><?= rupiah($it['harga_beli']) ?></td>
                    <td class="text-end fw-semibold"><?= rupiah($it['harga_jual']) ?></td>
                    <td class="text-end fw-semibold <?= (float) $it['laba'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= rupiah($it['laba']) ?>
                    </td>
                    <td class="text-end small"><?= round((float) $it['laba_persen'], 1) ?>%</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="5" class="text-end">Total:</th>
                    <th class="text-end"><?= rupiah($trx['total']) ?></th>
                    <th class="text-end text-success"><?= rupiah($totalLaba) ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
