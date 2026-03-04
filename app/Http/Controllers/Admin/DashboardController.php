<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\Loan;
use App\Models\GroupSaving;
use App\Models\Deposit;
use App\Models\LoanRequest;
use App\Models\Fine;
use App\Models\FiscalYear;

class DashboardController
{
    public function index()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        // Get dashboard statistics
        $stats = [
            'total_members' => Member::count(),
            'total_savings' => $this->getTotalSavings($activeFiscalYear),
            'active_loans' => $this->getActiveLoans($activeFiscalYear),
            'pending_loan_requests' => $this->getPendingLoanRequests($activeFiscalYear),
            'total_loans_amount' => $this->getTotalLoansAmount($activeFiscalYear),
            'total_fines' => $this->getTotalFines($activeFiscalYear),
            'monthly_deposits' => $this->getMonthlyDeposits($activeFiscalYear),
            'recent_activities' => $this->getRecentActivities($activeFiscalYear),
        ];

        return view('dashboard', compact('stats', 'activeFiscalYear'));
    }

    private function getTotalSavings($activeFiscalYear)
    {
        if (!$activeFiscalYear) return 0;
        
        return GroupSaving::where('fiscal_year_id', $activeFiscalYear->id)
            ->sum('amount') + 
            Deposit::where('fiscal_year_id', $activeFiscalYear->id)
                ->whereHas('distributions', function($query) {
                    $query->where('type', 'savings');
                })
                ->with('distributions')
                ->get()
                ->sum(function($deposit) {
                    return $deposit->distributions->where('type', 'savings')->sum('amount');
                });
    }

    private function getActiveLoans($activeFiscalYear)
    {
        if (!$activeFiscalYear) return 0;
        
        return Loan::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('status', 'active')
            ->count();
    }

    private function getPendingLoanRequests($activeFiscalYear)
    {
        if (!$activeFiscalYear) return 0;
        
        return LoanRequest::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('status', 'pending')
            ->count();
    }

    private function getTotalLoansAmount($activeFiscalYear)
    {
        if (!$activeFiscalYear) return 0;
        
        return Loan::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('status', 'active')
            ->sum('principal_amount');
    }

    private function getTotalFines($activeFiscalYear)
    {
        if (!$activeFiscalYear) return 0;
        
        return Fine::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('status', 'pending')
            ->sum('amount');
    }

    private function getMonthlyDeposits($activeFiscalYear)
    {
        if (!$activeFiscalYear) return collect([]);
        
        return Deposit::where('fiscal_year_id', $activeFiscalYear->id)
            ->with(['member'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    private function getRecentActivities($activeFiscalYear)
    {
        if (!$activeFiscalYear) return collect([]);
        
        $activities = collect();
        
        // Recent loans
        $recentLoans = Loan::where('fiscal_year_id', $activeFiscalYear->id)
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
        $recentDeposits = Deposit::where('fiscal_year_id', $activeFiscalYear->id)
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
