@extends('layouts.app')
@section('title', 'Detail Pesanan – '.$order->order_number)

@section('content')
{{-- Action Bar --}}
<div class="flex items-center gap-3 mb-6 flex-wrap">
    <a href="{{ route('orders.index') }}" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;border:1px solid #e2e8f0;background:#fff;text-decoration:none;">
        <span class="ms" style="color:#64748b;">arrow_back</span>
    </a>
    <div style="flex:1;min-width:0;">
        <h1 style="font-size:18px;font-weight:800;color:#0f172a;font-family:monospace;margin:0;">{{ $order->order_number }}</h1>
        <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">{{ $order->created_at->isoFormat('dddd, D MMMM Y · HH:mm') }}</p>
    </div>
    <span class="badge {{ $order->status_color }}" style="font-size:13px;padding:6px 14px;">
        <span class="ms ms-fill">{{ $order->status_icon }}</span>
        {{ $order->status_label }}
    </span>

    {{-- WhatsApp Button --}}
    @php
        $waPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (str_starts_with($waPhone, '0')) $waPhone = '62' . substr($waPhone, 1);
        $portalUrl = url('/portal');
        $waMsg = urlencode(
            "Halo {$order->customer_name}, pesanan laundry Anda sudah masuk! 🧺\n\n" .
            "📋 No. Order : {$order->order_number}\n" .
            "👕 Layanan   : {$order->service_type} ({$order->weight} kg)\n" .
            "💰 Total     : Rp " . number_format($order->total,0,',','.') . "\n" .
            "📌 Status    : {$order->status_label}\n\n" .
            "🔍 *Pantau status cucian Anda secara real-time di:*\n" .
            "{$portalUrl}\n\n" .
            "🔑 Login menggunakan nomor HP terdaftar:\n" .
            "*{$order->customer_phone}*\n\n" .
            "Terima kasih sudah mempercayakan cucian Anda kepada kami! 👍"
        );
    @endphp
    <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#25D366;color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:600;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        WhatsApp
    </a>

    {{-- Print Buttons --}}
    <button onclick="printCustomerReceipt()" id="btn-print"
            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#0d9488;color:#fff;border-radius:10px;border:none;cursor:pointer;font-size:13px;font-weight:600;font-family:inherit;">
        <span class="ms ms-fill" style="font-size:18px;">print</span>
        Struk Pelanggan
    </button>
    <button onclick="printProductionReceipt()" id="btn-print-prod"
            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#4f46e5;color:#fff;border-radius:10px;border:none;cursor:pointer;font-size:13px;font-weight:600;font-family:inherit;">
        <span class="ms ms-fill" style="font-size:18px;">receipt_long</span>
        Nota Produksi
    </button>

    {{-- Bluetooth Printer Controls --}}
    <div style="display:inline-flex;align-items:center;gap:6px;border:1px solid #e2e8f0;background:#fff;border-radius:10px;padding:4px 6px;box-shadow:0 1px 2px rgba(0,0,0,0.05);flex-wrap:wrap;">
        <button onclick="toggleBluetoothPrinter()" id="btn-connect-bt"
                type="button"
                style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:#f1f5f9;color:#475569;border-radius:8px;border:none;cursor:pointer;"
                title="Hubungkan Printer Bluetooth">
            <span class="ms ms-fill" style="font-size:16px;" id="bt-icon">bluetooth</span>
        </button>
        <span id="bt-status-badge" class="badge bg-slate-50 text-slate-500 border border-slate-200" style="font-size:10px;padding:2px 6px;">
            <span id="bt-status-label">Printer Off</span>
        </span>
        <button onclick="printBluetooth('customer')" id="btn-print-bt"
                type="button"
                style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#2563eb;color:#fff;border-radius:8px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:inherit;">
            <span class="ms ms-fill" style="font-size:15px;">print</span>
            BT Pelanggan
        </button>
        <button onclick="printBluetooth('production')" id="btn-print-bt-prod"
                type="button"
                style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#4f46e5;color:#fff;border-radius:8px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:inherit;">
            <span class="ms ms-fill" style="font-size:15px;">receipt_long</span>
            BT Produksi
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Left --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Customer Info --}}
        <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
            <h2 class="font-bold text-ink mb-4 flex items-center gap-2">
                <span class="ms ms-fill text-primary">person</span> Pelanggan
            </h2>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($order->customer_name,0,1)) }}
                </div>
                <div>
                    <p class="font-bold text-ink">{{ $order->customer_name }}</p>
                    <p class="text-sm text-ink-muted">{{ $order->customer_phone }}</p>
                    @if($order->customer)
                    <span class="badge {{ $order->customer->member_badge_color }} mt-1">{{ $order->customer->member_level }}</span>
                    @endif
                </div>
                @if($order->customer)
                <a href="{{ route('customers.show', $order->customer) }}" class="ml-auto btn-secondary text-xs py-1.5 px-3">
                    <span class="ms text-sm">person</span> Profil
                </a>
                @endif
            </div>
        </div>

        {{-- Order Details --}}
        <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
            <h2 class="font-bold text-ink mb-4 flex items-center gap-2">
                <span class="ms ms-fill text-primary">local_laundry_service</span> Detail Layanan
            </h2>
            
            @if($order->items->count() > 0)
            {{-- Itemized List --}}
            <div class="space-y-2.5 mb-4">
                @foreach($order->items as $item)
                <div class="flex justify-between items-center bg-slate-50 rounded-xl p-3 text-sm">
                    <div>
                        <p class="font-bold text-ink">{{ $item->name }}</p>
                        <p class="text-xs text-ink-muted">
                            {{ $item->qty }} {{ $item->unit }} @ Rp {{ number_format($item->price, 0, ',', '.') }}
                        </p>
                    </div>
                    <span class="font-semibold text-ink">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mt-4">
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-ink-muted mb-1">Kecepatan</p>
                    <p class="font-semibold text-ink capitalize">{{ $order->speed }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-ink-muted mb-1 flex items-center gap-1">
                        <span>Parfum</span>
                        <span class="ms text-ink-faint" style="font-size: 11px;">edit</span>
                    </p>
                    <form action="{{ route('orders.updatePerfume', $order) }}" method="POST" id="update-perfume-form-items">
                        @csrf @method('PATCH')
                        <select name="perfume" onchange="document.getElementById('update-perfume-form-items').submit()" 
                                style="background:transparent; border:none; padding:0; margin:0; font-weight:600; color:#0f172a; font-size:14px; cursor:pointer; outline:none; width:100%;"
                                class="capitalize">
                            <option value="harum" {{ $order->perfume === 'harum' ? 'selected' : '' }}>🌸 Harum</option>
                            <option value="sakura" {{ $order->perfume === 'sakura' ? 'selected' : '' }}>🌸 Sakura</option>
                            <option value="lavender" {{ $order->perfume === 'lavender' ? 'selected' : '' }}>💜 Lavender</option>
                            <option value="tanpa" {{ $order->perfume === 'tanpa' ? 'selected' : '' }}>❌ Tanpa</option>
                        </select>
                    </form>
                </div>
                @if($order->estimated_done)
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-ink-muted mb-1">Est. Selesai</p>
                    <p class="font-semibold text-ink">{{ $order->estimated_done->format('d M Y') }}</p>
                </div>
                @endif
                @if($order->ironingStaff)
                <div class="bg-slate-50 rounded-xl p-3 col-span-2 md:col-span-1">
                    <p class="text-xs text-ink-muted mb-1">PJ Setrika</p>
                    <p class="font-semibold text-ink">{{ $order->ironingStaff->name }}</p>
                    @if($order->ironing_fee)
                    <p class="text-xs text-primary mt-0.5">Fee: Rp {{ number_format($order->ironing_fee,0,',','.') }}</p>
                    @endif
                </div>
                @endif
            </div>
            @else
            {{-- Original Single Service Info --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-ink-muted mb-1">Kategori</p>
                    <p class="font-semibold text-ink capitalize">{{ $order->category }}</p>
                </div>
                @if($order->service_type)
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-ink-muted mb-1">Jenis Layanan</p>
                    <p class="font-semibold text-ink">{{ str_replace('_',' ', ucfirst($order->service_type)) }}</p>
                </div>
                @endif
                @if($order->weight)
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-ink-muted mb-1">Berat</p>
                    <p class="font-semibold text-ink">{{ $order->weight }} kg</p>
                </div>
                @endif
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-ink-muted mb-1 flex items-center gap-1">
                        <span>Parfum</span>
                        <span class="ms text-ink-faint" style="font-size: 11px;">edit</span>
                    </p>
                    <form action="{{ route('orders.updatePerfume', $order) }}" method="POST" id="update-perfume-form-static">
                        @csrf @method('PATCH')
                        <select name="perfume" onchange="document.getElementById('update-perfume-form-static').submit()" 
                                style="background:transparent; border:none; padding:0; margin:0; font-weight:600; color:#0f172a; font-size:14px; cursor:pointer; outline:none; width:100%;"
                                class="capitalize">
                            <option value="harum" {{ $order->perfume === 'harum' ? 'selected' : '' }}>🌸 Harum</option>
                            <option value="sakura" {{ $order->perfume === 'sakura' ? 'selected' : '' }}>🌸 Sakura</option>
                            <option value="lavender" {{ $order->perfume === 'lavender' ? 'selected' : '' }}>💜 Lavender</option>
                            <option value="tanpa" {{ $order->perfume === 'tanpa' ? 'selected' : '' }}>❌ Tanpa</option>
                        </select>
                    </form>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-ink-muted mb-1">Kecepatan</p>
                    <p class="font-semibold text-ink capitalize">{{ $order->speed }}</p>
                </div>
                @if($order->estimated_done)
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-ink-muted mb-1">Est. Selesai</p>
                    <p class="font-semibold text-ink">{{ $order->estimated_done->format('d M Y') }}</p>
                </div>
                @endif
                @if($order->ironingStaff)
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-ink-muted mb-1">PJ Setrika</p>
                    <p class="font-semibold text-ink">{{ $order->ironingStaff->name }}</p>
                    @if($order->ironing_fee)
                    <p class="text-xs text-primary mt-0.5">Fee: Rp {{ number_format($order->ironing_fee,0,',','.') }}</p>
                    @endif
                </div>
                @endif
            </div>
            @endif
            @if($order->notes)
            <div class="mt-4 p-3 bg-amber-50 border border-amber-100 rounded-xl text-sm text-ink">
                <span class="ms text-amber-500 text-sm">sticky_note_2</span>
                <span class="ml-1">{{ $order->notes }}</span>
            </div>
            @endif
        </div>

        {{-- Queue Progress --}}
        <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
            <h2 class="font-bold text-ink mb-4 flex items-center gap-2">
                <span class="ms ms-fill text-primary">timeline</span> Progress Antrian
            </h2>
            @php
                $stages = ['antri','dicuci','dijemur','disetrika','siap_ambil','selesai'];
                $currentIdx = array_search($order->status, $stages);
            @endphp
            <div class="flex items-center">
                @foreach($stages as $i => $stage)
                @php
                    $done = $currentIdx !== false && $i <= $currentIdx;
                    $current = $order->status === $stage;
                    $icon = \App\Models\Order::$statusIcons[$stage];
                    $label = \App\Models\Order::$statusLabels[$stage];
                @endphp
                <div class="flex flex-col items-center flex-1 relative">
                    @if($i < count($stages)-1)
                    <div class="absolute top-4 left-1/2 w-full h-0.5 {{ $done && !$current ? 'bg-primary' : 'bg-slate-200' }}"></div>
                    @endif
                    <div class="w-8 h-8 rounded-full flex items-center justify-center z-10 {{ $current ? 'bg-primary shadow-glow' : ($done ? 'bg-primary/80' : 'bg-slate-200') }}">
                        <span class="ms ms-fill text-sm {{ $done ? 'text-white' : 'text-ink-faint' }}">{{ $icon }}</span>
                    </div>
                    <span class="text-[10px] text-ink-muted mt-1 text-center leading-tight hidden md:block">{{ $label }}</span>
                </div>
                @endforeach
            </div>

            {{-- Update Status --}}
            @if(!in_array($order->status, ['selesai','dibatalkan']))
            <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="mt-5 flex flex-wrap gap-2">
                @csrf @method('PATCH')
                @php
                    $nextStatuses = array_slice($stages, $currentIdx !== false ? $currentIdx+1 : 0, 2);
                    $prevStatus   = ($currentIdx !== false && $currentIdx > 0) ? $stages[$currentIdx - 1] : null;
                @endphp

                {{-- Tombol Kembali ke status sebelumnya --}}
                @if($prevStatus)
                <button type="button"
                        onclick="openRevertModal('{{ $prevStatus }}', '{{ \App\Models\Order::$statusLabels[$prevStatus] }}')"
                        class="btn-secondary text-sm px-4 py-2">
                    <span class="ms ms-fill text-sm">undo</span>
                    Kembali: {{ \App\Models\Order::$statusLabels[$prevStatus] }}
                </button>
                @endif

                {{-- Tombol maju ke status berikutnya --}}
                @foreach($nextStatuses as $next)
                <button type="submit" name="status" value="{{ $next }}"
                        class="btn-primary text-sm px-4 py-2">
                    <span class="ms ms-fill text-sm">{{ \App\Models\Order::$statusIcons[$next] }}</span>
                    Tandai: {{ \App\Models\Order::$statusLabels[$next] }}
                </button>
                @endforeach

                {{-- Batalkan (buka modal konfirmasi) --}}
                <button type="button"
                        onclick="openCancelModal()"
                        class="btn-danger text-sm px-4 py-2 ml-auto">
                    <span class="ms text-sm">cancel</span> Batalkan
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Right: Payment Summary --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
            <h2 class="font-bold text-ink mb-4 flex items-center gap-2">
                <span class="ms ms-fill text-primary">receipt</span> Ringkasan Biaya
            </h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-ink-muted">Subtotal</span>
                    <span class="font-semibold">Rp {{ number_format($order->subtotal,0,',','.') }}</span>
                </div>
                @if($order->speed_surcharge > 0)
                <div class="flex justify-between">
                    <span class="text-ink-muted">Surcharge ({{ ucfirst($order->speed) }})</span>
                    <span class="font-semibold text-amber-600">+ Rp {{ number_format($order->speed_surcharge,0,',','.') }}</span>
                </div>
                @endif
                @if($order->discount > 0)
                <div class="flex justify-between">
                    <span class="text-ink-muted">Diskon</span>
                    <span class="font-semibold text-red-500">- Rp {{ number_format($order->discount,0,',','.') }}</span>
                </div>
                @endif
                <div class="border-t border-slate-100 pt-3 flex justify-between items-end">
                    <span class="font-bold text-ink">Total</span>
                    <span class="text-2xl font-extrabold text-primary">Rp {{ number_format($order->total,0,',','.') }}</span>
                </div>
            </div>

            <div class="mt-4 p-3 rounded-xl {{ $order->payment_status==='lunas' ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }}">
                <p class="text-sm font-semibold {{ $order->payment_status==='lunas' ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $order->payment_status==='lunas' ? '✅ Sudah Lunas' : '⏳ Belum Lunas' }}
                </p>
                @if($order->payment_status==='lunas' && $order->paid_at)
                <p class="text-xs text-emerald-600 mt-0.5">{{ $order->paid_at->isoFormat('D MMM Y, HH:mm') }} via {{ ucfirst($order->payment_method) }}</p>
                @endif
            </div>

            @if($order->payment_status === 'belum_lunas')
            <form action="{{ route('orders.markPaid', $order) }}" method="POST" class="mt-4">
                @csrf @method('PATCH')
                <select name="payment_method" class="form-input mb-3" required>
                    <option value="">Pilih metode pembayaran</option>
                    <option value="cash">💵 Cash</option>
                    <option value="transfer">🏦 Transfer Bank</option>
                    <option value="qris">📱 QRIS</option>
                    <option value="wallet">👛 Wallet</option>
                </select>
                <button type="submit" class="w-full btn-primary justify-center py-2.5">
                    <span class="ms ms-fill">payments</span> Tandai Lunas
                </button>
            </form>
            @endif
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border text-sm space-y-2">
            <p class="text-xs font-bold text-ink-muted uppercase tracking-wider">Info Order</p>
            <div class="flex justify-between"><span class="text-ink-muted">Dibuat oleh</span><span class="font-medium">{{ $order->created_by ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-ink-muted">Tanggal</span><span class="font-medium">{{ $order->created_at->isoFormat('D MMM Y') }}</span></div>
        </div>
    </div>
</div>

{{-- ── Struk Thermal (hanya tampil saat print) ──────────────── --}}
<div id="print-receipt" style="display:none;">
    <div style="width:280px; margin:0 auto; font-family:'Courier New',monospace; font-size:12px; color:#000;">
        <div style="text-align:center; border-bottom:1px dashed #000; padding-bottom:10px; margin-bottom:10px;">
            <p style="font-size:16px; font-weight:bold; margin:0;">LinenFlow POS</p>
            <p style="margin:2px 0; font-size:11px;">Laundry Management System</p>
            <p style="margin:2px 0; font-size:11px;">================================</p>
        </div>

        <div style="margin-bottom:10px;">
            <p style="margin:2px 0;"><b>No. Order :</b> {{ $order->order_number }}</p>
            <p style="margin:2px 0;"><b>Tanggal   :</b> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p style="margin:2px 0;"><b>Pelanggan :</b> {{ $order->customer_name }}</p>
            <p style="margin:2px 0;"><b>Telp      :</b> {{ $order->customer_phone }}</p>
        </div>

        <p style="margin:4px 0;">--------------------------------</p>

        <div style="margin-bottom:6px;">
            @if($order->items->count() > 0)
                @foreach($order->items as $item)
                <div style="display:flex;justify-content:space-between;margin:4px 0;">
                    <span>{{ $item->name }} ({{ $item->qty }} {{ $item->unit }})</span>
                    <span>Rp {{ number_format($item->subtotal,0,',','.') }}</span>
                </div>
                @endforeach
                @if($order->speed && $order->speed !== 'reguler')
                <p style="margin:4px 0 2px;"><b>Kecepatan:</b> {{ ucfirst($order->speed) }}</p>
                @endif
                @if($order->perfume)
                <p style="margin:2px 0;"><b>Parfum:</b> {{ ucfirst($order->perfume) }}</p>
                @endif
            @else
                @if($order->service_type)
                <p style="margin:2px 0;"><b>Layanan:</b> {{ str_replace('_',' ', ucfirst($order->service_type)) }}</p>
                @endif
                @if($order->weight)
                <p style="margin:2px 0;"><b>Berat:</b> {{ $order->weight }} kg</p>
                @endif
                @if($order->speed)
                <p style="margin:2px 0;"><b>Kecepatan:</b> {{ ucfirst($order->speed) }}</p>
                @endif
                @if($order->perfume)
                <p style="margin:2px 0;"><b>Parfum:</b> {{ ucfirst($order->perfume) }}</p>
                @endif
            @endif
        </div>

        <p style="margin:4px 0;">--------------------------------</p>

        <div style="margin-bottom:6px;">
            <div style="display:flex;justify-content:space-between;">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->subtotal,0,',','.') }}</span>
            </div>
            @if($order->speed_surcharge > 0)
            <div style="display:flex;justify-content:space-between;">
                <span>Surcharge</span>
                <span>Rp {{ number_format($order->speed_surcharge,0,',','.') }}</span>
            </div>
            @endif
            @if($order->discount > 0)
            <div style="display:flex;justify-content:space-between;">
                <span>Diskon</span>
                <span>-Rp {{ number_format($order->discount,0,',','.') }}</span>
            </div>
            @endif
        </div>

        <p style="margin:4px 0;">================================</p>
        <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:bold;">
            <span>TOTAL</span>
            <span>Rp {{ number_format($order->total,0,',','.') }}</span>
        </div>
        <p style="margin:4px 0;">================================</p>

        <div style="margin:8px 0;">
            <p style="margin:2px 0;"><b>Pembayaran:</b> {{ $order->payment_status === 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}</p>
            @if($order->payment_status === 'lunas' && $order->payment_method)
            <p style="margin:2px 0;"><b>Metode:</b> {{ strtoupper($order->payment_method) }}</p>
            @endif
        </div>

        @if($order->estimated_done)
        <p style="margin:4px 0;">--------------------------------</p>
        <p style="margin:2px 0;"><b>Est. Selesai:</b> {{ $order->estimated_done->format('d/m/Y') }}</p>
        @endif

        <div style="text-align:center; margin-top:14px; border-top:1px dashed #000; padding-top:10px;">
            <p style="margin:2px 0;">Terima kasih atas kepercayaan Anda!</p>
            <p style="margin:2px 0; font-size:11px;">Simpan struk ini sebagai bukti pembayaran</p>
        </div>
    </div>
</div>

{{-- ── Struk Produksi (hanya tampil saat print) ──────────────── --}}
<div id="print-production" style="display:none;">
    <div style="width:280px; margin:0 auto; font-family:'Courier New',monospace; font-size:12px; color:#000;">
        <div style="text-align:center; border-bottom:1px dashed #000; padding-bottom:10px; margin-bottom:10px;">
            <p style="font-size:16px; font-weight:bold; margin:0;">NOTA PRODUKSI</p>
            <p style="margin:2px 0; font-size:11px;">LinenFlow POS</p>
            <p style="margin:2px 0; font-size:11px;">================================</p>
        </div>

        <div style="margin-bottom:10px;">
            <p style="margin:2px 0;"><b>No. Order :</b> {{ $order->order_number }}</p>
            <p style="margin:2px 0;"><b>Tanggal   :</b> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p style="margin:2px 0;"><b>Pelanggan :</b> {{ $order->customer_name }}</p>
            <p style="margin:2px 0;"><b>Telp      :</b> {{ $order->customer_phone }}</p>
        </div>

        <p style="margin:4px 0;">--------------------------------</p>

        <div style="margin-bottom:6px;">
            @if($order->items->count() > 0)
                @foreach($order->items as $item)
                <div style="display:flex;justify-content:space-between;margin:4px 0;font-size:13px;font-weight:bold;">
                    <span>{{ $item->name }}</span>
                    <span>{{ $item->qty }} {{ $item->unit }}</span>
                </div>
                @endforeach
            @else
                @if($order->service_type)
                <p style="margin:2px 0;"><b>Layanan:</b> {{ str_replace('_',' ', ucfirst($order->service_type)) }}</p>
                @endif
                @if($order->weight)
                <p style="margin:2px 0;font-size:13px;font-weight:bold;"><b>Berat:</b> {{ $order->weight }} kg</p>
                @endif
            @endif
        </div>

        <p style="margin:4px 0;">--------------------------------</p>

        <div style="margin-bottom:6px; font-size:13px;">
            @if($order->speed)
            <p style="margin:4px 0;"><b>Kecepatan:</b> <span style="background:#000;color:#fff;padding:2px 6px;font-weight:bold;text-transform:uppercase;">{{ $order->speed }}</span></p>
            @endif
            @if($order->perfume)
            <p style="margin:4px 0;"><b>Parfum:</b> <span style="font-weight:bold;text-transform:uppercase;">{{ $order->perfume }}</span></p>
            @endif
            @if($order->ironingStaff)
            <p style="margin:4px 0;"><b>PJ Setrika:</b> {{ $order->ironingStaff->name }}</p>
            @endif
        </div>

        @if($order->notes)
        <p style="margin:4px 0;">--------------------------------</p>
        <div style="margin:6px 0; padding:6px; border:1px solid #000; font-size:13px;">
            <p style="margin:0 0 4px; font-weight:bold;">CATATAN:</p>
            <p style="margin:0; font-weight:bold; line-height:1.4;">{{ $order->notes }}</p>
        </div>
        @endif

        <p style="margin:4px 0;">================================</p>

        @if($order->estimated_done)
        <p style="margin:2px 0; font-size:13px; font-weight:bold;"><b>Est. Selesai:</b> {{ $order->estimated_done->format('d/m/Y') }}</p>
        @endif

        <div style="text-align:center; margin-top:20px; border-top:1px dashed #000; padding-top:10px;">
            <p style="margin:2px 0; font-size:10px;">LinenFlow Laundry System</p>
        </div>
    </div>
</div>
@if(session('new_order'))
{{-- ── Modal Pesanan Baru ─────────────────────────────────────── --}}
<div id="new-order-modal" style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;">
    {{-- Overlay --}}
    <div style="position:absolute;inset:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(6px);" onclick="closeNewOrderModal()"></div>

    {{-- Card --}}
    <div id="modal-card" style="position:relative;background:#fff;border-radius:24px;padding:32px 28px;max-width:360px;width:100%;box-shadow:0 30px 70px rgba(0,0,0,0.3);animation:modalSlideUp .4s cubic-bezier(.34,1.56,.64,1);">

        {{-- Icon & Judul --}}
        <div style="text-align:center;margin-bottom:24px;">
            <div style="width:80px;height:80px;background:linear-gradient(135deg,#0d9488,#14b8a6);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;box-shadow:0 10px 30px rgba(13,148,136,.4);">
                <span class="ms ms-fill" style="font-size:40px;color:#fff;">task_alt</span>
            </div>
            <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0 0 6px;">Pesanan Berhasil Dibuat!</h2>
            <p style="font-size:13px;color:#0d9488;margin:0;font-family:monospace;font-weight:700;letter-spacing:.5px;">{{ $order->order_number }}</p>
            <p style="font-size:12px;color:#94a3b8;margin:5px 0 0;">{{ $order->customer_name }} &nbsp;·&nbsp; Rp {{ number_format($order->total,0,',','.') }}</p>
        </div>

        {{-- Label --}}
        <p style="font-size:11px;font-weight:700;color:#94a3b8;text-align:center;text-transform:uppercase;letter-spacing:1.2px;margin:0 0 12px;">Langkah Selanjutnya</p>

        {{-- Tombol Aksi --}}
        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:14px;">

            {{-- Kirim WhatsApp --}}
            <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank"
               onclick="closeNewOrderModal()"
               style="display:flex;align-items:center;justify-content:center;gap:10px;padding:15px;background:#25D366;color:#fff;border-radius:14px;text-decoration:none;font-size:15px;font-weight:700;transition:transform .15s,box-shadow .15s;box-shadow:0 4px 14px rgba(37,211,102,.35);"
               onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 6px 20px rgba(37,211,102,.5)'"
               onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 14px rgba(37,211,102,.35)'">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Kirim WhatsApp
            </a>

            {{-- Row 1: System Print --}}
            <div style="display:flex;gap:8px;">
                <button onclick="printCustomerReceipt()"
                        style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:12px 8px;background:#0d9488;color:#fff;border-radius:14px;border:none;cursor:pointer;font-size:13px;font-weight:700;font-family:inherit;transition:transform .15s,box-shadow .15s;box-shadow:0 4px 12px rgba(13,148,136,.25);"
                        onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 6px 16px rgba(13,148,136,.4)'"
                        onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 12px rgba(13,148,136,.25)'">
                    <span class="ms ms-fill" style="font-size:18px;">print</span>
                    Struk Pelanggan
                </button>
                <button onclick="printProductionReceipt()"
                        style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:12px 8px;background:#4f46e5;color:#fff;border-radius:14px;border:none;cursor:pointer;font-size:13px;font-weight:700;font-family:inherit;transition:transform .15s,box-shadow .15s;box-shadow:0 4px 12px rgba(79,70,229,.25);"
                        onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 6px 16px rgba(79,70,229,.4)'"
                        onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 12px rgba(79,70,229,.25)'">
                    <span class="ms ms-fill" style="font-size:18px;">receipt_long</span>
                    Nota Produksi
                </button>
            </div>

            {{-- Row 2: Bluetooth Print --}}
            <div style="display:flex;gap:8px;">
                <button onclick="printBluetooth('customer')"
                        style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:12px 8px;background:#2563eb;color:#fff;border-radius:14px;border:none;cursor:pointer;font-size:13px;font-weight:700;font-family:inherit;transition:transform .15s,box-shadow .15s;box-shadow:0 4px 12px rgba(37,99,235,.25);"
                        onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 6px 16px rgba(37,99,235,.4)'"
                        onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 12px rgba(37,99,235,.25)'">
                    <span class="ms ms-fill" style="font-size:18px;">bluetooth</span>
                    BT Pelanggan
                </button>
                <button onclick="printBluetooth('production')"
                        style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:12px 8px;background:#4f46e5;color:#fff;border-radius:14px;border:none;cursor:pointer;font-size:13px;font-weight:700;font-family:inherit;transition:transform .15s,box-shadow .15s;box-shadow:0 4px 12px rgba(79,70,229,.25);"
                        onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 6px 16px rgba(79,70,229,.4)'"
                        onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 12px rgba(79,70,229,.25)'">
                    <span class="ms ms-fill" style="font-size:18px;">receipt_long</span>
                    BT Produksi
                </button>
            </div>
        </div>

        {{-- Tombol Lewati --}}
        <button onclick="closeNewOrderModal()"
                style="width:100%;padding:11px;background:transparent;border:1.5px solid #e2e8f0;border-radius:12px;color:#64748b;font-size:14px;cursor:pointer;font-family:inherit;transition:background .15s,border-color .15s;"
                onmouseover="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'"
                onmouseout="this.style.background='transparent';this.style.borderColor='#e2e8f0'">
            Lewati — Lihat Detail Pesanan
        </button>
    </div>
</div>

<style>
@keyframes modalSlideUp {
    from { opacity:0; transform:translateY(40px) scale(0.9); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
</style>
<script>
function closeNewOrderModal() {
    const modal = document.getElementById('new-order-modal');
    if (!modal) return;
    modal.style.transition = 'opacity .25s';
    modal.style.opacity = '0';
    setTimeout(() => modal.remove(), 260);
}
</script>
@endif

{{-- Modal Konfirmasi Batalkan --}}
<div id="cancelModal" class="hidden" style="position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);">
    <div style="background:#fff;border-radius:20px;box-shadow:0 25px 60px rgba(0,0,0,0.25);max-width:380px;width:100%;padding:28px 24px;text-align:center;animation:modalSlideUp .3s cubic-bezier(.34,1.56,.64,1);">
        <div style="width:56px;height:56px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <span class="ms ms-fill" style="color:#ef4444;font-size:28px;">cancel</span>
        </div>
        <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 8px;">Batalkan Pesanan?</h3>
        <p style="font-size:13px;color:#64748b;margin:0 0 24px;line-height:1.6;">
            Order <span style="font-family:monospace;font-weight:700;color:#0f172a;">{{ $order->order_number }}</span>
            akan dibatalkan. Tindakan ini tidak bisa diurungkan.
        </p>
        <div style="display:flex;gap:10px;">
            <button onclick="closeCancelModal()"
                    style="flex:1;padding:11px;background:#f1f5f9;border:none;border-radius:12px;color:#475569;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .15s;"
                    onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                Tidak, Kembali
            </button>
            <form action="{{ route('orders.updateStatus', $order) }}" method="POST" style="flex:1;">
                @csrf @method('PATCH')
                <button type="submit" name="status" value="dibatalkan"
                        style="width:100%;padding:11px;background:#ef4444;border:none;border-radius:12px;color:#fff;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .15s;"
                        onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                    Ya, Batalkan
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Kembali Status --}}
<div id="revertModal" class="hidden" style="position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);">
    <div style="background:#fff;border-radius:20px;box-shadow:0 25px 60px rgba(0,0,0,0.25);max-width:380px;width:100%;padding:28px 24px;text-align:center;animation:modalSlideUp .3s cubic-bezier(.34,1.56,.64,1);">
        <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <span class="ms ms-fill" style="color:#f59e0b;font-size:28px;">undo</span>
        </div>
        <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 8px;">Kembalikan Status?</h3>
        <p style="font-size:13px;color:#64748b;margin:0 0 24px;line-height:1.6;">
            Status akan dikembalikan ke <span id="revertStatusLabel" style="font-weight:700;color:#0f172a;"></span>.
        </p>
        <div style="display:flex;gap:10px;">
            <button onclick="closeRevertModal()"
                    style="flex:1;padding:11px;background:#f1f5f9;border:none;border-radius:12px;color:#475569;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .15s;"
                    onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                Batal
            </button>
            <form id="revertForm" action="{{ route('orders.updateStatus', $order) }}" method="POST" style="flex:1;">
                @csrf @method('PATCH')
                <input type="hidden" id="revertStatusInput" name="status" value="">
                <button type="submit"
                        style="width:100%;padding:11px;background:#0d9488;border:none;border-radius:12px;color:#fff;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .15s;"
                        onmouseover="this.style.background='#0f766e'" onmouseout="this.style.background='#0d9488'">
                    Ya, Kembalikan
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
    @keyframes modalSlideUp {
        from { opacity:0; transform:translateY(30px) scale(0.95); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
    @media print {
        @page {
            margin: 0;
        }
        body {
            margin: 0;
        }
        /* Sembunyikan semua elemen kecuali struk */
        body * { visibility: hidden !important; }
        
        /* Mode Struk Pelanggan (Default) */
        body:not(.print-production-mode) #print-receipt, 
        body:not(.print-production-mode) #print-receipt * { 
            visibility: visible !important; 
        }
        body:not(.print-production-mode) #print-receipt {
            display: block !important;
            position: fixed !important;
            top: 0 !important; left: 0 !important;
            width: 100% !important;
        }

        /* Mode Nota Produksi */
        body.print-production-mode #print-production, 
        body.print-production-mode #print-production * { 
            visibility: visible !important; 
        }
        body.print-production-mode #print-production {
            display: block !important;
            position: fixed !important;
            top: 0 !important; left: 0 !important;
            width: 100% !important;
        }
        
        #mobile-header, #mobile-bottom-nav,
        #sidebar, .desktop-header { display: none !important; }
    }
</style>
<script id="order-data" type="application/json">
{
    "order_number": "{{ $order->order_number }}",
    "created_at": "{{ $order->created_at->format('d/m/Y H:i') }}",
    "customer_name": "{{ $order->customer_name }}",
    "customer_phone": "{{ $order->customer_phone }}",
    "service_type": "{{ str_replace('_',' ', ucfirst($order->service_type)) }}",
    "weight": "{{ $order->weight }}",
    "perfume": "{{ ucfirst($order->perfume) }}",
    "speed": "{{ ucfirst($order->speed) }}",
    "subtotal": {{ $order->subtotal }},
    "speed_surcharge": {{ $order->speed_surcharge }},
    "discount": {{ $order->discount }},
    "total": {{ $order->total }},
    "payment_status": "{{ $order->payment_status }}",
    "payment_method": "{{ $order->payment_method ?? '' }}",
    "estimated_done": "{{ $order->estimated_done ? $order->estimated_done->format('d/m/Y') : '' }}",
    "notes": {!! json_encode($order->notes) !!},
    "ironing_staff": {!! json_encode($order->ironingStaff ? $order->ironingStaff->name : '') !!},
    "items": [
        @foreach($order->items as $item)
        {
            "name": "{{ $item->name }}",
            "qty": "{{ $item->qty }}",
            "unit": "{{ $item->unit }}",
            "price": {{ $item->price }},
            "subtotal": {{ $item->subtotal }}
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>
<script>
let lastPrintType = 'customer';

function printCustomerReceipt() {
    document.body.classList.remove('print-production-mode');
    window.print();
}

function printProductionReceipt() {
    document.body.classList.add('print-production-mode');
    window.print();
}

function openCancelModal() {
    const m = document.getElementById('cancelModal');
    m.style.display = 'flex';
    m.classList.remove('hidden');
}
function closeCancelModal() {
    const m = document.getElementById('cancelModal');
    m.style.display = 'none';
    m.classList.add('hidden');
}
function openRevertModal(status, label) {
    document.getElementById('revertStatusLabel').textContent = '"' + label + '"';
    document.getElementById('revertStatusInput').value = status;
    const m = document.getElementById('revertModal');
    m.style.display = 'flex';
    m.classList.remove('hidden');
}
function closeRevertModal() {
    const m = document.getElementById('revertModal');
    m.style.display = 'none';
    m.classList.add('hidden');
}

// ── Bluetooth Thermal Printer (ESC/POS) Integration ───────────────────
class EscPosEncoder {
    constructor() {
        this.buffer = [];
    }

    initialize() {
        this.buffer.push(0x1B, 0x40);
        return this;
    }

    alignCenter() {
        this.buffer.push(0x1B, 0x61, 0x01);
        return this;
    }

    alignLeft() {
        this.buffer.push(0x1B, 0x61, 0x00);
        return this;
    }

    alignRight() {
        this.buffer.push(0x1B, 0x61, 0x02);
        return this;
    }

    bold(enabled) {
        this.buffer.push(0x1B, 0x45, enabled ? 0x01 : 0x00);
        return this;
    }

    size(width, height) {
        let val = 0;
        if (width) val |= 0x10;
        if (height) val |= 0x01;
        this.buffer.push(0x1D, 0x21, val);
        return this;
    }

    text(str) {
        const encoder = new TextEncoder();
        const bytes = encoder.encode(str);
        for (let i = 0; i < bytes.length; i++) {
            this.buffer.push(bytes[i]);
        }
        return this;
    }

    line(str = "") {
        this.text(str + "\n");
        return this;
    }

    feed(lines = 1) {
        for (let i = 0; i < lines; i++) {
            this.buffer.push(0x0A);
        }
        return this;
    }

    getBytes() {
        return new Uint8Array(this.buffer);
    }
}

function formatRow(left, right, maxLen = 32) {
    const spaceCount = maxLen - left.length - right.length;
    if (spaceCount <= 0) {
        return left + " " + right;
    }
    return left + " ".repeat(spaceCount) + right;
}

let printerCharacteristic = null;
let printerDevice = null;
const PRINTER_SERVICE_UUID = '000018f0-0000-1000-8000-00805f9b34fb';

function bytesToBase64(byteArray) {
    let binary = '';
    const len = byteArray.length;
    for (let i = 0; i < len; i++) {
        binary += String.fromCharCode(byteArray[i]);
    }
    return window.btoa(binary);
}

function showAndroidDeviceSelector(devices) {
    const existing = document.getElementById('androidDeviceSelectorModal');
    if (existing) existing.remove();

    const modal = document.createElement('div');
    modal.id = 'androidDeviceSelectorModal';
    modal.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,0.6);display:flex;align-items:center;justify-content:center;z-index:9999;backdrop-filter:blur(4px);padding:20px;';

    let listHtml = '';
    devices.forEach((d) => {
        listHtml += `
            <div onclick="selectAndroidDevice('${d.address}', '${escapeJs(d.name)}')" style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;border:1px solid #e2e8f0;cursor:pointer;margin-bottom:8px;transition:all 0.2s;text-align:left;background:#fff;">
                <span style="font-size:20px;">🖨️</span>
                <div style="flex-grow:1;">
                    <div style="font-weight:700;font-size:14px;color:#0f172a;">${d.name || 'Printer Bluetooth'}</div>
                    <div style="font-size:12px;color:#64748b;">${d.address}</div>
                </div>
                <span style="color:#2563eb;font-weight:600;font-size:13px;">Pilih ›</span>
            </div>
        `;
    });

    modal.innerHTML = `
        <div style="background:#fff;border-radius:16px;width:100%;max-width:360px;padding:24px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);text-align:center;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0;">Pilih Printer Bluetooth</h3>
                <button onclick="document.getElementById('androidDeviceSelectorModal').remove()" style="background:none;border:none;font-size:18px;cursor:pointer;color:#64748b;margin-left:auto;padding:4px;">✕</button>
            </div>
            <p style="font-size:12px;color:#64748b;margin-bottom:16px;text-align:left;">Pilih printer yang sudah dipasangkan (paired) di perangkat Android Anda.</p>
            <div style="max-height:240px;overflow-y:auto;">
                ${listHtml}
            </div>
        </div>
    `;

    document.body.appendChild(modal);
}

function escapeJs(str) {
    if (!str) return '';
    return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
}

function selectAndroidDevice(address, name) {
    const modal = document.getElementById('androidDeviceSelectorModal');
    if (modal) modal.remove();

    updateBluetoothStatus('connecting', 'Koneksi...');
    
    setTimeout(() => {
        try {
            const ok = AndroidPrint.connectDevice(address);
            if (ok) {
                updateBluetoothStatus('connected', name);
                
                // Save settings
                const settings = { deviceName: name, deviceAddress: address };
                localStorage.setItem('jm_printer_settings', JSON.stringify(settings));
                
                // Trigger printing automatically after connection is established
                printBluetooth();
            } else {
                throw new Error('Koneksi gagal. Pastikan printer aktif.');
            }
        } catch(err) {
            updateBluetoothStatus('disconnected', 'Printer Off');
            alert('Gagal terhubung ke printer: ' + err.message);
        }
    }, 100);
}

function updateBluetoothStatus(status, text) {
    const badge = document.getElementById('bt-status-badge');
    const label = document.getElementById('bt-status-label');
    const icon = document.getElementById('bt-icon');
    if (!badge || !label) return;

    label.textContent = text;
    
    badge.className = 'badge';
    if (status === 'connected') {
        badge.style.background = '#ecfdf5';
        badge.style.color = '#047857';
        badge.style.border = '1px solid #a7f3d0';
        if (icon) {
            icon.style.background = '#10b981';
            icon.style.color = '#fff';
        }
    } else if (status === 'connecting') {
        badge.style.background = '#fffbeb';
        badge.style.color = '#b45309';
        badge.style.border = '1px solid #fde68a';
        if (icon) {
            icon.style.background = '#f59e0b';
            icon.style.color = '#fff';
        }
    } else {
        badge.style.background = '#f8fafc';
        badge.style.color = '#64748b';
        badge.style.border = '1px solid #e2e8f0';
        if (icon) {
            icon.style.background = '#e2e8f0';
            icon.style.color = '#475569';
        }
    }
}

function onDisconnected() {
    printerCharacteristic = null;
    printerDevice = null;
    updateBluetoothStatus('disconnected', 'Printer Off');
}

async function connectBluetoothPrinter() {
    try {
        updateBluetoothStatus('connecting', 'Mencari...');
        
        let device = null;
        // 1. Cek apakah ada printer yang sudah diizinkan sebelumnya
        if (navigator.bluetooth && navigator.bluetooth.getDevices) {
            const devices = await navigator.bluetooth.getDevices();
            if (devices.length > 0) {
                device = devices[0];
            }
        }

        // 2. Jika tidak ada, minta user memilih
        if (!device) {
            device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: [PRINTER_SERVICE_UUID]
            });
        }

        printerDevice = device;
        device.addEventListener('gattserverdisconnected', onDisconnected);

        updateBluetoothStatus('connecting', 'Koneksi...');
        const server = await device.gatt.connect();

        let service;
        try {
            service = await server.getPrimaryService(PRINTER_SERVICE_UUID);
        } catch (e) {
            const services = await server.getPrimaryServices();
            if (services.length > 0) {
                service = services[0];
            } else {
                throw new Error("GATT Service tidak ditemukan");
            }
        }

        const characteristics = await service.getCharacteristics();
        printerCharacteristic = characteristics.find(c => c.properties.write || c.properties.writeWithoutResponse);

        if (!printerCharacteristic) {
            throw new Error("Write characteristic tidak ditemukan");
        }

        updateBluetoothStatus('connected', device.name || 'Printer OK');
        return true;
    } catch (error) {
        console.error("Bluetooth error:", error);
        onDisconnected();
        if (error.name === 'NotFoundError' || error.message.includes('cancelled') || error.message.includes('user cancelled') || error.message.includes('User cancelled')) {
            alert("Pencarian printer dibatalkan atau printer tidak terdeteksi.\n\nTips:\n1. Pastikan Bluetooth perangkat Anda menyala.\n2. Hubungkan langsung via Pengaturan Bluetooth, lalu coba lagi.");
        } else {
            alert("Gagal koneksi printer: " + error.message);
        }
        return false;
    }
}

async function toggleBluetoothPrinter() {
    if (typeof AndroidPrint !== 'undefined') {
        if (AndroidPrint.isConnected()) {
            AndroidPrint.disconnectDevice();
            onDisconnected();
        } else {
            try {
                const devicesJson = AndroidPrint.getPairedDevices();
                const devices = JSON.parse(devicesJson);
                if (devices.length === 0) {
                    alert('Tidak ada printer Bluetooth terpasang (paired) di HP/Tablet Anda. Silakan pasangkan via Pengaturan Bluetooth terlebih dahulu.');
                    return;
                }
                showAndroidDeviceSelector(devices);
            } catch (e) {
                alert('Gagal memindai printer Android: ' + e.message);
            }
        }
        return;
    }

    if (printerDevice && printerDevice.gatt.connected) {
        printerDevice.gatt.disconnect();
        onDisconnected();
    } else {
        await connectBluetoothPrinter();
    }
}

async function printBluetooth(type) {
    if (type) {
        lastPrintType = type;
    } else {
        type = lastPrintType;
    }

    if (typeof AndroidPrint === 'undefined' && !navigator.bluetooth) {
        alert("Web Bluetooth API tidak didukung di browser ini. Gunakan Google Chrome atau jalankan aplikasi dari HP.");
        return;
    }

    if (typeof AndroidPrint !== 'undefined') {
        if (!AndroidPrint.isConnected()) {
            const saved = JSON.parse(localStorage.getItem('jm_printer_settings') || '{}');
            if (saved.deviceAddress) {
                updateBluetoothStatus('connecting', 'Koneksi...');
                const ok = AndroidPrint.connectDevice(saved.deviceAddress);
                if (ok) {
                    updateBluetoothStatus('connected', saved.deviceName);
                } else {
                    updateBluetoothStatus('disconnected', 'Printer Off');
                    try {
                        const devicesJson = AndroidPrint.getPairedDevices();
                        const devices = JSON.parse(devicesJson);
                        if (devices.length === 0) {
                            alert('Gagal menghubungkan ke printer tersimpan. Tidak ada printer terpasang di HP.');
                            return;
                        }
                        showAndroidDeviceSelector(devices);
                        return;
                    } catch (e) {
                        alert('Gagal memindai printer: ' + e.message);
                        return;
                    }
                }
            } else {
                try {
                    const devicesJson = AndroidPrint.getPairedDevices();
                    const devices = JSON.parse(devicesJson);
                    if (devices.length === 0) {
                        alert('Tidak ada printer Bluetooth terpasang (paired) di HP/Tablet Anda. Silakan pasangkan via Pengaturan Bluetooth terlebih dahulu.');
                        return;
                    }
                    showAndroidDeviceSelector(devices);
                    return;
                } catch (e) {
                    alert('Gagal memindai printer: ' + e.message);
                    return;
                }
            }
        }
    } else {
        if (!printerCharacteristic) {
            const connected = await connectBluetoothPrinter();
            if (!connected) return;
        }
    }

    try {
        const orderData = JSON.parse(document.getElementById('order-data').textContent);
        const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(num);
        const encoder = new EscPosEncoder();
        
        encoder.initialize();
        
        if (type === 'production') {
            // Header
            encoder.alignCenter().bold(true).size(1, 1).line("NOTA PRODUKSI").size(0, 0).bold(false);
            encoder.line("LinenFlow");
            encoder.line("================================");

            // Metadata
            encoder.alignLeft();
            encoder.line(`No. Order : ${orderData.order_number}`);
            encoder.line(`Tanggal   : ${orderData.created_at}`);
            encoder.line(`Pelanggan : ${orderData.customer_name}`);
            encoder.line(`Telp      : ${orderData.customer_phone}`);
            encoder.line("--------------------------------");

            // Items/Layanan
            if (orderData.items && orderData.items.length > 0) {
                orderData.items.forEach(item => {
                    const qtyStr = `${item.qty} ${item.unit}`;
                    encoder.bold(true).line(formatRow(`${item.name}`, qtyStr)).bold(false);
                });
            } else {
                if (orderData.service_type) encoder.line(`Layanan: ${orderData.service_type}`);
                if (orderData.weight) encoder.bold(true).line(`Berat: ${orderData.weight} kg`).bold(false);
            }
            encoder.line("--------------------------------");

            // Kecepatan & Parfum (Emphasized/Bold)
            if (orderData.speed) {
                encoder.bold(true).line(`KECEPATAN: ${orderData.speed.toUpperCase()}`).bold(false);
            }
            if (orderData.perfume) {
                encoder.bold(true).line(`PARFUM   : ${orderData.perfume.toUpperCase()}`).bold(false);
            }
            if (orderData.ironing_staff) {
                encoder.line(`PJ Setrika: ${orderData.ironing_staff}`);
            }

            if (orderData.notes) {
                encoder.line("--------------------------------");
                encoder.bold(true).line("CATATAN:").bold(false);
                encoder.bold(true).line(orderData.notes).bold(false);
            }
            
            if (orderData.estimated_done) {
                encoder.line("--------------------------------");
                encoder.line(`Est. Selesai: ${orderData.estimated_done}`);
            }

            encoder.line("================================");
            encoder.feed(4); // spasi potong kertas
        } else {
            // Header
            encoder.alignCenter().bold(true).size(1, 1).line("LinenFlow").size(0, 0).bold(false);
            encoder.line("Laundry Management System");
            encoder.line("================================");

            // Metadata
            encoder.alignLeft();
            encoder.line(`No. Order : ${orderData.order_number}`);
            encoder.line(`Tanggal   : ${orderData.created_at}`);
            encoder.line(`Pelanggan : ${orderData.customer_name}`);
            encoder.line(`Telp      : ${orderData.customer_phone}`);
            encoder.line("--------------------------------");

            // Items/Layanan
            if (orderData.items && orderData.items.length > 0) {
                orderData.items.forEach(item => {
                    const qtyStr = `${item.qty} ${item.unit}`;
                    encoder.line(formatRow(`${item.name} (${qtyStr})`, `Rp ${formatNumber(item.subtotal)}`));
                });
                if (orderData.speed && orderData.speed !== 'Reguler') encoder.line(`Kecepatan: ${orderData.speed}`);
                if (orderData.perfume) encoder.line(`Parfum: ${orderData.perfume}`);
            } else {
                if (orderData.service_type) encoder.line(`Layanan: ${orderData.service_type}`);
                if (orderData.weight) encoder.line(`Berat: ${orderData.weight} kg`);
                if (orderData.speed) encoder.line(`Kecepatan: ${orderData.speed}`);
                if (orderData.perfume) encoder.line(`Parfum: ${orderData.perfume}`);
            }
            encoder.line("--------------------------------");

            // Biaya
            encoder.line(formatRow("Subtotal", `Rp ${formatNumber(orderData.subtotal)}`));
            if (orderData.speed_surcharge > 0) {
                encoder.line(formatRow("Surcharge", `Rp ${formatNumber(orderData.speed_surcharge)}`));
            }
            if (orderData.discount > 0) {
                encoder.line(formatRow("Diskon", `-Rp ${formatNumber(orderData.discount)}`));
            }
            encoder.line("================================");

            // Total
            encoder.bold(true);
            encoder.line(formatRow("TOTAL", `Rp ${formatNumber(orderData.total)}`));
            encoder.bold(false);
            encoder.line("================================");

            // Status Bayar
            encoder.line(`Pembayaran: ${orderData.payment_status === 'lunas' ? 'LUNAS' : 'BELUM LUNAS'}`);
            if (orderData.payment_status === 'lunas' && orderData.payment_method) {
                encoder.line(`Metode    : ${orderData.payment_method.toUpperCase()}`);
            }

            if (orderData.estimated_done) {
                encoder.line("--------------------------------");
                encoder.line(`Est. Selesai: ${orderData.estimated_done}`);
            }

            encoder.feed(1);
            encoder.alignCenter();
            encoder.line("Terima kasih atas");
            encoder.line("kepercayaan Anda!");
            encoder.line("Simpan struk ini sebagai");
            encoder.line("bukti pembayaran");
            encoder.feed(4); // spasi potong kertas
        }
        
        const bytes = encoder.getBytes();
        
        if (typeof AndroidPrint !== 'undefined') {
            const base64Str = bytesToBase64(bytes);
            const ok = AndroidPrint.printBase64(base64Str);
            if (!ok) throw new Error("Gagal mencetak dari aplikasi.");
            return;
        }
        
        // Kirim dalam potongan 20 bytes (Untuk Web Bluetooth)
        const chunkSize = 20;
        for (let i = 0; i < bytes.length; i += chunkSize) {
            const chunk = bytes.slice(i, i + chunkSize);
            await printerCharacteristic.writeValue(chunk);
            await new Promise(resolve => setTimeout(resolve, 30));
        }
        
    } catch (error) {
        console.error("Print gagal:", error);
        alert("Gagal mencetak: " + error.message);
    }
}

// Cek otomatis printer terpasang di background saat load
window.addEventListener('DOMContentLoaded', async () => {
    if (typeof AndroidPrint !== 'undefined') {
        if (AndroidPrint.isConnected()) {
            updateBluetoothStatus('connected', AndroidPrint.getConnectedDeviceName());
        } else {
            // Auto connect if we have saved printer info
            const saved = JSON.parse(localStorage.getItem('jm_printer_settings') || '{}');
            if (saved.deviceAddress) {
                const ok = AndroidPrint.connectDevice(saved.deviceAddress);
                if (ok) {
                    updateBluetoothStatus('connected', saved.deviceName);
                } else {
                    updateBluetoothStatus('disconnected', 'Printer Off');
                }
            } else {
                updateBluetoothStatus('disconnected', 'Printer Off');
            }
        }
        return;
    }

    if (navigator.bluetooth && navigator.bluetooth.getDevices) {
        try {
            const devices = await navigator.bluetooth.getDevices();
            if (devices.length > 0) {
                updateBluetoothStatus('disconnected', `Siap: ${devices[0].name || 'Printer'}`);
            }
        } catch (e) {
            console.error("Cek auto-connect gagal:", e);
        }
    }
});
</script>
@endpush
