<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberAccount;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\FiscalYear;

class MemberDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('member');
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
        $activeLoans = Loan::where('member_id', $member->id)
            ->where('loan_status', 'active')
            ->with(['repaymentSchedules' => function($query) {
                $query->where('status', 'pending')->orderBy('due_date');
            }])
            ->get();

        // Get recent transactions
        $recentTransactions = Transaction::where('member_id', $member->id)
            ->with(['cashflowTransaction'])
            ->latest()
            ->take(10)
            ->get();

        // Calculate totals
        $totalSavings = $memberAccount ? $memberAccount->savings_balance : 0;
        $totalLoans = $activeLoans->sum('balance');
        $availableCredit = 1000000 - $totalLoans; // Example credit limit
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
            'availableCredit',
            'nextPayment',
            'currentFiscalYear'
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

        $transactions = Transaction::where('member_id', $member->id)
            ->with(['cashflowTransaction'])
            ->when($request->date_from, function($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->when($request->type, function($query) use ($request) {
                $query->where('transaction_type', $request->type);
            })
            ->latest()
            ->paginate(20);

        return view('member.transactions', compact('transactions'));
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

        $activeLoans = Loan::where('member_id', $member->id)
            ->where('loan_status', 'active')
            ->with(['repaymentSchedules' => function($query) {
                $query->orderBy('due_date');
            }])
            ->get();

        $completedLoans = Loan::where('member_id', $member->id)
            ->where('loan_status', 'completed')
            ->latest()
            ->get();

        return view('member.loans', compact('activeLoans', 'completedLoans'));
    }
}
