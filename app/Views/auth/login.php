<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · Sistem Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .login-card { max-width: 400px; width: 100%; }
    </style>
</head>
<body>
    <div class="login-card card p-4" style="border-radius:1rem">
        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px">
                <i class="bi bi-box-seam fs-4"></i>
            </div>
            <h4 class="mb-1 fw-bold">Sistem Inventaris</h4>
            <p class="text-muted small mb-0">Masuk untuk mengelola stok & transaksi</p>
        </div>

        <?php if ($flash = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2 small"><?= esc($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = session()->getFlashdata('sukses')): ?>
            <div class="alert alert-success py-2 small"><?= esc($flash) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Username</label>
                <input type="text" name="username" class="form-control" required autofocus
                       value="<?= esc(old('username')) ?>">
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </form>
    </div>
</body>
</html>
