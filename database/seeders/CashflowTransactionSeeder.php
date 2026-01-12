<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashflowTransaction;
use App\Models\FiscalYear;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class CashflowTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing cashflow transactions
        DB::table('cashflow_transactions')->delete();
        
        // Get or create fiscal year
        $fiscalYear = FiscalYear::first();
        if (!$fiscalYear) {
            $fiscalYear = FiscalYear::create([
                'name' => '2025-2026',
                'start_date' => '2025-07-01',
                'end_date' => '2026-06-30',
                'status' => 'active'
            ]);
        }

        // Get or create a test user
        $user = DB::table('users')->first();
        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $userId = $user->id;
        }
        
        // Get some members for testing
        $members = Member::take(5)->get();
        $memberIds = $members->pluck('id')->toArray();
        
        // Create test cashflow transactions
        $transactions = [
            // Operating Activities - Member Deposits (Inflows)
            [
                'transaction_date' => now()->subDays(30),
                'transaction_type' => CashflowTransaction::TYPE_INFLOW,
                'category' => CashflowTransaction::CATEGORY_OPERATING,
                'subcategory' => 'Member Savings Deposit',
                'description' => 'Monthly member savings deposit - John Doe',
                'amount' => 500000,
                'payment_method' => 'Bank Transfer',
                'reference_type' => CashflowTransaction::REFERENCE_DEPOSIT,
                'reference_number' => 'DEP-2025-001',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => $memberIds[0] ?? null,
                'created_by' => $userId,
                'notes' => 'Regular monthly savings contribution',
                'status' => CashflowTransaction::STATUS_CLEARED
            ],
            [
                'transaction_date' => now()->subDays(25),
                'transaction_type' => CashflowTransaction::TYPE_INFLOW,
                'category' => CashflowTransaction::CATEGORY_OPERATING,
                'subcategory' => 'Member Savings Deposit',
                'description' => 'Monthly member savings deposit - Jane Smith',
                'amount' => 350000,
                'payment_method' => 'Mobile Money',
                'reference_type' => CashflowTransaction::REFERENCE_DEPOSIT,
                'reference_number' => 'DEP-2025-002',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => $memberIds[1] ?? null,
                'created_by' => $userId,
                'notes' => 'Regular monthly savings contribution',
                'status' => CashflowTransaction::STATUS_CLEARED
            ],
            
            // Operating Activities - Welfare Payments (Outflows)
            [
                'transaction_date' => now()->subDays(20),
                'transaction_type' => CashflowTransaction::TYPE_OUTFLOW,
                'category' => CashflowTransaction::CATEGORY_OPERATING,
                'subcategory' => 'Welfare Payment',
                'description' => 'Welfare payment for member medical expenses',
                'amount' => 150000,
                'payment_method' => 'Cash',
                'reference_type' => CashflowTransaction::REFERENCE_WELFARE_PAYMENT,
                'reference_number' => 'WEL-2025-001',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => $memberIds[2] ?? null,
                'created_by' => $userId,
                'notes' => 'Medical emergency welfare support',
                'status' => CashflowTransaction::STATUS_CLEARED
            ],
            
            // Operating Activities - Fine Payments (Inflows)
            [
                'transaction_date' => now()->subDays(15),
                'transaction_type' => CashflowTransaction::TYPE_INFLOW,
                'category' => CashflowTransaction::CATEGORY_OPERATING,
                'subcategory' => 'Fine Payment',
                'description' => 'Late payment fine - Robert Johnson',
                'amount' => 50000,
                'payment_method' => 'Mobile Money',
                'reference_type' => CashflowTransaction::REFERENCE_FINE_PAYMENT,
                'reference_number' => 'FINE-2025-001',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => $memberIds[3] ?? null,
                'created_by' => $userId,
                'notes' => 'Fine for late monthly savings payment',
                'status' => CashflowTransaction::STATUS_CLEARED
            ],
            
            // Financing Activities - Loan Disbursements (Outflows)
            [
                'transaction_date' => now()->subDays(10),
                'transaction_type' => CashflowTransaction::TYPE_OUTFLOW,
                'category' => CashflowTransaction::CATEGORY_FINANCING,
                'subcategory' => 'Business Loan Disbursement',
                'description' => 'Business loan for Sarah Williams',
                'amount' => 2000000,
                'payment_method' => 'Bank Transfer',
                'reference_type' => CashflowTransaction::REFERENCE_LOAN_DISBURSEMENT,
                'reference_number' => 'LOAN-2025-001',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => $memberIds[4] ?? null,
                'created_by' => $userId,
                'notes' => 'Business startup loan approved and disbursed',
                'status' => CashflowTransaction::STATUS_CLEARED
            ],
            [
                'transaction_date' => now()->subDays(8),
                'transaction_type' => CashflowTransaction::TYPE_OUTFLOW,
                'category' => CashflowTransaction::CATEGORY_FINANCING,
                'subcategory' => 'Emergency Loan Disbursement',
                'description' => 'Emergency loan for Michael Brown',
                'amount' => 800000,
                'payment_method' => 'Cash',
                'reference_type' => CashflowTransaction::REFERENCE_LOAN_DISBURSEMENT,
                'reference_number' => 'LOAN-2025-002',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => $memberIds[0] ?? null,
                'created_by' => $userId,
                'notes' => 'Emergency medical loan',
                'status' => CashflowTransaction::STATUS_CLEARED
            ],
            
            // Financing Activities - Loan Repayments (Inflows)
            [
                'transaction_date' => now()->subDays(5),
                'transaction_type' => CashflowTransaction::TYPE_INFLOW,
                'category' => CashflowTransaction::CATEGORY_FINANCING,
                'subcategory' => 'Business Loan Repayment',
                'description' => 'Monthly loan repayment - David Wilson',
                'amount' => 450000,
                'payment_method' => 'Bank Transfer',
                'reference_type' => CashflowTransaction::REFERENCE_LOAN_REPAYMENT,
                'reference_number' => 'REPAY-2025-001',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => $memberIds[1] ?? null,
                'created_by' => $userId,
                'notes' => 'Monthly loan installment payment',
                'status' => CashflowTransaction::STATUS_CLEARED
            ],
            [
                'transaction_date' => now()->subDays(3),
                'transaction_type' => CashflowTransaction::TYPE_INFLOW,
                'category' => CashflowTransaction::CATEGORY_FINANCING,
                'subcategory' => 'Emergency Loan Repayment',
                'description' => 'Loan repayment - Lisa Anderson',
                'amount' => 200000,
                'payment_method' => 'Mobile Money',
                'reference_type' => CashflowTransaction::REFERENCE_LOAN_REPAYMENT,
                'reference_number' => 'REPAY-2025-002',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => $memberIds[2] ?? null,
                'created_by' => $userId,
                'notes' => 'Partial loan repayment',
                'status' => CashflowTransaction::STATUS_CLEARED
            ],
            
            // Investing Activities - Investment Returns (Inflows)
            [
                'transaction_date' => now()->subDays(2),
                'transaction_type' => CashflowTransaction::TYPE_INFLOW,
                'category' => CashflowTransaction::CATEGORY_INVESTING,
                'subcategory' => 'Investment Returns',
                'description' => 'Fixed deposit interest earnings',
                'amount' => 120000,
                'payment_method' => 'Bank Transfer',
                'reference_type' => CashflowTransaction::REFERENCE_OTHER,
                'reference_number' => 'INV-2025-001',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => null,
                'created_by' => $userId,
                'notes' => 'Quarterly investment returns from bank fixed deposit',
                'status' => CashflowTransaction::STATUS_CLEARED
            ],
            
            // Investing Activities - Investment Purchases (Outflows)
            [
                'transaction_date' => now()->subDays(1),
                'transaction_type' => CashflowTransaction::TYPE_OUTFLOW,
                'category' => CashflowTransaction::CATEGORY_INVESTING,
                'subcategory' => 'Investment Purchase',
                'description' => 'Purchase of government treasury bills',
                'amount' => 1500000,
                'payment_method' => 'Bank Transfer',
                'reference_type' => CashflowTransaction::REFERENCE_OTHER,
                'reference_number' => 'INV-2025-002',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => null,
                'created_by' => $userId,
                'notes' => '6-month treasury bill investment for liquidity management',
                'status' => CashflowTransaction::STATUS_CLEARED
            ],
            
            // Operating Activities - Administrative Expenses (Outflows)
            [
                'transaction_date' => now()->subDays(7),
                'transaction_type' => CashflowTransaction::TYPE_OUTFLOW,
                'category' => CashflowTransaction::CATEGORY_OPERATING,
                'subcategory' => 'Administrative Expenses',
                'description' => 'Office rent payment for July 2025',
                'amount' => 800000,
                'payment_method' => 'Bank Transfer',
                'reference_type' => CashflowTransaction::REFERENCE_EXPENSE,
                'reference_number' => 'EXP-2025-001',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => null,
                'created_by' => $userId,
                'notes' => 'Monthly office rent and utilities',
                'status' => CashflowTransaction::STATUS_CLEARED
            ],
            
            // Pending transaction for testing approval workflow
            [
                'transaction_date' => now(),
                'transaction_type' => CashflowTransaction::TYPE_OUTFLOW,
                'category' => CashflowTransaction::CATEGORY_OPERATING,
                'subcategory' => 'Administrative Expenses',
                'description' => 'Staff training workshop expenses',
                'amount' => 350000,
                'payment_method' => 'Pending',
                'reference_type' => CashflowTransaction::REFERENCE_EXPENSE,
                'reference_number' => 'EXP-2025-002',
                'fiscal_year_id' => $fiscalYear->id,
                'member_id' => null,
                'created_by' => $userId,
                'notes' => 'Pending approval - Staff capacity building workshop',
                'status' => CashflowTransaction::STATUS_PENDING
            ]
        ];

        // Insert all transactions
        foreach ($transactions as $transaction) {
            CashflowTransaction::create($transaction);
        }

        $this->command->info('Cashflow test data seeded successfully!');
        $this->command->info('Created ' . count($transactions) . ' cashflow transactions');
        $this->command->info('Categories seeded:');
        $this->command->info('- Operating Activities: Member deposits, welfare payments, fines, expenses');
        $this->command->info('- Financing Activities: Loan disbursements and repayments');
        $this->command->info('- Investing Activities: Investment returns and purchases');
        $this->command->info('- 1 Pending transaction for testing approval workflow');
    }
}
