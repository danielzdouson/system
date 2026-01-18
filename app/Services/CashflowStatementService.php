<?php

namespace App\Services;

use App\Models\CashflowTransaction;
use App\Models\Deposit;
use App\Models\Distribution;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Fine;
use App\Models\FiscalYear;
use Carbon\Carbon;

class CashflowStatementService
{
    /**
     * Generate monthly cashflow statement with real business data
     */
    public function generateMonthlyStatement($fiscalYearId, $month, $filters = []): array
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        if (!$fiscalYear) {
            throw new \Exception('Fiscal year not found');
        }

        $startDate = Carbon::create($fiscalYear->start_date->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        // Get real business data for the period
        $businessData = $this->getBusinessDataForPeriod($fiscalYearId, $month, $startDate, $endDate);
        
        // Get manual cashflow transactions
        $manualCashflow = $this->getManualCashflowForPeriod($fiscalYearId, $month, $startDate, $endDate);
        
        // Combine all data
        $combinedData = $this->combineCashflowData($businessData, $manualCashflow);
        
        // Calculate monthly summary
        $monthData = CashPositionService::getMonthlyCashflow($fiscalYear->start_date->year, $month);
        
        return [
            'fiscal_year' => $fiscalYear->name,
            'month' => $month,
            'period' => Carbon::create($fiscalYear->start_date->year, $month)->format('F Y'),
            
            'operating_activities' => [
                'inflows' => $combinedData['operating']['inflows'],
                'outflows' => $combinedData['operating']['outflows'],
                'net' => $combinedData['operating']['net'],
                'details' => $this->getOperatingDetailsWithBusinessData($fiscalYearId, $month, $businessData, $manualCashflow)
            ],
            
            'investing_activities' => [
                'inflows' => $combinedData['investing']['inflows'],
                'outflows' => $combinedData['investing']['outflows'],
                'net' => $combinedData['investing']['net'],
                'details' => $this->getInvestingDetailsWithBusinessData($fiscalYearId, $month, $businessData, $manualCashflow)
            ],
            
            'financing_activities' => [
                'inflows' => $combinedData['financing']['inflows'],
                'outflows' => $combinedData['financing']['outflows'],
                'net' => $combinedData['financing']['net'],
                'details' => $this->getFinancingDetailsWithBusinessData($fiscalYearId, $month, $businessData, $manualCashflow)
            ],
            
            'summary' => [
                'opening_balance' => $monthData['opening_balance'],
                'net_cashflow' => $monthData['net_cashflow'],
                'closing_balance' => $monthData['closing_balance']
            ],
            
            // Add individual transactions for detailed view
            'transactions' => $this->getFilteredTransactions($fiscalYearId, $month)
        ];
    }

    /**
     * Get real business data for the period
     */
    private function getBusinessDataForPeriod($fiscalYearId, $month, $startDate, $endDate): array
    {
        return [
            'deposits' => $this->getDepositsForPeriod($fiscalYearId, $month),
            'distributions' => $this->getDistributionsForPeriod($fiscalYearId, $month),
            'loans' => $this->getLoansForPeriod($fiscalYearId, $month),
            'loan_repayments' => $this->getLoanRepaymentsForPeriod($fiscalYearId, $month),
            'fines' => $this->getFinesForPeriod($fiscalYearId, $month),
        ];
    }

    /**
     * Get deposits for the period
     */
    private function getDepositsForPeriod($fiscalYearId, $month): array
    {
        return Deposit::where('fiscal_year_id', $fiscalYearId)
            ->where('month', $month)
            ->with(['member', 'distributions'])
            ->get()
            ->toArray();
    }

    /**
     * Get distributions for the period
     */
    private function getDistributionsForPeriod($fiscalYearId, $month): array
    {
        $depositIds = Deposit::where('fiscal_year_id', $fiscalYearId)
            ->where('month', $month)
            ->pluck('id');

        return Distribution::whereIn('deposit_id', $depositIds)
            ->with(['deposit.member'])
            ->get()
            ->toArray();
    }

    /**
     * Get loans for the period
     */
    private function getLoansForPeriod($fiscalYearId, $month): array
    {
        return Loan::where('fiscal_year_id', $fiscalYearId)
            ->whereMonth('disbursement_date', $month)
            ->with(['member'])
            ->get()
            ->toArray();
    }

    /**
     * Get loan repayments for the period
     */
    private function getLoanRepaymentsForPeriod($fiscalYearId, $month): array
    {
        return LoanRepayment::whereHas('loan', function($query) use ($fiscalYearId) {
                $query->where('fiscal_year_id', $fiscalYearId);
            })
            ->whereMonth('payment_date', $month)
            ->with(['loan.member'])
            ->get()
            ->toArray();
    }

