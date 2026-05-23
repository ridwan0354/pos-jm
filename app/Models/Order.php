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
        'dicuci'     => 'Dicuci',
        'dijemur'    => 'Dijemur',
        'disetrika'  => 'Disetrika',
        'siap_ambil' => 'Siap Ambil',
        'selesai'    => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    // Status badge colors
    public static array $statusColors = [
        'antri'      => 'bg-slate-100 text-slate-600',
        'dicuci'     => 'bg-blue-100 text-blue-700',
        'dijemur'    => 'bg-yellow-100 text-yellow-700',
        'disetrika'  => 'bg-purple-100 text-purple-700',
        'siap_ambil' => 'bg-green-100 text-green-700',
        'selesai'    => 'bg-teal-100 text-teal-700',
        'dibatalkan' => 'bg-red-100 text-red-600',
    ];

    // Status icons
    public static array $statusIcons = [
        'antri'      => 'hourglass_empty',
        'dicuci'     => 'water_drop',
        'dijemur'    => 'wb_sunny',
        'disetrika'  => 'iron',
        'siap_ambil' => 'check_circle',
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
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? 'bg-gray-100 text-gray-600';
    }

    public function getStatusIconAttribute(): string
    {
        return self::$statusIcons[$this->status] ?? 'help';
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
