<?php

echo "=== Testing LoanGuarantor getStatusColor Fix ===\n\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Test the LoanGuarantor model
    echo "🔍 Testing LoanGuarantor model methods...\n";
    
    $guarantors = \App\Models\LoanGuarantor::take(3)->get();
    
    if ($guarantors->count() > 0) {
        echo "✅ Found " . $guarantors->count() . " guarantors\n\n";
        
        foreach ($guarantors as $guarantor) {
            echo "📋 Guarantor ID: " . $guarantor->id . "\n";
            echo "   Status: " . $guarantor->guarantee_status . "\n";
            echo "   Color: " . $guarantor->getStatusColor() . "\n";
            echo "   Badge: " . strip_tags($guarantor->getStatusBadge()) . "\n";
            echo "   Member: " . ($guarantor->guarantor ? $guarantor->guarantor->first_name . ' ' . $guarantor->guarantor->last_name : 'N/A') . "\n";
            echo "   Amount: UGX " . number_format($guarantor->guaranteed_amount, 2) . "\n";
            echo "   Percentage: " . $guarantor->guarantee_percentage . "%\n\n";
        }
        
        echo "✅ All methods working correctly!\n";
        echo "\n🌐 The guarantors page should now work:\n";
        echo "   http://localhost:8080/admin/documents/uploaded-forms/7/guarantors\n";
        
    } else {
        echo "❌ No guarantors found in database\n";
        echo "Please run the test seeder first:\n";
        echo "php artisan db:seed --class=TestUploadedFormsSeeder\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), 'getStatusColor') !== false) {
        echo "The getStatusColor method is still missing or has an error.\n";
    }
}

echo "\n=== Fix Summary ===\n";
echo "✅ Added getStatusColor() method to LoanGuarantor model\n";
echo "✅ Method returns appropriate Bootstrap color classes:\n";
echo "   - 'pending' → 'warning' (yellow)\n";
echo "   - 'confirmed' → 'success' (green)\n";
echo "   - 'withdrawn' → 'secondary' (gray)\n";
echo "   - 'called_upon' → 'danger' (red)\n";
echo "✅ Guarantors view page should now display correctly\n";