    /**
     * Get fines for the period
     */
    private function getFinesForPeriod($fiscalYearId, $month): array
    {
        return Fine::where('fiscal_year_id', $fiscalYearId)
            ->where('month', $month)
            ->with(['member'])
            ->get()
            ->toArray();
    }

    /**
     * Get manual cashflow transactions for the period
     */
    private function getManualCashflowForPeriod($fiscalYearId, $month, $startDate, $endDate): array
    {
        return CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->fiscalYear($fiscalYearId)
            ->orderBy('transaction_date')
            ->get()
            ->toArray();
    }

    /**
     * Combine business data with manual cashflow
     */
    private function combineCashflowData($businessData, $manualCashflow): array
    {
        $combined = [
            'operating' => ['inflows' => 0, 'outflows' => 0, 'net' => 0],
            'investing' => ['inflows' => 0, 'outflows' => 0, 'net' => 0],
            'financing' => ['inflows' => 0, 'outflows' => 0, 'net' => 0]
        ];

        // Add deposits (Operating Inflows)
        foreach ($businessData['deposits'] as $deposit) {
            $combined['operating']['inflows'] += $deposit['amount'];
        }

        // Add distributions (Operating Outflows)
        foreach ($businessData['distributions'] as $distribution) {
            $combined['operating']['outflows'] += $distribution['amount'];
        }

        // Add loan disbursements (Financing Outflows)
        foreach ($businessData['loans'] as $loan) {
            $combined['financing']['outflows'] += $loan['principal_amount'];
        }

        // Add loan repayments (Financing Inflows)
        foreach ($businessData['loan_repayments'] as $repayment) {
            $combined['financing']['inflows'] += $repayment['payment_amount'];
        }

        // Add fine payments (Operating Outflows - already included in distributions)
        // Fines are already captured in distributions, so no double counting

        // Add manual cashflow transactions
        foreach ($manualCashflow as $transaction) {
            $category = strtolower($transaction['category']);
            $type = $transaction['transaction_type'];
            
            if (isset($combined[$category])) {
                if ($type === 'INFLOW') {
                    $combined[$category]['inflows'] += $transaction['amount'];
                } else {
                    $combined[$category]['outflows'] += $transaction['amount'];
                }
            }
        }

        // Calculate net values
        foreach ($combined as $category => $data) {
            $combined[$category]['net'] = $data['inflows'] - $data['outflows'];
        }

        return $combined;
    }

    /**
     * Get operating activities details with business data
     */
    private function getOperatingDetailsWithBusinessData($fiscalYearId, $month, $businessData, $manualCashflow): array
    {
        $details = [];
        
        // Member Deposits
        $depositTotal = array_sum(array_column($businessData['deposits'], 'amount'));
        if ($depositTotal > 0) {
            $details[] = [
                'subcategory' => 'Member Deposits',
                'inflows' => $depositTotal,
                'outflows' => 0,
                'net' => $depositTotal,
                'transactions' => $businessData['deposits']
            ];
        }

        // Savings Distributions
        $savingsTotal = 0;
        foreach ($businessData['distributions'] as $distribution) {
            if ($distribution['type'] === 'savings') {
                $savingsTotal += $distribution['amount'];
            }
        }
        if ($savingsTotal > 0) {
            $details[] = [
                'subcategory' => 'Savings Distribution',
                'inflows' => 0,
                'outflows' => $savingsTotal,
                'net' => -$savingsTotal,
                'transactions' => array_filter($businessData['distributions'], fn($d) => $d['type'] === 'savings')
            ];
        }

        // Welfare Distributions
        $welfareTotal = 0;
        foreach ($businessData['distributions'] as $distribution) {
            if ($distribution['type'] === 'welfare') {
                $welfareTotal += $distribution['amount'];
            }
        }
        if ($welfareTotal > 0) {
            $details[] = [
                'subcategory' => 'Welfare Fund',
                'inflows' => 0,
                'outflows' => $welfareTotal,
                'net' => -$welfareTotal,
                'transactions' => array_filter($businessData['distributions'], fn($d) => $d['type'] === 'welfare')
            ];
        }

        // Fine Payments
        $finesTotal = 0;
        foreach ($businessData['distributions'] as $distribution) {
            if ($distribution['type'] === 'fines') {
                $finesTotal += $distribution['amount'];
            }
        }
        if ($finesTotal > 0) {
            $details[] = [
                'subcategory' => 'Fine Payments',
                'inflows' => 0,
                'outflows' => $finesTotal,
                'net' => -$finesTotal,
                'transactions' => array_filter($businessData['distributions'], fn($d) => $d['type'] === 'fines')
            ];
        }

        // Add manual operating transactions
        $manualOperating = array_filter($manualCashflow, fn($t) => $t['category'] === 'OPERATING');
        $manualGrouped = collect($manualOperating)->groupBy('subcategory');
        
        foreach ($manualGrouped as $subcategory => $transactions) {
            $inflows = $transactions->where('transaction_type', 'INFLOW')->sum('amount');
            $outflows = $transactions->where('transaction_type', 'OUTFLOW')->sum('amount');
            
            $details[] = [
                'subcategory' => $subcategory,
                'inflows' => $inflows,
                'outflows' => $outflows,
                'net' => $inflows - $outflows,
                'transactions' => $transactions->toArray()
            ];
        }

        return $details;
    }

