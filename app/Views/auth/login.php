<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · Sistem Inventaris & Tracking Unit</title>
    
    <!-- Fonts & Bootstrap 5 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">

    <style>
        body {
            background-color: #0b0f17;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(16, 185, 129, 0.08) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #f8fafc;
            position: relative;
            overflow-x: hidden;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            z-index: 10;
        }

        .login-card {
            background: rgba(17, 24, 39, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            padding: 2.25rem 2rem;
            position: relative;
        }

        .brand-badge {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, #4f46e5 0%, #38bdf8 100%);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
            margin-bottom: 1.25rem;
        }

        .input-group-modern {
            position: relative;
        }
        .input-group-modern input {
            background: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: #fff !important;
            border-radius: 10px !important;
            padding: 0.65rem 1rem 0.65rem 2.6rem !important;
            font-size: 0.9rem;
        }
        .input-group-modern input:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
        }
        .input-group-modern .input-icon {
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1rem;
            z-index: 5;
            pointer-events: none;
        }
        .btn-toggle-pwd {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            z-index: 5;
            padding: 0.25rem;
        }
        .btn-toggle-pwd:hover {
            color: #cbd5e1;
        }

        .quick-demo-pill {
            font-size: 0.75rem;
            padding: 0.3rem 0.65rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .quick-demo-pill:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: #818cf8;
            color: #fff;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="text-center mb-4">
                <div class="brand-badge">
                    <i class="bi bi-box-seam"></i>
                </div>
                <h3 class="fw-bold mb-1" style="letter-spacing: -0.02em;">Sistem Inventaris</h3>
                <p class="text-secondary small mb-0">Platform Manajemen Stok, IMEI & Kasir Multi-Unit</p>
            </div>

            <?php if ($flash = session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-3" style="border-radius:10px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div><?= esc($flash) ?></div>
                </div>
            <?php endif; ?>

            <?php if ($flash = session()->getFlashdata('sukses')): ?>
                <div class="alert alert-success py-2 px-3 small d-flex align-items-center gap-2 mb-3" style="border-radius:10px;">
                    <i class="bi bi-check-circle-fill"></i>
                    <div><?= esc($flash) ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Username</label>
                    <div class="input-group-modern">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" id="inputUsername" name="username" class="form-control" required autofocus
                               placeholder="Masukkan username" value="<?= esc(old('username')) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Password</label>
                    <div class="input-group-modern">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" id="inputPassword" name="password" class="form-control" required
                               placeholder="••••••••">
                        <button type="button" class="btn-toggle-pwd" id="btnTogglePwd" aria-label="Lihat Password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-secondary" style="font-size:0.75rem;">Akun demo 1-klik:</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="quick-demo-pill" data-user="admin" data-pass="admin123">
                            <i class="bi bi-shield-check text-warning"></i> Admin
                        </button>
                        <button type="button" class="quick-demo-pill" data-user="pembeli" data-pass="pembeli123">
                            <i class="bi bi-cart text-info"></i> Kasir
                        </button>
                        <button type="button" class="quick-demo-pill" data-user="staf" data-pass="staf123">
                            <i class="bi bi-boxes text-success"></i> Gudang
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2" style="font-size:0.95rem; border-radius:10px;">
                    <span>Masuk ke Dashboard</span>
                    <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </form>

            <div class="text-center mt-4 pt-2 border-top" style="border-color: rgba(255,255,255,0.08) !important;">
                <span class="small text-secondary" style="font-size:0.75rem;">
                    CodeIgniter 4 · Single Unit IMEI Tracking · MariaDB
                </span>
            </div>
        </div>
    </div>

    <script>
        // 1-Click Demo Account Picker
        document.querySelectorAll('.quick-demo-pill').forEach(btn => {
            btn.addEventListener('click', function() {
                const u = this.getAttribute('data-user');
                const p = this.getAttribute('data-pass');
                document.getElementById('inputUsername').value = u;
                document.getElementById('inputPassword').value = p;
                
                // Visual pulse feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => this.style.transform = '', 150);
            });
        });

        // Show / Hide Password
        const btnToggle = document.getElementById('btnTogglePwd');
        const inputPwd = document.getElementById('inputPassword');
        btnToggle?.addEventListener('click', () => {
            const isPwd = inputPwd.type === 'password';
            inputPwd.type = isPwd ? 'text' : 'password';
            btnToggle.innerHTML = isPwd ? '<i class="bi bi-eye-slash text-primary"></i>' : '<i class="bi bi-eye"></i>';
        });
    </script>
</body>
</html>
