<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Kasir & Penjualan Unit</h4>
        <small class="text-muted">Pilih unit fisik spesifik berdasarkan IMEI untuk menghitung laba kotor riil per item</small>
    </div>
    <a href="/penjualan" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Riwayat Penjualan
    </a>
</div>

<form method="POST" action="/penjualan/save" id="formPenjualan">
    <?= csrf_field() ?>
    <div class="row g-3">
        <!-- Kolom Kiri: Keranjang Kasir -->
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-cart3 me-1 text-primary"></i> Keranjang Penjualan Kasir</span>
                    <span class="badge badge-soft-primary" id="badgeItemCount">0 Unit Terpilih</span>
                </div>

                <!-- Pemilih Barang & Unit -->
                <div class="p-3 bg-body-tertiary border-bottom">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">1. Pilih Model Barang</label>
                            <select id="pilihBarang" class="form-select form-select-sm" onchange="loadUnitTersedia()">
                                <option value="">-- Pilih Model Perangkat --</option>
                                <?php foreach ($barangList as $b): ?>
                                    <option value="<?= (int) $b['id'] ?>" data-harga="<?= (float) $b['harga_jual'] ?>" data-nama="<?= esc($b['nama']) ?>">
                                        <?= esc($b['nama']) ?> (Ready: <?= (int) $b['stok_tersedia'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">2. Pilih Unit Fisik (IMEI / Kondisi)</label>
                            <select id="pilihUnit" class="form-select form-select-sm" disabled>
                                <option value="">-- Pilih model barang dulu --</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-primary w-100" id="btnTambah" onclick="tambahUnitKeTabel()" disabled>
                                <i class="bi bi-plus-circle me-1"></i> Masukkan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabel Unit Terpilih -->
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="tabelKeranjang">
                        <thead>
                            <tr>
                                <th>Perangkat</th>
                                <th>Identitas Unit</th>
                                <th class="text-end">Modal Beli</th>
                                <th class="text-end" style="width:170px">Harga Jual Akhir</th>
                                <th class="text-end">Laba Riil</th>
                                <th style="width:40px"></th>
                            </tr>
                        </thead>
                        <tbody id="bodyKeranjang">
                            <tr id="emptyRow">
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-cart-x fs-2 d-block mb-2 text-secondary"></i>
                                    <span>Belum ada unit fisik yang dimasukkan ke keranjang kasir.</span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Penjualan:</th>
                                <th class="text-end font-monospace fs-6" id="labelTotal">Rp 0</th>
                                <th class="text-end font-monospace fs-6 text-success" id="labelLaba">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pembayaran -->
        <div class="col-lg-4">
            <div class="card p-3">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-credit-card me-1 text-success"></i> Rincian Pembayaran</h6>
                    <span class="badge-dot badge-soft-success">Kasir Aktif</span>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">No. Nota / Faktur</label>
                    <input type="text" name="no" class="form-control form-control-sm font-monospace" value="<?= esc($nextNo) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Metode Pembayaran</label>
                    <select name="metode" id="metodeBayar" class="form-select form-select-sm" onchange="hitungKembalian()">
                        <option value="cash">Tunai / Cash</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS / Digital Pay</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Uang Diterima / Dibayar (Rp)</label>
                    <input type="number" name="dibayar" id="inputDibayar" class="form-control form-control-sm text-end font-monospace fw-bold" step="1000" value="0" required oninput="hitungKembalian()">
                </div>

                <!-- Quick Cash Helpers -->
                <div class="d-flex gap-1 mb-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill py-1" style="font-size:0.75rem;" onclick="setUangPas()">
                        Uang Pas
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill py-1" style="font-size:0.75rem;" onclick="tambahUang(50000)">
                        +50k
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill py-1" style="font-size:0.75rem;" onclick="tambahUang(100000)">
                        +100k
                    </button>
                </div>

                <div class="mb-3 p-3 rounded text-center border" style="background: var(--surface-alt);">
                    <small class="text-muted d-block text-uppercase fw-semibold" style="font-size:0.7rem; letter-spacing:0.05em;">Kembalian Pelanggan</small>
                    <span class="fs-4 fw-bold font-monospace text-primary" id="labelKembalian">Rp 0</span>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan Transaksi</label>
                    <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Nama pelanggan, garansi toko, dll"></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2" id="btnSubmit" disabled>
                    <i class="bi bi-check-circle me-1"></i> Selesaikan Transaksi
                </button>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let unitCache = [];
let unitTerpilih = new Set();
let grandTotal = 0;

function loadUnitTersedia() {
    const barangId = document.getElementById('pilihBarang').value;
    const selectUnit = document.getElementById('pilihUnit');
    const btnTambah = document.getElementById('btnTambah');

    if (!barangId) {
        selectUnit.innerHTML = '<option value="">-- Pilih model barang dulu --</option>';
        selectUnit.disabled = true;
        btnTambah.disabled = true;
        return;
    }

    fetch('/unit/tersedia/' + barangId)
        .then(r => r.json())
        .then(data => {
            unitCache = data;
            selectUnit.innerHTML = '<option value="">-- Pilih Unit Fisik --</option>';
            data.forEach(u => {
                if (!unitTerpilih.has(u.id)) {
                    selectUnit.innerHTML += `<option value="${u.id}">${u.kode} · ${u.imei ? 'IMEI: '+u.imei : 'Non-IMEI'} (${u.kondisi}) - Modal: Rp ${parseInt(u.harga_beli).toLocaleString('id-ID')}</option>`;
                }
            });
            selectUnit.disabled = false;
            btnTambah.disabled = false;
        });
}

function tambahUnitKeTabel() {
    const unitId = parseInt(document.getElementById('pilihUnit').value);
    if (!unitId || unitTerpilih.has(unitId)) return;

    const unit = unitCache.find(u => parseInt(u.id) === unitId);
    if (!unit) return;

    const barangOpt = document.getElementById('pilihBarang').selectedOptions[0];
    const namaBarang = barangOpt.dataset.nama;
    const acuanJual = parseFloat(barangOpt.dataset.harga) || 0;

    unitTerpilih.add(unitId);
    document.getElementById('emptyRow')?.remove();

    const tbody = document.getElementById('bodyKeranjang');
    const tr = document.createElement('tr');
    tr.id = 'row-unit-' + unitId;
    tr.innerHTML = `
        <td><strong class="small">${namaBarang}</strong></td>
        <td>
            <span class="chip-imei">${unit.kode}</span>
            <div class="small text-muted">${unit.imei ? 'IMEI: '+unit.imei : 'Non-IMEI'} (${unit.kondisi})</div>
        </td>
        <td class="text-end font-monospace text-muted">Rp ${parseInt(unit.harga_beli).toLocaleString('id-ID')}</td>
        <td>
            <input type="hidden" name="unit_id[]" value="${unit.id}">
            <input type="number" name="harga_jual[]" class="form-control form-control-sm text-end font-monospace input-jual"
                   value="${acuanJual}" data-modal="${unit.harga_beli}" step="1000" required oninput="recalc()">
        </td>
        <td class="text-end font-monospace fw-semibold col-laba text-success">Rp 0</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="hapusUnit(${unit.id})"><i class="bi bi-x-circle fs-5"></i></button>
        </td>
    `;
    tbody.appendChild(tr);

    recalc();
    loadUnitTersedia();
    window.showToast(`Unit ${unit.kode} ditambahkan ke keranjang`, 'success');
}

function hapusUnit(unitId) {
    unitTerpilih.delete(unitId);
    document.getElementById('row-unit-' + unitId)?.remove();

    if (unitTerpilih.size === 0) {
        document.getElementById('bodyKeranjang').innerHTML = `
            <tr id="emptyRow">
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-cart-x fs-2 d-block mb-2 text-secondary"></i>
                    <span>Belum ada unit fisik yang dimasukkan ke keranjang kasir.</span>
                </td>
            </tr>
        `;
    }
    recalc();
    loadUnitTersedia();
}

function recalc() {
    grandTotal = 0;
    let totalLaba = 0;

    document.querySelectorAll('#bodyKeranjang tr:not(#emptyRow)').forEach(tr => {
        const inp = tr.querySelector('.input-jual');
        const modal = parseFloat(inp.dataset.modal) || 0;
        const jual = parseFloat(inp.value) || 0;
        const laba = jual - modal;

        grandTotal += jual;
        totalLaba += laba;

        const colLaba = tr.querySelector('.col-laba');
        colLaba.innerText = 'Rp ' + Math.round(laba).toLocaleString('id-ID');
        colLaba.className = 'text-end font-monospace fw-semibold col-laba ' + (laba >= 0 ? 'text-success' : 'text-danger');
    });

    document.getElementById('labelTotal').innerText = 'Rp ' + Math.round(grandTotal).toLocaleString('id-ID');
    document.getElementById('labelLaba').innerText = 'Rp ' + Math.round(totalLaba).toLocaleString('id-ID');
    document.getElementById('labelLaba').className = 'text-end font-monospace fs-6 ' + (totalLaba >= 0 ? 'text-success' : 'text-danger');

    document.getElementById('badgeItemCount').innerText = `${unitTerpilih.size} Unit Terpilih`;

    // Auto set default dibayar jika metode non-cash
    const metode = document.getElementById('metodeBayar').value;
    if (metode !== 'cash') {
        document.getElementById('inputDibayar').value = grandTotal;
    }

    hitungKembalian();
    document.getElementById('btnSubmit').disabled = (unitTerpilih.size === 0);
}

function hitungKembalian() {
    const dibayar = parseFloat(document.getElementById('inputDibayar').value) || 0;
    const kembalian = Math.max(0, dibayar - grandTotal);
    document.getElementById('labelKembalian').innerText = 'Rp ' + Math.round(kembalian).toLocaleString('id-ID');
}

function setUangPas() {
    document.getElementById('inputDibayar').value = grandTotal;
    hitungKembalian();
}

function tambahUang(nominal) {
    const current = parseFloat(document.getElementById('inputDibayar').value) || 0;
    document.getElementById('inputDibayar').value = current + nominal;
    hitungKembalian();
}
</script>
<?= $this->endSection() ?>
