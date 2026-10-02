<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold"><?= esc($title) ?></h5>
        <small class="text-muted">Kategori: <?= esc($barang['kategori_nama']) ?> · Harga acuan: <?= rupiah($barang['harga_jual']) ?></small>
    </div>
    <a href="/barang" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kode Unit</th>
                    <th>IMEI / Serial</th>
                    <th>Kondisi</th>
                    <th>Tgl Masuk</th>
                    <th class="text-end">Harga Beli</th>
                    <th>Supplier</th>
                    <th class="text-center">Status</th>
                    <th>Penjualan</th>
                    <th class="text-end">Laba</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($units)): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada unit fisik untuk barang ini.</td></tr>
            <?php else: ?>
                <?php foreach ($units as $u): ?>
                <tr>
                    <td><code><?= esc($u['kode']) ?></code></td>
                    <td><code><?= esc($u['imei'] ?? '-') ?></code></td>
                    <td><span class="badge badge-soft-secondary"><?= esc($u['kondisi']) ?></span></td>
                    <td><?= esc($u['tanggal_masuk']) ?></td>
                    <td class="text-end"><?= rupiah($u['harga_beli']) ?></td>
                    <td class="small"><?= esc($u['supplier_nama'] ?? '-') ?></td>
                    <td class="text-center"><span class="badge <?= badge_unit($u['status']) ?>"><?= esc($u['status']) ?></span></td>
                    <td>
                        <?php if ($u['penjualan_no']): ?>
                            <a href="/penjualan/detail/<?= (int) $u['pembelian_id'] ?>"><?= esc($u['penjualan_no']) ?></a>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <?php if ($u['status'] === 'terjual'): ?>
                            <span class="fw-semibold <?= (float) $u['laba'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= rupiah($u['laba']) ?>
                            </span>
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

<?= $this->endSection() ?>
