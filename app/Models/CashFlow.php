<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CashFlow extends Model
{
    protected $fillable = [
        'transaction_date',
        'description',
        'reference_number',
        'type',
        'category',
        'amount',
        'payment_method',
        'status',
        'notes',
        'user_id',
        'fiscal_year_id'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    // Scopes
    public function scopeForFiscalYear($query, $fiscalYearId)
    {
        return $query->where('fiscal_year_id', $fiscalYearId);
    }
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        $endDate = $endDate ?: $startDate;
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Helper methods
    public function getFormattedAmountAttribute()
    {
        return number_format((float)$this->amount, 2);
    }

    public function getTypeBadgeAttribute()
    {
        $type = strtolower($this->type);
        $class = $type === 'income' ? 'success' : 'danger';
        return '<span class="badge bg-' . $class . '">' . ucfirst($type) . '</span>';
    }
}
