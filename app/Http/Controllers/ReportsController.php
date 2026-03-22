<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Loan;
use App\Models\GroupSaving;
use App\Models\Deposit;
use App\Models\Fine;
use App\Models\FiscalYear;
use App\Models\CashFlow;
use App\Services\FiscalYearContext;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        $currentFiscalYear = FiscalYearContext::getCurrent();
        $allFiscalYears = FiscalYearContext::getAllForSelector();
        
        // Key Financial Metrics
        $metrics = $this->calculateFinancialMetrics($currentFiscalYear);
        
        // Portfolio Performance
        $portfolioData = $this->getPortfolioPerformance($currentFiscalYear);
        
        // Risk Assessment
        $riskMetrics = $this->calculateRiskMetrics($currentFiscalYear);
        
        // Monthly Trends
        $monthlyTrends = $this->getMonthlyTrends($currentFiscalYear);
        
        // Top Performers
        $topPerformers = $this->getTopPerformers($currentFiscalYear);
        
        return view('admin.reports.index', compact(
            'metrics', 
            'portfolioData', 
            'riskMetrics', 
            'monthlyTrends', 
            'topPerformers',
            'currentFiscalYear',
            'allFiscalYears'
        ));
    }

    private function calculateFinancialMetrics($activeFiscalYear)
    {
        $query = function($model) use ($activeFiscalYear) {
            return $model->when($activeFiscalYear, function($q) use ($activeFiscalYear) {
                $q->where('fiscal_year_id', $activeFiscalYear->id);
            });
        };

        $totalMembers = Member::count();
        $activeMembers = Member::whereHas('loans')->orWhereHas('deposits')->count();
        
        $totalDeposits = $query(new Deposit())->sum('amount');
        $totalLoansDisbursed = $query(new Loan())->sum('principal_amount');
        $totalLoanRepayments = $query(new Loan())->sum('paid_amount');
        $outstandingLoanBalance = $query(new Loan())->sum('balance');
        
        $totalFines = $query(new Fine())->sum('amount');
        $paidFines = $query(new Fine())->where('status', 'paid')->sum('amount');
        
        $cashflowIncome = $this->getCashflowIncome($activeFiscalYear);
        $cashflowExpenses = $this->getCashflowExpenses($activeFiscalYear);
        
        $loanPortfolioQuality = $totalLoansDisbursed > 0 ? 
            (($totalLoansDisbursed - $outstandingLoanBalance) / $totalLoansDisbursed) * 100 : 0;
        
        $delinquencyRate = $totalLoansDisbursed > 0 ? 
            ($outstandingLoanBalance / $totalLoansDisbursed) * 100 : 0;

        return [
            'total_members' => $totalMembers,
            'active_members' => $activeMembers,
            'member_participation_rate' => $totalMembers > 0 ? ($activeMembers / $totalMembers) * 100 : 0,
            'total_deposits' => $totalDeposits,
            'total_loans_disbursed' => $totalLoansDisbursed,
            'total_loan_repayments' => $totalLoanRepayments,
            'outstanding_loan_balance' => $outstandingLoanBalance,
            'total_fines' => $totalFines,
            'paid_fines' => $paidFines,
            'cashflow_income' => $cashflowIncome,
            'cashflow_expenses' => $cashflowExpenses,
            'net_cashflow' => $cashflowIncome - $cashflowExpenses,
            'loan_portfolio_quality' => round($loanPortfolioQuality, 2),
            'delinquency_rate' => round($delinquencyRate, 2),
            'liquidity_ratio' => $totalLoansDisbursed > 0 ? ($totalDeposits / $totalLoansDisbursed) * 100 : 0,
        ];
    }

    private function getPortfolioPerformance($activeFiscalYear)
    {
        $loanQuery = Loan::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        });

        $totalLoans = $loanQuery->count();
        $performingLoans = $loanQuery->where('balance', '<=', 0)->count();
        $nonPerformingLoans = $totalLoans - $performingLoans;
        
        $loanAmounts = $loanQuery->get();
        $totalLoanAmount = $loanAmounts->sum('principal_amount');
        $totalRepaid = $loanAmounts->sum('paid_amount');
        
        $averageLoanSize = $totalLoans > 0 ? $totalLoanAmount / $totalLoans : 0;
        $repaymentRate = $totalLoanAmount > 0 ? ($totalRepaid / $totalLoanAmount) * 100 : 0;

        return [
            'total_loans' => $totalLoans,
            'performing_loans' => $performingLoans,
            'non_performing_loans' => $nonPerformingLoans,
            'portfolio_at_risk' => $totalLoans > 0 ? ($nonPerformingLoans / $totalLoans) * 100 : 0,
            'average_loan_size' => $averageLoanSize,
            'repayment_rate' => round($repaymentRate, 2),
        ];
    }

    private function calculateRiskMetrics($activeFiscalYear)
    {
        $loanQuery = Loan::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        });

        $overdueLoans = $loanQuery->where('balance', '>', 0)
            ->where('maturity_date', '<', now())
            ->count();
        
        $totalLoans = $loanQuery->count();
        $highRiskLoans = $loanQuery->where('balance', '>', 0)
            ->where('balance', '>', function($query) {
                $query->selectRaw('principal_amount * 0.5');
            })->count();

        $depositQuery = Deposit::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        });

        $totalDeposits = $depositQuery->sum('amount');
        $concentrationRisk = $this->calculateConcentrationRisk($activeFiscalYear);

        return [
            'overdue_loans' => $overdueLoans,
            'overdue_rate' => $totalLoans > 0 ? ($overdueLoans / $totalLoans) * 100 : 0,
            'high_risk_loans' => $highRiskLoans,
            'high_risk_rate' => $totalLoans > 0 ? ($highRiskLoans / $totalLoans) * 100 : 0,
            'concentration_risk' => $concentrationRisk,
            'capital_adequacy' => $totalDeposits > 0 ? (($totalDeposits - $loanQuery->sum('balance')) / $totalDeposits) * 100 : 0,
        ];
    }

    private function calculateConcentrationRisk($activeFiscalYear)
    {
        $depositQuery = Deposit::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        });

        $totalDeposits = $depositQuery->sum('amount');
        if ($totalDeposits == 0) return 0;

        $largestDeposits = $depositQuery->selectRaw('member_id, SUM(amount) as total')
            ->groupBy('member_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $top5Concentration = ($largestDeposits->sum('total') / $totalDeposits) * 100;
        
        return round($top5Concentration, 2);
    }

    private function getMonthlyTrends($activeFiscalYear)
    {
        $depositQuery = Deposit::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        });

        $loanQuery = Loan::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        });

        $monthlyDeposits = $depositQuery->selectRaw('month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyLoans = $loanQuery->selectRaw('MONTH(disbursement_date) as month, SUM(principal_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'deposits' => $monthlyDeposits,
            'loans' => $monthlyLoans,
        ];
    }

    private function getTopPerformers($activeFiscalYear)
    {
        $depositQuery = Deposit::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        });

        $topSavers = $depositQuery->selectRaw('member_id, SUM(amount) as total_deposits')
            ->with('member:first_name,last_name')
            ->groupBy('member_id')
            ->orderByDesc('total_deposits')
            ->limit(5)
            ->get();

        $topBorrowers = Loan::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        })
        ->selectRaw('member_id, SUM(principal_amount) as total_loans, SUM(paid_amount) as total_repaid')
        ->with('member:first_name,last_name')
        ->groupBy('member_id')
        ->orderByDesc('total_repaid')
        ->limit(5)
        ->get();

        return [
            'top_savers' => $topSavers,
            'top_borrowers' => $topBorrowers,
        ];
    }

    private function getCashflowIncome($activeFiscalYear)
    {
        $query = CashFlow::where('type', 'income');
        
        if ($activeFiscalYear) {
            $query->whereBetween('transaction_date', [
                $activeFiscalYear->start_date,
                $activeFiscalYear->end_date
            ]);
        }
        
        return $query->sum('amount');
    }

    private function getCashflowExpenses($activeFiscalYear)
    {
        $query = CashFlow::where('type', 'expense');
        
        if ($activeFiscalYear) {
            $query->whereBetween('transaction_date', [
                $activeFiscalYear->start_date,
                $activeFiscalYear->end_date
            ]);
        }
        
        return $query->sum('amount');
    }

    public function memberReports()
    {
        $activeFiscalYear = FiscalYear::getActive();
        $members = Member::with(['accounts', 'loans', 'deposits'])
            ->when($activeFiscalYear, function($query) use ($activeFiscalYear) {
                $query->with(['accounts' => function($q) use ($activeFiscalYear) {
                    $q->where('fiscal_year_id', $activeFiscalYear->id);
                }]);
            })
            ->get();

        return view('admin.reports.members', compact('members', 'activeFiscalYear'));
    }

    public function savingsReports()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        $savings = GroupSaving::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        })->with(['member'])->get();

        $deposits = Deposit::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        })->with(['member', 'distributions'])->get();

        return view('admin.reports.savings', compact('savings', 'deposits', 'activeFiscalYear'));
    }

    public function loanReports()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        $loans = Loan::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        })->with(['member', 'payments'])->get();

        return view('admin.reports.loans', compact('loans', 'activeFiscalYear'));
    }

    public function cashflowReports()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        $cashflows = CashFlow::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        })->with(['member'])->get();

        return view('admin.reports.cashflow', compact('cashflows', 'activeFiscalYear'));
    }
}
