<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="max-width: 680px">
    <div class="card-header bg-white fw-bold"><?= esc($title) ?></div>
    <form class="card-body" method="POST" action="/unit/save">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= esc($unit['id'] ?? '') ?>">

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kode Unit <span class="text-danger">*</span></label>
                <input type="text" name="kode" class="form-control" required
                       value="<?= esc($unit['kode'] ?? $nextKode) ?>">
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Barang Katalog <span class="text-danger">*</span></label>
                <select name="barang_id" class="form-select" required>
                    <option value="">Pilih Barang</option>
                    <?php foreach ($barang as $b): ?>
                        <option value="<?= (int) $b['id'] ?>" <?= ((int) ($unit['barang_id'] ?? 0) === (int) $b['id']) ? 'selected' : '' ?>>
                            <?= esc($b['kode']) ?> · <?= esc($b['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">IMEI / Serial Number</label>
                <input type="text" name="imei" class="form-control" placeholder="15 digit IMEI atau serial"
                       value="<?= esc($unit['imei'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Kondisi</label>
                <select name="kondisi" class="form-select">
                    <?php foreach (['mulus', 'lecet', 'minus', 'batangan', 'fullset'] as $k): ?>
                        <option value="<?= $k ?>" <?= ($unit['kondisi'] ?? 'mulus') === $k ? 'selected' : '' ?>><?= ucfirst($k) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Harga Beli (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="harga_beli" class="form-control" required step="1000"
                       value="<?= esc($unit['harga_beli'] ?? 0) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Supplier</label>
                <select name="supplier_id" class="form-select">
                    <option value="">-- Non Supplier --</option>
                    <?php foreach ($supplier as $s): ?>
                        <option value="<?= (int) $s['id'] ?>" <?= ((int) ($unit['supplier_id'] ?? 0) === (int) $s['id']) ? 'selected' : '' ?>>
                            <?= esc($s['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tanggal Masuk</label>
                <input type="date" name="tanggal_masuk" class="form-control"
                       value="<?= esc($unit['tanggal_masuk'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['tersedia', 'rusak', 'hilang'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($unit['status'] ?? 'tersedia') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Lokasi Simpan</label>
                <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Rak A1"
                       value="<?= esc($unit['lokasi'] ?? 'Gudang Utama') ?>">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2"><?= esc($unit['catatan'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="/unit" class="btn btn-sm btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
