<?php

echo "=== Debugging Session Flow Issue ===\n\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "🔍 Testing the session flow after confirm_fine...\n\n";
    
    // Get the document and member
    $document = \App\Models\Document::where('requires_fine', true)->first();
    $member = \App\Models\Member::first();
    
    if (!$document || !$member) {
        echo "❌ Missing test data\n";
        exit;
    }
    
    echo "✅ Document: " . $document->title . "\n";
    echo "✅ Member: " . $member->first_name . " " . $member->last_name . "\n\n";
    
    // Check current download status
    $existingDownload = \App\Models\DocumentDownload::where('document_id', $document->id)
        ->where('member_id', $member->id)
        ->first();
    
    echo "📊 Current Download Status:\n";
    if ($existingDownload) {
        echo "   ✅ Download record exists: ID " . $existingDownload->id . "\n";
        echo "   ✅ Fine Applied: " . ($existingDownload->fine_applied ? 'YES' : 'NO') . "\n";
        echo "   ✅ Downloaded At: " . $existingDownload->downloaded_at . "\n";
        
        if ($existingDownload->fine_id) {
            $fine = \App\Models\Fine::find($existingDownload->fine_id);
            echo "   ✅ Fine Record: ID " . $fine->id . ", Amount: UGX " . number_format($fine->amount, 2) . "\n";
        }
    } else {
        echo "   ❌ No download record found\n";
    }
    
    echo "\n🔧 What should happen in the view:\n";
    echo "   - hasDownloaded: " . ($existingDownload ? 'true' : 'false') . "\n";
    echo "   - document->requires_fine: " . ($document->requires_fine ? 'true' : 'false') . "\n";
    
    if ($existingDownload) {
        echo "\n✅ Since download record exists, view should show:\n";
        echo "   - 'Already Downloaded' status\n";
        echo "   - Regular 'Download Document' button\n";
        echo "   - No fine confirmation needed\n";
    } else {
        echo "\n❌ Since no download record, view should show:\n";
        echo "   - Fine confirmation if session has warning\n";
        echo "   - 'Download & Pay Fine' button\n";
    }
    
    echo "\n🌐 Testing session simulation...\n";
    
    // Simulate what the controller sets after fine confirmation
    echo "   After confirm_fine, controller sets:\n";
    echo "   - session('success'): 'Fine of UGX 5,000.00 has been applied...'\n";
    echo "   - session('download_ready'): true\n\n";
    
    // Test view conditions
    $hasDownloaded = $existingDownload ? true : false;
    $sessionWarning = true; // Simulating session('warning')
    $sessionSuccess = true; // Simulating session('success') 
    $sessionDownloadReady = true; // Simulating session('download_ready')
    
    echo "🎯 View Logic Check:\n";
    echo "   session('show_fine_confirmation') || (session('warning') && document->requires_fine && !hasDownloaded)\n";
    echo "   = false || (true && true && " . (!$hasDownloaded ? 'true' : 'false') . ")\n";
    echo "   = " . (($sessionWarning && $document->requires_fine && !$hasDownloaded) ? 'true' : 'false') . "\n";
    
    echo "\n   session('download_ready') && session('success')\n";
    echo "   = true && true\n";
    echo "   = true\n";
    
    echo "\n💡 Expected Behavior:\n";
    if ($hasDownloaded) {
        echo "   Should show regular download button (no fine needed)\n";
    } else {
        echo "   Should show 'Download Ready' alert with download button\n";
    }
    
    echo "\n🔧 Possible Issues:\n";
    echo "1. Session data not persisting after redirect\n";
    echo "2. Download record not being created properly\n";
    echo "3. View conditions not matching expected state\n";
    echo "4. Page caching interfering with session display\n";
    echo "5. Multiple redirects causing session loss\n";
    
    echo "\n💡 Quick Fix Test:\n";
    echo "Try manually clearing session and testing again:\n";
    echo "1. Clear browser cookies/session\n";
    echo "2. Try the download process again\n";
    echo "3. Check if download_ready message appears\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
