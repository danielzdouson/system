<?php

namespace App\Observers;

use App\Models\LoanPenalty;
use App\Models\CashflowTransaction;

class LoanPenaltyObserver
{
    /**
     * Handle the LoanPenalty "updated" event.
     * This captures when a loan penalty is marked as paid.
     */
    public function updated(LoanPenalty $loanPenalty): void
    {
        // Check if the penalty was just marked as paid
        if ($loanPenalty->wasChanged('status') && $loanPenalty->status === 'paid') {
            $member = $loanPenalty->member;
            $loan = $loanPenalty->loan;
            $memberName = $member ? "{$member->first_name} {$member->last_name}" : 'Unknown Member';
            $loanNumber = $loan?->loan_number ?? 'Unknown';

            // Create cashflow transaction for the penalty payment (INFLOW)
            CashflowTransaction::create([
                'transaction_date' => $loanPenalty->paid_date ?? now(),
                'transaction_type' => CashflowTransaction::TYPE_INFLOW,
                'category' => CashflowTransaction::CATEGORY_FINANCING,
                'subcategory' => 'Loan Penalty Payment',
                'description' => "Loan penalty payment from {$memberName} - Loan {$loanNumber} ({$loanPenalty->penalty_type})",
                'amount' => $loanPenalty->penalty_amount,
                'reference_type' => 'LOAN_PENALTY',
                'reference_id' => $loanPenalty->id,
                'reference_number' => 'PENALTY-' . str_pad($loanPenalty->id, 6, '0', STR_PAD_LEFT),
                'payment_method' => 'cash',
                'status' => CashflowTransaction::STATUS_CLEARED,
                'fiscal_year_id' => $loanPenalty->fiscal_year_id,
                'member_id' => $loanPenalty->member_id,
                'created_by' => auth()->id(),
                'notes' => "Penalty payment for loan #{$loan?->id}. Penalty type: {$loanPenalty->penalty_type}, Days overdue: {$loanPenalty->days_overdue}"
            ]);
        }
    }

    /**
     * Handle the LoanPenalty "deleted" event.
     */
    public function deleted(LoanPenalty $loanPenalty): void
    {
        // Delete corresponding cashflow transactions
        CashflowTransaction::where('reference_type', 'LOAN_PENALTY')
            ->where('reference_id', $loanPenalty->id)
            ->delete();
    }
}
