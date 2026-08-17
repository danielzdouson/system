<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminBankDetail extends Model
{
    protected $fillable = [
        'bank_name',
        'account_name',
        'account_number',
        'branch',
        'swift_code',
        'is_active',
        'is_default',
        'mobile_money_provider',
        'mobile_money_number',
        'instructions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeMobileMoney($query)
    {
        return $query->whereNotNull('mobile_money_provider');
    }

    public function scopeBankTransfer($query)
    {
        return $query->whereNull('mobile_money_provider');
    }

    public function getProviderLabelAttribute(): string
    {
        return [
            'MTN' => 'MTN Mobile Money',
            'AIRTEL' => 'Airtel Money',
        ][$this->mobile_money_provider] ?? 'Bank Transfer';
    }
}
