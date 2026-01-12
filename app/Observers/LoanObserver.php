<?php

namespace App\Observers;

use App\Models\Loan;
use App\Models\CashflowTransaction;
use Illuminate\Support\Facades\Auth;

class LoanObserver
{
    /**
     * Handle the Loan "updated" event.
     */
    public function updated(Loan $loan): void
    {
        // Create cashflow transaction when loan is disbursed
        if ($loan->wasChanged('loan_status') && $loan->loan_status === 'disbursed') {
            CashflowTransaction::create([
                'transaction_date' => $loan->disbursement_date,
                'transaction_type' => CashflowTransaction::TYPE_OUTFLOW,
                'category' => CashflowTransaction::CATEGORY_FINANCING,
                'subcategory' => 'Loan Disbursement',
                'description' => "Loan disbursement to {$loan->member->first_name} {$loan->member->last_name}",
                'amount' => $loan->loan_amount,
                'reference_type' => CashflowTransaction::REFERENCE_LOAN_DISBURSEMENT,
                'reference_id' => $loan->id,
                'reference_number' => $loan->loan_number ?? 'LOAN-' . str_pad($loan->id, 6, '0', STR_PAD_LEFT),
                'payment_method' => $loan->payment_method,
                'status' => CashflowTransaction::STATUS_CLEARED,
                'fiscal_year_id' => $loan->fiscal_year_id,
                'member_id' => $loan->member_id,
                'created_by' => $loan->disbursed_by,
                'notes' => $loan->notes
            ]);
        }
    }
}
