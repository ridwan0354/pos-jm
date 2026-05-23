@extends('layouts.app')
@section('title', 'Manajemen Stok')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-ink">Manajemen Stok</h1>
        <p class="text-xs text-ink-muted">
            {{ $lowCount > 0 ? "⚠️ {$lowCount} item stok menipis" : 'Semua stok dalam kondisi aman' }}
        </p>
    </div>
    <button onclick="document.getElementById('addStockModal').classList.remove('hidden')" class="btn-primary">
        <span class="ms">add</span> Tambah Item
    </button>
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-2xl p-4 shadow-card border border-surface-border mb-5 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
           class="form-input flex-1 min-w-48" placeholder="🔍 Cari nama atau kategori…">
    <label class="flex items-center gap-2 text-sm text-ink-muted cursor-pointer">
        <input type="checkbox" name="low_only" value="1" {{ request('low_only') ? 'checked' : '' }}
               class="w-4 h-4 accent-primary">
        Stok menipis saja
    </label>
    <button type="submit" class="btn-primary px-5"><span class="ms">search</span></button>
    @if(request()->hasAny(['search','low_only']))
    <a href="{{ route('stocks.index') }}" class="btn-secondary px-4"><span class="ms">close</span></a>
    @endif
</form>

{{-- Stock Table --}}
<div class="bg-white rounded-2xl shadow-card border border-surface-border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-ink-muted uppercase tracking-wider border-b border-surface-border">
                <tr>
                    <th class="px-5 py-3 text-left">Nama Item</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Kategori</th>
                    <th class="px-4 py-3 text-center">Stok</th>
                    <th class="px-4 py-3 text-center hidden md:table-cell">Min. Stok</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Harga/Unit</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                <tr class="table-row" id="stock-{{ $stock->id }}">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-ink">{{ $stock->name }}</p>
                        @if($stock->notes)<p class="text-xs text-ink-muted">{{ $stock->notes }}</p>@endif
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <span class="text-ink-muted capitalize">{{ $stock->category ?? '-' }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="text-lg font-bold {{ $stock->is_low ? 'text-red-500' : 'text-ink' }}">
                            {{ number_format($stock->quantity,1) }}
                        </span>
                        <span class="text-xs text-ink-muted ml-1">{{ $stock->unit }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-center hidden md:table-cell text-ink-muted">
                        {{ number_format($stock->min_quantity,1) }} {{ $stock->unit }}
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell text-ink-muted">
                        {{ $stock->price_per_unit ? 'Rp '.number_format($stock->price_per_unit,0,',','.') : '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @if($stock->is_low)
                        <span class="badge bg-red-50 text-red-600">⚠️ Menipis</span>
                        @else
                        <span class="badge bg-emerald-50 text-emerald-700">✓ Aman</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="showAddForm({{ $stock->id }}, '{{ addslashes($stock->name) }}', '{{ $stock->unit }}')"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors" title="Tambah stok">
                                <span class="ms ms-fill text-base">add</span>
                            </button>
                            <button onclick="showReduceForm({{ $stock->id }}, '{{ addslashes($stock->name) }}', '{{ $stock->unit }}')"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="Kurangi stok">
                                <span class="ms ms-fill text-base">remove</span>
                            </button>
                            <form action="{{ route('stocks.destroy',$stock) }}" method="POST"
                                  onsubmit="return confirm('Hapus item ini?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="Hapus">
                                    <span class="ms text-base">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <span class="ms ms-fill text-4xl text-ink-faint">inventory_2</span>
                        <p class="text-ink-muted mt-2">Belum ada item stok</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stocks->hasPages())
    <div class="px-5 py-4 border-t border-surface-border flex items-center justify-between">
        <p class="text-xs text-ink-muted">{{ $stocks->firstItem() }}–{{ $stocks->lastItem() }} dari {{ $stocks->total() }}</p>
        {{ $stocks->links('pagination::simple-tailwind') }}
    </div>
    @endif
</div>

{{-- Add Stock Item Modal --}}
<div id="addStockModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-ink text-lg">Tambah Item Stok</h3>
            <button onclick="document.getElementById('addStockModal').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100">
                <span class="ms">close</span>
            </button>
        </div>
        <form action="{{ route('stocks.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="form-label">Nama Item <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="Nama bahan/item" required>
                </div>
                <div>
                    <label class="form-label">Kategori</label>
                    <input type="text" name="category" class="form-input" placeholder="deterjen, pewangi…">
                </div>
                <div>
                    <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                    <select name="unit" class="form-input" required>
                        <option value="pcs">pcs</option>
                        <option value="kg">kg</option>
                        <option value="liter">liter</option>
                        <option value="botol">botol</option>
                        <option value="roll">roll</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Stok Awal <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" step="0.1" min="0" value="0" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Min. Stok Alert <span class="text-red-500">*</span></label>
                    <input type="number" name="min_quantity" step="0.1" min="0" value="5" class="form-input" required>
                </div>
                <div class="col-span-2">
                    <label class="form-label">Harga/Unit (Rp)</label>
                    <input type="number" name="price_per_unit" min="0" class="form-input" placeholder="Opsional">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addStockModal').classList.add('hidden')"
                        class="btn-secondary flex-1 justify-center">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <span class="ms">save</span> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Adjust Stock Modal --}}
<div id="adjustModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-1">
            <h3 class="font-bold text-ink text-lg" id="adjustModalTitle">Sesuaikan Stok</h3>
            <button onclick="document.getElementById('adjustModal').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100">
                <span class="ms">close</span>
            </button>
        </div>
        <p class="text-sm text-ink-muted mb-5" id="adjustModalSub"></p>
        <form id="adjustForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Jumlah <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2">
                    <input type="number" name="qty" step="0.1" min="0.1" class="form-input flex-1" placeholder="0" required>
                    <span class="text-sm text-ink-muted" id="adjustUnit">unit</span>
                </div>
            </div>
            <div>
                <label class="form-label">Keterangan</label>
                <input type="text" name="reason" class="form-input" placeholder="Alasan penyesuaian">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('adjustModal').classList.add('hidden')"
                        class="btn-secondary flex-1 justify-center">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center" id="adjustSubmitBtn">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showAddForm(id, name, unit) {
    document.getElementById('adjustModalTitle').textContent = 'Tambah Stok';
    document.getElementById('adjustModalSub').textContent = name;
    document.getElementById('adjustUnit').textContent = unit;
    document.getElementById('adjustForm').action = `/stocks/${id}/add`;
    document.getElementById('adjustModal').classList.remove('hidden');
}
function showReduceForm(id, name, unit) {
    document.getElementById('adjustModalTitle').textContent = 'Kurangi Stok';
    document.getElementById('adjustModalSub').textContent = name;
    document.getElementById('adjustUnit').textContent = unit;
    document.getElementById('adjustForm').action = `/stocks/${id}/reduce`;
    document.getElementById('adjustModal').classList.remove('hidden');
}
</script>
@endpush
@endsection
