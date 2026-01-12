<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashFlow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CashBookImporterSeeder extends Seeder
{
    public function run()
    {
        // Clear existing cash flow data
        DB::table('cash_flows')->delete();
        
        $filePath = database_path('../data/cash_book 2024 - 2025.csv');
        
        if (!file_exists($filePath)) {
            $this->command->error('Cash book file not found: ' . $filePath);
            return;
        }
        
        $csvData = $this->parseCSV($filePath);
        $cashFlowRecords = [];
        
        // Process each row from the cash book
        foreach ($csvData as $row) {
            $category = trim($row[0]);
            
            // Skip empty categories and headers
            if (empty($category) || in_array($category, ['CASHFLOW [2022/2023]', 'Cash incoming', 'Cash outgoing'])) {
                continue;
            }
            
            // Process monthly data (Jul-25 to Jun-26)
            $months = ['Jul-25', 'Aug-25', 'Sep-25', 'Oct-25', 'Nov-25', 'Dec-25', 
                      'Jan-26', 'Feb-26', 'Mar-26', 'Apr-26', 'May-26', 'Jun-26'];
            
            for ($i = 0; $i < 12; $i++) {
                $amount = $this->parseAmount($row[$i + 1]);
                
                if ($amount > 0) {
                    $transactionDate = $this->getTransactionDate($months[$i]);
                    $type = $this->getTransactionType($category);
                    
                    $cashFlowRecords[] = [
                        'user_id' => 1, // Admin user
                        'transaction_date' => $transactionDate,
                        'description' => $category,
                        'category' => $this->getCategory($category),
                        'type' => $type,
                        'amount' => $amount,
                        'payment_method' => 'cash',
                        'reference_number' => $this->generateReference($category, $months[$i]),
                        'notes' => $category . ' - ' . $months[$i],
                        'status' => 'cleared',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        
        // Insert all records
        if (!empty($cashFlowRecords)) {
            DB::table('cash_flows')->insert($cashFlowRecords);
            $this->command->info('Successfully imported ' . count($cashFlowRecords) . ' cash flow records from cash book');
        } else {
            $this->command->warn('No valid records found in cash book');
        }
    }
    
    private function parseCSV($filePath)
    {
        $csvData = [];
        $handle = fopen($filePath, 'r');
        
        if ($handle !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Clean and process each cell
                $cleanRow = [];
                foreach ($row as $cell) {
                    $cleanCell = trim($cell);
                    // Remove USh prefix and clean the amount
                    $cleanCell = str_replace('USh', '', $cleanCell);
                    $cleanCell = str_replace('"', '', $cleanCell);
                    $cleanCell = preg_replace('/\s+/', '', $cleanCell);
                    $cleanRow[] = $cleanCell;
                }
                $csvData[] = $cleanRow;
            }
            fclose($handle);
        }
        
        return $csvData;
    }
    
    private function parseAmount($amountString)
    {
        // Remove commas, quotes, and other non-numeric characters
        $cleanAmount = preg_replace('/[^0-9.]/', '', $amountString);
        return is_numeric($cleanAmount) ? (float)$cleanAmount : 0;
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
    
    private function getTransactionType($category)
    {
        $incomeCategories = [
            'Savings', 'Asset sales', 'Welfare', 'Loan charges', 'Educ in', 
            'Subscription/membership', 'Tithe', 'Executive (10%)', 
            'NET SHAREABLE', 'Retained earnings', 'EQUITY'
        ];
        
        return in_array($category, $incomeCategories) ? 'income' : 'expense';
    }
    
    private function getCategory($category)
    {
        $categoryMap = [
            'Savings' => 'FINANCIAL',
            'Asset sales' => 'ASSETS',
            'Welfare' => 'WELFARE',
            'Loan charges' => 'FINANCIAL',
            'Educ in' => 'EDUCATION',
            'Subscription/membership' => 'FINANCIAL',
            'Fines' => 'FINANCIAL',
            'Tithe' => 'FINANCIAL',
            'Executive (10%)' => 'FINANCIAL',
            'NET SHAREABLE' => 'FINANCIAL',
            'Retained earnings' => 'FINANCIAL',
            'EQUITY' => 'FINANCIAL',
            'OPENING BALANCE' => 'BALANCE',
            'STATIONERY' => 'OPERATIONAL FACILITATIONS',
            'AGM EXPENSES' => 'EXPENSES',
            'REMAINING EXPENSE' => 'EXPENSES',
        ];
        
        return $categoryMap[$category] ?? 'GENERAL';
    }
    
    private function generateReference($category, $month)
    {
        $prefix = strtoupper(substr($category, 0, 3));
        $monthCode = str_replace(['-', ' '], '', $month);
        return $prefix . '-' . $monthCode . '-' . rand(100, 999);
    }
}
