<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FindDuplicateDistributions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'distributions:find-duplicates {--fix : Automatically fix duplicates by keeping the first and deleting others}';

    /**
     * The console command description.
     */
    protected $description = 'Find and optionally fix duplicate distribution records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Searching for duplicate distributions...');

        // Find distributions with same deposit_id, type, amount, and fiscal_year_id
        $duplicates = DB::table('distributions')
            ->select(
                'deposit_id',
                'type',
                'amount',
                'fiscal_year_id',
                DB::raw('COUNT(*) as count'),
                DB::raw('GROUP_CONCAT(id ORDER BY id) as ids')
            )
            ->groupBy('deposit_id', 'type', 'amount', 'fiscal_year_id')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✓ No duplicate distributions found!');
            return 0;
        }

        $this->warn("Found {$duplicates->count()} sets of duplicate distributions:");
        $this->newLine();

        $totalDuplicates = 0;

        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);
            $duplicateCount = count($ids) - 1;
            $totalDuplicates += $duplicateCount;

            // Get deposit info
            $deposit = DB::table('deposits')->find($duplicate->deposit_id);
            $member = $deposit ? DB::table('members')->find($deposit->member_id) : null;
            $memberName = $member ? "{$member->first_name} {$member->last_name}" : "Unknown";

            $this->line("Member: {$memberName}");
            $this->line("Deposit ID: {$duplicate->deposit_id}");
            $this->line("Type: {$duplicate->type}");
            $this->line("Amount: {$duplicate->amount}");
            $this->line("Duplicate IDs: {$duplicate->ids}");
            $this->line("Count: {$duplicate->count} records (keeping first, removing " . $duplicateCount . ")");
            $this->newLine();

            // If --fix flag is provided, delete duplicates
            if ($this->option('fix')) {
                // Keep the first ID, delete the rest
                $idsToDelete = array_slice($ids, 1);
                
                foreach ($idsToDelete as $idToDelete) {
                    DB::table('distributions')->where('id', $idToDelete)->delete();
                    $this->info("  ✓ Deleted duplicate distribution ID: {$idToDelete}");
                }

                // Recalculate deposit balance
                if ($deposit) {
                    $totalDistributed = DB::table('distributions')
                        ->where('deposit_id', $duplicate->deposit_id)
                        ->sum('amount');
                    
                    DB::table('deposits')
                        ->where('id', $duplicate->deposit_id)
                        ->update(['balance' => $deposit->amount - $totalDistributed]);
                    
                    $this->info("  ✓ Recalculated deposit balance");
                }
            }
        }

        $this->newLine();
        $this->warn("Total duplicate records found: {$totalDuplicates}");

        if (!$this->option('fix')) {
            $this->newLine();
            $this->info('To automatically fix these duplicates, run:');
            $this->line('php artisan distributions:find-duplicates --fix');
        } else {
            $this->newLine();
            $this->info("✓ Fixed {$totalDuplicates} duplicate distributions!");
        }

        return 0;
    }
}
