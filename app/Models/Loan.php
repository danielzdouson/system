<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_request_id',
        'member_id',
        'fiscal_year_id',
        'loan_number',
        'loan_amount',
        'principal_amount',
        'interest_rate',
        'interest_type',
        'loan_term',
        'duration_months',
        'loan_purpose',
        'monthly_installment',
        'monthly_payment',
        'total_interest',
        'total_repayable',
        'total_repayment',
        'balance',
        'paid_amount',
        'loan_status',
        'status',
        'disbursement_date',
        'first_payment_date',
        'maturity_date',
        'completed_at',
        'disbursed_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'total_interest' => 'decimal:2',
        'total_repayable' => 'decimal:2',
        'total_repayment' => 'decimal:2',
        'balance' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'disbursement_date' => 'date',
        'first_payment_date' => 'date',
        'maturity_date' => 'date',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Loan statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DEFAULTED = 'defaulted';
    const STATUS_SUSPENDED = 'suspended';

    public function loanRequest(): BelongsTo
    {
        return $this->belongsTo(LoanRequest::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function disbursedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class);
    }

    public function repaymentSchedules(): HasMany
    {
        return $this->hasMany(RepaymentSchedule::class);
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(LoanPenalty::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeDefaulted($query)
    {
        return $query->where('status', self::STATUS_DEFAULTED);
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    // Helper methods
    public function getStatusBadge(): string
    {
        $status = $this->loan_status ?? $this->status;
        return match($status) {
            'active', 'disbursed' => '<span class="badge bg-success">Active</span>',
            'completed' => '<span class="badge bg-primary">Completed</span>',
            'defaulted' => '<span class="badge bg-danger">Defaulted</span>',
            'suspended' => '<span class="badge bg-warning">Suspended</span>',
            'pending' => '<span class="badge bg-info">Pending</span>',
            'approved' => '<span class="badge bg-primary">Approved</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getProgressPercentage(): float
    {
        $totalRepayable = $this->total_repayable ?? $this->total_repayment;
        if ($totalRepayable <= 0) return 0;
        return min(100, (($this->paid_amount / $totalRepayable) * 100));
    }

    public function isOverdue(): bool
    {
        return $this->repaymentSchedules()
            ->where('due_date', '<', now())
            ->where('status', 'pending')
            ->exists();
    }

    public function getOverdueAmount(): float
    {
        return $this->repaymentSchedules()
            ->where('due_date', '<', now())
            ->where('status', 'pending')
            ->sum('total_due');
    }

    public function getNextPaymentDue(): ?RepaymentSchedule
    {
        return $this->repaymentSchedules()
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->first();
    }

    public function generateLoanNumber(): string
    {
        $prefix = 'LN';
        $year = date('Y');
        $sequence = Loan::whereYear('created_at', $year)->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    public function calculateMonthlyInstallment(): float
    {
        if ($this->interest_type === 'flat') {
            // Flat rate calculation
            $totalInterest = $this->principal_amount * ($this->interest_rate / 100) * ($this->duration_months / 12);
            return ($this->principal_amount + $totalInterest) / $this->duration_months;
        } else {
            // Reducing balance calculation (simplified)
            $monthlyRate = $this->interest_rate / 100 / 12;
            $numerator = $this->principal_amount * $monthlyRate * pow(1 + $monthlyRate, $this->duration_months);
            $denominator = pow(1 + $monthlyRate, $this->duration_months) - 1;
            return $denominator != 0 ? $numerator / $denominator : $this->principal_amount / $this->duration_months;
        }
    }

    public function createRepaymentSchedule(): void
    {
        $schedules = [];
        $remainingBalance = $this->principal_amount;
        $paymentDate = Carbon::parse($this->first_payment_date);

        for ($i = 1; $i <= $this->duration_months; $i++) {
            $interestComponent = $remainingBalance * ($this->interest_rate / 100 / 12);
            $principalComponent = $this->monthly_installment - $interestComponent;
            
            $schedules[] = [
                'loan_id' => $this->id,
                'installment_number' => $i,
                'due_date' => $paymentDate->copy(),
                'principal_due' => $principalComponent,
                'interest_due' => $interestComponent,
                'total_due' => $this->monthly_installment,
                'outstanding_balance' => $remainingBalance - $principalComponent,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $remainingBalance -= $principalComponent;
            $paymentDate->addMonth();
        }

        RepaymentSchedule::insert($schedules);
    }

    public function canBeCompleted(): bool
    {
        return $this->balance <= 0 && $this->status === self::STATUS_ACTIVE;
    }

    public function markAsCompleted(): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->balance = 0.0;
        $this->save();
    }
}
