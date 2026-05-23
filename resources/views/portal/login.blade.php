@extends('portal.layouts.app')
@section('title', 'Login Pelanggan')

@section('content')
<div style="min-height:calc(100vh - 65px);display:flex;align-items:center;justify-content:center;padding:20px 0;">
    <div style="width:100%;max-width:400px;">
        <div class="card" style="padding:32px 28px;">

            {{-- Icon & Judul --}}
            <div style="text-align:center;margin-bottom:28px;">
                <div style="width:76px;height:76px;background:linear-gradient(135deg,#0d9488,#14b8a6);border-radius:22px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;box-shadow:0 10px 28px rgba(13,148,136,.3);">
                    <span class="ms ms-fill" style="font-size:38px;color:#fff;">local_laundry_service</span>
                </div>
                <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 6px;">Selamat Datang!</h1>
                <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;">Masukkan nomor HP untuk melihat<br>status cucian & saldo Anda</p>
            </div>

            {{-- Error --}}
            @if($errors->any())
            <div class="alert alert-error">
                <span class="ms ms-fill" style="font-size:18px;flex-shrink:0;">error</span>
                {{ $errors->first() }}
            </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('portal.login') }}" method="POST">
                @csrf
                <div style="margin-bottom:20px;">
                    <label class="form-label">Nomor HP / WhatsApp</label>
                    <div style="position:relative;">
                        <span class="ms" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:20px;">phone</span>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               class="form-input" style="padding-left:42px;"
                               placeholder="08xx xxxx xxxx" required autofocus>
                    </div>
                    <p style="font-size:11px;color:#94a3b8;margin:6px 0 0;">Gunakan nomor yang terdaftar di kasir</p>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;padding:14px;font-size:15px;">
                    <span class="ms ms-fill" style="font-size:20px;">login</span>
                    Masuk ke Portal
                </button>
            </form>

            {{-- Info --}}
            <div style="margin-top:20px;padding:12px 14px;background:#f0fdf9;border-radius:10px;border:1px solid #ccfbf1;text-align:center;">
                <p style="font-size:12px;color:#0f766e;margin:0;line-height:1.5;">
                    <span class="ms ms-fill" style="font-size:14px;vertical-align:middle;">info</span>
                    Belum terdaftar? Hubungi kasir untuk mendaftarkan nomor HP Anda.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
