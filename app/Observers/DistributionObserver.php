<?php

namespace App\Observers;

use App\Models\Distribution;
use App\Models\CashflowTransaction;
use App\Models\FiscalYear;

class DistributionObserver
{
    /**
     * Handle the Distribution "created" event.
     */
    public function created(Distribution $distribution): void
    {
        // Get deposit info for context
        $deposit = $distribution->deposit;
        $member = $deposit?->member;
        $memberName = $member ? "{$member->first_name} {$member->last_name}" : 'Unknown Member';

        // Map distribution types to appropriate categories and descriptions
        $typeMapping = [
            'savings' => [
                'subcategory' => 'Savings Distribution',
                'description' => "Savings allocation for {$memberName}",
            ],
            'welfare' => [
                'subcategory' => 'Welfare Fund Allocation',
                'description' => "Welfare fund allocation for {$memberName}",
            ],
            'fines' => [
                'subcategory' => 'Fine Payment Distribution',
                'description' => "Fine payment from {$memberName}",
            ],
            'other' => [
                'subcategory' => 'Other Distribution',
                'description' => "Other fund distribution for {$memberName}",
            ],
        ];

        $mapping = $typeMapping[$distribution->type] ?? $typeMapping['other'];

        // Create cashflow transaction for the distribution (OUTFLOW)
        CashflowTransaction::create([
            'transaction_date' => $distribution->created_at,
            'transaction_type' => CashflowTransaction::TYPE_OUTFLOW,
            'category' => CashflowTransaction::CATEGORY_OPERATING,
            'subcategory' => $mapping['subcategory'],
            'description' => $distribution->description ?: $mapping['description'],
            'amount' => $distribution->amount,
            'reference_type' => 'DISTRIBUTION',
            'reference_id' => $distribution->id,
            'reference_number' => 'DIST-' . str_pad($distribution->id, 6, '0', STR_PAD_LEFT),
            'payment_method' => 'internal_transfer',
            'status' => CashflowTransaction::STATUS_CLEARED,
            'fiscal_year_id' => $distribution->fiscal_year_id,
            'member_id' => $deposit?->member_id,
            'created_by' => $distribution->created_by,
            'notes' => "Distribution from deposit #{$deposit?->id} for {$distribution->type}"
        ]);
    }

    /**
     * Handle the Distribution "updated" event.
     */
    public function updated(Distribution $distribution): void
    {
        // Update corresponding cashflow transaction if amount changed
        if ($distribution->wasChanged('amount')) {
            $cashflow = CashflowTransaction::where('reference_type', 'DISTRIBUTION')
                ->where('reference_id', $distribution->id)
                ->first();

            if ($cashflow) {
                $cashflow->update([
                    'amount' => $distribution->amount,
                    'notes' => 'Updated due to distribution modification: ' . $distribution->description
                ]);
            }
        }
    }

    /**
     * Handle the Distribution "deleted" event.
     */
    public function deleted(Distribution $distribution): void
    {
        // Delete corresponding cashflow transaction
        CashflowTransaction::where('reference_type', 'DISTRIBUTION')
            ->where('reference_id', $distribution->id)
            ->delete();
    }
}
