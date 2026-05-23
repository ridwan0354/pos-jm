<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    protected $fillable = [
        'name', 'category', 'unit', 'quantity', 'min_quantity', 'price_per_unit', 'notes',
    ];

    protected $casts = [
        'quantity'       => 'float',
        'min_quantity'   => 'float',
        'price_per_unit' => 'float',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(StockLog::class);
    }

    public function getIsLowAttribute(): bool
    {
        return $this->quantity <= $this->min_quantity;
    }
}
