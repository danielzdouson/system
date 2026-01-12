<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberLoanSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberLoanSummaryController extends Controller
{
    /**
     * Show loan entry form
     */
    public function create()
    {
        $members = Member::all();

        return view('admin.loans.create', compact('members'));
    }

    /**
     * Store loan summary
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'name' => 'required|string|max:255',

            'loan_brought_forward' => 'nullable|numeric|min:0',
            'loan_issued_current_year' => 'nullable|numeric|min:0',
            'current_year_loan_plus_interest' => 'nullable|numeric|min:0',
            'loan_balance_without_fines' => 'nullable|numeric|min:0',
            'loan_out' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {

            $broughtForward = $validated['loan_brought_forward'] ?? 0;
            $issued = $validated['loan_issued_current_year'] ?? 0;
            $interestTotal = $validated['current_year_loan_plus_interest'] ?? 0;
            $balanceWithoutFines = $validated['loan_balance_without_fines'] ?? 0;
            $paid = $validated['loan_out'] ?? 0;

            /**
             * CORE SACCO LOGIC
             * -----------------
             * Total = (B/F + Issued + Interest) - Paid
             */
            $totalLoanBalance = max(
                ($broughtForward + $issued + $interestTotal) - $paid,
                0
            );

            MemberLoanSummary::create([
                'member_id' => $validated['member_id'],
                'name' => $validated['name'],

                'loan_brought_forward' => $broughtForward,
                'loan_issued_current_year' => $issued,
                'current_year_loan_plus_interest' => $interestTotal,

                'loan_balance_without_fines' => $balanceWithoutFines,
                'loan_out' => $paid,
                'total' => $totalLoanBalance,

                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Loan summary saved successfully.');
    }

    /**
     * Optional: List loan summaries
     */
    public function index()
{
    $loans = MemberLoanSummary::with('member')
        ->latest()
        ->paginate(20);

    $members = Member::all(); // fetch all members

    return view('admin.loans.index', compact('loans', 'members'));
}
}
