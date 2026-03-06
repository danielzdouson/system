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
        'month',
        'amount',
        'reason',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
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
}
