<?php

namespace App\Services;

use App\Models\CashflowTransaction;
use App\Models\Deposit;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Distribution;
use App\Models\MemberAccount;
use App\Models\FiscalYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashFlowDashboardService
{
    /**
     * Get comprehensive cash flow dashboard data
     */
    public function getDashboardData($fiscalYearId = null, $month = null): array
    {
        $activeFiscalYear = $fiscalYearId 
            ? FiscalYear::find($fiscalYearId)
            : FiscalYear::getActive();

        if (!$activeFiscalYear) {
            return $this->getEmptyDashboard();
        }

        $today = now();
        $currentMonth = $month ?? $today->month;
        $currentYear = $activeFiscalYear->start_date->year;

        return [
            'overview' => $this->getCashOverview($activeFiscalYear, $today),
            'today_totals' => $this->getTodayTotals($activeFiscalYear, $today),
            'monthly_summary' => $this->getMonthlySummary($activeFiscalYear, $currentMonth, $currentYear),
            'recent_transactions' => $this->getRecentTransactions($activeFiscalYear, 10),
            'fiscal_year' => $activeFiscalYear,
            'filters' => [
                'available_months' => $this->getAvailableMonths($activeFiscalYear),
                'current_month' => $currentMonth,
                'current_year' => $currentYear,
            ]
        ];
    }

    /**
     * Get cash overview section data
     */
    private function getCashOverview($fiscalYear, $today): array
    {
        // Current balance from cashflow transactions
        $currentBalance = CashflowTransaction::where('fiscal_year_id', $fiscalYear->id)
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->selectRaw('SUM(CASE WHEN transaction_type = "INFLOW" THEN amount ELSE -amount END) as balance')
            ->value('balance') ?? 0;

        // Today's cashflow
        $todayInflows = CashflowTransaction::whereDate('transaction_date', $today->toDateString())
            ->where('fiscal_year_id', $fiscalYear->id)
            ->where('transaction_type', 'INFLOW')
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->sum('amount') ?? 0;

        $todayOutflows = CashflowTransaction::whereDate('transaction_date', $today->toDateString())
            ->where('fiscal_year_id', $fiscalYear->id)
            ->where('transaction_type', 'OUTFLOW')
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->sum('amount') ?? 0;

        // Get today's business transactions for more accurate picture
        $todayDeposits = Deposit::whereDate('deposit_date', $today->toDateString())
            ->where('fiscal_year_id', $fiscalYear->id)
            ->sum('amount') ?? 0;

        $todayLoanRepayments = LoanRepayment::whereDate('payment_date', $today->toDateString())
            ->whereHas('loan', function($query) use ($fiscalYear) {
                $query->where('fiscal_year_id', $fiscalYear->id);
            })
            ->sum('payment_amount') ?? 0;

        // Combine for accurate today's totals
        $totalTodayInflows = $todayInflows + $todayDeposits + $todayLoanRepayments;
        $totalTodayOutflows = $todayOutflows;

        return [
            'current_balance' => $currentBalance,
            'today_inflows' => $totalTodayInflows,
            'today_outflows' => $totalTodayOutflows,
            'net_cashflow' => $totalTodayInflows - $totalTodayOutflows,
            'balance_change' => $totalTodayInflows - $totalTodayOutflows,
            'balance_trend' => $this->getBalanceTrend($fiscalYear),
        ];
    }

    /**
     * Get today's transaction totals by category
     */
    private function getTodayTotals($fiscalYear, $today): array
    {
        // Today's deposits
        $savingsDeposits = Deposit::whereDate('deposit_date', $today->toDateString())
            ->where('fiscal_year_id', $fiscalYear->id)
            ->sum('amount') ?? 0;

        // Today's loan repayments
        $loanRepayments = LoanRepayment::whereDate('payment_date', $today->toDateString())
            ->whereHas('loan', function($query) use ($fiscalYear) {
                $query->where('fiscal_year_id', $fiscalYear->id);
            })
            ->sum('payment_amount') ?? 0;

        // Today's withdrawals (distributions)
        $withdrawals = Distribution::whereDate('created_at', $today->toDateString())
            ->whereHas('deposit', function($query) use ($fiscalYear) {
                $query->where('fiscal_year_id', $fiscalYear->id);
            })
            ->sum('amount') ?? 0;

        // Today's manual cashflow transactions
        $manualInflows = CashflowTransaction::whereDate('transaction_date', $today->toDateString())
            ->where('fiscal_year_id', $fiscalYear->id)
            ->where('transaction_type', 'INFLOW')
            ->where('reference_type', '!=', 'DEPOSIT') // Exclude deposits (already counted)
            ->where('reference_type', '!=', 'LOAN_REPAYMENT') // Exclude repayments (already counted)
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->sum('amount') ?? 0;

        $manualOutflows = CashflowTransaction::whereDate('transaction_date', $today->toDateString())
            ->where('fiscal_year_id', $fiscalYear->id)
            ->where('transaction_type', 'OUTFLOW')
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->sum('amount') ?? 0;

        return [
            'savings_deposits' => $savingsDeposits,
            'loan_repayments' => $loanRepayments,
            'member_withdrawals' => $withdrawals,
            'manual_inflows' => $manualInflows,
            'manual_outflows' => $manualOutflows,
            'total_inflows' => $savingsDeposits + $loanRepayments + $manualInflows,
            'total_outflows' => $withdrawals + $manualOutflows,
        ];
    }

    /**
     * Get monthly cash flow summary with daily breakdown
     */
    private function getMonthlySummary($fiscalYear, $month, $year): array
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get daily cashflow data for the month
        $dailyCashflow = CashflowTransaction::where('fiscal_year_id', $fiscalYear->id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->selectRaw('
                DATE(transaction_date) as date,
                SUM(CASE WHEN transaction_type = "INFLOW" THEN amount ELSE 0 END) as daily_inflows,
                SUM(CASE WHEN transaction_type = "OUTFLOW" THEN amount ELSE 0 END) as daily_outflows,
                SUM(CASE WHEN transaction_type = "INFLOW" THEN amount ELSE -amount END) as net_cashflow
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'inflows' => (float) $item->daily_inflows,
                    'outflows' => (float) $item->daily_outflows,
                    'net' => (float) $item->net_cashflow,
                ];
            })
            ->toArray();

        // Add business data to the daily breakdown
        $businessDailyData = $this->getBusinessDailyData($fiscalYear, $month, $year);
        $combinedDailyData = $this->combineDailyData($dailyCashflow, $businessDailyData);

        return [
            'daily_data' => $combinedDailyData,
            'total_inflows' => array_sum(array_column($combinedDailyData, 'inflows')),
            'total_outflows' => array_sum(array_column($combinedDailyData, 'outflows')),
            'net_cashflow' => array_sum(array_column($combinedDailyData, 'net')),
            'month_name' => $startDate->format('F Y'),
        ];
    }

    /**
     * Get business data for daily breakdown
     */
    private function getBusinessDailyData($fiscalYear, $month, $year): array
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get daily deposits
        $dailyDeposits = Deposit::where('fiscal_year_id', $fiscalYear->id)
            ->whereBetween('deposit_date', [$startDate, $endDate])
            ->selectRaw('DATE(deposit_date) as date, SUM(amount) as amount')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Get daily loan repayments
        $dailyRepayments = LoanRepayment::whereHas('loan', function($query) use ($fiscalYear) {
                $query->where('fiscal_year_id', $fiscalYear->id);
            })
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('DATE(payment_date) as date, SUM(payment_amount) as amount')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $businessData = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $businessData[$dateStr] = [
                'deposits' => $dailyDeposits->get($dateStr)?->amount ?? 0,
                'repayments' => $dailyRepayments->get($dateStr)?->amount ?? 0,
            ];
            $currentDate->addDay();
        }

        return $businessData;
    }

    /**
     * Combine cashflow and business data for daily breakdown
     */
    private function combineDailyData($cashflowData, $businessData): array
    {
        $combined = [];
        
        foreach ($cashflowData as $day) {
            $date = $day['date'];
            $combined[$date] = [
                'date' => $date,
                'inflows' => $day['inflows'],
                'outflows' => $day['outflows'],
                'net' => $day['net'],
            ];
        }

        foreach ($businessData as $date => $data) {
            if (!isset($combined[$date])) {
                $combined[$date] = [
                    'date' => $date,
                    'inflows' => 0,
                    'outflows' => 0,
                    'net' => 0,
                ];
            }
            
            $combined[$date]['inflows'] += $data['deposits'] + $data['repayments'];
            $combined[$date]['net'] = $combined[$date]['inflows'] - $combined[$date]['outflows'];
        }

        return array_values($combined);
    }

    /**
     * Get recent transactions for dashboard
     */
    private function getRecentTransactions($fiscalYear, $limit = 10): array
    {
        $transactions = CashflowTransaction::where('fiscal_year_id', $fiscalYear->id)
            ->with(['member', 'creator', 'fiscalYear'])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->transaction_date->format('Y-m-d'),
                    'description' => $transaction->description,
                    'transaction_type' => $transaction->transaction_type,
                    'category' => $transaction->category,
                    'subcategory' => $transaction->subcategory,
                    'amount' => (float) $transaction->amount,
                    'reference_type' => $transaction->reference_type,
                    'reference_number' => $transaction->reference_number,
                    'member' => $transaction->member ? [
                        'name' => $transaction->member->first_name . ' ' . $transaction->member->last_name,
                        'id' => $transaction->member->id,
                    ] : null,
                    'status' => $transaction->status,
                    'source_module' => $this->getSourceModule($transaction->reference_type),
                ];
            })
            ->toArray();

        // Add recent business transactions
        $recentDeposits = Deposit::where('fiscal_year_id', $fiscalYear->id)
            ->with(['member'])
            ->orderBy('deposit_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($deposit) {
                return [
                    'id' => 'DEP-' . $deposit->id,
                    'date' => $deposit->deposit_date->format('Y-m-d'),
                    'description' => 'Member Deposit - ' . $deposit->member->first_name . ' ' . $deposit->member->last_name,
                    'transaction_type' => 'INFLOW',
                    'category' => 'OPERATING',
                    'subcategory' => 'Member Deposits',
                    'amount' => (float) $deposit->amount,
                    'reference_type' => 'DEPOSIT',
                    'reference_number' => $deposit->id,
                    'member' => [
                        'name' => $deposit->member->first_name . ' ' . $deposit->member->last_name,
                        'id' => $deposit->member->id,
                    ],
                    'status' => 'CLEARED',
                    'source_module' => 'Savings',
                ];
            })
            ->toArray();

        $recentRepayments = LoanRepayment::whereHas('loan', function($query) use ($fiscalYear) {
                $query->where('fiscal_year_id', $fiscalYear->id);
            })
            ->with(['loan.member'])
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($repayment) {
                return [
                    'id' => 'REP-' . $repayment->id,
                    'date' => $repayment->payment_date->format('Y-m-d'),
                    'description' => 'Loan Repayment - ' . $repayment->loan->member->first_name . ' ' . $repayment->loan->member->last_name,
                    'transaction_type' => 'INFLOW',
                    'category' => 'FINANCING',
                    'subcategory' => 'Loan Repayments',
                    'amount' => (float) $repayment->payment_amount,
                    'reference_type' => 'LOAN_REPAYMENT',
                    'reference_number' => $repayment->loan_id,
                    'member' => [
                        'name' => $repayment->loan->member->first_name . ' ' . $repayment->loan->member->last_name,
                        'id' => $repayment->loan->member->id,
                    ],
                    'status' => 'CLEARED',
                    'source_module' => 'Loans',
                ];
            })
            ->toArray();

        // Combine and sort by date
        $allTransactions = array_merge($transactions, $recentDeposits, $recentRepayments);
        usort($allTransactions, function ($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return array_slice($allTransactions, 0, $limit);
    }

    /**
     * Get balance trend for the past 7 days
     */
    private function getBalanceTrend($fiscalYear): string
    {
        $balances = CashflowTransaction::where('fiscal_year_id', $fiscalYear->id)
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->selectRaw('
                DATE(transaction_date) as date,
                SUM(CASE WHEN transaction_type = "INFLOW" THEN amount ELSE -amount END) as daily_balance
            ')
            ->where('transaction_date', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->pluck('daily_balance')
            ->reverse()
            ->values();

        if ($balances->count() < 2) {
            return 'stable';
        }

        $firstBalance = $balances->first();
        $lastBalance = $balances->last();
        
        if ($lastBalance > $firstBalance) {
            return 'increasing';
        } elseif ($lastBalance < $firstBalance) {
            return 'decreasing';
        } else {
            return 'stable';
        }
    }

    /**
     * Get source module name from reference type
     */
    private function getSourceModule($referenceType): string
    {
        return match($referenceType) {
            'DEPOSIT' => 'Savings',
            'LOAN_DISBURSEMENT' => 'Loans',
            'LOAN_REPAYMENT' => 'Loans',
            'WELFARE_PAYMENT' => 'Welfare',
            'FINE_PAYMENT' => 'Fines',
            'EXPENSE' => 'Expenses',
            default => 'Cashflow',
        };
    }

    /**
     * Get available months for the fiscal year
     */
    private function getAvailableMonths($fiscalYear): array
    {
        $months = [];
        $startDate = $fiscalYear->start_date;
        $endDate = $fiscalYear->end_date;
        
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if ($current->year == $startDate->year || $current->year == $endDate->year) {
                $months[] = [
                    'value' => $current->month,
                    'name' => $current->format('F'),
                ];
            }
            $current->addMonth();
        }
        
        return $months;
    }

    /**
     * Get empty dashboard structure
     */
    private function getEmptyDashboard(): array
    {
        return [
            'overview' => [
                'current_balance' => 0,
                'today_inflows' => 0,
                'today_outflows' => 0,
                'net_cashflow' => 0,
                'balance_change' => 0,
                'balance_trend' => 'stable',
            ],
            'today_totals' => [
                'savings_deposits' => 0,
                'loan_repayments' => 0,
                'member_withdrawals' => 0,
                'manual_inflows' => 0,
                'manual_outflows' => 0,
                'total_inflows' => 0,
                'total_outflows' => 0,
            ],
            'monthly_summary' => [
                'daily_data' => [],
                'total_inflows' => 0,
                'total_outflows' => 0,
                'net_cashflow' => 0,
                'month_name' => 'No Data',
            ],
            'recent_transactions' => [],
            'fiscal_year' => null,
            'filters' => [
                'available_months' => [],
                'current_month' => date('n'),
                'current_year' => date('Y'),
            ],
        ];
    }
}
