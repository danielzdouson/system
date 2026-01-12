<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberAccount;
use App\Models\Member;
use App\Models\FiscalYear;

class AccountController extends Controller
{
    /**
     * Display all member accounts
     */
    public function index()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        if (!$activeFiscalYear) {
            return view('admin.accounts.index', [
                'accounts' => collect(),
                'activeFiscalYear' => null,
            ]);
        }

        $accounts = MemberAccount::with(['member', 'fiscalYear'])
            ->where('fiscal_year_id', $activeFiscalYear->id)
            ->orderBy('current_balance', 'desc')
            ->get();

        return view('admin.accounts.index', compact('accounts', 'activeFiscalYear'));
    }

    /**
     * Show specific member account details
     */
    public function show($memberId)
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        if (!$activeFiscalYear) {
            return redirect()->route('admin.accounts.index')
                ->with('error', 'No active fiscal year found');
        }

        $member = Member::findOrFail($memberId);
        $account = MemberAccount::where('member_id', $memberId)
            ->where('fiscal_year_id', $activeFiscalYear->id)
            ->with(['member', 'fiscalYear'])
            ->first();

        if (!$account) {
            return redirect()->route('admin.accounts.index')
                ->with('error', 'Account not found for this member');
        }

        // Get transaction history (deposits and distributions)
        $deposits = $member->deposits()
            ->where('fiscal_year_id', $activeFiscalYear->id)
            ->with('distributions')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.accounts.show', compact('member', 'account', 'deposits', 'activeFiscalYear'));
    }

    /**
     * Show account summary statistics
     */
    public function summary()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        if (!$activeFiscalYear) {
            return view('admin.accounts.summary', [
                'summary' => null,
                'activeFiscalYear' => null,
            ]);
        }

        $accounts = MemberAccount::where('fiscal_year_id', $activeFiscalYear->id)->get();
        
        $summary = [
            'total_members' => $accounts->count(),
            'total_deposited' => $accounts->sum('total_deposited'),
            'total_distributed' => $accounts->sum('total_distributed'),
            'total_balance' => $accounts->sum('current_balance'),
            'total_savings_balance' => $accounts->sum('savings_balance'),
            'total_welfare_balance' => $accounts->sum('welfare_balance'),
            'total_fines_balance' => $accounts->sum('fines_balance'),
            'total_other_balance' => $accounts->sum('other_balance'),
            'members_with_balance' => $accounts->where('current_balance', '>', 0)->count(),
            'members_with_zero_balance' => $accounts->where('current_balance', '=', 0)->count(),
        ];

        return view('admin.accounts.summary', compact('summary', 'activeFiscalYear'));
    }
}
