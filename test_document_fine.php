<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;
use App\Models\Member;
use App\Models\DocumentDownload;
use App\Models\FiscalYear;

echo "Testing document download fine creation...\n\n";

// Check if we have documents that require fines
$documentsWithFines = Document::where('requires_fine', true)->get();
echo "Found {$documentsWithFines->count()} documents that require fines\n";

if ($documentsWithFines->count() > 0) {
    foreach ($documentsWithFines as $doc) {
        echo "- {$doc->title} (Fine: UGX {$doc->fine_amount})\n";
    }
}

// Check if we have members
$membersCount = Member::count();
echo "\nFound {$membersCount} members in the system\n";

// Check fiscal year
$activeFiscalYear = FiscalYear::where('status', 'active')
    ->orWhere(function($query) {
        $query->where('start_date', '<=', now())
              ->where('end_date', '>=', now());
    })
    ->first();

if ($activeFiscalYear) {
    echo "Active fiscal year: {$activeFiscalYear->name}\n";
} else {
    $latestFiscalYear = FiscalYear::latest()->first();
    if ($latestFiscalYear) {
        echo "Using latest fiscal year: {$latestFiscalYear->name}\n";
    } else {
        echo "No fiscal years found - this may cause issues\n";
    }
}

// Test the applyFine method logic
echo "\nTesting fine creation logic...\n";

if ($documentsWithFines->count() > 0 && $membersCount > 0) {
    $testDoc = $documentsWithFines->first();
    $testMember = Member::first();
    
    echo "Testing with document: {$testDoc->title}\n";
    echo "Testing with member: {$testMember->first_name} {$testMember->last_name}\n";
    
    // Create a test download record
    $testDownload = DocumentDownload::create([
        'document_id' => $testDoc->id,
        'member_id' => $testMember->id,
        'downloaded_at' => now(),
        'ip_address' => '127.0.0.1',
        'fine_applied' => false,
    ]);
    
    echo "Created test download record\n";
    
    // Test the applyFine method
    try {
        $fine = $testDownload->applyFine();
        if ($fine) {
            echo "✓ Fine created successfully!\n";
            echo "  - Fine ID: {$fine->id}\n";
            echo "  - Amount: UGX {$fine->amount}\n";
            echo "  - Reason: {$fine->reason}\n";
            echo "  - Description: {$fine->description}\n";
            echo "  - Status: {$fine->status}\n";
            echo "  - Fiscal Year ID: {$fine->fiscal_year_id}\n";
            echo "  - Month: {$fine->month}\n";
            
            // Clean up test data
            $fine->delete();
            echo "✓ Test fine deleted\n";
        } else {
            echo "✗ No fine was created\n";
        }
    } catch (Exception $e) {
        echo "✗ Error creating fine: " . $e->getMessage() . "\n";
    }
    
    // Clean up test download
    $testDownload->delete();
    echo "✓ Test download deleted\n";
} else {
    echo "Cannot test - need both documents with fines and members\n";
}

echo "\nTest complete!\n";
