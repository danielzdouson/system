<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'fiscal_year_id',
        'carried_forward_from_fiscal_year_id',
        'original_fiscal_year_id',
        'is_carried_forward',
        'carried_forward_at',
        'month',
        'amount',
        'reason',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_carried_forward' => 'boolean',
        'carried_forward_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function carriedForwardFromFiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'carried_forward_from_fiscal_year_id');
    }

    public function originalFiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'original_fiscal_year_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(FinePayment::class);
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'created_by' => auth()->id(),
        ]);
    }

    public function waive($reason = null)
    {
        $this->update([
            'status' => 'waived',
            'description' => $reason,
            'created_by' => auth()->id(),
        ]);
    }

    public static function createMissedSavingFine($memberId, $fiscalYearId, $month, $amount = 10000)
    {
        return self::create([
            'member_id' => $memberId,
            'fiscal_year_id' => $fiscalYearId,
            'month' => $month,
            'amount' => $amount,
            'reason' => 'missed_saving',
            'description' => 'Fine for missed saving in ' . self::getMonthName($month),
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);
    }

    public static function getMonthName($month)
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        return $months[$month] ?? 'Unknown';
    }

    // Carry-forward helper methods
    public function isCarriedForward(): bool
    {
        return $this->is_carried_forward;
    }

    public function getOriginalFiscalYearNameAttribute(): string
    {
        return $this->originalFiscalYear?->name ?? $this->fiscalYear?->name ?? 'Unknown';
    }

    public function getCarryForwardStatusBadgeAttribute(): string
    {
        if (!$this->is_carried_forward) {
            return '<span class="badge bg-secondary">Original</span>';
        }

        return '<span class="badge bg-warning">Carried Forward</span>';
    }

    public function getFullStatusBadgeAttribute(): string
    {
        $statusBadge = $this->getStatusBadge();
        $carryForwardBadge = $this->getCarryForwardStatusBadgeAttribute();
        
        return $statusBadge . ' ' . $carryForwardBadge;
    }

    public function getStatusBadge(): string
    {
        $colors = [
            'pending' => 'warning',
            'paid' => 'success',
            'waived' => 'info',
        ];

        $color = $colors[$this->status] ?? 'secondary';
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->status) . '</span>';
    }
}
