<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'customer_id', 'customer_name', 'customer_phone',
        'category', 'service_type', 'weight', 'perfume', 'speed', 'estimated_done',
        'subtotal', 'speed_surcharge', 'discount', 'total',
        'payment_status', 'payment_method', 'paid_at',
        'status', 'notes', 'created_by',
        'ironing_staff_id', 'ironing_fee',
    ];

    protected $casts = [
        'weight'          => 'float',
        'subtotal'        => 'float',
        'speed_surcharge' => 'float',
        'discount'        => 'float',
        'total'           => 'float',
        'ironing_fee'     => 'float',
        'estimated_done'  => 'date',
        'paid_at'         => 'datetime',
    ];

    // Status labels in Indonesian
    public static array $statusLabels = [
        'antri'      => 'Antri',
        'proses'     => 'Proses',
        'selesai'    => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    // Status badge colors
    public static array $statusColors = [
        'antri'      => 'bg-slate-100 text-slate-600',
        'proses'     => 'bg-blue-100 text-blue-700',
        'selesai'    => 'bg-teal-100 text-teal-700',
        'dibatalkan' => 'bg-red-100 text-red-600',
    ];

    // Status icons
    public static array $statusIcons = [
        'antri'      => 'hourglass_empty',
        'proses'     => 'autorenew',
        'selesai'    => 'task_alt',
        'dibatalkan' => 'cancel',
    ];

    // Speed surcharge rates
    public static array $speedRates = [
        'reguler' => 0,
        'kilat'   => 0.5,
        'ekspres' => 1.0,
    ];

    public function ironingStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'ironing_staff_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        $statusMap = [
            'dicuci'     => 'proses',
            'dijemur'    => 'proses',
            'disetrika'  => 'proses',
            'siap_ambil' => 'proses'
        ];
        $normalized = $statusMap[$this->status] ?? $this->status;
        return self::$statusLabels[$normalized] ?? $normalized;
    }

    public function getStatusColorAttribute(): string
    {
        $statusMap = [
            'dicuci'     => 'proses',
            'dijemur'    => 'proses',
            'disetrika'  => 'proses',
            'siap_ambil' => 'proses'
        ];
        $normalized = $statusMap[$this->status] ?? $this->status;
        return self::$statusColors[$normalized] ?? 'bg-gray-100 text-gray-600';
    }

    public function getStatusIconAttribute(): string
    {
        $statusMap = [
            'dicuci'     => 'proses',
            'dijemur'    => 'proses',
            'disetrika'  => 'proses',
            'siap_ambil' => 'proses'
        ];
        $normalized = $statusMap[$this->status] ?? $this->status;
        return self::$statusIcons[$normalized] ?? 'help';
    }

    public static function generateOrderNumber(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "LF-{$date}-";
        $last   = self::where('order_number', 'like', $prefix . '%')
                      ->orderByDesc('id')->first();
        $seq    = $last ? (intval(substr($last->order_number, -4)) + 1) : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
