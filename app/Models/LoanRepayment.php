<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $table = 'repayments';

    protected $fillable = [
        'loan_id',
        'member_id',
        'amount',
        'principal_component',
        'interest_component',
        'method',
        'reference',
        'paid_at',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'principal_component' => 'decimal:2',
        'interest_component' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function repaymentSchedule(): BelongsTo
    {
        return $this->belongsTo(RepaymentSchedule::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Helper methods
    public function getTotalAmount(): float
    {
        return $this->principal_component + $this->interest_component;
    }

    public function isFullPayment(): bool
    {
        return $this->repaymentSchedule && 
               $this->getTotalAmount() >= $this->repaymentSchedule->total_due;
    }

    public function getPaymentMethodBadge(): string
    {
        return match($this->method) {
            'cash' => '<span class="badge bg-success">Cash</span>',
            'bank_transfer' => '<span class="badge bg-info">Bank Transfer</span>',
            'mobile_money' => '<span class="badge bg-primary">Mobile Money</span>',
            'cheque' => '<span class="badge bg-warning">Cheque</span>',
            'other' => '<span class="badge bg-secondary">Other</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
