<?php

echo "=== Debugging Fine Confirmation Issue ===\n\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "🔍 Checking session data and document flow...\n";
    
    // Get a document with fine
    $document = \App\Models\Document::where('requires_fine', true)->first();
    if (!$document) {
        echo "❌ No document with fine found\n";
        exit;
    }
    
    echo "✅ Found document: " . $document->title . "\n";
    echo "   Fine Amount: UGX " . number_format($document->fine_amount, 2) . "\n\n";
    
    // Get a test member
    $member = \App\Models\Member::first();
    if (!$member) {
        echo "❌ No member found\n";
        exit;
    }
    
    echo "✅ Test member: " . $member->first_name . " " . $member->last_name . "\n\n";
    
    // Simulate the controller logic
    echo "🔄 Simulating download request...\n";
    
    // Check if already downloaded
    $existingDownload = \App\Models\DocumentDownload::where('document_id', $document->id)
        ->where('member_id', $member->id)
        ->first();
    
    if ($existingDownload) {
        echo "   📄 Already downloaded - should show download button\n";
    } else {
        echo "   📄 Not downloaded - should show fine confirmation\n";
        
        // This is what the controller does when fine confirmation is needed
        echo "   🎯 Controller would set session data:\n";
        echo "      - warning: 'This document requires a fine of UGX " . number_format($document->fine_amount, 2) . ". Please confirm to proceed.'\n";
        echo "      - show_fine_confirmation: true\n\n";
        
        echo "   💡 The view should show:\n";
        echo "      - Alert with fine amount\n";
        echo "      - 'Confirm & Pay Fine' button\n";
        echo "      - 'Cancel' button\n\n";
        
        echo "   🔧 Possible issues:\n";
        echo "      1. Session data not being set properly\n";
        echo "      2. View condition not matching\n";
        echo "      3. Bootstrap CSS not loading properly\n";
        echo "      4. Form submission not working\n\n";
        
        // Let's create a simplified version of the confirmation
        echo "   📝 Creating simplified confirmation test...\n";
        
        // Test if we can manually set session and check view
        session(['show_fine_confirmation' => true]);
        session(['warning' => 'This document requires a fine of UGX ' . number_format($document->fine_amount, 2) . '. Please confirm to proceed.']);
        
        echo "   ✅ Session data set manually\n";
        echo "   📋 Session contents:\n";
        echo "      - show_fine_confirmation: " . (session('show_fine_confirmation') ? 'true' : 'false') . "\n";
        echo "      - warning: " . session('warning') . "\n";
    }
    
    echo "\n🌐 Testing the actual URL...\n";
    echo "   URL: /member/documents/" . $document->id . "\n";
    echo "   Expected: Fine confirmation prompt with buttons\n\n";
    
    echo "🔧 Quick Fix Suggestions:\n";
    echo "1. Check browser console for JavaScript errors\n";
    echo "2. Verify Bootstrap CSS is loading\n";
    echo "3. Check if session is being cleared prematurely\n";
    echo "4. Test with a simplified confirmation form\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "If you see the message but no buttons:\n";
echo "1. The session('show_fine_confirmation') might not be set\n";
echo "2. The @if condition in the view might not be working\n";
echo "3. CSS might be hiding the buttons\n";
echo "4. JavaScript might be interfering\n\n";

echo "Let me create a simpler version that always shows the confirmation...\n";
