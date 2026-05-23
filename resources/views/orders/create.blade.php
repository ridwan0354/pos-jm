@extends('layouts.app')
@section('title', 'Pesanan Baru')

@push('styles')
<style>
.order-create-layout {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}
.order-create-left {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.order-create-right {
    width: 340px;
    flex-shrink: 0;
}
.order-summary-sticky {
    position: sticky;
    top: 80px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
@media (max-width: 768px) {
    .order-create-layout {
        flex-direction: column;
    }
    .order-create-left {
        width: 100%;
        box-sizing: border-box;
    }
    .order-create-right {
        width: 100%;
        box-sizing: border-box;
    }
    .order-summary-sticky {
        position: static;
    }
}

/* Hide native inputs cleanly */
.sr-only {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border-width: 0 !important;
}

/* Peer checked logic for custom inputs */
.peer:checked + div,
.peer:checked + .peer-checked\:bg-primary {
    border-color: #0d9488 !important;
    background-color: rgba(13, 148, 136, 0.05) !important;
}
.peer:checked + div span,
.peer:checked + div p {
    color: #0d9488 !important;
}

/* Payment Status toggle overrides */
.peer:checked + .peer-checked\:bg-primary {
    background-color: #0d9488 !important;
    color: #ffffff !important;
}
.peer:checked + .peer-checked\:bg-amber-500 {
    background-color: #f59e0b !important;
    color: #ffffff !important;
}
</style>
@endpush

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('orders.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-white border border-surface-border">
        <span class="ms text-ink-muted">arrow_back</span>
    </a>
    <div>
        <h1 class="text-xl font-bold text-ink">Pesanan Baru</h1>
        <p class="text-xs text-ink-muted">Buat pesanan untuk pelanggan</p>
    </div>
</div>

<form id="orderForm" action="{{ route('orders.store') }}" method="POST">
@csrf
<div class="order-create-layout">

    {{-- Left Column --}}
    <div class="order-create-left">

        {{-- Customer Section --}}
        <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
            <h2 class="font-bold text-ink mb-4 flex items-center gap-2">
                <span class="ms ms-fill text-primary text-xl">person</span> Informasi Pelanggan
            </h2>

            {{-- Search existing --}}
            <div class="mb-4">
                <label class="form-label">Cari Pelanggan Terdaftar</label>
                <div class="relative">
                    <span class="ms absolute left-3 top-1/2 -translate-y-1/2 text-ink-faint">search</span>
                    <input type="text" id="customerSearch" placeholder="Nama, nomor HP, atau kode referral…"
                           class="form-input pl-10" autocomplete="off">
                    <div id="searchDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-card-hover border border-surface-border z-30 max-h-52 overflow-y-auto">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 my-4">
                <div class="h-px bg-slate-100 flex-1"></div>
                <span class="text-xs text-ink-faint font-semibold uppercase tracking-wider">atau isi manual</span>
                <div class="h-px bg-slate-100 flex-1"></div>
            </div>

            <input type="hidden" name="customer_id" id="customerId">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_name" id="customerName" class="form-input" placeholder="Nama pelanggan" required>
                </div>
                <div>
                    <label class="form-label">Nomor Telepon <span class="text-red-500">*</span></label>
                    <input type="tel" name="customer_phone" id="customerPhone" class="form-input" placeholder="08xx xxxx xxxx" required>
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Kode Referral (Opsional)</label>
                    <input type="text" name="referral_code" id="referralCode" class="form-input" placeholder="Kode referral teman">
                    <p class="text-xs text-ink-faint mt-1">Referrer mendapat 10% komisi dari total order</p>
                </div>
            </div>
            {{-- Selected customer badge --}}
            <div id="selectedCustomerBadge" class="hidden mt-3 flex items-center gap-3 p-3 bg-primary/5 border border-primary/20 rounded-xl">
                <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm flex-shrink-0" id="scBadgeInitial">?</div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-ink text-sm" id="scBadgeName">-</p>
                    <p class="text-xs text-ink-muted" id="scBadgeInfo">-</p>
                </div>
                <button type="button" onclick="clearCustomer()" class="text-ink-faint hover:text-ink">
                    <span class="ms text-sm">close</span>
                </button>
            </div>
        </div>

        {{-- Service Details --}}
        <div style="background:#fff;border-radius:20px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid #e2e8f0;">
            <h2 style="font-weight:700;color:#0f172a;margin:0 0 16px;display:flex;align-items:center;gap:8px;">
                <span class="ms ms-fill" style="color:#0d9488;font-size:20px;">local_laundry_service</span> Detail Layanan
            </h2>

            {{-- Kategori --}}
            <div style="margin-bottom:20px;">
                <label class="form-label">Kategori Layanan</label>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                    @foreach(['kiloan'=>['scale','Kiloan'],'satuan'=>['apparel','Satuan'],'ongkir'=>['local_shipping','Ongkir']] as $val=>[$icon,$label])
                    <label style="cursor:pointer;">
                        <input type="radio" name="category" value="{{ $val }}"
                               style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;"
                               {{ $val==='kiloan'?'checked':'' }} onchange="switchCategory('{{ $val }}')">
                        <div id="cat_card_{{ $val }}"
                             onclick="setActiveCard('cat_card_{{ $val }}', 'cat_group')"
                             style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:12px 8px;border-radius:14px;border:2px solid #e2e8f0;transition:all .2s;{{ $val==='kiloan'?'border-color:#0d9488;background:rgba(13,148,136,.07);':'' }}">
                            <span class="ms ms-fill" id="cat_icon_{{ $val }}"
                                  style="font-size:24px;{{ $val==='kiloan'?'color:#0d9488;':'color:#94a3b8;' }}">{{ $icon }}</span>
                            <span id="cat_label_{{ $val }}"
                                  style="font-size:13px;font-weight:600;{{ $val==='kiloan'?'color:#0d9488;':'color:#64748b;' }}">{{ $label }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Kiloan Section --}}
            <div id="kiloanSection">
                <div style="margin-bottom:16px;">
                    <label class="form-label">Jenis Layanan</label>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                        @foreach($services['kiloan'] ?? [] as $svc)
                        <label style="cursor:pointer;display:block;">
                            <input type="radio" name="service_type" value="{{ $svc->id }}"
                                   data-price="{{ $svc->price }}" data-name="{{ $svc->name }}"
                                   data-ironing="{{ $svc->is_ironing ? '1' : '0' }}"
                                   style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;"
                                   {{ $loop->first?'checked':'' }} onchange="setActiveCard('svc_card_{{ $svc->id }}','svc_group'); onServiceTypeChange(this); recalcTotal()">
                            <div id="svc_card_{{ $svc->id }}"
                                 style="padding:14px 12px;border-radius:14px;background:{{ $loop->first?'#f0fdf9':'#f8fafc' }};border:2px solid {{ $loop->first?'#0d9488':'transparent' }};transition:all .2s;">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                    <div id="svc_dot_{{ $svc->id }}" style="width:9px;height:9px;border-radius:50%;background:{{ $loop->first?'#0d9488':'#cbd5e1' }};transition:background .15s;"></div>
                                    <span id="svc_check_{{ $svc->id }}" class="ms ms-fill" style="font-size:17px;color:{{ $loop->first?'#0d9488':'transparent' }};transition:color .15s;">check_circle</span>
                                </div>
                                <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 3px;">{{ $svc->name }}</p>
                                <p style="font-size:12px;color:#64748b;margin:0;">Rp {{ number_format($svc->price,0,',','.') }}/kg</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div style="margin-bottom:16px;">
                    <label class="form-label">Berat Cucian (Kg)</label>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input type="number" name="weight" id="weightInput" step="0.1" min="0" value="0.0"
                               class="form-input" style="width:100px;text-align:right;font-size:18px;font-weight:700;"
                               oninput="recalcTotal();updateFeePreview()" onchange="recalcTotal();updateFeePreview()">
                        <span style="font-size:13px;color:#64748b;">kg</span>
                        <button type="button" onclick="document.getElementById('weightInput').focus()"
                                class="btn-secondary" style="font-size:12px;padding:8px 14px;">
                            <span class="ms" style="font-size:14px;">scale</span> Input Berat
                        </button>
                    </div>
                    <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">Minimum 1 kg</p>
                </div>

                {{-- Staff Setrika (muncul untuk Cuci+Setrika dan Setrika Saja) --}}
                <div id="staffSection" style="display:none;margin-bottom:16px;">
                    <label class="form-label">Penanggung Jawab Setrika</label>
                    <select name="ironing_staff_id" id="ironingStaffSelect" class="form-input" onchange="updateFeePreview()">
                        <option value="">-- Pilih Staff (Opsional) --</option>
                        @foreach($staffList ?? [] as $s)
                        <option value="{{ $s->id }}" data-fee="{{ $s->fee_per_kg }}">
                            {{ $s->name }} — Rp {{ number_format($s->fee_per_kg,0,',','.') }}/kg
                        </option>
                        @endforeach
                    </select>
                    <div id="feePreview" style="display:none;margin-top:8px;padding:10px 12px;background:#f0fdf9;border-radius:10px;border:1px solid rgba(13,148,136,.2);">
                        <p style="font-size:12px;color:#0d9488;font-weight:600;margin:0;" id="feePreviewText"></p>
                    </div>
                </div>
            </div>

            {{-- Satuan Section --}}
            <div id="satuanSection" style="display:none;margin-bottom:16px;">
                @if(isset($services['satuan']) && $services['satuan']->count())
                <label class="form-label">Pilih Item Satuan</label>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                    @foreach($services['satuan'] as $svc)
                    <div id="scard_{{ $svc->id }}"
                         style="padding:12px;border-radius:14px;background:#f8fafc;border:2px solid transparent;transition:all .2s;">
                        <input type="hidden" name="satuan_quantities[{{ $svc->id }}]"
                               id="sqty_{{ $svc->id }}" value="0"
                               data-price="{{ $svc->price }}" data-name="{{ $svc->name }}">
                        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 2px;">{{ $svc->name }}</p>
                        <p style="font-size:11px;color:#64748b;margin:0 0 10px;">Rp {{ number_format($svc->price,0,',','.') }} / pcs</p>
                        <div style="display:flex;align-items:center;justify-content:space-between;background:#fff;border-radius:10px;padding:3px;border:1px solid #e2e8f0;">
                            <button type="button" onclick="changeSatuanQty({{ $svc->id }}, -1)"
                                    style="width:28px;height:28px;border:none;background:#f1f5f9;border-radius:8px;cursor:pointer;font-size:18px;font-weight:700;color:#64748b;display:flex;align-items:center;justify-content:center;line-height:1;">−</button>
                            <span id="sqty_display_{{ $svc->id }}"
                                  style="font-size:15px;font-weight:800;color:#0f172a;min-width:28px;text-align:center;">0</span>
                            <button type="button" onclick="changeSatuanQty({{ $svc->id }}, 1)"
                                    style="width:28px;height:28px;border:none;background:#0d9488;border-radius:8px;cursor:pointer;font-size:18px;font-weight:700;color:#fff;display:flex;align-items:center;justify-content:center;line-height:1;">+</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p style="color:#94a3b8;font-size:13px;text-align:center;padding:20px 0;">Belum ada layanan satuan aktif.</p>
                @endif
            </div>

            {{-- Ongkir Section --}}
            <div id="ongkirSection" style="display:none;margin-bottom:16px;">
                @if(isset($services['ongkir']) && $services['ongkir']->count())
                <label class="form-label">Pilih Ongkir</label>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                    @foreach($services['ongkir'] as $svc)
                    <label style="cursor:pointer;display:block;">
                        <input type="radio" name="ongkir_id" value="{{ $svc->id }}"
                               data-price="{{ $svc->price }}" data-name="{{ $svc->name }}"
                               style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;"
                               {{ $loop->first?'checked':'' }} onchange="recalcTotal()">
                        <div id="ongkir_card_{{ $svc->id }}"
                             onclick="setActiveCard('ongkir_card_{{ $svc->id }}', 'ongkir_group')"
                             style="padding:14px 12px;border-radius:14px;background:{{ $loop->first?'#f0fdf9':'#f8fafc' }};border:2px solid {{ $loop->first?'#0d9488':'transparent' }};transition:all .2s;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                <div id="ongkir_dot_{{ $svc->id }}" style="width:9px;height:9px;border-radius:50%;background:{{ $loop->first?'#0d9488':'#e2e8f0' }};transition:background .15s;"></div>
                                <span id="ongkir_check_{{ $svc->id }}" class="ms ms-fill" style="font-size:17px;color:{{ $loop->first?'#0d9488':'transparent' }};transition:color .15s;">check_circle</span>
                            </div>
                            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 3px;">{{ $svc->name }}</p>
                            <p style="font-size:12px;color:#64748b;margin:0;">Rp {{ number_format($svc->price,0,',','.') }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @else
                <p style="color:#94a3b8;font-size:13px;text-align:center;padding:20px 0;">Belum ada layanan ongkir aktif.</p>
                @endif
            </div>

            <div style="border-top:1px solid #f1f5f9;margin:16px 0;"></div>

            {{-- Options --}}
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label class="form-label">Parfum</label>
                    <select name="perfume" class="form-input">
                        <option value="harum">🌸 Harum (Default)</option>
                        <option value="sakura">🌸 Sakura</option>
                        <option value="lavender">💜 Lavender</option>
                        <option value="tanpa">❌ Tanpa Parfum</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Kecepatan Layanan</label>
                    <select name="speed" id="speedSelect" class="form-input" onchange="recalcTotal()">
                        <option value="reguler">📅 Reguler (3 Hari)</option>
                        <option value="kilat">⚡ Kilat 24 Jam (+50%)</option>
                        <option value="ekspres">🚀 Ekspres 6 Jam (+100%)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-input" style="height:80px;resize:none;" placeholder="Instruksi khusus, warna pakaian, dll…"></textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Summary --}}
    <div class="order-create-right">
        <div class="order-summary-sticky">
            <div class="bg-white rounded-2xl p-5 shadow-card border border-surface-border">
                <h2 class="font-bold text-ink mb-4 flex items-center gap-2">
                    <span class="ms ms-fill text-primary text-xl">receipt_long</span> Ringkasan Biaya
                </h2>
                <div class="space-y-2.5 mb-4 text-sm" id="summaryItems">
                    <div class="flex justify-between text-ink-muted"><span>Pilih layanan…</span><span>—</span></div>
                </div>
                <div class="border-t border-slate-100 pt-3 mb-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-ink-muted">Diskon (Rp)</label>
                        <input type="number" name="discount" id="discountInput" value="0" min="0"
                               class="w-32 px-3 py-1.5 text-right text-sm border border-surface-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                               onchange="recalcTotal()">
                    </div>
                </div>
                <div class="bg-primary/5 rounded-xl p-4 flex items-end justify-between mb-5">
                    <span class="font-bold text-ink">Total</span>
                    <span class="text-2xl font-extrabold text-primary" id="totalDisplay">Rp 0</span>
                </div>

                {{-- Payment Status --}}
                <div class="mb-5">
                    <label class="form-label">Status Pembayaran</label>
                    <div class="grid grid-cols-2 rounded-xl overflow-hidden border border-surface-border">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_status" value="lunas" class="sr-only peer" checked>
                            <div class="py-2.5 text-center text-sm font-semibold peer-checked:bg-primary peer-checked:text-white text-ink-muted transition-colors">
                                ✅ Lunas
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_status" value="belum_lunas" class="sr-only peer">
                            <div class="py-2.5 text-center text-sm font-semibold peer-checked:bg-amber-500 peer-checked:text-white text-ink-muted transition-colors">
                                ⏳ Belum Lunas
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Metode Pembayaran --}}
                <div class="mb-4" id="paymentMethodSection">
                    <label class="form-label">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['cash'=>['💵','Cash'],'transfer'=>['🏦','Transfer'],'qris'=>['📱','QRIS'],'wallet'=>['👛','Wallet']] as $val=>[$icon,$label])
                        <label class="cursor-pointer" onclick="setPaymentMethod('{{ $val }}')">
                            <input type="radio" name="payment_method" value="{{ $val }}" class="sr-only" id="pm_{{ $val }}" {{ $val==='cash'?'checked':'' }}>
                            <div id="pm_card_{{ $val }}" style="padding:10px;text-align:center;font-size:13px;font-weight:600;border:2px solid {{ $val==='cash'?'#0d9488':'#e2e8f0' }};border-radius:12px;transition:all .15s;color:{{ $val==='cash'?'#0d9488':'#64748b' }};background:{{ $val==='cash'?'rgba(13,148,136,.05)':'#fff' }};">
                                {{ $icon }} {{ $label }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                    {{-- Wallet Info --}}
                    <div id="walletInfo" class="hidden mt-3 p-3 rounded-xl" style="background:rgba(13,148,136,.05);border:1px solid rgba(13,148,136,.2);">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-ink-muted">Saldo Wallet</span>
                            <span class="font-bold text-primary" id="walletBalanceDisplay">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ink-muted">Sisa Setelah Bayar</span>
                            <span class="font-bold" id="walletAfterDisplay">Rp 0</span>
                        </div>
                        <p id="walletWarning" class="hidden text-xs text-red-600 font-medium mt-2">⚠️ Saldo tidak mencukupi!</p>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-1.5 text-emerald-600 text-xs font-medium mb-4">
                    <span class="ms ms-fill text-base">verified</span> WhatsApp Notification Ready
                </div>

                <button type="submit" class="w-full btn-primary justify-center py-3 text-base rounded-xl">
                    <span class="ms">task_alt</span> Buat Pesanan
                </button>
            </div>

            <div class="bg-slate-50 rounded-xl p-4 text-xs text-ink-muted text-center border border-surface-border">
                <span class="ms text-sm">info</span>
                Pastikan data pesanan sudah benar sebelum dikonfirmasi.
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
// ── Category card active state ────────────────────────────────────
const catCards   = ['kiloan', 'satuan', 'ongkir'];
const speedRates = { reguler: 0, kilat: 0.5, ekspres: 1.0 };
const ACTIVE_CARD_STYLE  = 'border-color:#0d9488;background:rgba(13,148,136,.07);';
const INACTIVE_CARD_STYLE = 'border-color:#e2e8f0;background:#fff;';
const ACTIVE_TEXT_COLOR  = '#0d9488';
const INACTIVE_TEXT_COLOR = '#94a3b8';
const ACTIVE_LABEL_COLOR = '#0d9488';
const INACTIVE_LABEL_COLOR = '#64748b';

function setActiveCard(activeId, group) {
    // Category cards (3-col grid)
    if (group === 'cat_group') {
        catCards.forEach(cat => {
            const card  = document.getElementById('cat_card_' + cat);
            const icon  = document.getElementById('cat_icon_' + cat);
            const label = document.getElementById('cat_label_' + cat);
            const isActive = ('cat_card_' + cat) === activeId;
            if (card) {
                card.style.cssText = card.style.cssText
                    .replace(/border-color:[^;]+;/g,'')
                    .replace(/background:[^;]+;/g,'')
                    + (isActive ? ACTIVE_CARD_STYLE : INACTIVE_CARD_STYLE);
            }
            if (icon)  icon.style.color  = isActive ? ACTIVE_TEXT_COLOR : INACTIVE_TEXT_COLOR;
            if (label) label.style.color = isActive ? ACTIVE_LABEL_COLOR : INACTIVE_LABEL_COLOR;
        });
    }

    // Service radio rows (kiloan)
    if (group === 'svc_group') {
        document.querySelectorAll('[id^="svc_card_"]').forEach(row => {
            const id = row.id.replace('svc_card_', '');
            const isActive = row.id === activeId;
            row.style.background  = isActive ? '#f0fdf9' : '#f8fafc';
            row.style.borderColor = isActive ? '#0d9488' : 'transparent';
            const dot   = document.getElementById('svc_dot_'   + id);
            const check = document.getElementById('svc_check_' + id);
            if (dot)   dot.style.background = isActive ? '#0d9488' : '#cbd5e1';
            if (check) check.style.color    = isActive ? '#0d9488' : 'transparent';
        });
    }

    // Ongkir radio rows
    if (group === 'ongkir_group') {
        document.querySelectorAll('[id^="ongkir_card_"]').forEach(row => {
            const id = row.id.replace('ongkir_card_', '');
            const isActive = row.id === activeId;
            row.style.background  = isActive ? '#f0fdf9' : '#f8fafc';
            row.style.borderColor = isActive ? '#0d9488' : 'transparent';
            const dot   = document.getElementById('ongkir_dot_'   + id);
            const check = document.getElementById('ongkir_check_' + id);
            if (dot)   dot.style.background = isActive ? '#0d9488' : '#e2e8f0';
            if (check) check.style.color    = isActive ? '#0d9488' : 'transparent';
        });
    }
}

// Toggle satuan checkbox row — membaca checkbox.checked aktual untuk state yang benar
function toggleSatuanRow(rowId, dotId, checkId) {
    const row   = document.getElementById(rowId);
    const dot   = document.getElementById(dotId);
    const check = document.getElementById(checkId);
    if (!row) return;
    // Baca state checkbox aktual dari parent label
    const cb = row.closest('label')?.querySelector('input[type="checkbox"]');
    const isChecked = cb ? cb.checked : false;
    row.style.background  = isChecked ? '#f0fdf9' : '#f8fafc';
    row.style.borderColor = isChecked ? '#0d9488' : 'transparent';
    if (dot)   dot.style.background = isChecked ? '#0d9488' : '#e2e8f0';
    if (check) check.style.color    = isChecked ? '#0d9488' : 'transparent';
}

function toggleCheckAndCard(rowDiv) {
    const label    = rowDiv.closest('label');
    const checkbox = label ? label.querySelector('input[type="checkbox"]') : null;
    if (!checkbox) return;
    checkbox.checked = !checkbox.checked;
    checkbox.dispatchEvent(new Event('change'));
}

function changeSatuanQty(id, delta) {
    const inp     = document.getElementById('sqty_' + id);
    const display = document.getElementById('sqty_display_' + id);
    const card    = document.getElementById('scard_' + id);
    if (!inp) return;
    let qty = parseInt(inp.value) + delta;
    if (qty < 0) qty = 0;
    if (qty > 99) qty = 99;
    inp.value           = qty;
    display.textContent = qty;
    if (qty > 0) {
        card.style.borderColor = '#0d9488';
        card.style.background  = 'rgba(13,148,136,.05)';
        display.style.color    = '#0d9488';
    } else {
        card.style.borderColor = 'transparent';
        card.style.background  = '#f8fafc';
        display.style.color    = '#0f172a';
    }
    recalcTotal();
}

function onServiceTypeChange(input) {
    const isIroning   = input.dataset.ironing === '1';
    const staffSection = document.getElementById('staffSection');
    if (!staffSection) return;
    staffSection.style.display = isIroning ? 'block' : 'none';
    if (!isIroning) {
        document.getElementById('ironingStaffSelect').value = '';
        document.getElementById('feePreview').style.display = 'none';
    }
    updateFeePreview();
}

function updateFeePreview() {
    const select  = document.getElementById('ironingStaffSelect');
    const preview = document.getElementById('feePreview');
    const text    = document.getElementById('feePreviewText');
    if (!select || !select.value) { if (preview) preview.style.display = 'none'; return; }
    const feePerKg = parseFloat(select.options[select.selectedIndex].dataset.fee) || 0;
    const weight   = parseFloat(document.getElementById('weightInput')?.value) || 0;
    const total    = feePerKg * weight;
    if (feePerKg > 0 && weight > 0) {
        text.textContent = `Fee staff: ${weight} kg × Rp ${feePerKg.toLocaleString('id-ID')}/kg = Rp ${total.toLocaleString('id-ID')}`;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

function switchCategory(cat) {
    // Show/hide sections
    document.getElementById('kiloanSection').style.display  = (cat === 'kiloan')  ? 'block' : 'none';
    document.getElementById('satuanSection').style.display  = (cat === 'satuan')  ? 'block' : 'none';
    document.getElementById('ongkirSection').style.display  = (cat === 'ongkir')  ? 'block' : 'none';
    // Update category card active state
    setActiveCard('cat_card_' + cat, 'cat_group');
    recalcTotal();
}

function recalcTotal() {
    const cat = document.querySelector('input[name="category"]:checked')?.value ?? 'kiloan';
    let subtotal = 0;
    let lines = [];

    if (cat === 'kiloan') {
        const svcInput  = document.querySelector('input[name="service_type"]:checked');
        const weight    = parseFloat(document.getElementById('weightInput').value) || 0;
        const pricePerKg = parseFloat(svcInput?.dataset?.price) || 7000;
        const name       = svcInput?.dataset?.name || 'Layanan Kiloan';
        subtotal = weight * pricePerKg;
        lines.push({
            label: name,
            detail: `${weight > 0 ? weight : '0'} kg × Rp ${pricePerKg.toLocaleString('id-ID')}`,
            value: subtotal
        });
    }

    if (cat === 'satuan') {
        document.querySelectorAll('input[id^="sqty_"]').forEach(inp => {
            const qty = parseInt(inp.value) || 0;
            if (qty <= 0) return;
            const price = parseFloat(inp.dataset.price) || 0;
            const name  = inp.dataset.name || 'Item';
            subtotal += price * qty;
            lines.push({ label: name, detail: qty > 1 ? `${qty} × Rp ${price.toLocaleString('id-ID')}` : '', value: price * qty });
        });
    }

    if (cat === 'ongkir') {
        const ongkirInput = document.querySelector('input[name="ongkir_id"]:checked');
        if (ongkirInput) {
            const price = parseFloat(ongkirInput.dataset.price) || 0;
            const name  = ongkirInput.dataset.name || 'Ongkir';
            subtotal = price;
            lines.push({ label: name, detail: '', value: price });
        }
    }

    const speed = document.getElementById('speedSelect').value;
    const surcharge = subtotal * (speedRates[speed] ?? 0);
    if (surcharge > 0) {
        const speedLabel = { kilat:'Surcharge Kilat (+50%)', ekspres:'Surcharge Ekspres (+100%)' }[speed] ?? '';
        lines.push({ label: speedLabel, detail:'', value: surcharge });
    }

    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total = Math.max(0, subtotal + surcharge - discount);

    const summaryEl = document.getElementById('summaryItems');
    const emptyMsg  = cat === 'satuan' ? 'Pilih item satuan…' : (cat === 'ongkir' ? 'Pilih layanan ongkir…' : 'Masukkan berat cucian…');
    if (lines.length === 0) {
        summaryEl.innerHTML = `<div style="color:#94a3b8;font-style:italic;text-align:center;padding:8px 0;">${emptyMsg}</div>`;
    } else {
        summaryEl.innerHTML = lines.map(l => `
            <div style="padding-bottom:8px;border-bottom:1px solid #f8fafc;">
                <div style="display:flex;justify-content:space-between;font-weight:500;color:#1e293b;">
                    <span>${l.label}</span>
                    <span style="color:#0d9488;font-weight:600;">Rp ${l.value.toLocaleString('id-ID')}</span>
                </div>
                ${l.detail ? `<div style="font-size:12px;color:#94a3b8;margin-top:2px;">${l.detail}</div>` : ''}
            </div>`).join('');
    }

    if (discount > 0) {
        summaryEl.innerHTML += `<div style="display:flex;justify-content:space-between;color:#ef4444;font-weight:500;padding-top:4px;"><span>Diskon</span><span>- Rp ${discount.toLocaleString('id-ID')}</span></div>`;
    }

    document.getElementById('totalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
    const totalEl = document.getElementById('totalDisplay');
    totalEl.style.transform = 'scale(1.05)';
    setTimeout(() => totalEl.style.transform = '', 200);
    if (document.querySelector('input[name="payment_method"]:checked')?.value === 'wallet') {
        updateWalletInfo();
    }
}

// Customer search
let searchTimeout;
document.getElementById('customerSearch').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    if (q.length < 2) { document.getElementById('searchDropdown').classList.add('hidden'); return; }
    searchTimeout = setTimeout(() => {
        fetch(`/api/customers/search?q=${encodeURIComponent(q)}`)
            .then(r => r.json()).then(customers => {
                const dd = document.getElementById('searchDropdown');
                if (!customers.length) { dd.classList.add('hidden'); return; }
                dd.innerHTML = customers.map(c => `
                    <div class="px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-surface-border last:border-0"
                         onclick="selectCustomer(${c.id},'${c.name}','${c.phone}','${c.member_level}',${c.wallet_balance})">
                        <p class="font-semibold text-sm text-ink">${c.name}</p>
                        <p class="text-xs text-ink-muted">${c.phone} · ${c.member_level}</p>
                    </div>`).join('');
                dd.classList.remove('hidden');
            });
    }, 300);
});

let customerWalletBalance = 0;

function selectCustomer(id, name, phone, level, wallet) {
    document.getElementById('customerId').value = id;
    document.getElementById('customerName').value = name;
    document.getElementById('customerPhone').value = phone;
    document.getElementById('searchDropdown').classList.add('hidden');
    document.getElementById('customerSearch').value = '';
    const badge = document.getElementById('selectedCustomerBadge');
    document.getElementById('scBadgeInitial').textContent = name[0].toUpperCase();
    document.getElementById('scBadgeName').textContent = name;
    document.getElementById('scBadgeInfo').textContent = `${phone} · ${level} · Wallet: Rp ${wallet.toLocaleString('id-ID')}`;
    badge.classList.remove('hidden');
    customerWalletBalance = wallet;
    if (document.querySelector('input[name="payment_method"]:checked')?.value === 'wallet') {
        updateWalletInfo();
    }
}

function clearCustomer() {
    document.getElementById('customerId').value = '';
    document.getElementById('customerName').value = '';
    document.getElementById('customerPhone').value = '';
    document.getElementById('selectedCustomerBadge').classList.add('hidden');
    customerWalletBalance = 0;
}

function setPaymentMethod(method) {
    document.getElementById('pm_' + method).checked = true;
    ['cash','transfer','qris','wallet'].forEach(m => {
        const card = document.getElementById('pm_card_' + m);
        if (!card) return;
        card.style.borderColor = m === method ? '#0d9488' : '#e2e8f0';
        card.style.color       = m === method ? '#0d9488' : '#64748b';
        card.style.background  = m === method ? 'rgba(13,148,136,.05)' : '#fff';
    });
    onPaymentMethodChange(method);
}

function onPaymentMethodChange(method) {
    const walletInfo = document.getElementById('walletInfo');
    if (method === 'wallet') {
        walletInfo.classList.remove('hidden');
        updateWalletInfo();
    } else {
        walletInfo.classList.add('hidden');
    }
}

function updateWalletInfo() {
    const total   = parseInt(document.getElementById('totalDisplay').textContent.replace(/[^0-9]/g, '')) || 0;
    const balance = customerWalletBalance;
    const after   = balance - total;
    document.getElementById('walletBalanceDisplay').textContent = 'Rp ' + balance.toLocaleString('id-ID');
    document.getElementById('walletAfterDisplay').textContent   = 'Rp ' + Math.abs(after).toLocaleString('id-ID');
    document.getElementById('walletAfterDisplay').style.color   = after < 0 ? '#dc2626' : '#0d9488';
    const warn = document.getElementById('walletWarning');
    after < 0 ? warn.classList.remove('hidden') : warn.classList.add('hidden');
}

recalcTotal();

// Inisialisasi staff section berdasarkan service type yang sudah terpilih saat halaman load
document.addEventListener('DOMContentLoaded', function () {
    const initSvc = document.querySelector('input[name="service_type"]:checked');
    if (initSvc) onServiceTypeChange(initSvc);
});
</script>
@endpush
