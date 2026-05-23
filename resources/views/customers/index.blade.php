@extends('layouts.app')
@section('title', 'Pelanggan')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-ink">Pelanggan</h1>
        <p class="text-xs text-ink-muted">{{ $customers->total() }} pelanggan terdaftar</p>
    </div>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="btn-primary">
        <span class="ms">person_add</span> Tambah Pelanggan
    </button>
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-2xl p-4 shadow-card border border-surface-border mb-5 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
           class="form-input flex-1 min-w-48" placeholder="🔍 Cari nama, HP, atau kode referral…">
    <select name="level" class="form-input w-auto">
        <option value="">Semua Level</option>
        @foreach(['Bronze','Silver','Gold'] as $lvl)
        <option value="{{ $lvl }}" {{ request('level')===$lvl?'selected':'' }}>{{ $lvl }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary px-5"><span class="ms">search</span></button>
    @if(request()->hasAny(['search','level']))
    <a href="{{ route('customers.index') }}" class="btn-secondary px-4"><span class="ms">close</span></a>
    @endif
</form>

{{-- Customer Cards (Mobile) / Table (Desktop) --}}
<div class="block md:hidden space-y-3">
    @forelse($customers as $c)
    <a href="{{ route('customers.show',$c) }}"
       class="block bg-white rounded-2xl p-4 shadow-card border border-surface-border hover:shadow-card-hover transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold flex-shrink-0">
                {{ strtoupper(substr($c->name,0,1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="font-semibold text-ink truncate">{{ $c->name }}</p>
                    <span class="badge {{ $c->member_badge_color }} flex-shrink-0">{{ $c->member_level }}</span>
                </div>
                <p class="text-xs text-ink-muted">{{ $c->phone }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-xs font-bold text-ink">{{ $c->orders_count }} order</p>
                <p class="text-xs text-ink-muted">Rp {{ number_format($c->wallet_balance,0,',','.') }}</p>
            </div>
        </div>
    </a>
    @empty
    <div class="bg-white rounded-2xl p-12 text-center border border-surface-border">
        <span class="ms ms-fill text-4xl text-ink-faint">group</span>
        <p class="text-ink-muted mt-2">Belum ada pelanggan</p>
    </div>
    @endforelse
</div>

{{-- Table (Desktop) --}}
<div class="hidden md:block bg-white rounded-2xl shadow-card border border-surface-border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs text-ink-muted uppercase tracking-wider border-b border-surface-border">
            <tr>
                <th class="px-5 py-3 text-left">Pelanggan</th>
                <th class="px-4 py-3 text-left">Kode Referral</th>
                <th class="px-4 py-3 text-center">Level</th>
                <th class="px-4 py-3 text-right">Total Order</th>
                <th class="px-4 py-3 text-right">Wallet</th>
                <th class="px-4 py-3 text-right">Total Kg</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $c)
            <tr class="table-row">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr($c->name,0,1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-ink">{{ $c->name }}</p>
                            <p class="text-xs text-ink-muted">{{ $c->phone }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3.5">
                    <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded-lg text-ink-muted">{{ $c->referral_code }}</span>
                </td>
                <td class="px-4 py-3.5 text-center">
                    <span class="badge {{ $c->member_badge_color }}">{{ $c->member_level }}</span>
                </td>
                <td class="px-4 py-3.5 text-right font-semibold">{{ $c->orders_count }}</td>
                <td class="px-4 py-3.5 text-right font-semibold text-primary">
                    Rp {{ number_format($c->wallet_balance,0,',','.') }}
                </td>
                <td class="px-4 py-3.5 text-right text-ink-muted">{{ number_format($c->total_weight,1) }} kg</td>
                <td class="px-4 py-3.5">
                    <a href="{{ route('customers.show',$c) }}" class="btn-secondary py-1.5 px-3 text-xs">
                        <span class="ms text-sm">visibility</span>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-16 text-center">
                    <span class="ms ms-fill text-4xl text-ink-faint">group</span>
                    <p class="text-ink-muted mt-2">Belum ada pelanggan</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($customers->hasPages())
    <div class="px-5 py-4 border-t border-surface-border flex items-center justify-between">
        <p class="text-xs text-ink-muted">{{ $customers->firstItem() }}–{{ $customers->lastItem() }} dari {{ $customers->total() }}</p>
        {{ $customers->links('pagination::simple-tailwind') }}
    </div>
    @endif
</div>

{{-- Add Customer Modal --}}
<div id="addModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-ink text-lg">Tambah Pelanggan Baru</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100">
                <span class="ms">close</span>
            </button>
        </div>
        <form action="{{ route('customers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="form-input" placeholder="Nama pelanggan" required>
            </div>
            <div>
                <label class="form-label">Nomor Telepon <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" class="form-input" placeholder="08xx xxxx xxxx" required>
            </div>
            <div>
                <label class="form-label">Email (Opsional)</label>
                <input type="email" name="email" class="form-input" placeholder="email@example.com">
            </div>
            <div>
                <label class="form-label">Alamat (Opsional)</label>
                <input type="text" name="address" class="form-input" placeholder="Alamat lengkap">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')"
                        class="btn-secondary flex-1 justify-center">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <span class="ms">save</span> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
