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
</script>
@endpush
