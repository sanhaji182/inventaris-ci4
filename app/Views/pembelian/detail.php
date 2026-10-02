<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Detail Pembelian · <code><?= esc($trx['no']) ?></code></h5>
        <small class="text-muted">Tanggal: <?= esc($trx['tanggal']) ?> · Input oleh: <?= esc($trx['user_nama'] ?? '-') ?></small>
    </div>
    <a href="/pembelian" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Supplier</small>
            <div class="fw-bold"><?= esc($trx['supplier_nama'] ?? 'Non-Supplier') ?></div>
            <small class="text-muted"><?= esc($trx['sumber']) ?></small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Total Pembelian</small>
            <div class="fw-bold fs-5"><?= rupiah($trx['total']) ?></div>
            <small class="text-muted"><?= count($trx['items']) ?> unit</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Metode / Status</small>
            <div class="fw-bold"><?= ucfirst(esc($trx['metode'])) ?></div>
            <?php if ((float) $trx['sisa'] > 0): ?>
                <small class="text-danger">Sisa: <?= rupiah($trx['sisa']) ?></small>
            <?php else: ?>
                <small class="text-success">Lunas</small>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Status Utang</small>
            <?php if (! empty($trx['utang'])): ?>
                <div><a href="/utang/detail/<?= (int) $trx['utang']['id'] ?>"><code><?= esc($trx['utang']['kode']) ?></code></a></div>
                <small class="text-muted">Jatuh tempo: <?= esc($trx['utang']['jatuh_tempo']) ?></small>
            <?php else: ?>
                <div class="fw-bold text-muted">-</div>
                <small class="text-muted">Tanpa utang</small>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-bold">Unit Fisik yang Dihasilkan</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:100px">Kode Unit</th>
                    <th>Barang Katalog</th>
                    <th>IMEI / Serial</th>
                    <th>Kondisi</th>
                    <th class="text-end">Harga Beli</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($trx['items'] as $it): ?>
                <tr>
                    <td><code><?= esc($it['unit_kode']) ?></code></td>
                    <td class="fw-semibold"><?= esc($it['barang_nama']) ?></td>
                    <td><code><?= esc($it['imei'] ?? '-') ?></code></td>
                    <td><span class="badge badge-soft-secondary"><?= esc($it['kondisi']) ?></span></td>
                    <td class="text-end fw-semibold"><?= rupiah($it['harga_beli']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="4" class="text-end">Total:</th>
                    <th class="text-end"><?= rupiah($trx['total']) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
