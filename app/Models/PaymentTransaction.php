<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'member_id',
        'amount',
        'currency',
        'payment_type',
        'payment_method',
        'mobile_network',
        'phone_number',
        'transaction_reference',
        'pesapal_tracking_id',
        'status',
        'related_id',
        'related_type',
        'admin_bank_details',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'admin_bank_details' => 'array',
        'completed_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return [
            'loan_payment' => 'Loan Payment',
            'fine_payment' => 'Fine Payment',
            'savings_deposit' => 'Savings Deposit',
            'education' => 'Education',
            'other' => 'Other',
        ][$this->payment_type] ?? $this->payment_type;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return [
            'mobile_money' => 'Mobile Money',
            'card' => 'Card',
            'bank_transfer' => 'Bank Transfer',
        ][$this->payment_method] ?? $this->payment_method;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'processing' => '<span class="badge bg-info">Processing</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'failed' => '<span class="badge bg-danger">Failed</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function scopeForMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentType($query, $type)
    {
        return $query->where('payment_type', $type);
    }
}