    /**
     * Get investing activities details with business data
     */
    private function getInvestingDetailsWithBusinessData($fiscalYearId, $month, $businessData, $manualCashflow): array
    {
        $details = [];
        
        // Add manual investing transactions
        $manualInvesting = array_filter($manualCashflow, fn($t) => $t['category'] === 'INVESTING');
        $manualGrouped = collect($manualInvesting)->groupBy('subcategory');
        
        foreach ($manualGrouped as $subcategory => $transactions) {
            $inflows = $transactions->where('transaction_type', 'INFLOW')->sum('amount');
            $outflows = $transactions->where('transaction_type', 'OUTFLOW')->sum('amount');
            
            $details[] = [
                'subcategory' => $subcategory,
                'inflows' => $inflows,
                'outflows' => $outflows,
                'net' => $inflows - $outflows,
                'transactions' => $transactions->toArray()
            ];
        }

        return $details;
    }

    /**
     * Get financing activities details with business data
     */
    private function getFinancingDetailsWithBusinessData($fiscalYearId, $month, $businessData, $manualCashflow): array
    {
        $details = [];
        
        // Loan Disbursements
        $loanTotal = array_sum(array_column($businessData['loans'], 'principal_amount'));
        if ($loanTotal > 0) {
            $details[] = [
                'subcategory' => 'Loan Disbursements',
                'inflows' => 0,
                'outflows' => $loanTotal,
                'net' => -$loanTotal,
                'transactions' => $businessData['loans']
            ];
        }

        // Loan Repayments
        $repaymentTotal = array_sum(array_column($businessData['loan_repayments'], 'payment_amount'));
        if ($repaymentTotal > 0) {
            $details[] = [
                'subcategory' => 'Loan Repayments',
                'inflows' => $repaymentTotal,
                'outflows' => 0,
                'net' => $repaymentTotal,
                'transactions' => $businessData['loan_repayments']
            ];
        }

        // Add manual financing transactions
        $manualFinancing = array_filter($manualCashflow, fn($t) => $t['category'] === 'FINANCING');
        $manualGrouped = collect($manualFinancing)->groupBy('subcategory');
        
        foreach ($manualGrouped as $subcategory => $transactions) {
            $inflows = $transactions->where('transaction_type', 'INFLOW')->sum('amount');
            $outflows = $transactions->where('transaction_type', 'OUTFLOW')->sum('amount');
            
            $details[] = [
                'subcategory' => $subcategory,
                'inflows' => $inflows,
                'outflows' => $outflows,
                'net' => $inflows - $outflows,
                'transactions' => $transactions->toArray()
            ];
        }

        return $details;
    }

    /**
     * Generate fiscal year cashflow statement
     */
    public function generateFiscalYearStatement($fiscalYearId): array
    {
        return CashPositionService::getFiscalYearCashflow($fiscalYearId);
    }

    // Legacy methods for backward compatibility
    private function getOperatingInflows($fiscalYearId, $month): float
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        $startDate = Carbon::create($fiscalYear->start_date->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->operating()
            ->inflow()
            ->fiscalYear($fiscalYearId)
            ->sum('amount');
    }

    private function getOperatingOutflows($fiscalYearId, $month): float
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        $startDate = Carbon::create($fiscalYear->start_date->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->operating()
            ->outflow()
            ->fiscalYear($fiscalYearId)
            ->sum('amount');
    }

    private function getInvestingInflows($fiscalYearId, $month): float
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        $startDate = Carbon::create($fiscalYear->start_date->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->investing()
            ->inflow()
            ->fiscalYear($fiscalYearId)
            ->sum('amount');
    }

