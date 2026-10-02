<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Manajemen Utang & Pelunasan Termin</h4>
        <small class="text-muted">Kewajiban pembayaran ke pemasok (supplier), pemantauan jatuh tempo dan pencatatan cicilan</small>
    </div>
</div>

<!-- Bento Aging Grid -->
<div class="row g-3 mb-3">
    <?php foreach ($aging as $a): ?>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge-dot badge-soft-<?= esc($a['warna']) ?>"><?= esc($a['label']) ?></span>
                <span class="small text-muted fw-semibold"><?= (int) $a['jumlah'] ?> Faktur</span>
            </div>
            <div class="value fs-5 font-monospace"><?= rupiah($a['total']) ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Status Switcher & Filter -->
<div class="card mb-3 p-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="btn-group btn-group-sm" role="group">
            <a href="/utang" class="btn <?= empty($status) ? 'btn-primary' : 'btn-outline-secondary' ?>">Semua Status</a>
            <a href="/utang?status=belum" class="btn <?= $status === 'belum' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <i class="bi bi-clock-history me-1"></i> Belum Lunas
            </a>
            <a href="/utang?status=lunas" class="btn <?= $status === 'lunas' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <i class="bi bi-check2-circle me-1"></i> Lunas
            </a>
        </div>
        <div class="table-search-input" style="max-width: 240px;">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Cari kode/supplier..." data-table-search="#tableUtangFull">
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tableUtangFull">
            <thead>
                <tr>
                    <th style="width:140px">Kode Utang</th>
                    <th>Supplier</th>
                    <th>No Faktur Beli</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-end">Nominal</th>
                    <th class="text-end">Terbayar</th>
                    <th class="text-end">Sisa Kewajiban</th>
                    <th class="text-center" style="width:110px">Status</th>
                    <th class="text-end" style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($utang)): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada data utang yang sesuai filter.</td></tr>
            <?php else: ?>
                <?php foreach ($utang as $u): ?>
                <tr>
                    <td>
                        <a href="/utang/detail/<?= (int) $u['id'] ?>" class="chip-imei" data-copy="<?= esc($u['kode']) ?>">
                            <i class="bi bi-hash"></i>
                            <?= esc($u['kode']) ?>
                        </a>
                    </td>
                    <td><strong><?= esc($u['supplier_nama'] ?? '-') ?></strong></td>
                    <td>
                        <a href="/pembelian/detail/<?= (int) $u['pembelian_id'] ?>" class="chip-imei">
                            <i class="bi bi-receipt"></i>
                            <?= esc($u['pembelian_no']) ?>
                        </a>
                    </td>
                    <td>
                        <div class="small fw-semibold"><?= esc($u['jatuh_tempo']) ?></div>
                        <?php if ($u['status'] === 'belum' && (int) $u['hari_telat'] > 0): ?>
                            <span class="badge-dot badge-soft-danger pulse" style="font-size:0.7rem;">
                                Telat <?= (int) $u['hari_telat'] ?>h
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end font-monospace"><?= rupiah($u['nominal']) ?></td>
                    <td class="text-end font-monospace text-muted"><?= rupiah($u['terbayar']) ?></td>
                    <td class="text-end font-monospace fw-bold <?= (float) $u['sisa'] > 0 ? 'text-danger' : 'text-success' ?>">
                        <?= rupiah($u['sisa']) ?>
                    </td>
                    <td class="text-center">
                        <span class="badge-dot <?= $u['status'] === 'lunas' ? 'badge-soft-success' : 'badge-soft-warning' ?>">
                            <?= $u['status'] === 'lunas' ? 'Lunas' : 'Belum Lunas' ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="/utang/detail/<?= (int) $u['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-wallet2 me-1"></i> Rincian
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
