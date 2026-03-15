<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\RepaymentSchedule;
use PDF;

class MemberLoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('member');
    }

    /**
     * Show loan payment page
     */
    public function payment($id)
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        $loan = Loan::with(['repaymentSchedules'])
            ->where('id', $id)
            ->where('member_id', $member->id)
            ->firstOrFail();

        return view('member.loan-payment', compact('loan'));
    }

    /**
     * Show loan details page
     */
    public function details($id)
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        $loan = Loan::with(['repaymentSchedules', 'repayments', 'penalties'])
            ->where('id', $id)
            ->where('member_id', $member->id)
            ->firstOrFail();

        return view('member.loan-details', compact('loan'));
    }

    /**
     * Download loan statement
     */
    public function statement($id)
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        $loan = Loan::with(['repaymentSchedules', 'repayments', 'member'])
            ->where('id', $id)
            ->where('member_id', $member->id)
            ->firstOrFail();

        // For now, return a simple view that can be printed
        // In production, you would generate a PDF
        return view('member.loan-statement', compact('loan'));
    }

    /**
     * Download clearance certificate
     */
    public function certificate($id)
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        $loan = Loan::with(['member'])
            ->where('id', $id)
            ->where('member_id', $member->id)
            ->where('status', 'completed')
            ->firstOrFail();

        // For now, return a simple view that can be printed
        // In production, you would generate a PDF certificate
        return view('member.loan-certificate', compact('loan'));
    }

    /**
     * Show full repayment schedule
     */
    public function schedule($id)
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        $loan = Loan::with(['repaymentSchedules'])
            ->where('id', $id)
            ->where('member_id', $member->id)
            ->firstOrFail();

        return view('member.loan-schedule', compact('loan'));
    }

    /**
     * Export loans data
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        $type = $request->get('type', 'all');
        
        $query = Loan::where('member_id', $member->id);
        
        if ($type === 'active') {
            $query->where('status', 'active');
        } elseif ($type === 'completed') {
            $query->where('status', 'completed');
        }
        
        $loans = $query->get();

        // For now, return JSON data
        // In production, you would generate Excel/CSV
        return response()->json([
            'success' => true,
            'type' => $type,
            'count' => $loans->count(),
            'loans' => $loans,
            'message' => 'Export functionality will generate Excel/CSV file in production'
        ]);
    }

    /**
     * Show loan application page
     */
    public function apply()
    {
        $user = auth()->user();
        $member = $user->member;
        
        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        // Check eligibility
        $activeLoansCount = Loan::where('member_id', $member->id)
            ->where('status', 'active')
            ->count();

        $hasOverduePayments = false;
        if ($activeLoansCount > 0) {
            $hasOverduePayments = RepaymentSchedule::whereHas('loan', function($query) use ($member) {
                $query->where('member_id', $member->id)
                      ->where('status', 'active');
            })
            ->where('due_date', '<', now())
            ->where('status', 'pending')
            ->exists();
        }

        return view('member.loan-apply', compact('member', 'activeLoansCount', 'hasOverduePayments'));
    }

    /**
     * Show loan information page
     */
    public function info()
    {
        return view('member.loan-info');
    }
}
