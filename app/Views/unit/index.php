<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Unit Fisik & Pelacakan IMEI</h4>
        <small class="text-muted">Setiap device dicatat secara unik dengan nomor serial, kondisi fisik, dan harga modal spesifik</small>
    </div>
    <div class="d-flex gap-2">
        <a href="/pembelian/form" class="btn btn-primary btn-sm">
            <i class="bi bi-cart-plus me-1"></i> Kulak Masuk
        </a>
        <a href="/unit/form" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Manual
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-3 p-3">
    <form method="GET" action="/unit" class="row g-2 align-items-center">
        <div class="col-md-3">
            <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari kode, IMEI, nama..." value="<?= esc($filter['cari'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <?php foreach (['tersedia', 'terjual', 'rusak', 'hilang'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($filter['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="barang" class="form-select form-select-sm">
                <option value="">Semua Model Barang</option>
                <?php foreach ($barang as $b): ?>
                    <option value="<?= (int) $b['id'] ?>" <?= ((int) ($filter['barang_id'] ?? 0) === (int) $b['id']) ? 'selected' : '' ?>>
                        <?= esc($b['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel me-1"></i> Terapkan Filter</button>
            <?php if (! empty(array_filter($filter))): ?>
                <a href="/unit" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card">
    <div class="table-filter-bar">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-phone text-primary fs-5"></i>
            <span class="fw-bold">Daftar Unit Tercatat</span>
            <span class="badge badge-soft-primary ms-1" data-table-count="#tableUnits"><?= count($units) ?> baris</span>
        </div>
        <div class="table-search-input">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Ketik untuk filter cepat..." data-table-search="#tableUnits">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tableUnits">
            <thead>
                <tr>
                    <th style="width:100px">Kode Unit</th>
                    <th>Model Perangkat</th>
                    <th>IMEI / Serial Number</th>
                    <th>Kondisi Fisik</th>
                    <th class="text-end">Harga Modal</th>
                    <th>Tgl Masuk</th>
                    <th>Lokasi Rak</th>
                    <th class="text-center" style="width:110px">Status</th>
                    <th class="text-end" style="width:130px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($units)): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada unit yang sesuai filter.</td></tr>
            <?php else: ?>
                <?php foreach ($units as $u): ?>
                <tr>
                    <td>
                        <span class="chip-imei" data-copy="<?= esc($u['kode']) ?>" title="Klik untuk menyalin">
                            <i class="bi bi-clipboard"></i>
                            <?= esc($u['kode']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold"><?= esc($u['barang_nama']) ?></div>
                        <span class="badge badge-soft-secondary" style="font-size:0.7rem;"><?= esc($u['kategori_nama']) ?></span>
                    </td>
                    <td>
                        <?php if (! empty($u['imei'])): ?>
                            <span class="chip-imei" data-copy="<?= esc($u['imei']) ?>" title="Klik untuk menyalin IMEI">
                                <i class="bi bi-upc-scan text-muted"></i>
                                <?= esc($u['imei']) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-soft-info"><?= esc(ucfirst($u['kondisi'])) ?></span>
                    </td>
                    <td class="text-end font-monospace fw-semibold"><?= rupiah($u['harga_beli']) ?></td>
                    <td><span class="small text-muted"><?= esc($u['tanggal_masuk']) ?></span></td>
                    <td><span class="badge badge-soft-secondary"><?= esc($u['lokasi'] ?? 'Rak Utama') ?></span></td>
                    <td class="text-center">
                        <?php
                            $stMap = [
                                'tersedia' => ['class' => 'badge-soft-success pulse', 'label' => 'Tersedia'],
                                'terjual'  => ['class' => 'badge-soft-primary', 'label' => 'Terjual'],
                                'rusak'    => ['class' => 'badge-soft-danger', 'label' => 'Rusak'],
                                'hilang'   => ['class' => 'badge-soft-secondary', 'label' => 'Hilang'],
                            ];
                            $st = $stMap[$u['status']] ?? ['class' => 'badge-soft-secondary', 'label' => $u['status']];
                        ?>
                        <span class="badge-dot <?= $st['class'] ?>"><?= $st['label'] ?></span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" onclick='ubahStatus(<?= json_encode($u) ?>)' title="Ubah status fisik"><i class="bi bi-arrow-repeat"></i></button>
                            <a href="/unit/form/<?= (int) $u['id'] ?>" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <?php if ($u['status'] !== 'terjual'): ?>
                            <form action="/unit/delete/<?= (int) $u['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus unit ini dari database?')">
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

<!-- Modal Ubah Status Unit -->
<div class="modal fade" id="modalStatus" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" id="formStatus">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Ubah Status Fisik Unit: <span id="statusKode" class="chip-imei"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Status Fisik Baru</label>
                    <select name="status" id="statusVal" class="form-select">
                        <option value="tersedia">Tersedia (Siap Jual)</option>
                        <option value="rusak">Rusak / Masuk Servis</option>
                        <option value="hilang">Hilang / Selisih Stok</option>
                    </select>
                    <small class="text-muted d-block mt-1">Status 'terjual' akan diubah otomatis saat transaksi di kasir penjualan.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Alasan Perubahan Status</label>
                    <textarea name="catatan" id="statusCatatan" class="form-control" rows="2" placeholder="Tulis alasan jika rusak atau hilang..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Simpan Status</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function ubahStatus(u) {
    document.getElementById('statusKode').innerText = u.kode;
    document.getElementById('formStatus').action = '/unit/status/' + u.id;
    document.getElementById('statusVal').value = u.status === 'terjual' ? 'tersedia' : u.status;
    document.getElementById('statusCatatan').value = u.catatan || '';
    new bootstrap.Modal(document.getElementById('modalStatus')).show();
}
</script>
<?= $this->endSection() ?>
