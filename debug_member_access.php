<?php

echo "=== Debugging Member Document Access ===\n\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "🔍 Checking Document Access for Different Members...\n\n";
    
    // Get the document
    $document = \App\Models\Document::find(1);
    if (!$document) {
        echo "❌ Document ID 1 not found\n";
        exit;
    }
    
    echo "📋 Document Details:\n";
    echo "   ID: " . $document->id . "\n";
    echo "   Title: " . $document->title . "\n";
    echo "   Type: " . $document->document_type . "\n";
    echo "   Active: " . ($document->is_active ? 'YES' : 'NO') . "\n";
    echo "   Requires Fine: " . ($document->requires_fine ? 'YES' : 'NO') . "\n";
    echo "   Fine Amount: UGX " . number_format($document->fine_amount, 2) . "\n\n";
    
    // Get all members
    $members = \App\Models\Member::take(5)->get();
    
    echo "👥 Testing Access for All Members:\n";
    echo "----------------------------------------\n";
    
    foreach ($members as $member) {
        echo "👤 Member: " . $member->first_name . " " . $member->last_name . "\n";
        echo "   ID: " . $member->id . "\n";
        echo "   Membership Number: " . $member->membership_number . "\n";
        echo "   Status: " . $member->status . "\n";
        echo "   Is Active: " . ($member->is_active ? 'YES' : 'NO') . "\n";
        
        // Check document access
        $canDownload = $document->isDownloadableBy($member);
        echo "   Can Download: " . ($canDownload ? 'YES' : 'NO') . "\n";
        
        // Check user account
        $user = $member->user;
        if ($user) {
            echo "   User Email: " . $user->email . "\n";
            echo "   User Role: " . $user->role . "\n";
            echo "   Is Member: " . ($user->isMember() ? 'YES' : 'NO') . "\n";
            echo "   Is Admin: " . ($user->isAdmin() ? 'YES' : 'NO') . "\n";
        } else {
            echo "   ❌ No User Account Linked\n";
        }
        
        // Check existing downloads
        $downloads = \App\Models\DocumentDownload::where('member_id', $member->id)
            ->where('document_id', $document->id)
            ->get();
        
        echo "   Previous Downloads: " . $downloads->count() . "\n";
        foreach ($downloads as $dl) {
            echo "     - Download ID: " . $dl->id . " (" . $dl->downloaded_at->format('Y-m-d') . ")\n";
            echo "       Status: " . $dl->getUploadWindowStatus() . "\n";
            echo "       Used: " . ($dl->used_for_upload ? 'YES' : 'NO') . "\n";
        }
        
        echo "\n";
    }
    
    echo "🔍 Additional Checks:\n";
    echo "----------------------------------------\n";
    
    // Check if there are any member-specific restrictions
    echo "📋 Member Status Types:\n";
    $statusCounts = \App\Models\Member::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->get();
    
    foreach ($statusCounts as $status) {
        echo "   " . $status->status . ": " . $status->count . " members\n";
    }
    
    echo "\n📋 User Account Status:\n";
    $totalUsers = \App\Models\User::count();
    $memberUsers = \App\Models\User::where('role', 'member')->count();
    $adminUsers = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->count();
    echo "   Total Users: " . $totalUsers . "\n";
    echo "   Member Users: " . $memberUsers . "\n";
    echo "   Admin Users: " . $adminUsers . "\n";
    
    echo "\n💡 Possible Issues:\n";
    echo "1. Member account not linked to user\n";
    echo "2. User account is inactive\n";
    echo "3. Member status is not 'active'\n";
    echo "4. Document access restrictions\n";
    echo "5. Session/authentication issues\n";
    
    echo "\n🚀 Recommendations:\n";
    echo "1. Check if the member who can't download has an active user account\n";
    echo "2. Verify member status is 'active'\n";
    echo "3. Ensure user is properly logged in\n";
    echo "4. Check for any middleware restrictions\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
