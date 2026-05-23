@extends('portal.layouts.app')
@section('title', 'Dashboard Saya')

@push('styles')
<style>
/* Tab System */
.tab-nav{display:flex;background:#f1f5f9;border-radius:14px;padding:4px;gap:2px;margin-bottom:16px;}
.tab-btn{flex:1;padding:9px 6px;border:none;background:transparent;border-radius:10px;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:5px;}
.tab-btn.active{background:#fff;color:#0d9488;box-shadow:0 1px 4px rgba(0,0,0,.1);}
.tab-pane{display:none;}
.tab-pane.active{display:block;}

/* Status Progress */
.status-stages{display:flex;align-items:center;margin:12px 0 4px;}
.stage-item{flex:1;display:flex;flex-direction:column;align-items:center;position:relative;}
.stage-item:not(:last-child)::after{content:'';position:absolute;top:10px;left:50%;width:100%;height:2px;background:#e2e8f0;z-index:0;}
.stage-item.done:not(:last-child)::after{background:#0d9488;}
.stage-dot{width:20px;height:20px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;z-index:1;position:relative;transition:all .2s;}
.stage-dot.done{background:#0d9488;}
.stage-dot.current{background:#0d9488;box-shadow:0 0 0 4px rgba(13,148,136,.2);}
.stage-label{font-size:9px;color:#94a3b8;margin-top:4px;text-align:center;line-height:1.2;}
.stage-label.done,.stage-label.current{color:#0d9488;font-weight:600;}

/* Order Card */
.order-card{border:1.5px solid #e2e8f0;border-radius:14px;padding:14px;margin-bottom:10px;background:#fff;transition:border-color .2s;}
.order-card:hover{border-color:#0d9488;}

/* Wallet */
.wallet-balance-card{background:linear-gradient(135deg,#0d9488,#0891b2);border-radius:18px;padding:24px;color:#fff;margin-bottom:16px;position:relative;overflow:hidden;}
.wallet-balance-card::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.08);border-radius:50%;}
.wallet-balance-card::after{content:'';position:absolute;bottom:-20px;left:-20px;width:80px;height:80px;background:rgba(255,255,255,.06);border-radius:50%;}

/* Transaction item */
.tx-item{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f8fafc;}
.tx-item:last-child{border-bottom:none;}
.tx-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
</style>
@endpush

@section('content')
@php
    $stages = ['antri','dicuci','dijemur','disetrika','siap_ambil','selesai'];
    $stageIcons = ['hourglass_empty','water_drop','wb_sunny','iron','check_circle','task_alt'];
    $stageLabels = ['Antri','Dicuci','Dijemur','Disetrika','Siap','Selesai'];
    $levelBadge = match($customer->member_level) {
        'Silver' => 'badge-silver',
        'Gold'   => 'badge-gold',
        default  => 'badge-bronze',
    };
    $levelIcon = match($customer->member_level) {
        'Silver' => '🥈',
        'Gold'   => '🥇',
        default  => '🥉',
    };
@endphp

{{-- Customer Header --}}
<div class="card" style="margin-bottom:16px;padding:20px;">
    <div style="display:flex;align-items:center;gap:14px;">
        <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#14b8a6);display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;font-weight:800;flex-shrink:0;">
            {{ strtoupper(substr($customer->name,0,1)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:17px;font-weight:800;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $customer->name }}</div>
            <div style="font-size:12px;color:#64748b;margin:2px 0;">{{ $customer->phone }}</div>
            <span class="badge {{ $levelBadge }}">{{ $levelIcon }} {{ $customer->member_level }}</span>
        </div>
        <div style="text-align:right;flex-shrink:0;">
            <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">Total Cucian</div>
            <div style="font-size:15px;font-weight:700;color:#0d9488;">{{ $customer->total_orders }}x</div>
            <div style="font-size:11px;color:#64748b;">{{ number_format($customer->total_weight,1) }} kg</div>
        </div>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="tab-nav">
    <button class="tab-btn active" onclick="switchTab('aktif',this)" id="tab-btn-aktif">
        <span class="ms ms-fill" style="font-size:16px;">local_laundry_service</span>
        Aktif
        @if($activeOrders->count())
        <span style="background:#0d9488;color:#fff;border-radius:20px;padding:1px 7px;font-size:10px;">{{ $activeOrders->count() }}</span>
        @endif
    </button>
    <button class="tab-btn" onclick="switchTab('riwayat',this)" id="tab-btn-riwayat">
        <span class="ms ms-fill" style="font-size:16px;">history</span>
        Riwayat
    </button>
    <button class="tab-btn" onclick="switchTab('saldo',this)" id="tab-btn-saldo">
        <span class="ms ms-fill" style="font-size:16px;">account_balance_wallet</span>
        Saldo
    </button>
</div>

{{-- Tab: Pesanan Aktif --}}
<div class="tab-pane active" id="tab-aktif">
    @forelse($activeOrders as $order)
    @php
        $currentIdx = array_search($order->status, $stages);
    @endphp
    <div class="order-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <span style="font-size:13px;font-weight:700;color:#0f172a;font-family:monospace;">{{ $order->order_number }}</span>
            <span style="font-size:11px;padding:4px 10px;border-radius:20px;font-weight:600;background:{{ ['antri'=>'#f1f5f9','dicuci'=>'#dbeafe','dijemur'=>'#fef9c3','disetrika'=>'#f3e8ff','siap_ambil'=>'#dcfce7','selesai'=>'#ccfbf1','dibatalkan'=>'#fee2e2'][$order->status] ?? '#f1f5f9' }};color:{{ ['antri'=>'#475569','dicuci'=>'#1d4ed8','dijemur'=>'#854d0e','disetrika'=>'#7e22ce','siap_ambil'=>'#15803d','selesai'=>'#0f766e','dibatalkan'=>'#dc2626'][$order->status] ?? '#475569' }};">
                {{ \App\Models\Order::$statusLabels[$order->status] ?? $order->status }}
            </span>
        </div>

        {{-- Progress Stages --}}
        <div class="status-stages">
            @foreach($stages as $i => $stage)
            @php
                $isDone    = $currentIdx !== false && $i <= $currentIdx;
                $isCurrent = $order->status === $stage;
            @endphp
            <div class="stage-item {{ $isDone ? 'done' : '' }}">
                <div class="stage-dot {{ $isCurrent ? 'current' : ($isDone ? 'done' : '') }}">
                    <span class="ms ms-fill" style="font-size:11px;color:{{ $isDone ? '#fff' : '#cbd5e1' }};">{{ $stageIcons[$i] }}</span>
                </div>
                <span class="stage-label {{ $isCurrent ? 'current' : ($isDone ? 'done' : '') }}">{{ $stageLabels[$i] }}</span>
            </div>
            @endforeach
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding-top:10px;border-top:1px solid #f8fafc;">
            <div style="font-size:12px;color:#64748b;">
                @if($order->service_type) {{ str_replace('_',' ',ucfirst($order->service_type)) }} @endif
                @if($order->weight) · {{ $order->weight }} kg @endif
            </div>
            <div style="font-size:14px;font-weight:700;color:#0d9488;">Rp {{ number_format($order->total,0,',','.') }}</div>
        </div>
        @if($order->estimated_done)
        <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
            <span class="ms" style="font-size:13px;vertical-align:middle;">schedule</span>
            Est. selesai: {{ $order->estimated_done->format('d M Y') }}
        </div>
        @endif
    </div>
    @empty
    <div style="text-align:center;padding:40px 20px;">
        <span class="ms ms-fill" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:12px;">local_laundry_service</span>
        <p style="color:#94a3b8;font-size:14px;">Tidak ada pesanan aktif saat ini</p>
    </div>
    @endforelse
</div>

{{-- Tab: Riwayat --}}
<div class="tab-pane" id="tab-riwayat">
    @forelse($historyOrders as $order)
    <div class="order-card" style="padding:12px 14px;">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:13px;font-weight:700;color:#0f172a;font-family:monospace;">{{ $order->order_number }}</div>
                <div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $order->created_at->isoFormat('D MMM Y') }}</div>
                @if($order->service_type)
                <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ str_replace('_',' ',ucfirst($order->service_type)) }}@if($order->weight) · {{ $order->weight }} kg @endif</div>
                @endif
            </div>
            <div style="text-align:right;">
                <div style="font-size:14px;font-weight:700;color:#0f172a;">Rp {{ number_format($order->total,0,',','.') }}</div>
                <span style="font-size:10px;padding:3px 8px;border-radius:20px;font-weight:600;background:{{ $order->status==='selesai'?'#ccfbf1':'#fee2e2' }};color:{{ $order->status==='selesai'?'#0f766e':'#dc2626' }};">
                    {{ \App\Models\Order::$statusLabels[$order->status] ?? $order->status }}
                </span>
            </div>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:40px 20px;">
        <span class="ms ms-fill" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:12px;">history</span>
        <p style="color:#94a3b8;font-size:14px;">Belum ada riwayat pesanan</p>
    </div>
    @endforelse
</div>

{{-- Tab: Saldo --}}
<div class="tab-pane" id="tab-saldo">
    {{-- Wallet Balance Card --}}
    <div class="wallet-balance-card">
        <div style="font-size:12px;font-weight:600;opacity:.8;margin-bottom:6px;position:relative;z-index:1;">SALDO WALLET</div>
        <div style="font-size:32px;font-weight:800;position:relative;z-index:1;">Rp {{ number_format($customer->wallet_balance,0,',','.') }}</div>
        <div style="font-size:12px;opacity:.7;margin-top:6px;position:relative;z-index:1;">{{ $customer->name }}</div>
    </div>

    {{-- Topup Form --}}
    <div class="card" style="margin-bottom:16px;">
        <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:14px;display:flex;align-items:center;gap:6px;">
            <span class="ms ms-fill" style="color:#0d9488;font-size:18px;">add_card</span> Topup Saldo
        </h3>

        @if($errors->has('amount'))
        <div class="alert alert-error"><span class="ms ms-fill" style="font-size:16px;flex-shrink:0;">error</span>{{ $errors->first('amount') }}</div>
        @endif

        @if(!$adminPhone)
        <div class="alert alert-info"><span class="ms ms-fill" style="font-size:16px;flex-shrink:0;">info</span>Fitur topup belum dikonfigurasi. Hubungi kasir.</div>
        @else
        <form action="{{ route('portal.topup') }}" method="POST">
            @csrf
            <div style="margin-bottom:14px;">
                <label class="form-label">Nominal Topup</label>
                <div style="position:relative;">
                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:13px;font-weight:600;color:#64748b;">Rp</span>
                    <input type="number" name="amount" min="10000" step="5000"
                           class="form-input" style="padding-left:36px;"
                           placeholder="50000" value="{{ old('amount') }}">
                </div>
                <p style="font-size:11px;color:#94a3b8;margin:5px 0 0;">Minimal Rp 10.000</p>
            </div>

            {{-- Quick Amount Buttons --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:14px;">
                @foreach([20000,50000,100000,150000,200000,500000] as $amt)
                <button type="button" onclick="document.querySelector('[name=amount]').value={{ $amt }}"
                        style="padding:8px;border:1.5px solid #e2e8f0;border-radius:10px;background:#f8fafc;font-size:12px;font-weight:600;color:#475569;cursor:pointer;font-family:inherit;transition:all .15s;"
                        onmouseover="this.style.borderColor='#0d9488';this.style.color='#0d9488';this.style.background='#f0fdf9'"
                        onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569';this.style.background='#f8fafc'">
                    {{ number_format($amt/1000) }}rb
                </button>
                @endforeach
            </div>

            <button type="submit" class="btn btn-wa" style="width:100%;padding:14px;font-size:15px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Topup via WhatsApp
            </button>
        </form>
        @endif
    </div>

    {{-- Riwayat Transaksi --}}
    <div class="card">
        <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:14px;display:flex;align-items:center;gap:6px;">
            <span class="ms ms-fill" style="color:#0d9488;font-size:18px;">receipt_long</span> Riwayat Transaksi
        </h3>
        @forelse($customer->walletTransactions as $tx)
        <div class="tx-item">
            <div class="tx-icon" style="background:{{ $tx->type==='credit'?'#f0fdf9':'#fef2f2' }};">
                <span class="ms ms-fill" style="font-size:18px;color:{{ $tx->type==='credit'?'#0d9488':'#ef4444' }};">{{ $tx->type==='credit'?'arrow_downward':'arrow_upward' }}</span>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $tx->description }}</div>
                <div style="font-size:11px;color:#94a3b8;">{{ $tx->created_at->isoFormat('D MMM Y, HH:mm') }}</div>
            </div>
            <div style="font-size:14px;font-weight:700;color:{{ $tx->type==='credit'?'#0d9488':'#ef4444' }};flex-shrink:0;">
                {{ $tx->type==='credit'?'+':'-' }}Rp {{ number_format($tx->amount,0,',','.') }}
            </div>
        </div>
        @empty
        <p style="text-align:center;color:#94a3b8;font-size:13px;padding:16px 0;">Belum ada transaksi wallet</p>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}
</script>
@endpush
