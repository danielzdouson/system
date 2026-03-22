<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\LoanRepayment;
use App\Models\RepaymentSchedule;
use App\Models\LoanPenalty;
use App\Models\FiscalYear;
use App\Services\FiscalYearContext;
use Illuminate\Http\Request;

class GroupLoanController extends Controller
{
    /**
     * Display loan dashboard
     */
    public function index()
    {
        $currentFiscalYear = FiscalYearContext::getCurrent();
        $allFiscalYears = FiscalYearContext::getAllForSelector();
        
        // Get loan statistics - filter by current fiscal year if set
        $query = Loan::query();
        if ($currentFiscalYear) {
            $query->where('fiscal_year_id', $currentFiscalYear->id);
        }
        
        $totalLoans = $query->count();
        $activeLoans = (clone $query)->where('status', 'active')->count();
        $completedLoans = (clone $query)->where('status', 'completed')->count();
        $defaultedLoans = (clone $query)->where('status', 'defaulted')->count();

        // Get financial summary - filtered by fiscal year
        $totalDisbursed = (clone $query)->sum('principal_amount');
        $totalRepaid = (clone $query)->sum('paid_amount');
        $totalOutstanding = $totalDisbursed - $totalRepaid;

        // Get pending requests - filtered by fiscal year
        $pendingRequestsQuery = LoanRequest::where('status', 'pending');
        if ($currentFiscalYear) {
            $pendingRequestsQuery->where('fiscal_year_id', $currentFiscalYear->id);
        }
        $pendingRequests = $pendingRequestsQuery->count();

        // Get recent loans - filtered by fiscal year
        $recentLoansQuery = Loan::with('member')->latest();
        if ($currentFiscalYear) {
            $recentLoansQuery->where('fiscal_year_id', $currentFiscalYear->id);
        }
        $recentLoans = $recentLoansQuery->take(5)->get();

        // Get overdue loans - filtered by fiscal year
        $overdueLoansQuery = Loan::where('status', 'active')
            ->whereHas('repaymentSchedules', function($query) {
                $query->where('due_date', '<', now())
                    ->where('status', 'pending');
            });
        if ($currentFiscalYear) {
            $overdueLoansQuery->where('fiscal_year_id', $currentFiscalYear->id);
        }
        $overdueLoans = $overdueLoansQuery->count();

        // Get members for dropdowns
        $members = Member::orderBy('first_name')->get();
        
        // Get loans for table - filtered by fiscal year
        $loansQuery = Loan::with('member')->whereHas('member');
        if ($currentFiscalYear) {
            $loansQuery->where('fiscal_year_id', $currentFiscalYear->id);
        }
        $loans = $loansQuery->latest()->paginate(10);

        return view('admin.group-loans.index', compact(
            'totalLoans',
            'activeLoans',
            'completedLoans',
            'defaultedLoans',
            'totalDisbursed',
            'totalRepaid',
            'totalOutstanding',
            'pendingRequests',
            'recentLoans',
            'overdueLoans',
            'members',
            'loans',
            'currentFiscalYear',
            'allFiscalYears'
        ));
    }

    /**
     * Display loan requests
     */
    public function requests()
    {
        $currentFiscalYear = FiscalYearContext::getCurrent();
        $allFiscalYears = FiscalYearContext::getAllForSelector();
        
        $loanRequestsQuery = LoanRequest::with('member', 'approvedBy')->latest();
        if ($currentFiscalYear) {
            $loanRequestsQuery->where('fiscal_year_id', $currentFiscalYear->id);
        }
        $loanRequests = $loanRequestsQuery->paginate(10);

        // Get all members for new loan requests
        $members = Member::orderBy('first_name')->get();

        return view('admin.group-loans.requests', compact('loanRequests', 'currentFiscalYear', 'allFiscalYears', 'members'));
    }

