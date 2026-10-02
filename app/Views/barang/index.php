<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Inventaris Barang Dagangan</h4>
        <small class="text-muted">Kelola stok barang, harga modal beli, harga jual, margin keuntungan, dan sumber kulak</small>
    </div>
    <a href="/barang/form" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Barang Baru
    </a>
</div>

<!-- Filter Card -->
<div class="card mb-3 p-3">
    <form method="GET" action="/barang" class="row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari nama, kode, toko, minus..." value="<?= esc($cari) ?>">
        </div>
        <div class="col-md-4">
            <select name="kategori" class="form-select form-select-sm">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= (int) $k['id'] ?>" <?= $kategoriId === (int) $k['id'] ? 'selected' : '' ?>>
                        <?= esc($k['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            <?php if ($cari || $kategoriId): ?>
                <a href="/barang" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:100px">Kode</th>
                    <th>Nama Barang & Kondisi / Minus</th>
                    <th>Kategori</th>
                    <th class="text-center" style="width:90px">Stok</th>
                    <th class="text-end">Harga Beli (Modal)</th>
                    <th class="text-end">Harga Jual</th>
                    <th class="text-center">Untung / Rugi</th>
                    <th>Beli Dari / Link</th>
                    <th class="text-end" style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($barang)): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada barang di inventaris.</td></tr>
            <?php else: ?>
                <?php foreach ($barang as $b): ?>
                <?php $margin = hitung_margin((float) $b['harga_beli'], (float) $b['harga_jual']); ?>
                <tr>
                    <td>
                        <span class="chip-imei" data-copy="<?= esc($b['kode_barang']) ?>" title="Klik untuk salin">
                            <?= esc($b['kode_barang']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold"><?= esc($b['nama_barang']) ?></div>
                        <?php if (! empty($b['minus_kondisi'])): ?>
                            <div class="small text-danger">
                                <i class="bi bi-exclamation-circle me-1"></i><strong>Minus:</strong> <?= esc($b['minus_kondisi']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (! empty($b['catatan'])): ?>
                            <div class="small text-muted">
                                <i class="bi bi-info-circle me-1"></i><?= esc($b['catatan']) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-soft-secondary"><?= esc($b['kategori_nama'] ?? 'Tanpa Kategori') ?></span>
                    </td>
                    <td class="text-center">
                        <?php if ((int) $b['stok'] <= 2): ?>
                            <span class="badge badge-soft-warning font-monospace fw-bold"><?= (int) $b['stok'] ?> <?= esc($b['satuan']) ?></span>
                        <?php else: ?>
                            <span class="badge badge-soft-success font-monospace fw-bold"><?= (int) $b['stok'] ?> <?= esc($b['satuan']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end font-monospace"><?= rupiah($b['harga_beli']) ?></td>
                    <td class="text-end font-monospace fw-semibold"><?= rupiah($b['harga_jual']) ?></td>
                    <td class="text-center">
                        <?= badge_margin((float) $b['harga_beli'], (float) $b['harga_jual']) ?>
                    </td>
                    <td>
                        <?php if (! empty($b['link_pembelian'])): ?>
                            <a href="<?= esc($b['link_pembelian']) ?>" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 small text-decoration-none" title="Buka tautan toko/marketplace">
                                <i class="bi bi-link-45deg me-1"></i><?= esc($b['sumber_toko'] ?: 'Link Toko') ?>
                            </a>
                        <?php elseif (! empty($b['sumber_toko'])): ?>
                            <span class="small text-muted"><i class="bi bi-shop me-1"></i><?= esc($b['sumber_toko']) ?></span>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="/barang/form/<?= (int) $b['id'] ?>" class="btn btn-outline-secondary" title="Edit Data"><i class="bi bi-pencil"></i></a>
                            <?php if (session()->get('user_role') === 'admin'): ?>
                            <form action="/barang/delete/<?= (int) $b['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus barang ini beserta riwayatnya?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
