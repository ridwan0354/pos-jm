<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Pelanggan') — {{ $shopName ?? 'LinenFlow' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;background:#f0fdf9;min-height:100vh;color:#0f172a;}
        .ms{font-family:'Material Symbols Rounded';font-weight:normal;font-style:normal;font-size:20px;line-height:1;letter-spacing:normal;text-transform:none;display:inline-block;white-space:nowrap;direction:ltr;-webkit-font-smoothing:antialiased;}
        .ms-fill{font-variation-settings:'FILL' 1;}

        .portal-header{position:sticky;top:0;z-index:100;background:#fff;border-bottom:1px solid #e2e8f0;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 4px rgba(0,0,0,.06);}
        .portal-logo{width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#0d9488,#14b8a6);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;}
        .portal-logo img{width:100%;height:100%;object-fit:cover;}
        .portal-content{max-width:480px;margin:0 auto;padding:16px;}

        .card{background:#fff;border-radius:16px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid #e2e8f0;}

        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;border:none;border-radius:12px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;text-decoration:none;transition:all .15s;}
        .btn-primary{background:#0d9488;color:#fff;}
        .btn-primary:hover{background:#0f766e;transform:translateY(-1px);}
        .btn-wa{background:#25D366;color:#fff;}
        .btn-wa:hover{background:#1da851;transform:translateY(-1px);}
        .btn-outline{background:transparent;border:1.5px solid #e2e8f0;color:#64748b;}
        .btn-outline:hover{background:#f8fafc;border-color:#cbd5e1;}
        .btn-danger{background:#ef4444;color:#fff;}
        .btn-danger:hover{background:#dc2626;}

        .form-input{width:100%;padding:12px 14px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:inherit;color:#0f172a;outline:none;transition:border-color .15s,box-shadow .15s;background:#fff;}
        .form-input:focus{border-color:#0d9488;box-shadow:0 0 0 3px rgba(13,148,136,.1);}
        .form-label{display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;}

        .badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
        .badge-bronze{background:#fef3c7;color:#92400e;}
        .badge-silver{background:#f1f5f9;color:#475569;}
        .badge-gold{background:#fef9c3;color:#854d0e;}

        .alert{padding:12px 14px;border-radius:12px;font-size:13px;margin-bottom:14px;display:flex;align-items:flex-start;gap:8px;}
        .alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;}
        .alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;}
        .alert-info{background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;}
    </style>
    @stack('styles')
</head>
<body>

<header class="portal-header">
    <div style="display:flex;align-items:center;gap:10px;">
        <div class="portal-logo">
            @if(!empty($shopLogo ?? ''))
                <img src="{{ asset('storage/'.($shopLogo ?? '')) }}" alt="Logo">
            @else
                <span class="ms ms-fill" style="color:#fff;font-size:20px;">local_laundry_service</span>
            @endif
        </div>
        <div>
            <div style="font-size:15px;font-weight:700;color:#0f172a;">{{ $shopName ?? 'LinenFlow' }}</div>
            <div style="font-size:11px;color:#64748b;">Portal Pelanggan</div>
        </div>
    </div>
    @if(session('portal_customer_id'))
    <form action="{{ route('portal.logout') }}" method="POST" style="margin:0;">
        @csrf
        <button type="submit" style="background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:4px;color:#64748b;font-size:13px;font-family:inherit;padding:6px 10px;border-radius:8px;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='none'">
            <span class="ms" style="font-size:18px;">logout</span> Keluar
        </button>
    </form>
    @endif
</header>

<div class="portal-content">
    @if(session('success'))
    <div class="alert alert-success"><span class="ms ms-fill" style="font-size:18px;flex-shrink:0;">check_circle</span>{{ session('success') }}</div>
    @endif
    @if(session('info'))
    <div class="alert alert-info"><span class="ms ms-fill" style="font-size:18px;flex-shrink:0;">info</span>{{ session('info') }}</div>
    @endif

    @yield('content')
</div>

@stack('scripts')
</body>
</html>
