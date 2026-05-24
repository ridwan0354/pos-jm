<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keamanan – LinenFlow POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0fdf9;
            padding: 24px;
        }
        .ms { font-family: 'Material Symbols Rounded'; font-weight: normal; font-style: normal; display: inline-block; line-height: 1; letter-spacing: normal; text-transform: none; white-space: nowrap; word-wrap: normal; font-size: 24px; -webkit-font-smoothing: antialiased; font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .ms-fill { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

        .verify-box {
            width: 100%;
            max-width: 400px;
            background: #fff;
            padding: 40px 32px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(15,23,42,0.06);
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 18px;
            background: linear-gradient(135deg, #0d9488, #0891b2);
            margin-bottom: 24px;
            box-shadow: 0 8px 16px rgba(13,148,136,0.2);
        }

        .verify-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
        }
        .verify-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 24px;
            text-align: left;
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 20px;
            pointer-events: none;
        }
        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 13px 14px 13px 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            background: #f8fafc;
            transition: all 0.2s;
            outline: none;
        }
        input:focus {
            border-color: #0d9488;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(13,148,136,0.1);
        }
        .toggle-pw {
            position: absolute;
            right: 14px;
            cursor: pointer;
            color: #94a3b8;
            font-size: 20px;
            background: none;
            border: none;
        }
        .toggle-pw:hover {
            color: #64748b;
        }

        .btn-verify {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0d9488, #0891b2);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(13,148,136,0.35);
        }
        .btn-verify:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(13,148,136,0.45);
        }
        .btn-verify:active {
            transform: translateY(0);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            margin-top: 20px;
            font-weight: 500;
            transition: color 0.15s;
        }
        .btn-back:hover {
            color: #0f172a;
        }

        .error-msg {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            color: #dc2626;
            font-size: 13px;
            font-weight: 500;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="verify-box">
    <div class="brand-logo">
        <span class="ms ms-fill" style="color:#fff; font-size:28px;">lock</span>
    </div>

    <h1 class="verify-title">Verifikasi Keamanan</h1>
    <p class="verify-subtitle">Silakan masukkan password verifikasi untuk mengakses halaman laporan & pengaturan.</p>

    {{-- Error --}}
    @if($errors->any())
    <div class="error-msg">
        <span class="ms ms-fill" style="font-size:18px;">error</span>
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.verify.post') }}">
        @csrf

        <div class="form-group">
            <label for="password">Password Verifikasi</label>
            <div class="input-wrap">
                <span class="ms input-icon">lock</span>
                <input type="password" id="password" name="password"
                       placeholder="Masukkan password khusus..."
                       required autofocus>
                <button type="button" class="toggle-pw ms" id="togglePw" onclick="togglePassword()">visibility</button>
            </div>
        </div>

        <button type="submit" class="btn-verify">
            <span class="ms ms-fill" style="font-size:20px;">verified_user</span>
            Verifikasi Akses
        </button>
    </form>

    <a href="{{ route('dashboard') }}" class="btn-back">
        <span class="ms" style="font-size:18px;">arrow_back</span> Kembali ke Dashboard
    </a>
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
