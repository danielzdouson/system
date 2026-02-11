<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestmentTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'investment_portfolio_id',
        'transaction_type',
        'amount',
        'transaction_date',
        'description',
        'reference_number',
        'receipt_number',
        'payment_method',
        'running_balance',
        'accumulated_returns',
        'created_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'running_balance' => 'decimal:2',
        'accumulated_returns' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class, 'investment_portfolio_id');
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
        return $query->whereIn('transaction_type', [
            'INTEREST_INCOME',
            'DIVIDEND_INCOME',
            'CAPITAL_GAIN',
            'PRINCIPAL_RETURN'
        ]);
    }

    public function scopeOutflow($query)
    {
        return $query->whereIn('transaction_type', [
            'INITIAL_INVESTMENT',
            'ADDITIONAL_CONTRIBUTION',
            'INVESTMENT_EXPENSE',
            'WITHDRAWAL'
        ]);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        if (!$endDate) {
            $endDate = $startDate;
        }
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Helper methods
    public function getFormattedAmountAttribute(): string
    {
        return number_format((float)$this->amount, 2);
    }

    public function getFormattedRunningBalanceAttribute(): string
    {
        return number_format((float)$this->running_balance, 2);
    }

    public function getFormattedAccumulatedReturnsAttribute(): string
    {
        return number_format((float)$this->accumulated_returns, 2);
    }

    public function getTransactionTypeBadgeAttribute(): string
    {
        $typeColors = [
            'INITIAL_INVESTMENT' => 'primary',
            'ADDITIONAL_CONTRIBUTION' => 'info',
            'INTEREST_INCOME' => 'success',
            'DIVIDEND_INCOME' => 'success',
            'CAPITAL_GAIN' => 'success',
            'PRINCIPAL_RETURN' => 'warning',
            'INVESTMENT_EXPENSE' => 'danger',
            'WITHDRAWAL' => 'secondary',
            'REINVESTMENT' => 'info'
        ];

        $color = $typeColors[$this->transaction_type] ?? 'secondary';
        $label = str_replace('_', ' ', $this->transaction_type);
        
        return '<span class="badge bg-' . $color . '">' . ucwords(strtolower($label)) . '</span>';
    }

    public function isInflow(): bool
    {
        return in_array($this->transaction_type, [
            'INTEREST_INCOME',
            'DIVIDEND_INCOME', 
            'CAPITAL_GAIN',
            'PRINCIPAL_RETURN'
        ]);
    }

    public function isOutflow(): bool
    {
        return in_array($this->transaction_type, [
            'INITIAL_INVESTMENT',
            'ADDITIONAL_CONTRIBUTION',
            'INVESTMENT_EXPENSE',
            'WITHDRAWAL'
        ]);
    }

    public function isApproved(): bool
    {
        return !is_null($this->approved_at);
    }

    public function approve(User $approver): void
    {
        $this->approved_by = $approver->id;
        $this->approved_at = now();
        $this->save();
    }

    // Constants
    const TYPE_INITIAL_INVESTMENT = 'INITIAL_INVESTMENT';
    const TYPE_ADDITIONAL_CONTRIBUTION = 'ADDITIONAL_CONTRIBUTION';
    const TYPE_INTEREST_INCOME = 'INTEREST_INCOME';
    const TYPE_DIVIDEND_INCOME = 'DIVIDEND_INCOME';
    const TYPE_CAPITAL_GAIN = 'CAPITAL_GAIN';
    const TYPE_PRINCIPAL_RETURN = 'PRINCIPAL_RETURN';
    const TYPE_INVESTMENT_EXPENSE = 'INVESTMENT_EXPENSE';
    const TYPE_WITHDRAWAL = 'WITHDRAWAL';
    const TYPE_REINVESTMENT = 'REINVESTMENT';

    public static function getTransactionTypes(): array
    {
        return [
            self::TYPE_INITIAL_INVESTMENT => 'Initial Investment',
            self::TYPE_ADDITIONAL_CONTRIBUTION => 'Additional Contribution',
            self::TYPE_INTEREST_INCOME => 'Interest Income',
            self::TYPE_DIVIDEND_INCOME => 'Dividend Income',
            self::TYPE_CAPITAL_GAIN => 'Capital Gain',
            self::TYPE_PRINCIPAL_RETURN => 'Principal Return',
            self::TYPE_INVESTMENT_EXPENSE => 'Investment Expense',
            self::TYPE_WITHDRAWAL => 'Withdrawal',
            self::TYPE_REINVESTMENT => 'Reinvestment'
        ];
    }

    public static function getInflowTypes(): array
    {
        return [
            self::TYPE_INTEREST_INCOME,
            self::TYPE_DIVIDEND_INCOME,
            self::TYPE_CAPITAL_GAIN,
            self::TYPE_PRINCIPAL_RETURN
        ];
    }

    public static function getOutflowTypes(): array
    {
        return [
            self::TYPE_INITIAL_INVESTMENT,
            self::TYPE_ADDITIONAL_CONTRIBUTION,
            self::TYPE_INVESTMENT_EXPENSE,
            self::TYPE_WITHDRAWAL
        ];
    }
}
