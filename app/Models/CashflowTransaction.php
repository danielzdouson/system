<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CashflowTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_date',
        'transaction_type',
        'category',
        'subcategory',
        'description',
        'amount',
        'reference_type',
        'reference_id',
        'reference_number',
        'payment_method',
        'status',
        'fiscal_year_id',
        'member_id',
        'created_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeInflow($query)
    {
        return $query->where('transaction_type', 'INFLOW');
    }

    public function scopeOutflow($query)
    {
        return $query->where('transaction_type', 'OUTFLOW');
    }

    public function scopeOperating($query)
    {
        return $query->where('category', 'OPERATING');
    }

    public function scopeInvesting($query)
    {
        return $query->where('category', 'INVESTING');
    }

    public function scopeFinancing($query)
    {
        return $query->where('category', 'FINANCING');
    }

    public function scopeCleared($query)
    {
        return $query->where('status', 'CLEARED');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeReconciled($query)
    {
        return $query->where('status', 'RECONCILED');
    }

    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        if (!$endDate) {
            $endDate = $startDate;
        }
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeFiscalYear($query, $fiscalYearId)
    {
        return $query->where('fiscal_year_id', $fiscalYearId);
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByReference($query, $referenceType, $referenceId)
    {
        return $query->where('reference_type', $referenceType)
                    ->where('reference_id', $referenceId);
    }

    // Helper methods
    public function getFormattedAmountAttribute(): string
    {
        return number_format((float)$this->amount, 2);
    }

    public function getTypeBadgeAttribute(): string
    {
        $type = strtolower($this->transaction_type);
        $class = $type === 'inflow' ? 'success' : 'danger';
        return '<span class="badge bg-' . $class . '">' . ucfirst($type) . '</span>';
    }

    public function getCategoryBadgeAttribute(): string
    {
        $category = strtolower($this->category);
        $colors = [
            'operating' => 'primary',
            'investing' => 'info',
            'financing' => 'warning'
        ];
        $class = $colors[$category] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst($category) . '</span>';
    }

    public function getStatusBadgeAttribute(): string
    {
        $status = strtolower($this->status);
        $colors = [
            'pending' => 'warning',
            'cleared' => 'success',
            'reconciled' => 'info'
        ];
        $class = $colors[$status] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst($status) . '</span>';
    }

    public function isApproved(): bool
    {
        return !is_null($this->approved_at);
    }

    public function approve(User $approver): void
    {
        $this->approved_by = $approver->id;
        $this->approved_at = now();
        $this->status = 'CLEARED';
        $this->save();
    }

    public function reconcile(): void
    {
        $this->status = 'RECONCILED';
        $this->save();
    }

    // Constants for reference types
    const REFERENCE_DEPOSIT = 'DEPOSIT';
    const REFERENCE_LOAN_DISBURSEMENT = 'LOAN_DISBURSEMENT';
    const REFERENCE_LOAN_REPAYMENT = 'LOAN_REPAYMENT';
    const REFERENCE_WELFARE_PAYMENT = 'WELFARE_PAYMENT';
    const REFERENCE_FINE_PAYMENT = 'FINE_PAYMENT';
    const REFERENCE_EXPENSE = 'EXPENSE';
    const REFERENCE_OTHER = 'OTHER';

    // Constants for categories
    const CATEGORY_OPERATING = 'OPERATING';
    const CATEGORY_INVESTING = 'INVESTING';
    const CATEGORY_FINANCING = 'FINANCING';

    // Constants for transaction types
    const TYPE_INFLOW = 'INFLOW';
    const TYPE_OUTFLOW = 'OUTFLOW';

    // Constants for status
    const STATUS_PENDING = 'PENDING';
    const STATUS_CLEARED = 'CLEARED';
    const STATUS_RECONCILED = 'RECONCILED';
}
