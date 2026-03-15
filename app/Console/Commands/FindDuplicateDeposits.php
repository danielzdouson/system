<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FindDuplicateDeposits extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'deposits:find-duplicates {--fix : Automatically fix duplicates by keeping the first and deleting others}';

    /**
     * The console command description.
     */
    protected $description = 'Find and optionally fix duplicate deposit records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Searching for duplicate deposits...');

        // Find deposits with same member_id, fiscal_year_id, deposit_date, and amount
        $duplicates = DB::table('deposits')
            ->select(
                'member_id',
                'fiscal_year_id',
                'deposit_date',
                'amount',
                DB::raw('COUNT(*) as count'),
                DB::raw('GROUP_CONCAT(id ORDER BY id) as ids')
            )
            ->groupBy('member_id', 'fiscal_year_id', 'deposit_date', 'amount')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✓ No duplicate deposits found!');
            return 0;
        }

        $this->warn("Found {$duplicates->count()} sets of duplicate deposits:");
        $this->newLine();

        $totalDuplicates = 0;

        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);
            $duplicateCount = count($ids) - 1; // Subtract 1 because we keep the first
            $totalDuplicates += $duplicateCount;

            // Get member name
            $member = DB::table('members')->find($duplicate->member_id);
            $memberName = $member ? "{$member->first_name} {$member->last_name}" : "Unknown";

            $this->line("Member: {$memberName}");
            $this->line("Amount: {$duplicate->amount}");
            $this->line("Date: {$duplicate->deposit_date}");
            $this->line("Duplicate IDs: {$duplicate->ids}");
            $this->line("Count: {$duplicate->count} records (keeping first, removing " . $duplicateCount . ")");
            $this->newLine();

            // If --fix flag is provided, delete duplicates
            if ($this->option('fix')) {
                // Keep the first ID, delete the rest
                $idsToDelete = array_slice($ids, 1);
                
                foreach ($idsToDelete as $idToDelete) {
                    // Delete associated distributions first
                    DB::table('distributions')->where('deposit_id', $idToDelete)->delete();
                    
                    // Delete the duplicate deposit
                    DB::table('deposits')->where('id', $idToDelete)->delete();
                    
                    $this->info("  ✓ Deleted duplicate deposit ID: {$idToDelete}");
                }
            }
        }

        $this->newLine();
        $this->warn("Total duplicate records found: {$totalDuplicates}");

        if (!$this->option('fix')) {
            $this->newLine();
            $this->info('To automatically fix these duplicates, run:');
            $this->line('php artisan deposits:find-duplicates --fix');
        } else {
            $this->newLine();
            $this->info("✓ Fixed {$totalDuplicates} duplicate deposits!");
        }

        return 0;
    }
}
