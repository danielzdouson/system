<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\Loan;
use App\Models\GroupSaving;
use App\Models\Deposit;
use App\Models\LoanRequest;
use App\Models\Fine;
use App\Models\FiscalYear;
use App\Services\FiscalYearContext;

class DashboardController
{
    public function index()
    {
        $currentFiscalYear = FiscalYearContext::getCurrent();
        $allFiscalYears = FiscalYearContext::getAllForSelector();
        
        // Get dashboard statistics
        $stats = [
            'total_members' => Member::count(),
            'total_savings' => $this->getTotalSavings($currentFiscalYear),
            'active_loans' => $this->getActiveLoans($currentFiscalYear),
            'pending_loan_requests' => $this->getPendingLoanRequests($currentFiscalYear),
            'total_loans_amount' => $this->getTotalLoansAmount($currentFiscalYear),
            'total_fines' => $this->getTotalFines($currentFiscalYear),
            'monthly_deposits' => $this->getMonthlyDeposits($currentFiscalYear),
            'recent_activities' => $this->getRecentActivities($currentFiscalYear),
        ];

        return view('dashboard', compact('stats', 'currentFiscalYear', 'allFiscalYears'));
    }

    private function getTotalSavings($currentFiscalYear)
    {
        if (!$currentFiscalYear) return 0;
        
        // Use MemberAccount savings_balance as the single source of truth
        // This represents the actual current savings for each member in this fiscal year
        return \App\Models\MemberAccount::where('fiscal_year_id', $currentFiscalYear->id)
            ->sum('savings_balance');
    }

    private function getActiveLoans($currentFiscalYear)
    {
        if (!$currentFiscalYear) return 0;
        
        return Loan::where('fiscal_year_id', $currentFiscalYear->id)
            ->where('status', 'active')
            ->count();
    }

    private function getPendingLoanRequests($currentFiscalYear)
    {
        if (!$currentFiscalYear) return 0;
        
        return LoanRequest::where('fiscal_year_id', $currentFiscalYear->id)
            ->where('status', 'pending')
            ->count();
    }

    private function getTotalLoansAmount($currentFiscalYear)
    {
        if (!$currentFiscalYear) return 0;
        
        return Loan::where('fiscal_year_id', $currentFiscalYear->id)
            ->where('status', 'active')
            ->sum('principal_amount');
    }

    private function getTotalFines($currentFiscalYear)
    {
        if (!$currentFiscalYear) return 0;
        
        return Fine::where('fiscal_year_id', $currentFiscalYear->id)
            ->where('status', 'pending')
            ->sum('amount');
    }

    private function getMonthlyDeposits($currentFiscalYear)
    {
        if (!$currentFiscalYear) return collect([]);
        
        return Deposit::where('fiscal_year_id', $currentFiscalYear->id)
            ->with(['member'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    private function getRecentActivities($currentFiscalYear)
    {
        if (!$currentFiscalYear) return collect([]);
        
        $activities = collect();
        
        // Recent loans
        $recentLoans = Loan::where('fiscal_year_id', $currentFiscalYear->id)
            ->with(['member'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($loan) {
                $memberName = 'Unknown Member';
                if ($loan->member) {
                    $memberName = ($loan->member->first_name ?? '') . ' ' . ($loan->member->last_name ?? '');
                    $memberName = trim($memberName) ?: 'Unknown Member';
                }
                return [
                    'type' => 'loan',
                    'description' => 'Loan issued to ' . $memberName,
                    'amount' => $loan->principal_amount,
                    'date' => $loan->created_at,
                    'icon' => '💳'
                ];
            });
        
        // Recent deposits
        $recentDeposits = Deposit::where('fiscal_year_id', $currentFiscalYear->id)
            ->with(['member'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($deposit) {
                $memberName = 'Unknown Member';
                if ($deposit->member) {
                    $memberName = ($deposit->member->first_name ?? '') . ' ' . ($deposit->member->last_name ?? '');
                    $memberName = trim($memberName) ?: 'Unknown Member';
                }
                return [
                    'type' => 'deposit',
                    'description' => 'Deposit from ' . $memberName,
                    'amount' => $deposit->amount,
                    'date' => $deposit->created_at,
                    'icon' => '💰'
                ];
            });
        
        $activities = $activities->merge($recentLoans)->merge($recentDeposits)
            ->sortByDesc('date')
            ->take(5);
        
        return $activities;
    }
}
