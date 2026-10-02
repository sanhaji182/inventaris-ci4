<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="max-width: 600px">
    <div class="card-header bg-white fw-bold"><?= esc($title) ?></div>
    <form class="card-body" method="POST" action="/supplier/save">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= esc($supplier['id'] ?? '') ?>">

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kode</label>
                <input type="text" name="kode" class="form-control" placeholder="Auto"
                       value="<?= esc($supplier['kode'] ?? '') ?>">
                <small class="text-muted">Kosongkan untuk otomatis</small>
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Nama Supplier <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" required
                       value="<?= esc($supplier['nama'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">No. Telepon</label>
                <input type="text" name="telepon" class="form-control"
                       value="<?= esc($supplier['telepon'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2"><?= esc($supplier['alamat'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2"><?= esc($supplier['catatan'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="/supplier" class="btn btn-sm btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
