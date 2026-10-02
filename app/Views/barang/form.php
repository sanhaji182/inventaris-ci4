<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0 fw-bold"><?= esc($title) ?></h4>
        <small class="text-muted">Lengkapi data barang, modal beli, estimasi jual, persentase keuntungan, dan sumber link pembelian</small>
    </div>
    <a href="/barang" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
    </a>
</div>

<div class="card p-4">
    <form method="POST" action="/barang/save" id="formBarang">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) ($barang['id'] ?? 0) ?>">

        <div class="row g-3">
            <!-- Kode & Nama Barang -->
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kode Barang <span class="text-danger">*</span></label>
                <input type="text" name="kode_barang" class="form-control font-monospace" value="<?= esc(old('kode_barang', $barang['kode_barang'] ?? $kodeOtomatis)) ?>" required>
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: iPhone 13 128GB Midnight" value="<?= esc(old('nama_barang', $barang['nama_barang'] ?? '')) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Kategori <span class="text-danger">*</span></label>
                <select name="kategori_id" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= (int) $k['id'] ?>" <?= (int) old('kategori_id', $barang['kategori_id'] ?? 0) === (int) $k['id'] ? 'selected' : '' ?>>
                            <?= esc($k['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Stok & Satuan -->
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Jumlah Stok Saat Ini <span class="text-danger">*</span></label>
                <input type="number" name="stok" class="form-control font-monospace" min="0" value="<?= esc(old('stok', $barang['stok'] ?? 1)) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Satuan <span class="text-danger">*</span></label>
                <input type="text" name="satuan" class="form-control" placeholder="Unit, Pcs, Box..." value="<?= esc(old('satuan', $barang['satuan'] ?? 'Unit')) ?>" required>
            </div>

            <!-- Modal Beli vs Harga Jual (Kalkulator Margin Real-time) -->
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Harga Beli / Modal (Rp) <span class="text-danger">*</span></label>
                <input type="number" id="inputHargaBeli" name="harga_beli" class="form-control font-monospace" step="1000" min="0" value="<?= esc(old('harga_beli', (int) ($barang['harga_beli'] ?? 0))) ?>" required oninput="hitungMarginPreview()">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Harga Jual (Rp) <span class="text-danger">*</span></label>
                <input type="number" id="inputHargaJual" name="harga_jual" class="form-control font-monospace fw-bold" step="1000" min="0" value="<?= esc(old('harga_jual', (int) ($barang['harga_jual'] ?? 0))) ?>" required oninput="hitungMarginPreview()">
            </div>

            <!-- Preview Card Margin Untung / Rugi -->
            <div class="col-12">
                <div class="p-3 rounded border bg-light d-flex flex-wrap align-items-center justify-content-between gap-3" id="boxMarginPreview">
                    <div>
                        <span class="small text-muted d-block text-uppercase fw-semibold" style="font-size:0.75rem;">Estimasi Margin Keuntungan / Kerugian</span>
                        <div class="fs-5 fw-bold font-monospace" id="previewNominal">Rp 0</div>
                    </div>
                    <div class="text-end">
                        <span class="small text-muted d-block text-uppercase fw-semibold" style="font-size:0.75rem;">Persentase Profit Terhadap Modal</span>
                        <span class="badge fs-6 font-monospace" id="previewPersen">0.0%</span>
                    </div>
                </div>
            </div>

            <!-- Sumber Toko & Link Pembelian -->
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Beli Dari Mana? (Nama Toko / Supplier)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shop"></i></span>
                    <input type="text" name="sumber_toko" class="form-control" placeholder="Contoh: Toko Berkah Mangga Dua, Grosir Official..." value="<?= esc(old('sumber_toko', $barang['sumber_toko'] ?? '')) ?>">
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Link Pembelian (Toko Online / Marketplace)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                    <input type="url" name="link_pembelian" class="form-control font-monospace small" placeholder="https://tokopedia.com/... atau https://shopee.co.id/..." value="<?= esc(old('link_pembelian', $barang['link_pembelian'] ?? '')) ?>">
                </div>
            </div>

            <!-- Kondisi Minus & Catatan Tambahan -->
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-danger">
                    <i class="bi bi-exclamation-triangle me-1"></i>Minus / Kondisi Fisik Barang
                </label>
                <textarea name="minus_kondisi" class="form-control" rows="3" placeholder="Contoh: Ada baret halus di layar, BH 85%, tombol volume agak keras, kelengkapan batangan..."><?= esc(old('minus_kondisi', $barang['minus_kondisi'] ?? '')) ?></textarea>
                <small class="text-muted">Kosongkan jika barang baru mulus / BNIB.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">
                    <i class="bi bi-journal-text me-1"></i>Catatan Tambahan
                </label>
                <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Garansi toko 30 hari, disimpan di rak etalase depan, titipan rekan..."><?= esc(old('catatan', $barang['catatan'] ?? '')) ?></textarea>
            </div>

            <div class="col-12 mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                <a href="/barang" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Data Barang
                </button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function hitungMarginPreview() {
    const beli = parseFloat(document.getElementById('inputHargaBeli').value) || 0;
    const jual = parseFloat(document.getElementById('inputHargaJual').value) || 0;
    const selisih = jual - beli;
    const persen = beli > 0 ? ((selisih / beli) * 100).toFixed(1) : 0;

    const box = document.getElementById('boxMarginPreview');
    const labelNominal = document.getElementById('previewNominal');
    const labelPersen = document.getElementById('previewPersen');

    if (selisih >= 0) {
        labelNominal.innerText = '+Rp ' + Math.round(selisih).toLocaleString('id-ID');
        labelNominal.className = 'fs-5 fw-bold font-monospace text-success';
        labelPersen.innerText = '+' + persen + '% (Untung)';
        labelPersen.className = 'badge fs-6 font-monospace badge-soft-success';
    } else {
        labelNominal.innerText = '-Rp ' + Math.round(Math.abs(selisih)).toLocaleString('id-ID');
        labelNominal.className = 'fs-5 fw-bold font-monospace text-danger';
        labelPersen.innerText = persen + '% (Rugi)';
        labelPersen.className = 'badge fs-6 font-monospace badge-soft-danger';
    }
}
document.addEventListener('DOMContentLoaded', hitungMarginPreview);
</script>
<?= $this->endSection() ?>
