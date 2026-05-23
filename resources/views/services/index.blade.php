@extends('layouts.app')
@section('title', 'Detail Layanan')
@php use App\Models\Service; @endphp

@push('styles')
<style>
.svc-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:16px; }
.svc-card {
    background:#fff; border-radius:16px; padding:20px;
    border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,.06);
    display:flex; flex-direction:column; gap:12px;
    transition: box-shadow .2s;
}
.svc-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); }
.svc-badge-kiloan { background:#dbeafe; color:#1d4ed8; }
.svc-badge-satuan { background:#d1fae5; color:#065f46; }
.svc-badge-ongkir { background:#fef3c7; color:#92400e; }
.svc-badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;letter-spacing:.4px;text-transform:uppercase; }
.svc-inactive { opacity:.5; }
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:20px;padding:28px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2); }
.tab-btn { padding:8px 20px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;border:none;background:transparent;color:#64748b;transition:.15s; }
.tab-btn.active { background:#0d9488;color:#fff; }
.tab-section { display:none; }
.tab-section.active { display:block; }
</style>
@endpush

@section('content')
{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:22px;font-weight:800;color:#1e293b;margin:0;">Detail Layanan</h1>
        <p style="font-size:13px;color:#94a3b8;margin:4px 0 0;">Kelola layanan kiloan, satuan, dan ongkir</p>
    </div>
    <button onclick="openAddModal()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#0d9488;color:#fff;border:none;border-radius:12px;font-weight:600;font-size:14px;cursor:pointer;">
        <span class="ms" style="font-size:18px;">add</span> Tambah Layanan
    </button>
</div>

{{-- Category Tabs --}}
<div style="display:flex;gap:6px;background:#f1f5f9;padding:4px;border-radius:12px;width:fit-content;margin-bottom:24px;">
    <button class="tab-btn active" id="tab-all"     onclick="switchTab('all')">Semua ({{ $services->count() }})</button>
    <button class="tab-btn"        id="tab-kiloan"  onclick="switchTab('kiloan')">Kiloan</button>
    <button class="tab-btn"        id="tab-satuan"  onclick="switchTab('satuan')">Satuan</button>
    <button class="tab-btn"        id="tab-ongkir"  onclick="switchTab('ongkir')">Ongkir</button>
</div>

{{-- Service Cards --}}
@foreach(['kiloan','satuan','ongkir'] as $cat)
<div class="tab-section {{ $loop->first ? 'active' : '' }}" id="section-{{ $cat }}">
    @php $catServices = $services->where('category', $cat); @endphp
    @if($catServices->isEmpty())
    <div style="text-align:center;padding:40px;color:#94a3b8;background:#fff;border-radius:16px;border:2px dashed #e2e8f0;">
        <span class="ms" style="font-size:36px;display:block;margin-bottom:8px;">inventory_2</span>
        Belum ada layanan {{ ucfirst($cat) }}. <button onclick="openAddModal('{{ $cat }}')" style="color:#0d9488;background:none;border:none;cursor:pointer;font-weight:600;">Tambah sekarang →</button>
    </div>
    @else
    <div class="svc-grid">
        @foreach($catServices->sortBy('sort_order') as $svc)
        <div class="svc-card {{ $svc->is_active ? '' : 'svc-inactive' }}">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span class="svc-badge svc-badge-{{ $svc->category }}">
                    {{ Service::$categoryLabels[$svc->category] ?? $svc->category }}
                </span>
                <div style="display:flex;gap:6px;">
                    {{-- Toggle active --}}
                    <form action="{{ route('services.toggle', $svc) }}" method="POST" style="margin:0;">
                        @csrf @method('PATCH')
                        <button type="submit" title="{{ $svc->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                            style="width:30px;height:30px;border-radius:8px;border:1px solid #e2e8f0;background:{{ $svc->is_active ? '#f0fdf4' : '#fef2f2' }};cursor:pointer;display:flex;align-items:center;justify-content:center;">
                            <span class="ms" style="font-size:16px;color:{{ $svc->is_active ? '#22c55e' : '#ef4444' }};">
                                {{ $svc->is_active ? 'toggle_on' : 'toggle_off' }}
                            </span>
                        </button>
                    </form>
                    {{-- Edit --}}
                    <button onclick="openEditModal({{ $svc->id }},'{{ addslashes($svc->name) }}','{{ $svc->category }}','{{ addslashes($svc->unit ?? '') }}',{{ $svc->price }},'{{ addslashes($svc->description ?? '') }}',{{ $svc->sort_order }},{{ $svc->is_ironing ? 1 : 0 }})"
                        style="width:30px;height:30px;border-radius:8px;border:1px solid #e2e8f0;background:#f8fafc;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        <span class="ms" style="font-size:16px;color:#64748b;">edit</span>
                    </button>
                    {{-- Delete --}}
                    <form action="{{ route('services.destroy', $svc) }}" method="POST" style="margin:0;"
                          onsubmit="return confirm('Hapus layanan {{ addslashes($svc->name) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            style="width:30px;height:30px;border-radius:8px;border:1px solid #fee2e2;background:#fef2f2;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                            <span class="ms" style="font-size:16px;color:#ef4444;">delete</span>
                        </button>
                    </form>
                </div>
            </div>

            <div>
                <p style="font-size:16px;font-weight:700;color:#1e293b;margin:0;">{{ $svc->name }}</p>
                @if($svc->is_ironing)
                <span style="font-size:11px;background:#f0fdf9;color:#0d9488;padding:2px 8px;border-radius:999px;font-weight:600;display:inline-block;margin-top:4px;">🔥 Setrika</span>
                @endif
                @if($svc->description)
                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">{{ $svc->description }}</p>
                @endif
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:8px;border-top:1px solid #f1f5f9;">
                <span style="font-size:22px;font-weight:800;color:#0d9488;">
                    Rp {{ number_format($svc->price, 0, ',', '.') }}
                </span>
                @if($svc->unit)
                <span style="font-size:12px;color:#94a3b8;background:#f8fafc;padding:3px 10px;border-radius:999px;">/ {{ $svc->unit }}</span>
                @endif
            </div>

            @if($svc->sort_order > 0)
            <p style="font-size:11px;color:#cbd5e1;margin:0;">Urutan tampil: {{ $svc->sort_order }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endforeach

{{-- All tab section --}}
<div class="tab-section" id="section-all" style="display:none;">
    <div class="svc-grid">
        @foreach($services as $svc)
        <div class="svc-card {{ $svc->is_active ? '' : 'svc-inactive' }}">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span class="svc-badge svc-badge-{{ $svc->category }}">
                    {{ Service::$categoryLabels[$svc->category] ?? $svc->category }}
                </span>
                <div style="display:flex;gap:6px;">
                    <form action="{{ route('services.toggle', $svc) }}" method="POST" style="margin:0;">
                        @csrf @method('PATCH')
                        <button type="submit" title="{{ $svc->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                            style="width:30px;height:30px;border-radius:8px;border:1px solid #e2e8f0;background:{{ $svc->is_active ? '#f0fdf4' : '#fef2f2' }};cursor:pointer;display:flex;align-items:center;justify-content:center;">
                            <span class="ms" style="font-size:16px;color:{{ $svc->is_active ? '#22c55e' : '#ef4444' }};">
                                {{ $svc->is_active ? 'toggle_on' : 'toggle_off' }}
                            </span>
                        </button>
                    </form>
                    <button onclick="openEditModal({{ $svc->id }},'{{ addslashes($svc->name) }}','{{ $svc->category }}','{{ addslashes($svc->unit ?? '') }}',{{ $svc->price }},'{{ addslashes($svc->description ?? '') }}',{{ $svc->sort_order }})"
                        style="width:30px;height:30px;border-radius:8px;border:1px solid #e2e8f0;background:#f8fafc;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        <span class="ms" style="font-size:16px;color:#64748b;">edit</span>
                    </button>
                    <form action="{{ route('services.destroy', $svc) }}" method="POST" style="margin:0;"
                          onsubmit="return confirm('Hapus layanan {{ addslashes($svc->name) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            style="width:30px;height:30px;border-radius:8px;border:1px solid #fee2e2;background:#fef2f2;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                            <span class="ms" style="font-size:16px;color:#ef4444;">delete</span>
                        </button>
                    </form>
                </div>
            </div>
            <div>
                <p style="font-size:16px;font-weight:700;color:#1e293b;margin:0;">{{ $svc->name }}</p>
                @if($svc->description)
                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">{{ $svc->description }}</p>
                @endif
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:8px;border-top:1px solid #f1f5f9;">
                <span style="font-size:22px;font-weight:800;color:#0d9488;">Rp {{ number_format($svc->price, 0, ',', '.') }}</span>
                @if($svc->unit)
                <span style="font-size:12px;color:#94a3b8;background:#f8fafc;padding:3px 10px;border-radius:999px;">/ {{ $svc->unit }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ═══ ADD MODAL ═══ --}}
<div class="modal-overlay" id="addModal">
    <div class="modal-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 style="font-size:18px;font-weight:700;color:#1e293b;margin:0;">Tambah Layanan</h2>
            <button onclick="closeModal('addModal')" style="background:none;border:none;cursor:pointer;font-size:20px;color:#94a3b8;">✕</button>
        </div>
        <form action="{{ route('services.store') }}" method="POST">
            @csrf
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Nama Layanan *</label>
                    <input type="text" name="name" id="add_name" required placeholder="Contoh: Cuci + Setrika, Sepatu..."
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Kategori *</label>
                        <select name="category" id="add_category" required
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;background:#fff;">
                            <option value="kiloan">Kiloan</option>
                            <option value="satuan">Satuan</option>
                            <option value="ongkir">Ongkir</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Satuan Unit</label>
                        <input type="text" name="unit" id="add_unit" placeholder="kg / pcs / item"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Harga (Rp) *</label>
                        <input type="number" name="price" id="add_price" required min="0" step="500" placeholder="0"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Urutan Tampil</label>
                        <input type="number" name="sort_order" value="0" min="0"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Deskripsi (Opsional)</label>
                    <input type="text" name="description" placeholder="Contoh: Termasuk lipat rapi"
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;">
                </div>
                <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f0fdf9;border-radius:10px;border:1px solid rgba(13,148,136,.2);">
                    <input type="checkbox" name="is_ironing" id="add_is_ironing" value="1" style="width:16px;height:16px;accent-color:#0d9488;cursor:pointer;">
                    <label for="add_is_ironing" style="font-size:13px;font-weight:600;color:#0f172a;cursor:pointer;">Layanan ini melibatkan setrika (tampilkan PJ Setrika)</label>
                </div>
                <div style="display:flex;gap:10px;margin-top:6px;">
                    <button type="button" onclick="closeModal('addModal')"
                        style="flex:1;padding:12px;border:1.5px solid #e2e8f0;border-radius:12px;background:#f8fafc;font-weight:600;font-size:14px;cursor:pointer;color:#64748b;">Batal</button>
                    <button type="submit"
                        style="flex:2;padding:12px;border:none;border-radius:12px;background:#0d9488;color:#fff;font-weight:700;font-size:14px;cursor:pointer;">
                        ✓ Simpan Layanan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ═══ EDIT MODAL ═══ --}}
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 style="font-size:18px;font-weight:700;color:#1e293b;margin:0;">Edit Layanan</h2>
            <button onclick="closeModal('editModal')" style="background:none;border:none;cursor:pointer;font-size:20px;color:#94a3b8;">✕</button>
        </div>
        <form id="editForm" action="" method="POST">
            @csrf @method('PUT')
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Nama Layanan *</label>
                    <input type="text" name="name" id="edit_name" required
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Kategori *</label>
                        <select name="category" id="edit_category" required
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;background:#fff;">
                            <option value="kiloan">Kiloan</option>
                            <option value="satuan">Satuan</option>
                            <option value="ongkir">Ongkir</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Satuan Unit</label>
                        <input type="text" name="unit" id="edit_unit"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Harga (Rp) *</label>
                        <input type="number" name="price" id="edit_price" required min="0" step="500"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Urutan Tampil</label>
                        <input type="number" name="sort_order" id="edit_sort" min="0"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px;">Deskripsi</label>
                    <input type="text" name="description" id="edit_desc"
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;outline:none;">
                </div>
                <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f0fdf9;border-radius:10px;border:1px solid rgba(13,148,136,.2);">
                    <input type="checkbox" name="is_ironing" id="edit_is_ironing" value="1" style="width:16px;height:16px;accent-color:#0d9488;cursor:pointer;">
                    <label for="edit_is_ironing" style="font-size:13px;font-weight:600;color:#0f172a;cursor:pointer;">Layanan ini melibatkan setrika (tampilkan PJ Setrika)</label>
                </div>
                <div style="display:flex;gap:10px;margin-top:6px;">
                    <button type="button" onclick="closeModal('editModal')"
                        style="flex:1;padding:12px;border:1.5px solid #e2e8f0;border-radius:12px;background:#f8fafc;font-weight:600;font-size:14px;cursor:pointer;color:#64748b;">Batal</button>
                    <button type="submit"
                        style="flex:2;padding:12px;border:none;border-radius:12px;background:#0d9488;color:#fff;font-weight:700;font-size:14px;cursor:pointer;">
                        ✓ Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Tab switching ──────────────────────────────────────────────────
const tabMap = { all:'section-all', kiloan:'section-kiloan', satuan:'section-satuan', ongkir:'section-ongkir' };
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-section').forEach(s => s.style.display = 'none');
    document.getElementById('tab-' + tab).classList.add('active');
    document.getElementById(tabMap[tab]).style.display = 'block';
}
// init: show kiloan first
switchTab('kiloan');

// ── Modal helpers ──────────────────────────────────────────────────
function openAddModal(cat = 'kiloan') {
    document.getElementById('add_category').value     = cat;
    document.getElementById('add_name').value         = '';
    document.getElementById('add_price').value        = '';
    document.getElementById('add_unit').value         = '';
    document.getElementById('add_is_ironing').checked = false;
    document.getElementById('addModal').classList.add('open');
}
function openEditModal(id, name, category, unit, price, description, sort, isIroning) {
    const base = '{{ url("/services") }}';
    document.getElementById('editForm').action             = base + '/' + id;
    document.getElementById('edit_name').value             = name;
    document.getElementById('edit_category').value         = category;
    document.getElementById('edit_unit').value             = unit;
    document.getElementById('edit_price').value            = price;
    document.getElementById('edit_desc').value             = description;
    document.getElementById('edit_sort').value             = sort;
    document.getElementById('edit_is_ironing').checked     = isIroning === 1;
    document.getElementById('editModal').classList.add('open');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}
// Close on overlay click
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});
</script>
@endpush