    private function getInvestingOutflows($fiscalYearId, $month): float
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        $startDate = Carbon::create($fiscalYear->start_date->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->investing()
            ->outflow()
            ->fiscalYear($fiscalYearId)
            ->sum('amount');
    }

    private function getFinancingInflows($fiscalYearId, $month): float
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        $startDate = Carbon::create($fiscalYear->start_date->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->financing()
            ->inflow()
            ->fiscalYear($fiscalYearId)
            ->sum('amount');
    }

    private function getFinancingOutflows($fiscalYearId, $month): float
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        $startDate = Carbon::create($fiscalYear->start_date->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->financing()
            ->outflow()
            ->fiscalYear($fiscalYearId)
            ->sum('amount');
    }

    private function getOperatingDetails($fiscalYearId, $month): array
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        $startDate = Carbon::create($fiscalYear->start_date->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->operating()
            ->fiscalYear($fiscalYearId)
            ->orderBy('transaction_date')
            ->get()
            ->groupBy('subcategory')
            ->map(function ($transactions, $subcategory) {
                $inflows = $transactions->where('transaction_type', 'INFLOW');
                $outflows = $transactions->where('transaction_type', 'OUTFLOW');
                
                return [
                    'subcategory' => $subcategory,
                    'inflows' => $inflows->sum('amount'),
                    'outflows' => $outflows->sum('amount'),
                    'net' => $inflows->sum('amount') - $outflows->sum('amount'),
                    'transactions' => $transactions->toArray()
                ];
            })
            ->values()
            ->toArray();
    }

    private function getInvestingDetails($fiscalYearId, $month): array
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        $startDate = Carbon::create($fiscalYear->start_date->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->investing()
            ->fiscalYear($fiscalYearId)
            ->orderBy('transaction_date')
            ->get()
            ->groupBy('subcategory')
            ->map(function ($transactions, $subcategory) {
                $inflows = $transactions->where('transaction_type', 'INFLOW');
                $outflows = $transactions->where('transaction_type', 'OUTFLOW');
                
                return [
                    'subcategory' => $subcategory,
                    'inflows' => $inflows->sum('amount'),
                    'outflows' => $outflows->sum('amount'),
                    'net' => $inflows->sum('amount') - $outflows->sum('amount'),
                    'transactions' => $transactions->toArray()
                ];
            })
            ->values()
            ->toArray();
    }

    private function getFinancingDetails($fiscalYearId, $month): array
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        $startDate = Carbon::create($fiscalYear->start_date->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['CLEARED', 'PENDING'])
            ->financing()
            ->fiscalYear($fiscalYearId)
            ->orderBy('transaction_date')
            ->get()
            ->groupBy('subcategory')
            ->map(function ($transactions, $subcategory) {
                $inflows = $transactions->where('transaction_type', 'INFLOW');
                $outflows = $transactions->where('transaction_type', 'OUTFLOW');
                
                return [
                    'subcategory' => $subcategory,
                    'inflows' => $inflows->sum('amount'),
                    'outflows' => $outflows->sum('amount'),
                    'net' => $inflows->sum('amount') - $outflows->sum('amount'),
                    'transactions' => $transactions->toArray()
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get individual transactions for the period
     */
    private function getPeriodTransactions($fiscalYearId, $month): array
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        $startDate = Carbon::create($fiscalYear->start_date->year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        return CashflowTransaction::with(['member', 'creator', 'fiscalYear'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
    
    /**
     * Get all transactions for selected month/year based on transaction dates
     */
    private function getFilteredTransactions($fiscalYearId, $month, $filters = []): array
    {
        // Use the fiscal year to get the correct year, but filter by transaction dates
        $fiscalYear = FiscalYear::find($fiscalYearId);
        
        // Calculate the actual year based on fiscal year start month
        $fiscalYearStartMonth = $fiscalYear->start_date->month;
        $fiscalYearStartYear = $fiscalYear->start_date->year;
        
        if ($month >= $fiscalYearStartMonth) {
            // Month is in the first year of fiscal year
            $year = $fiscalYearStartYear;
        } else {
            // Month is in the second year of fiscal year
            $year = $fiscalYearStartYear + 1;
        }
        
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        // Simple date-based query - get ALL transactions for this month/year
        $query = CashflowTransaction::with(['member', 'creator', 'fiscalYear'])
            ->whereBetween('transaction_date', [$startDate, $endDate]);
            
        // Apply additional filters if provided
        if (isset($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }
        
        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}
