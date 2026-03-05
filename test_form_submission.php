<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Form Submission Debug ===\n\n";

use App\Models\Deposit;
use App\Models\MemberAccount;

// Test 1: Check deposit #3 details
echo "1. Checking deposit #3:\n";
$deposit = Deposit::find(3);

if ($deposit) {
    echo "   ✅ Deposit Found:\n";
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
        echo "   ✅ Member Account Found:\n";
        echo "   - Current Balance: {$memberAccount->current_balance}\n";
        echo "   - Total Deposited: {$memberAccount->total_deposited}\n";
        echo "   - Total Distributed: {$memberAccount->total_distributed}\n";
    } else {
        echo "   ❌ Member Account NOT FOUND!\n";
        echo "   This could cause the form submission to fail!\n";
    }
    
} else {
    echo "   ❌ Deposit #3 NOT FOUND!\n";
    echo "   This would cause a 404 error!\n";
}

echo "\n";

// Test 2: Simulate form validation
echo "2. Testing form validation rules:\n";
$testData = [
    'savings_amount' => 1000,
    'welfare_amount' => 500,
    'fines_amount' => 0,
    'other_amount' => 0,
    'distribution_month' => 3,
    'distribution_note' => 'Test distribution'
];

echo "   Test data: " . json_encode($testData) . "\n";

// Check validation rules
$rules = [
    'savings_amount' => 'required|numeric|min:0',
    'welfare_amount' => 'required|numeric|min:0',
    'fines_amount' => 'required|numeric|min:0',
    'other_amount' => 'required|numeric|min:0',
    'distribution_month' => 'required|integer|min:1|max:12',
    'distribution_note' => 'nullable|string|max:255',
];

foreach ($rules as $field => $rule) {
    $value = $testData[$field] ?? null;
    echo "   - {$field}: {$value} (Rule: {$rule})\n";
}

echo "\n";

// Test 3: Check route exists
echo "3. Checking if distribution route exists:\n";
$routeName = 'admin.group-savings.store-distribution';
echo "   Route: {$routeName}\n";
echo "   Expected URL: /admin/group-savings/distribute/3 (POST)\n";

echo "\n=== Debug Complete ===\n";
echo "\nIf the form is refreshing without saving, check:\n";
echo "1. Browser console for JavaScript errors\n";
echo "2. Network tab for failed requests\n";
echo "3. Laravel logs for server errors\n";
echo "4. Member account existence (shown above)\n";
