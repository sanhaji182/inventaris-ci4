<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · Sistem Inventaris BNSP</title>
    
    <!-- Google Fonts & Bootstrap 5 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.08) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(16, 185, 129, 0.04) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #0f172a;
            position: relative;
            overflow-x: hidden;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            z-index: 10;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(15, 23, 42, 0.03);
            padding: 2.25rem 2rem;
            position: relative;
        }

        .brand-badge {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: linear-gradient(135deg, #4f46e5 0%, #38bdf8 100%);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.25);
            margin-bottom: 1.25rem;
        }

        .input-group-modern {
            position: relative;
        }
        .input-group-modern input {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
            border-radius: 10px !important;
            padding: 0.65rem 1rem 0.65rem 2.6rem !important;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .input-group-modern input:focus {
            background: #ffffff !important;
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
        }
        .input-group-modern .input-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 5;
            font-size: 1rem;
        }
        .input-group-modern .toggle-pwd {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            padding: 0.25rem;
            cursor: pointer;
            z-index: 5;
            transition: color 0.15s;
        }
        .input-group-modern .toggle-pwd:hover {
            color: #0f172a;
        }

        .quick-demo-pill {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 0.78rem;
            padding: 0.4rem 0.85rem;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 600;
        }
        .quick-demo-pill:hover {
            background: #eef2ff;
            color: #4f46e5;
            border-color: #c7d2fe;
            transform: translateY(-1px);
        }

        .btn-login-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: -0.01em;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
            transition: all 0.2s ease;
        }
        .btn-login-gradient:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
            color: #fff;
        }

        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 1;
            opacity: 0.5;
        }
        .glow-orb-1 {
            width: 320px;
            height: 320px;
            background: #c7d2fe;
            top: 10%;
            left: 15%;
        }
        .glow-orb-2 {
            width: 300px;
            height: 300px;
            background: #bae6fd;
            bottom: 10%;
            right: 15%;
        }
    </style>
</head>
<body>
    <div class="glow-orb glow-orb-1"></div>
    <div class="glow-orb glow-orb-2"></div>

    <div class="login-wrapper">
        <div class="login-card">
            <!-- Brand -->
            <div class="text-center">
                <div class="brand-badge">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <h3 class="fw-bold mb-1" style="letter-spacing: -0.02em; color: #0f172a;">Sistem Inventaris</h3>
                <p class="text-muted small mb-4">Pengelolaan Stok, Modal Beli & Margin Untung-Rugi (BNSP)</p>
            </div>

            <!-- Flash Alert -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2 px-3 small rounded-3 d-flex align-items-center mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                    <div><?= esc(session()->getFlashdata('error')) ?></div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('sukses')): ?>
                <div class="alert alert-success py-2 px-3 small rounded-3 d-flex align-items-center mb-3">
                    <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                    <div><?= esc(session()->getFlashdata('sukses')) ?></div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="/login">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary mb-1">Username</label>
                    <div class="input-group-modern">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" id="inputUsername" name="username" class="form-control" required autofocus
                               placeholder="Masukkan username" value="<?= old('username') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary mb-1">Password</label>
                    <div class="input-group-modern">
                        <i class="bi bi-key input-icon"></i>
                        <input type="password" id="inputPassword" name="password" class="form-control" required
                               placeholder="••••••••">
                        <button type="button" class="toggle-pwd" id="btnTogglePwd" title="Lihat Password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- 1-Click Demo Pills (Admin & Pengelola) -->
                <div class="mb-4">
                    <label class="form-label small text-muted d-block mb-2" style="font-size:0.72rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Masuk Cepat Demo (1-Klik):</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="quick-demo-pill flex-fill justify-content-center" data-user="admin" data-pass="admin123">
                            <i class="bi bi-shield-check text-primary fs-6"></i> Admin
                        </button>
                        <button type="button" class="quick-demo-pill flex-fill justify-content-center" data-user="pengelola" data-pass="pengelola123">
                            <i class="bi bi-boxes text-info fs-6"></i> Pengelola
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-login-gradient w-100">
                    Masuk ke Sistem <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </form>

            <div class="text-center mt-4 pt-2 border-top">
                <small class="text-muted" style="font-size:0.75rem;">
                    CodeIgniter 4 · Clean Light UI · Standar Asesmen BNSP
                </small>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        const btnToggle = document.getElementById('btnTogglePwd');
        const inputPwd = document.getElementById('inputPassword');
        btnToggle.addEventListener('click', () => {
            const isPassword = inputPwd.type === 'password';
            inputPwd.type = isPassword ? 'text' : 'password';
            btnToggle.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });

        // Quick Demo Auto-filler
        document.querySelectorAll('.quick-demo-pill').forEach(btn => {
            btn.addEventListener('click', () => {
                const u = btn.dataset.user;
                const p = btn.dataset.pass;
                document.getElementById('inputUsername').value = u;
                document.getElementById('inputPassword').value = p;
                btn.style.transform = 'scale(0.96)';
                setTimeout(() => btn.style.transform = '', 150);
            });
        });
    </script>
</body>
</html>
