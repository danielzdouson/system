<?php

namespace App\Services;

use App\Models\CashflowTransaction;
use App\Models\FiscalYear;
use Carbon\Carbon;

class CashflowStatementService
{
    /**
     * Generate monthly cashflow statement
     */
    public function generateMonthlyStatement($fiscalYearId, $month): array
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        if (!$fiscalYear) {
            throw new \Exception('Fiscal year not found');
        }

        $monthData = CashPositionService::getMonthlyCashflow(
            $fiscalYear->start_date->year, 
            $month
        );
        
        // Operating Activities
        $operatingInflows = $this->getOperatingInflows($fiscalYearId, $month);
        $operatingOutflows = $this->getOperatingOutflows($fiscalYearId, $month);
        
        // Investing Activities
        $investingInflows = $this->getInvestingInflows($fiscalYearId, $month);
        $investingOutflows = $this->getInvestingOutflows($fiscalYearId, $month);
        
        // Financing Activities
        $financingInflows = $this->getFinancingInflows($fiscalYearId, $month);
        $financingOutflows = $this->getFinancingOutflows($fiscalYearId, $month);
        
        return [
            'fiscal_year' => $fiscalYear->name,
            'month' => $month,
            'period' => Carbon::create($fiscalYear->start_date->year, $month)->format('F Y'),
            
            'operating_activities' => [
                'inflows' => $operatingInflows,
                'outflows' => $operatingOutflows,
                'net' => $operatingInflows - $operatingOutflows,
                'details' => $this->getOperatingDetails($fiscalYearId, $month)
            ],
            
            'investing_activities' => [
                'inflows' => $investingInflows,
                'outflows' => $investingOutflows,
                'net' => $investingInflows - $investingOutflows,
                'details' => $this->getInvestingDetails($fiscalYearId, $month)
            ],
            
            'financing_activities' => [
                'inflows' => $financingInflows,
                'outflows' => $financingOutflows,
                'net' => $financingInflows - $financingOutflows,
                'details' => $this->getFinancingDetails($fiscalYearId, $month)
            ],
            
            'summary' => [
                'opening_balance' => $monthData['opening_balance'],
                'net_cashflow' => $monthData['net_cashflow'],
                'closing_balance' => $monthData['closing_balance']
            ]
        ];
    }

    /**
     * Generate fiscal year cashflow statement
     */
    public function generateFiscalYearStatement($fiscalYearId): array
    {
        return CashPositionService::getFiscalYearCashflow($fiscalYearId);
    }

    /**
     * Get operating activities inflows
     */
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

    /**
     * Get operating activities outflows
     */
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

    /**
     * Get investing activities inflows
     */
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

    /**
     * Get investing activities outflows
     */
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

    /**
     * Get financing activities inflows
     */
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

    /**
     * Get financing activities outflows
     */
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

    /**
     * Get operating activities details
     */
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

    /**
     * Get investing activities details
     */
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

    /**
     * Get financing activities details
     */
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
}
