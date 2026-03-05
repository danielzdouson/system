<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'fine_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'payment_date',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function fine()
    {
        return $this->belongsTo(Fine::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getPaymentMethodLabelAttribute()
    {
        return [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'mobile_money' => 'Mobile Money',
            'check' => 'Check',
            'other' => 'Other'
        ][$this->payment_method] ?? $this->payment_method;
    }

    public function scopeForFiscalYear($query, $fiscalYearId)
    {
        return $query->whereHas('fine', function($q) use ($fiscalYearId) {
            $q->where('fiscal_year_id', $fiscalYearId);
        });
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }
}
