<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashFlow;
use Illuminate\Support\Facades\DB;

class CashBookSimpleSeeder extends Seeder
{
    public function run()
    {
        // Clear existing cash flow data
        DB::table('cash_flows')->delete();
        
        // Manual cash book data based on the CSV structure
        $cashBookData = [
            // Income entries
            ['category' => 'Savings', 'type' => 'income', 'amounts' => [4075000, 5325000, 5150000, 2300000, 0, 0, 0, 0, 0, 0, 0, 0]],
            ['category' => 'Welfare', 'type' => 'income', 'amounts' => [62000, 82000, 95000, 32000, 0, 0, 0, 0, 0, 0, 0, 0]],
            ['category' => 'Loan charges', 'type' => 'income', 'amounts' => [0, 27500, 87850, 0, 0, 0, 0, 0, 0, 0, 0, 0]],
            ['category' => 'Educ in', 'type' => 'income', 'amounts' => [4643050, 8267500, 5028750, 50000, 0, 0, 0, 0, 0, 0, 0, 0]],
            ['category' => 'Subscription/membership', 'type' => 'income', 'amounts' => [520000, 250000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]],
            ['category' => 'Fines', 'type' => 'income', 'amounts' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]],
            
            // Expense entries
            ['category' => 'Education out', 'type' => 'expense', 'amounts' => [5975700, 10164500, 18988000, 0, 0, 0, 0, 0, 0, 0, 0, 0]],
            ['category' => 'Loan disbursed', 'type' => 'expense', 'amounts' => [34450000, 7000000, 15000000, 0, 0, 0, 0, 0, 0, 0, 0, 0]],
            ['category' => 'Other expenses', 'type' => 'expense', 'amounts' => [4206000, 158000, 6000, 4000, 0, 0, 0, 0, 0, 0, 0, 0]],
        ];
        
        $months = ['Jul-25', 'Aug-25', 'Sep-25', 'Oct-25', 'Nov-25', 'Dec-25', 
                  'Jan-26', 'Feb-26', 'Mar-26', 'Apr-26', 'May-26', 'Jun-26'];
        
        $cashFlowRecords = [];
        
        foreach ($cashBookData as $item) {
            foreach ($months as $index => $month) {
                $amount = $item['amounts'][$index];
                
                if ($amount > 0) {
                    $transactionDate = $this->getTransactionDate($month);
                    
                    $cashFlowRecords[] = [
                        'user_id' => 1,
                        'transaction_date' => $transactionDate,
                        'description' => $item['category'],
                        'category' => $this->getCategory($item['category']),
                        'type' => $item['type'],
                        'amount' => $amount,
                        'payment_method' => 'cash',
                        'reference_number' => $this->generateReference($item['category'], $month),
                        'notes' => $item['category'] . ' - ' . $month,
                        'status' => 'cleared',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        
        // Insert all records in batches to avoid memory issues
        if (!empty($cashFlowRecords)) {
            $chunks = array_chunk($cashFlowRecords, 100);
            
            foreach ($chunks as $chunk) {
                DB::table('cash_flows')->insert($chunk);
            }
            
            $this->command->info('Successfully imported ' . count($cashFlowRecords) . ' cash flow records from cash book');
        } else {
            $this->command->warn('No valid records found in cash book');
        }
    }
    
    private function getTransactionDate($month)
    {
        $monthMap = [
            'Jul-25' => '2025-07-15',
            'Aug-25' => '2025-08-15',
            'Sep-25' => '2025-09-15',
            'Oct-25' => '2025-10-15',
            'Nov-25' => '2025-11-15',
            'Dec-25' => '2025-12-15',
            'Jan-26' => '2026-01-15',
            'Feb-26' => '2026-02-15',
            'Mar-26' => '2026-03-15',
            'Apr-26' => '2026-04-15',
            'May-26' => '2026-05-15',
            'Jun-26' => '2026-06-15',
        ];
        
        return $monthMap[$month] ?? '2025-07-15';
    }
    
    private function getCategory($category)
    {
        $categoryMap = [
            'Savings' => 'FINANCIAL',
            'Welfare' => 'WELFARE',
            'Loan charges' => 'FINANCIAL',
            'Educ in' => 'EDUCATION',
            'Subscription/membership' => 'FINANCIAL',
            'Fines' => 'FINANCIAL',
            'Education out' => 'EDUCATION',
            'Loan disbursed' => 'LOANS',
            'Other expenses' => 'EXPENSES',
        ];
        
        return $categoryMap[$category] ?? 'GENERAL';
    }
    
    private function generateReference($category, $month)
    {
        $prefix = strtoupper(substr($category, 0, 3));
        $monthCode = str_replace('-', '', $month);
        return $prefix . '-' . $monthCode . '-' . rand(100, 999);
    }
}
