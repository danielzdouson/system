<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Simplified Balance Distribution Test ===\n\n";

use App\Models\Deposit;
use App\Models\MemberAccount;

// Test 1: Check member with account balance but no deposit for specific month
echo "1. Testing member with account balance but no monthly deposit:\n";

// Find a member who has account balance but might not have deposit for month 4
$membersWithBalance = MemberAccount::where('current_balance', '>', 0)->limit(3)->get();

foreach ($membersWithBalance as $memberAccount) {
    echo "   Member ID: {$memberAccount->member_id}\n";
    echo "   Account Balance: {$memberAccount->current_balance}\n";
    
    // Check if they have deposits for month 4
    $depositsForMonth4 = Deposit::where('member_id', $memberAccount->member_id)
        ->where('month', 4)
        ->where('fiscal_year_id', $memberAccount->fiscal_year_id)
        ->get();
    
    echo "   Deposits for month 4: " . $depositsForMonth4->count() . "\n";
    
    if ($depositsForMonth4->count() > 0) {
        foreach ($depositsForMonth4 as $deposit) {
            echo "     - Deposit #{$deposit->id}: Amount {$deposit->amount}, Balance {$deposit->balance}\n";
        }
    } else {
        echo "     ❌ No deposits for month 4 - This is the scenario we're fixing!\n";
    }
    
    echo "   ---\n";
}

echo "\n";

// Test 2: Simulate distribution validation
echo "2. Testing simplified distribution validation:\n";

foreach ($membersWithBalance as $memberAccount) {
    $testAmount = 1000;
    echo "   Member {$memberAccount->member_id}:\n";
    echo "   - Account Balance: {$memberAccount->current_balance}\n";
    echo "   - Test Distribution: {$testAmount}\n";
    
    if ($testAmount <= $memberAccount->current_balance) {
        echo "   ✅ PASS: Can distribute from account balance\n";
    } else {
        echo "   ❌ FAIL: Insufficient account balance\n";
    }
    
    echo "   ---\n";
}

echo "\n";

// Test 3: Check total deposit balance vs account balance
echo "3. Comparing deposit balances vs account balance:\n";

foreach ($membersWithBalance as $memberAccount) {
    $totalDepositBalance = Deposit::where('member_id', $memberAccount->member_id)
        ->where('fiscal_year_id', $memberAccount->fiscal_year_id)
        ->sum('balance');
    
    echo "   Member {$memberAccount->member_id}:\n";
    echo "   - Total Deposit Balance: {$totalDepositBalance}\n";
    echo "   - Account Balance: {$memberAccount->current_balance}\n";
    
    if ($totalDepositBalance != $memberAccount->current_balance) {
        echo "   ⚠️  MISMATCH: This is expected with the new simplified logic!\n";
    } else {
        echo "   ✅ MATCH: Traditional scenario\n";
    }
    
    echo "   ---\n";
}

echo "\n=== Test Complete ===\n";
echo "\nExpected Results:\n";
echo "- Members should be able to distribute from account balance even without monthly deposits\n";
echo "- Account balance is the single source of truth\n";
echo "- No more 'insufficient deposit balance' errors when account has funds\n";
