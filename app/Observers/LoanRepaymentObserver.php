<?php

namespace App\Observers;

use App\Models\LoanRepayment;
use App\Models\CashflowTransaction;
use Illuminate\Support\Facades\Auth;

class LoanRepaymentObserver
{
    /**
     * Handle the LoanRepayment "created" event.
     */
    public function created(LoanRepayment $repayment): void
    {
        // Update loan balance and repayment totals
        $loan = $repayment->loan;
        $loan->paid_amount += $repayment->amount;
        $loan->balance = max(0, $loan->total_repayable - $loan->paid_amount);
        
        // Update loan status if fully paid
        if ($loan->balance <= 0) {
            $loan->status = 'completed';
            $loan->completed_at = now();
        }
        
        $loan->save();

        // Create cashflow transaction for loan repayment
        CashflowTransaction::create([
            'transaction_date' => $repayment->paid_at,
            'transaction_type' => CashflowTransaction::TYPE_INFLOW,
            'category' => CashflowTransaction::CATEGORY_FINANCING,
            'subcategory' => 'Loan Repayment',
            'description' => "Loan repayment from {$repayment->loan->member->first_name} {$repayment->loan->member->last_name}",
            'amount' => $repayment->amount,
            'reference_type' => CashflowTransaction::REFERENCE_LOAN_REPAYMENT,
            'reference_id' => $repayment->id,
            'reference_number' => $repayment->reference ?? 'REP-' . str_pad($repayment->id, 6, '0', STR_PAD_LEFT),
            'payment_method' => $repayment->method,
            'status' => CashflowTransaction::STATUS_CLEARED,
            'fiscal_year_id' => $repayment->loan->fiscal_year_id,
            'member_id' => $repayment->loan->member_id,
            'created_by' => $repayment->received_by,
            'notes' => $repayment->notes
        ]);
    }
}
