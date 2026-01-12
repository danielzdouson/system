<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SimpleLoanStatusSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Importing loan status data...');
        
        // Clear existing loans
        DB::table('loans')->delete();
        
        // Manually create loans based on the CSV data we saw
        $loanData = [
            [
                'member_name' => 'NATUKUNDA ANN',
                'loan_amount' => 2000000,
                'total_repayment' => 2200000,
                'balance' => 500000,
            ],
            [
                'member_name' => 'WESIGYE JONAH',
                'loan_amount' => 2537000,
                'total_repayment' => 2640000,
                'balance' => 1030000,
            ],
            [
                'member_name' => 'CHRISTOPHER MWESIGYE',
                'loan_amount' => 5390000,
                'total_repayment' => 5640000,
                'balance' => 250000,
            ],
            [
                'member_name' => 'KIMULI BASIL',
                'loan_amount' => 7700000,
                'total_repayment' => 8304000,
                'balance' => 604000,
            ],
            [
                'member_name' => 'SERUNJOJI JOHN K',
                'loan_amount' => 17100000,
                'total_repayment' => 17100000,
                'balance' => 0,
            ],
            [
                'member_name' => 'BABIRYE CLIFFORD',
                'loan_amount' => 15390000,
                'total_repayment' => 15390000,
                'balance' => 0,
            ],
            [
                'member_name' => 'MWESIGYE JONAH',
                'loan_amount' => 22900000,
                'total_repayment' => 22900000,
                'balance' => 0,
            ],
            [
                'member_name' => 'KIRIZI BOSCO',
                'loan_amount' => 26400000,
                'total_repayment' => 26400000,
                'balance' => 0,
            ],
            [
                'member_name' => 'NAMWANDA NAMUSISYA',
                'loan_amount' => 33000000,
                'total_repayment' => 33000000,
                'balance' => 0,
            ],
            [
                'member_name' => 'KUKIRIZA MAYANJA',
                'loan_amount' => 26300000,
                'total_repayment' => 26300000,
                'balance' => 0,
            ],
            [
                'member_name' => 'NABWINE DAVID',
                'loan_amount' => 16350000,
                'total_repayment' => 16350000,
                'balance' => 0,
            ],
            [
                'member_name' => 'MWANGI ZABULONI',
                'loan_amount' => 14820000,
                'total_repayment' => 14820000,
                'balance' => 0,
            ],
            [
                'member_name' => 'TAYEBWA DAVID',
                'loan_amount' => 6350000,
                'total_repayment' => 6350000,
                'balance' => 0,
            ],
            [
                'member_name' => 'MWESIGYE ISAAC',
                'loan_amount' => 2845600,
                'total_repayment' => 2845600,
                'balance' => 0,
            ],
            [
                'member_name' => 'KIMULI BASIL',
                'loan_amount' => 7000000,
                'total_repayment' => 8304000,
                'balance' => 1304000,
            ],
            [
                'member_name' => 'MAGALA ABDUL',
                'loan_amount' => 1000000,
                'total_repayment' => 1100000,
                'balance' => 100000,
            ],
            [
                'member_name' => 'SSENYONJO DAVID',
                'loan_amount' => 500000,
                'total_repayment' => 500000,
                'balance' => 0,
            ],
            [
                'member_name' => 'MWANGI ZABULONI',
                'loan_amount' => 5000000,
                'total_repayment' => 5000000,
                'balance' => 0,
            ],
        ];
        
        $members = Member::all()->keyBy('name');
        $loanId = 1;
        
        foreach ($loanData as $data) {
            $memberName = $data['member_name'];
            $member = $members->get($memberName);
            
            if (!$member) {
                // Create member if not exists
                $nameParts = explode(' ', $memberName);
                $member = Member::create([
                    'first_name' => $nameParts[0] ?? 'Unknown',
                    'last_name' => $nameParts[1] ?? 'Member',
                    'national_id' => 'LOAN-' . str_pad($loanId, 4, '0', STR_PAD_LEFT) . '-' . $loanId,
                    'email' => 'loan' . $loanId . '-' . $loanId . '@saco.com',
                    'phone' => '0000000000',
                    'date_of_birth' => '1990-01-01',
                    'address' => 'Unknown',
                    'city' => 'Unknown',
                    'country' => 'Uganda',
                ]);
            }
            
            // Calculate loan details
            $loanAmount = $data['loan_amount'];
            $interestRate = 15; // Default 15% interest rate
            $loanTerm = 12; // Default 12 months
            $monthlyPayment = $loanAmount / $loanTerm;
            $totalInterest = $loanAmount * ($interestRate / 100);
            $totalRepayment = $data['total_repayment'];
            $balance = $data['balance'];
            
            // Determine loan status based on balance
            $loanStatus = Loan::STATUS_ACTIVE;
            if ($balance <= 0) {
                $loanStatus = Loan::STATUS_COMPLETED;
            } elseif ($balance < $loanAmount * 0.1) {
                $loanStatus = Loan::STATUS_ARREARS;
            }
            
            Loan::create([
                'member_id' => $member->id,
                'loan_amount' => $loanAmount,
                'interest_rate' => $interestRate,
                'loan_term' => $loanTerm,
                'loan_purpose' => 'Personal Loan',
                'loan_status' => $loanStatus,
                'disbursement_date' => Carbon::now()->subMonths(rand(3, 12)),
                'first_payment_date' => Carbon::now()->subMonths(rand(2, 11)),
                'maturity_date' => Carbon::now()->addMonths($loanTerm),
                'monthly_payment' => $monthlyPayment,
                'total_interest' => $totalInterest,
                'total_repayment' => $totalRepayment,
                'balance' => max(0, $balance),
                'arrears' => $balance < 0 ? abs($balance) : 0,
                'payment_method' => 'bank_transfer',
                'notes' => 'Imported from loan status CSV',
                'created_by' => null,
                'approved_by' => null,
                'approved_at' => Carbon::now()->subMonths(rand(1, 6)),
                'created_at' => Carbon::now()->subMonths(rand(1, 6)),
                'updated_at' => Carbon::now()->subMonths(rand(1, 6)),
            ]);
            
            $loanId++;
        }
        
        $this->command->info('Loan status data imported successfully!');
        $this->command->info('Created ' . count($loanData) . ' loan records.');
    }
}
