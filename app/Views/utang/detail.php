<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Detail Utang · <code><?= esc($utang['kode']) ?></code></h5>
        <small class="text-muted">Supplier: <?= esc($utang['supplier_nama']) ?> · Faktur: <?= esc($utang['pembelian_no']) ?></small>
    </div>
    <a href="/utang" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Total Utang</small>
            <div class="fw-bold fs-5"><?= rupiah($utang['nominal']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Sudah Dibayar</small>
            <div class="fw-bold fs-5 text-success"><?= rupiah($utang['terbayar']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Sisa Kewajiban</small>
            <div class="fw-bold fs-5 text-danger"><?= rupiah($utang['sisa']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted">Jatuh Tempo</small>
            <div class="fw-bold fs-5"><?= esc($utang['jatuh_tempo']) ?></div>
            <?php if ($utang['status'] === 'belum' && (int) $utang['hari_telat'] > 0): ?>
                <small class="text-danger fw-semibold">Terlambat <?= (int) $utang['hari_telat'] ?> hari</small>
            <?php else: ?>
                <small class="text-muted"><?= ucfirst($utang['status']) ?></small>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Form Bayar Cicilan (bila belum lunas) -->
    <?php if ($utang['status'] === 'belum' && (float) $utang['sisa'] > 0): ?>
    <div class="col-lg-4">
        <div class="card p-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-cash-coin me-1"></i> Catat Pembayaran / Cicilan</h6>
            <form method="POST" action="/utang/bayar/<?= (int) $utang['id'] ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tanggal Bayar</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nominal Bayar (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="nominal" class="form-control form-control-sm" step="1000" max="<?= (float) $utang['sisa'] ?>" value="<?= (float) $utang['sisa'] ?>" required>
                    <small class="text-muted">Maksimal: <?= rupiah($utang['sisa']) ?></small>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Metode</label>
                    <select name="metode" class="form-select form-select-sm">
                        <option value="transfer">Transfer Bank</option>
                        <option value="tunai">Tunai / Cash</option>
                        <option value="giro">Giro / Cek</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Bukti Transfer (opsional)</label>
                    <input type="file" name="bukti" class="form-control form-control-sm" accept="image/*">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan</label>
                    <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Cicilan ke-X, no referensi bank"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-check-lg me-1"></i> Simpan Pembayaran</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Riwayat Pembayaran -->
    <div class="<?= ($utang['status'] === 'belum' && (float) $utang['sisa'] > 0) ? 'col-lg-8' : 'col-12' ?>">
        <div class="card">
            <div class="card-header bg-white fw-bold">Riwayat Pembayaran & Cicilan</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center">Metode</th>
                            <th>Catatan</th>
                            <th class="text-center">Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($utang['pembayaran'])): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pembayaran.</td></tr>
                    <?php else: ?>
                        <?php foreach ($utang['pembayaran'] as $p): ?>
                        <tr>
                            <td><?= esc($p['tanggal']) ?></td>
                            <td class="text-end fw-semibold text-success"><?= rupiah($p['nominal']) ?></td>
                            <td class="text-center"><span class="badge badge-soft-secondary"><?= ucfirst(esc($p['metode'])) ?></span></td>
                            <td class="small"><?= esc($p['catatan'] ?? '-') ?></td>
                            <td class="text-center">
                                <?php if ($p['bukti']): ?>
                                    <a href="/uploads/bukti/<?= esc($p['bukti']) ?>" target="_blank" class="btn btn-sm btn-link"><i class="bi bi-paperclip"></i></a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
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
</div>

<?= $this->endSection() ?>
