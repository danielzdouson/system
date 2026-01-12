<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberAccount extends Model
{
    protected $fillable = [
        'member_id',
        'fiscal_year_id',
        'total_deposited',
        'total_distributed',
        'current_balance',
        'savings_balance',
        'welfare_balance',
        'fines_balance',
        'other_balance',
    ];

    protected $casts = [
        'total_deposited' => 'decimal:2',
        'total_distributed' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'savings_balance' => 'decimal:2',
        'welfare_balance' => 'decimal:2',
        'fines_balance' => 'decimal:2',
        'other_balance' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    // Add deposit to account
    public function addDeposit($amount)
    {
        $this->total_deposited += $amount;
        $this->current_balance += $amount;
        $this->save();
    }

    // Distribute funds from account
    public function distributeFunds($savingsAmount = 0, $welfareAmount = 0, $finesAmount = 0, $otherAmount = 0)
    {
        $totalDistribution = $savingsAmount + $welfareAmount + $finesAmount + $otherAmount;
        
        if ($totalDistribution > $this->current_balance) {
            throw new \Exception('Insufficient balance for distribution');
        }

        $this->savings_balance += $savingsAmount;
        $this->welfare_balance += $welfareAmount;
        $this->fines_balance += $finesAmount;
        $this->other_balance += $otherAmount;
        $this->total_distributed += $totalDistribution;
        $this->current_balance -= $totalDistribution;
        $this->save();
    }

    // Get or create account for member in fiscal year
    public static function getOrCreate($memberId, $fiscalYearId)
    {
        return self::firstOrCreate(
            ['member_id' => $memberId, 'fiscal_year_id' => $fiscalYearId],
            [
                'total_deposited' => 0,
                'total_distributed' => 0,
                'current_balance' => 0,
                'savings_balance' => 0,
                'welfare_balance' => 0,
                'fines_balance' => 0,
                'other_balance' => 0,
            ]
        );
    }
}
