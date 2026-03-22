<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Savings System Calculation Test ===\n\n";

use App\Models\FiscalYear;
use App\Models\Deposit;
use App\Models\MemberAccount;
use App\Models\Distribution;
use App\Models\GroupSaving;
use App\Models\WelfareFund;
use App\Models\Fine;

// Test 1: Fiscal Year Totals
echo "1. Testing Fiscal Year Totals:\n";
$activeYear = FiscalYear::getActive();

if ($activeYear) {
    echo "   Active Fiscal Year: {$activeYear->name}\n";
    echo "   Total Deposits: " . number_format($activeYear->total_deposits, 2) . "\n";
    echo "   Total Savings (from distributions): " . number_format($activeYear->total_savings, 2) . "\n";
    echo "   Total Welfare (from distributions): " . number_format($activeYear->total_welfare, 2) . "\n";
    echo "   Total Fines (from distributions): " . number_format($activeYear->total_fines, 2) . "\n";
    echo "   Total Other (from distributions): " . number_format($activeYear->total_other, 2) . "\n";
    echo "   Total Distributed: " . number_format($activeYear->total_distributed, 2) . "\n";
    
    // Verify balance
    $expectedBalance = $activeYear->total_deposits - $activeYear->total_distributed;
    echo "   Expected Balance: " . number_format($expectedBalance, 2) . "\n";
    
    if (abs($expectedBalance) < 0.01) {
        echo "   ✅ BALANCE CHECK: PASSED\n";
    } else {
        echo "   ❌ BALANCE CHECK: FAILED - Balance should be zero\n";
    }
} else {
    echo "   No active fiscal year found\n";
}

echo "\n";

// Test 2: Deposit Balance Consistency
echo "2. Testing Deposit Balance Consistency:\n";
$deposits = Deposit::with('distributions')->get();
$inconsistentDeposits = [];

foreach ($deposits as $deposit) {
    $actualDistributed = $deposit->distributions->sum('amount');
    $expectedBalance = $deposit->amount - $actualDistributed;
    
    if (abs($deposit->balance - $expectedBalance) > 0.01) {
        $inconsistentDeposits[] = [
            'id' => $deposit->id,
            'amount' => $deposit->amount,
            'current_balance' => $deposit->balance,
            'expected_balance' => $expectedBalance,
            'distributed' => $actualDistributed
        ];
    }
}

if (empty($inconsistentDeposits)) {
    echo "   ✅ All deposit balances are consistent\n";
} else {
    echo "   ❌ Found " . count($inconsistentDeposits) . " inconsistent deposits:\n";
    foreach ($inconsistentDeposits as $dep) {
        echo "      Deposit #{$dep['id']}: Current {$dep['current_balance']}, Expected {$dep['expected_balance']}\n";
    }
}

echo "\n";

// Test 3: Member Account Balance Consistency
echo "3. Testing Member Account Balance Consistency:\n";
$memberAccounts = MemberAccount::all();
$inconsistentAccounts = [];

foreach ($memberAccounts as $account) {
    // Calculate expected values from deposits and distributions
    $totalDeposited = Deposit::where('member_id', $account->member_id)
        ->where('fiscal_year_id', $account->fiscal_year_id)
        ->sum('amount');
    
    $totalDistributed = Distribution::whereHas('deposit', function($query) use ($account) {
        $query->where('member_id', $account->member_id)
              ->where('fiscal_year_id', $account->fiscal_year_id);
    })->sum('amount');
    
    $expectedBalance = $totalDeposited - $totalDistributed;
    
    if (abs($account->current_balance - $expectedBalance) > 0.01 ||
        abs($account->total_deposited - $totalDeposited) > 0.01 ||
        abs($account->total_distributed - $totalDistributed) > 0.01) {
        
        $inconsistentAccounts[] = [
            'member_id' => $account->member_id,
            'fiscal_year_id' => $account->fiscal_year_id,
            'current_balance' => $account->current_balance,
            'expected_balance' => $expectedBalance,
            'total_deposited' => $account->total_deposited,
            'expected_deposited' => $totalDeposited,
            'total_distributed' => $account->total_distributed,
            'expected_distributed' => $totalDistributed
        ];
    }
}

if (empty($inconsistentAccounts)) {
    echo "   ✅ All member account balances are consistent\n";
} else {
    echo "   ❌ Found " . count($inconsistentAccounts) . " inconsistent member accounts:\n";
    foreach ($inconsistentAccounts as $acc) {
        echo "      Member #{$acc['member_id']} (FY {$acc['fiscal_year_id']}):\n";
        echo "         Balance: Current {$acc['current_balance']}, Expected {$acc['expected_balance']}\n";
        echo "         Deposited: Current {$acc['total_deposited']}, Expected {$acc['expected_deposited']}\n";
        echo "         Distributed: Current {$acc['total_distributed']}, Expected {$acc['expected_distributed']}\n";
    }
}

echo "\n";

// Test 4: Distribution vs GroupSaving/WelfareFund Consistency
echo "4. Testing Distribution vs Record Consistency:\n";

if ($activeYear) {
    // Check savings distributions vs GroupSaving records
    $savingsDistributions = Distribution::whereHas('deposit', function($query) use ($activeYear) {
        $query->where('fiscal_year_id', $activeYear->id);
    })->where('type', 'savings')->sum('amount');
    
    $groupSavingsTotal = GroupSaving::where('fiscal_year_id', $activeYear->id)->sum('amount');
    
    echo "   Savings Distributions: " . number_format($savingsDistributions, 2) . "\n";
    echo "   Group Savings Total: " . number_format($groupSavingsTotal, 2) . "\n";
    
    if (abs($savingsDistributions - $groupSavingsTotal) < 0.01) {
        echo "   ✅ Savings distributions match GroupSaving records\n";
    } else {
        echo "   ❌ Savings distributions don't match GroupSaving records\n";
        echo "      Difference: " . number_format(abs($savingsDistributions - $groupSavingsTotal), 2) . "\n";
    }
    
    // Check welfare distributions vs WelfareFund records
    $welfareDistributions = Distribution::whereHas('deposit', function($query) use ($activeYear) {
        $query->where('fiscal_year_id', $activeYear->id);
    })->where('type', 'welfare')->sum('amount');
    
    $welfareFundsTotal = WelfareFund::where('fiscal_year_id', $activeYear->id)->sum('amount');
    
    echo "   Welfare Distributions: " . number_format($welfareDistributions, 2) . "\n";
    echo "   Welfare Funds Total: " . number_format($welfareFundsTotal, 2) . "\n";
    
    if (abs($welfareDistributions - $welfareFundsTotal) < 0.01) {
        echo "   ✅ Welfare distributions match WelfareFund records\n";
    } else {
        echo "   ❌ Welfare distributions don't match WelfareFund records\n";
        echo "      Difference: " . number_format(abs($welfareDistributions - $welfareFundsTotal), 2) . "\n";
    }
}

echo "\n";

// Test 5: Virtual Deposit Detection
echo "5. Testing Virtual Deposit Detection:\n";
$virtualDeposits = Deposit::where('status', 'virtual')->get();
echo "   Found " . count($virtualDeposits) . " virtual deposits\n";

foreach ($virtualDeposits as $virtual) {
    echo "      Virtual Deposit #{$virtual->id}: Member {$virtual->member_id}, Month {$virtual->month}, Amount {$virtual->amount}\n";
}

echo "\n=== Test Complete ===\n";
