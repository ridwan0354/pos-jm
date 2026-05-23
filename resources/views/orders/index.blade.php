@extends('layouts.app')
@section('title', 'Daftar Pesanan')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-ink">Daftar Pesanan</h1>
        <p class="text-xs text-ink-muted">{{ $orders->total() }} pesanan ditemukan</p>
    </div>
    <a href="{{ route('orders.create') }}" class="btn-primary">
        <span class="ms">add</span> Pesanan Baru
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-2xl p-4 shadow-card border border-surface-border mb-5">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="col-span-2 md:col-span-1">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-input" placeholder="🔍 Cari order / pelanggan…">
        </div>
        <select name="status" class="form-input">
            <option value="">Semua Status</option>
            @foreach(\App\Models\Order::$statusLabels as $val => $label)
            <option value="{{ $val }}" {{ request('status')===$val?'selected':'' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="payment_status" class="form-input">
            <option value="">Semua Pembayaran</option>
            <option value="lunas" {{ request('payment_status')==='lunas'?'selected':'' }}>Lunas</option>
            <option value="belum_lunas" {{ request('payment_status')==='belum_lunas'?'selected':'' }}>Belum Lunas</option>
        </select>
        <div class="flex gap-2">
            <input type="date" name="date" value="{{ request('date') }}" class="form-input flex-1">
            <button type="submit" class="btn-primary px-4">
                <span class="ms">search</span>
            </button>
            @if(request()->hasAny(['search','status','payment_status','date']))
            <a href="{{ route('orders.index') }}" class="btn-secondary px-4"><span class="ms">close</span></a>
            @endif
        </div>
    </div>
</form>

{{-- Status Quick Filters --}}
<div class="flex gap-2 overflow-x-auto pb-2 mb-4">
    @php
        $statusItems = [''=>'Semua'] + \App\Models\Order::$statusLabels;
    @endphp
    @foreach($statusItems as $val => $label)
    <a href="{{ route('orders.index', array_merge(request()->except('status','page'), $val ? ['status'=>$val] : [])) }}"
       class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold border transition-all
              {{ request('status')===$val ? 'bg-primary text-white border-primary' : 'bg-white text-ink-muted border-surface-border hover:border-primary/40' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

{{-- Orders Table --}}
<div class="bg-white rounded-2xl shadow-card border border-surface-border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-ink-muted uppercase tracking-wider border-b border-surface-border">
                <tr>
                    <th class="px-5 py-3 text-left">No. Order</th>
                    <th class="px-4 py-3 text-left">Pelanggan</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Layanan</th>
                    <th class="px-4 py-3 text-right hidden md:table-cell">Total</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Bayar</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Tanggal</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="table-row">
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs font-semibold text-primary">{{ $order->order_number }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <p class="font-medium text-ink">{{ $order->customer_name }}</p>
                        <p class="text-xs text-ink-muted">{{ $order->customer_phone }}</p>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <p class="text-ink capitalize">{{ str_replace('_',' ', $order->service_type ?? $order->category) }}</p>
                        @if($order->weight)
                        <p class="text-xs text-ink-muted">{{ $order->weight }} kg</p>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-right hidden md:table-cell font-semibold">
                        Rp {{ number_format($order->total,0,',','.') }}
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="badge {{ $order->status_color }}">
                            <span class="ms ms-fill text-xs">{{ $order->status_icon }}</span>
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        @if($order->payment_status === 'lunas')
                            <span class="badge bg-emerald-50 text-emerald-700">✓ Lunas</span>
                        @else
                            <span class="badge bg-amber-50 text-amber-700">⏳ Belum</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell text-xs text-ink-muted">
                        {{ $order->created_at->diffForHumans() }}
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('orders.show', $order) }}" class="btn-secondary py-1.5 px-3 text-xs">
                            <span class="ms text-sm">visibility</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center">
                        <span class="ms ms-fill text-4xl text-ink-faint">receipt_long</span>
                        <p class="text-ink-muted mt-2">Tidak ada pesanan ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="px-5 py-4 border-t border-surface-border flex items-center justify-between">
        <p class="text-xs text-ink-muted">
            Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }}
        </p>
        {{ $orders->links('pagination::simple-tailwind') }}
    </div>
    @endif
</div>
@endsection
