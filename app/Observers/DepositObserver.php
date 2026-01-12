<?php

namespace App\Observers;

use App\Models\Deposit;
use App\Models\CashflowTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DepositObserver
{
    /**
     * Handle the Deposit "created" event.
     */
    public function created(Deposit $deposit): void
    {
        // Create cashflow transaction for deposit
        CashflowTransaction::create([
            'transaction_date' => $deposit->deposit_date,
            'transaction_type' => CashflowTransaction::TYPE_INFLOW,
            'category' => CashflowTransaction::CATEGORY_OPERATING,
            'subcategory' => 'Member Savings Deposit',
            'description' => "Savings deposit from {$deposit->member->first_name} {$deposit->member->last_name}",
            'amount' => $deposit->amount,
            'reference_type' => CashflowTransaction::REFERENCE_DEPOSIT,
            'reference_id' => $deposit->id,
            'reference_number' => 'DEP-' . str_pad($deposit->id, 6, '0', STR_PAD_LEFT),
            'payment_method' => 'Bank/Cash',
            'status' => CashflowTransaction::STATUS_CLEARED,
            'fiscal_year_id' => $deposit->fiscal_year_id,
            'member_id' => $deposit->member_id,
            'created_by' => $deposit->created_by,
            'notes' => $deposit->notes
        ]);
    }

    /**
     * Handle the Deposit "updated" event.
     */
    public function updated(Deposit $deposit): void
    {
        // Update corresponding cashflow transaction if deposit amount changed
        if ($deposit->wasChanged('amount')) {
            $cashflow = CashflowTransaction::byReference(
                CashflowTransaction::REFERENCE_DEPOSIT, 
                $deposit->id
            )->first();
            
            if ($cashflow) {
                $cashflow->update([
                    'amount' => $deposit->amount,
                    'description' => "Updated savings deposit from {$deposit->member->first_name} {$deposit->member->last_name}",
                    'notes' => 'Updated due to deposit modification: ' . $deposit->notes
                ]);
            }
        }
    }
}
