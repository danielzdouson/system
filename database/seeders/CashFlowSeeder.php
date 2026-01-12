<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashFlow;
use Illuminate\Support\Facades\DB;

class CashFlowSeeder extends Seeder
{
    public function run()
    {
        // Clear existing cash flow data
        DB::table('cash_flows')->delete();

        // Import cash flow data from cash_book 2024 - 2025.csv
        $cashFlowData = [
            // Income entries
            [
                'user_id' => 1, // Admin user
                'transaction_date' => '2024-01-01',
                'description' => 'STATIONERY',
                'category' => 'OPERATIONAL FACILITATIONS',
                'type' => 'expense',
                'amount' => 115000,
                'payment_method' => 'cash',
                'reference_number' => null,
                'notes' => 'Stationery purchase',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-01-15',
                'description' => 'Subscription',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 165000,
                'payment_method' => 'bank',
                'reference_number' => 'SUB-001',
                'notes' => 'Monthly subscription fees',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-02-01',
                'description' => 'Loan forms',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 30000,
                'payment_method' => 'cash',
                'reference_number' => 'LOAN-001',
                'notes' => 'Loan application fees',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-02-15',
                'description' => 'Fines',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 95000,
                'payment_method' => 'cash',
                'reference_number' => 'FINE-001',
                'notes' => 'Loan penalty fees',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-03-01',
                'description' => 'Loan charges',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 115350,
                'payment_method' => 'bank',
                'reference_number' => 'CHARGE-001',
                'notes' => 'Loan interest and charges',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-04-01',
                'description' => 'Administration',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 50000,
                'payment_method' => 'cash',
                'reference_number' => 'ADMIN-001',
                'notes' => 'Administrative fees',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-05-01',
                'description' => 'Wedding contbn',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 200000,
                'payment_method' => 'bank',
                'reference_number' => 'WEDDING-001',
                'notes' => 'Wedding contribution',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-06-01',
                'description' => 'Investment profits',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 8600000,
                'payment_method' => 'bank',
                'reference_number' => 'INV-001',
                'notes' => 'Investment returns',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-07-01',
                'description' => 'AGM EXPENSES',
                'category' => 'EXPENSES',
                'type' => 'expense',
                'amount' => 1500000,
                'payment_method' => 'bank',
                'reference_number' => 'AGM-001',
                'notes' => 'Annual General Meeting expenses',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-08-01',
                'description' => 'REMAINING EXPENSE',
                'category' => 'EXPENSES',
                'type' => 'expense',
                'amount' => 2750000,
                'payment_method' => 'bank',
                'reference_number' => 'EXP-001',
                'notes' => 'Remaining operational expenses',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-09-01',
                'description' => 'Investment portifolio growth',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 2550000,
                'payment_method' => 'bank',
                'reference_number' => 'GROWTH-001',
                'notes' => 'Portfolio growth returns',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-10-01',
                'description' => 'TITHE',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 632189,
                'payment_method' => 'cash',
                'reference_number' => 'TITHE-001',
                'notes' => 'Tithe contributions',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-11-01',
                'description' => 'EXECUTIVE (10%)',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 568970,
                'payment_method' => 'bank',
                'reference_number' => 'EXEC-001',
                'notes' => 'Executive committee fees',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2024-12-01',
                'description' => 'NET SHAREABLE',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 5120731,
                'payment_method' => 'bank',
                'reference_number' => 'SHARE-001',
                'notes' => 'Net shareable income',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2025-01-01',
                'description' => 'Retained earnings',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 10430185,
                'payment_method' => 'bank',
                'reference_number' => 'RETAIN-001',
                'notes' => 'Retained earnings from previous year',
                'status' => 'cleared',
            ],
            [
                'user_id' => 1,
                'transaction_date' => '2025-02-01',
                'description' => 'EQUITY',
                'category' => 'FINANCIAL',
                'type' => 'income',
                'amount' => 189031003,
                'payment_method' => 'bank',
                'reference_number' => 'EQUITY-001',
                'notes' => 'Total equity contribution',
                'status' => 'cleared',
            ],
        ];

        foreach ($cashFlowData as $record) {
            CashFlow::create($record);
        }

        $this->command->info('Imported ' . count($cashFlowData) . ' cash flow records');
    }
}
