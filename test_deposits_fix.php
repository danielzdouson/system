<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Total Deposits Fix...\n\n";

// Test 1: Check CashflowTransaction deposits
$cashflowDeposits = \App\Models\CashflowTransaction::where('reference_type', 'DEPOSIT')->sum('amount');
echo "CashflowTransaction deposits: " . number_format($cashflowDeposits, 2) . "\n";

// Test 2: Check regular deposits
$regularDeposits = \App\Models\Deposit::sum('amount');
echo "Regular deposits: " . number_format($regularDeposits, 2) . "\n";

// Test 3: Check combined total
$combinedTotal = $regularDeposits + $cashflowDeposits;
echo "Combined total: " . number_format($combinedTotal, 2) . "\n\n";

// Test 4: Test FiscalYear model
$activeYear = \App\Models\FiscalYear::getActive();
if ($activeYear) {
    echo "Active Fiscal Year: " . $activeYear->name . "\n";
    echo "Fiscal Year Total Deposits (Fixed): " . number_format($activeYear->total_deposits, 2) . "\n";
} else {
    echo "No active fiscal year found\n";
}

echo "\nTest completed!\n";
