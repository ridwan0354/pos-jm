<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name', 'category', 'unit', 'price', 'description', 'is_active', 'sort_order', 'is_ironing',
    ];

    protected $casts = [
        'price'      => 'float',
        'is_active'  => 'boolean',
        'is_ironing' => 'boolean',
    ];

    public static array $categoryLabels = [
        'kiloan' => 'Kiloan',
        'satuan' => 'Satuan',
        'ongkir' => 'Ongkir',
    ];
}
