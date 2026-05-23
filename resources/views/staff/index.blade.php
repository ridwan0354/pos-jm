@extends('layouts.app')
@section('title', 'Manajemen Tim')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-ink">Manajemen Tim</h1>
        <p class="text-xs text-ink-muted">Penanggung jawab setrika & rekap fee bulanan</p>
    </div>
    <button onclick="document.getElementById('addStaffModal').classList.remove('hidden')" class="btn-primary">
        <span class="ms">person_add</span> Tambah Staff
    </button>
</div>

@if(session('success'))
<div class="flex items-center gap-2 mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm font-medium">
    <span class="ms ms-fill text-lg">check_circle</span> {{ session('success') }}
</div>
@endif

{{-- Summary Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="stat-card">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center mb-3">
            <span class="ms ms-fill text-primary">group</span>
        </div>
        <p class="text-2xl font-bold text-ink">{{ $staffList->where('is_active', true)->count() }}</p>
        <p class="text-xs text-ink-muted mt-1">Staff Aktif</p>
    </div>
    <div class="stat-card">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
            <span class="ms ms-fill text-amber-500">scale</span>
        </div>
        <p class="text-2xl font-bold text-ink">{{ number_format($staffList->sum('monthly_kg'), 1) }} kg</p>
        <p class="text-xs text-ink-muted mt-1">Total Kg Bulan Ini</p>
    </div>
    <div class="stat-card">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
            <span class="ms ms-fill text-emerald-500">payments</span>
        </div>
        <p class="text-2xl font-bold text-primary">Rp {{ number_format($staffList->sum('monthly_fee'), 0, ',', '.') }}</p>
        <p class="text-xs text-ink-muted mt-1">Total Fee Bulan Ini</p>
    </div>
</div>

{{-- Staff Table --}}
<div class="bg-white rounded-2xl shadow-card border border-surface-border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-ink-muted uppercase tracking-wider border-b border-surface-border">
                <tr>
                    <th class="px-5 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">No HP</th>
                    <th class="px-4 py-3 text-right">Fee/kg</th>
                    <th class="px-4 py-3 text-right">Kg Bulan Ini</th>
                    <th class="px-4 py-3 text-right">Fee Bulan Ini</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffList as $s)
                <tr class="table-row">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-ink">{{ $s->name }}</p>
                        <p class="text-xs text-ink-muted">{{ $s->monthly_orders ?? 0 }} order bulan ini</p>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell text-ink-muted">
                        {{ $s->phone ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-right font-semibold text-ink">
                        Rp {{ number_format($s->fee_per_kg, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <span class="font-bold text-ink">{{ number_format($s->monthly_kg ?? 0, 1) }}</span>
                        <span class="text-xs text-ink-muted ml-1">kg</span>
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <span class="font-bold text-primary">Rp {{ number_format($s->monthly_fee ?? 0, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @if($s->is_active)
                        <span class="badge bg-emerald-50 text-emerald-700">✓ Aktif</span>
                        @else
                        <span class="badge bg-slate-100 text-slate-500">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="openEditModal({{ $s->id }}, '{{ addslashes($s->name) }}', '{{ $s->phone }}', {{ $s->fee_per_kg }}, {{ $s->is_active ? 1 : 0 }})"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="Edit">
                                <span class="ms ms-fill text-base">edit</span>
                            </button>
                            @if($s->is_active)
                            <form action="{{ route('staff.destroy', $s) }}" method="POST"
                                  onsubmit="return confirm('Nonaktifkan staff ini?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="Nonaktifkan">
                                    <span class="ms text-base">person_off</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                {{-- Riwayat order terbaru --}}
                @if(isset($recentByStaff[$s->id]) && $recentByStaff[$s->id]->count())
                <tr class="bg-slate-50/50">
                    <td colspan="7" class="px-5 py-3">
                        <p class="text-xs font-semibold text-ink-muted mb-2">10 order terbaru:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($recentByStaff[$s->id] as $ord)
                            <a href="{{ route('orders.show', $ord) }}"
                               class="text-xs bg-white border border-surface-border rounded-lg px-2.5 py-1.5 text-ink hover:border-primary hover:text-primary transition-colors">
                                {{ $ord->order_number }}
                                <span class="text-ink-muted ml-1">{{ number_format($ord->weight, 1) }} kg</span>
                                @if($ord->ironing_fee)
                                <span class="text-primary ml-1">· Rp {{ number_format($ord->ironing_fee, 0, ',', '.') }}</span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <span class="ms ms-fill text-4xl text-ink-faint">group</span>
                        <p class="text-ink-muted mt-2">Belum ada staff terdaftar</p>
                        <button onclick="document.getElementById('addStaffModal').classList.remove('hidden')"
                                class="btn-primary mt-4 text-sm">
                            <span class="ms">person_add</span> Tambah Staff Pertama
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add Staff Modal --}}
<div id="addStaffModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-ink text-lg">Tambah Staff</h3>
            <button onclick="document.getElementById('addStaffModal').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100">
                <span class="ms">close</span>
            </button>
        </div>
        <form action="{{ route('staff.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Nama Staff <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="form-input" placeholder="Nama lengkap" required>
            </div>
            <div>
                <label class="form-label">No HP</label>
                <input type="text" name="phone" class="form-input" placeholder="08xx xxxx xxxx">
            </div>
            <div>
                <label class="form-label">Fee per Kg (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="fee_per_kg" min="0" step="100" class="form-input" placeholder="2000" required>
                <p class="text-xs text-ink-faint mt-1">Upah yang diterima staff per kg cucian yang disetrika.</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addStaffModal').classList.add('hidden')"
                        class="btn-secondary flex-1 justify-center">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <span class="ms">save</span> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Staff Modal --}}
<div id="editStaffModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-ink text-lg">Edit Staff</h3>
            <button onclick="document.getElementById('editStaffModal').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100">
                <span class="ms">close</span>
            </button>
        </div>
        <form id="editStaffForm" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="form-label">Nama Staff <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="editName" class="form-input" required>
            </div>
            <div>
                <label class="form-label">No HP</label>
                <input type="text" name="phone" id="editPhone" class="form-input">
            </div>
            <div>
                <label class="form-label">Fee per Kg (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="fee_per_kg" id="editFee" min="0" step="100" class="form-input" required>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="editActive" value="1" class="w-4 h-4 accent-primary">
                <label for="editActive" class="text-sm text-ink cursor-pointer">Staff aktif</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editStaffModal').classList.add('hidden')"
                        class="btn-secondary flex-1 justify-center">Batal</button>
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <span class="ms">save</span> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(id, name, phone, fee, isActive) {
    document.getElementById('editStaffForm').action = `/staff/${id}`;
    document.getElementById('editName').value    = name;
    document.getElementById('editPhone').value   = phone || '';
    document.getElementById('editFee').value     = fee;
    document.getElementById('editActive').checked = isActive === 1;
    document.getElementById('editStaffModal').classList.remove('hidden');
}
</script>
@endpush
