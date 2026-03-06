<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberAccount;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\FiscalYear;
use App\Models\Fine;
use App\Models\Investment;
use App\Models\Deposit;
use App\Models\GroupSaving;
use App\Models\Distribution;
use App\Services\MemberFinancialSummaryService;

class MemberDashboardController extends Controller
{
    protected $memberFinancialService;

    public function __construct(MemberFinancialSummaryService $memberFinancialService)
    {
        $this->middleware('auth');
        $this->middleware('member');
        $this->memberFinancialService = $memberFinancialService;
    }

    /**
     * Display member dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        // Get current fiscal year
        $currentFiscalYear = FiscalYear::where('status', 'active')->first();
        
        // Get member account for current fiscal year
        $memberAccount = null;
        if ($currentFiscalYear) {
            $memberAccount = MemberAccount::where('member_id', $member->id)
                ->where('fiscal_year_id', $currentFiscalYear->id)
                ->first();
        }

        // Get active loans
        $activeLoans = Loan::with(['member', 'repaymentSchedules'])
            ->where('member_id', $member->id)
            ->where(function($query) {
                $query->where('status', 'active')
                      ->orWhere('status', 'completed');
            })
            ->whereHas('member')
            ->latest()
            ->get();

        // Get recent transactions
        $recentTransactions = Transaction::where('member_id', $member->id)
            ->latest()
            ->take(10)
            ->get();

        // Get comprehensive financial data using the same service as admin
        $memberFinancialData = $this->memberFinancialService->getMemberFinancialSummary($member);
        
        // Extract data from service
        $totalDeposits = $memberFinancialData['total_deposits'];
        $totalSavings = $memberFinancialData['total_savings'];
        $welfare = $memberFinancialData['welfare'];
        $outstandingFines = $memberFinancialData['outstanding_fines'];
        $loanBalance = $memberFinancialData['loan_balance'];
        $availableBalance = $memberFinancialData['available_balance'];
        $distributedFunds = $memberFinancialData['distributed_funds'];
        $totalShares = $memberFinancialData['total_shares']; // This is now percentage
        $sharesOnHold = $memberFinancialData['shares_on_hold'];
        $netWorth = $memberFinancialData['net_worth'];
        
        // Calculate total contributions (all deposits for member) - same as admin
        $totalContributions = $totalDeposits;
        
        // Get member fines using same logic as admin dashboard
        $totalFines = $outstandingFines;
        $paidFines = Fine::where('member_id', $member->id)
            ->where('status', 'paid')
            ->sum('amount') ?? 0;

        // Get group investments and their inflows with better error handling
        try {
            $groupInvestments = Investment::where('created_by', $member->id)
                ->with(['transactions' => function($query) {
                    $query->inflow();
                }])
                ->get() ?? collect();
            
            $totalInvestmentInflows = $groupInvestments->sum(function($investment) {
                return $investment->transactions->sum('amount') ?? 0;
            });

            $totalInvestmentPrincipal = $groupInvestments->sum('principal_amount') ?? 0;
            $totalInvestmentReturns = $groupInvestments->sum('total_returns') ?? 0;
            
            // Calculate ROI
            $roi = 0;
            if ($totalInvestmentPrincipal > 0) {
                $roi = (($totalInvestmentReturns - $totalInvestmentPrincipal) / $totalInvestmentPrincipal) * 100;
            }
        } catch (\Exception $e) {
            $groupInvestments = collect();
            $totalInvestmentInflows = 0;
            $totalInvestmentPrincipal = 0;
            $totalInvestmentReturns = 0;
            $roi = 0;
        }

        // Calculate monthly savings growth based on deposits
        $monthlySavingsGrowth = 0;
        $lastMonthDeposits = Deposit::where('member_id', $member->id)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');
        
        $thisMonthDeposits = Deposit::where('member_id', $member->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        
        if ($lastMonthDeposits > 0) {
            $monthlySavingsGrowth = (($thisMonthDeposits - $lastMonthDeposits) / $lastMonthDeposits) * 100;
        }

        // Get next payment
        $totalLoans = $loanBalance;
        $nextPayment = null;
        if ($activeLoans->isNotEmpty()) {
            $nextPaymentSchedule = $activeLoans->first()->repaymentSchedules->first();
            if ($nextPaymentSchedule) {
                $nextPayment = [
                    'amount' => $nextPaymentSchedule->amount,
                    'due_date' => $nextPaymentSchedule->due_date,
                ];
            }
        }

        return view('member.dashboard', compact(
            'member',
            'memberAccount',
            'activeLoans',
            'recentTransactions',
            'totalSavings',
            'totalLoans',
            'nextPayment',
            'currentFiscalYear',
            'monthlySavingsGrowth',
            'totalContributions',
            'totalShares',
            'sharesOnHold',
            'totalFines',
            'paidFines',
            'totalDeposits',
            'groupInvestments',
            'totalInvestmentInflows',
            'totalInvestmentPrincipal',
            'totalInvestmentReturns',
            'roi',
            'welfare',
            'distributedFunds',
            'availableBalance',
            'netWorth'
        ));
    }

    /**
     * Show transaction history.
     */
    public function transactions(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        // Get regular transactions
        $transactions = Transaction::where('member_id', $member->id)
            ->when($request->date_from, function($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->when($request->type, function($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->latest()
            ->paginate(20);

        // Get member's distributions through their deposits
        $distributions = Distribution::whereHas('deposit', function($query) use ($member) {
            $query->where('member_id', $member->id);
        })
        ->with(['deposit', 'creator'])
        ->when($request->date_from, function($query) use ($request) {
            $query->whereDate('created_at', '>=', $request->date_from);
        })
        ->when($request->date_to, function($query) use ($request) {
            $query->whereDate('created_at', '<=', $request->date_to);
        })
        ->when($request->type, function($query) use ($request) {
            if ($request->type === 'distribution') {
                // Only show distributions when this type is selected
            } else {
                // If filtering by other types, exclude distributions
                $query->whereRaw('1=0'); // This will exclude all distributions
            }
        })
        ->latest()
        ->paginate(20);

        // Calculate distribution statistics
        $totalDistributed = Distribution::whereHas('deposit', function($query) use ($member) {
            $query->where('member_id', $member->id);
        })->sum('amount');

        $distributionCount = Distribution::whereHas('deposit', function($query) use ($member) {
            $query->where('member_id', $member->id);
        })->count();

        // Calculate comprehensive transaction statistics
        $totalDeposits = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');
        
        // Get available balance from member account (same as admin financials)
        $currentAccount = \App\Models\MemberAccount::where('member_id', $member->id)
            ->orderBy('fiscal_year_id', 'desc')
            ->first();
        $availableBalance = $currentAccount ? $currentAccount->current_balance : 0;
        
        // Use available balance as net balance (matches admin financials)
        $netBalance = $availableBalance;

        return view('member.transactions', compact(
            'transactions', 
            'distributions', 
            'totalDistributed', 
            'distributionCount',
            'totalDeposits',
            'totalWithdrawals', 
            'netBalance'
        ));
    }

    /**
     * Show loans page.
     */
    public function loans()
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        $activeLoans = Loan::with(['member', 'repaymentSchedules'])
            ->where('member_id', $member->id)
            ->where(function($query) {
                $query->where('status', 'active')
                      ->orWhere('status', 'completed');
            })
            ->whereHas('member')
            ->latest()
            ->get();

        $completedLoans = Loan::with(['member'])
            ->where('member_id', $member->id)
            ->where('status', 'completed')
            ->whereHas('member')
            ->latest()
            ->get();

        return view('member.loans', compact('activeLoans', 'completedLoans'));
    }

    /**
     * Download member statement.
     */
    public function downloadStatement(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        // Validate request
        $request->validate([
            'type' => 'required|in:full,savings,loans',
            'format' => 'required|in:pdf,excel',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        // For now, return a simple response
        // In a real implementation, you would generate and return the actual file
        return redirect()->back()->with('success', 'Statement download functionality will be implemented soon.');
    }
}
