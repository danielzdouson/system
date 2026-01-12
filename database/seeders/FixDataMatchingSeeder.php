<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\MemberFinancial;
use App\Models\MemberLoanSummary;
use App\Models\MonthlySaving;
use App\Models\CashFlow;
use Illuminate\Support\Facades\DB;

class FixDataMatchingSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting data matching fix...');
        
        // 1. Sync missing MemberFinancial records
        $this->syncMemberFinancials();
        
        // 2. Sync missing MemberLoanSummary records
        $this->syncMemberLoanSummaries();
        
        // 3. Reconcile savings data
        $this->reconcileSavings();
        
        // 4. Add missing loan disbursements to CashFlow
        $this->addMissingLoanDisbursements();
        
        $this->command->info('Data matching fix completed!');
    }
    
    private function syncMemberFinancials()
    {
        $this->command->info('Syncing MemberFinancial records...');
        
        // Get members without financial records using direct query
        $membersWithFinancials = MemberFinancial::pluck('member_id')->toArray();
        $membersWithoutFinancials = Member::whereNotIn('id', $membersWithFinancials)->get();
        
        foreach ($membersWithoutFinancials as $member) {
            MemberFinancial::create([
                'member_id' => $member->id,
                'name' => $member->first_name . ' ' . $member->last_name,
                'savings' => 0,
                'welfare' => 0,
                'loan_repayments' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info("Created {$membersWithoutFinancials->count()} missing MemberFinancial records");
    }
    
    private function syncMemberLoanSummaries()
    {
        $this->command->info('Syncing MemberLoanSummary records...');
        
        // Get members without loan summaries using direct query
        $membersWithLoans = MemberLoanSummary::pluck('member_id')->toArray();
        $membersWithoutLoans = Member::whereNotIn('id', $membersWithLoans)->get();
        
        foreach ($membersWithoutLoans as $member) {
            MemberLoanSummary::create([
                'member_id' => $member->id,
                'name' => $member->first_name . ' ' . $member->last_name,
                'loan_brought_forward' => 0,
                'loan_issued_current_year' => 0,
                'current_year_loan_plus_interest' => 0,
                'loan_balance_without_fines' => 0,
                'loan_out' => 0,
                'total' => 0,
                'notes' => 'Auto-generated for data consistency',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info("Created {$membersWithoutLoans->count()} missing MemberLoanSummary records");
    }
    
    private function reconcileSavings()
    {
        $this->command->info('Reconciling savings data...');
        
        // Calculate average monthly savings and update MemberFinancials
        $monthlySavingsByMember = MonthlySaving::selectRaw('
            member_id,
            SUM(jul_25 + aug_25 + sep_25 + oct_25 + nov_25 + dec_25 + jan_26 + feb_26 + mar_26 + apr_26 + may_26 + jun_26) as total_monthly_savings
        ')->groupBy('member_id')->get();
        
        foreach ($monthlySavingsByMember as $monthlySaving) {
            MemberFinancial::where('member_id', $monthlySaving->member_id)
                ->update(['savings' => $monthlySaving->total_monthly_savings]);
        }
        
        $this->command->info('Reconciled savings data between MonthlySavings and MemberFinancials');
    }
    
    private function addMissingLoanDisbursements()
    {
        $this->command->info('Adding missing loan disbursements to CashFlow...');
        
        // Get total loans from MemberLoanSummary
        $totalLoans = MemberLoanSummary::sum('total');
        $currentLoanExpenses = CashFlow::where('description', 'like', '%loan%')->where('type', 'expense')->sum('amount');
        
        $missingLoanAmount = $totalLoans - $currentLoanExpenses;
        
        if ($missingLoanAmount > 0) {
            // Add a summary loan disbursement record
            CashFlow::create([
                'transaction_date' => now(),
                'description' => 'Loan Disbursements Summary',
                'type' => 'expense',
                'category' => 'loans_disbursement',
                'amount' => $missingLoanAmount,
                'payment_method' => 'bank_transfer',
                'status' => 'cleared',
                'user_id' => 1, // Assuming admin user
                'reference_number' => 'LOAN-SUMMARY-' . date('Y-m-d'),
                'notes' => 'Auto-generated to reconcile loan disbursements',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info("Added missing loan disbursement: UGX " . number_format($missingLoanAmount, 2));
        }
    }
}
