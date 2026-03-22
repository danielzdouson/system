<?php

echo "=== Testing Download Button Visibility ===\n\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "🔍 Testing Download Button Visibility for Different Members...\n\n";
    
    // Get the document
    $document = \App\Models\Document::find(1);
    if (!$document) {
        echo "❌ Document ID 1 not found\n";
        exit;
    }
    
    echo "📋 Document: " . $document->title . "\n";
    echo "📋 Requires Fine: " . ($document->requires_fine ? 'YES' : 'NO') . "\n";
    echo "📋 Fine Amount: UGX " . number_format($document->fine_amount, 2) . "\n\n";
    
    // Test each member
    $members = \App\Models\Member::take(3)->get();
    
    foreach ($members as $index => $member) {
        echo "👤 Member " . ($index + 1) . ": " . $member->first_name . " " . $member->last_name . "\n";
        echo "   ID: " . $member->id . "\n";
        echo "   Email: " . ($member->user ? $member->user->email : 'No user') . "\n";
        
        // Check if has downloaded (same logic as controller)
        $hasDownloaded = $document->downloads()
            ->where('member_id', $member->id)
            ->exists();
        
        echo "   Has Downloaded: " . ($hasDownloaded ? 'YES' : 'NO') . "\n";
        
        // Check download count
        $downloadCount = $document->downloads()
            ->where('member_id', $member->id)
            ->count();
        
        echo "   Download Count: " . $downloadCount . "\n";
        
        // Simulate view logic for button visibility
        echo "   🎯 Button Visibility Logic:\n";
        
        // Check if session data would hide buttons
        $hasSessionData = false; // Assume no session data for this test
        
        if ($hasDownloaded) {
            echo "     ✅ Should show 'Download Again' button (hasDownloaded = true)\n";
        } else {
            if ($document->requires_fine) {
                echo "     ✅ Should show 'Download & Pay Fine' button (no download + requires fine)\n";
            } else {
                echo "     ✅ Should show 'Download Document' button (no download + no fine)\n";
            }
        }
        
        // Check if there are any valid downloads for upload
        $validDownload = \App\Models\DocumentDownload::getValidDownloadForUpload($member->id, $document->id);
        if ($validDownload) {
            echo "     ✅ Has valid download for upload: " . $validDownload->getUploadWindowStatus() . "\n";
        } else {
            echo "     ❌ No valid download for upload\n";
        }
        
        echo "\n";
    }
    
    echo "🔍 Possible Issues with Download Button:\n";
    echo "1. Session data hiding buttons (show_fine_confirmation, download_ready, warning)\n";
    echo "2. JavaScript errors preventing form submission\n";
    echo "3. CSS hiding buttons (display: none, visibility: hidden)\n";
    echo "4. Authentication issues (user not properly logged in)\n";
    echo "5. View caching issues\n\n";
    
    echo "💡 Debugging Steps:\n";
    echo "1. Check browser console for JavaScript errors\n";
    echo "2. Inspect the HTML to see if button exists but is hidden\n";
    echo "3. Check network tab for failed requests\n";
    echo "4. Clear browser cache and try again\n";
    echo "5. Verify user is properly authenticated\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
