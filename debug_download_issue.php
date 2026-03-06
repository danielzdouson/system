<?php

echo "=== Debugging Download Issue ===\n\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "🔍 Checking download process...\n";
    
    // Get the document
    $document = \App\Models\Document::where('requires_fine', true)->first();
    $member = \App\Models\Member::first();
    
    if (!$document || !$member) {
        echo "❌ Missing test data\n";
        exit;
    }
    
    echo "✅ Document: " . $document->title . "\n";
    echo "✅ File Path: " . $document->file_path . "\n";
    echo "✅ Member: " . $member->first_name . " " . $member->last_name . "\n\n";
    
    // Check if file exists
    $storagePath = storage_path('app/' . $document->file_path);
    echo "📁 Storage Path: " . $storagePath . "\n";
    echo "📁 File Exists: " . (file_exists($storagePath) ? 'YES' : 'NO') . "\n\n";
    
    // Check existing download
    $existingDownload = \App\Models\DocumentDownload::where('document_id', $document->id)
        ->where('member_id', $member->id)
        ->first();
    
    echo "📊 Existing Download: " . ($existingDownload ? 'YES' : 'NO') . "\n";
    if ($existingDownload) {
        echo "   - Fine Applied: " . ($existingDownload->fine_applied ? 'YES' : 'NO') . "\n";
        echo "   - Downloaded At: " . $existingDownload->downloaded_at . "\n";
    }
    
    echo "\n🔧 Simulating confirm_fine submission...\n";
    
    // Simulate what happens when confirm_fine = 1
    $confirmFine = true;
    echo "   confirm_fine: " . ($confirmFine ? 'true' : 'false') . "\n";
    
    if ($confirmFine) {
        echo "   ✅ Would create download record\n";
        echo "   ✅ Would apply fine\n";
        echo "   ✅ Would redirect back with success message\n";
        
        // Test creating download record
        try {
            $download = \App\Models\DocumentDownload::create([
                'document_id' => $document->id,
                'member_id' => $member->id,
                'downloaded_at' => now(),
                'ip_address' => '127.0.0.1',
            ]);
            
            echo "   ✅ Download record created: ID " . $download->id . "\n";
            
            // Test applying fine
            $fine = $download->applyFine();
            echo "   ✅ Fine applied: ID " . $fine->id . ", Amount: UGX " . number_format($fine->amount, 2) . "\n";
            
            // Test file download
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($document->file_path)) {
                echo "   ✅ File exists in storage\n";
                echo "   📄 Ready for download\n";
            } else {
                echo "   ❌ File not found in storage\n";
                echo "   🔧 Need to create test file\n";
                
                // Create a test file
                $dir = dirname($document->file_path);
                if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($dir)) {
                    \Illuminate\Support\Facades\Storage::disk('local')->makeDirectory($dir);
                }
                
                \Illuminate\Support\Facades\Storage::disk('local')->put($document->file_path, 'Test PDF content for ' . $document->title);
                echo "   ✅ Test file created\n";
            }
            
        } catch (\Exception $e) {
            echo "   ❌ Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n🌐 What should happen when you click 'Confirm & Pay Fine':\n";
    echo "1. Form submits with confirm_fine=1\n";
    echo "2. Controller creates DocumentDownload record\n";
    echo "3. Controller applies fine (creates Fine record)\n";
    echo "4. Controller redirects back with success message\n";
    echo "5. Page shows 'Download Document Now' button\n";
    echo "6. Clicking that button downloads the actual file\n\n";
    
    echo "🔧 Possible Issues:\n";
    echo "1. confirm_fine parameter not being sent correctly\n";
    echo "2. File doesn't exist in storage\n";
    echo "3. Permission issues with storage directory\n";
    echo "4. Session data being lost\n";
    echo "5. Redirect loop\n\n";
    
    echo "💡 Quick Test: Try clicking the confirm button and check what happens\n";
    echo "   - Does it show any error?\n";
    echo "   - Does it reload the page?\n";
    echo "   - Does it show success message?\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
