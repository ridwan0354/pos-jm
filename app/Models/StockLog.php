<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    protected $fillable = [
        'stock_id', 'type', 'qty', 'qty_before', 'qty_after', 'reason', 'created_by',
    ];

    protected $casts = [
        'qty'        => 'float',
        'qty_before' => 'float',
        'qty_after'  => 'float',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
