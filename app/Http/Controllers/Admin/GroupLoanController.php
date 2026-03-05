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
use Illuminate\Http\Request;

class GroupLoanController extends Controller
{
    /**
     * Display loan dashboard
     */
    public function index()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        // Get loan statistics
        $totalLoans = Loan::count();
        $activeLoans = Loan::where('status', 'active')
            ->count();
        $completedLoans = Loan::where('status', 'completed')
            ->count();
        $defaultedLoans = Loan::where('status', 'defaulted')
            ->count();

        // Get financial summary
        $totalDisbursed = Loan::sum('principal_amount');
        $totalRepaid = Loan::sum('paid_amount');
        $totalOutstanding = $totalDisbursed - $totalRepaid;

        // Get pending requests
        $pendingRequests = LoanRequest::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('status', 'pending')
            ->count();

        // Get recent loans
        $recentLoans = Loan::with('member')
            ->latest()
            ->take(5)
            ->get();

        // Get overdue loans
        $overdueLoans = Loan::where('status', 'active')
            ->whereHas('repaymentSchedules', function($query) {
                $query->where('due_date', '<', now())
                    ->where('status', 'pending');
            })
            ->count();

        // Get members for dropdowns
        $members = Member::orderBy('first_name')->get();
        
        // Get loans for table
        $loans = Loan::with('member')
            ->whereHas('member') // Only include loans that have valid members
            ->latest()
            ->paginate(10);

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
            'activeFiscalYear'
        ));
    }

    /**
     * Display loan requests
     */
    public function requests()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        $loanRequests = LoanRequest::where('fiscal_year_id', $activeFiscalYear->id)
            ->with('member', 'approvedBy')
            ->latest()
            ->paginate(10);

        // Get all members for new loan requests
        $members = Member::orderBy('first_name')->get();

        return view('admin.group-loans.requests', compact('loanRequests', 'activeFiscalYear', 'members'));
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

        $activeFiscalYear = FiscalYear::getActive();
        
        // Calculate monthly payment for loan request
        $totalInterest = ($request->principal_amount * $request->interest_rate * $request->loan_term_months) / 100;
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
            'fiscal_year_id' => $activeFiscalYear->id,
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

        $activeFiscalYear = FiscalYear::getActive();
        
        // Cast values to proper types
        $principalAmount = (float) $request->principal_amount;
        $interestRate = (float) $request->interest_rate;
        $loanTermMonths = (int) $request->loan_term_months;
        
        // Calculate loan details using proper amortization
        $monthlyRate = $interestRate / 12 / 100; // Monthly interest rate as decimal
        
        if ($monthlyRate == 0) {
            // If no interest, simple division
            $monthlyPayment = $principalAmount / $loanTermMonths;
            $totalInterest = 0;
            $totalRepayment = $principalAmount;
        } else {
            // Use amortization formula: M = P * [r(1+r)^n] / [(1+r)^n - 1]
            $r = $monthlyRate;
            $n = $loanTermMonths;
            $monthlyPayment = $principalAmount * ($r * pow(1 + $r, $n)) / (pow(1 + $r, $n) - 1);
            $totalRepayment = $monthlyPayment * $loanTermMonths;
            $totalInterest = $totalRepayment - $principalAmount;
        }
        
        // Generate unique loan number
        $loanNumber = 'LN-' . date('Y') . '-' . str_pad(Loan::count() + 1, 4, '0', STR_PAD_LEFT);
        
        // Create loan directly using original database fields
        $loan = Loan::create([
            'member_id' => $request->member_id,
            'fiscal_year_id' => $activeFiscalYear->id,
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
                'fiscal_year_id' => $activeFiscalYear->id,
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
            'payment_amount' => $totalPaid,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'interest_portion' => $interestPaid,
            'principal_portion' => $principalPaid,
            'balance_after_payment' => $loan->balance,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
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
        $activeFiscalYear = FiscalYear::getActive();
        
        $loans = Loan::with(['member', 'repaymentSchedules'])
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

        return view('admin.group-loans.loans', compact('loans', 'members', 'activeFiscalYear'));
    }

    /**
     * Generate loan reports
     */
    public function reports()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        // Member loan summary - use correct field names
        $memberLoanSummary = Member::with(['loans' => function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        }])
        ->whereHas('loans', function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        })
        ->get();

        // Monthly loan activity - use correct field names
        $monthlyActivity = Loan::where('fiscal_year_id', $activeFiscalYear->id)
            ->selectRaw('MONTH(disbursement_date) as month, COUNT(*) as loans_count, SUM(principal_amount) as total_amount')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fiscal year summary - use correct field names
        $fiscalYearSummary = [
            'total_loans' => Loan::where('fiscal_year_id', $activeFiscalYear->id)->count(),
            'total_principal' => Loan::where('fiscal_year_id', $activeFiscalYear->id)->sum('principal_amount'),
            'total_interest' => Loan::where('fiscal_year_id', $activeFiscalYear->id)->sum('total_interest'),
            'total_repaid' => Loan::where('fiscal_year_id', $activeFiscalYear->id)->sum('paid_amount'),
            'total_penalties' => 0, // Will implement when penalties table is ready
        ];

        return view('admin.group-loans.reports', compact(
            'memberLoanSummary',
            'monthlyActivity',
            'fiscalYearSummary',
            'activeFiscalYear'
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

        $activeFiscalYear = FiscalYear::getActive();
        
        // Cast values to proper types
        $principalAmount = (float) $loanRequest->principal_amount;
        $interestRate = (float) $loanRequest->interest_rate;
        $loanTermMonths = (int) $loanRequest->loan_term_months;
        
        // Calculate loan details using proper amortization
        $monthlyRate = $interestRate / 12 / 100; // Monthly interest rate as decimal
        
        if ($monthlyRate == 0) {
            // If no interest, simple division
            $monthlyPayment = $principalAmount / $loanTermMonths;
            $totalInterest = 0;
            $totalRepayment = $principalAmount;
        } else {
            // Use amortization formula: M = P * [r(1+r)^n] / [(1+r)^n - 1]
            $r = $monthlyRate;
            $n = $loanTermMonths;
            $monthlyPayment = $principalAmount * ($r * pow(1 + $r, $n)) / (pow(1 + $r, $n) - 1);
            $totalRepayment = $monthlyPayment * $loanTermMonths;
            $totalInterest = $totalRepayment - $principalAmount;
        }
        
        // Generate unique loan number
        $loanNumber = 'LN-' . date('Y') . '-' . str_pad(Loan::count() + 1, 4, '0', STR_PAD_LEFT);
        
        // Create loan from request using correct field names
        $loan = Loan::create([
            'member_id' => $loanRequest->member_id,
            'fiscal_year_id' => $activeFiscalYear->id,
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
                'fiscal_year_id' => $activeFiscalYear->id,
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
        $activeFiscalYear = FiscalYear::getActive();

        $repayment = LoanRepayment::create([
            'loan_id' => $loan->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'fiscal_year_id' => $activeFiscalYear->id,
            'received_by' => auth()->id(),
        ]);

        // Update loan paid amount
        $loan->increment('paid_amount', $request->amount);

        // Check if loan is fully paid
        if ($loan->paid_amount >= $loan->total_amount) {
            $loan->update(['status' => 'completed']);
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
        $activeFiscalYear = FiscalYear::getActive();

        LoanPenalty::create([
            'loan_id' => $schedule->loan_id,
            'schedule_id' => $schedule->id,
            'penalty_amount' => $request->penalty_amount,
            'reason' => $request->reason,
            'fiscal_year_id' => $activeFiscalYear->id,
            'applied_by' => auth()->id(),
        ]);

        return back()->with('success', 'Penalty applied successfully!');
    }
}
