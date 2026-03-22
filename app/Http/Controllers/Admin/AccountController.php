<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use App\Models\MemberAccount;
use App\Models\FiscalYear;
use App\Models\Deposit;
use App\Services\FiscalYearContext;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Display all member accounts and user account creation interface.
     */
    public function index()
    {
        $members = Member::with(['user', 'memberAccounts' => function($query) {
            $query->with('fiscalYear')->latest();
        }])->latest()->get();
        
        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        return view('admin.accounts.index', compact('members', 'currentFiscalYear'));
    }

    /**
     * Show account summary statistics.
     */
    public function summary()
    {
        $totalMembers = Member::count();
        $membersWithAccounts = Member::whereHas('user')->count();
        $membersWithoutAccounts = $totalMembers - $membersWithAccounts;
        
        $currentFiscalYear = FiscalYearContext::getCurrent();
        $totalSavings = 0;
        $totalLoans = 0;
        
        if ($currentFiscalYear) {
            $memberAccounts = MemberAccount::where('fiscal_year_id', $currentFiscalYear->id)->get();
            $totalSavings = $memberAccounts->sum('savings_balance');
        }
        
        $activeLoans = \App\Models\Loan::where('loan_status', 'active')->sum('balance');
        
        return view('admin.accounts.summary', compact(
            'totalMembers', 
            'membersWithAccounts', 
            'membersWithoutAccounts',
            'totalSavings',
            'activeLoans',
            'currentFiscalYear'
        ));
    }

    /**
     * Show specific member account details.
     */
    public function show($memberId)
    {
        $member = Member::with(['user', 'memberAccounts' => function($query) {
            $query->with('fiscalYear')->latest();
        }, 'loans' => function($query) {
            $query->with('repaymentSchedules')->latest();
        }])->findOrFail($memberId);
        
        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        // Get the member's account for the current fiscal year
        $account = null;
        if ($currentFiscalYear) {
            $account = $member->memberAccounts->where('fiscal_year_id', $currentFiscalYear->id)->first();
        }
        
        // Get member's deposits with distributions
        $deposits = Deposit::where('member_id', $memberId)
            ->with('distributions')
            ->latest()
            ->get();
        
        return view('admin.accounts.show', compact('member', 'currentFiscalYear', 'account', 'deposits'));
    }

    /**
     * Create user account for a member.
     */
    public function createUserAccount(Request $request, $memberId)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $member = Member::findOrFail($memberId);
        
        // Check if member already has a user account
        if ($member->user) {
            return redirect()->back()->with('error', 'Member already has a user account.');
        }

        // Create user account
        $user = User::create([
            'name' => $member->first_name . ' ' . $member->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'member',
        ]);

        // Link the user to the member
        $member->user_id = $user->id;
        $member->save();

        return redirect()->back()->with('success', 'User account created successfully for ' . $member->first_name . ' ' . $member->last_name);
    }

    /**
     * Reset member password.
     */
    public function resetPassword(Request $request, $memberId)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $member = Member::with('user')->findOrFail($memberId);
        
        if (!$member->user) {
            return redirect()->back()->with('error', 'Member does not have a user account.');
        }

        $member->user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()->with('success', 'Password reset successfully for ' . $member->first_name . ' ' . $member->last_name);
    }

    /**
     * Toggle user account status (activate/deactivate).
     */
    public function toggleUserStatus($memberId)
    {
        $member = Member::with('user')->findOrFail($memberId);
        
        if (!$member->user) {
            return redirect()->back()->with('error', 'Member does not have a user account.');
        }

        // Toggle between active and suspended (you could add a 'status' field to users table)
        // For now, we'll just toggle soft delete
        if ($member->user->deleted_at) {
            $member->user->restore();
            $message = 'User account activated successfully.';
        } else {
            $member->user->delete();
            $message = 'User account deactivated successfully.';
        }

        return redirect()->back()->with('success', $message);
    }
}
