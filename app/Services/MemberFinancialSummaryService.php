<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Deposit;
use App\Models\MonthlySaving;
use App\Models\MemberFinancial;
use App\Models\MemberLoanSummary;
use App\Models\Fine;
use App\Models\MemberAccount;

class MemberFinancialSummaryService
{
    /**
     * Get comprehensive financial summary for all members
     */
    public function getAllMembersFinancialSummary($perPage = 20, $search = null)
    {
        $query = Member::with([
            'monthlySaving',
            'memberFinancial',
            'memberLoanSummary',
            'memberAccounts' => function($query) {
                $query->orderBy('fiscal_year_id', 'desc');
            },
            'deposits',
            'loans' => function($query) {
                $query->whereNotIn('status', ['completed', 'paid']);
            },
            'fines' => function($query) {
                $query->where('status', 'pending');
            }
        ]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        $members = $query->paginate($perPage);

        $members->getCollection()->transform(function ($member) {
            return $this->getMemberFinancialSummary($member);
        });

        return $members;
    }

    /**
     * Get financial summary for a single member
     */
    public function getMemberFinancialSummary($member)
    {
        // Get total deposits across all fiscal years
        $totalDeposits = Deposit::where('member_id', $member->id)->sum('amount');

        // Get total savings from member account savings_balance
        $currentAccount = $member->memberAccounts->first();
        $totalSavings = $currentAccount ? $currentAccount->savings_balance : 0;

        // Get welfare contributions from MemberAccount
        $welfare = $currentAccount ? $currentAccount->welfare_balance : 0;

        // Get outstanding fines
        $outstandingFines = Fine::where('member_id', $member->id)
                               ->where('status', 'pending')
                               ->sum('amount');

        // Get loan balance from preloaded loans relationship
        $loanBalance = $member->loans->sum('balance');

        // Get member account info (current available balance)
        $availableBalance = $currentAccount ? $currentAccount->current_balance : 0;
        $distributedFunds = $currentAccount ? $currentAccount->total_distributed : 0;
        
        // Calculate shares as percentage of total group savings
        $totalGroupSavings = MemberAccount::sum('savings_balance');
        $memberSharePercentage = ($totalGroupSavings > 0) ? (($totalSavings / $totalGroupSavings) * 100) : 0;
        
        // For display purposes, we'll show the percentage value
        $totalShares = $memberSharePercentage; // This now represents percentage share
        $sharesOnHold = 0; // Can be calculated based on business rules later

        // Calculate net worth (including shares)
        $netWorth = $availableBalance + $totalSavings + $welfare + $totalShares - $loanBalance - $outstandingFines - $sharesOnHold;

        // Determine member status
        $status = $this->determineMemberStatus($member, $outstandingFines, $loanBalance);

        return [
            'id' => $member->id,
            'name' => $member->first_name . ' ' . $member->last_name,
            'member_number' => 'MEM-' . str_pad($member->id, 4, '0', STR_PAD_LEFT),
            'total_deposits' => $totalDeposits,
            'total_savings' => $totalSavings,
            'welfare' => $welfare,
            'outstanding_fines' => $outstandingFines,
            'loan_balance' => $loanBalance,
            'available_balance' => $availableBalance,
            'distributed_funds' => $distributedFunds,
            'shares_on_hold' => $sharesOnHold,
            'total_shares' => $totalShares,
            'net_worth' => $netWorth,
            'status' => $status,
            'account_details' => $currentAccount ? [
                'savings_balance' => $currentAccount->savings_balance,
                'welfare_balance' => $currentAccount->welfare_balance,
                'fines_balance' => $currentAccount->fines_balance,
                'other_balance' => $currentAccount->other_balance,
                'shares_on_hold' => 0, // Calculated based on business rules
                'total_shares' => $memberSharePercentage, // Percentage share of total savings
                'share_percentage' => $memberSharePercentage . '%' // Display as percentage
            ] : null
        ];
    }

    /**
     * Determine member status based on financial activity
     */
    private function determineMemberStatus($member, $outstandingFines, $loanBalance)
    {
        // Check for recent activity (last 3 months)
        $hasRecentActivity = Deposit::where('member_id', $member->id)
                                  ->where('deposit_date', '>=', now()->subMonths(3))
                                  ->exists();

        if (!$hasRecentActivity) {
            return 'inactive';
        }

        if ($outstandingFines > 0 || $loanBalance > 0) {
            return $outstandingFines > 10000 ? 'delinquent' : 'active';
        }

        return 'active';
    }

    
    /**
     * Get summary statistics for all members
     */
    public function getMembersSummaryStats()
    {
        $members = Member::count();
        
        $stats = [
            'total_members' => $members,
            'total_deposits' => Deposit::sum('amount'),
            'total_savings' => MemberAccount::sum('savings_balance'),
            'total_welfare' => MemberAccount::sum('welfare_balance'),
            'total_outstanding_fines' => Fine::where('status', 'pending')->sum('amount'),
            'total_loan_balance' => \App\Models\Loan::whereNotIn('status', ['completed', 'paid'])->sum('balance'),
            'total_available_balance' => MemberAccount::sum('current_balance'),
            'total_distributed_funds' => MemberAccount::sum('total_distributed'),
        ];

        // Handle shares calculation - total should be 100% (sum of all member percentages)
        $totalGroupSavings = MemberAccount::sum('savings_balance');
        $stats['total_shares_on_hold'] = 0; // Can be calculated based on business rules
        $stats['total_shares'] = ($totalGroupSavings > 0) ? 100 : 0; // Total percentage should be 100%

        $stats['total_net_worth'] = $stats['total_available_balance'] + 
                                   $stats['total_savings'] + 
                                   $stats['total_welfare'] + 
                                   $stats['total_shares'] - 
                                   $stats['total_loan_balance'] - 
                                   $stats['total_outstanding_fines'] - 
                                   $stats['total_shares_on_hold'];

        return $stats;
    }
}
