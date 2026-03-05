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
        'shares_on_hold',
        'total_shares',
    ];

    protected $casts = [
        'total_deposited' => 'decimal:2',
        'total_distributed' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'savings_balance' => 'decimal:2',
        'welfare_balance' => 'decimal:2',
        'fines_balance' => 'decimal:2',
        'other_balance' => 'decimal:2',
        'shares_on_hold' => 'decimal:2',
        'total_shares' => 'decimal:2',
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

    // Distribute funds from account (using account balance as single source of truth)
    public function distributeFunds($savingsAmount = 0, $welfareAmount = 0, $finesAmount = 0, $otherAmount = 0)
    {
        $totalDistribution = $savingsAmount + $welfareAmount + $finesAmount + $otherAmount;
        
        if ($totalDistribution > $this->current_balance) {
            throw new \Exception('Insufficient balance for distribution. Available: ' . $this->current_balance . ', Requested: ' . $totalDistribution);
        }

        // Removed validateAgainstDeposits call - account balance is the single source of truth

        $this->savings_balance += $savingsAmount;
        $this->welfare_balance += $welfareAmount;
        $this->fines_balance += $finesAmount;
        $this->other_balance += $otherAmount;
        $this->total_distributed += $totalDistribution;
        $this->current_balance -= $totalDistribution;
        $this->save();
    }

    // Log distribution vs deposit balance for reporting (no validation)
    public function validateAgainstDeposits($distributionAmount)
    {
        // Just log for reporting, don't throw exceptions
        $totalDepositBalance = Deposit::where('member_id', $this->member_id)
            ->where('fiscal_year_id', $this->fiscal_year_id)
            ->sum('balance');
        
        \Log::info('Distribution vs deposit balance', [
            'member_id' => $this->member_id,
            'fiscal_year_id' => $this->fiscal_year_id,
            'distribution_amount' => $distributionAmount,
            'total_deposit_balance' => $totalDepositBalance,
            'account_balance' => $this->current_balance
        ]);
    }

    public function recalculateBalances()
    {
        // Calculate actual totals from deposits
        $totalDeposited = Deposit::where('member_id', $this->member_id)
            ->where('fiscal_year_id', $this->fiscal_year_id)
            ->sum('amount');
        
        // Calculate actual distributed from distributions
        $totalDistributed = Distribution::whereHas('deposit', function($query) {
            $query->where('member_id', $this->member_id)
                  ->where('fiscal_year_id', $this->fiscal_year_id);
        })->sum('amount');
        
        // Calculate expected current balance
        $expectedBalance = $totalDeposited - $totalDistributed;
        
        // Update if values are incorrect
        $this->total_deposited = $totalDeposited;
        $this->total_distributed = $totalDistributed;
        $this->current_balance = $expectedBalance;
        
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
                'shares_on_hold' => 0,
                'total_shares' => 0,
            ]
        );
    }
}