    /**
     * Store new loan request
     */
    public function storeRequest(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'principal_amount' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:1|max:30',
            'loan_term_months' => 'required|integer|min:1|max:60',
            'purpose' => 'required|string|max:255',
        ]);

        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        // Calculate monthly payment for loan request using simple interest
        // Simple Interest Formula: Interest = Principal × Rate / 100
        $totalInterest = ($request->principal_amount * $request->interest_rate) / 100;
        $totalRepayment = $request->principal_amount + $totalInterest;
        $monthlyPayment = $totalRepayment / $request->loan_term_months;
        
        $loanRequest = LoanRequest::create([
            'member_id' => $request->member_id,
            'principal_amount' => $request->principal_amount,
            'interest_rate' => $request->interest_rate,
            'loan_term_months' => $request->loan_term_months,
            'purpose' => $request->purpose,
            'amount' => $monthlyPayment, // Simple amount field for compatibility
                'outstanding_balance' => $monthlyPayment, // Balance after this installment
            'fiscal_year_id' => $currentFiscalYear->id,
            'requested_date' => now(),
        ]);

        return back()->with('success', 'Loan request created successfully!');
    }

    /**
     * Create direct loan (without approval process)
     */
    public function createDirectLoan(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'principal_amount' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:1|max:30',
            'loan_term_months' => 'required|integer|min:1|max:60',
            'purpose' => 'required|string|max:255',
        ]);

        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        // Cast values to proper types
        $principalAmount = (float) $request->principal_amount;
        $interestRate = (float) $request->interest_rate;
        $loanTermMonths = (int) $request->loan_term_months;
        
        // Calculate loan details using simple interest
        // Simple Interest Formula: Interest = Principal × Rate / 100
        $totalInterest = ($principalAmount * $interestRate) / 100;
        $totalRepayment = $principalAmount + $totalInterest;
        $monthlyPayment = $totalRepayment / $loanTermMonths;
        
        // Generate unique loan number
        $loanNumber = 'LN-' . date('Y') . '-' . str_pad(Loan::count() + 1, 4, '0', STR_PAD_LEFT);
        
        // Create loan directly using original database fields
        $loan = Loan::create([
            'member_id' => $request->member_id,
            'fiscal_year_id' => $currentFiscalYear->id,
            'loan_number' => $loanNumber,
            'principal_amount' => $principalAmount,
            'interest_rate' => $interestRate,
            'duration_months' => $loanTermMonths,
            'disbursement_date' => now(),
            'disbursed_by' => auth()->id(),
            'first_payment_date' => now()->addMonths(2),
            'maturity_date' => now()->addMonths($loanTermMonths),
            'monthly_installment' => $monthlyPayment,
            'total_interest' => $totalInterest,
            'total_repayable' => $totalRepayment,
            'balance' => $totalRepayment,
            'paid_amount' => 0,
        ]);

        // Create repayment schedules
        for ($i = 1; $i <= $loanTermMonths; $i++) {
            RepaymentSchedule::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => now()->addMonths($i + 1), // Start payments after 2 months
                'principal_due' => $principalAmount / $loanTermMonths, // Principal portion
                'interest_due' => ($totalInterest / $loanTermMonths), // Interest portion
                'total_due' => $monthlyPayment, // Total payment
                'principal_paid' => 0, // Initially unpaid
                'interest_paid' => 0, // Initially unpaid
                'penalty_charged' => 0, // Initially no penalty
                'penalty_paid' => 0, // Initially no penalty paid
                'amount' => $monthlyPayment, // Simple amount field for compatibility
                'outstanding_balance' => $monthlyPayment, // Balance after this installment
                'fiscal_year_id' => $currentFiscalYear->id,
            ]);
        }

        return back()->with('success', 'Loan created and disbursed successfully!');
    }

    /**
     * Record loan payment
     */
    public function recordPayment(Request $request, $loanId, $scheduleId)
    {
        $request->validate([
            'principal_paid' => 'required|numeric|min:0',
            'interest_paid' => 'required|numeric|min:0',
            'penalty_paid' => 'numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $loan = Loan::findOrFail($loanId);
        $schedule = RepaymentSchedule::findOrFail($scheduleId);

        // Ensure schedule belongs to the loan
        if ($schedule->loan_id !== $loan->id) {
            return back()->with('error', 'Invalid repayment schedule.');
        }

        // Update repayment schedule
        $schedule->update([
            'principal_paid' => $request->principal_paid,
            'interest_paid' => $request->interest_paid,
            'penalty_paid' => $request->penalty_paid ?? 0,
            'status' => 'paid',
            'paid_date' => $request->payment_date,
        ]);

        // Calculate total payment with proper type casting
        $principalPaid = (float) $request->principal_paid;
        $interestPaid = (float) $request->interest_paid;
        $penaltyPaid = (float) ($request->penalty_paid ?? 0);
        $totalPaid = $principalPaid + $interestPaid + $penaltyPaid;
        
        // Debug: Ensure totalPaid is not null or zero
        if ($totalPaid <= 0) {
            return back()->with('error', 'Payment amount must be greater than 0.');
        }
        
        // Update loan balance
        $loan->update([
            'balance' => max(0, $loan->balance - $totalPaid),
            'paid_amount' => $loan->paid_amount + $totalPaid,
        ]);

        // Check if loan is fully paid
        if ($loan->balance <= 0) {
            $loan->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        // Create payment record using correct database fields from first migration
        $payment = LoanRepayment::create([
            'loan_id' => $loan->id,
            'member_id' => $loan->member_id,
            'amount' => $totalPaid,
            'paid_at' => $request->payment_date,
            'method' => $request->payment_method,
            'interest_component' => $interestPaid,
            'principal_component' => $principalPaid,
            'notes' => $request->notes,
            'received_by' => auth()->id(),
        ]);

        return back()->with('success', 'Payment recorded successfully!');
    }

    /**
     * Show payment form for a specific installment
     */
    public function showPaymentForm($loanId, $scheduleId)
    {
        $loan = Loan::findOrFail($loanId);
        $schedule = RepaymentSchedule::findOrFail($scheduleId);

        // Ensure schedule belongs to the loan
        if ($schedule->loan_id !== $loan->id) {
            return back()->with('error', 'Invalid repayment schedule.');
        }

        return view('admin.group-loans.payment', compact('loan', 'schedule'));
    }
    public function loans()
    {
        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        $loansQuery = Loan::with(['member', 'repaymentSchedules']);
        
        // Filter by fiscal year if selected
        if ($currentFiscalYear) {
            $loansQuery->where('fiscal_year_id', $currentFiscalYear->id);
        } else {
            // Show no data if no fiscal year selected
            $loansQuery->whereRaw('1 = 0');
        }
        
        $loans = $loansQuery
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('member_id'), function($query, $memberId) {
                $query->where('member_id', $memberId);
            })
            ->whereHas('member') // Only include loans that have valid members
            ->latest()
            ->paginate(15);

        $members = Member::orderBy('first_name')->get();

        return view('admin.group-loans.loans', compact('loans', 'members', 'currentFiscalYear'));
    }

    /**
     * Generate loan reports
     */
    public function reports()
    {
        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        // Member loan summary - use correct field names
        $memberLoanSummary = Member::with(['loans' => function($query) use ($currentFiscalYear) {
            $query->where('fiscal_year_id', $currentFiscalYear->id);
        }])
        ->whereHas('loans', function($query) use ($currentFiscalYear) {
            $query->where('fiscal_year_id', $currentFiscalYear->id);
        })
        ->get();

        // Monthly loan activity - use correct field names
        $monthlyActivity = Loan::where('fiscal_year_id', $currentFiscalYear->id)
            ->selectRaw('MONTH(disbursement_date) as month, COUNT(*) as loans_count, SUM(principal_amount) as total_amount')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fiscal year summary - use correct field names
        $fiscalYearSummary = [
            'total_loans' => Loan::where('fiscal_year_id', $currentFiscalYear->id)->count(),
            'total_principal' => Loan::where('fiscal_year_id', $currentFiscalYear->id)->sum('principal_amount'),
            'total_interest' => Loan::where('fiscal_year_id', $currentFiscalYear->id)->sum('total_interest'),
            'total_repaid' => Loan::where('fiscal_year_id', $currentFiscalYear->id)->sum('paid_amount'),
            'total_penalties' => 0, // Will implement when penalties table is ready
        ];

        return view('admin.group-loans.reports', compact(
            'memberLoanSummary',
            'monthlyActivity',
            'fiscalYearSummary',
            'currentFiscalYear'
        ));
    }

    /**
     * Show loan details
     */
    public function show($id)
    {
        $loan = Loan::with(['member', 'repaymentSchedules', 'repayments', 'penalties'])
            ->findOrFail($id);

        return view('admin.group-loans.show', compact('loan'));
    }

    /**
     * Approve loan request
     */
    public function approveRequest($id)
    {
        $loanRequest = LoanRequest::findOrFail($id);
        
        if ($loanRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        // Cast values to proper types
        $principalAmount = (float) $loanRequest->principal_amount;
        $interestRate = (float) $loanRequest->interest_rate;
        $loanTermMonths = (int) $loanRequest->loan_term_months;
        
        // Calculate loan details using simple interest
        // Simple Interest Formula: Interest = Principal × Rate / 100
        $totalInterest = ($principalAmount * $interestRate) / 100;
        $totalRepayment = $principalAmount + $totalInterest;
        $monthlyPayment = $totalRepayment / $loanTermMonths;
        
        // Generate unique loan number
        $loanNumber = 'LN-' . date('Y') . '-' . str_pad(Loan::count() + 1, 4, '0', STR_PAD_LEFT);
        
        // Create loan from request using correct field names
        $loan = Loan::create([
            'member_id' => $loanRequest->member_id,
            'fiscal_year_id' => $currentFiscalYear->id,
            'loan_number' => $loanNumber,
            'principal_amount' => $principalAmount,
            'interest_rate' => $interestRate,
            'duration_months' => $loanTermMonths,
            'disbursement_date' => now(),
            'disbursed_by' => auth()->id(),
            'first_payment_date' => now()->addMonths(2),
            'maturity_date' => now()->addMonths($loanTermMonths),
            'monthly_installment' => $monthlyPayment,
            'total_interest' => $totalInterest,
            'total_repayment' => $totalRepayment,
            'total_repayable' => $totalRepayment,
            'balance' => $totalRepayment,
            'paid_amount' => 0,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Create repayment schedules
        for ($i = 1; $i <= $loanTermMonths; $i++) {
            RepaymentSchedule::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => now()->addMonths($i + 1), // Start payments after 2 months
                'principal_due' => $principalAmount / $loanTermMonths, // Principal portion
                'interest_due' => ($totalInterest / $loanTermMonths), // Interest portion
                'total_due' => $monthlyPayment, // Total payment
                'principal_paid' => 0, // Initially unpaid
                'interest_paid' => 0, // Initially unpaid
                'penalty_charged' => 0, // Initially no penalty
                'penalty_paid' => 0, // Initially no penalty paid
                'amount' => $monthlyPayment, // Simple amount field for compatibility
                'outstanding_balance' => $monthlyPayment, // Balance after this installment
                'fiscal_year_id' => $currentFiscalYear->id,
            ]);
        }

        // Update request status
        $loanRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_date' => now(),
        ]);

        return back()->with('success', 'Loan request approved and loan created successfully!');
    }

    /**
     * Reject loan request
     */
    public function rejectRequest(Request $request, $id)
    {
        $loanRequest = LoanRequest::findOrFail($id);
        
        if ($loanRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $loanRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_by' => auth()->id(),
            'rejected_date' => now(),
        ]);

        return back()->with('success', 'Loan request rejected successfully!');
    }

    /**
     * Record loan repayment
     */
    public function recordRepayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        $loan = Loan::findOrFail($id);
        $currentFiscalYear = FiscalYearContext::getCurrent();

        $repayment = LoanRepayment::create([
            'loan_id' => $loan->id,
            'member_id' => $loan->member_id,
            'amount' => $request->amount,
            'paid_at' => $request->payment_date,
            'method' => $request->payment_method,
            'notes' => $request->notes,
            'received_by' => auth()->id(),
        ]);

        // Update loan paid amount and total repayment
        $loan->increment('paid_amount', $request->amount);
        $loan->increment('total_repayment', $request->amount);
        
        // Recalculate balance
        $newBalance = $loan->total_repayable - $loan->total_repayment;
        $loan->balance = max(0, $newBalance);
        $loan->save();

        // Check if loan is fully paid
        if ($loan->total_repayment >= $loan->total_repayable || $loan->balance <= 0) {
            $loan->update([
                'status' => 'completed',
                'loan_status' => 'completed',
                'completed_at' => now(),
                'balance' => 0
            ]);
        }

        return back()->with('success', 'Repayment recorded successfully!');
    }

    /**
     * Apply penalty to overdue schedule
     */
    public function applyPenalty(Request $request, $id)
    {
        $request->validate([
            'penalty_amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $schedule = RepaymentSchedule::findOrFail($id);
        $currentFiscalYear = FiscalYearContext::getCurrent();

        LoanPenalty::create([
            'loan_id' => $schedule->loan_id,
            'schedule_id' => $schedule->id,
            'penalty_amount' => $request->penalty_amount,
            'reason' => $request->reason,
            'fiscal_year_id' => $currentFiscalYear->id,
            'applied_by' => auth()->id(),
        ]);

        return back()->with('success', 'Penalty applied successfully!');
    }
}
