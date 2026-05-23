@extends('layouts.app')
@section('title', 'Laporan')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-ink">Laporan & Analitik</h1>
        <p class="text-xs text-ink-muted">Ringkasan performa bisnis laundry Anda</p>
    </div>
    {{-- Period Selector --}}
    <div class="flex bg-white rounded-xl border border-surface-border p-1 gap-1 shadow-card">
        @foreach(['today'=>'Hari Ini','week'=>'Minggu Ini','month'=>'Bulan Ini','year'=>'Tahun Ini'] as $val=>$label)
        <a href="{{ route('reports.index', ['period'=>$val]) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
                  {{ $period===$val ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:bg-slate-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

{{-- Summary Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center mb-3">
            <span class="ms ms-fill text-primary">payments</span>
        </div>
        <p class="text-2xl font-bold text-ink">Rp {{ number_format($totalRevenue,0,',','.') }}</p>
        <p class="text-xs text-ink-muted mt-1">Total Pendapatan</p>
    </div>
    <div class="stat-card">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
            <span class="ms ms-fill text-blue-500">receipt_long</span>
        </div>
        <p class="text-2xl font-bold text-ink">{{ $totalOrders }}</p>
        <p class="text-xs text-ink-muted mt-1">Total Pesanan</p>
    </div>
    <div class="stat-card">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
            <span class="ms ms-fill text-amber-500">scale</span>
        </div>
        <p class="text-2xl font-bold text-ink">{{ number_format($totalWeight,1) }} kg</p>
        <p class="text-xs text-ink-muted mt-1">Total Berat</p>
    </div>
    <div class="stat-card">
        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mb-3">
            <span class="ms ms-fill text-purple-500">trending_up</span>
        </div>
        <p class="text-2xl font-bold text-ink">Rp {{ number_format($avgOrderValue,0,',','.') }}</p>
        <p class="text-xs text-ink-muted mt-1">Rata-rata/Order</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    {{-- Revenue Chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-card border border-surface-border">
        <h2 class="font-bold text-ink mb-4">Grafik Pendapatan</h2>
        <div class="h-56"><canvas id="revChart"></canvas></div>
    </div>

    {{-- Orders by Status --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
        <h2 class="font-bold text-ink mb-4">Distribusi Status</h2>
        <div class="h-48"><canvas id="statusChart"></canvas></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    {{-- Service Breakdown --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
        <h2 class="font-bold text-ink mb-4">Perbandingan Layanan</h2>
        @if($byService->count())
        <div class="space-y-3">
            @foreach($byService as $svc)
            @php $pct = $totalOrders > 0 ? ($svc->total / $totalOrders * 100) : 0; @endphp
            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="font-medium text-ink capitalize">{{ str_replace('_',' ',$svc->service_type ?? 'Lainnya') }}</span>
                    <span class="text-ink-muted">{{ $svc->total }} order · Rp {{ number_format($svc->revenue,0,',','.') }}</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-ink-muted text-sm">Tidak ada data untuk periode ini.</p>
        @endif
    </div>

    {{-- Top Customers --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
        <h2 class="font-bold text-ink mb-4">Pelanggan Terbaik</h2>
        @if($topCustomers->count())
        <div class="space-y-3">
            @foreach($topCustomers as $i => $cust)
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                    {{ $i===0 ? 'bg-amber-400 text-white' : ($i===1 ? 'bg-slate-300 text-ink' : 'bg-orange-300 text-white') }}">
                    {{ $i+1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-ink truncate">{{ $cust->name }}</p>
                    <p class="text-xs text-ink-muted">{{ $cust->period_orders }} order</p>
                </div>
                <p class="text-sm font-bold text-primary flex-shrink-0">
                    Rp {{ number_format($cust->period_revenue,0,',','.') }}
                </p>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-ink-muted text-sm">Tidak ada data untuk periode ini.</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revData = @json($revenueByDay);
    const rCtx = document.getElementById('revChart').getContext('2d');
    const grad = rCtx.createLinearGradient(0,0,0,200);
    grad.addColorStop(0,'rgba(13,148,136,0.2)');
    grad.addColorStop(1,'rgba(13,148,136,0)');
    new Chart(rCtx, {
        type:'bar',
        data:{
            labels: revData.map(d=>d.label),
            datasets:[{
                label:'Pendapatan',
                data: revData.map(d=>d.revenue),
                backgroundColor:'rgba(13,148,136,0.15)',
                borderColor:'#0d9488',
                borderWidth:2,
                borderRadius:6,
                hoverBackgroundColor:'rgba(13,148,136,0.3)',
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>'Rp '+c.parsed.y.toLocaleString('id-ID')}}},
            scales:{
                x:{grid:{display:false},ticks:{font:{size:10},color:'#94a3b8',maxRotation:45}},
                y:{grid:{color:'#f1f5f9'},border:{dash:[4,4]},ticks:{font:{size:10},color:'#94a3b8',callback:v=>'Rp '+(v/1000).toFixed(0)+'k'}}
            }
        }
    });

    // Status Donut Chart
    const statusData = @json($byStatus);
    const statusLabels = { antri:'Antri', dicuci:'Dicuci', dijemur:'Dijemur', disetrika:'Disetrika', siap_ambil:'Siap Ambil', selesai:'Selesai', dibatalkan:'Batal' };
    const statusColors = ['#94a3b8','#3b82f6','#f59e0b','#a855f7','#10b981','#0d9488','#ef4444'];
    const keys = Object.keys(statusData);
    if (keys.length) {
        new Chart(document.getElementById('statusChart').getContext('2d'), {
            type:'doughnut',
            data:{
                labels: keys.map(k=>statusLabels[k]??k),
                datasets:[{data:keys.map(k=>statusData[k]),backgroundColor:statusColors,borderWidth:0,hoverOffset:6}]
            },
            options:{
                responsive:true, maintainAspectRatio:false,
                plugins:{legend:{position:'bottom',labels:{font:{size:10},padding:10,boxWidth:10}}}
            }
        });
    }
});
</script>
@endpush
