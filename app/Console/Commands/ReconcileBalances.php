<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\BalanceReconciliationSeeder;

class ReconcileBalances extends Command
{
    protected $signature = 'balances:reconcile {--force : Force reconciliation without confirmation}';
    protected $description = 'Reconcile all savings system balances and fix calculation errors';

    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will reconcile all deposit and member account balances. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting balance reconciliation...');
        
        try {
            $seeder = new BalanceReconciliationSeeder();
            $seeder->setCommand($this);
            $seeder->run();
            
            $this->info('✅ Balance reconciliation completed successfully!');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Reconciliation failed: ' . $e->getMessage());
            return 1;
        }
    }
}
