<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Investment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'investment_type',
        'institution',
        'principal_amount',
        'interest_rate',
        'investment_date',
        'maturity_date',
        'status',
        'current_value',
        'total_returns',
        'reference_number',
        'account_number',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:4',
        'current_value' => 'decimal:2',
        'total_returns' => 'decimal:2',
        'investment_date' => 'date',
        'maturity_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function transactions(): HasMany
    {
        return $this->hasMany(InvestmentTransaction::class);
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
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeMatured($query)
    {
        return $query->where('status', 'MATURED');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('investment_type', $type);
    }

    public function scopeByInstitution($query, $institution)
    {
        return $query->where('institution', 'like', "%{$institution}%");
    }

    public function scopeMaturityWithin($query, $days)
    {
        return $query->where('maturity_date', '<=', now()->addDays($days))
                    ->where('status', 'ACTIVE');
    }

    // Helper methods
    public function getDaysToMaturityAttribute(): int
    {
        if (!$this->maturity_date || $this->status !== 'ACTIVE') {
            return 0;
        }
        return now()->diffInDays($this->maturity_date, false);
    }

    public function getIsMaturedAttribute(): bool
    {
        return $this->maturity_date && $this->maturity_date->isPast() && $this->status === 'ACTIVE';
    }

    public function getExpectedReturnsAttribute(): float
    {
        if (!$this->interest_rate || !$this->maturity_date) {
            return 0;
        }

        $days = $this->investment_date->diffInDays($this->maturity_date);
        $years = $days / 365;
        
        if ($this->investment_type === 'FIXED_DEPOSIT' || $this->investment_type === 'TREASURY_BILL') {
            // Simple interest calculation
            return $this->principal_amount * ($this->interest_rate / 100) * $years;
        }
        
        return 0; // Complex calculations for other types can be added later
    }

    public function getCurrentValueAttribute(): float
    {
        // If current_value is set, use it, otherwise calculate
        if ($this->attributes['current_value']) {
            return (float) $this->attributes['current_value'];
        }
        
        return $this->principal_amount + $this->total_returns;
    }

    public function getRoiAttribute(): float
    {
        if ($this->principal_amount == 0) {
            return 0;
        }
        
        return (($this->current_value - $this->principal_amount) / $this->principal_amount) * 100;
    }

    public function getAnnualizedRoiAttribute(): float
    {
        if (!$this->investment_date || $this->principal_amount == 0) {
            return 0;
        }

        $days = $this->investment_date->diffInDays(now());
        if ($days == 0) {
            return 0;
        }

        $years = $days / 365;
        $totalRoi = $this->roi;
        
        return $totalRoi / $years;
    }

    public function getFormattedPrincipalAmountAttribute(): string
    {
        return number_format($this->principal_amount, 2);
    }

    public function getFormattedCurrentValueAttribute(): string
    {
        return number_format($this->current_value, 2);
    }

    public function getFormattedTotalReturnsAttribute(): string
    {
        return number_format($this->total_returns, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        $status = strtolower($this->status);
        $colors = [
            'active' => 'success',
            'matured' => 'info',
            'closed' => 'secondary',
            'defaulted' => 'danger'
        ];
        $class = $colors[$status] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst($status) . '</span>';
    }

    public function getTypeBadgeAttribute(): string
    {
        $type = strtolower(str_replace('_', ' ', $this->investment_type));
        return '<span class="badge bg-primary">' . ucwords($type) . '</span>';
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

    public function markAsMatured(): void
    {
        $this->status = 'MATURED';
        $this->save();
    }

    public function close(): void
    {
        $this->status = 'CLOSED';
        $this->save();
    }

    // Constants
    const TYPE_FIXED_DEPOSIT = 'FIXED_DEPOSIT';
    const TYPE_TREASURY_BILL = 'TREASURY_BILL';
    const TYPE_CORPORATE_BOND = 'CORPORATE_BOND';
    const TYPE_EQUITY = 'EQUITY';
    const TYPE_MUTUAL_FUND = 'MUTUAL_FUND';
    const TYPE_REAL_ESTATE = 'REAL_ESTATE';
    const TYPE_OTHER = 'OTHER';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_MATURED = 'MATURED';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_DEFAULTED = 'DEFAULTED';

    public static function getInvestmentTypes(): array
    {
        return [
            self::TYPE_FIXED_DEPOSIT => 'Fixed Deposit',
            self::TYPE_TREASURY_BILL => 'Treasury Bill',
            self::TYPE_CORPORATE_BOND => 'Corporate Bond',
            self::TYPE_EQUITY => 'Equity/Shares',
            self::TYPE_MUTUAL_FUND => 'Mutual Fund',
            self::TYPE_REAL_ESTATE => 'Real Estate',
            self::TYPE_OTHER => 'Other'
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_MATURED => 'Matured',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_DEFAULTED => 'Defaulted'
        ];
    }
}
