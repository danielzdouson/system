<?php

namespace App\Observers;

use App\Models\WelfareFund;
use App\Models\CashflowTransaction;

class WelfareFundObserver
{
    /**
     * Handle the WelfareFund "created" event.
     */
    public function created(WelfareFund $welfareFund): void
    {
        // Only create cashflow entry if there's an actual amount allocated
        if ($welfareFund->amount > 0) {
            CashflowTransaction::create([
                'transaction_date' => $welfareFund->created_at,
                'transaction_type' => CashflowTransaction::TYPE_OUTFLOW,
                'category' => CashflowTransaction::CATEGORY_OPERATING,
                'subcategory' => 'Welfare Fund Distribution',
                'description' => $welfareFund->description ?: "Welfare fund allocation for month {$welfareFund->month}",
                'amount' => $welfareFund->amount,
                'reference_type' => 'WELFARE_FUND',
                'reference_id' => $welfareFund->id,
                'reference_number' => 'WELFARE-' . str_pad($welfareFund->id, 6, '0', STR_PAD_LEFT),
                'payment_method' => 'internal_transfer',
                'status' => CashflowTransaction::STATUS_CLEARED,
                'fiscal_year_id' => $welfareFund->fiscal_year_id,
                'member_id' => null, // Welfare is a collective fund, not member-specific
                'created_by' => $welfareFund->created_by,
                'notes' => "Welfare fund allocation for fiscal year #{$welfareFund->fiscal_year_id}, month {$welfareFund->month}"
            ]);
        }
    }

    /**
     * Handle the WelfareFund "updated" event.
     */
    public function updated(WelfareFund $welfareFund): void
    {
        // Update corresponding cashflow transaction if amount changed
        if ($welfareFund->wasChanged('amount')) {
            $cashflow = CashflowTransaction::where('reference_type', 'WELFARE_FUND')
                ->where('reference_id', $welfareFund->id)
                ->first();

            if ($cashflow) {
                if ($welfareFund->amount <= 0) {
                    // Delete cashflow entry if amount is now zero or negative
                    $cashflow->delete();
                } else {
                    $cashflow->update([
                        'amount' => $welfareFund->amount,
                        'description' => $welfareFund->description ?: "Welfare fund allocation for month {$welfareFund->month}",
                        'notes' => 'Updated due to welfare fund modification'
                    ]);
                }
            } elseif ($welfareFund->amount > 0) {
                // Create new entry if it didn't exist before but now has amount
                $this->created($welfareFund);
            }
        }
    }

    /**
     * Handle the WelfareFund "deleted" event.
     */
    public function deleted(WelfareFund $welfareFund): void
    {
        // Delete corresponding cashflow transaction
        CashflowTransaction::where('reference_type', 'WELFARE_FUND')
            ->where('reference_id', $welfareFund->id)
            ->delete();
    }
}
