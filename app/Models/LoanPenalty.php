<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanPenalty extends Model
{
    protected $fillable = [
        'loan_id',
        'repayment_schedule_id',
        'member_id',
        'fiscal_year_id',
        'penalty_amount',
        'penalty_type',
        'penalty_date',
        'due_date_of_missed_payment',
        'days_overdue',
        'penalty_rate',
        'status',
        'paid_date',
        'waived_by',
        'waiver_reason',
        'notes',
    ];

    protected $casts = [
        'penalty_amount' => 'decimal:2',
        'penalty_rate' => 'decimal:2',
        'penalty_date' => 'date',
        'due_date_of_missed_payment' => 'date',
        'paid_date' => 'date',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function repaymentSchedule(): BelongsTo
    {
        return $this->belongsTo(RepaymentSchedule::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function waivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waived_by');
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

    public function scopeWaived($query)
    {
        return $query->where('status', 'waived');
    }

    // Helper methods
    public function getStatusBadge(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'paid' => '<span class="badge bg-success">Paid</span>',
            'waived' => '<span class="badge bg-info">Waived</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getPenaltyTypeBadge(): string
    {
        return match($this->penalty_type) {
            'late_payment' => '<span class="badge bg-warning">Late Payment</span>',
            'missed_payment' => '<span class="badge bg-danger">Missed Payment</span>',
            'default_interest' => '<span class="badge bg-secondary">Default Interest</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isWaived(): bool
    {
        return $this->status === 'waived';
    }

    public function markAsPaid(): void
    {
        $this->status = 'paid';
        $this->paid_date = now();
        $this->save();
    }

    public function waive($userId, $reason = null): void
    {
        $this->status = 'waived';
        $this->waived_by = $userId;
        $this->waiver_reason = $reason;
        $this->save();
    }

    public function calculatePenaltyAmount(float $dueAmount, int $daysOverdue, float $rate): float
    {
        // Calculate penalty as percentage of due amount per day overdue
        return ($dueAmount * ($rate / 100) * $daysOverdue);
    }
}
