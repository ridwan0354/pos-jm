@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- ── Stat Cards ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl p-5 border border-surface-border shadow-card">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-ink-muted uppercase tracking-wider">Order Hari Ini</span>
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                <span class="ms ms-fill text-blue-600" style="font-size:18px;">shopping_bag</span>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-ink">{{ $todayOrders }}</p>
        <p class="text-xs text-ink-muted mt-1">pesanan masuk</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-surface-border shadow-card">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-ink-muted uppercase tracking-wider">Pendapatan Hari Ini</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                <span class="ms ms-fill text-emerald-600" style="font-size:18px;">payments</span>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-ink">Rp {{ number_format($todayRevenue,0,',','.') }}</p>
        <p class="text-xs text-ink-muted mt-1">sudah lunas</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-surface-border shadow-card">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-ink-muted uppercase tracking-wider">Antrian Aktif</span>
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                <span class="ms ms-fill text-amber-600" style="font-size:18px;">pending_actions</span>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-ink">{{ $pendingOrders }}</p>
        <p class="text-xs text-ink-muted mt-1">sedang diproses</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-surface-border shadow-card">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-ink-muted uppercase tracking-wider">Siap Diambil</span>
            <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center">
                <span class="ms ms-fill text-green-600" style="font-size:18px;">done_all</span>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-primary">{{ $siapAmbil }}</p>
        <p class="text-xs text-ink-muted mt-1">menunggu pelanggan</p>
    </div>
</div>

