<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'payment_amount',
        'payment_date',
        'payment_method',
        'interest_portion',
        'principal_portion',
        'balance_after_payment',
        'receipt_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'principal_portion' => 'decimal:2',
        'interest_portion' => 'decimal:2',
        'balance_after_payment' => 'decimal:2',
        'payment_date' => 'date',
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
        return $this->principal_portion + $this->interest_portion;
    }

    public function isFullPayment(): bool
    {
        return $this->repaymentSchedule && 
               $this->getTotalAmount() >= $this->repaymentSchedule->total_due;
    }

    public function getPaymentMethodBadge(): string
    {
        return match($this->payment_method) {
            'cash' => '<span class="badge bg-success">Cash</span>',
            'bank_transfer' => '<span class="badge bg-info">Bank Transfer</span>',
            'mobile_money' => '<span class="badge bg-primary">Mobile Money</span>',
            'cheque' => '<span class="badge bg-warning">Cheque</span>',
            'other' => '<span class="badge bg-secondary">Other</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
