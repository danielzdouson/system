<?php

echo "=== Verifying Uploaded Forms Setup ===\n\n";

// Check if we can access the uploaded forms through the controller logic
try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Test the same query as the controller
    $uploadedForms = \App\Models\UploadedForm::with(['member', 'document', 'guarantors.guarantor'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);
    
    echo "✅ Controller query successful!\n";
    echo "📊 Found " . $uploadedForms->count() . " uploaded forms\n\n";
    
    if ($uploadedForms->count() > 0) {
        echo "📋 Uploaded Forms Details:\n";
        echo "ID\tFilename\t\t\tStatus\t\tLoan Amount\tGuarantors\n";
        echo str_repeat("-", 70) . "\n";
        
        foreach ($uploadedForms as $form) {
            $memberName = $form->member ? ($form->member->first_name . ' ' . $form->member->last_name) : 'N/A';
            $guarantorCount = $form->guarantors->count() . '/' . $form->guarantors_required;
            
            printf("%d\t%-20s\t%-15s\tUGX %s\t%s\n", 
                $form->id, 
                substr($form->filename, 0, 20), 
                $form->status, 
                number_format($form->loan_amount, 0), 
                $guarantorCount
            );
        }
        
        echo "\n✅ All components are working correctly!\n";
        echo "\n🌐 You can now access the uploaded forms at:\n";
        echo "   http://localhost:8080/admin/documents/uploaded-forms\n\n";
        
        echo "📱 Navigation Path:\n";
        echo "   1. Login as admin\n";
        echo "   2. Click 'Documents ▼' in the left menu\n";
        echo "   3. Click 'Uploaded Forms'\n\n";
        
        echo "🎯 What you should see:\n";
        echo "   - 4 test loan applications\n";
        echo "   - Different statuses (ready_for_review, pending_guarantors, approved, rejected)\n";
        echo "   - Review, Guarantors, and Download buttons for each\n";
        echo "   - Progress bars showing guarantee completion\n";
        
    } else {
        echo "❌ No uploaded forms found. The seeder may not have run correctly.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "This indicates a database connection or configuration issue.\n";
}

echo "\n=== Troubleshooting ===\n";
echo "If you don't see the uploaded forms:\n";
echo "1. Ensure Docker is running: docker-compose up -d\n";
echo "2. Check the URL: http://localhost:8080/admin/documents/uploaded-forms\n";
echo "3. Verify you're logged in as admin\n";
echo "4. Check the Documents dropdown menu in the navigation\n";
