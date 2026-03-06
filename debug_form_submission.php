<?php

echo "=== Debugging Form Submission Issue ===\n\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "🔍 Checking Form Submission Logic...\n\n";
    
    // Get the document and member who can't download
    $document = \App\Models\Document::find(1);
    $member = \App\Models\Member::where('first_name', 'clone2')->first(); // The member who can't download
    
    if (!$document || !$member) {
        echo "❌ Missing test data\n";
        exit;
    }
    
    echo "📋 Document: " . $document->title . "\n";
    echo "📋 Member: " . $member->first_name . " " . $member->last_name . "\n";
    echo "📋 Member ID: " . $member->id . "\n";
    echo "📋 User Email: " . ($member->user ? $member->user->email : 'No user') . "\n\n";
    
    // Check if member has downloaded before
    $hasDownloaded = $document->downloads()
        ->where('member_id', $member->id)
        ->exists();
    
    echo "📊 Download Status:\n";
    echo "   Has Downloaded: " . ($hasDownloaded ? 'YES' : 'NO') . "\n";
    echo "   Requires Fine: " . ($document->requires_fine ? 'YES' : 'NO') . "\n";
    echo "   Fine Amount: UGX " . number_format($document->fine_amount, 2) . "\n\n";
    
    // Simulate the controller logic
    echo "🔄 Simulating Controller Logic:\n";
    echo "   1. Check if document_type === 'loan_form': " . ($document->document_type === 'loan_form' ? 'YES' : 'NO') . "\n";
    
    if ($document->document_type === 'loan_form') {
        echo "   2. Calls handleLoanFormDownload() method\n";
        echo "   3. Checks if confirm_fine parameter is present\n";
        echo "   4. If confirm_fine=1: Creates download record and applies fine\n";
        echo "   5. If confirm_fine missing: Shows fine confirmation prompt\n";
    }
    
    echo "\n🎯 Expected Form Behavior:\n";
    if ($hasDownloaded) {
        echo "   ✅ Should show 'Download Again' button (no JavaScript confirmation)\n";
        echo "   ✅ Should call performDownload() directly\n";
        echo "   ✅ Should download file immediately\n";
    } else {
        echo "   ✅ Should show 'Download & Pay Fine' button\n";
        echo "   ✅ Should show JavaScript confirmation dialog\n";
        echo "   ✅ User must click 'OK' to submit form\n";
        echo "   ✅ If user clicks 'Cancel', form submission blocked\n";
    }
    
    echo "\n🔍 Possible Issues:\n";
    echo "1. User clicking 'Cancel' on JavaScript confirmation\n";
    echo "2. JavaScript error preventing form submission\n";
    echo "3. CSRF token missing or invalid\n";
    echo "4. Form validation failing\n";
    echo "5. Controller logic redirecting back\n";
    echo "6. Session data interfering with form display\n";
    
    echo "\n💡 Debugging Steps:\n";
    echo "1. Check if user is clicking 'OK' or 'Cancel' on confirmation dialog\n";
    echo "2. Check browser console for JavaScript errors during submission\n";
    echo "3. Check Network tab for POST request to /member/documents/1/download\n";
    echo "4. Check if confirm_fine parameter is being sent\n";
    echo "5. Check if there are any Laravel validation errors\n";
    
    echo "\n🧪 Test Scenarios:\n";
    echo "Scenario 1: Member with no downloads (clone2)\n";
    echo "   - Shows 'Download & Pay Fine' button\n";
    echo "   - JavaScript confirmation appears\n";
    echo "   - Must click 'OK' to proceed\n";
    echo "   - If 'Cancel' clicked: page refreshes (expected)\n";
    echo "   - If 'OK' clicked: should process fine and download\n";
    
    echo "\nScenario 2: Member with downloads (Admin User)\n";
    echo "   - Shows 'Download Again' button\n";
    echo "   - No JavaScript confirmation\n";
    echo "   - Should download immediately\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
