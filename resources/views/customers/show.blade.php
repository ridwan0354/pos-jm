@extends('layouts.app')
@section('title', $customer->name . ' – Profil Pelanggan')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('customers.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-white border border-surface-border">
        <span class="ms text-ink-muted">arrow_back</span>
    </a>
    <h1 class="text-xl font-bold text-ink">Profil Pelanggan</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Left --}}
    <div class="space-y-5">
        {{-- Profile Card --}}
        <div class="bg-white rounded-2xl p-6 shadow-card border border-surface-border text-center">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-2xl mx-auto mb-3">
                {{ strtoupper(substr($customer->name,0,1)) }}
            </div>
            <h2 class="font-bold text-ink text-lg">{{ $customer->name }}</h2>
            <p class="text-sm text-ink-muted">{{ $customer->phone }}</p>
            <div class="mt-2">
                <span class="badge {{ $customer->member_badge_color }} text-sm px-3 py-1">
                    ⭐ {{ $customer->member_level }} Member
                </span>
            </div>
            @if($customer->email)
            <p class="text-xs text-ink-muted mt-2">{{ $customer->email }}</p>
            @endif
            @if($customer->address)
            <p class="text-xs text-ink-muted mt-1">📍 {{ $customer->address }}</p>
            @endif

            <div class="grid grid-cols-3 gap-3 mt-5 pt-5 border-t border-surface-border">
                <div>
                    <p class="text-xl font-extrabold text-primary">{{ $customer->total_orders }}</p>
                    <p class="text-[11px] text-ink-muted">Total Order</p>
                </div>
                <div>
                    <p class="text-xl font-extrabold text-ink">{{ number_format($customer->total_weight,1) }}</p>
                    <p class="text-[11px] text-ink-muted">Total Kg</p>
                </div>
                <div>
                    <p class="text-xl font-extrabold text-emerald-600">{{ $referrals->count() }}</p>
                    <p class="text-[11px] text-ink-muted">Referral</p>
                </div>
            </div>
        </div>

        {{-- Wallet --}}
        <div class="bg-gradient-to-br from-primary to-primary-dark rounded-2xl p-5 text-white shadow-md">
            <div class="flex items-center gap-2 mb-3">
                <span class="ms ms-fill text-white/80">account_balance_wallet</span>
                <p class="text-sm font-semibold text-white/80">Saldo Wallet</p>
            </div>
            <p class="text-3xl font-extrabold">Rp {{ number_format($customer->wallet_balance,0,',','.') }}</p>
            <div class="flex items-center gap-1.5 mt-3">
                <span class="font-mono text-xs bg-white/20 px-2 py-1 rounded-lg">{{ $customer->referral_code }}</span>
                <span class="text-xs text-white/70">kode referral</span>
            </div>
        </div>

        {{-- Topup Saldo (Admin) --}}
        <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
            <h3 class="font-bold text-ink text-sm mb-4 flex items-center gap-2">
                <span class="ms ms-fill text-primary text-base">add_card</span> Tambah Saldo
            </h3>

            @if(session('topup_success'))
            <div class="flex items-center gap-2 mb-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-xs font-medium">
                <span class="ms ms-fill text-base flex-shrink-0">check_circle</span>
                {{ session('topup_success') }}
            </div>
            @endif

            <form action="{{ route('customers.topup', $customer) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="form-label">Nominal Topup <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-ink-muted">Rp</span>
                        <input type="number" name="amount" min="1000" step="1000"
                               class="form-input pl-9" placeholder="50000"
                               value="{{ old('amount') }}" required>
                    </div>
                    @error('amount')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nominal Cepat --}}
                <div class="grid grid-cols-3 gap-1.5">
                    @foreach([20000,50000,100000,150000,200000,500000] as $amt)
                    <button type="button"
                            onclick="document.querySelector('[name=amount]').value='{{ $amt }}'"
                            class="text-xs font-semibold py-1.5 px-2 border border-surface-border rounded-lg text-ink-muted hover:border-primary hover:text-primary transition-all">
                        {{ number_format($amt/1000) }}rb
                    </button>
                    @endforeach
                </div>

                <div>
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="description" class="form-input"
                           placeholder="Topup via transfer BCA"
                           value="{{ old('description') }}">
                </div>

                <button type="submit" class="btn-primary w-full justify-center">
                    <span class="ms ms-fill">add_card</span> Tambah Saldo
                </button>
            </form>
        </div>

        {{-- Referrals --}}

        @if($referrals->count() > 0)
        <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
            <h3 class="font-bold text-ink text-sm mb-3 flex items-center gap-2">
                <span class="ms ms-fill text-primary text-base">diversity_3</span>
                Referral ({{ $referrals->count() }})
            </h3>
            <div class="space-y-2">
                @foreach($referrals as $ref)
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                        {{ strtoupper(substr($ref->name,0,1)) }}
                    </div>
                    <span class="text-ink font-medium truncate">{{ $ref->name }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Edit Form --}}
        <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
            <h3 class="font-bold text-ink text-sm mb-4">Edit Data</h3>
            <form action="{{ route('customers.update',$customer) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')
                <div>
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" value="{{ $customer->name }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Nomor HP</label>
                    <input type="tel" name="phone" value="{{ $customer->phone }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ $customer->email }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Alamat</label>
                    <input type="text" name="address" value="{{ $customer->address }}" class="form-input">
                </div>
                <button type="submit" class="btn-primary w-full justify-center">
                    <span class="ms">save</span> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    {{-- Right --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Order History --}}
        <div class="bg-white rounded-2xl shadow-card border border-surface-border overflow-hidden">
            <div class="px-5 py-4 border-b border-surface-border flex items-center justify-between">
                <h2 class="font-bold text-ink flex items-center gap-2">
                    <span class="ms ms-fill text-primary">receipt_long</span> Riwayat Pesanan
                </h2>
                <a href="{{ route('orders.index', ['search' => $customer->phone]) }}" class="text-xs text-primary font-semibold hover:underline">Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs text-ink-muted uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3 text-left">No. Order</th>
                            <th class="px-4 py-3 text-left hidden md:table-cell">Layanan</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left hidden md:table-cell">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->orders()->latest()->take(10)->get() as $order)
                        <tr class="table-row cursor-pointer" onclick="location='{{ route('orders.show',$order) }}'">
                            <td class="px-5 py-3 font-mono text-xs text-primary font-semibold">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 hidden md:table-cell capitalize text-ink-muted">
                                {{ str_replace('_',' ',$order->service_type ?? $order->category) }}
                                @if($order->weight)<span class="text-xs">({{ $order->weight }}kg)</span>@endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($order->total,0,',','.') }}</td>
                            <td class="px-4 py-3"><span class="badge {{ $order->status_color }}">{{ $order->status_label }}</span></td>
                            <td class="px-4 py-3 text-xs text-ink-muted hidden md:table-cell">{{ $order->created_at->isoFormat('D MMM Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-ink-muted">Belum ada pesanan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Wallet History --}}
        @if($walletHistory->count() > 0)
        <div class="bg-white rounded-2xl shadow-card border border-surface-border overflow-hidden">
            <div class="px-5 py-4 border-b border-surface-border">
                <h2 class="font-bold text-ink flex items-center gap-2">
                    <span class="ms ms-fill text-primary">account_balance_wallet</span> Riwayat Wallet
                </h2>
            </div>
            <div class="divide-y divide-surface-border">
                @foreach($walletHistory as $tx)
                <div class="flex items-center gap-3 px-5 py-3.5">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 {{ $tx->type==='credit' ? 'bg-emerald-50' : 'bg-red-50' }}">
                        <span class="ms ms-fill text-base {{ $tx->type==='credit' ? 'text-emerald-500' : 'text-red-500' }}">
                            {{ $tx->type==='credit' ? 'add_circle' : 'remove_circle' }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink truncate">{{ $tx->description }}</p>
                        <p class="text-xs text-ink-muted">{{ $tx->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                    </div>
                    <p class="font-bold {{ $tx->type==='credit' ? 'text-emerald-600' : 'text-red-500' }} flex-shrink-0">
                        {{ $tx->type==='credit' ? '+' : '-' }}Rp {{ number_format($tx->amount,0,',','.') }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
