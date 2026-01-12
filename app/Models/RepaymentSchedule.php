<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepaymentSchedule extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'principal_due',
        'interest_due',
        'total_due',
        'principal_paid',
        'interest_paid',
        'penalty_charged',
        'penalty_paid',
        'status',
        'paid_date',
        'outstanding_balance',
    ];

    protected $casts = [
        'principal_due' => 'decimal:2',
        'interest_due' => 'decimal:2',
        'total_due' => 'decimal:2',
        'principal_paid' => 'decimal:2',
        'interest_paid' => 'decimal:2',
        'penalty_charged' => 'decimal:2',
        'penalty_paid' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class);
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(LoanPenalty::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeDue($query)
    {
        return $query->where('due_date', '<=', now());
    }

    // Helper methods
    public function getStatusBadge(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'paid' => '<span class="badge bg-success">Paid</span>',
            'overdue' => '<span class="badge bg-danger">Overdue</span>',
            'partially_paid' => '<span class="badge bg-info">Partially Paid</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getAmountPaid(): float
    {
        return $this->principal_paid + $this->interest_paid + $this->penalty_paid;
    }

    public function getRemainingAmount(): float
    {
        return $this->total_due - $this->getAmountPaid();
    }

    public function isOverdue(): bool
    {
        return $this->due_date < now() && $this->status === 'pending';
    }

    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) return 0;
        return now()->diffInDays($this->due_date);
    }

    public function isFullyPaid(): bool
    {
        return $this->getAmountPaid() >= $this->total_due;
    }

    public function markAsPaid(): void
    {
        $this->status = 'paid';
        $this->paid_date = now();
        $this->save();
    }

    public function markAsOverdue(): void
    {
        if ($this->status === 'pending') {
            $this->status = 'overdue';
            $this->save();
        }
    }
}
