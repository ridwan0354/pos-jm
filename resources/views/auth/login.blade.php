<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – LinenFlow POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0fdf9;
            overflow: hidden;
        }
        .ms { font-family: 'Material Symbols Rounded'; font-weight: normal; font-style: normal; display: inline-block; line-height: 1; letter-spacing: normal; text-transform: none; white-space: nowrap; word-wrap: normal; font-size: 24px; -webkit-font-smoothing: antialiased; font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .ms-fill { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

        /* Left Panel */
        .left-panel {
            display: none;
            flex: 1;
            background: linear-gradient(145deg, #0d9488 0%, #0f766e 40%, #0891b2 100%);
            padding: 60px 56px;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 1024px) { .left-panel { display: flex; } }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 380px; height: 380px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .brand-logo {
            display: flex; align-items: center; gap: 14px; position: relative; z-index: 1;
        }
        .brand-icon {
            width: 52px; height: 52px; border-radius: 16px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
            display: flex; align-items: center; justify-content: center;
        }
        .hero-text { position: relative; z-index: 1; }
        .hero-text h1 {
            font-size: 42px; font-weight: 900; color: #fff;
            line-height: 1.15; letter-spacing: -0.03em;
        }
        .hero-text p {
            font-size: 17px; color: rgba(255,255,255,0.75);
            margin-top: 16px; line-height: 1.6;
        }
        .feature-list { display: flex; flex-direction: column; gap: 16px; position: relative; z-index: 1; }
        .feature-item {
            display: flex; align-items: center; gap: 14px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            border-radius: 14px; padding: 14px 18px;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .feature-item .ms { color: #fff; font-size: 22px; }
        .feature-item p { font-size: 14px; color: rgba(255,255,255,0.9); font-weight: 500; }

        /* Right Panel */
        .right-panel {
            width: 100%; max-width: 480px;
            display: flex; align-items: center; justify-content: center;
            padding: 32px 24px;
            background: #fff;
        }
        @media (min-width: 1024px) { .right-panel { width: 480px; flex-shrink: 0; } }

        .login-box { width: 100%; max-width: 380px; }

        .mobile-brand {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 40px;
        }
        @media (min-width: 1024px) { .mobile-brand { display: none; } }
        .mobile-brand-icon {
            width: 42px; height: 42px; border-radius: 13px;
            background: linear-gradient(135deg, #0d9488, #0891b2);
            display: flex; align-items: center; justify-content: center;
        }

        .login-title {
            font-size: 26px; font-weight: 800; color: #0f172a;
            letter-spacing: -0.03em; margin-bottom: 6px;
        }
        .login-subtitle { font-size: 14px; color: #94a3b8; margin-bottom: 36px; }

        .form-group { margin-bottom: 20px; }
        label {
            display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 8px;
        }
        .input-wrap {
            position: relative; display: flex; align-items: center;
        }
        .input-icon {
            position: absolute; left: 14px;
            color: #94a3b8; font-size: 20px;
            pointer-events: none;
        }
        input[type="email"], input[type="password"], input[type="text"] {
            width: 100%; padding: 13px 14px 13px 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px; font-size: 14px; font-family: 'Inter', sans-serif;
            color: #0f172a; background: #f8fafc;
            transition: all 0.2s;
            outline: none;
        }
        input:focus {
            border-color: #0d9488; background: #fff;
            box-shadow: 0 0 0 3px rgba(13,148,136,0.1);
        }
        .toggle-pw {
            position: absolute; right: 14px; cursor: pointer;
            color: #94a3b8; font-size: 20px; background: none; border: none;
        }
        .toggle-pw:hover { color: #64748b; }

        .remember-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px;
        }
        .checkbox-label {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #64748b; cursor: pointer;
        }
        .checkbox-label input[type="checkbox"] {
            width: 16px; height: 16px; padding: 0;
            accent-color: #0d9488; cursor: pointer;
        }
        .forgot-link {
            font-size: 13px; color: #0d9488; font-weight: 600;
            text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #0d9488, #0891b2);
            color: #fff; font-size: 15px; font-weight: 700;
            border: none; border-radius: 12px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(13,148,136,0.35);
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(13,148,136,0.45);
        }
        .btn-login:active { transform: translateY(0); }

        .error-msg {
            display: flex; align-items: center; gap: 8px;
            padding: 12px 16px; margin-bottom: 20px;
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 12px; color: #dc2626; font-size: 13px; font-weight: 500;
        }

        .divider {
            text-align: center; margin: 28px 0;
            color: #94a3b8; font-size: 12px; font-weight: 600;
            position: relative;
        }
        .divider::before, .divider::after {
            content: ''; position: absolute; top: 50%;
            width: calc(50% - 40px); height: 1px; background: #e2e8f0;
        }
        .divider::before { left: 0; }
        .divider::after { right: 0; }

        .demo-info {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 12px; padding: 14px 16px;
            font-size: 12px; color: #15803d;
        }
        .demo-info strong { font-weight: 700; }
        .demo-cred { margin-top: 6px; display: flex; flex-direction: column; gap: 2px; }
        .demo-cred span { opacity: 0.8; }
    </style>
</head>
<body>

{{-- Left Hero Panel --}}
<div class="left-panel">
    <div class="brand-logo">
        <div class="brand-icon">
            <span class="ms ms-fill" style="color:#fff; font-size:26px;">local_laundry_service</span>
        </div>
        <div>
            <p style="font-weight:800; color:#fff; font-size:20px; letter-spacing:-0.03em;">LinenFlow</p>
            <p style="font-size:12px; color:rgba(255,255,255,0.65); font-weight:500;">POS System</p>
        </div>
    </div>

    <div class="hero-text">
        <h1>Kelola laundry<br>lebih cerdas.</h1>
        <p>Sistem POS modern untuk bisnis laundry Anda.<br>Cepat, mudah, dan terpercaya.</p>
    </div>

    <div class="feature-list">
        <div class="feature-item">
            <span class="ms ms-fill">receipt_long</span>
            <div>
                <p>Manajemen Pesanan Real-time</p>
            </div>
        </div>
        <div class="feature-item">
            <span class="ms ms-fill">inventory_2</span>
            <div>
                <p>Kontrol Stok Otomatis</p>
            </div>
        </div>
        <div class="feature-item">
            <span class="ms ms-fill">analytics</span>
            <div>
                <p>Laporan & Analitik Bisnis</p>
            </div>
        </div>
    </div>
</div>

{{-- Right Login Panel --}}
<div class="right-panel">
    <div class="login-box">

        {{-- Mobile Brand --}}
        <div class="mobile-brand">
            <div class="mobile-brand-icon">
                <span class="ms ms-fill" style="color:#fff; font-size:22px;">local_laundry_service</span>
            </div>
            <div>
                <p style="font-weight:800; color:#0d9488; font-size:18px; letter-spacing:-0.03em;">LinenFlow</p>
                <p style="font-size:11px; color:#94a3b8;">POS System</p>
            </div>
        </div>

        <h1 class="login-title">Selamat datang 👋</h1>
        <p class="login-subtitle">Masuk ke akun LinenFlow Anda</p>

        {{-- Error --}}
        @if($errors->any())
        <div class="error-msg">
            <span class="ms ms-fill" style="font-size:18px;">error</span>
            {{ $errors->first() }}
        </div>
        @endif
        @if(session('status'))
        <div style="display:flex;align-items:center;gap:8px;padding:12px 16px;margin-bottom:20px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;color:#15803d;font-size:13px;">
            <span class="ms ms-fill" style="font-size:18px;color:#22c55e;">check_circle</span>
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <span class="ms input-icon">mail</span>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="admin@linenflow.id"
                           required autofocus autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="ms input-icon">lock</span>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••"
                           required autocomplete="current-password">
                    <button type="button" class="toggle-pw ms" id="togglePw" onclick="togglePassword()">visibility</button>
                </div>
            </div>

            <div class="remember-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
                @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                @endif
            </div>

            <button type="submit" class="btn-login">
                <span class="ms ms-fill" style="font-size:20px;">login</span>
                Masuk Sekarang
            </button>
        </form>

        <div class="divider">DEMO AKUN</div>

        <div class="demo-info">
            <strong>🧪 Akun Demo</strong>
            <div class="demo-cred">
                <span>Email: <strong>admin@linenflow.id</strong></span>
                <span>Password: <strong>password</strong></span>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const pw = document.getElementById('password');
    const btn = document.getElementById('togglePw');
    if (pw.type === 'password') {
        pw.type = 'text';
        btn.textContent = 'visibility_off';
    } else {
        pw.type = 'password';
        btn.textContent = 'visibility';
    }
}
</script>
</body>
</html>
