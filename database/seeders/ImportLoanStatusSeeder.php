<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportLoanStatusSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Importing loan status data...');
        
        // Clear existing loans
        DB::table('loans')->delete();
        
        // Read the CSV file
        $csvPath = base_path('data/loan status.csv');
        $csvData = $this->parseCsv($csvPath);
        
        $members = Member::all()->keyBy('name');
        $loanId = 1;
        
        foreach ($csvData as $row) {
            if (empty($row[0]) || $row[0] === 'NAMES') {
                continue;
            }
            
            $memberName = trim($row[0]);
            $loanBF = $this->parseAmount($row[1]);
            $loanIssued = $this->parseAmount($row[2]);
            $loanWithInterest = $this->parseAmount($row[3]);
            $loanBalance = $this->parseAmount($row[4]);
            $loanOut = $this->parseAmount($row[5]);
            $totalLoan = $this->parseAmount($row[6]);
            
            // Find or create member
            $member = $members->get($memberName);
            if (!$member) {
                // Try to match by partial name or create a placeholder member
                $member = Member::where('first_name', 'LIKE', '%' . explode(' ', $memberName)[0] . '%')
                    ->orWhere('last_name', 'LIKE', '%' . explode(' ', $memberName)[1] ?? '' . '%')
                    ->first();
                
                if (!$member) {
                    $member = Member::create([
                        'first_name' => explode(' ', $memberName)[0] ?? 'Unknown',
                        'last_name' => explode(' ', $memberName)[1] ?? 'Member',
                        'national_id' => 'LOAN-' . str_pad($loanId, 4, '0', STR_PAD_LEFT),
                        'email' => 'loan' . $loanId . '@saco.com',
                        'phone' => '0000000000',
                        'date_of_birth' => '1990-01-01',
                        'address' => 'Unknown',
                        'city' => 'Unknown',
                        'country' => 'Uganda',
                    ]);
                }
            }
            
            if ($totalLoan > 0) {
                // Calculate loan details
                $interestRate = 15; // Default 15% interest rate
                $loanTerm = 12; // Default 12 months
                $monthlyPayment = $totalLoan / $loanTerm;
                $totalInterest = $totalLoan * ($interestRate / 100);
                $totalRepayment = $totalLoan + $totalInterest;
                
                // Determine loan status based on balance
                $loanStatus = Loan::STATUS_ACTIVE;
                if ($loanBalance <= 0) {
                    $loanStatus = Loan::STATUS_COMPLETED;
                } elseif ($loanBalance < $totalLoan * 0.1) {
                    $loanStatus = Loan::STATUS_ARREARS;
                }
                
                Loan::create([
                    'member_id' => $member->id,
                    'loan_amount' => $totalLoan,
                    'interest_rate' => $interestRate,
                    'loan_term' => $loanTerm,
                    'loan_purpose' => 'Personal Loan',
                    'loan_status' => $loanStatus,
                    'disbursement_date' => Carbon::now()->subMonths(6),
                    'first_payment_date' => Carbon::now()->subMonths(5),
                    'maturity_date' => Carbon::now()->addMonths(6),
                    'monthly_payment' => $monthlyPayment,
                    'total_interest' => $totalInterest,
                    'total_repayment' => $totalRepayment,
                    'balance' => max(0, $loanBalance),
                    'arrears' => $loanBalance < 0 ? abs($loanBalance) : 0,
                    'payment_method' => 'bank_transfer',
                    'notes' => 'Imported from loan status CSV',
                    'created_by' => 1,
                    'approved_by' => 1,
                    'approved_at' => Carbon::now()->subMonths(6),
                    'created_at' => Carbon::now()->subMonths(6),
                    'updated_at' => Carbon::now()->subMonths(6),
                ]);
                
                $loanId++;
            }
        }
        
        $this->command->info('Loan status data imported successfully!');
    }
    
    private function parseCsv($filePath)
    {
        $csvData = [];
        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $csvData[] = $data;
            }
            fclose($handle);
        }
        return $csvData;
    }
    
    private function parseAmount($amount)
    {
        // Remove parentheses, quotes, and convert to number
        $amount = str_replace(['(', ')', '"', 'UGX', ','], '', $amount);
        $amount = str_replace([')', '-'], '', $amount);
        
        // Handle special cases like "2,000,000" format
        if (strpos($amount, ',') !== false) {
            $amount = str_replace(',', '', $amount);
        }
        
        return is_numeric($amount) ? (float)$amount : 0;
    }
}
