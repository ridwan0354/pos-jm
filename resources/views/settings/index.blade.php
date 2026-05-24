@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-ink">Pengaturan</h1>
        <p class="text-xs text-ink-muted">Konfigurasi toko dan sistem</p>
    </div>
</div>

@if(session('success'))
<div class="flex items-center gap-2 mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm font-medium">
    <span class="ms ms-fill text-lg">check_circle</span> {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="flex items-center gap-2 mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
    <span class="ms ms-fill text-lg">error</span> {{ $errors->first() }}
</div>
@endif

<div class="max-w-2xl">
<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Identitas Toko --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border mb-5">
        <h2 class="font-bold text-ink mb-5 flex items-center gap-2">
            <span class="ms ms-fill text-primary text-xl">store</span> Identitas Toko
        </h2>

        {{-- Logo Upload --}}
        <div class="mb-5">
            <label class="form-label">Logo Toko</label>
            <div class="flex items-center gap-4">
                <div id="logoPreview" style="width:80px;height:80px;border-radius:16px;overflow:hidden;border:2px solid #e2e8f0;display:flex;align-items:center;justify-content:center;background:#f8fafc;flex-shrink:0;">
                    @if($settings['shop_logo'])
                        <img src="{{ asset('storage/'.$settings['shop_logo']) }}" style="width:100%;height:100%;object-fit:cover;" alt="Logo">
                    @else
                        <span class="ms ms-fill text-ink-faint" style="font-size:32px;">store</span>
                    @endif
                </div>
                <div>
                    <input type="file" name="shop_logo" id="shopLogoInput" accept="image/*" class="hidden" onchange="previewLogo(this)">
                    <button type="button" onclick="document.getElementById('shopLogoInput').click()" class="btn-secondary text-sm">
                        <span class="ms text-sm">upload</span> Upload Logo
                    </button>
                    <p class="text-xs text-ink-faint mt-1.5">Format: JPG, PNG, SVG. Maks 2MB</p>
                </div>
            </div>
        </div>

        {{-- Nama Toko --}}
        <div>
            <label class="form-label">Nama Toko <span class="text-red-500">*</span></label>
            <input type="text" name="shop_name"
                   value="{{ old('shop_name', $settings['shop_name']) }}"
                   class="form-input" placeholder="LinenFlow POS" required>
        </div>
    </div>

    {{-- Kontak Admin --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border mb-5">
        <h2 class="font-bold text-ink mb-5 flex items-center gap-2">
            <span class="ms ms-fill text-primary text-xl">support_agent</span> Kontak Admin
        </h2>

        <div>
            <label class="form-label">Nomor WhatsApp Admin <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="ms absolute left-3 top-1/2 -translate-y-1/2 text-ink-faint">phone</span>
                <input type="text" name="admin_phone"
                       value="{{ old('admin_phone', $settings['admin_phone']) }}"
                       class="form-input pl-10"
                       placeholder="08xx xxxx xxxx atau 628xxxxxxxxx" required>
            </div>
            <p class="text-xs text-ink-faint mt-1.5">
                Nomor ini digunakan untuk menerima request topup saldo dari pelanggan via WhatsApp.
            </p>
        </div>
    </div>

    {{-- Program Referral --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border mb-5">
        <h2 class="font-bold text-ink mb-1 flex items-center gap-2">
            <span class="ms ms-fill text-primary text-xl">diversity_3</span> Program Referral
        </h2>
        <p class="text-xs text-ink-muted mb-5">Komisi otomatis masuk ke saldo wallet pemilik kode referral.</p>

        <div>
            <label class="form-label">Persentase Komisi Referral</label>
            <div class="relative" style="max-width:180px;">
                <input type="number" name="referral_commission" min="0" max="100" step="0.5"
                       value="{{ old('referral_commission', $settings['referral_commission']) }}"
                       class="form-input pr-10" placeholder="10" required>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-bold text-ink-muted">%</span>
            </div>
            @error('referral_commission')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-ink-faint mt-2">
                <strong>Siapa yang dapat komisi?</strong> Pemilik kode referral (referrer) mendapat komisi ini
                saat pelanggan baru yang memakai kodenya pertama kali bayar lunas.
                Komisi langsung masuk ke saldo wallet referrer.
            </p>
        </div>
    </div>

    {{-- Pengaturan Printer Bluetooth --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border mb-5">
        <h2 class="font-bold text-ink mb-2 flex items-center gap-2">
            <span class="ms ms-fill text-primary text-xl">print</span> Printer Bluetooth
        </h2>
        <p class="text-xs text-ink-muted mb-4">Pilih dan hubungkan printer Bluetooth Kasir.</p>

        <div id="no-android-app" class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-ink-muted text-xs">
            <span class="ms text-sm font-semibold flex items-center gap-1.5 text-slate-500 mb-1">
                <span class="ms text-base">info</span> Info Koneksi
            </span>
            Koneksi printer Bluetooth instan didukung jika Anda mengakses POS melalui aplikasi kasir Android kami. Untuk pengguna laptop/desktop, silakan gunakan fitur print sistem (tombol hijau).
        </div>

        <div id="android-printer-settings" class="hidden">
            <div class="flex items-center justify-between mb-4 p-3 bg-slate-50 rounded-xl border border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="ms ms-fill text-xl text-slate-400" id="setting-bt-icon">print</span>
                    <div>
                        <p class="text-xs text-ink-muted font-medium">Status Printer</p>
                        <p class="font-semibold text-sm text-ink" id="setting-bt-label">Memuat...</p>
                    </div>
                </div>
                <div id="setting-bt-badge" class="badge bg-slate-100 text-slate-500 border border-slate-200 text-xs px-2.5 py-1" style="font-size:11px;padding:3px 8px;border-radius:8px;">
                    Terputus
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="scanAndroidPrinters()" class="btn-primary text-xs py-2 px-4" style="height:38px;border-radius:10px;">
                    <span class="ms text-sm">search</span> Cari Printer
                </button>
                <button type="button" onclick="printTestPage()" id="btn-test-print" class="btn-secondary text-xs py-2 px-4 hidden" style="height:38px;border-radius:10px;">
                    <span class="ms text-sm">receipt_long</span> Cetak Test
                </button>
                <button type="button" onclick="disconnectAndroidPrinter()" id="btn-disconnect-print" class="btn-danger text-xs py-2 px-4 hidden" style="height:38px;border-radius:10px;background:#ef4444;color:#fff;">
                    Putuskan
                </button>
            </div>
        </div>
    </div>

    {{-- Link Portal --}}


    <div class="bg-primary/5 rounded-xl p-4 border border-primary/20 mb-5">
        <p class="text-sm font-semibold text-primary mb-1 flex items-center gap-2">
            <span class="ms ms-fill text-base">link</span> Link Portal Pelanggan
        </p>
        <p class="text-xs text-ink-muted mb-3">Bagikan link ini ke pelanggan untuk melihat status cucian & saldo mereka:</p>
        <div class="flex items-center gap-2">
            <code class="text-xs bg-white px-3 py-2 rounded-lg border border-surface-border flex-1 text-ink overflow-x-auto">{{ url('/portal') }}</code>
            <button type="button" id="copyBtn"
                    onclick="navigator.clipboard.writeText('{{ url('/portal') }}');document.getElementById('copyBtn').textContent='Tersalin!';setTimeout(()=>document.getElementById('copyBtn').textContent='Salin',2000)"
                    class="btn-secondary text-xs py-2 px-3 flex-shrink-0">Salin</button>
        </div>
    </div>

    <button type="submit" class="btn-primary py-3 px-8">
        <span class="ms ms-fill">save</span> Simpan Pengaturan
    </button>
</form>
</div>
@endsection

@push('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('logoPreview').innerHTML =
                `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;" alt="Preview">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ── Android Bluetooth Printer Settings Page Integration ──────────────
document.addEventListener('DOMContentLoaded', () => {
    if (typeof AndroidPrint !== 'undefined') {
        document.getElementById('no-android-app').classList.add('hidden');
        document.getElementById('android-printer-settings').classList.remove('hidden');
        checkPrinterStatus();
    }
});

function checkPrinterStatus() {
    if (typeof AndroidPrint === 'undefined') return;

    const isConnected = AndroidPrint.isConnected();
    const badge = document.getElementById('setting-bt-badge');
    const label = document.getElementById('setting-bt-label');
    const icon = document.getElementById('setting-bt-icon');
    const btnTest = document.getElementById('btn-test-print');
    const btnDisc = document.getElementById('btn-disconnect-print');

    if (!badge || !label) return;

    if (isConnected) {
        const name = AndroidPrint.getConnectedDeviceName() || 'Printer Bluetooth';
        label.textContent = name;
        badge.textContent = 'Terhubung';
        badge.style.background = '#ecfdf5';
        badge.style.color = '#047857';
        badge.style.border = '1px solid #a7f3d0';
        if (icon) icon.style.color = '#10b981';
        if (btnTest) btnTest.classList.remove('hidden');
        if (btnDisc) btnDisc.classList.remove('hidden');
    } else {
        const saved = JSON.parse(localStorage.getItem('jm_printer_settings') || '{}');
        label.textContent = saved.deviceName ? `Tersimpan: ${saved.deviceName}` : 'Belum dikonfigurasi';
        badge.textContent = 'Terputus';
        badge.style.background = '#f8fafc';
        badge.style.color = '#64748b';
        badge.style.border = '1px solid #e2e8f0';
        if (icon) icon.style.color = '#94a3b8';
        if (btnTest) btnTest.classList.add('hidden');
        if (btnDisc) btnDisc.classList.add('hidden');
    }
}

function scanAndroidPrinters() {
    if (typeof AndroidPrint === 'undefined') return;
    try {
        const devicesJson = AndroidPrint.getPairedDevices();
        const devices = JSON.parse(devicesJson);
        if (devices.length === 0) {
            alert('Tidak ada printer Bluetooth terpasang (paired) di HP/Tablet Anda. Pasangkan via Pengaturan Bluetooth Android terlebih dahulu.');
            return;
        }
        showSettingsDeviceSelector(devices);
    } catch (e) {
        alert('Gagal memindai printer: ' + e.message);
    }
}

function showSettingsDeviceSelector(devices) {
    const existing = document.getElementById('settingsDeviceSelectorModal');
    if (existing) existing.remove();

    const modal = document.createElement('div');
    modal.id = 'settingsDeviceSelectorModal';
    modal.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,0.6);display:flex;align-items:center;justify-content:center;z-index:9999;backdrop-filter:blur(4px);padding:20px;';

    let listHtml = '';
    devices.forEach((d) => {
        listHtml += `
            <div onclick="selectSettingsDevice('${d.address}', '${escapeJsSetting(d.name)}')" style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;border:1px solid #e2e8f0;cursor:pointer;margin-bottom:8px;transition:all 0.2s;text-align:left;background:#fff;">
                <span style="font-size:20px;">🖨️</span>
                <div style="flex-grow:1;">
                    <div style="font-weight:700;font-size:14px;color:#0f172a;">${d.name || 'Printer Bluetooth'}</div>
                    <div style="font-size:12px;color:#64748b;">${d.address}</div>
                </div>
                <span style="color:#0d9488;font-weight:600;font-size:13px;">Pilih ›</span>
            </div>
        `;
    });

    modal.innerHTML = `
        <div style="background:#fff;border-radius:16px;width:100%;max-width:360px;padding:24px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);text-align:center;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0;">Hubungkan Printer</h3>
                <button type="button" onclick="document.getElementById('settingsDeviceSelectorModal').remove()" style="background:none;border:none;font-size:18px;cursor:pointer;color:#64748b;margin-left:auto;padding:4px;">✕</button>
            </div>
            <p style="font-size:12px;color:#64748b;margin-bottom:16px;text-align:left;">Pilih printer yang terdaftar di HP Android Anda.</p>
            <div style="max-height:240px;overflow-y:auto;">
                ${listHtml}
            </div>
        </div>
    `;

    document.body.appendChild(modal);
}

function escapeJsSetting(str) {
    if (!str) return '';
    return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
}

function selectSettingsDevice(address, name) {
    const modal = document.getElementById('settingsDeviceSelectorModal');
    if (modal) modal.remove();

    const label = document.getElementById('setting-bt-label');
    if (label) label.textContent = 'Menghubungkan...';

    setTimeout(() => {
        try {
            const ok = AndroidPrint.connectDevice(address);
            if (ok) {
                const settings = { deviceName: name, deviceAddress: address };
                localStorage.setItem('jm_printer_settings', JSON.stringify(settings));
                alert(`Berhasil terhubung dan menyimpan printer: ${name}`);
            } else {
                throw new Error('Koneksi gagal.');
            }
        } catch(err) {
            alert('Gagal menghubungkan printer: ' + err.message);
        }
        checkPrinterStatus();
    }, 100);
}

function disconnectAndroidPrinter() {
    if (typeof AndroidPrint === 'undefined') return;
    try {
        AndroidPrint.disconnectDevice();
        localStorage.removeItem('jm_printer_settings');
        alert('Printer diputuskan dan setelan dihapus.');
    } catch(e) {
        console.error(e);
    }
    checkPrinterStatus();
}

function printTestPage() {
    if (typeof AndroidPrint === 'undefined') return;
    try {
        const buffer = [];
        // ESC @ (initialize)
        buffer.push(0x1B, 0x40);
        // Align center
        buffer.push(0x1B, 0x61, 0x01);
        // Double size bold
        buffer.push(0x1B, 0x45, 0x01);
        buffer.push(0x1D, 0x21, 0x11);
        
        const encoder = new TextEncoder();
        const t1 = encoder.encode("LinenFlow POS\n");
        t1.forEach(b => buffer.push(b));
        
        // Normal size
        buffer.push(0x1B, 0x45, 0x00);
        buffer.push(0x1D, 0x21, 0x00);
        
        const t2 = encoder.encode("TEST PRINT BERHASIL\n\n");
        t2.forEach(b => buffer.push(b));
        
        const t3 = encoder.encode("Tanggal: " + new Date().toLocaleString() + "\n");
        t3.forEach(b => buffer.push(b));
        
        const t4 = encoder.encode("================================\n\n\n\n");
        t4.forEach(b => buffer.push(b));
        
        const bytes = new Uint8Array(buffer);
        
        // bytes to base64
        let binary = '';
        const len = bytes.length;
        for (let i = 0; i < len; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        const base64Str = window.btoa(binary);
        
        const ok = AndroidPrint.printBase64(base64Str);
        if (ok) {
            alert('Test print berhasil dikirim!');
        } else {
            alert('Gagal mengirim data print.');
        }
    } catch(e) {
        alert('Gagal print test: ' + e.message);
    }
}
</script>
@endpush
