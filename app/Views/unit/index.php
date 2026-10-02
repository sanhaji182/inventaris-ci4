<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Unit Stok Fisik</h5>
        <small class="text-muted">Pelacakan tiap device unik berdasarkan IMEI/Serial & kondisi</small>
    </div>
    <div class="d-flex gap-2">
        <a href="/pembelian/form" class="btn btn-primary btn-sm"><i class="bi bi-cart-plus me-1"></i> Masuk via Pembelian</a>
        <a href="/unit/form" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Manual</a>
    </div>
</div>

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
                <option value="">Semua Barang</option>
                <?php foreach ($barang as $b): ?>
                    <option value="<?= (int) $b['id'] ?>" <?= ((int) ($filter['barang_id'] ?? 0) === (int) $b['id']) ? 'selected' : '' ?>>
                        <?= esc($b['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            <?php if (! empty(array_filter($filter))): ?>
                <a href="/unit" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:90px">Kode</th>
                    <th>Barang</th>
                    <th>IMEI / Serial</th>
                    <th>Kondisi</th>
                    <th class="text-end">Harga Beli</th>
                    <th>Tgl Masuk</th>
                    <th>Lokasi</th>
                    <th class="text-center" style="width:100px">Status</th>
                    <th class="text-end" style="width:130px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($units)): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada unit yang sesuai filter.</td></tr>
            <?php else: ?>
                <?php foreach ($units as $u): ?>
                <tr>
                    <td><code><?= esc($u['kode']) ?></code></td>
                    <td>
                        <div class="fw-semibold"><?= esc($u['barang_nama']) ?></div>
                        <small class="text-muted"><?= esc($u['kategori_nama']) ?></small>
                    </td>
                    <td><code><?= esc($u['imei'] ?? '-') ?></code></td>
                    <td><span class="badge badge-soft-secondary"><?= esc($u['kondisi']) ?></span></td>
                    <td class="text-end fw-semibold"><?= rupiah($u['harga_beli']) ?></td>
                    <td><?= esc($u['tanggal_masuk']) ?></td>
                    <td class="small"><?= esc($u['lokasi'] ?? '-') ?></td>
                    <td class="text-center"><span class="badge <?= badge_unit($u['status']) ?>"><?= esc($u['status']) ?></span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-secondary" onclick='ubahStatus(<?= json_encode($u) ?>)' title="Ubah status"><i class="bi bi-arrow-repeat"></i></button>
                        <a href="/unit/form/<?= (int) $u['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <?php if ($u['status'] !== 'terjual'): ?>
                        <form action="/unit/delete/<?= (int) $u['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus unit ini?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
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
                <h6 class="modal-title fw-bold">Ubah Status Unit: <span id="statusKode"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Status Baru</label>
                    <select name="status" id="statusVal" class="form-select">
                        <option value="tersedia">Tersedia</option>
                        <option value="rusak">Rusak</option>
                        <option value="hilang">Hilang</option>
                    </select>
                    <small class="text-muted">Status 'terjual' diubah otomatis melalui modul kasir/penjualan.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan / Alasan</label>
                    <textarea name="catatan" id="statusCatatan" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
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
