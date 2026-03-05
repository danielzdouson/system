<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Distribution Form Debug ===\n\n";

use App\Models\Deposit;
use App\Models\MemberAccount;

// Test 1: Check a specific deposit
echo "1. Checking deposit #3:\n";
$deposit = Deposit::find(3);

if ($deposit) {
    echo "   Deposit Found:\n";
    echo "   - ID: {$deposit->id}\n";
    echo "   - Member ID: {$deposit->member_id}\n";
    echo "   - Amount: {$deposit->amount}\n";
    echo "   - Balance: {$deposit->balance}\n";
    echo "   - Status: {$deposit->status}\n";
    echo "   - Month: {$deposit->month}\n";
    echo "   - Fiscal Year ID: {$deposit->fiscal_year_id}\n";
    
    // Check member account
    $memberAccount = MemberAccount::where('member_id', $deposit->member_id)
        ->where('fiscal_year_id', $deposit->fiscal_year_id)
        ->first();
    
    if ($memberAccount) {
        echo "   Member Account:\n";
        echo "   - Current Balance: {$memberAccount->current_balance}\n";
        echo "   - Total Deposited: {$memberAccount->total_deposited}\n";
        echo "   - Total Distributed: {$memberAccount->total_distributed}\n";
    } else {
        echo "   ❌ Member Account NOT FOUND!\n";
    }
    
    // Check distributions
    echo "   Distributions: " . $deposit->distributions->count() . "\n";
    foreach ($deposit->distributions as $dist) {
        echo "      - {$dist->type}: {$dist->amount} (Month: {$dist->month})\n";
    }
    
} else {
    echo "   ❌ Deposit #3 NOT FOUND!\n";
}

echo "\n";

// Test 2: Check all deposits with balance
echo "2. All deposits with available balance:\n";
$depositsWithBalance = Deposit::where('balance', '>', 0)->limit(5)->get();

foreach ($depositsWithBalance as $dep) {
    echo "   Deposit #{$dep->id}: Member {$dep->member_id}, Balance {$dep->balance}\n";
}

echo "\n";

// Test 3: Check form validation requirements
echo "3. Form validation requirements:\n";
echo "   - savings_amount: required|numeric|min:0\n";
echo "   - welfare_amount: required|numeric|min:0\n";
echo "   - fines_amount: required|numeric|min:0\n";
echo "   - other_amount: required|numeric|min:0\n";
echo "   - distribution_month: required|integer|min:1|max:12\n";
echo "   - distribution_note: nullable|string|max:255\n";

echo "\n=== Debug Complete ===\n";
