<?php

namespace App\Observers;

use App\Models\Fine;
use App\Models\FinePayment;
use App\Models\CashflowTransaction;

class FineObserver
{
    /**
     * Handle the Fine "updated" event.
     * This captures when a fine is marked as paid.
     */
    public function updated(Fine $fine): void
    {
        // Check if the fine was just marked as paid
        if ($fine->wasChanged('status') && $fine->status === 'paid') {
            $member = $fine->member;
            $memberName = $member ? "{$member->first_name} {$member->last_name}" : 'Unknown Member';

            // Create a FinePayment record first (for proper tracking)
            $finePayment = FinePayment::create([
                'fine_id' => $fine->id,
                'amount' => $fine->amount,
                'payment_method' => 'cash', // Default, can be updated later
                'transaction_reference' => 'FINE-PAYMENT-' . str_pad($fine->id, 6, '0', STR_PAD_LEFT),
                'payment_date' => now(),
                'notes' => $fine->description,
                'received_by' => auth()->id()
            ]);

            // Create cashflow transaction for the fine payment (INFLOW)
            CashflowTransaction::create([
                'transaction_date' => now(),
                'transaction_type' => CashflowTransaction::TYPE_INFLOW,
                'category' => CashflowTransaction::CATEGORY_OPERATING,
                'subcategory' => 'Fine Payment',
                'description' => "Fine payment received from {$memberName} - {$fine->reason}",
                'amount' => $fine->amount,
                'reference_type' => CashflowTransaction::REFERENCE_FINE_PAYMENT,
                'reference_id' => $fine->id,
                'reference_number' => 'FINE-' . str_pad($fine->id, 6, '0', STR_PAD_LEFT),
                'payment_method' => 'cash',
                'status' => CashflowTransaction::STATUS_CLEARED,
                'fiscal_year_id' => $fine->fiscal_year_id,
                'member_id' => $fine->member_id,
                'created_by' => auth()->id(),
                'notes' => "Fine payment for: {$fine->description}. FinePayment ID: {$finePayment->id}"
            ]);
        }
    }

    /**
     * Handle the Fine "deleted" event.
     */
    public function deleted(Fine $fine): void
    {
        // Delete corresponding cashflow transactions
        CashflowTransaction::where('reference_type', CashflowTransaction::REFERENCE_FINE_PAYMENT)
            ->where('reference_id', $fine->id)
            ->delete();
    }
}
