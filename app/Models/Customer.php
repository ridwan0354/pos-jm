<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'referral_code', 'referred_by',
        'member_level', 'wallet_balance', 'total_weight',
        'total_orders', 'address', 'email',
    ];

    protected $casts = [
        'wallet_balance' => 'float',
        'total_weight'   => 'float',
        'total_orders'   => 'integer',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function getMemberBadgeColorAttribute(): string
    {
        return match ($this->member_level) {
            'Silver' => 'text-slate-500 bg-slate-100',
            'Gold'   => 'text-amber-600 bg-amber-100',
            default  => 'text-orange-700 bg-orange-100',
        };
    }

    public static function generateReferralCode(string $name): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-z]/i', '', $name), 0, 3));
        do {
            $code = $prefix . strtoupper(substr(uniqid(), -5));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }
}
