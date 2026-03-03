<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Mark the already created tables as migrated
    $completedMigrations = [
        '2025_12_29_164605_create_cache_table',
        '2025_12_29_164840_create_sessions_table', 
        '2025_12_29_164043_create_users_table',
        '2025_02_10_170000_create_investment_portfolios_table',
    ];
    
    $batch = 1;
    foreach ($completedMigrations as $migration) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch
        ]);
        echo "✅ Marked migration: $migration\n";
    }
    
    echo "\n✅ Migration tracking updated!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
