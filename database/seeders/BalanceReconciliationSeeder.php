<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deposit;
use App\Models\MemberAccount;
use App\Models\Distribution;
use App\Models\FiscalYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BalanceReconciliationSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting balance reconciliation...');
        
        try {
            // Get all fiscal years
            $fiscalYears = FiscalYear::all();
            
            foreach ($fiscalYears as $fiscalYear) {
                $this->reconcileFiscalYear($fiscalYear);
            }
            
            $this->command->info('Balance reconciliation completed successfully!');
            
        } catch (\Exception $e) {
            $this->command->error('Reconciliation failed: ' . $e->getMessage());
            Log::error('Balance reconciliation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    private function reconcileFiscalYear($fiscalYear)
    {
        $this->command->info("Reconciling fiscal year: {$fiscalYear->name}");
        
        // Get all deposits for this fiscal year
        $deposits = Deposit::where('fiscal_year_id', $fiscalYear->id)->get();
        
        foreach ($deposits as $deposit) {
            $this->reconcileDeposit($deposit);
        }
        
        // Reconcile member accounts
        $memberAccounts = MemberAccount::where('fiscal_year_id', $fiscalYear->id)->get();
        
        foreach ($memberAccounts as $account) {
            $this->reconcileMemberAccount($account);
        }
    }
    
    private function reconcileDeposit($deposit)
    {
        // Calculate actual distributed amount
        $actualDistributed = $deposit->distributions()->sum('amount');
        
        // Calculate expected balance
        $expectedBalance = $deposit->amount - $actualDistributed;
        
        // Update deposit if balance is incorrect
        if ($deposit->balance != $expectedBalance) {
            $oldBalance = $deposit->balance;
            $deposit->balance = $expectedBalance;
            
            // Update status based on new balance
            if ($expectedBalance == 0) {
                $deposit->status = 'distributed';
            } elseif ($expectedBalance < $deposit->amount) {
                $deposit->status = 'partial';
            } else {
                $deposit->status = 'pending';
            }
            
            $deposit->save();
            
            $this->command->info("Updated deposit #{$deposit->id}: balance {$oldBalance} -> {$expectedBalance}, status {$deposit->status}");
        }
    }
    
    private function reconcileMemberAccount($account)
    {
        // Calculate actual totals from deposits
        $totalDeposited = Deposit::where('member_id', $account->member_id)
            ->where('fiscal_year_id', $account->fiscal_year_id)
            ->sum('amount');
        
        // Calculate actual distributed from distributions
        $totalDistributed = Distribution::whereHas('deposit', function($query) use ($account) {
            $query->where('member_id', $account->member_id)
                  ->where('fiscal_year_id', $account->fiscal_year_id);
        })->sum('amount');
        
        // Calculate expected current balance
        $expectedBalance = $totalDeposited - $totalDistributed;
        
        // Update if values are incorrect
        $needsUpdate = false;
        
        if ($account->total_deposited != $totalDeposited) {
            $account->total_deposited = $totalDeposited;
            $needsUpdate = true;
        }
        
        if ($account->total_distributed != $totalDistributed) {
            $account->total_distributed = $totalDistributed;
            $needsUpdate = true;
        }
        
        if ($account->current_balance != $expectedBalance) {
            $account->current_balance = $expectedBalance;
            $needsUpdate = true;
        }
        
        if ($needsUpdate) {
            $account->save();
            $this->command->info("Updated member account #{$account->member_id}: deposited {$totalDeposited}, distributed {$totalDistributed}, balance {$expectedBalance}");
        }
    }
}
