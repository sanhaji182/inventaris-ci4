<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Daftar Utang Usaha</h5>
        <small class="text-muted">Kewajiban pembayaran ke supplier dan analisis umur utang</small>
    </div>
</div>

<div class="row g-3 mb-3">
    <?php foreach ($aging as $a): ?>
    <div class="col-6 col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="badge badge-soft-<?= esc($a['warna']) ?>"><?= esc($a['label']) ?></span>
                <small class="text-muted"><?= (int) $a['jumlah'] ?> inv</small>
            </div>
            <div class="fw-bold fs-6"><?= rupiah($a['total']) ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card mb-3 p-3">
    <div class="d-flex gap-2">
        <a href="/utang" class="btn btn-sm <?= empty($status) ? 'btn-primary' : 'btn-outline-secondary' ?>">Semua</a>
        <a href="/utang?status=belum" class="btn btn-sm <?= $status === 'belum' ? 'btn-primary' : 'btn-outline-secondary' ?>">Belum Lunas</a>
        <a href="/utang?status=lunas" class="btn btn-sm <?= $status === 'lunas' ? 'btn-primary' : 'btn-outline-secondary' ?>">Lunas</a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:130px">Kode Utang</th>
                    <th>Supplier</th>
                    <th>No Faktur</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-end">Nominal</th>
                    <th class="text-end">Terbayar</th>
                    <th class="text-end">Sisa</th>
                    <th class="text-center">Status</th>
                    <th class="text-end" style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($utang)): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada data utang.</td></tr>
            <?php else: ?>
                <?php foreach ($utang as $u): ?>
                <tr>
                    <td><a class="fw-semibold" href="/utang/detail/<?= (int) $u['id'] ?>"><code><?= esc($u['kode']) ?></code></a></td>
                    <td><?= esc($u['supplier_nama'] ?? '-') ?></td>
                    <td><a href="/pembelian/detail/<?= (int) $u['pembelian_id'] ?>"><code><?= esc($u['pembelian_no']) ?></code></a></td>
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
                    <td class="text-center">
                        <span class="badge <?= $u['status'] === 'lunas' ? 'badge-soft-success' : 'badge-soft-warning' ?>">
                            <?= ucfirst(esc($u['status'])) ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="/utang/detail/<?= (int) $u['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-wallet2 me-1"></i> Bayar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
