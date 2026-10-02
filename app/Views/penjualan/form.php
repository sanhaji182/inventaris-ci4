<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<form method="POST" action="/penjualan/save" id="formPenjualan">
    <?= csrf_field() ?>
    <div class="row g-3">
        <!-- Kolom Kiri: Keranjang Kasir -->
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-cart3 me-1"></i> Keranjang Penjualan</span>
                    <small class="text-muted">Pilih barang di bawah untuk menambahkan unit fisik</small>
                </div>

                <!-- Pemilih Barang & Unit -->
                <div class="p-3 bg-light border-bottom">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">1. Pilih Barang</label>
                            <select id="pilihBarang" class="form-select form-select-sm" onchange="loadUnitTersedia()">
                                <option value="">-- Pilih Barang (Stok > 0) --</option>
                                <?php foreach ($barangList as $b): ?>
                                    <option value="<?= (int) $b['id'] ?>" data-harga="<?= (float) $b['harga_jual'] ?>" data-nama="<?= esc($b['nama']) ?>">
                                        <?= esc($b['nama']) ?> (sisa <?= (int) $b['stok_tersedia'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">2. Pilih Unit Fisik (IMEI)</label>
                            <select id="pilihUnit" class="form-select form-select-sm" disabled>
                                <option value="">-- Pilih barang dulu --</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-primary w-100" id="btnTambah" onclick="tambahUnitKeTabel()" disabled>
                                <i class="bi bi-plus-lg"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabel Unit Terpilih -->
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle" id="tabelKeranjang">
                        <thead class="table-light">
                            <tr>
                                <th>Barang</th>
                                <th>Unit (IMEI / Kondisi)</th>
                                <th class="text-end">Modal Beli</th>
                                <th class="text-end" style="width:160px">Harga Jual (Rp)</th>
                                <th class="text-end">Est. Laba</th>
                                <th style="width:40px"></th>
                            </tr>
                        </thead>
                        <tbody id="bodyKeranjang">
                            <tr id="emptyRow"><td colspan="6" class="text-center py-4 text-muted">Belum ada unit yang dipilih.</td></tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Belanja:</th>
                                <th class="text-end fs-6" id="labelTotal">Rp 0</th>
                                <th class="text-end fs-6 text-success" id="labelLaba">Rp 0</th>
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
                <h6 class="fw-bold mb-3"><i class="bi bi-cash me-1"></i> Pembayaran</h6>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">No. Nota</label>
                    <input type="text" name="no" class="form-control form-control-sm" value="<?= esc($nextNo) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Metode</label>
                    <select name="metode" id="metodeBayar" class="form-select form-select-sm" onchange="hitungKembalian()">
                        <option value="cash">Tunai / Cash</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Uang Dibayar (Rp)</label>
                    <input type="number" name="dibayar" id="inputDibayar" class="form-control form-control-sm" step="1000" value="0" required oninput="hitungKembalian()">
                </div>
                <div class="mb-3 p-2 bg-light rounded text-center">
                    <small class="text-muted d-block">Kembalian</small>
                    <span class="fs-5 fw-bold text-primary" id="labelKembalian">Rp 0</span>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan</label>
                    <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Nama pembeli, garansi personal, dll"></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100" id="btnSubmit" disabled>
                    <i class="bi bi-check2-circle me-1"></i> Selesaikan Transaksi
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
        selectUnit.innerHTML = '<option value="">-- Pilih barang dulu --</option>';
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
        <td class="fw-semibold">${namaBarang}</td>
        <td><code>${unit.kode}</code> <small class="text-muted">${unit.imei ? '· '+unit.imei : ''} (${unit.kondisi})</small></td>
        <td class="text-end text-muted">Rp ${parseInt(unit.harga_beli).toLocaleString('id-ID')}</td>
        <td>
            <input type="hidden" name="unit_id[]" value="${unit.id}">
            <input type="number" name="harga_jual[]" class="form-control form-control-sm text-end input-jual"
                   value="${acuanJual}" data-modal="${unit.harga_beli}" step="1000" required oninput="recalc()">
        </td>
        <td class="text-end fw-semibold col-laba text-success">Rp 0</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="hapusUnit(${unit.id})"><i class="bi bi-x-lg"></i></button>
        </td>
    `;
    tbody.appendChild(tr);

    recalc();
    loadUnitTersedia();
}

function hapusUnit(unitId) {
    unitTerpilih.delete(unitId);
    document.getElementById('row-unit-' + unitId)?.remove();

    if (unitTerpilih.size === 0) {
        document.getElementById('bodyKeranjang').innerHTML = '<tr id="emptyRow"><td colspan="6" class="text-center py-4 text-muted">Belum ada unit yang dipilih.</td></tr>';
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
        colLaba.className = 'text-end fw-semibold col-laba ' + (laba >= 0 ? 'text-success' : 'text-danger');
    });

    document.getElementById('labelTotal').innerText = 'Rp ' + Math.round(grandTotal).toLocaleString('id-ID');
    document.getElementById('labelLaba').innerText = 'Rp ' + Math.round(totalLaba).toLocaleString('id-ID');
    document.getElementById('labelLaba').className = 'text-end fs-6 ' + (totalLaba >= 0 ? 'text-success' : 'text-danger');

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
</script>
<?= $this->endSection() ?>
