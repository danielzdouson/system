<?php

namespace App\Services;

use App\Models\CarryForwardRecord;
use App\Models\FiscalYear;
use App\Models\Loan;
use App\Models\Fine;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FiscalYearCarryForwardService
{
    /**
     * Carry forward all eligible items from old fiscal year to new fiscal year
     */
    public function carryForwardAll(FiscalYear $fromFiscalYear, FiscalYear $toFiscalYear, int $userId): array
    {
        $results = [
            'loans' => $this->carryForwardLoans($fromFiscalYear, $toFiscalYear, $userId),
            'fines' => $this->carryForwardFines($fromFiscalYear, $toFiscalYear, $userId),
            'investments' => $this->carryForwardInvestments($fromFiscalYear, $toFiscalYear, $userId),
        ];

        return $results;
    }

    /**
     * Identify items that need to be carried forward
     */
    public function identifyCarryForwardItems(FiscalYear $fromFiscalYear, FiscalYear $toFiscalYear): array
    {
        return [
            'loans' => $this->getEligibleLoans($fromFiscalYear),
            'fines' => $this->getEligibleFines($fromFiscalYear),
            'investments' => $this->getEligibleInvestments($fromFiscalYear, $toFiscalYear),
        ];
    }

    /**
     * Carry forward active loans
     */
    public function carryForwardLoans(FiscalYear $fromFiscalYear, FiscalYear $toFiscalYear, int $userId): array
    {
        $loans = $this->getEligibleLoans($fromFiscalYear);
        $results = [];

        foreach ($loans as $loan) {
            try {
                DB::beginTransaction();

                // Create carry forward record
                $record = $this->createCarryForwardRecord(
                    $fromFiscalYear->id,
                    $toFiscalYear->id,
                    CarryForwardRecord::TYPE_LOAN,
                    $loan->id,
                    "Loan #{$loan->loan_number} carried forward",
                    $loan->balance,
                    $userId
                );

                // Update loan with new fiscal year and carry forward info
                $loan->update([
                    'fiscal_year_id' => $toFiscalYear->id,
                    'carried_forward_from_fiscal_year_id' => $fromFiscalYear->id,
                    'original_fiscal_year_id' => $loan->original_fiscal_year_id ?? $fromFiscalYear->id,
                    'is_carried_forward' => true,
                    'carried_forward_at' => now(),
                ]);

                $record->markAsCompleted();
                $results[] = ['success' => true, 'loan' => $loan, 'record' => $record];

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to carry forward loan {$loan->id}: " . $e->getMessage());
                
                if (isset($record)) {
                    $record->markAsFailed($e->getMessage());
                }
                
                $results[] = ['success' => false, 'loan' => $loan, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Carry forward unpaid fines
     */
    public function carryForwardFines(FiscalYear $fromFiscalYear, FiscalYear $toFiscalYear, int $userId): array
    {
        $fines = $this->getEligibleFines($fromFiscalYear);
        $results = [];

        foreach ($fines as $fine) {
            try {
                DB::beginTransaction();

                // Create carry forward record
                $record = $this->createCarryForwardRecord(
                    $fromFiscalYear->id,
                    $toFiscalYear->id,
                    CarryForwardRecord::TYPE_FINE,
                    $fine->id,
                    "Fine for {$fine->reason} carried forward",
                    $fine->amount,
                    $userId
                );

                // Update fine with new fiscal year and carry forward info
                $fine->update([
                    'fiscal_year_id' => $toFiscalYear->id,
                    'carried_forward_from_fiscal_year_id' => $fromFiscalYear->id,
                    'original_fiscal_year_id' => $fine->original_fiscal_year_id ?? $fromFiscalYear->id,
                    'is_carried_forward' => true,
                    'carried_forward_at' => now(),
                ]);

                $record->markAsCompleted();
                $results[] = ['success' => true, 'fine' => $fine, 'record' => $record];

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to carry forward fine {$fine->id}: " . $e->getMessage());
                
                if (isset($record)) {
                    $record->markAsFailed($e->getMessage());
                }
                
                $results[] = ['success' => false, 'fine' => $fine, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Carry forward long-term investments
     */
    public function carryForwardInvestments(FiscalYear $fromFiscalYear, FiscalYear $toFiscalYear, int $userId): array
    {
        $investments = $this->getEligibleInvestments($fromFiscalYear, $toFiscalYear);
        $results = [];

        foreach ($investments as $investment) {
            try {
                DB::beginTransaction();

                // Create carry forward record
                $record = $this->createCarryForwardRecord(
                    $fromFiscalYear->id,
                    $toFiscalYear->id,
                    CarryForwardRecord::TYPE_INVESTMENT,
                    $investment->id,
                    "Investment '{$investment->name}' marked as long-term",
                    $investment->current_value,
                    $userId
                );

                // Update investment with carry forward info
                $investment->update([
                    'is_long_term_investment' => true,
                    'carry_forward_notes' => "Carried forward from fiscal year {$fromFiscalYear->name} to {$toFiscalYear->name}",
                ]);

                $record->markAsCompleted();
                $results[] = ['success' => true, 'investment' => $investment, 'record' => $record];

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to carry forward investment {$investment->id}: " . $e->getMessage());
                
                if (isset($record)) {
                    $record->markAsFailed($e->getMessage());
                }
                
                $results[] = ['success' => false, 'investment' => $investment, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Get eligible loans for carry forward
     */
    private function getEligibleLoans(FiscalYear $fiscalYear): \Illuminate\Database\Eloquent\Collection
    {
        return Loan::where('fiscal_year_id', $fiscalYear->id)
            ->where('status', Loan::STATUS_ACTIVE)
            ->where('is_carried_forward', false)
            ->get();
    }

    /**
     * Get eligible fines for carry forward
     */
    private function getEligibleFines(FiscalYear $fiscalYear): \Illuminate\Database\Eloquent\Collection
    {
        return Fine::where('fiscal_year_id', $fiscalYear->id)
            ->where('status', 'pending')
            ->where('is_carried_forward', false)
            ->get();
    }

    /**
     * Get eligible investments for carry forward
     */
    private function getEligibleInvestments(FiscalYear $fromFiscalYear, FiscalYear $toFiscalYear): \Illuminate\Database\Eloquent\Collection
    {
        return Investment::where(function ($query) use ($fromFiscalYear, $toFiscalYear) {
            // Investments without fiscal year or from old fiscal year
            $query->whereNull('fiscal_year_id')
                  ->orWhere('fiscal_year_id', $fromFiscalYear->id);
        })
        ->where('status', Investment::STATUS_ACTIVE)
        ->where('is_long_term_investment', false)
        ->where(function ($query) use ($toFiscalYear) {
            // Maturity date extends beyond new fiscal year
            $query->whereNull('maturity_date')
                  ->orWhere('maturity_date', '>', $toFiscalYear->end_date);
        })
        ->get();
    }

    /**
     * Create a carry forward record
     */
    private function createCarryForwardRecord(
        int $fromFiscalYearId,
        int $toFiscalYearId,
        string $itemType,
        int $itemId,
        string $description,
        float $amount,
        int $userId
    ): CarryForwardRecord {
        return CarryForwardRecord::create([
            'from_fiscal_year_id' => $fromFiscalYearId,
            'to_fiscal_year_id' => $toFiscalYearId,
            'item_type' => $itemType,
            'item_id' => $itemId,
            'description' => $description,
            'amount' => $amount,
            'status' => CarryForwardRecord::STATUS_PENDING,
            'created_by' => $userId,
        ]);
    }

    /**
     * Get carry forward summary for fiscal years
     */
    public function getCarryForwardSummary(FiscalYear $fromFiscalYear, FiscalYear $toFiscalYear): array
    {
        $eligible = $this->identifyCarryForwardItems($fromFiscalYear, $toFiscalYear);
        
        return [
            'from_fiscal_year' => $fromFiscalYear,
            'to_fiscal_year' => $toFiscalYear,
            'eligible_items' => $eligible,
            'total_loans' => $eligible['loans']->count(),
            'total_fines' => $eligible['fines']->count(),
            'total_investments' => $eligible['investments']->count(),
            'total_loan_amount' => $eligible['loans']->sum('balance'),
            'total_fine_amount' => $eligible['fines']->sum('amount'),
            'total_investment_value' => $eligible['investments']->sum('current_value'),
        ];
    }

    /**
     * Check if carry forward is needed between fiscal years
     */
    public function isCarryForwardNeeded(FiscalYear $fromFiscalYear, FiscalYear $toFiscalYear): bool
    {
        $eligible = $this->identifyCarryForwardItems($fromFiscalYear, $toFiscalYear);
        
        return $eligible['loans']->count() > 0 || 
               $eligible['fines']->count() > 0 || 
               $eligible['investments']->count() > 0;
    }

    /**
     * Get carry forward history
     */
    public function getCarryForwardHistory(FiscalYear $fiscalYear = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = CarryForwardRecord::with(['fromFiscalYear', 'toFiscalYear', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($fiscalYear) {
            $query->where(function ($q) use ($fiscalYear) {
                $q->where('from_fiscal_year_id', $fiscalYear->id)
                  ->orWhere('to_fiscal_year_id', $fiscalYear->id);
            });
        }

        return $query->get();
    }
}
