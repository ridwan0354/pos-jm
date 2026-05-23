<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $table    = 'staff';
    protected $fillable = ['name', 'phone', 'fee_per_kg', 'is_active'];
    protected $casts    = [
        'is_active'   => 'boolean',
        'fee_per_kg'  => 'decimal:2',
    ];

    /** Semua order yang ditangani staff ini. */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'ironing_staff_id');
    }

    /** Total kg setrika bulan ini. */
    public function monthlyKg(): float
    {
        return (float) $this->orders()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereIn('service_type', ['cuci_setrika', 'setrika_saja'])
            ->sum('weight');
    }

    /** Total fee yang diterima bulan ini. */
    public function monthlyFee(): float
    {
        return (float) $this->orders()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('ironing_fee');
    }
}