{{-- ── Revenue + Queue ─────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Revenue Chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-surface-border shadow-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-bold text-ink">Pendapatan 7 Hari</h2>
                <p class="text-xs text-ink-muted">Rp {{ number_format($monthlyRevenue,0,',','.') }} bulan ini</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-primary hover:underline">Lihat Laporan →</a>
        </div>
        <div style="display:flex;align-items:flex-end;gap:8px;height:120px;">
            @php $maxRev = max(array_column($revenueChart,'revenue')) ?: 1; @endphp
            @foreach($revenueChart as $day)
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
                <span style="font-size:10px;color:#94a3b8;font-weight:600;">Rp{{ number_format($day['revenue']/1000,0) }}k</span>
                <div style="width:100%;background:{{ $day['revenue']>0 ? 'linear-gradient(180deg,#0d9488,#0891b2)' : '#e2e8f0' }};border-radius:6px 6px 0 0;height:{{ max(8, intval($day['revenue']/$maxRev*96)) }}px;transition:height .3s;"></div>
                <span style="font-size:10px;color:#94a3b8;">{{ $day['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Queue Status --}}
    <div class="bg-white rounded-2xl p-5 border border-surface-border shadow-card">
        <h2 class="font-bold text-ink mb-4">Status Antrian</h2>
        @php
            $statuses = [
                'antri'      => ['label'=>'Antri',      'color'=>'#94a3b8', 'icon'=>'hourglass_empty'],
                'dicuci'     => ['label'=>'Dicuci',     'color'=>'#3b82f6', 'icon'=>'water_drop'],
                'dijemur'    => ['label'=>'Dijemur',    'color'=>'#f59e0b', 'icon'=>'wb_sunny'],
                'disetrika'  => ['label'=>'Disetrika',  'color'=>'#8b5cf6', 'icon'=>'iron'],
                'siap_ambil' => ['label'=>'Siap Ambil', 'color'=>'#10b981', 'icon'=>'task_alt'],
            ];
            $total = $queue->sum() ?: 1;
        @endphp
        <div class="space-y-3">
            @foreach($statuses as $key => $info)
            @php $count = $queue[$key] ?? 0; @endphp
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background:{{ $info['color'] }}18;">
                    <span class="ms ms-fill" style="color:{{ $info['color'] }};font-size:15px;">{{ $info['icon'] }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-semibold text-ink">{{ $info['label'] }}</span>
                        <span class="text-xs font-bold" style="color:{{ $info['color'] }};">{{ $count }}</span>
                    </div>
                    <div style="height:5px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
                        <div style="height:100%;width:{{ intval($count/$total*100) }}%;background:{{ $info['color'] }};border-radius:99px;transition:width .4s;"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Recent Orders + Alerts ──────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Recent Orders --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-surface-border shadow-card overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-surface-border">
            <h2 class="font-bold text-ink">Pesanan Terbaru</h2>
            <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-primary hover:underline">Lihat Semua →</a>
        </div>
        <div>
            @forelse($recentOrders as $order)
            <a href="{{ route('orders.show', $order) }}"
               class="table-row flex items-center gap-3 px-5 py-3 no-underline">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-primary font-mono truncate">{{ $order->order_number }}</p>
                    <p class="text-xs text-ink-muted truncate">{{ $order->customer_name }}</p>
                </div>
                <div class="text-right">
                    <span class="badge {{ $order->status_color }} text-xs">
                        <span class="ms ms-fill" style="font-size:12px;">{{ $order->status_icon }}</span>
                        {{ $order->status_label }}
                    </span>
                    <p class="text-xs text-ink-muted mt-1">Rp {{ number_format($order->total,0,',','.') }}</p>
                </div>
            </a>
            @empty
            <div class="p-8 text-center text-ink-muted">
                <span class="ms" style="font-size:40px;display:block;margin-bottom:8px;color:#e2e8f0;">receipt_long</span>
                <p class="text-sm">Belum ada pesanan</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="space-y-4">

        {{-- Unpaid --}}
        <div class="bg-white rounded-2xl p-5 border border-surface-border shadow-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                    <span class="ms ms-fill text-red-500" style="font-size:18px;">money_off</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-ink-muted uppercase tracking-wider">Belum Lunas</p>
                    <p class="text-lg font-extrabold text-red-500">Rp {{ number_format($unpaidTotal,0,',','.') }}</p>
                </div>
            </div>
            <a href="{{ route('orders.index') }}?payment=belum_lunas" class="text-xs font-semibold text-red-500 hover:underline">Lihat pesanan belum lunas →</a>
        </div>

        {{-- Low Stock --}}
        <div class="bg-white rounded-2xl border border-surface-border shadow-card overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-surface-border">
                <h3 class="text-sm font-bold text-ink flex items-center gap-2">
                    <span class="ms ms-fill text-amber-500" style="font-size:16px;">warning</span>
                    Stok Menipis
                </h3>
                <a href="{{ route('stocks.index') }}" class="text-xs font-semibold text-primary hover:underline">Kelola →</a>
            </div>
            @forelse($lowStocks as $stock)
            <div class="flex items-center justify-between px-4 py-2.5 border-b border-surface-border last:border-0">
                <p class="text-sm font-semibold text-ink">{{ $stock->name }}</p>
                <span class="badge bg-red-50 text-red-600" style="border:1px solid #fecaca;">
                    {{ $stock->quantity }} {{ $stock->unit }}
                </span>
            </div>
            @empty
            <div class="p-4 text-center text-xs text-emerald-600">
                <span class="ms ms-fill" style="color:#22c55e;font-size:24px;display:block;margin-bottom:4px;">check_circle</span>
                Semua stok aman
            </div>
            @endforelse
        </div>

        {{-- Customers --}}
        <div class="bg-white rounded-2xl p-5 border border-surface-border shadow-card">
            <h3 class="text-xs font-bold text-ink-muted uppercase tracking-wider mb-3">Pelanggan Bulan Ini</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-blue-50 rounded-xl p-3 text-center">
                    <p class="text-2xl font-extrabold text-blue-600">{{ $newCustomers }}</p>
                    <p class="text-xs text-ink-muted mt-1">Pelanggan Baru</p>
                </div>
                <div class="bg-primary/10 rounded-xl p-3 text-center">
                    <p class="text-2xl font-extrabold text-primary">{{ $returningCustomers }}</p>
                    <p class="text-xs text-ink-muted mt-1">Returning</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
