<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanRepayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Loan::with('member')->latest();
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('loan_status', $request->status);
        }
        
        // Filter by member
        if ($request->has('member_id') && $request->member_id) {
            $query->where('member_id', $request->member_id);
        }
        
        $loans = $query->paginate(20);
        $members = Member::latest()->get();
        
        // Get statistics
        $stats = [
            'total_loans' => Loan::count(),
            'active_loans' => Loan::where('loan_status', Loan::STATUS_ACTIVE)->count(),
            'pending_loans' => Loan::where('loan_status', Loan::STATUS_PENDING)->count(),
            'completed_loans' => Loan::where('loan_status', Loan::STATUS_COMPLETED)->count(),
            'arrears_loans' => Loan::where('loan_status', Loan::STATUS_ARREARS)->count(),
            'total_disbursed' => Loan::whereIn('loan_status', [Loan::STATUS_ACTIVE, Loan::STATUS_ARREARS, Loan::STATUS_COMPLETED])->sum('loan_amount'),
            'total_balance' => Loan::whereIn('loan_status', [Loan::STATUS_ACTIVE, Loan::STATUS_ARREARS])->sum('balance'),
        ];
        
        return view('admin.loans.index', compact('loans', 'members', 'stats'));
    }

    public function create()
    {
        $members = Member::latest()->get();
        return view('admin.loans.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'loan_amount' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'loan_term' => 'required|integer|min:1|max:60',
            'loan_purpose' => 'required|string|max:255',
            'payment_method' => 'required|string|max:255',
            'guarantor_name' => 'nullable|string|max:255',
            'guarantor_phone' => 'nullable|string|max:20',
            'guarantor_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Calculate loan details
        $monthlyRate = $validated['interest_rate'] / 100 / 12;
        $monthlyPayment = $validated['loan_amount'] * 
            ($monthlyRate * pow(1 + $monthlyRate, $validated['loan_term'])) / 
            (pow(1 + $monthlyRate, $validated['loan_term']) - 1);
        
        $totalRepayment = $monthlyPayment * $validated['loan_term'];
        $totalInterest = $totalRepayment - $validated['loan_amount'];

        $validated['monthly_payment'] = $monthlyPayment;
        $validated['total_interest'] = $totalInterest;
        $validated['total_repayment'] = $totalRepayment;
        $validated['balance'] = $totalRepayment;
        $validated['arrears'] = 0;
        $validated['disbursement_date'] = now()->addDays(7); // 7 days for processing
        $validated['first_payment_date'] = now()->addMonth();
        $validated['maturity_date'] = now()->addMonths($validated['loan_term']);
        $validated['loan_status'] = Loan::STATUS_PENDING;
        $validated['created_by'] = Auth::id();

        Loan::create($validated);

        return redirect()->route('admin.loans.index')
            ->with('success', 'Loan application submitted successfully!');
    }

    public function show(Loan $loan)
    {
        $loan->load(['member', 'repayments' => function($query) {
            $query->latest('payment_date');
        }]);
        
        return view('admin.loans.show', compact('loan'));
    }

    public function edit(Loan $loan)
    {
        $members = Member::latest()->get();
        return view('admin.loans.edit', compact('loan', 'members'));
    }

    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'loan_status' => 'required|in:' . implode(',', [
                Loan::STATUS_PENDING, Loan::STATUS_APPROVED, Loan::STATUS_DISBURSED,
                Loan::STATUS_ACTIVE, Loan::STATUS_ARREARS, Loan::STATUS_COMPLETED,
                Loan::STATUS_DEFAULTED, Loan::STATUS_WRITTEN_OFF
            ]),
            'notes' => 'nullable|string|max:1000',
        ]);

        // Handle status changes
        if ($validated['loan_status'] === Loan::STATUS_APPROVED && $loan->loan_status === Loan::STATUS_PENDING) {
            $validated['approved_by'] = Auth::id();
            $validated['approved_at'] = now();
        }

        if ($validated['loan_status'] === Loan::STATUS_DISBURSED && $loan->loan_status !== Loan::STATUS_DISBURSED) {
            $validated['disbursement_date'] = now();
        }

        $loan->update($validated);

        return redirect()->route('admin.loans.index')
            ->with('success', 'Loan updated successfully!');
    }

    public function approve(Loan $loan)
    {
        $loan->update([
            'loan_status' => Loan::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Loan approved successfully!');
    }

    public function disburse(Loan $loan)
    {
        $loan->update([
            'loan_status' => Loan::STATUS_DISBURSED,
            'disbursement_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Loan disbursed successfully!');
    }

    public function destroy(Loan $loan)
    {
        if ($loan->loan_status === Loan::STATUS_ACTIVE) {
            return redirect()->back()->with('error', 'Cannot delete active loan!');
        }

        $loan->delete();

        return redirect()->route('admin.loans.index')
            ->with('success', 'Loan deleted successfully!');
    }
}
