<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="max-width: 720px">
    <div class="card-header bg-white fw-bold"><?= esc($title) ?></div>
    <form class="card-body" method="POST" action="/barang/save" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= esc($barang['id'] ?? '') ?>">

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kode Barang <span class="text-danger">*</span></label>
                <input type="text" name="kode" class="form-control" required
                       value="<?= esc($barang['kode'] ?? $nextKode) ?>">
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" required placeholder="Contoh: Samsung Galaxy A15"
                       value="<?= esc($barang['nama'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Kategori <span class="text-danger">*</span></label>
                <select name="kategori_id" class="form-select" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= (int) $k['id'] ?>" <?= (isset($barang['kategori_id']) && (int) $barang['kategori_id'] === (int) $k['id']) ? 'selected' : '' ?>>
                            <?= esc($k['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Merek</label>
                <input type="text" name="merek" class="form-control" placeholder="Contoh: Samsung, Apple"
                       value="<?= esc($barang['merek'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Harga Jual Acuan (Rp)</label>
                <input type="number" name="harga_jual" class="form-control" step="1000"
                       value="<?= esc($barang['harga_jual'] ?? 0) ?>">
                <small class="text-muted">Bisa disesuaikan per unit saat kasir</small>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Stok Minimum (Alert)</label>
                <input type="number" name="stok_min" class="form-control" min="0"
                       value="<?= esc($barang['stok_min'] ?? 2) ?>">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Spesifikasi Singkat</label>
                <textarea name="spek" class="form-control" rows="2" placeholder="RAM, memori, warna, dll"><?= esc($barang['spek'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Foto Produk (opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <?php if (! empty($barang['foto'])): ?>
                    <small class="text-muted">Foto saat ini: <?= esc($barang['foto']) ?></small>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="/barang" class="btn btn-sm btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
