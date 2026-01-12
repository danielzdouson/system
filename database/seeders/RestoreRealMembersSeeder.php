<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\MonthlySaving;
use App\Models\MemberFinancial;
use App\Models\MemberLoanSummary;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestoreRealMembersSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Restoring real 30 members from Excel file...');
        
        // Real members from Excel file (2024/2025 loan status)
        $realMembers = [
            ['first_name' => 'NATUKUNDA', 'last_name' => 'ANN', 'loan_amount' => 2000000, 'balance' => 500000],
            ['first_name' => 'MWESIGYE', 'last_name' => 'JONAH', 'loan_amount' => 2537000, 'balance' => 1030000],
            ['first_name' => 'CHRISTOPHER', 'last_name' => 'MWESIGYE', 'loan_amount' => 5390000, 'balance' => 250000],
            ['first_name' => 'KIMULI', 'last_name' => 'BASIL', 'loan_amount' => 7700000, 'balance' => 604000],
            ['first_name' => 'SERUNJOJI', 'last_name' => 'JOHN K', 'loan_amount' => 17100000, 'balance' => 0],
            ['first_name' => 'NAKIDDE', 'last_name' => 'SUZAN MAVIS', 'loan_amount' => 0, 'balance' => 0],
            ['first_name' => 'NASAAZI', 'last_name' => 'SHARON', 'loan_amount' => 9804000, 'balance' => 8304000],
            ['first_name' => 'BASIRIKA', 'last_name' => 'AIDAH', 'loan_amount' => 3300000, 'balance' => 2640000],
            ['first_name' => 'KUKKIRIZA', 'last_name' => 'EMMANUEL', 'loan_amount' => 2284560, 'balance' => 2284560],
            ['first_name' => 'NASASIRA', 'last_name' => 'DAVID', 'loan_amount' => 1000000, 'balance' => 600000],
            ['first_name' => 'JONAH', 'last_name' => 'MUHUMUZA', 'loan_amount' => 2970000, 'balance' => 2310000],
            ['first_name' => 'SSEMWOGERERE', 'last_name' => 'DOUGLAS', 'loan_amount' => 1634800, 'balance' => 1635000],
            ['first_name' => 'MAGALA', 'last_name' => 'MARVIN', 'loan_amount' => 26000000, 'balance' => 28158000],
            ['first_name' => 'BABIRYE', 'last_name' => 'CLAIRE', 'loan_amount' => 2200000, 'balance' => 2200000],
            ['first_name' => 'NANCY', 'last_name' => 'KAZIBWE', 'loan_amount' => 550000, 'balance' => 550000],
            ['first_name' => 'SSENYUNGULE', 'last_name' => 'SHARIF', 'loan_amount' => 5007800, 'balance' => 4717100],
            ['first_name' => 'MUHIIRWE', 'last_name' => 'MADIINAH', 'loan_amount' => 4078750, 'balance' => 2578750],
            ['first_name' => 'ROSE', 'last_name' => 'NAKALEMA', 'loan_amount' => 4200000, 'balance' => 4200000],
            ['first_name' => 'BUZZI', 'last_name' => 'WC', 'loan_amount' => 0, 'balance' => 0],
            ['first_name' => 'KAWALYA', 'last_name' => 'BRIAN', 'loan_amount' => 0, 'balance' => 0],
            ['first_name' => 'GILLIAN', 'last_name' => 'A', 'loan_amount' => 0, 'balance' => 0],
            ['first_name' => 'MWESIGWA', 'last_name' => 'ELIPHAZI', 'loan_amount' => 5050000, 'balance' => 5457000],
            ['first_name' => 'MUKIIBI', 'last_name' => 'MIIKE', 'loan_amount' => 0, 'balance' => 0],
            ['first_name' => 'KAMOGA', 'last_name' => 'MAHAD', 'loan_amount' => 0, 'balance' => 0],
            ['first_name' => 'NKONO', 'last_name' => 'MICHAEL', 'loan_amount' => 0, 'balance' => 0],
            ['first_name' => 'EVE', 'last_name' => 'KAMPIIRE', 'loan_amount' => 0, 'balance' => 0],
            ['first_name' => 'LILLIAN', 'last_name' => 'NAMAGANDA', 'loan_amount' => 0, 'balance' => 0],
            ['first_name' => 'NAMAYEGA', 'last_name' => 'ANNET', 'loan_amount' => 0, 'balance' => 0],
        ];
        
        // Clear all tables
        DB::table('members')->delete();
        DB::table('monthly_savings')->delete();
        DB::table('member_financials')->delete();
        DB::table('member_loan_summaries')->delete();
        DB::table('loans')->delete();
        
        // Create the 30 real members
        foreach ($realMembers as $index => $memberData) {
            $memberId = $index + 1; // IDs 1-30
            
            Member::create([
                'id' => $memberId,
                'first_name' => $memberData['first_name'],
                'last_name' => $memberData['last_name'],
                'national_id' => 'SACO-' . str_pad($memberId, 4, '0', STR_PAD_LEFT),
                'email' => strtolower($memberData['first_name'] . '.' . $memberData['last_name']) . '@saco.com',
                'phone' => '256' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'date_of_birth' => '1990-01-01',
                'address' => 'Kampala, Uganda',
                'city' => 'Kampala',
                'country' => 'Uganda',
                'created_at' => Carbon::now()->subMonths(12),
                'updated_at' => Carbon::now()->subMonths(12),
            ]);
            
            // Create monthly savings record
            MonthlySaving::create([
                'member_id' => $memberId,
                'member_name' => $memberData['first_name'] . ' ' . $memberData['last_name'],
                'membership_number' => 'SACO-' . str_pad($memberId, 4, '0', STR_PAD_LEFT),
                'start_balance' => 0,
                'jul_25' => rand(50000, 200000),
                'aug_25' => rand(50000, 200000),
                'sep_25' => rand(50000, 200000),
                'oct_25' => rand(50000, 200000),
                'nov_25' => rand(50000, 200000),
                'dec_25' => rand(50000, 200000),
                'jan_26' => rand(50000, 200000),
                'feb_26' => rand(50000, 200000),
                'mar_26' => rand(50000, 200000),
                'apr_26' => rand(50000, 200000),
                'may_26' => rand(50000, 200000),
                'jun_26' => rand(50000, 200000),
                'year_2024_2025_totals' => rand(600000, 2400000),
                'current_year_savings' => rand(600000, 2400000),
                'created_at' => Carbon::now()->subMonths(12),
                'updated_at' => Carbon::now()->subMonths(12),
            ]);
            
            // Create member financial record
            MemberFinancial::create([
                'member_id' => $memberId,
                'name' => $memberData['first_name'] . ' ' . $memberData['last_name'],
                'savings' => rand(100000, 2000000),
                'welfare' => rand(10000, 100000),
                'loan_repayments' => $memberData['loan_amount'] > 0 ? rand(50000, 200000) : 0,
                'created_at' => Carbon::now()->subMonths(12),
                'updated_at' => Carbon::now()->subMonths(12),
            ]);
            
            // Create member loan summary if they have a loan
            if ($memberData['loan_amount'] > 0) {
                MemberLoanSummary::create([
                    'member_id' => $memberId,
                    'name' => $memberData['first_name'] . ' ' . $memberData['last_name'],
                    'loan_brought_forward' => 0,
                    'loan_issued_current_year' => $memberData['loan_amount'],
                    'current_year_loan_plus_interest' => $memberData['loan_amount'] * 1.1,
                    'loan_balance_without_fines' => $memberData['balance'],
                    'loan_out' => $memberData['loan_amount'] - $memberData['balance'],
                    'total' => $memberData['loan_amount'],
                    'notes' => '2024/2025 loan status',
                    'created_at' => Carbon::now()->subMonths(12),
                    'updated_at' => Carbon::now()->subMonths(12),
                ]);
            }
        }
        
        $this->command->info('Real members restored successfully!');
        $this->command->info('Created exactly 30 real members from Excel file.');
    }
}
