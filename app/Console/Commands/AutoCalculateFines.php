<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Fine;
use App\Models\Member;
use App\Models\FiscalYear;
use App\Models\GroupSaving;
use Carbon\Carbon;

class AutoCalculateFines extends Command
{
    protected $signature = 'fines:calculate {--fiscal-year=} {--month=} {--dry-run}';
    protected $description = 'Automatically calculate and apply fines for missed savings';

    public function handle()
    {
        $fiscalYearId = $this->option('fiscal-year');
        $month = $this->option('month');
        $isDryRun = $this->option('dry-run');

        // Get current fiscal year if none specified
        if (!$fiscalYearId) {
            $currentFiscalYear = FiscalYear::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();
            
            if (!$currentFiscalYear) {
                $this->error('No active fiscal year found. Please specify a fiscal year ID.');
                return 1;
            }
            
            $fiscalYearId = $currentFiscalYear->id;
        }

        // Get current month if none specified
        if (!$month) {
            $month = now()->month;
        }

        $fiscalYear = FiscalYear::find($fiscalYearId);
        if (!$fiscalYear) {
            $this->error("Fiscal year with ID {$fiscalYearId} not found.");
            return 1;
        }

        $this->info("Processing fines for {$fiscalYear->name} - " . Carbon::create()->month($month)->format('F'));
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No fines will actually be created');
        }

        // Get members who missed savings for the specified month
        $membersWithoutSavings = Member::whereDoesntHave('groupSavings', function($query) use ($fiscalYearId, $month) {
            $query->where('fiscal_year_id', $fiscalYearId)
                  ->where('month', $month)
                  ->where('status', 'paid');
        })->get();

        $this->info("Found {$membersWithoutSavings->count()} members without savings for this month");

        $appliedCount = 0;
        $skippedCount = 0;
        $totalAmount = 0;

        foreach ($membersWithoutSavings as $member) {
            // Check if fine already exists
            $existingFine = Fine::where('member_id', $member->id)
                ->where('fiscal_year_id', $fiscalYearId)
                ->where('month', $month)
                ->where('reason', 'missed_saving')
                ->first();

            if ($existingFine) {
                $this->line("Skipping {$member->first_name} {$member->last_name} - Fine already exists");
                $skippedCount++;
                continue;
            }

            $fineAmount = $this->calculateFineAmount($member, $month, $fiscalYear);
            
            if (!$isDryRun) {
                Fine::create([
                    'member_id' => $member->id,
                    'fiscal_year_id' => $fiscalYearId,
                    'month' => $month,
                    'amount' => $fineAmount,
                    'reason' => 'missed_saving',
                    'description' => "Auto-generated fine for missed saving in " . Carbon::create()->month($month)->format('F'),
                    'status' => 'pending',
                    'created_by' => 1 // System user
                ]);
            }

            $totalAmount += $fineAmount;
            $appliedCount++;
            
            $this->line("Applied fine for {$member->first_name} {$member->last_name}: UGX {$fineAmount}");
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("Members processed: {$membersWithoutSavings->count()}");
        $this->line("Fines applied: {$appliedCount}");
        $this->line("Fines skipped: {$skippedCount}");
        $this->line("Total amount: UGX " . number_format($totalAmount, 0));
        
        if ($isDryRun) {
            $this->warn('This was a dry run. Use the command without --dry-run to actually apply fines.');
        }

        return 0;
    }

    private function calculateFineAmount($member, $month, $fiscalYear)
    {
        // Base fine amount
        $baseAmount = 10000;
        
        // You can implement more complex logic here:
        // - Progressive fines for repeat offenders
        // - Different amounts based on member tier
        // - Consider previous payment history
        
        // Example: Check if member has previous missed savings
        $previousMissedCount = Fine::where('member_id', $member->id)
            ->where('reason', 'missed_saving')
            ->where('status', '!=', 'paid')
            ->count();
        
        // Increase fine for repeat offenders
        if ($previousMissedCount >= 3) {
            $baseAmount = 15000;
        } elseif ($previousMissedCount >= 6) {
            $baseAmount = 20000;
        }
        
        return $baseAmount;
    }
}
