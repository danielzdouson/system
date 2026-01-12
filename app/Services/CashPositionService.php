<?php

namespace App\Services;

use App\Models\CashflowTransaction;
use App\Models\FiscalYear;
use Carbon\Carbon;

class CashPositionService
{
    /**
     * Get current cash balance
     */
    public static function getCurrentBalance(): float
    {
        return CashflowTransaction::cleared()
            ->selectRaw('SUM(CASE WHEN transaction_type = "INFLOW" THEN amount ELSE -amount END) as balance')
            ->value('balance') ?? 0;
    }

    /**
     * Get cash balance as of specific date
     */
    public static function getBalanceAsOf($date): float
    {
        $carbonDate = is_string($date) ? Carbon::parse($date) : $date;
        return CashflowTransaction::where('transaction_date', '<=', $carbonDate)
            ->cleared()
            ->selectRaw('SUM(CASE WHEN transaction_type = "INFLOW" THEN amount ELSE -amount END) as balance')
            ->value('balance') ?? 0;
    }

    /**
     * Get monthly cash flow summary
     */
    public static function getMonthlyCashflow($year, $month): array
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $inflows = CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->cleared()
            ->inflow()
            ->sum('amount');
            
        $outflows = CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->cleared()
            ->outflow()
            ->sum('amount');
            
        return [
            'period' => $startDate->format('F Y'),
            'opening_balance' => self::getBalanceAsOf($startDate->copy()->subDay()),
            'inflows' => $inflows,
            'outflows' => $outflows,
            'net_cashflow' => $inflows - $outflows,
            'closing_balance' => self::getBalanceAsOf($endDate)
        ];
    }

    /**
     * Get fiscal year cash flow summary
     */
    public static function getFiscalYearCashflow($fiscalYearId): array
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        if (!$fiscalYear) {
            return [];
        }

        $startDate = $fiscalYear->start_date;
        $endDate = $fiscalYear->end_date;
        
        // Operating activities
        $operatingInflows = CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->cleared()
            ->operating()
            ->inflow()
            ->sum('amount');
            
        $operatingOutflows = CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->cleared()
            ->operating()
            ->outflow()
            ->sum('amount');

        // Investing activities
        $investingInflows = CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->cleared()
            ->investing()
            ->inflow()
            ->sum('amount');
            
        $investingOutflows = CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->cleared()
            ->investing()
            ->outflow()
            ->sum('amount');

        // Financing activities
        $financingInflows = CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->cleared()
            ->financing()
            ->inflow()
            ->sum('amount');
            
        $financingOutflows = CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->cleared()
            ->financing()
            ->outflow()
            ->sum('amount');

        $openingBalance = self::getBalanceAsOf($startDate->copy()->subDay()->toDateString());
        $closingBalance = self::getBalanceAsOf($endDate->toDateString());

        return [
            'fiscal_year' => $fiscalYear->name,
            'period' => $fiscalYear->start_date->format('M d, Y') . ' - ' . $fiscalYear->end_date->format('M d, Y'),
            'opening_balance' => $openingBalance,
            
            'operating_activities' => [
                'inflows' => $operatingInflows,
                'outflows' => $operatingOutflows,
                'net' => $operatingInflows - $operatingOutflows
            ],
            
            'investing_activities' => [
                'inflows' => $investingInflows,
                'outflows' => $investingOutflows,
                'net' => $investingInflows - $investingOutflows
            ],
            
            'financing_activities' => [
                'inflows' => $financingInflows,
                'outflows' => $financingOutflows,
                'net' => $financingInflows - $financingOutflows
            ],
            
            'summary' => [
                'net_cashflow' => ($operatingInflows - $operatingOutflows) + 
                                ($investingInflows - $investingOutflows) + 
                                ($financingInflows - $financingOutflows),
                'closing_balance' => $closingBalance
            ]
        ];
    }

    /**
     * Get cash position trend for dashboard
     */
    public static function getCashPositionTrend($days = 30): array
    {
        $endDate = now();
        $startDate = $endDate->copy()->subDays($days);
        
        $transactions = CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->cleared()
            ->orderBy('transaction_date')
            ->get()
            ->groupBy(function($item) {
                return $item->transaction_date->format('Y-m-d');
            });

        $trend = [];
        $runningBalance = self::getBalanceAsOf($startDate->copy()->subDay());
        
        foreach ($transactions as $date => $dayTransactions) {
            $dailyInflow = $dayTransactions->where('transaction_type', 'INFLOW')->sum('amount');
            $dailyOutflow = $dayTransactions->where('transaction_type', 'OUTFLOW')->sum('amount');
            
            $runningBalance += $dailyInflow - $dailyOutflow;
            
            $trend[] = [
                'date' => $date,
                'inflow' => $dailyInflow,
                'outflow' => $dailyOutflow,
                'balance' => $runningBalance
            ];
        }
        
        return $trend;
    }

    /**
     * Get category-wise cash flow for a period
     */
    public static function getCategoryWiseCashflow($startDate, $endDate): array
    {
        $transactions = CashflowTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->cleared()
            ->get()
            ->groupBy('category');

        $categories = [
            'operating' => ['inflows' => 0, 'outflows' => 0],
            'investing' => ['inflows' => 0, 'outflows' => 0],
            'financing' => ['inflows' => 0, 'outflows' => 0]
        ];

        foreach ($transactions as $category => $categoryTransactions) {
            $inflows = $categoryTransactions->where('transaction_type', 'INFLOW')->sum('amount');
            $outflows = $categoryTransactions->where('transaction_type', 'OUTFLOW')->sum('amount');
            
            if (isset($categories[strtolower($category)])) {
                $categories[strtolower($category)] = [
                    'inflows' => $inflows,
                    'outflows' => $outflows
                ];
            }
        }

        return $categories;
    }
}
