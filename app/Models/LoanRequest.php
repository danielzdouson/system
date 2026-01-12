<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LoanRequest extends Model
{
    protected $fillable = [
        'member_id',
        'fiscal_year_id',
        'requested_amount',
        'loan_type',
        'duration_months',
        'purpose',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'admin_notes',
        'member_savings_at_request',
        'existing_loan_balance',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'member_savings_at_request' => 'decimal:2',
        'existing_loan_balance' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function loan(): HasOne
    {
        return $this->hasOne(Loan::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helper methods
    public function isEligible(): bool
    {
        // Check if member has no overdue loans
        $hasOverdueLoans = Loan::where('member_id', $this->member_id)
            ->where('status', 'active')
            ->where('balance', '>', 0)
            ->exists();

        if ($hasOverdueLoans) {
            return false;
        }

        // Check if requested amount is within allowed multiple of savings
        $maxLoanAmount = $this->member_savings_at_request * 3; // 3x savings rule
        return $this->requested_amount <= $maxLoanAmount;
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            'withdrawn' => '<span class="badge bg-secondary">Withdrawn</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
