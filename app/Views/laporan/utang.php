<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Laporan Utang Usaha & Aging</h5>
        <small class="text-muted">Analisis risiko jatuh tempo pembayaran ke supplier</small>
    </div>
    <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i> Cetak</button>
</div>

<div class="row g-3 mb-3">
    <?php foreach ($aging as $a): ?>
    <div class="col-6 col-md-3">
        <div class="card p-3">
            <span class="badge badge-soft-<?= esc($a['warna']) ?> mb-2"><?= esc($a['label']) ?></span>
            <div class="fw-bold fs-5"><?= rupiah($a['total']) ?></div>
            <small class="text-muted"><?= (int) $a['jumlah'] ?> tagihan</small>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Supplier</th>
                    <th>Faktur</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-end">Nominal</th>
                    <th class="text-end">Terbayar</th>
                    <th class="text-end">Sisa</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($utang as $u): ?>
                <tr>
                    <td><code><?= esc($u['kode']) ?></code></td>
                    <td><?= esc($u['supplier_nama']) ?></td>
                    <td><code><?= esc($u['pembelian_no']) ?></code></td>
                    <td>
                        <?= esc($u['jatuh_tempo']) ?>
                        <?php if ($u['status'] === 'belum' && (int) $u['hari_telat'] > 0): ?>
                            <span class="badge badge-soft-danger">telat <?= (int) $u['hari_telat'] ?>h</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end"><?= rupiah($u['nominal']) ?></td>
                    <td class="text-end text-muted"><?= rupiah($u['terbayar']) ?></td>
                    <td class="text-end fw-semibold <?= (float) $u['sisa'] > 0 ? 'text-danger' : 'text-success' ?>">
                        <?= rupiah($u['sisa']) ?>
                    </td>
                    <td class="text-center"><span class="badge <?= $u['status'] === 'lunas' ? 'badge-soft-success' : 'badge-soft-warning' ?>"><?= ucfirst(esc($u['status'])) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
